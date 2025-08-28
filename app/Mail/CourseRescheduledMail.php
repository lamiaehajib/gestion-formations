<?php

namespace App\Mail;

use App\Models\Course;
use App\Models\CourseReschedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $course;
    public $reschedule;

    /**
     * Create a new message instance.
     */
    public function __construct(Course $course, CourseReschedule $reschedule)
    {
        $this->course = $course;
        $this->reschedule = $reschedule;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ajournement du cours : ' . $this->course->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.course-rescheduled-notification',
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