<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationDeliveryOptimizationService
{
    /**
     * Optimize delivery timing for user
     */
    public static function getOptimalDeliveryTime(User $user, string $notificationType): Carbon
    {
        try {
            // Get user's optimal time based on historical engagement
            $optimalHour = static::getUserOptimalHour($user, $notificationType);
            
            // Get user's timezone
            $timezone = $user->timezone ?? config('app.timezone');
            
            // Calculate next optimal delivery time
            $now = now($timezone);
            $optimalTime = $now->copy()->hour($optimalHour)->minute(0)->second(0);
            
            // If optimal time has passed today, schedule for tomorrow
            if ($optimalTime <= $now) {
                $optimalTime->addDay();
            }
            
            // Apply frequency limits
            $optimalTime = static::applyFrequencyLimits($user, $notificationType, $optimalTime);
            
            // Apply quiet hours
            $optimalTime = static::applyQuietHours($user, $optimalTime);
            
            Log::debug("Calculated optimal delivery time", [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'optimal_time' => $optimalTime->toISOString(),
                'optimal_hour' => $optimalHour,
            ]);
            
            return $optimalTime;

        } catch (\Exception $e) {
            Log::error("Failed to calculate optimal delivery time", [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'error' => $e->getMessage(),
            ]);
            
            // Fallback to immediate delivery
            return now();
        }
    }

    /**
     * Get user's optimal hour based on historical engagement
     */
    private static function getUserOptimalHour(User $user, string $notificationType): int
    {
        $cacheKey = "user_optimal_hour_{$user->id}_{$notificationType}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $notificationType) {
            // Get engagement data from last 30 days
            $engagementData = static::getUserEngagementByHour($user, $notificationType);
            
            if (empty($engagementData)) {
                // Use global optimal hours if no user data
                return static::getGlobalOptimalHour($notificationType);
            }
            
            // Find hour with highest engagement rate
            $bestHour = 9; // Default to 9 AM
            $bestRate = 0;
            
            foreach ($engagementData as $hour => $data) {
                $rate = $data['total_sent'] > 0 ? 
                    ($data['total_engaged'] / $data['total_sent']) * 100 : 0;
                
                if ($rate > $bestRate) {
                    $bestRate = $rate;
                    $bestHour = $hour;
                }
            }
            
            return $bestHour;
        });
    }

    /**
     * Get user engagement by hour
     */
    private static function getUserEngagementByHour(User $user, string $notificationType): array
    {
        $startDate = now()->subDays(30);
        
        $engagementData = DB::table('notifications')
            ->where('user_id', $user->id)
            ->where('type', $notificationType)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                HOUR(created_at) as hour,
                COUNT(*) as total_sent,
                SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as total_read,
                SUM(CASE WHEN JSON_EXTRACT(data, "$.clicked") = true THEN 1 ELSE 0 END) as total_clicked
            ')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->get()
            ->keyBy('hour')
            ->toArray();

        // Convert to array and calculate engagement
        $result = [];
        foreach ($engagementData as $hour => $data) {
            $result[$hour] = [
                'total_sent' => $data->total_sent,
                'total_engaged' => $data->total_read + $data->total_clicked,
            ];
        }

        return $result;
    }

    /**
     * Get global optimal hour for notification type
     */
    private static function getGlobalOptimalHour(string $notificationType): int
    {
        $cacheKey = "global_optimal_hour_{$notificationType}";
        
        return Cache::remember($cacheKey, now()->addHours(12), function () use ($notificationType) {
            // Default optimal hours by notification type
            $defaultHours = [
                'thread_created' => 9,      // 9 AM - work start
                'thread_replied' => 14,     // 2 PM - afternoon break
                'comment_mention' => 10,    // 10 AM - morning check
                'product_out_of_stock' => 8, // 8 AM - early morning
                'order_status_changed' => 16, // 4 PM - afternoon
                'review_received' => 11,    // 11 AM - late morning
                'wishlist_available' => 9,  // 9 AM - morning shopping
                'seller_message' => 15,     // 3 PM - business hours
                'login_from_new_device' => 0, // Immediate for security
                'password_changed' => 0,    // Immediate for security
            ];

            if (isset($defaultHours[$notificationType])) {
                return $defaultHours[$notificationType];
            }

            // Calculate from global data
            $globalData = DB::table('notifications')
                ->where('type', $notificationType)
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('
                    HOUR(created_at) as hour,
                    COUNT(*) as total_sent,
                    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as total_read,
                    SUM(CASE WHEN JSON_EXTRACT(data, "$.clicked") = true THEN 1 ELSE 0 END) as total_clicked
                ')
                ->groupBy(DB::raw('HOUR(created_at)'))
                ->get();

            $bestHour = 9; // Default
            $bestRate = 0;

            foreach ($globalData as $data) {
                $rate = $data->total_sent > 0 ? 
                    (($data->total_read + $data->total_clicked) / $data->total_sent) * 100 : 0;
                
                if ($rate > $bestRate) {
                    $bestRate = $rate;
                    $bestHour = $data->hour;
                }
            }

            return $bestHour;
        });
    }

    /**
     * Apply frequency limits
     */
    private static function applyFrequencyLimits(User $user, string $notificationType, Carbon $optimalTime): Carbon
    {
        // Get user's notification frequency preferences
        $preferences = static::getUserNotificationPreferences($user);
        $maxPerDay = $preferences['max_per_day'][$notificationType] ?? static::getDefaultMaxPerDay($notificationType);
        
        // Count notifications sent today
        $sentToday = Notification::where('user_id', $user->id)
            ->where('type', $notificationType)
            ->whereDate('created_at', $optimalTime->toDateString())
            ->count();

        if ($sentToday >= $maxPerDay) {
            // Delay to next day
            $optimalTime->addDay();
            Log::debug("Applied frequency limit delay", [
                'user_id' => $user->id,
                'notification_type' => $notificationType,
                'sent_today' => $sentToday,
                'max_per_day' => $maxPerDay,
            ]);
        }

        return $optimalTime;
    }

    /**
     * Apply quiet hours
     */
    private static function applyQuietHours(User $user, Carbon $optimalTime): Carbon
    {
        $preferences = static::getUserNotificationPreferences($user);
        $quietStart = $preferences['quiet_hours']['start'] ?? 22; // 10 PM
        $quietEnd = $preferences['quiet_hours']['end'] ?? 7;     // 7 AM

        $hour = $optimalTime->hour;

        // Check if time falls in quiet hours
        if ($quietStart > $quietEnd) {
            // Quiet hours span midnight (e.g., 22:00 to 07:00)
            if ($hour >= $quietStart || $hour < $quietEnd) {
                $optimalTime->hour($quietEnd)->minute(0)->second(0);
            }
        } else {
            // Quiet hours within same day (e.g., 12:00 to 14:00)
            if ($hour >= $quietStart && $hour < $quietEnd) {
                $optimalTime->hour($quietEnd)->minute(0)->second(0);
            }
        }

        return $optimalTime;
    }

    /**
     * Get user notification preferences
     */
    private static function getUserNotificationPreferences(User $user): array
    {
        $cacheKey = "user_notification_preferences_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($user) {
            // This would come from user preferences table
            // For now, return defaults
            return [
                'max_per_day' => [
                    'thread_created' => 5,
                    'thread_replied' => 10,
                    'comment_mention' => 3,
                    'product_out_of_stock' => 2,
                    'order_status_changed' => 10,
                    'review_received' => 5,
                    'wishlist_available' => 3,
                    'seller_message' => 5,
                ],
                'quiet_hours' => [
                    'start' => 22, // 10 PM
                    'end' => 7,    // 7 AM
                ],
                'batch_notifications' => true,
                'digest_enabled' => true,
                'digest_frequency' => 'daily', // daily, weekly
            ];
        });
    }

    /**
     * Get default max notifications per day
     */
    private static function getDefaultMaxPerDay(string $notificationType): int
    {
        $defaults = [
            'thread_created' => 5,
            'thread_replied' => 10,
            'comment_mention' => 3,
            'product_out_of_stock' => 2,
            'order_status_changed' => 10,
            'review_received' => 5,
            'wishlist_available' => 3,
            'seller_message' => 5,
            'login_from_new_device' => 1,
            'password_changed' => 1,
        ];

        return $defaults[$notificationType] ?? 3;
    }

    /**
     * Batch notifications for user
     */
    public static function batchNotifications(User $user): array
    {
        try {
            $preferences = static::getUserNotificationPreferences($user);
            
            if (!$preferences['batch_notifications']) {
                return [];
            }

            // Get pending notifications for batching
            $pendingNotifications = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->where('created_at', '>=', now()->subHours(2)) // Last 2 hours
                ->orderBy('created_at', 'desc')
                ->get();

            if ($pendingNotifications->count() < 2) {
                return [];
            }

            // Group by type
            $groupedNotifications = $pendingNotifications->groupBy('type');
            $batches = [];

            foreach ($groupedNotifications as $type => $notifications) {
                if ($notifications->count() >= 2) {
                    $batches[] = [
                        'type' => $type,
                        'count' => $notifications->count(),
                        'notifications' => $notifications->take(5), // Limit to 5 per batch
                        'batch_title' => static::getBatchTitle($type, $notifications->count()),
                        'batch_message' => static::getBatchMessage($type, $notifications->count()),
                    ];
                }
            }

            Log::debug("Created notification batches", [
                'user_id' => $user->id,
                'batch_count' => count($batches),
                'total_notifications' => $pendingNotifications->count(),
            ]);

            return $batches;

        } catch (\Exception $e) {
            Log::error("Failed to batch notifications", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Get batch title
     */
    private static function getBatchTitle(string $type, int $count): string
    {
        $titles = [
            'thread_created' => "Có {$count} chủ đề mới",
            'thread_replied' => "Có {$count} phản hồi mới",
            'comment_mention' => "Bạn được nhắc đến {$count} lần",
            'product_out_of_stock' => "{$count} sản phẩm hết hàng",
            'order_status_changed' => "{$count} đơn hàng có cập nhật",
            'review_received' => "Nhận được {$count} đánh giá mới",
            'wishlist_available' => "{$count} sản phẩm yêu thích có sẵn",
            'seller_message' => "Có {$count} tin nhắn mới từ người bán",
        ];

        return $titles[$type] ?? "Có {$count} thông báo mới";
    }

    /**
     * Get batch message
     */
    private static function getBatchMessage(string $type, int $count): string
    {
        $messages = [
            'thread_created' => "Có {$count} chủ đề mới trong các diễn đàn bạn theo dõi.",
            'thread_replied' => "Có {$count} phản hồi mới trong các chủ đề bạn quan tâm.",
            'comment_mention' => "Bạn đã được nhắc đến trong {$count} bình luận.",
            'product_out_of_stock' => "{$count} sản phẩm bạn quan tâm đã hết hàng.",
            'order_status_changed' => "{$count} đơn hàng của bạn có cập nhật trạng thái.",
            'review_received' => "Bạn đã nhận được {$count} đánh giá mới.",
            'wishlist_available' => "{$count} sản phẩm trong danh sách yêu thích đã có sẵn.",
            'seller_message' => "Bạn có {$count} tin nhắn mới từ người bán.",
        ];

        return $messages[$type] ?? "Bạn có {$count} thông báo mới.";
    }

    /**
     * Optimize delivery for notification
     */
    public static function optimizeDelivery(Notification $notification): array
    {
        $user = $notification->user;
        $optimizations = [];

        // Calculate optimal delivery time
        $optimalTime = static::getOptimalDeliveryTime($user, $notification->type);
        $delay = $optimalTime->diffInMinutes(now());

        if ($delay > 0) {
            $optimizations['delayed_delivery'] = [
                'enabled' => true,
                'delay_minutes' => $delay,
                'optimal_time' => $optimalTime->toISOString(),
                'reason' => 'User engagement optimization',
            ];
        }

        // Check for batching opportunity
        $batches = static::batchNotifications($user);
        $canBatch = false;

        foreach ($batches as $batch) {
            if ($batch['type'] === $notification->type) {
                $canBatch = true;
                break;
            }
        }

        if ($canBatch) {
            $optimizations['batching'] = [
                'enabled' => true,
                'batch_count' => count($batches),
                'reason' => 'Reduce notification fatigue',
            ];
        }

        // Check frequency limits
        $sentToday = Notification::where('user_id', $user->id)
            ->where('type', $notification->type)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $maxPerDay = static::getDefaultMaxPerDay($notification->type);

        if ($sentToday >= $maxPerDay) {
            $optimizations['frequency_limit'] = [
                'enabled' => true,
                'sent_today' => $sentToday,
                'max_per_day' => $maxPerDay,
                'reason' => 'Daily frequency limit reached',
            ];
        }

        return $optimizations;
    }

    /**
     * Get delivery statistics
     */
    public static function getDeliveryStatistics(): array
    {
        $cacheKey = 'delivery_optimization_stats';
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $stats = [
                'total_optimizations' => 0,
                'delayed_deliveries' => 0,
                'batched_notifications' => 0,
                'frequency_limited' => 0,
                'engagement_improvement' => 0,
            ];

            // This would be calculated from actual optimization data
            // For now, return placeholder stats
            $stats['total_optimizations'] = rand(100, 500);
            $stats['delayed_deliveries'] = rand(50, 200);
            $stats['batched_notifications'] = rand(30, 150);
            $stats['frequency_limited'] = rand(10, 50);
            $stats['engagement_improvement'] = rand(15, 35); // Percentage

            return $stats;
        });
    }

    /**
     * Clear user optimization cache
     */
    public static function clearUserOptimizationCache(User $user): void
    {
        $patterns = [
            "user_optimal_hour_{$user->id}_*",
            "user_notification_preferences_{$user->id}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        Log::debug("Cleared user optimization cache", [
            'user_id' => $user->id,
        ]);
    }
}
