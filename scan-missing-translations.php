<?php

/**
 * SCAN MISSING TRANSLATIONS
 * Quét lại các files blade để tìm keys còn chưa có bản dịch (bỏ qua /admin)
 */

echo "=== SCANNING FOR MISSING TRANSLATIONS (EXCLUDING ADMIN) ===\n\n";

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

// Load all VI translation files with nested structure support
foreach (glob($viPath . '*.php') as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        $flattened = flattenArray($translations, $filename);
        $viTranslations = array_merge($viTranslations, $flattened);
    }
}

// Load all EN translation files with nested structure support
foreach (glob($enPath . '*.php') as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        $flattened = flattenArray($translations, $filename);
        $enTranslations = array_merge($enTranslations, $flattened);
    }
}

echo "📊 Loaded translations:\n";
echo "  VI: " . count($viTranslations) . " keys\n";
echo "  EN: " . count($enTranslations) . " keys\n\n";

// Scan blade files for translation calls (excluding admin)
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        // Skip admin directory (both Unix and Windows path separators)
        if (strpos($path, '/admin/') === false && strpos($path, '\\admin\\') === false) {
            $bladeFiles[] = $path;
        }
    }
}

echo "📁 Found " . count($bladeFiles) . " blade files to scan (excluding admin)\n\n";

$allUsedKeys = [];
$fileKeyMap = [];

// Extract translation keys from blade files
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(__DIR__ . '/resources/views/', '', $file);
    $fileKeys = [];

    // Extract __('key') calls
    preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
    foreach ($directMatches[1] as $key) {
        $allUsedKeys[] = $key;
        $fileKeys[] = $key;
    }

    // Extract t_helper('key') calls
    preg_match_all('/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $helperMatches);
    foreach ($helperMatches[1] as $i => $helper) {
        $key = $helperMatches[1][$i] . '.' . $helperMatches[2][$i];
        $allUsedKeys[] = $key;
        $fileKeys[] = $key;
    }

    // Extract @section('title', __('key')) calls
    preg_match_all('/@section\([\'"]title[\'"],\s*__\([\'"]([^\'"]+)[\'"]\)/', $content, $titleMatches);
    foreach ($titleMatches[1] as $key) {
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

    if (!empty($fileKeys)) {
        $fileKeyMap[$relativePath] = array_unique($fileKeys);
    }
}

$allUsedKeys = array_unique($allUsedKeys);
echo "🔑 Found " . count($allUsedKeys) . " unique translation keys in blade files\n\n";

// Analyze missing translations
$missingBoth = [];
$missingVI = [];
$missingEN = [];
$hasTranslations = [];

foreach ($allUsedKeys as $key) {
    $hasVI = isset($viTranslations[$key]);
    $hasEN = isset($enTranslations[$key]);

    if ($hasVI && $hasEN) {
        $hasTranslations[] = $key;
    } elseif (!$hasVI && !$hasEN) {
        $missingBoth[] = $key;
    } elseif (!$hasVI) {
        $missingVI[] = $key;
    } elseif (!$hasEN) {
        $missingEN[] = $key;
    }
}

echo "📈 TRANSLATION STATUS SUMMARY:\n";
echo "✅ Has both languages: " . count($hasTranslations) . " (" . round(count($hasTranslations)/count($allUsedKeys)*100, 1) . "%)\n";
echo "❌ Missing both languages: " . count($missingBoth) . " (" . round(count($missingBoth)/count($allUsedKeys)*100, 1) . "%)\n";
echo "🇻🇳 Missing Vietnamese only: " . count($missingVI) . " (" . round(count($missingVI)/count($allUsedKeys)*100, 1) . "%)\n";
echo "🇺🇸 Missing English only: " . count($missingEN) . " (" . round(count($missingEN)/count($allUsedKeys)*100, 1) . "%)\n\n";

// Find files with most missing translations
$fileMissingCount = [];
foreach ($fileKeyMap as $file => $keys) {
    $missingCount = 0;
    foreach ($keys as $key) {
        if (!isset($viTranslations[$key]) || !isset($enTranslations[$key])) {
            $missingCount++;
        }
    }
    if ($missingCount > 0) {
        $fileMissingCount[$file] = [
            'total' => count($keys),
            'missing' => $missingCount,
            'percentage' => round($missingCount / count($keys) * 100, 1)
        ];
    }
}

// Sort by missing count
arsort($fileMissingCount);

echo "🔥 TOP 15 FILES WITH MOST MISSING TRANSLATIONS:\n\n";
$count = 0;
foreach ($fileMissingCount as $file => $stats) {
    if ($count >= 15) break;

    echo sprintf("📄 %s\n", $file);
    echo sprintf("   Total keys: %d | Missing: %d (%.1f%%)\n",
        $stats['total'], $stats['missing'], $stats['percentage']);

    // Show some missing keys for this file
    $fileMissingKeys = [];
    foreach ($fileKeyMap[$file] as $key) {
        if (!isset($viTranslations[$key]) || !isset($enTranslations[$key])) {
            $fileMissingKeys[] = $key;
        }
    }

    echo "   Missing keys: " . implode(', ', array_slice($fileMissingKeys, 0, 5));
    if (count($fileMissingKeys) > 5) {
        echo " ... and " . (count($fileMissingKeys) - 5) . " more";
    }
    echo "\n\n";

    $count++;
}

// Show most common missing keys
echo "🎯 TOP 20 MOST COMMON MISSING KEYS:\n\n";

$keyMissingCount = [];
foreach ($missingBoth as $key) {
    $keyMissingCount[$key] = ($keyMissingCount[$key] ?? 0) + 1;
}
foreach ($missingVI as $key) {
    $keyMissingCount[$key] = ($keyMissingCount[$key] ?? 0) + 1;
}
foreach ($missingEN as $key) {
    $keyMissingCount[$key] = ($keyMissingCount[$key] ?? 0) + 1;
}

arsort($keyMissingCount);
$topMissingKeys = array_slice($keyMissingCount, 0, 20, true);

foreach ($topMissingKeys as $key => $count) {
    $status = '';
    if (in_array($key, $missingBoth)) {
        $status = '❌ Both missing';
    } elseif (in_array($key, $missingVI)) {
        $status = '🇻🇳 VI missing';
    } elseif (in_array($key, $missingEN)) {
        $status = '🇺🇸 EN missing';
    }

    echo sprintf("🔑 %-50s %s\n", $key, $status);
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Focus on files with highest missing percentages\n";
echo "2. Prioritize keys that appear in multiple files\n";
echo "3. Create missing translation files for new categories\n";
echo "4. Add common keys to appropriate translation files\n";

echo "\n✅ Missing translations scan completed at " . date('Y-m-d H:i:s') . "\n";
?>
