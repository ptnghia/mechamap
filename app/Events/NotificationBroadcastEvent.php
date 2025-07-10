<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationBroadcastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Notification $notification;
    public array $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Notification $notification)
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
        $channels[] = new PrivateChannel('user.' . $this->user->id);

        // Role-specific channels for certain notification types
        if ($this->isRoleSpecificNotification()) {
            $userRoles = $this->getUserRoles();
            foreach ($userRoles as $role) {
                $channels[] = new PrivateChannel('notifications.role.' . $role);
            }
        }

        // Marketplace-specific channels
        if ($this->isMarketplaceNotification()) {
            if ($this->user->marketplaceSeller) {
                $channels[] = new PrivateChannel('marketplace.seller.' . $this->user->marketplaceSeller->id);
            }
            $channels[] = new PrivateChannel('marketplace.orders.' . $this->user->id);
        }

        // Forum-specific channels
        if ($this->isForumNotification()) {
            $forumId = $this->getForumId();
            if ($forumId) {
                $channels[] = new PrivateChannel('forum.' . $forumId);
            }
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
     * Prepare broadcast data
     */
    private function prepareBroadcastData(): void
    {
        $this->broadcastData = [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'data' => $this->notification->data,
            'priority' => $this->notification->priority,
            'is_read' => $this->notification->is_read,
            'created_at' => $this->notification->created_at->toISOString(),
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'formatted_time' => $this->notification->created_at->diffForHumans(),
            'icon' => $this->getNotificationIcon(),
            'color' => $this->getNotificationColor(),
            'action_url' => $this->getActionUrl(),
            'can_mark_read' => true,
            'can_delete' => true,
            'metadata' => $this->getNotificationMetadata(),
        ];
    }

    /**
     * Check if notification is role-specific
     */
    private function isRoleSpecificNotification(): bool
    {
        $roleSpecificTypes = [
            'product_approved',
            'product_rejected',
            'seller_message',
            'seller_order_status_changed',
            'seller_payment_received',
        ];

        return in_array($this->notification->type, $roleSpecificTypes);
    }

    /**
     * Check if notification is marketplace-related
     */
    private function isMarketplaceNotification(): bool
    {
        $marketplaceTypes = [
            'product_out_of_stock',
            'price_drop_alert',
            'wishlist_available',
            'review_received',
            'seller_message',
            'buyer_message',
            'marketplace_message',
            'order_status_changed',
            'order_payment_status_changed',
            'seller_order_status_changed',
            'seller_payment_received',
            'product_approved',
            'product_rejected',
        ];

        return in_array($this->notification->type, $marketplaceTypes);
    }

    /**
     * Check if notification is forum-related
     */
    private function isForumNotification(): bool
    {
        $forumTypes = [
            'thread_created',
            'thread_replied',
            'comment_mention',
            'thread_followed',
        ];

        return in_array($this->notification->type, $forumTypes);
    }

    /**
     * Get user roles
     */
    private function getUserRoles(): array
    {
        if (method_exists($this->user, 'getRoleNames')) {
            return $this->user->getRoleNames()->toArray();
        }

        // Fallback for simple role system
        return [$this->user->role ?? 'Member'];
    }

    /**
     * Get forum ID from notification data
     */
    private function getForumId(): ?int
    {
        return $this->notification->data['forum_id'] ?? null;
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon(): string
    {
        $icons = [
            'thread_created' => 'fas fa-plus-circle',
            'thread_replied' => 'fas fa-reply',
            'comment_mention' => 'fas fa-at',
            'login_from_new_device' => 'fas fa-shield-alt',
            'password_changed' => 'fas fa-key',
            'product_out_of_stock' => 'fas fa-exclamation-triangle',
            'price_drop_alert' => 'fas fa-arrow-down',
            'wishlist_available' => 'fas fa-heart',
            'review_received' => 'fas fa-star',
            'seller_message' => 'fas fa-envelope',
            'buyer_message' => 'fas fa-envelope',
            'marketplace_message' => 'fas fa-envelope',
            'order_status_changed' => 'fas fa-shopping-cart',
            'order_payment_status_changed' => 'fas fa-credit-card',
            'product_approved' => 'fas fa-check-circle',
            'product_rejected' => 'fas fa-times-circle',
        ];

        return $icons[$this->notification->type] ?? 'fas fa-bell';
    }

    /**
     * Get notification color based on type and priority
     */
    private function getNotificationColor(): string
    {
        // Priority-based colors
        if ($this->notification->priority === 'high') {
            return '#dc3545'; // Red
        } elseif ($this->notification->priority === 'normal') {
            return '#ffc107'; // Yellow
        }

        // Type-based colors
        $colors = [
            'thread_created' => '#28a745',
            'thread_replied' => '#007bff',
            'comment_mention' => '#17a2b8',
            'login_from_new_device' => '#dc3545',
            'password_changed' => '#6f42c1',
            'product_out_of_stock' => '#dc3545',
            'price_drop_alert' => '#28a745',
            'wishlist_available' => '#e83e8c',
            'review_received' => '#ffc107',
            'seller_message' => '#007bff',
            'order_status_changed' => '#28a745',
            'product_approved' => '#28a745',
            'product_rejected' => '#dc3545',
        ];

        return $colors[$this->notification->type] ?? '#6c757d';
    }

    /**
     * Get action URL from notification data
     */
    private function getActionUrl(): ?string
    {
        return $this->notification->data['action_url'] ?? null;
    }

    /**
     * Get notification metadata
     */
    private function getNotificationMetadata(): array
    {
        return [
            'broadcast_time' => now()->toISOString(),
            'channels_count' => count($this->broadcastOn()),
            'is_real_time' => true,
            'notification_version' => '2.0',
        ];
    }
}
