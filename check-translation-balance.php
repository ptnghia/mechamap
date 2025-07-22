<?php

/**
 * CHECK TRANSLATION BALANCE
 * Kiểm tra số lượng bản dịch tiếng Anh và tiếng Việt có khớp nhau không
 */

echo "=== CHECKING TRANSLATION BALANCE ===\n\n";

// Function to flatten nested arrays with dot notation
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

// Load all translation files
$viPath = __DIR__ . '/resources/lang/vi/';
$enPath = __DIR__ . '/resources/lang/en/';

$viTranslations = [];
$enTranslations = [];

echo "📊 LOADING TRANSLATION FILES:\n\n";

// Load VI translations
$viFiles = glob($viPath . '*.php');
foreach ($viFiles as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        $flattened = flattenArray($translations, $filename);
        $viTranslations = array_merge($viTranslations, $flattened);
        echo "✅ VI $filename: " . count($flattened) . " keys\n";
    }
}

echo "\n";

// Load EN translations
$enFiles = glob($enPath . '*.php');
foreach ($enFiles as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        $flattened = flattenArray($translations, $filename);
        $enTranslations = array_merge($enTranslations, $flattened);
        echo "✅ EN $filename: " . count($flattened) . " keys\n";
    }
}

echo "\n📈 TOTAL COUNTS:\n";
echo "  VI: " . count($viTranslations) . " keys\n";
echo "  EN: " . count($enTranslations) . " keys\n";

// Check balance
$viCount = count($viTranslations);
$enCount = count($enTranslations);
$difference = abs($viCount - $enCount);

echo "\n🔍 BALANCE ANALYSIS:\n";

if ($viCount === $enCount) {
    echo "✅ PERFECT BALANCE! Both languages have exactly the same number of keys.\n";
    echo "🎯 Translation coverage: 100% balanced\n";
} else {
    echo "⚠️  IMBALANCE DETECTED!\n";
    echo "📊 Difference: $difference keys\n";
    
    if ($viCount > $enCount) {
        echo "🇻🇳 Vietnamese has $difference more keys than English\n";
        echo "📋 Missing English keys:\n";
        
        $missingInEn = array_diff(array_keys($viTranslations), array_keys($enTranslations));
        $count = 0;
        foreach ($missingInEn as $key) {
            if ($count < 20) { // Show first 20
                echo "  - $key\n";
                $count++;
            }
        }
        if (count($missingInEn) > 20) {
            echo "  ... and " . (count($missingInEn) - 20) . " more\n";
        }
    } else {
        echo "🇬🇧 English has $difference more keys than Vietnamese\n";
        echo "📋 Missing Vietnamese keys:\n";
        
        $missingInVi = array_diff(array_keys($enTranslations), array_keys($viTranslations));
        $count = 0;
        foreach ($missingInVi as $key) {
            if ($count < 20) { // Show first 20
                echo "  - $key\n";
                $count++;
            }
        }
        if (count($missingInVi) > 20) {
            echo "  ... and " . (count($missingInVi) - 20) . " more\n";
        }
    }
}

// Check for common keys
$commonKeys = array_intersect(array_keys($viTranslations), array_keys($enTranslations));
$commonCount = count($commonKeys);

echo "\n🤝 COMMON KEYS:\n";
echo "  Both languages: $commonCount keys\n";
echo "  Coverage: " . round(($commonCount / max($viCount, $enCount)) * 100, 2) . "%\n";

// File-by-file comparison
echo "\n📁 FILE-BY-FILE COMPARISON:\n\n";

$allFiles = array_unique(array_merge(
    array_map(function($f) { return basename($f, '.php'); }, $viFiles),
    array_map(function($f) { return basename($f, '.php'); }, $enFiles)
));

sort($allFiles);

foreach ($allFiles as $filename) {
    $viFile = $viPath . $filename . '.php';
    $enFile = $enPath . $filename . '.php';
    
    $viExists = file_exists($viFile);
    $enExists = file_exists($enFile);
    
    if ($viExists && $enExists) {
        $viData = include $viFile;
        $enData = include $enFile;
        
        $viFlat = flattenArray($viData, '');
        $enFlat = flattenArray($enData, '');
        
        $viFileCount = count($viFlat);
        $enFileCount = count($enFlat);
        
        if ($viFileCount === $enFileCount) {
            echo "✅ $filename: VI($viFileCount) = EN($enFileCount)\n";
        } else {
            $fileDiff = abs($viFileCount - $enFileCount);
            if ($viFileCount > $enFileCount) {
                echo "⚠️  $filename: VI($viFileCount) > EN($enFileCount) [+$fileDiff]\n";
            } else {
                echo "⚠️  $filename: VI($viFileCount) < EN($enFileCount) [-$fileDiff]\n";
            }
        }
    } elseif ($viExists) {
        echo "❌ $filename: VI exists, EN missing\n";
    } elseif ($enExists) {
        echo "❌ $filename: EN exists, VI missing\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Vietnamese files: " . count($viFiles) . "\n";
echo "English files: " . count($enFiles) . "\n";
echo "Vietnamese keys: $viCount\n";
echo "English keys: $enCount\n";
echo "Balance status: " . ($viCount === $enCount ? "PERFECT" : "IMBALANCED ($difference keys difference)") . "\n";

echo "\n✅ Translation balance check completed at " . date('Y-m-d H:i:s') . "\n";
?>
