<?php

/**
 * Test specific button keys that are failing
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Specific Button Keys\n";
echo "===============================\n\n";

$testKeys = [
    'buttons.view_all',
    'buttons.explore',
    'buttons.create',
    'buttons.join'
];

foreach ($testKeys as $key) {
    try {
        $value = __($key);

        if (is_array($value)) {
            echo "❌ $key: RETURNS ARRAY - " . json_encode($value) . "\n";
        } elseif ($value === $key) {
            echo "❌ $key: KEY NOT FOUND\n";
        } else {
            echo "✅ $key: '$value'\n";
        }
    } catch (Exception $e) {
        echo "💥 $key: Exception - " . $e->getMessage() . "\n";
    }
}

echo "\n🔍 Let's check the common.php file structure manually:\n";
echo "======================================================\n";

$viCommon = include 'resources/lang/vi/common.php';

echo "Buttons section exists: " . (isset($viCommon['buttons']) ? 'YES' : 'NO') . "\n";

if (isset($viCommon['buttons'])) {
    echo "Buttons is array: " . (is_array($viCommon['buttons']) ? 'YES' : 'NO') . "\n";
    echo "Keys in buttons: " . implode(', ', array_keys($viCommon['buttons'])) . "\n";

    foreach ($testKeys as $key) {
        $keyPart = str_replace('buttons.', '', $key);
        echo "- $keyPart: " . (isset($viCommon['buttons'][$keyPart]) ? "'{$viCommon['buttons'][$keyPart]}'" : 'NOT FOUND') . "\n";
    }
}

echo "\n";

?>
