<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inscription; // Assurez-vous d'importer le modèle Inscription
use App\Models\Payment;    // Assurez-vous d'importer le modèle Payment (peut être nécessaire pour une vérification précise des paiements)
use Carbon\Carbon;          // Pour utiliser les fonctions de date et heure
use Illuminate\Support\Facades\Log; // Pour l'enregistrement des événements (très important pour les tâches planifiées)
use Illuminate\Support\Facades\DB;  // Pour utiliser les transactions de base de données afin d'assurer l'intégrité des données

class CheckInstallmentDueDates extends Command
{
    /**
     * Le nom de la commande Artisan (ex: php artisan payments:check-due-dates)
     *
     * @var string
     */
    protected $signature = 'payments:check-due-dates';

    /**
     * La description de la commande (apparaît lors de l'exécution de php artisan list)
     *
     * @var string
     */
    protected $description = 'Checks monthly installment due dates and restricts access for overdue payments.';

    /**
     * Exécute la commande.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('Début de l\'exécution de la commande : payments:check-due-dates.'); // Enregistrement du début de l'exécution
        $this->info('Vérification des dates d\'échéance des acomptes en cours...'); // Message affiché dans le terminal

        // Obtenir le jour actuel du mois
        $currentDay = Carbon::now()->day;
        // Le jour cible pour la restriction (c'est-à-dire si le paiement n'a pas été effectué avant ce jour)
        $targetDayForRestriction = 5; 

        // Si le jour actuel est le cinquième ou avant, cela signifie que les acomptes pour ce mois ne sont pas encore en retard.
        // Il n'y a donc rien à faire pour le moment.
        if ($currentDay <= $targetDayForRestriction) {
            $this->info("Nous sommes le {$currentDay}. Aucun acompte n'est en retard pour le cycle de ce mois (jour cible : {$targetDayForRestriction}).");
            Log::info("Commande ignorée : Aujourd'hui ({$currentDay}) est avant ou au jour d'échéance cible ({$targetDayForRestriction}).");
            return Command::SUCCESS; // Terminer la commande avec succès
        }

        // Définir les catégories de formation qui sont soumises à la règle des frais fixes
        $restrictedCategories = ['Licence Professionnelle', 'Master Professionnelle'];
        
        // Récupérer les inscriptions potentiellement en retard :
        // 1. Le statut de l'inscription est 'active' (actif) ou 'completed' (terminé)
        // 2. Il reste des acomptes à payer (remaining_installments > 0)
        // 3. Leur accès n'est pas déjà restreint (access_restricted = false)
        // 4. Leur formation appartient aux catégories restreintes (Licence Pro, Master Pro)
        // 5. La date d'échéance de leur prochain acompte est passée (ou n'a pas été correctement mise à jour après le premier paiement)
        //    Cela nécessite une logique plus précise : ils n'ont pas payé l'acompte dû pour ce mois.
        
        $overdueInscriptions = Inscription::with(['formation.category', 'payments']) // Charger la catégorie de la formation et les paiements associés à l'inscription
            ->whereIn('status', ['active', 'completed'])
            ->where('remaining_installments', '>', 0)
            ->where('access_restricted', false)
            ->whereHas('formation.category', function($q) use ($restrictedCategories) {
                $q->whereIn('name', $restrictedCategories);
            })
            ->get(); // Récupérer toutes les inscriptions qui correspondent aux critères initiaux

        $countRestricted = 0; // Compteur pour les inscriptions dont l'accès sera restreint

        foreach ($overdueInscriptions as $inscription) {
            DB::beginTransaction(); // Démarrer une transaction de base de données pour assurer l'intégrité des données
            try {
                // --- Logique de vérification de l'acompte en retard pour ce mois ---
                // C'est la partie la plus complexe et nécessite une compréhension précise de votre calendrier de paiements.
                // L'idée : vérifier si l'acompte dû pour le mois en cours a été payé ou non.

                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;

                // 1. Vérifier la prochaine date d'échéance enregistrée dans l'inscription
                $nextDueDate = $inscription->next_installment_due_date; // La prochaine date d'échéance de l'acompte enregistrée

                // Si aucune date d'échéance n'est enregistrée, ou si la date d'échéance est passée
                // Cela indique qu'un acompte était dû et que la date d'échéance n'a pas été mise à jour.
                $isOverdueForThisPeriod = false;

                if ($nextDueDate && $nextDueDate->lt(Carbon::today())) { // Si la date d'échéance est effectivement passée
                    // 2. Vérifier si un paiement 'paid' couvre cet acompte dû
                    //    Ici, nous devons examiner l'historique des paiements.
                    //    Nous pouvons rechercher au moins un paiement effectué 'après' la date d'échéance précédente
                    //    ou un paiement qui n'a pas encore été comptabilisé.

                    // La méthode la plus simple (si chaque acompte est payé comme un paiement séparé) :
                    // Si le montant total payé ne couvre pas le nombre d'acomptes qui auraient dû être payés jusqu'à présent.
                    // Ou simplement, si le montant payé (paid_amount) ne couvre pas les acomptes qui auraient dû être payés jusqu'à aujourd'hui
                    // (par exemple, si l'étudiant paie des acomptes mensuels).

                    // Exemple très simplifié : Si aucun paiement n'a été effectué ce mois-ci
                    // ou si le montant payé n'est pas suffisant pour couvrir les acomptes dus jusqu'à présent
                    $totalPaid = $inscription->paid_amount;
                    $expectedPaidAmountForMonthsPassed = 0;

                    // Calculer le nombre de mois écoulés depuis la date d'inscription (début du calcul des acomptes)
                    $monthsSinceInscription = $inscription->inscription_date->diffInMonths(Carbon::today());
                    
                    // Si l'inscription a des acomptes mensuels
                    if ($inscription->amount_per_installment > 0 && $monthsSinceInscription >= 0) {
                        // Le montant qui aurait dû être payé jusqu'à ce mois-ci
                        $expectedPaidAmountForMonthsPassed = $inscription->amount_per_installment * ($monthsSinceInscription + 1); // +1 pour l'acompte actuel
                    }
                    
                    // Si le montant payé est nettement inférieur au montant attendu jusqu'à présent
                    // Et que nous sommes après le 5ème jour du mois (pour s'assurer que l'acompte est dû)
                    if (($totalPaid + 0.01) < $expectedPaidAmountForMonthsPassed && $currentDay > $targetDayForRestriction) { // Ajouter 0.01 pour gérer les erreurs de virgule flottante
                        $isOverdueForThisPeriod = true;
                    }
                }
                
                // Si le retard de paiement pour cet acompte est confirmé
                if ($isOverdueForThisPeriod) {
                    $inscription->access_restricted = true; // Restreindre l'accès
                    $inscription->save(); // Sauvegarder le changement
                    $countRestricted++; // Incrémenter le compteur
                    Log::info("Accès restreint pour l'Inscription ID: {$inscription->id} (Étudiant: {$inscription->user->email}) en raison d'un paiement en retard.");
                }

                DB::commit(); // Sauvegarder les changements dans la base de données
            } catch (\Exception $e) {
                DB::rollBack(); // Annuler les changements en cas d'erreur
                Log::error("Échec de la restriction d'accès pour l'Inscription ID: {$inscription->id}. Erreur: " . $e->getMessage());
            }
        }

        $this->info("Vérification des dates d'échéance terminée. Accès restreint pour {$countRestricted} inscription(s).");
        Log::info("La commande payments:check-due-dates est terminée. {$countRestricted} inscription(s) restreinte(s).");

        return Command::SUCCESS; // Terminer la commande avec succès
    }
}
