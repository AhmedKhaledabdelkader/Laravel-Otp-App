<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Define the email envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email',
        );
    }

    /**
     * Define the email content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',   // point to your Blade template
            with: [
                'user' => $this->user,
            ],
        );
    }

    /**
     * Attachments (if needed).
     */
    public function attachments(): array
    {
        return [];
    }
}
