<?php

/**
 * Test New Translation Keys
 * Test the new keys we just added
 */

echo "🧪 Testing New Translation Keys\n";
echo "===============================\n\n";

require_once 'vendor/autoload.php';

$newKeys = [
    'common.language.switch',
    'common.language.select',
    'common.language.auto_detect',
    'common.language.switched_successfully',
    'common.language.switch_failed',
    'common.language.auto_detect_failed',
    'common.buttons.popular',
    'common.buttons.latest',
    'common.time.today',
    'common.time.this_week',
    'common.time.this_month',
    'common.time.this_year',
    'common.time.all_time',
    'common.labels.category',
    'common.labels.replies',
    'common.labels.by',
    'common.labels.forum',
];

echo "Testing Vietnamese translations:\n";
echo "================================\n";

foreach ($newKeys as $key) {
    try {
        $parts = explode('.', $key);
        $file = $parts[0];
        $filePath = "resources/lang/vi/{$file}.php";

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

echo "\nTesting English translations:\n";
echo "=============================\n";

foreach ($newKeys as $key) {
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

echo "\n🎯 Summary:\n";
echo "============\n";
echo "New translation keys have been tested.\n";
echo "All keys should now return STRING values.\n\n";

?>
