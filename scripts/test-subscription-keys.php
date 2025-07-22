<?php

// Test subscription key resolution
echo "Testing subscription key resolution...\n";
echo "====================================\n\n";

$subscriptionFile = 'resources/lang/vi/subscription.php';
$sub = include $subscriptionFile;

echo "1. Subscription file loaded: " . (is_array($sub) ? "YES" : "NO") . "\n";
echo "2. Keys count: " . count($sub) . "\n";

$testKeys = [
    'Upgrade your account to unlock premium features and enhance your experience.',
    'Enjoy browsing without any advertisements or distractions.',
    'Send unlimited private messages to other users.',
    'Get a special badge that shows your premium status.',
    'Get faster responses from our support team.'
];

echo "\n3. Testing individual keys:\n";
foreach ($testKeys as $key) {
    $exists = isset($sub[$key]);
    echo "   - '$key': " . ($exists ? "EXISTS" : "NOT FOUND") . "\n";
    if ($exists) {
        echo "     Value: " . $sub[$key] . "\n";
    }
}

// Test full namespace resolution
echo "\n4. Testing namespace resolution:\n";
$allTranslations = ['subscription' => $sub];

function resolveTranslation($key, $allTranslations) {
    if (strpos($key, '.') === false) {
        return isset($allTranslations[$key]) ? $allTranslations[$key] : null;
    }

    $parts = explode('.', $key, 2);
    $namespace = $parts[0];
    $subKey = $parts[1];

    if (!isset($allTranslations[$namespace])) {
        echo "   DEBUG: Namespace '$namespace' not found\n";
        return null;
    }

    if (!isset($allTranslations[$namespace][$subKey])) {
        echo "   DEBUG: SubKey '$subKey' not found in namespace '$namespace'\n";
        return null;
    }

    return $allTranslations[$namespace][$subKey];
}

foreach ($testKeys as $key) {
    $fullKey = 'subscription.' . $key;
    $result = resolveTranslation($fullKey, $allTranslations);
    echo "   - '$fullKey': " . ($result ? "FOUND" : "NOT FOUND") . "\n";
}
