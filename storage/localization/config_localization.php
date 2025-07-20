<?php

/**
 * Laravel Configuration Updates for New Localization Structure
 * Add this to config/app.php or create new config/localization.php
 */

return [
    /*
    |--------------------------------------------------------------------------
    | New Localization Structure
    |--------------------------------------------------------------------------
    |
    | Configuration for the new feature-based localization structure
    |
    */

    'new_structure' => [
        'enabled' => env('NEW_LOCALIZATION_ENABLED', true),
        'path' => 'lang_new',
        'fallback_to_old' => env('LOCALIZATION_FALLBACK', false),
    ],

    'categories' => [
        'core' => ['auth', 'validation', 'pagination', 'passwords'],
        'ui' => ['common', 'navigation', 'buttons', 'forms', 'modals'],
        'content' => ['home', 'pages', 'alerts'],
        'features' => ['forum', 'marketplace', 'showcase', 'knowledge', 'community'],
        'user' => ['profile', 'settings', 'notifications', 'messages'],
        'admin' => ['dashboard', 'users', 'system'],
    ],
];
