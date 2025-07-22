<?php

/**
 * Check student.learning-hub.blade.php translation keys
 */

echo "🔍 Checking student/learning-hub.blade.php translation keys\n";
echo "=========================================================\n\n";

// Read the blade file
$bladeFile = 'resources/views/student/learning-hub.blade.php';
if (!file_exists($bladeFile)) {
    echo "❌ File not found: $bladeFile\n";
    exit(1);
}

$content = file_get_contents($bladeFile);

// Extract all __('student.*') calls
preg_match_all("/__\('student\.([^']+)'\)/", $content, $matches);

$keysUsed = [];
foreach ($matches[1] as $key) {
    $keysUsed[] = 'student.' . $key;
}

$keysUsed = array_unique($keysUsed);

echo "📋 Found " . count($keysUsed) . " student.* keys in blade file:\n";
foreach ($keysUsed as $key) {
    echo "  - $key\n";
}

echo "\n🧪 Testing each key against Vietnamese translation file:\n";
echo "======================================================\n";

// Load Vietnamese student.php
$viFile = 'resources/lang/vi/student.php';
if (!file_exists($viFile)) {
    echo "❌ Vietnamese student.php not found\n";
    exit(1);
}

$viTranslations = include $viFile;

function getNestedValue($array, $key) {
    $parts = explode('.', $key);
    array_shift($parts); // Remove 'student' prefix

    $current = $array;
    foreach ($parts as $part) {
        if (!isset($current[$part])) {
            return null;
        }
        $current = $current[$part];
    }

    return $current;
}

$problematicKeys = [];

foreach ($keysUsed as $key) {
    $value = getNestedValue($viTranslations, $key);

    if ($value === null) {
        echo "❌ $key: KEY NOT FOUND\n";
        $problematicKeys[] = $key . ' (missing)';
    } elseif (is_array($value)) {
        echo "❌ $key: RETURNS ARRAY - " . json_encode($value) . "\n";
        $problematicKeys[] = $key . ' (array)';
    } elseif (is_string($value)) {
        echo "✅ $key: '$value'\n";
    } else {
        echo "⚠️  $key: " . gettype($value) . " - " . var_export($value, true) . "\n";
        $problematicKeys[] = $key . ' (wrong type)';
    }
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total keys tested: " . count($keysUsed) . "\n";
echo "Problematic keys: " . count($problematicKeys) . "\n";

if (!empty($problematicKeys)) {
    echo "\n❌ KEYS THAT NEED FIXING:\n";
    foreach ($problematicKeys as $key) {
        echo "  - $key\n";
    }
} else {
    echo "\n✅ All keys are working correctly!\n";
}

echo "\n";

?>