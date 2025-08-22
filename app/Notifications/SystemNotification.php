<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * System Notification
 * For admin/system level notifications stored in Laravel notifications table
 * Only accessible by super admin
 */
class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->notificationData = $data;
        $this->onQueue('notifications-system');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add email for critical system notifications
        if (in_array($this->notificationData['priority'] ?? 'normal', ['high', 'urgent'])) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('[SYSTEM] ' . $this->notificationData['title'])
            ->greeting('System Notification')
            ->line($this->notificationData['message']);

        if (!empty($this->notificationData['action_url'])) {
            $mailMessage->action(
                $this->notificationData['action_text'] ?? 'View Details',
                $this->notificationData['action_url']
            );
        }

        return $mailMessage->line('This is an automated system notification.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->notificationData['type'],
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
            'data' => $this->notificationData['data'] ?? [],
            'priority' => $this->notificationData['priority'] ?? 'normal',
            'action_url' => $this->notificationData['action_url'] ?? null,
            'metadata' => array_merge($this->notificationData['metadata'] ?? [], [
                'system_notification' => true,
                'restricted_access' => true,
                'created_at' => now()->toISOString(),
                'notification_class' => self::class
            ])
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'system.' . $this->notificationData['type'];
    }
}
