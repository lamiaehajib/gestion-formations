<?php

namespace App\Mail;

use App\Models\Inscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OverduePaymentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;

    public function __construct(Inscription $inscription)
    {
        $this->inscription = $inscription;
        $this->inscription->load('user', 'formation');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alerte: Paiement en retard de ' . $this->inscription->user->name ?? 'N/A',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue-payment-notification',
        );
    }

    public function attachments()
    {
        return [];
    }
}