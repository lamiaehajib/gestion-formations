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
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index()
    {
        $promotions = Promotion::with(['formation', 'formation.category'])
            ->withCount('users')
            ->orderBy('year', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating new promotion.
     */
    public function create()
    {
        $eligibleCategories = Category::pluck('id');

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
     * ✅ MODIFIÉ: Exclure les étudiants avec access_restricted = true
     */
    public function show(Promotion $promotion)
    {
        $promotion->load([
            'formation.category',
            'users' => function ($query) use ($promotion) {
                $query->with(['inscriptions' => function ($q) use ($promotion) {
                    // ✅ FILTRE PRINCIPAL: exclure les inscriptions avec access_restricted = true
                    $q->where('formation_id', $promotion->formation_id)
                      ->where('access_restricted', false) // ← Hna l modification
                      ->with('payments');
                }])
                ->join('inscriptions', 'users.id', '=', 'inscriptions.user_id')
                ->where('inscriptions.formation_id', $promotion->formation_id)
                ->where('inscriptions.access_restricted', false) // ← O hna zedna filter f join
                ->orderBy('inscriptions.inscription_date', 'asc')
                ->select('users.*');
            },
        ]);

        $studentsData = [];
        $totalRevenue = 0;
        $totalPaid = 0;
        $totalRemaining = 0;

        foreach ($promotion->users as $user) {
            $inscription = $user->inscriptions->first();
            
            // ✅ Double-check: si inscription existe O access_restricted = false
            if ($inscription && !$inscription->access_restricted) {
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
                    'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
                    'inscription_date' => $inscription->inscription_date,
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
     * ✅ MODIFIÉ: Exclure les étudiants avec access_restricted = true
     */
    public function generateReport(Promotion $promotion, Request $request)
    {
        $promotion->load([
            'formation.category',
            'users' => function ($query) use ($promotion) {
                $query->with([
                    'inscriptions' => function ($q) use ($promotion) {
                        // ✅ Exclure access_restricted dans le rapport aussi
                        $q->where('formation_id', $promotion->formation_id)
                          ->where('access_restricted', false)
                          ->with('payments');
                    }
                ]);
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
            
            // ✅ Double-check pour le rapport
            if ($inscription && !$inscription->access_restricted) {
                $studentInfo = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'cin' => $user->cin,
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
            $output = fopen('php://temp', 'r+');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $headers_excel = [
                'Nom Étudiant',
                'Email',
                'Téléphone',
                'CIN',
                'Montant Total',
                'Montant Payé',
                'Reste à Payer',
                'Type de Paiement',
                'Statut Paiement'
            ];
            fputcsv($output, $headers_excel, ';');

            foreach ($reportData['students'] as $student) {
                $name = str_replace(';', ',', $student['name']);
                $email = str_replace(';', ',', $student['email']);
                $phone = str_replace(';', ',', $student['phone'] ?? 'N/A');
                $cin = str_replace(';', ',', $student['cin'] ?? 'N/A');

                fputcsv($output, [
                    $name,
                    $email,
                    $phone,
                    $cin,
                    number_format($student['total_amount'], 2, '.', ''),
                    number_format($student['paid_amount'], 2, '.', ''),
                    number_format($student['remaining_amount'], 2, '.', ''),
                    $student['payment_type'],
                    $student['payment_status'],
                ], ';');
            }

            rewind($output);
            $csv = stream_get_contents($output);
            fclose($output);

            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="rapport-' . $promotion->name . '_' . date('Ymd') . '.csv"',
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
     * ✅ MODIFIÉ: Exclure les inscriptions avec access_restricted = true
     */
    private function populatePromotionWithStudents(Promotion $promotion)
    {
        $inscriptions = Inscription::where('formation_id', $promotion->formation->id)
            ->where('status', '!=', 'cancelled')
            ->where('access_restricted', false) // ✅ Filtre ajouté ici
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
     * Auto-assign student to promotion when inscription is created
     * ✅ MODIFIÉ: Ne pas assigner si access_restricted = true
     */
    public static function autoAssignStudentToPromotion(Inscription $inscription)
    {
        // ✅ Skip si cancelled OU access_restricted = true
        if (!$inscription->user || 
            $inscription->status === 'cancelled' || 
            $inscription->access_restricted) {
            return false;
        }

        $year = date('Y', strtotime($inscription->inscription_date ?? now()));

        $promotion = Promotion::where('formation_id', $inscription->formation_id)
            ->where('year', $year)
            ->first();

        if (!$promotion) {
            try {
                $formation = Formation::find($inscription->formation_id);
                
                $promotion = Promotion::create([
                    'name' => $formation->title . ' - Promotion ' . $year,
                    'year' => $year,
                    'formation_id' => $inscription->formation_id,
                    'description' => 'Promotion créée automatiquement lors de l\'inscription',
                ]);

                Log::info("Auto-created promotion {$promotion->id} for formation {$inscription->formation_id}, year {$year}");
            } catch (\Exception $e) {
                Log::error("Failed to auto-create promotion: " . $e->getMessage());
                return false;
            }
        }

        $user = $inscription->user;
        
        if ($user->promotion_id !== $promotion->id) {
            $user->update(['promotion_id' => $promotion->id]);
            Log::info("Student {$user->id} auto-assigned to promotion {$promotion->id}");
            return true;
        }

        return false;
    }

    /**
     * Remove student from promotion if inscription is cancelled/deleted
     */
    public static function autoRemoveStudentFromPromotion(User $user)
    {
        // ✅ Check si user 3ando chi inscription active O access_restricted = false
        $activeInscriptions = Inscription::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->where('access_restricted', false) // ✅ Ajouté ici
            ->exists();

        if (!$activeInscriptions && $user->promotion_id) {
            $oldPromotionId = $user->promotion_id;
            $user->update(['promotion_id' => null]);
            Log::info("Student {$user->id} removed from promotion {$oldPromotionId} - no active inscriptions");
            return true;
        }

        return false;
    }

    /**
     * Get formations eligible for promotion creation.
     */
    public function getEligibleFormations()
    {
        $eligibleCategories = Category::pluck('id');

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
     * ✅ MODIFIÉ: Vérifier access_restricted
     */
    public function showStudentPayments(Promotion $promotion, User $user)
    {
        $inscription = Inscription::where('user_id', $user->id)
            ->where('formation_id', $promotion->formation_id)
            ->where('access_restricted', false) // ✅ Filtre ajouté
            ->with('payments')
            ->firstOrFail();

        return view('promotions.student_payment_history', compact('promotion', 'user', 'inscription'));
    }
}