<?php

return [
    'cache_ttl' => env('TRANSLATION_CACHE_TTL', 3600),
    'version_cache_ttl' => env('TRANSLATION_VERSION_CACHE_TTL', 300),
    'critical_groups' => [
        'common',
        'auth',
        'notifications',
    'thread',
    'ui',
    'features',
    ],
    'enable_manifest' => env('TRANSLATION_MANIFEST_ENABLED', true),
];
