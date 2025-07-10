<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationEngagementService
{
    /**
     * Track notification view/read event
     */
    public static function trackView(Notification $notification, User $user, array $context = []): void
    {
        try {
            // Update notification read status if not already read
            if (!$notification->is_read) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }

            // Track engagement event
            static::recordEngagementEvent($notification, $user, 'view', $context);

            // Update user engagement metrics
            static::updateUserEngagementMetrics($user, 'view');

            // Update notification type engagement
            static::updateNotificationTypeEngagement($notification->type, 'view');

        } catch (\Exception $e) {
            Log::error('Failed to track notification view', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track notification click event
     */
    public static function trackClick(Notification $notification, User $user, array $context = []): void
    {
        try {
            // Update notification data with click info
            $data = $notification->data ?? [];
            $data['clicked'] = true;
            $data['clicked_at'] = now()->toISOString();
            $data['click_context'] = $context;
            
            $notification->update(['data' => $data]);

            // Track engagement event
            static::recordEngagementEvent($notification, $user, 'click', $context);

            // Update user engagement metrics
            static::updateUserEngagementMetrics($user, 'click');

            // Update notification type engagement
            static::updateNotificationTypeEngagement($notification->type, 'click');

        } catch (\Exception $e) {
            Log::error('Failed to track notification click', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track notification dismiss event
     */
    public static function trackDismiss(Notification $notification, User $user, array $context = []): void
    {
        try {
            // Update notification data with dismiss info
            $data = $notification->data ?? [];
            $data['dismissed'] = true;
            $data['dismissed_at'] = now()->toISOString();
            $data['dismiss_context'] = $context;
            
            $notification->update(['data' => $data]);

            // Track engagement event
            static::recordEngagementEvent($notification, $user, 'dismiss', $context);

            // Update user engagement metrics
            static::updateUserEngagementMetrics($user, 'dismiss');

            // Update notification type engagement
            static::updateNotificationTypeEngagement($notification->type, 'dismiss');

        } catch (\Exception $e) {
            Log::error('Failed to track notification dismiss', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track notification action event (e.g., reply, like, share)
     */
    public static function trackAction(Notification $notification, User $user, string $action, array $context = []): void
    {
        try {
            // Update notification data with action info
            $data = $notification->data ?? [];
            $data['actions'] = $data['actions'] ?? [];
            $data['actions'][] = [
                'action' => $action,
                'performed_at' => now()->toISOString(),
                'context' => $context,
            ];
            
            $notification->update(['data' => $data]);

            // Track engagement event
            static::recordEngagementEvent($notification, $user, 'action', array_merge($context, ['action' => $action]));

            // Update user engagement metrics
            static::updateUserEngagementMetrics($user, 'action');

            // Update notification type engagement
            static::updateNotificationTypeEngagement($notification->type, 'action');

        } catch (\Exception $e) {
            Log::error('Failed to track notification action', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Record engagement event in cache for analytics
     */
    private static function recordEngagementEvent(Notification $notification, User $user, string $eventType, array $context): void
    {
        $event = [
            'notification_id' => $notification->id,
            'user_id' => $user->id,
            'notification_type' => $notification->type,
            'event_type' => $eventType,
            'timestamp' => now()->toISOString(),
            'context' => $context,
            'user_role' => $user->role,
            'notification_priority' => $notification->priority,
        ];

        // Store in daily engagement cache
        $cacheKey = 'engagement_events_' . now()->format('Y-m-d');
        $events = Cache::get($cacheKey, []);
        $events[] = $event;
        
        // Keep only last 1000 events per day
        if (count($events) > 1000) {
            $events = array_slice($events, -1000);
        }
        
        Cache::put($cacheKey, $events, now()->addDays(7));

        // Store in hourly engagement cache for real-time analytics
        $hourlyCacheKey = 'engagement_events_hourly_' . now()->format('Y-m-d-H');
        $hourlyEvents = Cache::get($hourlyCacheKey, []);
        $hourlyEvents[] = $event;
        Cache::put($hourlyCacheKey, $hourlyEvents, now()->addHours(2));
    }

    /**
     * Update user engagement metrics
     */
    private static function updateUserEngagementMetrics(User $user, string $eventType): void
    {
        $cacheKey = "user_engagement_{$user->id}";
        $metrics = Cache::get($cacheKey, [
            'total_views' => 0,
            'total_clicks' => 0,
            'total_dismisses' => 0,
            'total_actions' => 0,
            'last_engagement' => null,
            'engagement_score' => 0,
        ]);

        // Update metrics
        $metrics["total_{$eventType}s"]++;
        $metrics['last_engagement'] = now()->toISOString();
        
        // Calculate engagement score
        $metrics['engagement_score'] = static::calculateEngagementScore($metrics);

        Cache::put($cacheKey, $metrics, now()->addDays(30));
    }

    /**
     * Update notification type engagement metrics
     */
    private static function updateNotificationTypeEngagement(string $notificationType, string $eventType): void
    {
        $cacheKey = "notification_type_engagement_{$notificationType}";
        $metrics = Cache::get($cacheKey, [
            'total_sent' => 0,
            'total_views' => 0,
            'total_clicks' => 0,
            'total_dismisses' => 0,
            'total_actions' => 0,
            'engagement_rate' => 0,
        ]);

        // Update metrics
        $metrics["total_{$eventType}s"]++;
        
        // Calculate engagement rate
        if ($metrics['total_sent'] > 0) {
            $totalEngagements = $metrics['total_views'] + $metrics['total_clicks'] + $metrics['total_actions'];
            $metrics['engagement_rate'] = round(($totalEngagements / $metrics['total_sent']) * 100, 2);
        }

        Cache::put($cacheKey, $metrics, now()->addDays(7));
    }

    /**
     * Calculate user engagement score
     */
    private static function calculateEngagementScore(array $metrics): float
    {
        $score = 0;
        
        // Weight different engagement types
        $score += $metrics['total_views'] * 1;      // 1 point per view
        $score += $metrics['total_clicks'] * 3;     // 3 points per click
        $score += $metrics['total_actions'] * 5;    // 5 points per action
        $score -= $metrics['total_dismisses'] * 1;  // -1 point per dismiss

        // Normalize to 0-100 scale
        $maxScore = 100;
        $normalizedScore = min($maxScore, max(0, $score));

        return round($normalizedScore, 2);
    }

    /**
     * Get user engagement metrics
     */
    public static function getUserEngagementMetrics(User $user): array
    {
        $cacheKey = "user_engagement_{$user->id}";
        $metrics = Cache::get($cacheKey, [
            'total_views' => 0,
            'total_clicks' => 0,
            'total_dismisses' => 0,
            'total_actions' => 0,
            'last_engagement' => null,
            'engagement_score' => 0,
        ]);

        // Add calculated metrics
        $totalEngagements = $metrics['total_views'] + $metrics['total_clicks'] + $metrics['total_actions'];
        $metrics['total_engagements'] = $totalEngagements;
        
        if ($metrics['total_views'] > 0) {
            $metrics['click_through_rate'] = round(($metrics['total_clicks'] / $metrics['total_views']) * 100, 2);
            $metrics['action_rate'] = round(($metrics['total_actions'] / $metrics['total_views']) * 100, 2);
        } else {
            $metrics['click_through_rate'] = 0;
            $metrics['action_rate'] = 0;
        }

        return $metrics;
    }

    /**
     * Get notification type engagement metrics
     */
    public static function getNotificationTypeEngagement(string $notificationType): array
    {
        $cacheKey = "notification_type_engagement_{$notificationType}";
        return Cache::get($cacheKey, [
            'total_sent' => 0,
            'total_views' => 0,
            'total_clicks' => 0,
            'total_dismisses' => 0,
            'total_actions' => 0,
            'engagement_rate' => 0,
        ]);
    }

    /**
     * Get engagement events for a specific date
     */
    public static function getEngagementEvents(Carbon $date): array
    {
        $cacheKey = 'engagement_events_' . $date->format('Y-m-d');
        return Cache::get($cacheKey, []);
    }

    /**
     * Get hourly engagement events
     */
    public static function getHourlyEngagementEvents(Carbon $datetime): array
    {
        $cacheKey = 'engagement_events_hourly_' . $datetime->format('Y-m-d-H');
        return Cache::get($cacheKey, []);
    }

    /**
     * Get engagement summary for analytics
     */
    public static function getEngagementSummary(Carbon $startDate, Carbon $endDate): array
    {
        $summary = [
            'total_events' => 0,
            'events_by_type' => [
                'view' => 0,
                'click' => 0,
                'dismiss' => 0,
                'action' => 0,
            ],
            'events_by_notification_type' => [],
            'events_by_user_role' => [],
            'hourly_distribution' => [],
        ];

        // Collect events from date range
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $events = static::getEngagementEvents($currentDate);
            
            foreach ($events as $event) {
                $summary['total_events']++;
                $summary['events_by_type'][$event['event_type']]++;
                
                $notificationType = $event['notification_type'];
                $summary['events_by_notification_type'][$notificationType] = 
                    ($summary['events_by_notification_type'][$notificationType] ?? 0) + 1;
                
                $userRole = $event['user_role'];
                $summary['events_by_user_role'][$userRole] = 
                    ($summary['events_by_user_role'][$userRole] ?? 0) + 1;
                
                $hour = Carbon::parse($event['timestamp'])->format('H');
                $summary['hourly_distribution'][$hour] = 
                    ($summary['hourly_distribution'][$hour] ?? 0) + 1;
            }
            
            $currentDate->addDay();
        }

        return $summary;
    }

    /**
     * Get top performing notification types
     */
    public static function getTopPerformingNotificationTypes(int $limit = 10): array
    {
        $notificationTypes = [
            'thread_created', 'thread_replied', 'comment_mention',
            'product_out_of_stock', 'order_status_changed', 'review_received',
            'wishlist_available', 'seller_message', 'login_from_new_device',
            'password_changed'
        ];

        $performance = [];
        
        foreach ($notificationTypes as $type) {
            $metrics = static::getNotificationTypeEngagement($type);
            if ($metrics['total_sent'] > 0) {
                $performance[] = array_merge(['type' => $type], $metrics);
            }
        }

        // Sort by engagement rate
        usort($performance, function ($a, $b) {
            return $b['engagement_rate'] <=> $a['engagement_rate'];
        });

        return array_slice($performance, 0, $limit);
    }

    /**
     * Get user engagement leaderboard
     */
    public static function getUserEngagementLeaderboard(int $limit = 20): array
    {
        // This would require scanning all user engagement caches
        // For now, return a placeholder implementation
        $users = User::limit($limit)->get();
        $leaderboard = [];

        foreach ($users as $user) {
            $metrics = static::getUserEngagementMetrics($user);
            if ($metrics['engagement_score'] > 0) {
                $leaderboard[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'engagement_score' => $metrics['engagement_score'],
                    'total_engagements' => $metrics['total_engagements'],
                ];
            }
        }

        // Sort by engagement score
        usort($leaderboard, function ($a, $b) {
            return $b['engagement_score'] <=> $a['engagement_score'];
        });

        return array_slice($leaderboard, 0, $limit);
    }

    /**
     * Clean up old engagement data
     */
    public static function cleanupOldEngagementData(): int
    {
        $cleanedUp = 0;
        
        try {
            // Clean up engagement events older than 30 days
            $cutoffDate = now()->subDays(30);
            $currentDate = $cutoffDate->copy();
            
            while ($currentDate->lt(now()->subDays(7))) {
                $cacheKey = 'engagement_events_' . $currentDate->format('Y-m-d');
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                    $cleanedUp++;
                }
                $currentDate->addDay();
            }

            Log::info('Cleaned up old engagement data', [
                'cleaned_up_days' => $cleanedUp,
                'cutoff_date' => $cutoffDate->toDateString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old engagement data', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleanedUp;
    }
}
