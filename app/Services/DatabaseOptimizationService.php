<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class DatabaseOptimizationService
{
    protected $cachePrefix = 'db_opt';
    protected $defaultCacheTtl = 3600; // 1 hour
    protected $slowQueryThreshold = 1000; // 1 second in milliseconds

    /**
     * Optimize database queries with caching
     */
    public function optimizeQuery(string $cacheKey, callable $query, int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->defaultCacheTtl;
        $fullCacheKey = $this->getCacheKey($cacheKey);

        return Cache::remember($fullCacheKey, $ttl, function () use ($query, $cacheKey) {
            $startTime = microtime(true);

            try {
                $result = $query();

                $executionTime = (microtime(true) - $startTime) * 1000;
                $this->logQueryPerformance($cacheKey, $executionTime);

                return $result;

            } catch (\Exception $e) {
                Log::error('Query optimization error', [
                    'cache_key' => $cacheKey,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Cache model queries with automatic invalidation
     */
    public function cacheModelQuery(Model $model, string $method, array $parameters = [], int $ttl = null): mixed
    {
        $cacheKey = $this->generateModelCacheKey($model, $method, $parameters);
        $ttl = $ttl ?? $this->defaultCacheTtl;

        return Cache::remember($cacheKey, $ttl, function () use ($model, $method, $parameters) {
            return $model->$method(...$parameters);
        });
    }

    /**
     * Invalidate model cache
     */
    public function invalidateModelCache(string $modelClass, string $method = null): void
    {
        $pattern = $this->getCacheKey("model:{$modelClass}");

        if ($method) {
            $pattern .= ":{$method}:*";
        } else {
            $pattern .= ":*";
        }

        $this->invalidateCacheByPattern($pattern);
    }
    /**
     * Create optimized indexes for better performance
     */
    public function createOptimizedIndexes()
    {
        $indexes = [
            // Users table indexes
            'users' => [
                ['email', 'email_verified_at'],
                ['last_login_at'],
                ['created_at', 'status'],
                ['country', 'city'],
            ],

            // Marketplace tables indexes
            'marketplace_products' => [
                ['status', 'is_active'],
                ['seller_id', 'status'],
                ['product_category_id', 'status'],
                ['created_at', 'status'],
                ['price', 'sale_price'],
                ['view_count', 'download_count'],
                ['is_featured', 'status'],
            ],

            'marketplace_orders' => [
                ['customer_id', 'status'],
                ['payment_status', 'created_at'],
                ['status', 'created_at'],
                ['order_number'],
                ['total_amount', 'payment_status'],
            ],

            'marketplace_order_items' => [
                ['order_id', 'seller_id'],
                ['product_id', 'fulfillment_status'],
                ['seller_id', 'fulfillment_status'],
            ],

            'marketplace_sellers' => [
                ['user_id'],
                ['seller_type', 'status'],
                ['verification_status', 'status'],
                ['is_featured', 'status'],
                ['total_revenue', 'status'],
            ],

            // Technical tables indexes
            'technical_drawings' => [
                ['created_by', 'status'],
                ['company_id', 'visibility'],
                ['drawing_type', 'status'],
                ['industry_category', 'application_area'],
                ['is_featured', 'is_active'],
                ['download_count', 'view_count'],
                ['created_at', 'status'],
            ],

            'cad_files' => [
                ['created_by', 'status'],
                ['company_id', 'visibility'],
                ['cad_software', 'model_type'],
                ['processing_status', 'virus_scanned'],
                ['file_extension', 'status'],
            ],

            'materials' => [
                ['category', 'subcategory'],
                ['material_type', 'status'],
                ['is_active', 'is_featured'],
                ['density', 'yield_strength'],
                ['cost_per_kg', 'availability'],
            ],

            // Forum tables indexes
            'threads' => [
                ['forum_id', 'status'],
                ['user_id', 'created_at'],
                ['is_pinned', 'is_locked'],
                ['view_count', 'comment_count'],
                ['created_at', 'updated_at'],
            ],

            'comments' => [
                ['thread_id', 'created_at'],
                ['user_id', 'created_at'],
                ['parent_id', 'created_at'],
            ],

            'forums' => [
                ['category_id', 'is_active'],
                ['slug'],
                ['sort_order', 'is_active'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                foreach ($tableIndexes as $columns) {
                    $this->createIndexIfNotExists($table, $columns);
                }
            }
        }
    }

    /**
     * Optimize database queries with proper indexing
     */
    public function optimizeQueries()
    {
        // Analyze slow queries and create indexes
        $this->analyzeSlowQueries();

        // Update table statistics
        $this->updateTableStatistics();

        // Optimize table structures
        $this->optimizeTableStructures();
    }

    /**
     * Create composite indexes for complex queries
     */
    public function createCompositeIndexes()
    {
        $compositeIndexes = [
            // Analytics optimized indexes
            'marketplace_orders' => [
                'idx_orders_analytics' => ['payment_status', 'created_at', 'total_amount'],
                'idx_orders_customer' => ['customer_id', 'status', 'created_at'],
            ],

            'marketplace_order_items' => [
                'idx_items_seller_analytics' => ['seller_id', 'fulfillment_status', 'created_at'],
                'idx_items_product_analytics' => ['product_id', 'created_at', 'total_amount'],
            ],

            'marketplace_products' => [
                'idx_products_search' => ['status', 'is_active', 'seller_type'],
                'idx_products_featured' => ['is_featured', 'status', 'created_at'],
                'idx_products_category' => ['product_category_id', 'status', 'price'],
            ],

            'technical_drawings' => [
                'idx_drawings_search' => ['status', 'visibility', 'drawing_type'],
                'idx_drawings_analytics' => ['created_at', 'download_count', 'view_count'],
            ],

            'users' => [
                'idx_users_activity' => ['last_login_at', 'created_at', 'status'],
            ],
        ];

        foreach ($compositeIndexes as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $indexName => $columns) {
                    $this->createNamedIndexIfNotExists($table, $indexName, $columns);
                }
            }
        }
    }

    /**
     * Optimize database configuration
     */
    public function optimizeDatabaseConfig()
    {
        // Set optimal MySQL configuration for Laravel
        $optimizations = [
            "SET SESSION query_cache_type = ON",
            "SET SESSION query_cache_size = 268435456", // 256MB
            "SET SESSION innodb_buffer_pool_size = 1073741824", // 1GB
            "SET SESSION max_connections = 200",
            "SET SESSION innodb_log_file_size = 268435456", // 256MB
        ];

        foreach ($optimizations as $query) {
            try {
                DB::statement($query);
            } catch (\Exception $e) {
                // Log error but continue with other optimizations
                \Log::warning("Database optimization failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Clean up and optimize tables
     */
    public function cleanupTables()
    {
        $tables = [
            'marketplace_orders',
            'marketplace_order_items',
            'marketplace_products',
            'technical_drawings',
            'cad_files',
            'materials',
            'threads',
            'comments',
            'users',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("OPTIMIZE TABLE {$table}");
                    DB::statement("ANALYZE TABLE {$table}");
                } catch (\Exception $e) {
                    \Log::warning("Table optimization failed for {$table}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Get database performance metrics
     */
    public function getPerformanceMetrics()
    {
        try {
            $metrics = [
                'slow_queries' => DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value ?? 0,
                'connections' => DB::select("SHOW STATUS LIKE 'Connections'")[0]->Value ?? 0,
                'uptime' => DB::select("SHOW STATUS LIKE 'Uptime'")[0]->Value ?? 0,
                'queries' => DB::select("SHOW STATUS LIKE 'Queries'")[0]->Value ?? 0,
                'innodb_buffer_pool_reads' => DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_reads'")[0]->Value ?? 0,
                'innodb_buffer_pool_read_requests' => DB::select("SHOW STATUS LIKE 'Innodb_buffer_pool_read_requests'")[0]->Value ?? 0,
            ];

            // Calculate buffer pool hit ratio
            if ($metrics['innodb_buffer_pool_read_requests'] > 0) {
                $metrics['buffer_pool_hit_ratio'] = round(
                    (1 - ($metrics['innodb_buffer_pool_reads'] / $metrics['innodb_buffer_pool_read_requests'])) * 100,
                    2
                );
            } else {
                $metrics['buffer_pool_hit_ratio'] = 100;
            }

            return $metrics;
        } catch (\Exception $e) {
            return [
                'error' => 'Could not retrieve performance metrics: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Private helper methods
     */
    private function createIndexIfNotExists($table, $columns)
    {
        $indexName = 'idx_' . $table . '_' . implode('_', $columns);

        try {
            if (!$this->indexExists($table, $indexName)) {
                Schema::table($table, function ($table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to create index {$indexName} on table {$table}: " . $e->getMessage());
        }
    }

    private function createNamedIndexIfNotExists($table, $indexName, $columns)
    {
        try {
            if (!$this->indexExists($table, $indexName)) {
                Schema::table($table, function ($table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to create named index {$indexName} on table {$table}: " . $e->getMessage());
        }
    }

    private function indexExists($table, $indexName)
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function analyzeSlowQueries()
    {
        try {
            // Enable slow query log analysis
            DB::statement("SET SESSION slow_query_log = 'ON'");
            DB::statement("SET SESSION long_query_time = 1"); // Log queries taking more than 1 second
        } catch (\Exception $e) {
            \Log::warning("Could not configure slow query logging: " . $e->getMessage());
        }
    }

    private function updateTableStatistics()
    {
        $tables = Schema::getAllTables();

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            try {
                DB::statement("ANALYZE TABLE {$tableName}");
            } catch (\Exception $e) {
                \Log::warning("Failed to analyze table {$tableName}: " . $e->getMessage());
            }
        }
    }

    private function optimizeTableStructures()
    {
        // Add any table structure optimizations here
        // For example, converting MyISAM tables to InnoDB, optimizing column types, etc.

        try {
            // Ensure all tables are using InnoDB engine
            $tables = Schema::getAllTables();

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                DB::statement("ALTER TABLE {$tableName} ENGINE=InnoDB");
            }
        } catch (\Exception $e) {
            \Log::warning("Table structure optimization failed: " . $e->getMessage());
        }
    }

    // New advanced methods

    private function getCacheKey(string $key): string
    {
        return $this->cachePrefix . ':' . $key;
    }

    private function generateModelCacheKey(Model $model, string $method, array $parameters): string
    {
        $modelClass = get_class($model);
        $paramHash = md5(serialize($parameters));
        return $this->getCacheKey("model:{$modelClass}:{$method}:{$paramHash}");
    }

    private function invalidateCacheByPattern(string $pattern): void
    {
        try {
            // For Redis cache driver
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // Fallback: flush all cache
                Cache::flush();
            }
        } catch (\Exception $e) {
            Log::error('Error invalidating cache: ' . $e->getMessage());
        }
    }

    private function logQueryPerformance(string $cacheKey, float $executionTime): void
    {
        if ($executionTime > $this->slowQueryThreshold) {
            Log::warning('Slow query detected', [
                'cache_key' => $cacheKey,
                'execution_time_ms' => round($executionTime, 2),
                'threshold_ms' => $this->slowQueryThreshold,
            ]);
        }
    }

    /**
     * Create full-text search indexes
     */
    public function createFullTextIndexes(): array
    {
        $indexes = [];

        try {
            // Thread full-text search
            if (Schema::hasTable('threads')) {
                $indexes[] = $this->createFullTextIndex('threads', ['title', 'content'], 'threads_fulltext_index');
            }

            // Product full-text search
            if (Schema::hasTable('marketplace_products')) {
                $indexes[] = $this->createFullTextIndex('marketplace_products', ['name', 'description'], 'products_fulltext_index');
            }

            // User full-text search
            if (Schema::hasTable('users')) {
                $indexes[] = $this->createFullTextIndex('users', ['name', 'username'], 'users_fulltext_index');
            }

            Log::info('Full-text search indexes created', [
                'total_indexes' => count(array_filter($indexes)),
            ]);

            return $indexes;

        } catch (\Exception $e) {
            Log::error('Error creating full-text indexes: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createFullTextIndex(string $table, array $columns, string $indexName): bool
    {
        try {
            if (!Schema::hasTable($table)) {
                return false;
            }

            // Check if index already exists
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (!empty($indexes)) {
                return true;
            }

            $columnList = implode(', ', $columns);
            DB::statement("CREATE FULLTEXT INDEX {$indexName} ON {$table} ({$columnList})");

            return true;

        } catch (\Exception $e) {
            Log::warning("Failed to create full-text index {$indexName} on {$table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Analyze slow queries and provide optimization suggestions
     */
    public function analyzeSlowQueriesAdvanced(int $limit = 50): array
    {
        try {
            // Get slow queries from performance schema (MySQL 5.6+)
            $slowQueries = DB::select("
                SELECT
                    DIGEST_TEXT as query,
                    COUNT_STAR as exec_count,
                    AVG_TIMER_WAIT/1000000000 as avg_time_seconds,
                    MAX_TIMER_WAIT/1000000000 as max_time_seconds,
                    SUM_ROWS_EXAMINED as total_rows_examined,
                    SUM_ROWS_SENT as total_rows_sent
                FROM performance_schema.events_statements_summary_by_digest
                WHERE DIGEST_TEXT IS NOT NULL
                    AND AVG_TIMER_WAIT > 1000000000
                ORDER BY AVG_TIMER_WAIT DESC
                LIMIT ?
            ", [$limit]);

            $analysis = [];
            foreach ($slowQueries as $query) {
                $analysis[] = [
                    'query' => $query->query,
                    'execution_count' => $query->exec_count,
                    'avg_time_seconds' => round($query->avg_time_seconds, 3),
                    'max_time_seconds' => round($query->max_time_seconds, 3),
                    'total_rows_examined' => $query->total_rows_examined,
                    'total_rows_sent' => $query->total_rows_sent,
                    'efficiency_ratio' => $query->total_rows_sent > 0
                        ? round($query->total_rows_sent / $query->total_rows_examined, 3)
                        : 0,
                    'suggestions' => $this->generateOptimizationSuggestions($query),
                ];
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::error('Error analyzing slow queries: ' . $e->getMessage());
            return [];
        }
    }

    private function generateOptimizationSuggestions(object $query): array
    {
        $suggestions = [];

        // High row examination ratio
        if ($query->total_rows_examined > $query->total_rows_sent * 10) {
            $suggestions[] = 'Consider adding indexes to reduce rows examined';
        }

        // Low efficiency ratio
        $efficiency = $query->total_rows_sent > 0
            ? $query->total_rows_sent / $query->total_rows_examined
            : 0;

        if ($efficiency < 0.1) {
            $suggestions[] = 'Query efficiency is low - consider optimizing WHERE clauses';
        }

        // High execution time
        if ($query->avg_time_seconds > 5) {
            $suggestions[] = 'Query takes too long - consider query restructuring or caching';
        }

        return $suggestions;
    }
}
