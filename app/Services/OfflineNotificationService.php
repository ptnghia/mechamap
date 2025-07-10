<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessOfflineNotifications;

class OfflineNotificationService
{
    /**
     * Store notification for offline user
     */
    public static function storeForOfflineUser(User $user, Notification $notification): void
    {
        try {
            // Store in cache for immediate access when user comes online
            static::cacheOfflineNotification($user, $notification);

            // Store in database for persistence
            static::persistOfflineNotification($user, $notification);

            // Schedule delivery attempt
            static::scheduleDeliveryAttempt($user, $notification);

            Log::info('Notification stored for offline user', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'type' => $notification->type,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store notification for offline user', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Deliver all offline notifications when user comes online
     */
    public static function deliverOfflineNotifications(User $user): array
    {
        try {
            $deliveryResults = [];

            // Get cached offline notifications
            $cachedNotifications = static::getCachedOfflineNotifications($user);
            
            // Get persisted offline notifications
            $persistedNotifications = static::getPersistedOfflineNotifications($user);

            // Merge and deduplicate
            $allNotifications = static::mergeNotifications($cachedNotifications, $persistedNotifications);

            foreach ($allNotifications as $notificationData) {
                $notification = Notification::find($notificationData['notification_id']);
                
                if ($notification && !$notification->is_read) {
                    $delivered = static::deliverNotification($user, $notification);
                    $deliveryResults[] = [
                        'notification_id' => $notification->id,
                        'delivered' => $delivered,
                        'type' => $notification->type,
                    ];
                }
            }

            // Clear offline notifications after delivery
            static::clearOfflineNotifications($user);

            Log::info('Offline notifications delivered', [
                'user_id' => $user->id,
                'total_notifications' => count($allNotifications),
                'delivered_count' => count(array_filter($deliveryResults, fn($r) => $r['delivered'])),
            ]);

            return $deliveryResults;

        } catch (\Exception $e) {
            Log::error('Failed to deliver offline notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Cache offline notification for quick access
     */
    private static function cacheOfflineNotification(User $user, Notification $notification): void
    {
        $cacheKey = "offline_notifications_{$user->id}";
        $notifications = Cache::get($cacheKey, []);

        $notifications[] = [
            'notification_id' => $notification->id,
            'type' => $notification->type,
            'priority' => $notification->priority,
            'stored_at' => now()->toISOString(),
            'delivery_attempts' => 0,
        ];

        // Keep only last 100 offline notifications
        if (count($notifications) > 100) {
            $notifications = array_slice($notifications, -100);
        }

        Cache::put($cacheKey, $notifications, now()->addDays(7));
    }

    /**
     * Persist offline notification in database
     */
    private static function persistOfflineNotification(User $user, Notification $notification): void
    {
        // Update notification with offline status
        $notification->update([
            'data' => array_merge($notification->data ?? [], [
                'offline_stored' => true,
                'offline_stored_at' => now()->toISOString(),
                'delivery_attempts' => 0,
            ])
        ]);
    }

    /**
     * Schedule delivery attempt for later
     */
    private static function scheduleDeliveryAttempt(User $user, Notification $notification): void
    {
        // Schedule delivery attempts at increasing intervals
        $delays = [
            now()->addMinutes(5),   // First attempt after 5 minutes
            now()->addMinutes(15),  // Second attempt after 15 minutes
            now()->addHour(),       // Third attempt after 1 hour
            now()->addHours(6),     // Fourth attempt after 6 hours
            now()->addDay(),        // Final attempt after 1 day
        ];

        foreach ($delays as $delay) {
            ProcessOfflineNotifications::dispatch($user, $notification)
                ->delay($delay)
                ->onQueue('notifications');
        }
    }

    /**
     * Get cached offline notifications
     */
    private static function getCachedOfflineNotifications(User $user): array
    {
        $cacheKey = "offline_notifications_{$user->id}";
        return Cache::get($cacheKey, []);
    }

    /**
     * Get persisted offline notifications from database
     */
    private static function getPersistedOfflineNotifications(User $user): array
    {
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->whereJsonContains('data->offline_stored', true)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $notifications->map(function ($notification) {
            return [
                'notification_id' => $notification->id,
                'type' => $notification->type,
                'priority' => $notification->priority,
                'stored_at' => $notification->data['offline_stored_at'] ?? $notification->created_at->toISOString(),
                'delivery_attempts' => $notification->data['delivery_attempts'] ?? 0,
            ];
        })->toArray();
    }

    /**
     * Merge and deduplicate notifications
     */
    private static function mergeNotifications(array $cached, array $persisted): array
    {
        $merged = [];
        $seen = [];

        // Add cached notifications first (more recent)
        foreach ($cached as $notification) {
            $id = $notification['notification_id'];
            if (!isset($seen[$id])) {
                $merged[] = $notification;
                $seen[$id] = true;
            }
        }

        // Add persisted notifications if not already seen
        foreach ($persisted as $notification) {
            $id = $notification['notification_id'];
            if (!isset($seen[$id])) {
                $merged[] = $notification;
                $seen[$id] = true;
            }
        }

        // Sort by priority and timestamp
        usort($merged, function ($a, $b) {
            $priorityOrder = ['high' => 3, 'normal' => 2, 'low' => 1];
            $aPriority = $priorityOrder[$a['priority']] ?? 2;
            $bPriority = $priorityOrder[$b['priority']] ?? 2;

            if ($aPriority !== $bPriority) {
                return $bPriority - $aPriority; // Higher priority first
            }

            return strtotime($b['stored_at']) - strtotime($a['stored_at']); // Newer first
        });

        return $merged;
    }

    /**
     * Deliver individual notification
     */
    private static function deliverNotification(User $user, Notification $notification): bool
    {
        try {
            // Broadcast the notification
            broadcast(new \App\Events\NotificationBroadcastEvent($user, $notification))->toOthers();

            // Update delivery status
            $data = $notification->data ?? [];
            $data['delivered_at'] = now()->toISOString();
            $data['delivery_method'] = 'offline_delivery';
            $notification->update(['data' => $data]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to deliver offline notification', [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Clear offline notifications after delivery
     */
    private static function clearOfflineNotifications(User $user): void
    {
        // Clear cache
        $cacheKey = "offline_notifications_{$user->id}";
        Cache::forget($cacheKey);

        // Update database records
        Notification::where('user_id', $user->id)
            ->whereJsonContains('data->offline_stored', true)
            ->update([
                'data->offline_stored' => false,
                'data->offline_cleared_at' => now()->toISOString(),
            ]);
    }

    /**
     * Get offline notification statistics
     */
    public static function getOfflineStatistics(User $user): array
    {
        $cached = static::getCachedOfflineNotifications($user);
        $persisted = static::getPersistedOfflineNotifications($user);

        return [
            'cached_count' => count($cached),
            'persisted_count' => count($persisted),
            'total_unique' => count(static::mergeNotifications($cached, $persisted)),
            'by_priority' => [
                'high' => count(array_filter($cached, fn($n) => $n['priority'] === 'high')),
                'normal' => count(array_filter($cached, fn($n) => $n['priority'] === 'normal')),
                'low' => count(array_filter($cached, fn($n) => $n['priority'] === 'low')),
            ],
            'oldest_notification' => !empty($cached) ? min(array_column($cached, 'stored_at')) : null,
        ];
    }

    /**
     * Clean up old offline notifications
     */
    public static function cleanupOldOfflineNotifications(): int
    {
        $cleanedUp = 0;

        try {
            // Clean up notifications older than 7 days
            $cutoffDate = now()->subDays(7);

            $cleanedUp = Notification::whereJsonContains('data->offline_stored', true)
                ->where('created_at', '<', $cutoffDate)
                ->update([
                    'data->offline_stored' => false,
                    'data->offline_cleaned_up' => true,
                    'data->cleanup_date' => now()->toISOString(),
                ]);

            Log::info('Cleaned up old offline notifications', [
                'cleaned_up_count' => $cleanedUp,
                'cutoff_date' => $cutoffDate->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old offline notifications', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleanedUp;
    }
}
