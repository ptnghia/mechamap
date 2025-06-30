<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Optimization Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for database optimization
    | including caching, indexing, and query optimization settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Query Caching
    |--------------------------------------------------------------------------
    |
    | Enable or disable query caching and configure cache settings.
    |
    */
    'cache' => [
        'enabled' => env('DB_CACHE_ENABLED', true),
        'prefix' => env('DB_CACHE_PREFIX', 'db_opt'),
        'default_ttl' => env('DB_CACHE_TTL', 3600), // 1 hour
        'driver' => env('DB_CACHE_DRIVER', 'redis'),
        
        // Route-specific cache TTL (in seconds)
        'route_ttl' => [
            'threads.index' => 1800,      // 30 minutes
            'threads.show' => 3600,       // 1 hour
            'marketplace.products.index' => 1800,  // 30 minutes
            'marketplace.products.show' => 3600,   // 1 hour
            'categories.index' => 7200,   // 2 hours
            'forums.index' => 7200,       // 2 hours
            'users.index' => 1800,        // 30 minutes
            'search.advanced' => 900,     // 15 minutes
            'admin.dashboard' => 300,     // 5 minutes
        ],

        // Cache invalidation patterns
        'invalidation' => [
            'auto_invalidate' => true,
            'patterns' => [
                'Thread' => ['threads.*', 'forums.*', 'categories.*', 'search.*'],
                'Post' => ['threads.*', 'posts.*'],
                'MarketplaceProduct' => ['marketplace.*', 'categories.*', 'search.*'],
                'MarketplaceOrder' => ['marketplace.*', 'users.*'],
                'User' => ['users.*', 'search.*'],
                'Category' => ['categories.*', 'threads.*', 'marketplace.*'],
                'Forum' => ['forums.*', 'threads.*', 'categories.*'],
                'Notification' => ['users.*'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for query optimization and performance monitoring.
    |
    */
    'optimization' => [
        'enabled' => env('DB_OPTIMIZATION_ENABLED', true),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'log_slow_queries' => env('DB_LOG_SLOW_QUERIES', true),
        'auto_optimize' => env('DB_AUTO_OPTIMIZE', false),
        
        // Query analysis settings
        'analysis' => [
            'enabled' => true,
            'sample_rate' => 0.1, // Analyze 10% of queries
            'max_queries_to_analyze' => 100,
        ],

        // Index optimization
        'indexes' => [
            'auto_create' => env('DB_AUTO_CREATE_INDEXES', false),
            'analyze_missing' => true,
            'performance_indexes' => true,
            'fulltext_indexes' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configuration for database performance monitoring and metrics.
    |
    */
    'monitoring' => [
        'enabled' => env('DB_MONITORING_ENABLED', true),
        'metrics_collection' => true,
        'performance_tracking' => true,
        'health_checks' => true,
        
        // Metrics to collect
        'metrics' => [
            'query_count' => true,
            'slow_queries' => true,
            'connection_count' => true,
            'cache_hit_ratio' => true,
            'buffer_pool_usage' => true,
            'table_statistics' => true,
        ],

        // Alert thresholds
        'alerts' => [
            'slow_query_count' => 100,
            'connection_limit' => 80, // percentage
            'cache_hit_ratio_min' => 95, // percentage
            'buffer_pool_usage_max' => 90, // percentage
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration Recommendations
    |--------------------------------------------------------------------------
    |
    | Recommended database configuration settings for optimal performance.
    |
    */
    'recommendations' => [
        'mysql' => [
            'innodb_buffer_pool_size' => '70%', // of available RAM
            'innodb_log_file_size' => '256M',
            'innodb_flush_log_at_trx_commit' => 2,
            'query_cache_size' => '64M',
            'query_cache_type' => 1,
            'max_connections' => 200,
            'sort_buffer_size' => '2M',
            'read_buffer_size' => '128K',
            'read_rnd_buffer_size' => '256K',
            'myisam_sort_buffer_size' => '8M',
            'thread_cache_size' => 8,
            'table_open_cache' => 2000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup and Maintenance
    |--------------------------------------------------------------------------
    |
    | Settings for database cleanup and maintenance tasks.
    |
    */
    'maintenance' => [
        'enabled' => env('DB_MAINTENANCE_ENABLED', true),
        'auto_cleanup' => env('DB_AUTO_CLEANUP', false),
        
        // Cleanup schedules
        'cleanup_schedules' => [
            'old_logs' => 'daily',
            'expired_cache' => 'hourly',
            'optimize_tables' => 'weekly',
            'analyze_tables' => 'weekly',
        ],

        // Data retention periods (in days)
        'retention' => [
            'search_logs' => 30,
            'user_activities' => 90,
            'performance_logs' => 7,
            'slow_query_logs' => 14,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Pool Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for database connection pooling and management.
    |
    */
    'connection_pool' => [
        'enabled' => env('DB_POOL_ENABLED', false),
        'min_connections' => env('DB_POOL_MIN', 5),
        'max_connections' => env('DB_POOL_MAX', 20),
        'idle_timeout' => env('DB_POOL_IDLE_TIMEOUT', 300), // seconds
        'validation_query' => 'SELECT 1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Read/Write Splitting
    |--------------------------------------------------------------------------
    |
    | Configuration for database read/write splitting for better performance.
    |
    */
    'read_write_split' => [
        'enabled' => env('DB_READ_WRITE_SPLIT', false),
        'read_connections' => [
            // Define read-only database connections
        ],
        'write_connection' => 'mysql', // Default write connection
        'sticky_writes' => true, // Use write connection for reads after writes
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup and Recovery
    |--------------------------------------------------------------------------
    |
    | Settings for database backup and recovery optimization.
    |
    */
    'backup' => [
        'enabled' => env('DB_BACKUP_ENABLED', true),
        'schedule' => env('DB_BACKUP_SCHEDULE', 'daily'),
        'retention_days' => env('DB_BACKUP_RETENTION', 30),
        'compression' => true,
        'verify_backups' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development and Testing
    |--------------------------------------------------------------------------
    |
    | Settings specific to development and testing environments.
    |
    */
    'development' => [
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'explain_queries' => env('DB_EXPLAIN_QUERIES', false),
        'profile_queries' => env('DB_PROFILE_QUERIES', false),
        'debug_mode' => env('DB_DEBUG_MODE', false),
    ],
];
