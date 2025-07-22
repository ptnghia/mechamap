<?php

/**
 * Test Translation Keys
 * Test specific translation keys that were causing htmlspecialchars errors
 */

echo "🧪 Testing Translation Keys\n";
echo "===========================\n\n";

// Simulate Laravel environment
require_once 'vendor/autoload.php';

// Test the problematic translation keys
$testKeys = [
    'common.buttons.search',
    'common.buttons.save',
    'common.buttons.cancel',
    'common.buttons.delete',
    'search.form.placeholder',
    'search.scope.all_content',
    'auth.login.title',
    'navigation.main.marketplace',
];

echo "Testing Vietnamese translations:\n";
echo "================================\n";

foreach ($testKeys as $key) {
    try {
        // Load the translation manually
        $parts = explode('.', $key);
        $file = $parts[0];
        $filePath = "resources/lang/vi/{$file}.php";

        if (!file_exists($filePath)) {
            echo "❌ {$key}: File not found ({$filePath})\n";
            continue;
        }

        $translations = include $filePath;

        // Navigate to the nested key
        $value = $translations;
        for ($i = 1; $i < count($parts); $i++) {
            if (!isset($value[$parts[$i]])) {
                $value = null;
                break;
            }
            $value = $value[$parts[$i]];
        }

        if ($value === null) {
            echo "❌ {$key}: Key not found\n";
        } elseif (is_array($value)) {
            echo "❌ {$key}: Returns ARRAY - " . json_encode($value) . "\n";
        } elseif (is_string($value)) {
            echo "✅ {$key}: Returns STRING - '{$value}'\n";
        } else {
            echo "⚠️  {$key}: Returns " . gettype($value) . " - " . var_export($value, true) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ {$key}: Exception - " . $e->getMessage() . "\n";
    }
}

echo "\nTesting English translations:\n";
echo "=============================\n";

foreach ($testKeys as $key) {
    try {
        $parts = explode('.', $key);
        $file = $parts[0];
        $filePath = "resources/lang/en/{$file}.php";

        if (!file_exists($filePath)) {
            echo "❌ {$key}: File not found ({$filePath})\n";
            continue;
        }

        $translations = include $filePath;

        $value = $translations;
        for ($i = 1; $i < count($parts); $i++) {
            if (!isset($value[$parts[$i]])) {
                $value = null;
                break;
            }
            $value = $value[$parts[$i]];
        }

        if ($value === null) {
            echo "❌ {$key}: Key not found\n";
        } elseif (is_array($value)) {
            echo "❌ {$key}: Returns ARRAY - " . json_encode($value) . "\n";
        } elseif (is_string($value)) {
            echo "✅ {$key}: Returns STRING - '{$value}'\n";
        } else {
            echo "⚠️  {$key}: Returns " . gettype($value) . " - " . var_export($value, true) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ {$key}: Exception - " . $e->getMessage() . "\n";
    }
}

echo "\n🔍 Checking for remaining problematic patterns:\n";
echo "===============================================\n";

$translationFiles = glob('resources/lang/*/*.php');
$problemFiles = [];

foreach ($translationFiles as $file) {
    $content = file_get_contents($file);

    // Check for array patterns with numeric indexes
    if (preg_match('/\s+0\s*=>\s*/', $content)) {
        $problemFiles[] = $file;
        $matches = preg_match_all('/\s+0\s*=>\s*/', $content);
        echo "⚠️  {$file}: Found {$matches} problematic patterns\n";
    }
}

if (empty($problemFiles)) {
    echo "✅ No remaining problematic array patterns found!\n";
}

echo "\n🎯 Summary:\n";
echo "===========\n";
echo "Translation key testing completed.\n";
echo "If all keys return STRING values, the htmlspecialchars error should be resolved.\n";
echo "If any keys still return ARRAY, those need to be fixed manually.\n\n";

echo "💡 Next steps:\n";
echo "1. Access your website and test the header component\n";
echo "2. Check for new errors in: storage/logs/laravel.log\n";
echo "3. If errors persist, run: php check-error-logs.php\n\n";

?>
