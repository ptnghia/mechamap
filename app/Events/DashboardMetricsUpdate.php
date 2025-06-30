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
 * Dashboard Metrics Update Event
 * Broadcasts real-time dashboard metrics updates
 */
class DashboardMetricsUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $metrics;
    public $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(array $metrics)
    {
        $this->metrics = $metrics;
        $this->prepareBroadcastData();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Admin dashboard channel
            new PrivateChannel('admin.dashboard'),
            
            // Public metrics channel (for public dashboard widgets)
            new Channel('dashboard.public'),
            
            // Role-specific channels
            new PrivateChannel('dashboard.moderator'),
            new PrivateChannel('dashboard.supplier'),
            new PrivateChannel('dashboard.manufacturer'),
        ];
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'dashboard.metrics.updated';
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
        // Only broadcast if metrics have significant changes
        return $this->hasSignificantChanges();
    }

    /**
     * Prepare broadcast data
     */
    private function prepareBroadcastData(): void
    {
        $this->broadcastData = [
            'timestamp' => now()->toISOString(),
            'update_type' => 'metrics_update',
            'metrics' => $this->processMetrics(),
            'metadata' => $this->getMetadata(),
        ];
    }

    /**
     * Process and format metrics for broadcasting
     */
    private function processMetrics(): array
    {
        $processed = [];

        foreach ($this->metrics as $category => $data) {
            $processed[$category] = $this->processMetricCategory($category, $data);
        }

        return $processed;
    }

    /**
     * Process individual metric category
     */
    private function processMetricCategory(string $category, array $data): array
    {
        $processed = [
            'category' => $category,
            'updated_at' => now()->toISOString(),
            'data' => [],
        ];

        switch ($category) {
            case 'users':
                $processed['data'] = $this->processUserMetrics($data);
                break;

            case 'marketplace':
                $processed['data'] = $this->processMarketplaceMetrics($data);
                break;

            case 'forum':
                $processed['data'] = $this->processForumMetrics($data);
                break;

            case 'system':
                $processed['data'] = $this->processSystemMetrics($data);
                break;

            case 'analytics':
                $processed['data'] = $this->processAnalyticsMetrics($data);
                break;

            default:
                $processed['data'] = $data;
        }

        return $processed;
    }

    /**
     * Process user metrics
     */
    private function processUserMetrics(array $data): array
    {
        return [
            'total_users' => $data['total_users'] ?? 0,
            'online_users' => $data['online_users'] ?? 0,
            'new_users_today' => $data['new_users_today'] ?? 0,
            'active_users_week' => $data['active_users_week'] ?? 0,
            'user_growth_rate' => $data['user_growth_rate'] ?? 0,
            'user_retention_rate' => $data['user_retention_rate'] ?? 0,
            'users_by_role' => $data['users_by_role'] ?? [],
            'top_active_users' => $data['top_active_users'] ?? [],
            'user_activity_trend' => $data['user_activity_trend'] ?? [],
        ];
    }

    /**
     * Process marketplace metrics
     */
    private function processMarketplaceMetrics(array $data): array
    {
        return [
            'total_products' => $data['total_products'] ?? 0,
            'total_orders' => $data['total_orders'] ?? 0,
            'total_revenue' => $data['total_revenue'] ?? 0,
            'orders_today' => $data['orders_today'] ?? 0,
            'revenue_today' => $data['revenue_today'] ?? 0,
            'pending_orders' => $data['pending_orders'] ?? 0,
            'conversion_rate' => $data['conversion_rate'] ?? 0,
            'average_order_value' => $data['average_order_value'] ?? 0,
            'top_selling_products' => $data['top_selling_products'] ?? [],
            'revenue_trend' => $data['revenue_trend'] ?? [],
            'order_status_distribution' => $data['order_status_distribution'] ?? [],
        ];
    }

    /**
     * Process forum metrics
     */
    private function processForumMetrics(array $data): array
    {
        return [
            'total_threads' => $data['total_threads'] ?? 0,
            'total_posts' => $data['total_posts'] ?? 0,
            'threads_today' => $data['threads_today'] ?? 0,
            'posts_today' => $data['posts_today'] ?? 0,
            'active_discussions' => $data['active_discussions'] ?? 0,
            'unanswered_threads' => $data['unanswered_threads'] ?? 0,
            'most_active_categories' => $data['most_active_categories'] ?? [],
            'trending_topics' => $data['trending_topics'] ?? [],
            'engagement_rate' => $data['engagement_rate'] ?? 0,
            'activity_trend' => $data['activity_trend'] ?? [],
        ];
    }

    /**
     * Process system metrics
     */
    private function processSystemMetrics(array $data): array
    {
        return [
            'server_uptime' => $data['server_uptime'] ?? 0,
            'memory_usage' => $data['memory_usage'] ?? [],
            'cpu_usage' => $data['cpu_usage'] ?? 0,
            'disk_usage' => $data['disk_usage'] ?? [],
            'database_connections' => $data['database_connections'] ?? 0,
            'cache_hit_rate' => $data['cache_hit_rate'] ?? 0,
            'queue_size' => $data['queue_size'] ?? 0,
            'failed_jobs' => $data['failed_jobs'] ?? 0,
            'response_time' => $data['response_time'] ?? 0,
            'error_rate' => $data['error_rate'] ?? 0,
            'websocket_connections' => $data['websocket_connections'] ?? 0,
        ];
    }

    /**
     * Process analytics metrics
     */
    private function processAnalyticsMetrics(array $data): array
    {
        return [
            'page_views_today' => $data['page_views_today'] ?? 0,
            'unique_visitors_today' => $data['unique_visitors_today'] ?? 0,
            'bounce_rate' => $data['bounce_rate'] ?? 0,
            'session_duration' => $data['session_duration'] ?? 0,
            'top_pages' => $data['top_pages'] ?? [],
            'traffic_sources' => $data['traffic_sources'] ?? [],
            'device_breakdown' => $data['device_breakdown'] ?? [],
            'browser_breakdown' => $data['browser_breakdown'] ?? [],
            'geographic_distribution' => $data['geographic_distribution'] ?? [],
            'search_queries' => $data['search_queries'] ?? [],
        ];
    }

    /**
     * Check if metrics have significant changes
     */
    private function hasSignificantChanges(): bool
    {
        $previousMetrics = cache()->get('dashboard_metrics_previous', []);
        
        if (empty($previousMetrics)) {
            return true; // First time, always broadcast
        }

        // Check for significant changes in key metrics
        $significantThreshold = 0.05; // 5% change threshold

        $keyMetrics = [
            'users.online_users',
            'marketplace.orders_today',
            'marketplace.revenue_today',
            'forum.threads_today',
            'system.error_rate',
            'system.response_time',
        ];

        foreach ($keyMetrics as $metric) {
            $current = data_get($this->metrics, $metric, 0);
            $previous = data_get($previousMetrics, $metric, 0);

            if ($previous > 0) {
                $changePercent = abs($current - $previous) / $previous;
                if ($changePercent >= $significantThreshold) {
                    return true;
                }
            } elseif ($current > 0) {
                return true; // New metric with value
            }
        }

        return false;
    }

    /**
     * Get metadata for the metrics update
     */
    private function getMetadata(): array
    {
        return [
            'update_source' => 'real_time_metrics',
            'metrics_count' => count($this->metrics),
            'categories' => array_keys($this->metrics),
            'update_interval' => config('dashboard.metrics_update_interval', 30), // seconds
            'cache_duration' => config('dashboard.metrics_cache_duration', 300), // seconds
            'has_significant_changes' => $this->hasSignificantChanges(),
            'server_time' => now()->toISOString(),
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Get metrics summary for quick overview
     */
    public function getMetricsSummary(): array
    {
        return [
            'total_users' => data_get($this->metrics, 'users.total_users', 0),
            'online_users' => data_get($this->metrics, 'users.online_users', 0),
            'orders_today' => data_get($this->metrics, 'marketplace.orders_today', 0),
            'revenue_today' => data_get($this->metrics, 'marketplace.revenue_today', 0),
            'threads_today' => data_get($this->metrics, 'forum.threads_today', 0),
            'system_health' => $this->getSystemHealthScore(),
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Calculate system health score
     */
    private function getSystemHealthScore(): int
    {
        $score = 100;

        // Deduct points for system issues
        $errorRate = data_get($this->metrics, 'system.error_rate', 0);
        $score -= min($errorRate * 10, 30); // Max 30 points deduction

        $responseTime = data_get($this->metrics, 'system.response_time', 0);
        if ($responseTime > 1000) { // > 1 second
            $score -= min(($responseTime - 1000) / 100, 20); // Max 20 points deduction
        }

        $memoryUsage = data_get($this->metrics, 'system.memory_usage.percentage', 0);
        if ($memoryUsage > 80) {
            $score -= ($memoryUsage - 80) * 2; // 2 points per % over 80%
        }

        return max(0, min(100, round($score)));
    }

    /**
     * Get performance indicators
     */
    public function getPerformanceIndicators(): array
    {
        return [
            'response_time' => data_get($this->metrics, 'system.response_time', 0),
            'error_rate' => data_get($this->metrics, 'system.error_rate', 0),
            'cache_hit_rate' => data_get($this->metrics, 'system.cache_hit_rate', 0),
            'queue_size' => data_get($this->metrics, 'system.queue_size', 0),
            'websocket_connections' => data_get($this->metrics, 'system.websocket_connections', 0),
            'health_score' => $this->getSystemHealthScore(),
        ];
    }
}
