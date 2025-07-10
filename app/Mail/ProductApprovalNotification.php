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

class ProductApprovalNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MarketplaceProduct $product;
    public User $seller;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(MarketplaceProduct $product, User $seller, array $notificationData = [])
    {
        $this->product = $product;
        $this->seller = $seller;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->notificationData['status'] ?? $this->product->status;
        $statusText = $status === 'approved' ? 'Được duyệt' : 'Bị từ chối';
        
        return new Envelope(
            subject: "[MechaMap] Sản phẩm {$statusText} - {$this->product->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.product-approval',
            with: [
                'product' => $this->product,
                'seller' => $this->seller,
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
