<?php

namespace App\Mail;

use App\Models\FormationMessage; // 💡 Import du modèle
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewFormationMessage extends Mailable implements ShouldQueue // 💡 Implémente ShouldQueue (recommandé)
{
    use Queueable, SerializesModels;

    /**
     * L'instance du message de formation.
     * @var \App\Models\FormationMessage
     */
    public $formationMessage; // 💡 Propriété publique pour l'accès dans la vue

    /**
     * Crée une nouvelle instance de message.
     *
     * @param \App\Models\FormationMessage $message
     * @return void
     */
    public function __construct(FormationMessage $message)
    {
        // 💡 Assigner l'objet FormationMessage à une propriété publique
        $this->formationMessage = $message;
    }

    /**
     * Get the message envelope (Sujet).
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            // 💡 Utiliser le sujet réel du message
            subject: 'Nouveau Message : ' . $this->formationMessage->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.formation-messages-notification',
            with: [
                // 💡 Rendre l'objet message disponible pour la vue Blade sous la variable $message
                'message' => $this->formationMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}