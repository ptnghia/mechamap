<?php

namespace App\Mail\Business;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $businessInfo;
    public $rejectionReason;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $status, array $businessInfo, string $rejectionReason = null)
    {
        $this->user = $user;
        $this->status = $status;
        $this->businessInfo = $businessInfo;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => '🎉 Xác minh doanh nghiệp thành công - MechaMap',
            'rejected' => '❌ Cập nhật về xác minh doanh nghiệp - MechaMap',
            'pending' => '⏳ Đang xử lý xác minh doanh nghiệp - MechaMap'
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Cập nhật trạng thái xác minh doanh nghiệp - MechaMap',
            from: config('mail.from.address'),
            replyTo: 'business@mechamap.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.business.verification-status',
            with: [
                'user' => $this->user,
                'status' => $this->status,
                'businessInfo' => $this->businessInfo,
                'rejectionReason' => $this->rejectionReason
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
