<?php

// Simple test without full Laravel bootstrap
require_once 'vendor/autoload.php';

// Define test data
$files = [
    'resources/views/components/header.blade.php',
    'resources/views/components/menu/community-mega-menu.blade.php',
    'resources/views/partials/language-switcher.blade.php',
    'resources/views/components/mobile-nav.blade.php'
];

$problematicCalls = [];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "❌ File not found: $file\n";
        continue;
    }

    echo "📁 Checking file: $file\n";
    $content = file_get_contents($file);

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches);
    foreach ($matches[1] as $key) {
        echo "  Found __('$key')\n";

        // Check if this key might return array
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $langFile = 'resources/lang/vi/' . $parts[0] . '.php';
            if (file_exists($langFile)) {
                $translations = include $langFile;
                $current = $translations;

                for ($i = 1; $i < count($parts); $i++) {
                    if (isset($current[$parts[$i]])) {
                        $current = $current[$parts[$i]];
                    } else {
                        echo "    ⚠️  Key not found: $key\n";
                        $current = null;
                        break;
                    }
                }

                if ($current !== null && is_array($current)) {
                    echo "    ❌ ARRAY RETURN: $key\n";
                    $problematicCalls[] = "__('$key') in $file";
                }
            }
        }
    }

    // Find t_xxx() calls
    preg_match_all('/t_(\w+)\([\'"]([^\'"]*)[\'"]/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $function = $match[1];
        $key = $match[2];
        echo "  Found t_{$function}('$key')\n";

        // Check in helper functions
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $langFile = 'resources/lang/vi/' . $function . '.php';
            if (file_exists($langFile)) {
                $translations = include $langFile;
                $current = $translations;

                for ($i = 0; $i < count($parts); $i++) {
                    if (isset($current[$parts[$i]])) {
                        $current = $current[$parts[$i]];
                    } else {
                        echo "    ⚠️  Key not found: $key in $function.php\n";
                        $current = null;
                        break;
                    }
                }

                if ($current !== null && is_array($current)) {
                    echo "    ❌ ARRAY RETURN: t_{$function}('$key')\n";
                    $problematicCalls[] = "t_{$function}('$key') in $file";
                }
            } else {
                echo "    ⚠️  Lang file not found: $langFile\n";
            }
        }
    }
    echo "\n";
}

echo "=== PROBLEMATIC CALLS FOUND ===\n";
if (count($problematicCalls) > 0) {
    foreach ($problematicCalls as $call) {
        echo "🚨 $call\n";
    }
} else {
    echo "✅ No problematic calls found!\n";
}

echo "\nCheck completed.\n";
