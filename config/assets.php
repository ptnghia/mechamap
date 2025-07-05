<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asset Versioning Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho việc versioning assets để cache busting
    |
    */

    // Bật/tắt asset versioning
    'versioning_enabled' => env('ASSET_VERSIONING_ENABLED', true),

    // Phương thức versioning: 'filemtime', 'manual', 'git'
    'versioning_method' => env('ASSET_VERSIONING_METHOD', 'filemtime'),

    // Version thủ công (chỉ dùng khi method = 'manual')
    'manual_version' => env('ASSET_MANUAL_VERSION', '1.0.0'),

    // Cache thời gian cho versioned assets (giây)
    'cache_duration' => [
        'versioned' => 31536000, // 1 năm
        'non_versioned' => 3600,  // 1 giờ
    ],

    // Danh sách file extensions được versioning
    'versioned_extensions' => [
        'css',
        'js',
        'png',
        'jpg',
        'jpeg',
        'gif',
        'svg',
        'ico',
        'woff',
        'woff2',
        'ttf',
        'eot',
    ],

    // Paths được loại trừ khỏi versioning
    'excluded_paths' => [
        'vendor/',
        'node_modules/',
    ],

    // Development mode settings
    'development' => [
        'disable_caching' => env('APP_DEBUG', false),
        'force_reload' => env('ASSET_FORCE_RELOAD', false),
    ],
];
