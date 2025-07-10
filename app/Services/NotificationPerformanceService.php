<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationPerformanceService
{
    /**
     * Optimize notification queries with proper indexing
     */
    public static function optimizeQueries(): array
    {
        $results = [];

        try {
            // 1. Add missing indexes for notifications table
            $results['indexes'] = self::addOptimizedIndexes();

            // 2. Optimize user notification queries
            $results['user_queries'] = self::optimizeUserQueries();

            // 3. Clean up old data
            $results['cleanup'] = self::cleanupOldData();

            // 4. Update table statistics
            $results['statistics'] = self::updateTableStatistics();

            return [
                'success' => true,
                'message' => 'Database optimization completed',
                'results' => $results,
            ];

        } catch (\Exception $e) {
            Log::error('Database optimization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Database optimization failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add optimized indexes for better performance
     */
    private static function addOptimizedIndexes(): array
    {
        $indexes = [];

        // Notifications table indexes
        $notificationIndexes = [
            'notifications_user_read_created_idx' => ['user_id', 'is_read', 'created_at'],
            'notifications_type_priority_idx' => ['type', 'priority'],
            'notifications_created_priority_idx' => ['created_at', 'priority'],
            'notifications_user_type_idx' => ['user_id', 'type'],
        ];

        foreach ($notificationIndexes as $indexName => $columns) {
            if (!self::indexExists('notifications', $indexName)) {
                DB::statement("CREATE INDEX {$indexName} ON notifications (" . implode(', ', $columns) . ")");
                $indexes[] = "Created index: {$indexName}";
            }
        }

        // Users table indexes for targeting
        $userIndexes = [
            'users_role_active_idx' => ['role', 'is_active'],
            'users_locale_notifications_idx' => ['locale', 'email_notifications_enabled'],
            'users_last_login_role_idx' => ['last_login_at', 'role'],
        ];

        foreach ($userIndexes as $indexName => $columns) {
            if (!self::indexExists('users', $indexName)) {
                DB::statement("CREATE INDEX {$indexName} ON users (" . implode(', ', $columns) . ")");
                $indexes[] = "Created index: {$indexName}";
            }
        }

        return $indexes;
    }

    /**
     * Check if index exists
     */
    private static function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return !empty($result);
    }

    /**
     * Optimize user notification queries
     */
    private static function optimizeUserQueries(): array
    {
        $optimizations = [];

        // 1. Optimize unread count query
        $optimizations[] = self::optimizeUnreadCountQuery();

        // 2. Optimize recent notifications query
        $optimizations[] = self::optimizeRecentNotificationsQuery();

        // 3. Optimize notification list query
        $optimizations[] = self::optimizeNotificationListQuery();

        return $optimizations;
    }

    /**
     * Optimize unread count query
     */
    private static function optimizeUnreadCountQuery(): string
    {
        // Create materialized view for unread counts
        $sql = "
            CREATE OR REPLACE VIEW user_unread_counts AS
            SELECT
                user_id,
                COUNT(*) as unread_count,
                MAX(created_at) as latest_notification
            FROM notifications
            WHERE is_read = 0
            GROUP BY user_id
        ";

        try {
            DB::statement($sql);
            return "Created optimized unread count view";
        } catch (\Exception $e) {
            return "Failed to create unread count view: " . $e->getMessage();
        }
    }

    /**
     * Optimize recent notifications query
     */
    private static function optimizeRecentNotificationsQuery(): string
    {
        // Add covering index for recent notifications
        $sql = "
            CREATE INDEX IF NOT EXISTS notifications_recent_covering_idx
            ON notifications (user_id, created_at DESC, id, type, title, message, is_read)
        ";

        try {
            DB::statement($sql);
            return "Created covering index for recent notifications";
        } catch (\Exception $e) {
            return "Failed to create covering index: " . $e->getMessage();
        }
    }

    /**
     * Optimize notification list query
     */
    private static function optimizeNotificationListQuery(): string
    {
        // Create partial index for active notifications
        $sql = "
            CREATE INDEX IF NOT EXISTS notifications_active_idx
            ON notifications (user_id, created_at DESC)
            WHERE deleted_at IS NULL
        ";

        try {
            DB::statement($sql);
            return "Created partial index for active notifications";
        } catch (\Exception $e) {
            return "Failed to create partial index: " . $e->getMessage();
        }
    }

    /**
     * Clean up old data for better performance
     */
    private static function cleanupOldData(): array
    {
        $cleanup = [];

        // 1. Count old read notifications (older than 6 months) for reporting
        $archiveDate = now()->subMonths(6);
        $oldReadCount = DB::table('notifications')
            ->where('is_read', true)
            ->where('created_at', '<', $archiveDate)
            ->count();

        $cleanup[] = "Found {$oldReadCount} old read notifications (older than 6 months)";

        // 2. Delete very old notifications (older than 2 years)
        $deleteDate = now()->subYears(2);
        $deletedCount = DB::table('notifications')
            ->where('created_at', '<', $deleteDate)
            ->delete();

        $cleanup[] = "Deleted {$deletedCount} very old notifications";

        // 3. Clean up orphaned notification preferences (if table exists)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('notification_preferences')) {
                $orphanedPrefs = DB::table('notification_preferences')
                    ->leftJoin('users', 'notification_preferences.user_id', '=', 'users.id')
                    ->whereNull('users.id')
                    ->delete();

                $cleanup[] = "Cleaned up {$orphanedPrefs} orphaned preferences";
            } else {
                $cleanup[] = "Notification preferences table not found - skipping cleanup";
            }
        } catch (\Exception $e) {
            $cleanup[] = "Failed to cleanup preferences: " . $e->getMessage();
        }

        return $cleanup;
    }

    /**
     * Update table statistics for query optimizer
     */
    private static function updateTableStatistics(): array
    {
        $statistics = [];

        $tables = ['notifications', 'users', 'notification_preferences'];

        foreach ($tables as $table) {
            try {
                DB::statement("ANALYZE TABLE {$table}");
                $statistics[] = "Updated statistics for {$table}";
            } catch (\Exception $e) {
                $statistics[] = "Failed to update statistics for {$table}: " . $e->getMessage();
            }
        }

        return $statistics;
    }

    /**
     * Get query performance metrics
     */
    public static function getQueryMetrics(): array
    {
        $metrics = [];

        // 1. Slow query analysis
        $metrics['slow_queries'] = self::analyzeSlowQueries();

        // 2. Index usage analysis
        $metrics['index_usage'] = self::analyzeIndexUsage();

        // 3. Table size analysis
        $metrics['table_sizes'] = self::analyzeTableSizes();

        // 4. Query cache hit ratio
        $metrics['cache_ratio'] = self::getCacheHitRatio();

        return $metrics;
    }

    /**
     * Analyze slow queries
     */
    private static function analyzeSlowQueries(): array
    {
        try {
            $slowQueries = DB::select("
                SELECT
                    sql_text,
                    exec_count,
                    avg_timer_wait/1000000000 as avg_time_seconds,
                    sum_timer_wait/1000000000 as total_time_seconds
                FROM performance_schema.events_statements_summary_by_digest
                WHERE schema_name = DATABASE()
                AND avg_timer_wait > 1000000000
                ORDER BY avg_timer_wait DESC
                LIMIT 10
            ");

            return array_map(function($query) {
                return [
                    'query' => substr($query->sql_text, 0, 100) . '...',
                    'executions' => $query->exec_count,
                    'avg_time' => round($query->avg_time_seconds, 3),
                    'total_time' => round($query->total_time_seconds, 3),
                ];
            }, $slowQueries);

        } catch (\Exception $e) {
            return ['error' => 'Could not analyze slow queries: ' . $e->getMessage()];
        }
    }

    /**
     * Analyze index usage
     */
    private static function analyzeIndexUsage(): array
    {
        try {
            $indexUsage = DB::select("
                SELECT
                    table_name,
                    index_name,
                    cardinality,
                    CASE
                        WHEN cardinality = 0 THEN 'Unused'
                        WHEN cardinality < 10 THEN 'Low usage'
                        ELSE 'Good usage'
                    END as usage_status
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                AND table_name IN ('notifications', 'users', 'notification_preferences')
                ORDER BY table_name, cardinality DESC
            ");

            return array_map(function($index) {
                return [
                    'table' => $index->table_name,
                    'index' => $index->index_name,
                    'cardinality' => $index->cardinality,
                    'status' => $index->usage_status,
                ];
            }, $indexUsage);

        } catch (\Exception $e) {
            return ['error' => 'Could not analyze index usage: ' . $e->getMessage()];
        }
    }

    /**
     * Analyze table sizes
     */
    private static function analyzeTableSizes(): array
    {
        try {
            $tableSizes = DB::select("
                SELECT
                    table_name,
                    table_rows,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
                    ROUND((data_length / 1024 / 1024), 2) as data_mb,
                    ROUND((index_length / 1024 / 1024), 2) as index_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
                AND table_name IN ('notifications', 'users', 'notification_preferences')
                ORDER BY (data_length + index_length) DESC
            ");

            return array_map(function($table) {
                return [
                    'table' => $table->table_name,
                    'rows' => number_format($table->table_rows),
                    'total_size_mb' => $table->size_mb,
                    'data_size_mb' => $table->data_mb,
                    'index_size_mb' => $table->index_mb,
                ];
            }, $tableSizes);

        } catch (\Exception $e) {
            return ['error' => 'Could not analyze table sizes: ' . $e->getMessage()];
        }
    }

    /**
     * Get cache hit ratio
     */
    private static function getCacheHitRatio(): array
    {
        try {
            $cacheStats = DB::select("
                SHOW STATUS LIKE 'Qcache%'
            ");

            $stats = [];
            foreach ($cacheStats as $stat) {
                $stats[$stat->Variable_name] = $stat->Value;
            }

            $hitRatio = 0;
            if (isset($stats['Qcache_hits']) && isset($stats['Qcache_inserts'])) {
                $total = $stats['Qcache_hits'] + $stats['Qcache_inserts'];
                if ($total > 0) {
                    $hitRatio = ($stats['Qcache_hits'] / $total) * 100;
                }
            }

            return [
                'hit_ratio' => round($hitRatio, 2) . '%',
                'hits' => $stats['Qcache_hits'] ?? 0,
                'inserts' => $stats['Qcache_inserts'] ?? 0,
                'free_memory' => $stats['Qcache_free_memory'] ?? 0,
            ];

        } catch (\Exception $e) {
            return ['error' => 'Could not get cache statistics: ' . $e->getMessage()];
        }
    }

    /**
     * Optimize notification delivery performance
     */
    public static function optimizeDelivery(): array
    {
        $optimizations = [];

        // 1. Batch notification processing
        $optimizations[] = self::optimizeBatchProcessing();

        // 2. Queue optimization
        $optimizations[] = self::optimizeQueueProcessing();

        // 3. Memory optimization
        $optimizations[] = self::optimizeMemoryUsage();

        return $optimizations;
    }

    /**
     * Optimize batch processing
     */
    private static function optimizeBatchProcessing(): string
    {
        // Configure optimal batch sizes
        config(['notifications.batch_size' => 1000]);
        config(['notifications.chunk_size' => 100]);

        return "Configured optimal batch processing sizes";
    }

    /**
     * Optimize queue processing
     */
    private static function optimizeQueueProcessing(): string
    {
        // Configure queue workers for optimal performance
        config(['queue.connections.database.retry_after' => 90]);
        config(['queue.failed.database' => 'mariadb']);

        return "Optimized queue processing configuration";
    }

    /**
     * Optimize memory usage
     */
    private static function optimizeMemoryUsage(): string
    {
        // Clear unnecessary caches
        Cache::tags(['notifications', 'users'])->flush();

        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        return "Optimized memory usage and cleared caches";
    }
}
