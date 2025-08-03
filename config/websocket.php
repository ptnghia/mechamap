<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WebSocket Server Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Node.js WebSocket server
    | that handles real-time communication for MechaMap application.
    |
    */

    'server' => [
        'url' => env('WEBSOCKET_SERVER_URL', 'http://localhost:3000'),
        'host' => env('WEBSOCKET_SERVER_HOST', 'localhost'),
        'port' => env('WEBSOCKET_SERVER_PORT', 3000),
        'secure' => env('WEBSOCKET_SERVER_SECURE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for communicating with the WebSocket server API
    |
    */

    'api_key' => env('WEBSOCKET_API_KEY', 'your-api-key-here'),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for broadcasting events to the WebSocket server.
    |
    */

    'broadcasting' => [
        'url' => env('NODEJS_BROADCAST_URL', 'http://localhost:3000'),
        'endpoint' => '/api/broadcast',
        'timeout' => 30,
        'verify_ssl' => env('APP_ENV') === 'production',
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration that will be passed to the frontend client.
    |
    */

    'client' => [
        'url' => env('WEBSOCKET_SERVER_URL', 'http://localhost:3000'),
        'secure' => env('WEBSOCKET_SERVER_SECURE', false),
        'transports' => ['websocket', 'polling'],
        'timeout' => 20000,
        'reconnection' => true,
        'reconnection_delay' => 1000,
        'reconnection_attempts' => 5,
        'max_reconnection_attempts' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment-specific Settings
    |--------------------------------------------------------------------------
    |
    | Auto-detect environment settings based on APP_URL and WEBSOCKET_SERVER_URL
    |
    */

    'auto_detect_environment' => true,

    'environments' => [
        'local' => [
            'server_url' => 'http://localhost:3000',
            'laravel_url' => 'https://mechamap.test',
            'cors_origins' => [
                'https://mechamap.test',
                'http://localhost:8000',
                'http://127.0.0.1:8000',
            ],
        ],
        'production' => [
            'server_url' => 'https://realtime.mechamap.com',
            'laravel_url' => 'https://mechamap.com',
            'cors_origins' => [
                'https://mechamap.com',
                'https://www.mechamap.com',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Environment Detection
    |--------------------------------------------------------------------------
    |
    | Automatically detect environment based on domain configuration
    |
    */

    'domain_mapping' => [
        'mechamap.test' => 'local',
        'localhost' => 'local',
        '127.0.0.1' => 'local',
        'mechamap.com' => 'production',
        'www.mechamap.com' => 'production',
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WebSocket server monitoring.
    |
    */

    'monitoring' => [
        'enabled' => env('WEBSOCKET_MONITORING_ENABLED', true),
        'health_check_url' => env('WEBSOCKET_SERVER_URL', 'http://localhost:3000') . '/api/health',
        'metrics_url' => env('WEBSOCKET_SERVER_URL', 'http://localhost:3000') . '/api/monitoring/metrics',
    ],

];
