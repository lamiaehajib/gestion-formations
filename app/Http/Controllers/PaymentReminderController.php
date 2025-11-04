<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inscription;
use App\Models\PaymentReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentReminderController extends Controller
{
    

    /**
     * Afficher la page avec liste des étudiants concernés
     */
    public function index(Request $request)
    {
        // Les catégories concernées
        $targetCategories = ['LICENCE PROFESSIONNELLE RECONNU', 'Master Professionnelle', 'Licence Professionnelle'];
        
        // Récupérer tous les étudiants avec inscriptions actives f ces catégories
        $query = User::role('etudiant')
            ->whereHas('inscriptions', function($q) use ($targetCategories) {
                $q->whereIn('status', ['active', 'pending'])
                  ->whereHas('formation.category', function($catQuery) use ($targetCategories) {
                      $catQuery->whereIn('name', $targetCategories);
                  })
                  ->where(DB::raw('total_amount - paid_amount'), '>', 0.01);
            })
            ->with(['inscriptions' => function($q) use ($targetCategories) {
                $q->whereIn('status', ['active', 'pending'])
                  ->whereHas('formation.category', function($catQuery) use ($targetCategories) {
                      $catQuery->whereIn('name', $targetCategories);
                  })
                  ->with(['formation', 'formation.category']);
            }]);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('inscriptions.formation.category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $students = $query->get()->map(function($student) {
            // Calculer total montant restant pour chaque étudiant
            $totalRemaining = $student->inscriptions->sum(function($inscription) {
                return $inscription->total_amount - $inscription->paid_amount;
            });
            
            $student->total_remaining = $totalRemaining;
            $student->inscriptions_count = $student->inscriptions->count();
            
            // Vérifier si le rappel a été envoyé récemment
            $student->last_reminder = PaymentReminder::where('user_id', $student->id)
                ->latest()
                ->first();
                
            return $student;
        });

        return view('admin.payment-reminders.index', compact('students', 'targetCategories'));
    }

    /**
     * Envoyer rappel aux étudiants sélectionnés
     */
    public function sendReminders(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
            'expiry_date' => 'nullable|date|after_or_equal:today'
        ]);

        $studentIds = $request->student_ids;
        $expiryDate = $request->expiry_date ? Carbon::parse($request->expiry_date) : Carbon::create(2025, 11, 5);
        
        DB::beginTransaction();
        try {
            foreach ($studentIds as $studentId) {
                // Créer/Mettre à jour le rappel pour cet étudiant
                PaymentReminder::updateOrCreate(
                    ['user_id' => $studentId],
                    [
                        'expiry_date' => $expiryDate,
                        'is_active' => true,
                        'sent_at' => now(),
                        'sent_by' => auth()->id()
                    ]
                );
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
    public function deactivate($userId)
    {
        $reminder = PaymentReminder::where('user_id', $userId)->first();
        
        if ($reminder) {
            $reminder->update(['is_active' => false]);
            return redirect()->back()->with('success', 'Rappel désactivé avec succès!');
        }
        
        return redirect()->back()->with('error', 'Rappel introuvable.');
    }

    /**
     * Supprimer un rappel
     */
    public function destroy($userId)
    {
        PaymentReminder::where('user_id', $userId)->delete();
        return redirect()->back()->with('success', 'Rappel supprimé avec succès!');
    }
}