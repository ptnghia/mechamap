<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Services\RedisClusterManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Exception;

class EnhancedNotificationCacheService
{
    private RedisClusterManager $clusterManager;
    private array $config;

    public function __construct(RedisClusterManager $clusterManager)
    {
        $this->clusterManager = $clusterManager;
        $this->config = config('redis-cluster');
    }

    /**
     * Cache user notifications with cluster distribution
     */
    public function cacheUserNotifications(User $user, Collection $notifications, int $ttl = 3600): bool
    {
        try {
            $cacheKey = "user_notifications:{$user->id}";
            $connection = $this->clusterManager->getConnection('notifications');

            // Prepare notification data for caching
            $notificationData = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'data' => $notification->data,
                    'priority' => $notification->priority,
                    'is_read' => $notification->is_read,
                    'read_at' => $notification->read_at?->toISOString(),
                    'created_at' => $notification->created_at->toISOString(),
                ];
            })->toArray();

            // Use pipeline for better performance
            if ($this->config['performance']['pipeline_enabled'] ?? false) {
                $pipeline = $connection->pipeline();
                $pipeline->setex($cacheKey, $ttl, json_encode($notificationData));
                $pipeline->setex($cacheKey . ':count', $ttl, count($notificationData));
                $pipeline->setex($cacheKey . ':unread_count', $ttl, $notifications->where('is_read', false)->count());
                $pipeline->exec();
            } else {
                $connection->setex($cacheKey, $ttl, json_encode($notificationData));
                $connection->setex($cacheKey . ':count', $ttl, count($notificationData));
                $connection->setex($cacheKey . ':unread_count', $ttl, $notifications->where('is_read', false)->count());
            }

            // Cache individual notifications for quick access
            foreach ($notifications as $notification) {
                $this->cacheIndividualNotification($notification, $ttl);
            }

            Log::debug("Cached user notifications", [
                'user_id' => $user->id,
                'count' => count($notificationData),
                'ttl' => $ttl,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to cache user notifications", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cached user notifications
     */
    public function getCachedUserNotifications(User $user): ?array
    {
        try {
            $cacheKey = "user_notifications:{$user->id}";
            $connection = $this->clusterManager->getConnection('notifications');

            $cachedData = $connection->get($cacheKey);
            
            if ($cachedData) {
                $notifications = json_decode($cachedData, true);
                
                Log::debug("Retrieved cached user notifications", [
                    'user_id' => $user->id,
                    'count' => count($notifications),
                ]);
                
                return $notifications;
            }

            return null;

        } catch (Exception $e) {
            Log::error("Failed to get cached user notifications", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cache individual notification
     */
    public function cacheIndividualNotification(Notification $notification, int $ttl = 3600): bool
    {
        try {
            $cacheKey = "notification:{$notification->id}";
            $connection = $this->clusterManager->getConnection('notifications');

            $notificationData = [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'data' => $notification->data,
                'priority' => $notification->priority,
                'is_read' => $notification->is_read,
                'read_at' => $notification->read_at?->toISOString(),
                'created_at' => $notification->created_at->toISOString(),
                'updated_at' => $notification->updated_at->toISOString(),
            ];

            $connection->setex($cacheKey, $ttl, json_encode($notificationData));

            return true;

        } catch (Exception $e) {
            Log::error("Failed to cache individual notification", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cached individual notification
     */
    public function getCachedNotification(int $notificationId): ?array
    {
        try {
            $cacheKey = "notification:{$notificationId}";
            $connection = $this->clusterManager->getConnection('notifications');

            $cachedData = $connection->get($cacheKey);
            
            if ($cachedData) {
                return json_decode($cachedData, true);
            }

            return null;

        } catch (Exception $e) {
            Log::error("Failed to get cached notification", [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cache notification analytics data
     */
    public function cacheAnalyticsData(string $key, array $data, int $ttl = 900): bool
    {
        try {
            $cacheKey = "analytics:{$key}";
            $connection = $this->clusterManager->getConnection('analytics');

            // Add metadata
            $cacheData = [
                'data' => $data,
                'cached_at' => now()->toISOString(),
                'ttl' => $ttl,
                'version' => '1.0',
            ];

            $connection->setex($cacheKey, $ttl, json_encode($cacheData));

            Log::debug("Cached analytics data", [
                'key' => $key,
                'ttl' => $ttl,
                'size' => strlen(json_encode($cacheData)),
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to cache analytics data", [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cached analytics data
     */
    public function getCachedAnalyticsData(string $key): ?array
    {
        try {
            $cacheKey = "analytics:{$key}";
            $connection = $this->clusterManager->getConnection('analytics');

            $cachedData = $connection->get($cacheKey);
            
            if ($cachedData) {
                $cacheData = json_decode($cachedData, true);
                
                Log::debug("Retrieved cached analytics data", [
                    'key' => $key,
                    'cached_at' => $cacheData['cached_at'] ?? 'unknown',
                ]);
                
                return $cacheData['data'] ?? null;
            }

            return null;

        } catch (Exception $e) {
            Log::error("Failed to get cached analytics data", [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cache engagement events with time-based partitioning
     */
    public function cacheEngagementEvents(string $timeKey, array $events, int $ttl = 7200): bool
    {
        try {
            $cacheKey = "engagement_events:{$timeKey}";
            $connection = $this->clusterManager->getConnection('analytics');

            // Compress events if enabled and size exceeds threshold
            $eventsJson = json_encode($events);
            $compressionThreshold = $this->config['performance']['compression_threshold'] ?? 1024;
            
            if (strlen($eventsJson) > $compressionThreshold) {
                $eventsJson = gzcompress($eventsJson, 6);
                $cacheKey .= ':compressed';
            }

            $connection->setex($cacheKey, $ttl, $eventsJson);

            Log::debug("Cached engagement events", [
                'time_key' => $timeKey,
                'event_count' => count($events),
                'ttl' => $ttl,
                'compressed' => strpos($cacheKey, ':compressed') !== false,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to cache engagement events", [
                'time_key' => $timeKey,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cached engagement events
     */
    public function getCachedEngagementEvents(string $timeKey): ?array
    {
        try {
            $connection = $this->clusterManager->getConnection('analytics');
            
            // Try compressed version first
            $compressedKey = "engagement_events:{$timeKey}:compressed";
            $cachedData = $connection->get($compressedKey);
            
            if ($cachedData) {
                $decompressed = gzuncompress($cachedData);
                if ($decompressed !== false) {
                    return json_decode($decompressed, true);
                }
            }

            // Try uncompressed version
            $cacheKey = "engagement_events:{$timeKey}";
            $cachedData = $connection->get($cacheKey);
            
            if ($cachedData) {
                return json_decode($cachedData, true);
            }

            return null;

        } catch (Exception $e) {
            Log::error("Failed to get cached engagement events", [
                'time_key' => $timeKey,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Invalidate user notification cache
     */
    public function invalidateUserCache(User $user): bool
    {
        try {
            $connection = $this->clusterManager->getConnection('notifications');
            
            $keys = [
                "user_notifications:{$user->id}",
                "user_notifications:{$user->id}:count",
                "user_notifications:{$user->id}:unread_count",
            ];

            foreach ($keys as $key) {
                $connection->del($key);
            }

            Log::debug("Invalidated user notification cache", [
                'user_id' => $user->id,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to invalidate user cache", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Invalidate notification cache
     */
    public function invalidateNotificationCache(int $notificationId): bool
    {
        try {
            $connection = $this->clusterManager->getConnection('notifications');
            $cacheKey = "notification:{$notificationId}";
            
            $connection->del($cacheKey);

            Log::debug("Invalidated notification cache", [
                'notification_id' => $notificationId,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Failed to invalidate notification cache", [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStatistics(): array
    {
        try {
            $stats = [
                'clusters' => [],
                'total_memory_usage' => 0,
                'total_keys' => 0,
                'hit_rate' => 0,
            ];

            $clusters = ['notifications', 'analytics'];
            
            foreach ($clusters as $cluster) {
                $clusterStats = $this->clusterManager->getClusterStatistics($cluster);
                $stats['clusters'][$cluster] = $clusterStats;
                
                if (isset($clusterStats['memory_usage'])) {
                    $stats['total_memory_usage'] += array_sum($clusterStats['memory_usage']);
                }
            }

            return $stats;

        } catch (Exception $e) {
            Log::error("Failed to get cache statistics", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUpCache(): bool
    {
        try {
            Log::info("Starting cache warm-up process");

            // Warm up recent notifications for active users
            $activeUsers = User::where('last_login_at', '>=', now()->subDays(7))
                ->limit(100)
                ->get();

            foreach ($activeUsers as $user) {
                $notifications = Notification::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();

                if ($notifications->isNotEmpty()) {
                    $this->cacheUserNotifications($user, $notifications, 7200);
                }
            }

            Log::info("Cache warm-up completed", [
                'users_processed' => $activeUsers->count(),
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Cache warm-up failed", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Clean up expired cache entries
     */
    public function cleanupExpiredCache(): int
    {
        try {
            $cleanedUp = 0;
            $clusters = ['notifications', 'analytics'];

            foreach ($clusters as $cluster) {
                $connection = $this->clusterManager->getConnection($cluster);
                
                // This would require scanning keys, which is expensive in production
                // In a real implementation, you'd use a different strategy like
                // tracking keys with TTL in a separate data structure
                
                Log::info("Cleaned up expired cache entries", [
                    'cluster' => $cluster,
                    'cleaned_up' => $cleanedUp,
                ]);
            }

            return $cleanedUp;

        } catch (Exception $e) {
            Log::error("Cache cleanup failed", [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
