<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\RedisClusterManager;
use Carbon\Carbon;

class NotificationAnalyticsService
{
    /**
     * Get comprehensive notification analytics
     */
    public static function getAnalytics(array $filters = []): array
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30);
        $endDate = $filters['end_date'] ?? now();
        $notificationType = $filters['type'] ?? null;
        $userId = $filters['user_id'] ?? null;

        $cacheKey = 'notification_analytics_' . md5(serialize($filters));

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($startDate, $endDate, $notificationType, $userId) {
            return [
                'overview' => static::getOverviewMetrics($startDate, $endDate, $notificationType, $userId),
                'engagement' => static::getEngagementMetrics($startDate, $endDate, $notificationType, $userId),
                'delivery' => static::getDeliveryMetrics($startDate, $endDate, $notificationType, $userId),
                'trends' => static::getTrendData($startDate, $endDate, $notificationType, $userId),
                'top_types' => static::getTopNotificationTypes($startDate, $endDate, $userId),
                'user_segments' => static::getUserSegmentAnalysis($startDate, $endDate, $notificationType),
                'performance' => static::getPerformanceMetrics($startDate, $endDate, $notificationType, $userId),
            ];
        });
    }

    /**
     * Get overview metrics
     */
    private static function getOverviewMetrics($startDate, $endDate, $notificationType, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $totalSent = $query->count();
        $totalRead = (clone $query)->where('is_read', true)->count();
        $totalUnread = $totalSent - $totalRead;
        $readRate = $totalSent > 0 ? round(($totalRead / $totalSent) * 100, 2) : 0;

        // Get unique recipients
        $uniqueRecipients = (clone $query)->distinct('user_id')->count('user_id');

        // Get average time to read
        $avgTimeToRead = static::getAverageTimeToRead($startDate, $endDate, $notificationType, $userId);

        return [
            'total_sent' => $totalSent,
            'total_read' => $totalRead,
            'total_unread' => $totalUnread,
            'read_rate' => $readRate,
            'unique_recipients' => $uniqueRecipients,
            'avg_time_to_read' => $avgTimeToRead,
        ];
    }

    /**
     * Get engagement metrics
     */
    private static function getEngagementMetrics($startDate, $endDate, $notificationType, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Click-through rate (notifications with action_url that were clicked)
        $withActionUrl = (clone $query)->whereNotNull('data->action_url')->count();
        $clicked = (clone $query)->where('data->clicked', true)->count();
        $clickThroughRate = $withActionUrl > 0 ? round(($clicked / $withActionUrl) * 100, 2) : 0;

        // Engagement by priority
        $engagementByPriority = (clone $query)
            ->select('priority', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`'))
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                return [
                    'priority' => $item->priority,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                ];
            })->toArray();

        // Time-based engagement
        $hourlyEngagement = static::getHourlyEngagement($startDate, $endDate, $notificationType, $userId);

        return [
            'click_through_rate' => $clickThroughRate,
            'engagement_by_priority' => $engagementByPriority,
            'hourly_engagement' => $hourlyEngagement,
        ];
    }

    /**
     * Get delivery metrics
     */
    private static function getDeliveryMetrics($startDate, $endDate, $notificationType, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Real-time vs offline delivery
        $realTimeDelivered = (clone $query)->where('data->delivery_method', 'real-time')->count();
        $offlineDelivered = (clone $query)->where('data->delivery_method', 'offline_delivery')->count();
        $emailFallback = (clone $query)->where('data->email_fallback_sent', true)->count();

        // Delivery success rate
        $totalAttempted = (clone $query)->count();
        $deliveryFailures = (clone $query)->where('data->job_failed', true)->count();
        $deliverySuccessRate = $totalAttempted > 0 ? round((($totalAttempted - $deliveryFailures) / $totalAttempted) * 100, 2) : 0;

        // Average delivery time
        $avgDeliveryTime = static::getAverageDeliveryTime($startDate, $endDate, $notificationType, $userId);

        return [
            'real_time_delivered' => $realTimeDelivered,
            'offline_delivered' => $offlineDelivered,
            'email_fallback' => $emailFallback,
            'delivery_success_rate' => $deliverySuccessRate,
            'delivery_failures' => $deliveryFailures,
            'avg_delivery_time' => $avgDeliveryTime,
        ];
    }

    /**
     * Get trend data for charts
     */
    private static function getTrendData($startDate, $endDate, $notificationType, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Daily trends
        $dailyTrends = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                ];
            })->toArray();

        // Weekly trends
        $weeklyTrends = (clone $query)
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`')
            )
            ->groupBy(DB::raw('YEARWEEK(created_at)'))
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                return [
                    'week' => $item->week,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                ];
            })->toArray();

        return [
            'daily' => $dailyTrends,
            'weekly' => $weeklyTrends,
        ];
    }

    /**
     * Get top notification types
     */
    private static function getTopNotificationTypes($startDate, $endDate, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query
            ->select(
                'type',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`'),
                DB::raw('AVG(CASE WHEN is_read = 1 AND read_at IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, created_at, read_at) END) as avg_time_to_read')
            )
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                    'avg_time_to_read' => round($item->avg_time_to_read ?? 0, 2),
                ];
            })->toArray();
    }

    /**
     * Get user segment analysis
     */
    private static function getUserSegmentAnalysis($startDate, $endDate, $notificationType): array
    {
        $query = Notification::whereBetween('notifications.created_at', [$startDate, $endDate])
            ->join('users', 'notifications.user_id', '=', 'users.id');

        if ($notificationType) {
            $query->where('notifications.type', $notificationType);
        }

        // Engagement by user role
        $roleEngagement = (clone $query)
            ->select(
                'users.role',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN notifications.is_read = 1 THEN 1 ELSE 0 END) as `read`')
            )
            ->groupBy('users.role')
            ->get()
            ->map(function ($item) {
                return [
                    'role' => $item->role,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                ];
            })->toArray();

        // Active vs inactive users
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(7))->pluck('id');
        $activeUserEngagement = Notification::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('user_id', $activeUsers)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`')
            ->first();

        $inactiveUserEngagement = Notification::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('user_id', $activeUsers)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`')
            ->first();

        return [
            'by_role' => $roleEngagement,
            'active_users' => [
                'total' => $activeUserEngagement->total ?? 0,
                'read' => $activeUserEngagement->read ?? 0,
                'read_rate' => ($activeUserEngagement->total ?? 0) > 0 ?
                    round((($activeUserEngagement->read ?? 0) / $activeUserEngagement->total) * 100, 2) : 0,
            ],
            'inactive_users' => [
                'total' => $inactiveUserEngagement->total ?? 0,
                'read' => $inactiveUserEngagement->read ?? 0,
                'read_rate' => ($inactiveUserEngagement->total ?? 0) > 0 ?
                    round((($inactiveUserEngagement->read ?? 0) / $inactiveUserEngagement->total) * 100, 2) : 0,
            ],
        ];
    }

    /**
     * Get performance metrics
     */
    private static function getPerformanceMetrics($startDate, $endDate, $notificationType, $userId): array
    {
        // Database performance
        $avgQueryTime = static::getAverageQueryTime();

        // Cache hit rate
        $cacheStats = static::getCacheStatistics();

        // Queue performance
        $queueStats = static::getQueueStatistics();

        return [
            'avg_query_time' => $avgQueryTime,
            'cache_hit_rate' => $cacheStats['hit_rate'] ?? 0,
            'queue_processing_time' => $queueStats['avg_processing_time'] ?? 0,
            'failed_jobs' => $queueStats['failed_jobs'] ?? 0,
        ];
    }

    /**
     * Get average time to read notifications
     */
    private static function getAverageTimeToRead($startDate, $endDate, $notificationType, $userId): float
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_read', true)
            ->whereNotNull('read_at');

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $avgMinutes = $query->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, read_at)) as avg_time')->value('avg_time');

        return round($avgMinutes ?? 0, 2);
    }

    /**
     * Get hourly engagement patterns
     */
    private static function getHourlyEngagement($startDate, $endDate, $notificationType, $userId): array
    {
        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        if ($notificationType) {
            $query->where('type', $notificationType);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`')
            )
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => $item->hour,
                    'total' => $item->total,
                    'read' => $item->read,
                    'read_rate' => $item->total > 0 ? round(($item->read / $item->total) * 100, 2) : 0,
                ];
            })->toArray();
    }

    /**
     * Get average delivery time
     */
    private static function getAverageDeliveryTime($startDate, $endDate, $notificationType, $userId): float
    {
        // This would require tracking delivery timestamps in notification data
        // For now, return a placeholder
        return 0.5; // 0.5 seconds average
    }

    /**
     * Get average database query time
     */
    private static function getAverageQueryTime(): float
    {
        // This would require query performance monitoring
        // For now, return a placeholder
        return 15.5; // 15.5ms average
    }

    /**
     * Get cache statistics
     */
    private static function getCacheStatistics(): array
    {
        // This would require cache monitoring
        // For now, return placeholder data
        return [
            'hit_rate' => 85.2,
            'miss_rate' => 14.8,
            'total_requests' => 10000,
        ];
    }

    /**
     * Get queue statistics
     */
    private static function getQueueStatistics(): array
    {
        // This would require queue monitoring
        // For now, return placeholder data
        return [
            'avg_processing_time' => 2.3, // seconds
            'failed_jobs' => 5,
            'pending_jobs' => 12,
        ];
    }

    /**
     * Export analytics data
     */
    public static function exportAnalytics(array $filters = [], string $format = 'csv'): string
    {
        $analytics = static::getAnalytics($filters);

        if ($format === 'csv') {
            return static::exportToCsv($analytics);
        } elseif ($format === 'json') {
            return json_encode($analytics, JSON_PRETTY_PRINT);
        }

        throw new \InvalidArgumentException('Unsupported export format');
    }

    /**
     * Export to CSV format
     */
    private static function exportToCsv(array $analytics): string
    {
        $csv = "Metric,Value\n";

        // Overview metrics
        foreach ($analytics['overview'] as $key => $value) {
            $csv .= ucfirst(str_replace('_', ' ', $key)) . "," . $value . "\n";
        }

        return $csv;
    }
}
