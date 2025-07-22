<?php

/**
 * Generate missing translation keys for critical files
 * Focus on user-facing marketplace & forums
 */

// Target critical files first
$criticalFiles = [
    'marketplace/checkout/index.blade.php',
    'marketplace/cart/index.blade.php',
    'marketplace/index.blade.php',
    'forums/index.blade.php',
    'forums/search-advanced.blade.php',
    'auth/login.blade.php',
    'auth/register.blade.php',
    'home.blade.php'
];

echo "=== GENERATE CRITICAL TRANSLATION KEYS ===\n\n";

foreach ($criticalFiles as $file) {
    $filePath = __DIR__ . '/resources/views/' . $file;

    if (!file_exists($filePath)) {
        echo "❌ File not found: $file\n";
        continue;
    }

    $content = file_get_contents($filePath);
    echo "🔍 Processing: $file\n";

    // Extract all translation calls
    $translations = [];

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"]/', $content, $matches);
    foreach ($matches[1] as $key) {
        if (!empty($key) && str_contains($key, '.')) {
            $translations[] = "__('$key')";
        }
    }

    // Find t_xxx() calls
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"]/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $helper = 't_' . $match[1];
        $key = $match[2];
        if (!empty($key)) {
            $translations[] = "{$helper}('$key')";
        }
    }

    if (!empty($translations)) {
        echo "   Found " . count($translations) . " translation calls:\n";
        foreach (array_slice($translations, 0, 10) as $trans) { // Show first 10
            echo "   - $trans\n";
        }
        if (count($translations) > 10) {
            echo "   ... and " . (count($translations) - 10) . " more\n";
        }
        echo "\n";
    }
}

echo "✅ Critical files analysis completed\n";
?>
