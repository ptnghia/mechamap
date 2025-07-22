<?php

/**
 * Simple translation key tester
 * Location: scripts/test-translation-keys.php
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Translation Keys\n";
echo "============================\n\n";

$keys = [
    'search.form.placeholder',
    'search.scope.all_content',
    'search.scope.in_thread',
    'common.technical.resources',
    'navigation.main.marketplace',
    'marketplace.cart.shopping_cart',
    'forum.search.recent_searches',
];

foreach ($keys as $key) {
    echo "Testing: $key\n";

    try {
        $value = __($key);

        if (is_array($value)) {
            echo "  ❌ RETURNS ARRAY: " . json_encode($value) . "\n";
        } elseif ($value === $key) {
            echo "  ⚠️  KEY NOT FOUND\n";
        } else {
            echo "  ✅ OK: " . substr($value, 0, 50) . "...\n";
        }
    } catch (Exception $e) {
        echo "  💥 ERROR: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "✅ Test completed!\n";
