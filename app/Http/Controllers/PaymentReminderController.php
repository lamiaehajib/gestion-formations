<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Formation;
use App\Models\Inscription;
use App\Models\PaymentReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentReminderController extends Controller
{
    
    /**
     * Afficher la page avec liste des formations
     */
    public function index(Request $request)
    {
        // Récupérer toutes les formations qui ont des étudiants avec paiements en attente
        $query = Formation::with(['category', 'consultant'])
            ->whereHas('inscriptions', function($q) {
                $q->whereIn('status', ['active', 'pending'])
                  ->where(DB::raw('total_amount - paid_amount'), '>', 0.01);
            });

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $formations = $query->get()->map(function($formation) {
            // Compter étudiants avec paiements en attente
            $studentsWithDebt = $formation->inscriptions()
                ->whereIn('status', ['active', 'pending'])
                ->where(DB::raw('total_amount - paid_amount'), '>', 0.01)
                ->count();
            
            $formation->students_with_debt = $studentsWithDebt;
            
            // Total montant restant pour cette formation
            $totalRemaining = $formation->inscriptions()
                ->whereIn('status', ['active', 'pending'])
                ->get()
                ->sum(function($inscription) {
                    return max(0, $inscription->total_amount - $inscription->paid_amount);
                });
            
            $formation->total_remaining = $totalRemaining;
            
            // Vérifier si rappel actif existe pour cette formation
            $formation->active_reminders_count = PaymentReminder::where('formation_id', $formation->id)
                ->where('is_active', true)
                ->where('expiry_date', '>=', Carbon::today())
                ->count();
            
            return $formation;
        })->filter(function($formation) {
            return $formation->students_with_debt > 0;
        });

        // Pour les filtres
        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('admin.payment-reminders.index', compact('formations', 'categories'));
    }

    /**
     * Afficher les étudiants d'une formation spécifique
     */
    public function showStudents($formationId)
    {
        $formation = Formation::with(['category', 'consultant'])->findOrFail($formationId);
        
        // Récupérer étudiants avec paiements en attente pour cette formation
        $students = User::role('etudiant')
            ->whereHas('inscriptions', function($q) use ($formationId) {
                $q->where('formation_id', $formationId)
                  ->whereIn('status', ['active', 'pending'])
                  ->where(DB::raw('total_amount - paid_amount'), '>', 0.01);
            })
            ->with(['inscriptions' => function($q) use ($formationId) {
                $q->where('formation_id', $formationId)
                  ->whereIn('status', ['active', 'pending'])
                  ->with(['formation', 'formation.category']);
            }])
            ->get()
            ->map(function($student) use ($formationId) {
                // Calculer montant restant pour cette formation
                $inscription = $student->inscriptions->first();
                $student->remaining_amount = $inscription ? 
                    max(0, $inscription->total_amount - $inscription->paid_amount) : 0;
                
                $student->inscription = $inscription;
                
                // Vérifier si rappel actif existe
                $student->has_active_reminder = PaymentReminder::where('user_id', $student->id)
                    ->where('formation_id', $formationId)
                    ->where('is_active', true)
                    ->where('expiry_date', '>=', Carbon::today())
                    ->exists();
                
                return $student;
            });

        return view('admin.payment-reminders.students', compact('formation', 'students'));
    }

    /**
     * Envoyer rappel aux étudiants sélectionnés d'une formation
     */
    public function sendReminders(Request $request)
    {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
            'expiry_date' => 'required|date|after_or_equal:today'
        ]);

        $formationId = $request->formation_id;
        $studentIds = $request->student_ids;
        $expiryDate = Carbon::parse($request->expiry_date);
        
        DB::beginTransaction();
        try {
            foreach ($studentIds as $studentId) {
                // Vérifier que l'étudiant a bien une inscription pour cette formation
                $inscription = Inscription::where('user_id', $studentId)
                    ->where('formation_id', $formationId)
                    ->whereIn('status', ['active', 'pending'])
                    ->first();
                
                if ($inscription && $inscription->remaining_amount > 0.01) {
                    // Créer/Mettre à jour le rappel
                    PaymentReminder::updateOrCreate(
                        [
                            'user_id' => $studentId,
                            'formation_id' => $formationId
                        ],
                        [
                            'expiry_date' => $expiryDate,
                            'is_active' => true,
                            'sent_at' => now(),
                            'sent_by' => auth()->id()
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', count($studentIds) . ' rappel(s) envoyé(s) avec succès!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi des rappels: ' . $e->getMessage());
        }
    }

    /**
     * Désactiver un rappel
     */
    public function deactivate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'formation_id' => 'required|exists:formations,id'
        ]);
        
        $reminder = PaymentReminder::where('user_id', $request->user_id)
            ->where('formation_id', $request->formation_id)
            ->first();
        
        if ($reminder) {
            $reminder->update(['is_active' => false]);
            return redirect()->back()->with('success', 'Rappel désactivé avec succès!');
        }
        
        return redirect()->back()->with('error', 'Rappel introuvable.');
    }

    /**
     * Supprimer un rappel
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'formation_id' => 'required|exists:formations,id'
        ]);
        
        PaymentReminder::where('user_id', $request->user_id)
            ->where('formation_id', $request->formation_id)
            ->delete();
            
        return redirect()->back()->with('success', 'Rappel supprimé avec succès!');
    }
}