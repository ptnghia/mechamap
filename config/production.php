<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains production-specific configuration for MechaMap
    | when deployed to https://mechamap.com
    |
    */

    'domain' => [
        'primary' => 'mechamap.com',
        'www' => 'www.mechamap.com',
        'cdn' => env('CDN_URL'), // No default CDN URL - must be explicitly set
        'api' => env('API_URL', 'https://api.mechamap.com'),
    ],

    'ssl' => [
        'enabled' => env('SSL_REDIRECT', true),
        'force_https' => env('FORCE_HTTPS', true),
        'hsts_max_age' => env('HSTS_MAX_AGE', 31536000),
        'secure_proxy_header' => env('SECURE_PROXY_SSL_HEADER', 'HTTP_X_FORWARDED_PROTO'),
    ],

    'cache' => [
        'enabled' => env('RESPONSE_CACHE_ENABLED', true),
        'ttl' => env('CACHE_TTL', 3600),
        'tags_enabled' => true,
        'opcache_enabled' => env('OPCACHE_ENABLED', true),
    ],

    'performance' => [
        'route_cache' => env('ROUTE_CACHE_ENABLED', true),
        'config_cache' => env('CONFIG_CACHE_ENABLED', true),
        'view_cache' => env('VIEW_CACHE_ENABLED', true),
        'event_cache' => true,
        'query_cache' => true,
    ],

    'security' => [
        'headers' => [
            'enabled' => env('SECURE_HEADERS_ENABLED', true),
            'csp_enabled' => env('CONTENT_SECURITY_POLICY_ENABLED', true),
            'referrer_policy' => 'strict-origin-when-cross-origin',
            'x_frame_options' => 'DENY',
            'x_content_type_options' => 'nosniff',
            'x_xss_protection' => '1; mode=block',
        ],
        'rate_limiting' => [
            'enabled' => env('RATE_LIMIT_ENABLED', true),
            'api_limit' => env('API_RATE_LIMIT', 60),
            'web_limit' => env('WEB_RATE_LIMIT', 1000),
        ],
    ],

    'monitoring' => [
        'google_analytics' => env('GOOGLE_ANALYTICS_ID'),
        'sentry_dsn' => env('SENTRY_LARAVEL_DSN'),
        'error_tracking' => true,
        'performance_monitoring' => true,
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'disk' => env('BACKUP_DISK', 's3'),
        'schedule' => env('BACKUP_SCHEDULE', 'daily'),
        'retention_days' => env('DB_BACKUP_RETENTION_DAYS', 30),
    ],

    'features' => [
        'marketplace' => env('FEATURE_MARKETPLACE_ENABLED', true),
        'chat' => env('FEATURE_CHAT_ENABLED', true),
        'notifications' => env('FEATURE_NOTIFICATIONS_ENABLED', true),
        'advanced_search' => env('FEATURE_ADVANCED_SEARCH_ENABLED', true),
        'websockets' => env('FEATURE_WEBSOCKETS_ENABLED', true),
    ],

    'assets' => [
        'versioning' => env('ASSET_VERSIONING_ENABLED', true),
        'cdn_enabled' => env('CDN_ENABLED', false),
        'compression' => [
            'enabled' => true,
            'gzip' => true,
            'brotli' => true,
        ],
        'optimization' => [
            'css_minify' => true,
            'js_minify' => true,
            'image_optimization' => env('IMAGE_OPTIMIZATION_ENABLED', true),
            'image_quality' => env('IMAGE_QUALITY', 85),
        ],
    ],

    'database' => [
        'read_write_split' => false,
        'connection_pooling' => true,
        'query_logging' => false,
        'slow_query_threshold' => 1000, // milliseconds
    ],

    'mail' => [
        'queue_enabled' => true,
        'retry_attempts' => 3,
        'rate_limit' => 100, // emails per minute
    ],

    'search' => [
        'driver' => env('SEARCH_DRIVER', 'database'),
        'elasticsearch' => [
            'host' => env('ELASTICSEARCH_HOST', 'localhost:9200'),
            'index' => env('ELASTICSEARCH_INDEX', 'mechamap'),
        ],
    ],

    'social' => [
        'facebook_app_id' => env('FACEBOOK_APP_ID'),
        'twitter_api_key' => env('TWITTER_API_KEY'),
        'linkedin_client_id' => env('LINKEDIN_CLIENT_ID'),
    ],

    'payment' => [
        'stripe' => [
            'enabled' => !empty(env('STRIPE_KEY')),
            'public_key' => env('STRIPE_KEY'),
            'secret_key' => env('STRIPE_SECRET'),
        ],
        'paypal' => [
            'enabled' => !empty(env('PAYPAL_CLIENT_ID')),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        ],
    ],

    'maintenance' => [
        'enabled' => env('MAINTENANCE_MODE_ENABLED', false),
        'secret' => env('MAINTENANCE_MODE_SECRET', 'mechamap-maintenance-2024'),
        'template' => 'maintenance',
        'retry_after' => 60,
    ],
];
