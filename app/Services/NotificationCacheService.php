<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class NotificationCacheService
{
    private const CACHE_PREFIX = 'notifications:';
    private const UNREAD_COUNT_PREFIX = 'unread_count:';
    private const USER_NOTIFICATIONS_PREFIX = 'user_notifications:';
    private const CACHE_TTL = 3600; // 1 hour
    private const UNREAD_COUNT_TTL = 300; // 5 minutes

    /**
     * Get cached unread count for user
     */
    public static function getUnreadCount(User $user): int
    {
        $cacheKey = self::CACHE_PREFIX . self::UNREAD_COUNT_PREFIX . $user->id;
        
        return Cache::remember($cacheKey, self::UNREAD_COUNT_TTL, function () use ($user) {
            return Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        });
    }

    /**
     * Get cached user notifications
     */
    public static function getUserNotifications(User $user, int $page = 1, int $perPage = 20): array
    {
        $cacheKey = self::CACHE_PREFIX . self::USER_NOTIFICATIONS_PREFIX . $user->id . ':' . $page . ':' . $perPage;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $page, $perPage) {
            $offset = ($page - 1) * $perPage;
            
            return Notification::where('user_id', $user->id)
                ->with(['user:id,name,avatar'])
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get()
                ->toArray();
        });
    }

    /**
     * Invalidate user notification caches
     */
    public static function invalidateUserCache(User $user): void
    {
        try {
            // Clear unread count cache
            $unreadCountKey = self::CACHE_PREFIX . self::UNREAD_COUNT_PREFIX . $user->id;
            Cache::forget($unreadCountKey);
            
            // Clear user notifications cache (all pages)
            $pattern = self::CACHE_PREFIX . self::USER_NOTIFICATIONS_PREFIX . $user->id . ':*';
            self::clearCacheByPattern($pattern);
            
            Log::info("Notification cache invalidated for user {$user->id}");
            
        } catch (\Exception $e) {
            Log::error('Failed to invalidate notification cache: ' . $e->getMessage());
        }
    }

    /**
     * Cache notification by ID
     */
    public static function cacheNotification(Notification $notification): void
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'notification:' . $notification->id;
            Cache::put($cacheKey, $notification->toArray(), self::CACHE_TTL);
            
        } catch (\Exception $e) {
            Log::error('Failed to cache notification: ' . $e->getMessage());
        }
    }

    /**
     * Get cached notification by ID
     */
    public static function getCachedNotification(string $notificationId): ?array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'notification:' . $notificationId;
            return Cache::get($cacheKey);
            
        } catch (\Exception $e) {
            Log::error('Failed to get cached notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Increment unread count cache
     */
    public static function incrementUnreadCount(User $user): void
    {
        try {
            $cacheKey = self::CACHE_PREFIX . self::UNREAD_COUNT_PREFIX . $user->id;
            
            if (Cache::has($cacheKey)) {
                Cache::increment($cacheKey);
            } else {
                // If cache doesn't exist, set it to current count + 1
                $currentCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                Cache::put($cacheKey, $currentCount, self::UNREAD_COUNT_TTL);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to increment unread count cache: ' . $e->getMessage());
        }
    }

    /**
     * Decrement unread count cache
     */
    public static function decrementUnreadCount(User $user, int $count = 1): void
    {
        try {
            $cacheKey = self::CACHE_PREFIX . self::UNREAD_COUNT_PREFIX . $user->id;
            
            if (Cache::has($cacheKey)) {
                $currentValue = Cache::get($cacheKey);
                $newValue = max(0, $currentValue - $count);
                Cache::put($cacheKey, $newValue, self::UNREAD_COUNT_TTL);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to decrement unread count cache: ' . $e->getMessage());
        }
    }

    /**
     * Cache notification types for quick access
     */
    public static function cacheNotificationTypes(): void
    {
        try {
            $types = [
                'thread_created', 'thread_replied', 'comment_mention',
                'login_from_new_device', 'password_changed',
                'product_out_of_stock', 'price_drop_alert', 'wishlist_available',
                'review_received', 'seller_message', 'user_followed',
                'achievement_unlocked', 'weekly_digest'
            ];
            
            $cacheKey = self::CACHE_PREFIX . 'notification_types';
            Cache::put($cacheKey, $types, 86400); // Cache for 24 hours
            
        } catch (\Exception $e) {
            Log::error('Failed to cache notification types: ' . $e->getMessage());
        }
    }

    /**
     * Get cached notification types
     */
    public static function getCachedNotificationTypes(): array
    {
        try {
            $cacheKey = self::CACHE_PREFIX . 'notification_types';
            return Cache::get($cacheKey, []);
            
        } catch (\Exception $e) {
            Log::error('Failed to get cached notification types: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear cache by pattern (Redis specific)
     */
    private static function clearCacheByPattern(string $pattern): void
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // For non-Redis cache drivers, we can't use patterns
                // This is a limitation, but we'll log it
                Log::warning('Cache pattern clearing not supported for non-Redis cache driver');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to clear cache by pattern: ' . $e->getMessage());
        }
    }

    /**
     * Warm up cache for active users
     */
    public static function warmUpCache(): void
    {
        try {
            // Get active users (logged in within last 7 days)
            $activeUsers = User::where('last_seen_at', '>=', now()->subDays(7))
                ->where('is_active', true)
                ->limit(100) // Limit to prevent memory issues
                ->get();

            foreach ($activeUsers as $user) {
                // Pre-cache unread count
                self::getUnreadCount($user);
                
                // Pre-cache first page of notifications
                self::getUserNotifications($user, 1, 20);
            }
            
            // Cache notification types
            self::cacheNotificationTypes();
            
            Log::info('Notification cache warmed up for ' . $activeUsers->count() . ' active users');
            
        } catch (\Exception $e) {
            Log::error('Failed to warm up notification cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear all notification caches
     */
    public static function clearAllCache(): void
    {
        try {
            $pattern = self::CACHE_PREFIX . '*';
            self::clearCacheByPattern($pattern);
            
            Log::info('All notification caches cleared');
            
        } catch (\Exception $e) {
            Log::error('Failed to clear all notification caches: ' . $e->getMessage());
        }
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        try {
            $stats = [
                'cache_driver' => config('cache.default'),
                'redis_connected' => false,
                'total_keys' => 0,
                'memory_usage' => 0,
            ];
            
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $stats['redis_connected'] = true;
                
                $keys = $redis->keys(self::CACHE_PREFIX . '*');
                $stats['total_keys'] = count($keys);
                
                $info = $redis->info('memory');
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'Unknown';
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Failed to get cache stats: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
