<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotification
{
    public function handle(PaymentCreated $event): void
    {
        $payment = $event->payment;
        $etudiant = $payment->inscription->user;
        
        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => "Nouveau paiement reçu",
                'message' => "L'étudiant **{$etudiant->name}** a effectué un paiement de **{$payment->amount} DH** pour la formation **{$payment->inscription->formation->title}**.",
                'type' => 'payment',
                'data' => [
                    'payment_id' => $payment->id,
                    'etudiant_id' => $etudiant->id,
                    'formation_id' => $payment->inscription->formation->id,
                ],
            ]);
        }
    }
}