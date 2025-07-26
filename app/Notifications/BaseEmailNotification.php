<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class BaseEmailNotification extends Notification
{
    use Queueable;

    /**
     * Email types that should be sent immediately (not queued)
     */
    protected const CRITICAL_EMAIL_TYPES = [
        'email_verification',
        'password_reset',
        'security_alert',
        'account_lockout',
        'two_factor_auth',
    ];

    /**
     * Get the email type for this notification
     */
    abstract protected function getEmailType(): string;

    /**
     * Determine if this email should be queued
     */
    public function shouldQueue(): bool
    {
        $emailType = $this->getEmailType();

        // Critical emails are never queued
        if (in_array($emailType, self::CRITICAL_EMAIL_TYPES)) {
            return false;
        }

        // Check environment configuration
        $configKey = 'MAIL_QUEUE_' . strtoupper($emailType) . '_EMAILS';
        return config('mail.queue.' . strtolower($emailType), env($configKey, true));
    }

    /**
     * Get the queue name for this email type
     */
    public function getQueueName(): string
    {
        $emailType = $this->getEmailType();

        $queueMap = [
            'welcome' => 'emails-welcome',
            'notification' => 'emails-notifications',
            'newsletter' => 'emails-marketing',
            'marketing' => 'emails-marketing',
            'default' => 'emails',
        ];

        return $queueMap[$emailType] ?? $queueMap['default'];
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }
}
