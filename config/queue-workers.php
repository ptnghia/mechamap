<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Queue Workers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for different queue workers in production environment
    |
    */

    'workers' => [
        // Critical email worker - high priority, immediate processing
        'emails-critical' => [
            'connection' => env('QUEUE_CONNECTION', 'database'),
            'queue' => 'emails-critical',
            'processes' => 2, // Number of worker processes
            'timeout' => 60,
            'sleep' => 1, // Sleep when no jobs (seconds)
            'tries' => 3,
            'memory' => 256, // Memory limit in MB
            'priority' => 'high',
            'description' => 'Critical emails (verification, password reset)',
        ],

        // Welcome email worker - medium priority
        'emails-welcome' => [
            'connection' => env('QUEUE_CONNECTION', 'database'),
            'queue' => 'emails-welcome',
            'processes' => 1,
            'timeout' => 120,
            'sleep' => 3,
            'tries' => 3,
            'memory' => 256,
            'priority' => 'medium',
            'description' => 'Welcome emails after verification',
        ],

        // General notifications worker - low priority
        'emails-notifications' => [
            'connection' => env('QUEUE_CONNECTION', 'database'),
            'queue' => 'emails-notifications',
            'processes' => 1,
            'timeout' => 180,
            'sleep' => 5,
            'tries' => 2,
            'memory' => 256,
            'priority' => 'low',
            'description' => 'General notifications and updates',
        ],

        // Marketing emails worker - lowest priority
        'emails-marketing' => [
            'connection' => env('QUEUE_CONNECTION', 'database'),
            'queue' => 'emails-marketing',
            'processes' => 1,
            'timeout' => 300,
            'sleep' => 10,
            'tries' => 2,
            'memory' => 256,
            'priority' => 'lowest',
            'description' => 'Marketing emails and newsletters',
        ],

        // Default worker for other jobs
        'default' => [
            'connection' => env('QUEUE_CONNECTION', 'database'),
            'queue' => 'default',
            'processes' => 2,
            'timeout' => 120,
            'sleep' => 3,
            'tries' => 3,
            'memory' => 512,
            'priority' => 'medium',
            'description' => 'Default queue for general jobs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Supervisor Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for supervisor process management
    |
    */

    'supervisor' => [
        'enabled' => env('QUEUE_SUPERVISOR_ENABLED', false),
        'config_path' => env('QUEUE_SUPERVISOR_CONFIG_PATH', '/etc/supervisor/conf.d/'),
        'log_path' => env('QUEUE_SUPERVISOR_LOG_PATH', storage_path('logs/supervisor/')),
        'user' => env('QUEUE_SUPERVISOR_USER', 'www-data'),
        'auto_restart' => true,
        'restart_delay' => 1, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for queue monitoring and alerting
    |
    */

    'monitoring' => [
        'enabled' => env('QUEUE_MONITORING_ENABLED', true),
        'alert_thresholds' => [
            'failed_jobs' => 10, // Alert when failed jobs exceed this number
            'queue_size' => 100, // Alert when queue size exceeds this number
            'processing_time' => 300, // Alert when job takes longer than this (seconds)
        ],
        'alert_channels' => [
            'email' => env('QUEUE_ALERT_EMAIL', 'admin@mechamap.com'),
            'slack' => env('QUEUE_ALERT_SLACK_WEBHOOK', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for queue health checks
    |
    */

    'health_check' => [
        'enabled' => env('QUEUE_HEALTH_CHECK_ENABLED', true),
        'interval' => 60, // Check every 60 seconds
        'max_failed_jobs' => 50,
        'max_queue_size' => 500,
        'max_processing_time' => 600, // 10 minutes
    ],
];
