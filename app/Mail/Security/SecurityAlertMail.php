<?php

namespace App\Mail\Security;

use App\Mail\BaseMail;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SecurityAlertMail extends BaseMail
{
    public $user;
    public $alertType;
    public $alertData;
    public $ipAddress;
    public $userAgent;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $alertType, $alertData = [], $ipAddress = null, $userAgent = null)
    {
        parent::__construct();
        $this->user = $user;
        $this->alertType = $alertType;
        $this->alertData = $alertData;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'login_from_new_device' => '🔐 Đăng nhập từ thiết bị mới - MechaMap',
            'password_changed' => '🔑 Mật khẩu đã được thay đổi - MechaMap',
            'email_changed' => '📧 Email đã được thay đổi - MechaMap',
            'suspicious_activity' => '⚠️ Phát hiện hoạt động đáng ngờ - MechaMap',
            'account_locked' => '🔒 Tài khoản đã bị khóa - MechaMap',
            'failed_login_attempts' => '🚨 Nhiều lần đăng nhập thất bại - MechaMap',
        ];

        $subject = $subjects[$this->alertType] ?? '🔐 Cảnh báo bảo mật - MechaMap';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.security.alert',
            with: [
                'user' => $this->user,
                'alertType' => $this->alertType,
                'alertData' => $this->alertData,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'stats' => $this->stats,
            ],
        );
    }
}
