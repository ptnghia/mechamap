<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * User Activity Update Event
 * Broadcasts user activity updates in real-time
 */
class UserActivityUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $activity;
    public $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, array $activity)
    {
        $this->userId = $userId;
        $this->activity = $activity;
        $this->prepareBroadcastData();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // User's private channel for personal activity updates
        $channels[] = new PrivateChannel('user.' . $this->userId);

        // Global activity channel for public activities
        if ($this->isPublicActivity()) {
            $channels[] = new Channel('activity.global');
        }

        // Friend/follower channels for social activities
        if ($this->isSocialActivity()) {
            $channels[] = new Channel('activity.social.' . $this->userId);
        }

        // Admin channel for monitoring
        if ($this->isAdminRelevant()) {
            $channels[] = new PrivateChannel('admin.activity');
        }

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'user.activity.updated';
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
        // Don't broadcast sensitive activities
        if ($this->isSensitiveActivity()) {
            return false;
        }

        // Don't broadcast if user has disabled activity sharing
        if (!$this->isActivitySharingEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare broadcast data
     */
    private function prepareBroadcastData(): void
    {
        $this->broadcastData = [
            'user_id' => $this->userId,
            'activity_type' => $this->activity['type'] ?? 'unknown',
            'activity_data' => $this->sanitizeActivityData(),
            'timestamp' => $this->activity['timestamp'] ?? now()->toISOString(),
            'session_id' => session()->getId(),
            'ip_address' => $this->shouldIncludeIP() ? request()->ip() : null,
            'user_agent' => $this->shouldIncludeUserAgent() ? request()->userAgent() : null,
        ];

        // Add user information for public activities
        if ($this->isPublicActivity()) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                $this->broadcastData['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'avatar' => $user->avatar,
                    'role' => $user->role,
                    'is_online' => $this->isUserOnline(),
                ];
            }
        }

        // Add activity-specific metadata
        $this->addActivityMetadata();
    }

    /**
     * Sanitize activity data for broadcasting
     */
    private function sanitizeActivityData(): array
    {
        $data = $this->activity;
        
        // Remove sensitive information
        unset($data['password'], $data['token'], $data['secret'], $data['private_key']);
        
        // Limit data size
        if (isset($data['content']) && strlen($data['content']) > 500) {
            $data['content'] = substr($data['content'], 0, 500) . '...';
        }

        return $data;
    }

    /**
     * Add activity-specific metadata
     */
    private function addActivityMetadata(): void
    {
        $activityType = $this->activity['type'] ?? 'unknown';

        switch ($activityType) {
            case 'user_online':
            case 'user_offline':
                $this->broadcastData['online_status'] = $activityType === 'user_online';
                $this->broadcastData['last_seen'] = now()->toISOString();
                break;

            case 'typing_indicator':
                $this->broadcastData['typing'] = $this->activity['typing'] ?? false;
                $this->broadcastData['chat_id'] = $this->activity['chat_id'] ?? null;
                break;

            case 'page_view':
                $this->broadcastData['page'] = $this->activity['page'] ?? null;
                $this->broadcastData['referrer'] = $this->activity['referrer'] ?? null;
                break;

            case 'thread_created':
            case 'thread_replied':
                $this->broadcastData['thread_id'] = $this->activity['thread_id'] ?? null;
                $this->broadcastData['forum_id'] = $this->activity['forum_id'] ?? null;
                break;

            case 'product_viewed':
            case 'product_purchased':
                $this->broadcastData['product_id'] = $this->activity['product_id'] ?? null;
                $this->broadcastData['category_id'] = $this->activity['category_id'] ?? null;
                break;

            case 'file_uploaded':
            case 'file_downloaded':
                $this->broadcastData['file_id'] = $this->activity['file_id'] ?? null;
                $this->broadcastData['file_type'] = $this->activity['file_type'] ?? null;
                $this->broadcastData['file_size'] = $this->activity['file_size'] ?? null;
                break;

            case 'search_performed':
                $this->broadcastData['search_query'] = $this->activity['query'] ?? null;
                $this->broadcastData['results_count'] = $this->activity['results_count'] ?? 0;
                break;
        }
    }

    /**
     * Check if this is a public activity
     */
    private function isPublicActivity(): bool
    {
        $publicActivities = [
            'user_online',
            'user_offline',
            'thread_created',
            'thread_replied',
            'product_created',
            'achievement_earned',
            'profile_updated',
        ];

        return in_array($this->activity['type'] ?? '', $publicActivities);
    }

    /**
     * Check if this is a social activity
     */
    private function isSocialActivity(): bool
    {
        $socialActivities = [
            'friend_request_sent',
            'friend_request_accepted',
            'message_sent',
            'profile_visited',
            'post_liked',
            'comment_added',
        ];

        return in_array($this->activity['type'] ?? '', $socialActivities);
    }

    /**
     * Check if this activity is relevant for admins
     */
    private function isAdminRelevant(): bool
    {
        $adminRelevantActivities = [
            'login_failed',
            'suspicious_activity',
            'security_violation',
            'bulk_operation',
            'admin_action',
            'system_error',
        ];

        return in_array($this->activity['type'] ?? '', $adminRelevantActivities);
    }

    /**
     * Check if this is a sensitive activity
     */
    private function isSensitiveActivity(): bool
    {
        $sensitiveActivities = [
            'password_changed',
            'email_changed',
            'payment_processed',
            'admin_login',
            'security_settings_changed',
        ];

        return in_array($this->activity['type'] ?? '', $sensitiveActivities);
    }

    /**
     * Check if activity sharing is enabled for user
     */
    private function isActivitySharingEnabled(): bool
    {
        $user = \App\Models\User::find($this->userId);
        
        if (!$user) {
            return false;
        }

        return $user->privacy_settings['share_activity'] ?? true;
    }

    /**
     * Check if user is currently online
     */
    private function isUserOnline(): bool
    {
        return cache()->get("user_online_{$this->userId}", false);
    }

    /**
     * Check if IP address should be included
     */
    private function shouldIncludeIP(): bool
    {
        return $this->isAdminRelevant() || $this->isSensitiveActivity();
    }

    /**
     * Check if user agent should be included
     */
    private function shouldIncludeUserAgent(): bool
    {
        return $this->isAdminRelevant();
    }

    /**
     * Get activity priority for processing
     */
    public function getActivityPriority(): string
    {
        if ($this->isAdminRelevant()) {
            return 'high';
        }

        if ($this->isPublicActivity()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get activity retention period in days
     */
    public function getRetentionPeriod(): int
    {
        $activityType = $this->activity['type'] ?? 'unknown';

        $retentionPeriods = [
            'login_failed' => 90,
            'security_violation' => 365,
            'admin_action' => 365,
            'payment_processed' => 2555, // 7 years
            'user_online' => 7,
            'user_offline' => 7,
            'page_view' => 30,
            'search_performed' => 30,
        ];

        return $retentionPeriods[$activityType] ?? 30; // Default 30 days
    }

    /**
     * Get activity metadata
     */
    public function getMetadata(): array
    {
        return [
            'event_type' => 'user_activity_update',
            'user_id' => $this->userId,
            'activity_type' => $this->activity['type'] ?? 'unknown',
            'priority' => $this->getActivityPriority(),
            'retention_days' => $this->getRetentionPeriod(),
            'is_public' => $this->isPublicActivity(),
            'is_social' => $this->isSocialActivity(),
            'is_admin_relevant' => $this->isAdminRelevant(),
            'is_sensitive' => $this->isSensitiveActivity(),
            'broadcast_channels' => count($this->broadcastOn()),
            'created_at' => now()->toISOString(),
        ];
    }
}
