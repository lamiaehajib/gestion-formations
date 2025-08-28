<?php

namespace App\Mail;

use App\Models\Inscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInscriptionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Inscription $inscription, User $student)
    {
        $this->inscription = $inscription;
        $this->student = $student;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Nouvelle Inscription Soumise sur UITS',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        // CORRECTION: on utilise la bonne vue pour l'email
        return new Content(
            view: 'emails.new-inscription-notification',
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