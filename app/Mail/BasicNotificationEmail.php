<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BasicNotificationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $notificationTitle;
    public string $notificationMessage;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $message, array $data = [])
    {
        $this->notificationTitle = $title;
        $this->notificationMessage = $message;
        $this->notificationData = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notificationTitle . ' - MechaMap',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.basic-notification',
            with: [
                'title' => $this->notificationTitle,
                'message' => $this->notificationMessage,
                'data' => $this->notificationData,
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
