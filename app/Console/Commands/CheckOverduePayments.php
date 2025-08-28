<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Models\User;
use App\Mail\OverduePaymentNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckOverduePayments extends Command
{
    protected $signature = 'app:check-overdue-payments';
    protected $description = 'Checks for overdue payments and notifies admins.';

    public function handle()
    {
        $today = Carbon::today();
        
        // Kanqelbou 3la les inscriptions li:
        // 1. La date dial next_installment_due_date fayta
        // 2. Baqi khass l'étudiant ykhalles (paid_amount < total_amount)
        // 3. Ma seftnach l'notification l'l'admin qbel
        $inscriptions = Inscription::whereDate('next_installment_due_date', '<', $today)
                                       ->whereRaw('total_amount > paid_amount')
                                       ->where('overdue_notified_at', null)
                                       ->with('user', 'formation')
                                       ->get();

        if ($inscriptions->isEmpty()) {
            $this->info("Aucun paiement en retard n'a été trouvé.");
            return;
        }

        // Kanseftou l'email l'les admins
        // Correction here: Use `role` with an array to include all three roles
        $admins = User::role(['Super Admin', 'Admin', 'Finance'])->get();

        if ($admins->isEmpty()) {
            $this->error("Aucun administrateur trouvé pour envoyer la notification.");
            return;
        }

        foreach ($inscriptions as $inscription) {
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new OverduePaymentNotification($inscription));
            }
            
            // Kanmarkiw l'inscription belli t-seft l'email l'l'admin
            $inscription->update(['overdue_notified_at' => now()]);
            $this->info("Notification de retard de paiement envoyée pour l'inscription: " . $inscription->id);
        }

        $this->info("Tâche de vérification des retards terminée.");
    }
}