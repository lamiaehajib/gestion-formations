<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Formation;
use App\Models\Category;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PDF; // Assuming you have a PDF package like laravel-dompdf
use Maatwebsite\Excel\Facades\Excel; // Assuming you have Maatwebsite/Laravel-Excel

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index()
    {
        $promotions = Promotion::with(['formation', 'formation.category', 'users'])
            ->orderBy('year', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        $eligibleCategories = Category::whereIn('name', ['Licence Professionnelle', 'Master Professionnelle'])
            ->pluck('id');

        $formations = Formation::whereIn('category_id', $eligibleCategories)
            ->with('category', 'inscriptions')
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        $currentYear = date('Y');
        $availableYears = range($currentYear, $currentYear + 2);

        return view('promotions.create', compact('formations', 'availableYears'));
    }

    /**
     * Store a newly created promotion and automatically populate it with students.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:2050',
            'formation_id' => 'required|exists:formations,id',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $existingPromotion = Promotion::where('formation_id', $request->formation_id)
                ->where('year', $request->year)
                ->first();

            if ($existingPromotion) {
                return back()->withErrors(['error' => 'Une promotion pour cette formation et cette année existe déjà.']);
            }

            $promotion = Promotion::create([
                'name' => $request->name,
                'year' => $request->year,
                'formation_id' => $request->formation_id,
                'description' => $request->description,
            ]);

            $this->populatePromotionWithStudents($promotion);

            DB::commit();

            return redirect()->route('promotions.show', $promotion)
                ->with('success', 'Promotion créée avec succès et étudiants ajoutés automatiquement!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating promotion: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la création de la promotion.']);
        }
    }

    /**
     * Display the specified promotion with student payment details.
     */
   public function show(Promotion $promotion)
{
    $promotion->load([
        'formation.category',
        'users' => function ($query) use ($promotion) {
            $query->with(['inscriptions' => function ($q) use ($promotion) {
                $q->where('formation_id', $promotion->formation_id)->with('payments');
            }]); // <-- J'ai supprimé ', 'avatar''
        },
    ]);

    $studentsData = [];
    $totalRevenue = 0;
    $totalPaid = 0;
    $totalRemaining = 0;

    foreach ($promotion->users as $user) {
        $inscription = $user->inscriptions->first();
        if ($inscription) {
            $studentData = [
                'user' => $user,
                'inscription' => $inscription,
                'paid_amount' => $inscription->paid_amount,
                'remaining_amount' => $inscription->remaining_amount,
                'total_amount' => $inscription->total_amount,
                'payment_status' => $inscription->payment_status_label,
                'payment_type' => $inscription->payment_type,
                'last_payment_date' => $inscription->payments->max('paid_date'),
                'payments_count' => $inscription->payments->count(),
                // Ajoute le chemin de l'avatar ici
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ];

            $studentsData[] = $studentData;
            $totalRevenue += $inscription->total_amount;
            $totalPaid += $inscription->paid_amount;
            $totalRemaining += $inscription->remaining_amount;
        }
    }

    $statistics = [
        'total_students' => count($studentsData),
        'total_revenue' => $totalRevenue,
        'total_paid' => $totalPaid,
        'total_remaining' => $totalRemaining,
        'completion_percentage' => $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100, 2) : 0,
    ];

    return view('promotions.show', compact('promotion', 'studentsData', 'statistics'));
}

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promotion $promotion)
    {
        return view('promotions.edit', compact('promotion'));
    }

    /**
     * Generate promotion report with detailed payment information.
     */
    public function generateReport(Promotion $promotion, Request $request)
    {
        $promotion->load([
            'formation.category',
            'users' => function ($query) use ($promotion) {
                $query->with(['inscriptions' => function ($q) use ($promotion) {
                    $q->where('formation_id', $promotion->formation_id)->with('payments');
                }]);
            },
        ]);

        $reportData = [
            'promotion' => $promotion,
            'generation_date' => now(),
            'students' => [],
            'summary' => [
                'total_students' => 0,
                'total_revenue' => 0,
                'total_paid' => 0,
                'total_remaining' => 0,
                'fully_paid_count' => 0,
                'partially_paid_count' => 0,
                'unpaid_count' => 0,
            ]
        ];

        foreach ($promotion->users as $user) {
            $inscription = $user->inscriptions->first();
            if ($inscription) {
                $studentInfo = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'inscription_date' => $inscription->inscription_date,
                    'total_amount' => $inscription->total_amount,
                    'paid_amount' => $inscription->paid_amount,
                    'remaining_amount' => $inscription->remaining_amount,
                    'payment_type' => $inscription->payment_type,
                    'payment_status' => $inscription->payment_status_label,
                    'payments' => $inscription->payments->map(function ($payment) {
                        return [
                            'amount' => $payment->amount,
                            'paid_date' => $payment->paid_date,
                            'payment_method' => $payment->payment_method,
                            'reference' => $payment->reference,
                        ];
                    })->toArray()
                ];
                $reportData['students'][] = $studentInfo;
                $reportData['summary']['total_students']++;
                $reportData['summary']['total_revenue'] += $inscription->total_amount;
                $reportData['summary']['total_paid'] += $inscription->paid_amount;
                $reportData['summary']['total_remaining'] += $inscription->remaining_amount;

                if ($inscription->remaining_amount <= 0.01) {
                    $reportData['summary']['fully_paid_count']++;
                } elseif ($inscription->paid_amount > 0) {
                    $reportData['summary']['partially_paid_count']++;
                } else {
                    $reportData['summary']['unpaid_count']++;
                }
            }
        }
        
        $format = $request->input('format', 'html');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('promotions.report', compact('reportData'));
            return $pdf->download('rapport-' . $promotion->name . '.pdf');
        } elseif ($format === 'excel') {
            $csv = "Nom Étudiant,Email,Montant Total,Montant Payé,Reste à Payer,Type de Paiement\n";
            foreach ($reportData['students'] as $student) {
                $csv .= "{$student['name']},{$student['email']},{$student['total_amount']},{$student['paid_amount']},{$student['remaining_amount']},{$student['payment_type']}\n";
            }
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rapport-' . $promotion->name . '.csv"',
            ];
            return response($csv, 200, $headers);
        } else {
            return view('promotions.report', compact('reportData'));
        }
    }

    /**
     * Store a new promotion in bulk from the modal form.
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2050',
            'formation_ids' => 'required|array|min:1',
            'formation_ids.*' => 'exists:formations,id',
        ]);

        DB::beginTransaction();
        
        try {
            $createdPromotions = [];
            
            foreach ($request->formation_ids as $formationId) {
                $formation = Formation::find($formationId);
                
                $existingPromotion = Promotion::where('formation_id', $formationId)
                    ->where('year', $request->year)
                    ->first();

                if (!$existingPromotion) {
                    $promotion = Promotion::create([
                        'name' => $formation->title . ' - Promotion ' . $request->year,
                        'year' => $request->year,
                        'formation_id' => $formationId,
                        'description' => 'Promotion générée automatiquement pour ' . $request->year,
                    ]);

                    $this->populatePromotionWithStudents($promotion);
                    $createdPromotions[] = $promotion;
                }
            }

            DB::commit();

            return redirect()->route('promotions.index')
                ->with('success', count($createdPromotions) . ' promotions créées avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error bulk creating promotions: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la création des promotions.']);
        }
    }

    /**
     * Update promotion information.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $promotion->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('promotions.show', $promotion)
            ->with('success', 'Promotion mise à jour avec succès!');
    }

    /**
     * Remove the specified promotion from storage.
     */
    public function destroy(Promotion $promotion)
    {
        DB::beginTransaction();
        
        try {
            User::where('promotion_id', $promotion->id)->update(['promotion_id' => null]);
            $promotion->delete();
            
            DB::commit();

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion supprimée avec succès!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting promotion: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de la suppression de la promotion.']);
        }
    }
    
    /**
     * Automatically populate promotion with students from inscriptions.
     */
    private function populatePromotionWithStudents(Promotion $promotion)
    {
        $inscriptions = Inscription::where('formation_id', $promotion->formation->id)
            ->where('status', '!=', 'cancelled')
            ->with('user')
            ->get();

        $studentsToAdd = [];
        
        foreach ($inscriptions as $inscription) {
            if ($inscription->user && !in_array($inscription->user->id, $studentsToAdd)) {
                $studentsToAdd[] = $inscription->user->id;
            }
        }

        if (!empty($studentsToAdd)) {
            User::whereIn('id', $studentsToAdd)->update(['promotion_id' => $promotion->id]);
            Log::info("Promotion {$promotion->id} populated with " . count($studentsToAdd) . " students.");
        }
    }
    
    /**
     * Get formations eligible for promotion creation (licence/master categories).
     */
    public function getEligibleFormations()
    {
        $eligibleCategories = Category::whereIn('name', ['Licence Professionnelle', 'Master Professionnelle'])
            ->pluck('id');

        $formations = Formation::whereIn('category_id', $eligibleCategories)
            ->with(['category', 'inscriptions.user'])
            ->where('status', 'published')
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'title' => $formation->title,
                    'category' => $formation->category->name,
                    'students_count' => $formation->inscriptions->count(),
                ];
            });

        return response()->json($formations);
    }
    
    /**
     * Display the payment history for a specific student within a formation.
     */
    public function showStudentPayments(Promotion $promotion, User $user)
    {
        $inscription = Inscription::where('user_id', $user->id)
            ->where('formation_id', $promotion->formation_id)
            ->with('payments')
            ->firstOrFail();

        return view('promotions.student_payment_history', compact('promotion', 'user', 'inscription'));
    }
}
