<?php

namespace App\Mail;

use App\Models\Attestation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttestationReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * L'instance d'attestation.
     *
     * @var \App\Models\Attestation
     */
    public $attestation;

    /**
     * Créer une nouvelle instance de message.
     */
    public function __construct(Attestation $attestation)
    {
        $this->attestation = $attestation;
    }

    /**
     * Obtenir l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Votre attestation de scolarité est prête !',
        );
    }

    /**
     * Obtenir la définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attestation_ready', // Ceci fait référence à resources/views/emails/attestation_ready.blade.php
            with: [
                'studentName' => $this->attestation->user->name,
                'attestationLink' => route('student.attestations.download', $this->attestation), // Assurez-vous que cette route existe
                'formationTitle' => $this->attestation->inscription->formation->title,
            ],
        );
    }

    /**
     * Obtenir le tableau des pièces jointes du message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}