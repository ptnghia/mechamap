<?php

namespace App\Mail;

use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewReceivedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ProductReview $review;
    public User $seller;
    public array $notificationData;

    /**
     * Create a new message instance.
     */
    public function __construct(ProductReview $review, User $seller, array $notificationData = [])
    {
        $this->review = $review;
        $this->seller = $seller;
        $this->notificationData = $notificationData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ratingStars = str_repeat('⭐', $this->review->rating);
        return new Envelope(
            subject: "[MechaMap] Nhận được đánh giá {$ratingStars} - {$this->review->product->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notifications.review-received',
            with: [
                'review' => $this->review,
                'seller' => $this->seller,
                'product' => $this->review->product,
                'reviewer' => $this->review->user,
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
