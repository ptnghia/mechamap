<?php

namespace App\Mail;

use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MarketplaceOrder $order;
    public User $user;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketplaceOrder $order, User $user, array $notificationData = [])
    {
        $this->order = $order;
        $this->user = $user;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusText = $this->notificationData['status_text'] ?? $this->order->status;
        return new Envelope(
            subject: "[MechaMap] Đơn hàng #{$this->order->order_number} - {$statusText}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.order-status',
            with: [
                'order' => $this->order,
                'user' => $this->user,
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
