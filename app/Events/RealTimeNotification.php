<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Notification;

/**
 * Real-time Notification Event
 * Broadcasts notifications to users in real-time
 */
class RealTimeNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $notification;
    public $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(?User $user, $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->prepareBroadcastData();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // User-specific private channel
        if ($this->user) {
            $channels[] = new PrivateChannel('user.' . $this->user->id);
        }

        // Global notifications channel for system announcements
        if (!$this->user || $this->isSystemNotification()) {
            $channels[] = new Channel('notifications.global');
        }

        // Role-based channels
        if ($this->user && $this->hasRoleSpecificNotification()) {
            $channels[] = new Channel('notifications.role.' . $this->user->role);
        }

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'notification.received';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }

    /**
     * Determine if the event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Don't broadcast if user is offline and notification is not critical
        if ($this->user && !$this->isUserOnline() && !$this->isCriticalNotification()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare broadcast data
     */
    private function prepareBroadcastData(): void
    {
        if ($this->notification instanceof Notification) {
            $this->broadcastData = [
                'id' => $this->notification->id,
                'type' => $this->notification->type,
                'title' => $this->notification->data['title'] ?? 'New Notification',
                'message' => $this->notification->data['message'] ?? '',
                'icon' => $this->notification->data['icon'] ?? 'fas fa-bell',
                'level' => $this->notification->data['level'] ?? 'info',
                'action_url' => $this->notification->data['action_url'] ?? null,
                'action_text' => $this->notification->data['action_text'] ?? null,
                'created_at' => $this->notification->created_at->toISOString(),
                'read_at' => $this->notification->read_at?->toISOString(),
                'user_id' => $this->user?->id,
                'timestamp' => now()->toISOString(),
            ];
        } else {
            // Handle object notifications (system announcements, etc.)
            $this->broadcastData = [
                'id' => uniqid('notif_'),
                'type' => $this->notification->type ?? 'system',
                'title' => $this->notification->title ?? 'System Notification',
                'message' => $this->notification->message ?? '',
                'icon' => $this->notification->icon ?? 'fas fa-info-circle',
                'level' => $this->notification->level ?? 'info',
                'action_url' => $this->notification->action_url ?? null,
                'action_text' => $this->notification->action_text ?? null,
                'created_at' => now()->toISOString(),
                'read_at' => null,
                'user_id' => $this->user?->id,
                'timestamp' => now()->toISOString(),
            ];
        }

        // Add user-specific data
        if ($this->user) {
            $this->broadcastData['user'] = [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar,
                'role' => $this->user->role,
            ];
        }

        // Add notification count
        $this->broadcastData['unread_count'] = $this->getUnreadNotificationCount();
    }

    /**
     * Check if this is a system notification
     */
    private function isSystemNotification(): bool
    {
        if ($this->notification instanceof Notification) {
            return $this->notification->type === 'system_announcement' ||
                   $this->notification->type === 'maintenance' ||
                   $this->notification->type === 'security_alert';
        }

        return isset($this->notification->type) && 
               in_array($this->notification->type, ['system_announcement', 'maintenance', 'security_alert']);
    }

    /**
     * Check if this notification is role-specific
     */
    private function hasRoleSpecificNotification(): bool
    {
        if (!$this->notification instanceof Notification) {
            return false;
        }

        $roleSpecificTypes = [
            'admin_alert',
            'moderator_action',
            'supplier_update',
            'manufacturer_notice',
        ];

        return in_array($this->notification->type, $roleSpecificTypes);
    }

    /**
     * Check if user is currently online
     */
    private function isUserOnline(): bool
    {
        if (!$this->user) {
            return false;
        }

        return cache()->get("user_online_{$this->user->id}", false);
    }

    /**
     * Check if this is a critical notification
     */
    private function isCriticalNotification(): bool
    {
        if ($this->notification instanceof Notification) {
            return ($this->notification->data['level'] ?? 'info') === 'critical' ||
                   ($this->notification->data['priority'] ?? 'normal') === 'high';
        }

        return ($this->notification->level ?? 'info') === 'critical';
    }

    /**
     * Get unread notification count for user
     */
    private function getUnreadNotificationCount(): int
    {
        if (!$this->user) {
            return 0;
        }

        return cache()->remember(
            "user_notification_count_{$this->user->id}",
            300, // 5 minutes
            function () {
                return $this->user->unreadNotifications()->count();
            }
        );
    }

    /**
     * Get notification priority for queue processing
     */
    public function getNotificationPriority(): string
    {
        if ($this->isCriticalNotification()) {
            return 'high';
        }

        if ($this->isSystemNotification()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get notification delivery options
     */
    public function getDeliveryOptions(): array
    {
        return [
            'broadcast' => true,
            'database' => true,
            'email' => $this->shouldSendEmail(),
            'push' => $this->shouldSendPush(),
            'sms' => $this->shouldSendSMS(),
        ];
    }

    /**
     * Determine if email should be sent
     */
    private function shouldSendEmail(): bool
    {
        if (!$this->user) {
            return false;
        }

        // Send email for critical notifications or if user is offline
        return $this->isCriticalNotification() || 
               !$this->isUserOnline() ||
               ($this->user->notification_preferences['email'] ?? false);
    }

    /**
     * Determine if push notification should be sent
     */
    private function shouldSendPush(): bool
    {
        if (!$this->user) {
            return false;
        }

        return $this->user->notification_preferences['push'] ?? true;
    }

    /**
     * Determine if SMS should be sent
     */
    private function shouldSendSMS(): bool
    {
        if (!$this->user || !$this->user->phone) {
            return false;
        }

        // Only send SMS for critical notifications
        return $this->isCriticalNotification() && 
               ($this->user->notification_preferences['sms'] ?? false);
    }

    /**
     * Get notification metadata
     */
    public function getMetadata(): array
    {
        return [
            'event_type' => 'real_time_notification',
            'user_id' => $this->user?->id,
            'notification_type' => $this->notification->type ?? 'unknown',
            'priority' => $this->getNotificationPriority(),
            'delivery_options' => $this->getDeliveryOptions(),
            'broadcast_channels' => count($this->broadcastOn()),
            'created_at' => now()->toISOString(),
        ];
    }
}
