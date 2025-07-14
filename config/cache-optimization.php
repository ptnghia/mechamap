<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Optimization Configuration (No Redis)
    |--------------------------------------------------------------------------
    |
    | Cấu hình tối ưu hóa cache cho production environment khi không sử dụng Redis
    | Sử dụng file cache với các tối ưu hóa hiệu suất
    |
    */

    // File cache optimization settings
    'file_cache' => [
        'enabled' => env('CACHE_FILE_OPTIMIZATION_ENABLED', true),
        'path' => storage_path('framework/cache/data'),
        'permissions' => 0644,
        'serialize_method' => 'php', // php, json, igbinary
        'compression' => env('CACHE_FILE_COMPRESSION', false),
        'hash_algorithm' => 'xxh3', // md5, sha1, xxh3
    ],

    // Cache tags simulation (since file cache doesn't support tags)
    'tag_simulation' => [
        'enabled' => env('CACHE_TAG_SIMULATION_ENABLED', true),
        'prefix' => 'tag_',
        'separator' => ':',
    ],

    // Cache warming configuration
    'warming' => [
        'enabled' => env('CACHE_WARMING_ENABLED', true),
        'routes' => [
            'home',
            'threads.index',
            'categories.index',
            'forums.index',
            'marketplace.products.index',
        ],
        'schedule' => '0 */6 * * *', // Every 6 hours
    ],

    // Cache cleanup configuration
    'cleanup' => [
        'enabled' => env('CACHE_CLEANUP_ENABLED', true),
        'max_age_hours' => 24,
        'max_files' => 10000,
        'schedule' => '0 2 * * *', // Daily at 2 AM
    ],

    // Performance monitoring
    'monitoring' => [
        'enabled' => env('CACHE_MONITORING_ENABLED', true),
        'log_slow_operations' => true,
        'slow_threshold_ms' => 100,
        'track_hit_ratio' => true,
    ],

    // Cache strategies for different data types
    'strategies' => [
        'user_data' => [
            'ttl' => 3600, // 1 hour
            'tags' => ['users'],
            'serialize' => true,
        ],
        'thread_data' => [
            'ttl' => 1800, // 30 minutes
            'tags' => ['threads', 'forums'],
            'serialize' => true,
        ],
        'product_data' => [
            'ttl' => 3600, // 1 hour
            'tags' => ['products', 'marketplace'],
            'serialize' => true,
        ],
        'settings' => [
            'ttl' => 86400, // 24 hours
            'tags' => ['settings'],
            'serialize' => true,
        ],
        'navigation' => [
            'ttl' => 7200, // 2 hours
            'tags' => ['navigation', 'menus'],
            'serialize' => true,
        ],
    ],

    // Database query cache optimization
    'query_cache' => [
        'enabled' => env('DB_QUERY_CACHE_ENABLED', true),
        'default_ttl' => 1800, // 30 minutes
        'long_running_threshold' => 1000, // 1 second
        'cache_expensive_queries' => true,
    ],

    // Session optimization (when using database sessions)
    'session_optimization' => [
        'cleanup_probability' => 2, // 2% chance to run cleanup
        'cleanup_divisor' => 100,
        'max_lifetime' => 7200, // 2 hours
        'gc_maxlifetime' => 86400, // 24 hours
    ],

    // Memory usage optimization
    'memory' => [
        'max_cache_size_mb' => 256,
        'warning_threshold_mb' => 200,
        'auto_cleanup_enabled' => true,
        'memory_limit_check' => true,
    ],

    // Fallback configuration
    'fallback' => [
        'enabled' => true,
        'driver' => 'array', // Use array cache as fallback
        'log_fallback_usage' => true,
    ],

    // Development vs Production differences
    'environment_specific' => [
        'production' => [
            'aggressive_caching' => true,
            'cache_views' => true,
            'cache_config' => true,
            'cache_routes' => true,
            'preload_common_data' => true,
        ],
        'local' => [
            'aggressive_caching' => false,
            'cache_views' => false,
            'cache_config' => false,
            'cache_routes' => false,
            'preload_common_data' => false,
        ],
    ],

    // Cache key patterns
    'key_patterns' => [
        'user_profile' => 'user:profile:{user_id}',
        'user_permissions' => 'user:permissions:{user_id}',
        'thread_list' => 'threads:list:{forum_id}:{page}',
        'thread_detail' => 'thread:detail:{thread_id}',
        'product_list' => 'products:list:{category_id}:{page}',
        'product_detail' => 'product:detail:{product_id}',
        'forum_stats' => 'forum:stats:{forum_id}',
        'category_tree' => 'categories:tree',
        'navigation_menu' => 'navigation:menu:{role}',
        'settings_group' => 'settings:group:{group}',
    ],

    // Cache invalidation rules
    'invalidation' => [
        'user_update' => ['user:profile:{user_id}', 'user:permissions:{user_id}'],
        'thread_update' => ['thread:detail:{thread_id}', 'threads:list:*'],
        'product_update' => ['product:detail:{product_id}', 'products:list:*'],
        'forum_update' => ['forum:stats:{forum_id}', 'threads:list:{forum_id}:*'],
        'category_update' => ['categories:tree', 'navigation:menu:*'],
        'settings_update' => ['settings:group:*'],
    ],

    // Performance benchmarks
    'benchmarks' => [
        'file_cache_read_ms' => 5,
        'file_cache_write_ms' => 10,
        'database_query_ms' => 50,
        'view_render_ms' => 100,
        'api_response_ms' => 200,
    ],
];
