<?php

namespace App\Listeners;

use App\Events\ReclamationCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotificationOnReclamation
{
    public function handle(ReclamationCreated $event): void
    {
        $reclamation = $event->reclamation;
        $etudiant = $reclamation->user;

        $admins = User::role('Admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => "Nouvelle réclamation soumise",
                'message' => "L'étudiant **{$etudiant->name}** a soumis une nouvelle réclamation concernant : **{$reclamation->subject}**.",
                'type' => 'warning', // On peut utiliser 'warning' pour les réclamations pour les mettre en évidence.
                'data' => [
                    'reclamation_id' => $reclamation->id,
                    'etudiant_id' => $etudiant->id,
                ],
            ]);
        }
    }
}