<?php
/**
 * Count Keys Comparison
 * So sánh tổng số keys trong Blade templates vs keys trong thư mục lang
 */

echo "📊 TRANSLATION KEYS COMPARISON ANALYSIS\n";
echo "=======================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 PHASE 1: SCANNING BLADE TEMPLATES\n";
echo "====================================\n";

// Find all Blade files
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($basePath . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "Found " . count($bladeFiles) . " Blade template files\n\n";

// Patterns to find translation keys in Blade files
$translationPatterns = [
    // Standard Laravel __() function - improved pattern
    'standard_trans' => '/__(\'([^\']+)\'|"([^"]+)")/m',

    // Helper functions
    't_ui' => '/t_ui\([\'"]([^\'"]+)[\'"]\)/m',
    't_core' => '/t_core\([\'"]([^\'"]+)[\'"]\)/m',
    't_user' => '/t_user\([\'"]([^\'"]+)[\'"]\)/m',
    't_admin' => '/t_admin\([\'"]([^\'"]+)[\'"]\)/m',
    't_feature' => '/t_feature\([\'"]([^\'"]+)[\'"]\)/m',
    't_content' => '/t_content\([\'"]([^\'"]+)[\'"]\)/m',

    // Blade directives
    'blade_lang' => '/@lang\([\'"]([^\'"]+)[\'"]\)/m',
    'blade_ui' => '/@ui\([\'"]([^\'"]+)[\'"]\)/m',
    'blade_core' => '/@core\([\'"]([^\'"]+)[\'"]\)/m',

    // Trans function
    'trans_func' => '/trans\([\'"]([^\'"]+)[\'"]\)/m',
    'trans_choice' => '/trans_choice\([\'"]([^\'"]+)[\'"]\)/m',

    // Custom trans_key helper
    'trans_key' => '/trans_key\([\'"]([^\'"]+)[\'"]\)/m'
];

$usedKeys = [];
$keysByPattern = [];
$keysByFile = [];

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($basePath . '/', '', $file);
    $fileKeys = [];

    foreach ($translationPatterns as $patternName => $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Extract the key from the match
                $key = '';
                if ($patternName === 'standard_trans') {
                    // For __('key') or __("key")
                    $key = $match[1] ?? $match[2] ?? '';
                } else {
                    // For other patterns, key is in group 1
                    $key = $match[1] ?? '';
                }

                $key = trim($key, '\'"');
                if (!empty($key) && !is_numeric($key) && strlen($key) > 1) {
                    $usedKeys[$key] = ($usedKeys[$key] ?? 0) + 1;
                    $keysByPattern[$patternName][$key] = ($keysByPattern[$patternName][$key] ?? 0) + 1;
                    $fileKeys[$key] = ($fileKeys[$key] ?? 0) + 1;
                }
            }
        }
    }

    if (!empty($fileKeys)) {
        $keysByFile[$relativePath] = $fileKeys;
    }
}

$totalUsedKeys = count($usedKeys);
$totalUsages = array_sum($usedKeys);

echo "📈 BLADE TEMPLATES ANALYSIS:\n";
echo "============================\n";
echo "Total unique keys found: $totalUsedKeys\n";
echo "Total key usages: $totalUsages\n";
echo "Files with translation keys: " . count($keysByFile) . "\n\n";

echo "🔧 KEYS BY PATTERN TYPE:\n";
echo "========================\n";
foreach ($keysByPattern as $pattern => $keys) {
    echo sprintf("%-20s: %d unique keys (%d usages)\n",
        $pattern,
        count($keys),
        array_sum($keys)
    );
}

echo "\n🔍 PHASE 2: SCANNING LANG DIRECTORY\n";
echo "===================================\n";

// Scan lang directory
$langPath = $basePath . '/resources/lang';
$availableKeys = [];
$keysByLangFile = [];
$languages = ['vi', 'en'];

function countNestedKeys($array, $prefix = '') {
    $keys = [];
    foreach ($array as $key => $value) {
        $fullKey = $prefix ? "$prefix.$key" : $key;
        if (is_array($value)) {
            $keys = array_merge($keys, countNestedKeys($value, $fullKey));
        } else {
            $keys[] = $fullKey;
        }
    }
    return $keys;
}

foreach ($languages as $lang) {
    $langDir = $langPath . '/' . $lang;
    if (!is_dir($langDir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($langDir)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace($langDir . '/', '', $file->getPathname());
            $category = str_replace('.php', '', $relativePath);

            try {
                $translations = include $file->getPathname();
                if (is_array($translations)) {
                    $fileKeys = countNestedKeys($translations, $category);
                    foreach ($fileKeys as $key) {
                        $availableKeys[$key] = true;
                        $keysByLangFile[$lang][$category][] = $key;
                    }
                }
            } catch (Exception $e) {
                echo "Error reading $relativePath: " . $e->getMessage() . "\n";
            }
        }
    }
}

$totalAvailableKeys = count($availableKeys);

echo "📈 LANG DIRECTORY ANALYSIS:\n";
echo "===========================\n";
echo "Total available translation keys: $totalAvailableKeys\n";

