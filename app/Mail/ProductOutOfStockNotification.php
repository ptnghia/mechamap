<?php

namespace App\Mail;

use App\Models\MarketplaceProduct;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductOutOfStockNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MarketplaceProduct $product;
    public User $user;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketplaceProduct $product, User $user, array $notificationData = [])
    {
        $this->product = $product;
        $this->user = $user;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[MechaMap] Sản phẩm hết hàng - ' . $this->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.product-out-of-stock',
            with: [
                'product' => $this->product,
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
