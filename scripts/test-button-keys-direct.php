<?php

/**
 * Direct test of button keys in Laravel environment
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Direct Button Keys Test in Laravel Environment\n";
echo "==================================================\n\n";

$buttonKeys = [
    'buttons.view_all',
    'buttons.explore',
    'buttons.create',
    'buttons.join'
];

echo "Testing with __() function:\n";
foreach ($buttonKeys as $key) {
    $result = __($key);
    echo "- __('$key') = ";

    if (is_string($result)) {
        echo "'$result'\n";
    } elseif (is_array($result)) {
        echo "ARRAY: " . json_encode($result) . "\n";
    } else {
        echo gettype($result) . ": " . var_export($result, true) . "\n";
    }
}

echo "\nTesting with trans() function:\n";
foreach ($buttonKeys as $key) {
    $result = trans($key);
    echo "- trans('$key') = ";

    if (is_string($result)) {
        echo "'$result'\n";
    } elseif (is_array($result)) {
        echo "ARRAY: " . json_encode($result) . "\n";
    } else {
        echo gettype($result) . ": " . var_export($result, true) . "\n";
    }
}

echo "\nTesting with full common.buttons.* keys:\n";
foreach ($buttonKeys as $key) {
    $fullKey = 'common.' . $key;
    $result = __($fullKey);
    echo "- __('$fullKey') = ";

    if (is_string($result)) {
        echo "'$result'\n";
    } elseif (is_array($result)) {
        echo "ARRAY: " . json_encode($result) . "\n";
    } else {
        echo gettype($result) . ": " . var_export($result, true) . "\n";
    }
}

echo "\n";

?>
