<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Message $message;
    public User $sender;
    public User $recipient;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, User $sender, User $recipient, array $notificationData = [])
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderRole = $this->notificationData['sender_role'] ?? 'người dùng';
        return new Envelope(
            subject: "[MechaMap] Tin nhắn mới từ {$senderRole} - {$this->sender->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.seller-message',
            with: [
                'message' => $this->message,
                'sender' => $this->sender,
                'recipient' => $this->recipient,
                'conversation' => $this->message->conversation,
                'notificationData' => $this->notificationData,
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
