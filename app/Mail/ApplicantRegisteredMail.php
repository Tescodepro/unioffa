<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantRegisteredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $applicationNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $applicationNumber)
    {
        $this->user = $user;
        $this->applicationNumber = $applicationNumber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Application Number - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.applicant_registered',
            with: [
                'user' => $this->user,
                'applicationNumber' => $this->applicationNumber,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
