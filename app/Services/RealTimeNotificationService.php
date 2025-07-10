<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Events\NotificationBroadcastEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class RealTimeNotificationService
{
    /**
     * Send real-time notification to user
     */
    public static function sendToUser(User $user, array $notificationData): bool
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => $notificationData['type'],
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'data' => $notificationData['data'] ?? [],
                'priority' => $notificationData['priority'] ?? 'normal',
            ]);

            // Broadcast immediately
            static::broadcastNotification($user, $notification);

            // Update user's unread count
            static::updateUnreadCount($user);

            // Store in cache for quick access
            static::cacheNotification($user, $notification);

            Log::info('Real-time notification sent', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'type' => $notification->type,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send real-time notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send real-time notification to multiple users
     */
    public static function sendToUsers(array $users, array $notificationData): array
    {
        $results = [];

        foreach ($users as $user) {
            $results[$user->id] = static::sendToUser($user, $notificationData);
        }

        return $results;
    }

    /**
     * Send real-time notification to users with specific role
     */
    public static function sendToRole(string $role, array $notificationData): int
    {
        $users = User::whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();

        $successCount = 0;
        foreach ($users as $user) {
            if (static::sendToUser($user, $notificationData)) {
                $successCount++;
            }
        }

        Log::info('Real-time notification sent to role', [
            'role' => $role,
            'users_count' => $users->count(),
            'success_count' => $successCount,
        ]);

        return $successCount;
    }

    /**
     * Broadcast notification event
     */
    private static function broadcastNotification(User $user, Notification $notification): void
    {
        try {
            // Check if user is online
            if (static::isUserOnline($user)) {
                broadcast(new NotificationBroadcastEvent($user, $notification))->toOthers();

                Log::debug('Notification broadcasted to online user', [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                ]);
            } else {
                // Store for offline delivery using dedicated service
                \App\Services\OfflineNotificationService::storeForOfflineUser($user, $notification);

                Log::debug('Notification stored for offline user', [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if user is currently online
     */
    public static function isUserOnline(User $user): bool
    {
        $cacheKey = "user_online_{$user->id}";
        return Cache::has($cacheKey);
    }

    /**
     * Mark user as online
     */
    public static function markUserOnline(User $user): void
    {
        $cacheKey = "user_online_{$user->id}";
        Cache::put($cacheKey, true, now()->addMinutes(5));

        // Store in Redis for WebSocket server (if Redis is available)
        if (static::isRedisAvailable()) {
            try {
                Redis::setex("user_online_{$user->id}", 300, json_encode([
                    'user_id' => $user->id,
                    'last_seen' => now()->toISOString(),
                    'channels' => static::getUserChannels($user),
                ]));
            } catch (\Exception $e) {
                Log::warning('Failed to store user online status in Redis', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Mark user as offline
     */
    public static function markUserOffline(User $user): void
    {
        $cacheKey = "user_online_{$user->id}";
        Cache::forget($cacheKey);

        // Remove from Redis (if Redis is available)
        if (static::isRedisAvailable()) {
            try {
                Redis::del("user_online_{$user->id}");
            } catch (\Exception $e) {
                Log::warning('Failed to remove user online status from Redis', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get user's WebSocket channels
     */
    private static function getUserChannels(User $user): array
    {
        $channels = ["user.{$user->id}"];

        // Add role-based channels
        if (method_exists($user, 'getRoleNames')) {
            foreach ($user->getRoleNames() as $role) {
                $channels[] = "notifications.role.{$role}";
            }
        }

        // Add marketplace channels
        if ($user->marketplaceSeller) {
            $channels[] = "marketplace.seller.{$user->marketplaceSeller->id}";
        }
        $channels[] = "marketplace.orders.{$user->id}";

        return $channels;
    }

    /**
     * Store notification for offline delivery
     */
    private static function storeForOfflineDelivery(User $user, Notification $notification): void
    {
        $cacheKey = "offline_notifications_{$user->id}";
        $offlineNotifications = Cache::get($cacheKey, []);

        $offlineNotifications[] = [
            'notification_id' => $notification->id,
            'stored_at' => now()->toISOString(),
        ];

        // Keep only last 50 offline notifications
        if (count($offlineNotifications) > 50) {
            $offlineNotifications = array_slice($offlineNotifications, -50);
        }

        Cache::put($cacheKey, $offlineNotifications, now()->addDays(7));
    }

    /**
     * Deliver offline notifications when user comes online
     */
    public static function deliverOfflineNotifications(User $user): int
    {
        // Use the dedicated OfflineNotificationService
        $results = \App\Services\OfflineNotificationService::deliverOfflineNotifications($user);

        // Count successful deliveries
        $deliveredCount = count(array_filter($results, fn($r) => $r['delivered']));

        Log::info('Offline notifications delivered via RealTimeNotificationService', [
            'user_id' => $user->id,
            'total_results' => count($results),
            'delivered_count' => $deliveredCount,
        ]);

        return $deliveredCount;
    }

    /**
     * Update user's unread notification count
     */
    private static function updateUnreadCount(User $user): void
    {
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $cacheKey = "user_unread_count_{$user->id}";
        Cache::put($cacheKey, $unreadCount, now()->addHours(1));

        // Broadcast unread count update
        try {
            broadcast(new \App\Events\UnreadCountUpdated($user, $unreadCount))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast unread count update', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user's unread notification count
     */
    public static function getUnreadCount(User $user): int
    {
        $cacheKey = "user_unread_count_{$user->id}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($user) {
            return Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        });
    }

    /**
     * Cache notification for quick access
     */
    private static function cacheNotification(User $user, Notification $notification): void
    {
        $cacheKey = "user_recent_notifications_{$user->id}";
        $recentNotifications = Cache::get($cacheKey, []);

        array_unshift($recentNotifications, [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'created_at' => $notification->created_at->toISOString(),
            'is_read' => $notification->is_read,
        ]);

        // Keep only last 20 notifications in cache
        if (count($recentNotifications) > 20) {
            $recentNotifications = array_slice($recentNotifications, 0, 20);
        }

        Cache::put($cacheKey, $recentNotifications, now()->addHours(2));
    }

    /**
     * Check if Redis is available
     */
    private static function isRedisAvailable(): bool
    {
        try {
            return class_exists('Redis') &&
                   config('database.redis.default') &&
                   Redis::ping();
        } catch (\Exception $e) {
            return false;
        }
    }
}
