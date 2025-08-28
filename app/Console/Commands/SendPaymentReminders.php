<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'app:send-payment-reminders';
    protected $description = 'Sends reminder emails for upcoming payment installments.';

    public function handle()
    {
        $today = Carbon::today();
        $dueInTwoDays = $today->copy()->addDays(2);

        // Kanqelbou 3la les inscriptions li:
        // 1. Baqi fihom les paiements (remaining_installments > 0)
        // 2. La date dial next_installment_due_date hiya f'2 jours
        $inscriptions = Inscription::where('remaining_installments', '>', 0)
                                   ->whereDate('next_installment_due_date', $dueInTwoDays)
                                   ->get();

        foreach ($inscriptions as $inscription) {
            $student = $inscription->user;
            if ($student) {
                Mail::to($student->email)->send(new PaymentReminderMail($inscription));
            }
        }

        $this->info("Tâche de rappel des paiements terminée. " . count($inscriptions) . " rappels envoyés.");
    }
}