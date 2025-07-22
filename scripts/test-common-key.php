<?php

// Simple test script for common.new_threads key
$commonFile = 'D:/xampp/htdocs/laravel/mechamap_backend/resources/lang/vi/common.php';

echo "Testing common.php file loading...\n";
echo "================================\n\n";

// Test 1: Direct include
$translations = include $commonFile;

echo "1. File loaded successfully: " . (is_array($translations) ? "YES" : "NO") . "\n";
echo "2. Array keys count: " . count($translations) . "\n";
echo "3. Contains 'buttons' key: " . (isset($translations['buttons']) ? "YES" : "NO") . "\n";
echo "4. Contains 'new_threads' key: " . (isset($translations['new_threads']) ? "YES" : "NO") . "\n";

if (isset($translations['new_threads'])) {
    echo "5. Value of new_threads: " . $translations['new_threads'] . "\n";
} else {
    echo "5. new_threads key not found at root level\n";
    echo "   Available root keys: " . implode(', ', array_keys($translations)) . "\n";
}

// Test 2: Laravel-style resolution
function resolveTranslation($key, $translations) {
    if (strpos($key, '.') === false) {
        return isset($translations[$key]) ? $translations[$key] : null;
    }

    $parts = explode('.', $key, 2);
    $namespace = $parts[0];
    $subKey = $parts[1];

    if (!isset($translations[$namespace])) {
        return null;
    }

    return getNestedValue($translations[$namespace], $subKey);
}

function getNestedValue($array, $path) {
    $keys = explode('.', $path);
    $current = $array;

    foreach ($keys as $key) {
        if (!is_array($current) || !array_key_exists($key, $current)) {
            return null;
        }
        $current = $current[$key];
    }

    return $current;
}

$result = resolveTranslation('new_threads', $translations);
echo "\n6. Laravel-style resolution result: " . ($result ?: "NOT FOUND") . "\n";
