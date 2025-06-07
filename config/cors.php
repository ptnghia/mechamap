<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'auth/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => env('APP_ENV') === 'local'
        ? ['http://mechamap.test:3000', 'http://mechamap.test', 'http://localhost:3000', 'https://localhost:3000', 'http://127.0.0.1:3000', 'https://127.0.0.1:3000']
        : explode(',', env('CORS_ALLOWED_ORIGINS', 'http://mechamap.test:3000,http://mechamap.test')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
