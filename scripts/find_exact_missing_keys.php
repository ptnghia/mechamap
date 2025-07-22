<?php

/**
 * Find exact missing keys between Vietnamese and English files
 */

function loadTranslationFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    try {
        $content = include $filePath;
        return is_array($content) ? $content : [];
    } catch (Exception $e) {
        echo "Error loading $filePath: " . $e->getMessage() . "\n";
        return [];
    }
}

function flattenArray($array, $prefix = '') {
    $result = [];
    
    foreach ($array as $key => $value) {
        $newKey = $prefix === '' ? $key : $prefix . '.' . $key;
        
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value, $newKey));
        } else {
            $result[$newKey] = $value;
        }
    }
    
    return $result;
}

// Check user/messages.php
echo "🔍 Checking user/messages.php\n";
echo "==============================\n";

$viFile = dirname(__DIR__) . '/resources/lang/vi/user/messages.php';
$enFile = dirname(__DIR__) . '/resources/lang/en/user/messages.php';

$viData = loadTranslationFile($viFile);
$enData = loadTranslationFile($enFile);

$viFlat = flattenArray($viData);
$enFlat = flattenArray($enData);

$missingInEn = array_diff_key($viFlat, $enFlat);
$extraInEn = array_diff_key($enFlat, $viFlat);

echo "📊 Vietnamese keys: " . count($viFlat) . "\n";
echo "📊 English keys: " . count($enFlat) . "\n";
echo "❌ Missing in English: " . count($missingInEn) . "\n";
echo "➕ Extra in English: " . count($extraInEn) . "\n\n";

if (!empty($missingInEn)) {
    echo "❌ MISSING KEYS IN ENGLISH:\n";
    foreach ($missingInEn as $key => $value) {
        echo "   '$key' => '$value'\n";
    }
    echo "\n";
}

if (!empty($extraInEn)) {
    echo "➕ EXTRA KEYS IN ENGLISH:\n";
    foreach ($extraInEn as $key => $value) {
        echo "   '$key' => '$value'\n";
    }
    echo "\n";
}

// Check ui/forms.php
echo "🔍 Checking ui/forms.php\n";
echo "=========================\n";

$viFile = dirname(__DIR__) . '/resources/lang/vi/ui/forms.php';
$enFile = dirname(__DIR__) . '/resources/lang/en/ui/forms.php';

$viData = loadTranslationFile($viFile);
$enData = loadTranslationFile($enFile);

$viFlat = flattenArray($viData);
$enFlat = flattenArray($enData);

$missingInEn = array_diff_key($viFlat, $enFlat);
$extraInEn = array_diff_key($enFlat, $viFlat);

echo "📊 Vietnamese keys: " . count($viFlat) . "\n";
echo "📊 English keys: " . count($enFlat) . "\n";
echo "❌ Missing in English: " . count($missingInEn) . "\n";
echo "➕ Extra in English: " . count($extraInEn) . "\n\n";

if (!empty($missingInEn)) {
    echo "❌ MISSING KEYS IN ENGLISH:\n";
    foreach ($missingInEn as $key => $value) {
        echo "   '$key' => '$value'\n";
    }
    echo "\n";
}

if (!empty($extraInEn)) {
    echo "➕ EXTRA KEYS IN ENGLISH:\n";
    foreach ($extraInEn as $key => $value) {
        echo "   '$key' => '$value'\n";
    }
}

?>
