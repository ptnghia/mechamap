<?php

/**
 * ANALYZE TRANSLATION STRUCTURE
 * Phân tích cấu trúc keys trong translation files và blade files
 * Phát hiện keys 4 cấp và inconsistencies
 */

echo "=== ANALYZING TRANSLATION STRUCTURE ===\n\n";

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

// Load all translation files with proper nested structure
$viPath = __DIR__ . '/resources/lang/vi/';
$enPath = __DIR__ . '/resources/lang/en/';

$viTranslations = [];
$enTranslations = [];

echo "📊 LOADING TRANSLATION FILES WITH NESTED STRUCTURE:\n\n";

// Load VI translations
foreach (glob($viPath . '*.php') as $file) {
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
foreach (glob($enPath . '*.php') as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        $flattened = flattenArray($translations, $filename);
        $enTranslations = array_merge($enTranslations, $flattened);
        echo "✅ EN $filename: " . count($flattened) . " keys\n";
    }
}

echo "\n📈 TOTAL LOADED:\n";
echo "  VI: " . count($viTranslations) . " keys\n";
echo "  EN: " . count($enTranslations) . " keys\n\n";

// Analyze navigation keys specifically
echo "🧭 NAVIGATION KEYS ANALYSIS:\n\n";

$navKeys = [];
foreach ($viTranslations as $key => $value) {
    if (strpos($key, 'nav.') === 0 || strpos($key, 'navigation.') === 0) {
        $navKeys[$key] = $value;
    }
}

echo "Found " . count($navKeys) . " navigation-related keys:\n";
foreach ($navKeys as $key => $value) {
    $exists_en = isset($enTranslations[$key]) ? '✅' : '❌';
    echo "  $exists_en $key => '$value'\n";
}

echo "\n";

// Scan blade files for translation calls
echo "🔍 SCANNING BLADE FILES FOR TRANSLATION CALLS:\n\n";

$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        // Skip admin directory
        if (strpos($path, '/admin/') === false && strpos($path, '\\admin\\') === false) {
            $bladeFiles[] = $path;
        }
    }
}

$allUsedKeys = [];
$problemFiles = [];

// Extract translation keys from blade files
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
    $fileKeys = [];

    // Extract __('key') calls - improved regex
    preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
    foreach ($directMatches[1] as $key) {
        $allUsedKeys[] = $key;
        $fileKeys[] = $key;
    }

    // Extract @lang('key') calls
    preg_match_all('/@lang\([\'"]([^\'"]+)[\'"]\)/', $content, $langMatches);
    foreach ($langMatches[1] as $key) {
        $allUsedKeys[] = $key;
        $fileKeys[] = $key;
    }

    // Extract trans('key') calls
    preg_match_all('/trans\([\'"]([^\'"]+)[\'"]\)/', $content, $transMatches);
    foreach ($transMatches[1] as $key) {
        $allUsedKeys[] = $key;
        $fileKeys[] = $key;
    }

    // Check for navigation keys specifically
    $navKeysInFile = array_filter($fileKeys, function($key) {
        return strpos($key, 'nav.') === 0 || strpos($key, 'navigation.') === 0;
    });

    if (!empty($navKeysInFile)) {
        $problemFiles[$relativePath] = $navKeysInFile;
    }
}

echo "📁 Found " . count($bladeFiles) . " blade files\n";
echo "🗝️  Found " . count(array_unique($allUsedKeys)) . " unique translation keys\n\n";

// Analyze navigation keys in blade files
echo "🎯 NAVIGATION KEYS IN BLADE FILES:\n\n";

foreach ($problemFiles as $file => $keys) {
    echo "📄 $file:\n";
    foreach ($keys as $key) {
        $vi_exists = isset($viTranslations[$key]) ? '✅ VI' : '❌ VI';
        $en_exists = isset($enTranslations[$key]) ? '✅ EN' : '❌ EN';
        echo "  $vi_exists $en_exists $key\n";
    }
    echo "\n";
}

// Find missing navigation keys
echo "❌ MISSING NAVIGATION KEYS:\n\n";

$missingKeys = [];
foreach ($problemFiles as $file => $keys) {
    foreach ($keys as $key) {
        if (!isset($viTranslations[$key]) || !isset($enTranslations[$key])) {
            $missingKeys[] = $key;
        }
    }
}

$missingKeys = array_unique($missingKeys);
echo "Found " . count($missingKeys) . " missing navigation keys:\n";
foreach ($missingKeys as $key) {
    $vi_status = isset($viTranslations[$key]) ? '✅' : '❌';
    $en_status = isset($enTranslations[$key]) ? '✅' : '❌';
    echo "  $vi_status VI | $en_status EN | $key\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
if (empty($missingKeys)) {
    echo "✅ All navigation keys exist in translation files!\n";
    echo "🔧 Issue might be in scan script - need to fix nested array handling\n";
} else {
    echo "❌ Found missing navigation keys - need to add them to translation files\n";
    echo "📝 Also need to fix scan script for proper nested array handling\n";
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
