<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;
    public $stats;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl, array $stats = [])
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->stats = $stats;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác minh địa chỉ email - MechaMap',
            from: config('mail.from.address'),
            replyTo: 'support@mechamap.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.verify-email',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'stats' => $this->stats
            ]
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