foreach ($languages as $lang) {
    if (isset($keysByLangFile[$lang])) {
        $langTotal = 0;
        foreach ($keysByLangFile[$lang] as $category => $keys) {
            $langTotal += count($keys);
        }
        echo "Keys in $lang: $langTotal\n";
    }
}

echo "\n📊 DETAILED BREAKDOWN BY CATEGORY:\n";
echo "==================================\n";

$categoryStats = [];
foreach ($keysByLangFile as $lang => $categories) {
    foreach ($categories as $category => $keys) {
        $categoryStats[$category][$lang] = count($keys);
    }
}

foreach ($categoryStats as $category => $langCounts) {
    echo sprintf("%-20s: ", $category);
    foreach ($languages as $lang) {
        $count = $langCounts[$lang] ?? 0;
        echo "$lang($count) ";
    }
    echo "\n";
}

echo "\n🔍 PHASE 3: COMPARISON ANALYSIS\n";
echo "===============================\n";

// Find used keys that exist in lang files
$foundKeys = [];
$missingKeys = [];

foreach ($usedKeys as $key => $usageCount) {
    if (isset($availableKeys[$key])) {
        $foundKeys[$key] = $usageCount;
    } else {
        $missingKeys[$key] = $usageCount;
    }
}

$foundCount = count($foundKeys);
$missingCount = count($missingKeys);
$coveragePercent = $totalUsedKeys > 0 ? ($foundCount / $totalUsedKeys) * 100 : 0;

echo "📊 COVERAGE ANALYSIS:\n";
echo "====================\n";
echo "Keys used in Blade templates: $totalUsedKeys\n";
echo "Keys available in lang files: $totalAvailableKeys\n";
echo "Keys found (working): $foundCount\n";
echo "Keys missing: $missingCount\n";
echo "Coverage percentage: " . round($coveragePercent, 1) . "%\n\n";

// Find unused keys (available but not used)
$unusedKeys = [];
foreach ($availableKeys as $key => $true) {
    if (!isset($usedKeys[$key])) {
        $unusedKeys[] = $key;
    }
}

$unusedCount = count($unusedKeys);
$utilizationPercent = $totalAvailableKeys > 0 ? (($totalAvailableKeys - $unusedCount) / $totalAvailableKeys) * 100 : 0;

echo "📈 UTILIZATION ANALYSIS:\n";
echo "========================\n";
echo "Available keys: $totalAvailableKeys\n";
echo "Used keys: " . ($totalAvailableKeys - $unusedCount) . "\n";
echo "Unused keys: $unusedCount\n";
echo "Utilization percentage: " . round($utilizationPercent, 1) . "%\n\n";

echo "🔥 TOP 10 MOST USED KEYS:\n";
echo "=========================\n";
arsort($usedKeys);
$topKeys = array_slice($usedKeys, 0, 10, true);
foreach ($topKeys as $key => $count) {
    $status = isset($availableKeys[$key]) ? "✅" : "❌";
    echo "$status $key (used $count times)\n";
}

echo "\n❌ TOP 10 MISSING KEYS:\n";
echo "=======================\n";
arsort($missingKeys);
$topMissing = array_slice($missingKeys, 0, 10, true);
foreach ($topMissing as $key => $count) {
    echo "❌ $key (used $count times)\n";
}

echo "\n📁 FILES WITH MOST TRANSLATION KEYS:\n";
echo "====================================\n";
$fileKeysCounts = [];
foreach ($keysByFile as $file => $keys) {
    $fileKeysCounts[$file] = count($keys);
}
arsort($fileKeysCounts);
$topFiles = array_slice($fileKeysCounts, 0, 10, true);
foreach ($topFiles as $file => $count) {
    echo "📄 $file ($count unique keys)\n";
}

echo "\n💡 RECOMMENDATIONS:\n";
echo "===================\n";

if ($coveragePercent >= 90) {
    echo "🎉 EXCELLENT: Translation coverage is very high!\n";
} elseif ($coveragePercent >= 70) {
    echo "✅ GOOD: Translation coverage is acceptable, minor improvements needed.\n";
} elseif ($coveragePercent >= 50) {
    echo "⚠️  MODERATE: Significant translation gaps exist.\n";
} else {
    echo "❌ POOR: Major translation work needed.\n";
}

if ($utilizationPercent < 50) {
    echo "📝 Consider cleaning up unused translation keys.\n";
}

if ($missingCount > 0) {
    echo "🔧 Priority: Add translations for the " . min(10, $missingCount) . " most frequently used missing keys.\n";
}

echo "\n📋 SUMMARY STATISTICS:\n";
echo "======================\n";
echo "Blade files scanned: " . count($bladeFiles) . "\n";
echo "Lang files scanned: " . count(array_keys($availableKeys)) . "\n";
echo "Translation patterns used: " . count($translationPatterns) . "\n";
echo "Coverage rate: " . round($coveragePercent, 1) . "%\n";
echo "Utilization rate: " . round($utilizationPercent, 1) . "%\n";
echo "Overall health score: " . round(($coveragePercent + $utilizationPercent) / 2, 1) . "%\n";
