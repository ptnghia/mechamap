<?php
/**
 * Accurate Key Count
 * Đếm chính xác số keys trong Blade vs Lang directory
 */

echo "📊 ACCURATE TRANSLATION KEYS COUNT\n";
echo "==================================\n\n";

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

// Simple and effective patterns
$usedKeys = [];
$keysByFile = [];
$totalMatches = 0;

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace($basePath . '/', '', $file);
    $fileKeys = [];
    
    // Pattern 1: __('key') with single quotes
    if (preg_match_all("/__(\'([^\']+)\')/", $content, $matches)) {
        foreach ($matches[2] as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $usedKeys[$key] = ($usedKeys[$key] ?? 0) + 1;
                $fileKeys[$key] = ($fileKeys[$key] ?? 0) + 1;
                $totalMatches++;
            }
        }
    }
    
    // Pattern 2: __("key") with double quotes
    if (preg_match_all('/__\("([^"]+)"\)/', $content, $matches)) {
        foreach ($matches[1] as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $usedKeys[$key] = ($usedKeys[$key] ?? 0) + 1;
                $fileKeys[$key] = ($fileKeys[$key] ?? 0) + 1;
                $totalMatches++;
            }
        }
    }
    
    // Pattern 3: t_ui('key') helper functions
    $helperPatterns = [
        't_ui' => '/t_ui\([\'"]([^\'"]+)[\'"]\)/',
        't_core' => '/t_core\([\'"]([^\'"]+)[\'"]\)/',
        't_user' => '/t_user\([\'"]([^\'"]+)[\'"]\)/',
        't_admin' => '/t_admin\([\'"]([^\'"]+)[\'"]\)/',
        't_feature' => '/t_feature\([\'"]([^\'"]+)[\'"]\)/',
        't_content' => '/t_content\([\'"]([^\'"]+)[\'"]\)/',
    ];
    
    foreach ($helperPatterns as $prefix => $pattern) {
        if (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $key) {
                $fullKey = $prefix === 't_ui' ? "ui/$key" : 
                          ($prefix === 't_core' ? "core/$key" :
                          ($prefix === 't_user' ? "user/$key" :
                          ($prefix === 't_admin' ? "admin/$key" :
                          ($prefix === 't_feature' ? "features/$key" :
                          ($prefix === 't_content' ? "content/$key" : $key)))));
                
                $usedKeys[$fullKey] = ($usedKeys[$fullKey] ?? 0) + 1;
                $fileKeys[$fullKey] = ($fileKeys[$fullKey] ?? 0) + 1;
                $totalMatches++;
            }
        }
    }
    
    // Pattern 4: @lang('key') directive
    if (preg_match_all('/@lang\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
        foreach ($matches[1] as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $usedKeys[$key] = ($usedKeys[$key] ?? 0) + 1;
                $fileKeys[$key] = ($fileKeys[$key] ?? 0) + 1;
                $totalMatches++;
            }
        }
    }
    
    // Pattern 5: trans('key') function
    if (preg_match_all('/trans\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
        foreach ($matches[1] as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $usedKeys[$key] = ($usedKeys[$key] ?? 0) + 1;
                $fileKeys[$key] = ($fileKeys[$key] ?? 0) + 1;
                $totalMatches++;
            }
        }
    }
    
    if (!empty($fileKeys)) {
        $keysByFile[$relativePath] = $fileKeys;
    }
}

$totalUsedKeys = count($usedKeys);

echo "📈 BLADE TEMPLATES RESULTS:\n";
echo "===========================\n";
echo "Total unique keys found: $totalUsedKeys\n";
echo "Total key usages: $totalMatches\n";
echo "Files with translation keys: " . count($keysByFile) . "\n\n";

echo "🔍 PHASE 2: SCANNING LANG DIRECTORY\n";
echo "===================================\n";

// Count available keys in lang directory
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

$availableKeys = [];
$langPath = $basePath . '/resources/lang';
$languages = ['vi', 'en'];

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
                    }
                }
            } catch (Exception $e) {
                // Skip problematic files
            }
        }
    }
}

$totalAvailableKeys = count($availableKeys);

echo "📈 LANG DIRECTORY RESULTS:\n";
echo "==========================\n";
echo "Total available translation keys: $totalAvailableKeys\n\n";

echo "🔍 PHASE 3: COMPARISON ANALYSIS\n";
echo "===============================\n";

// Find matches and misses
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

// Find unused keys
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
if (!empty($missingKeys)) {
    arsort($missingKeys);
    $topMissing = array_slice($missingKeys, 0, 10, true);
    foreach ($topMissing as $key => $count) {
        echo "❌ $key (used $count times)\n";
    }
} else {
    echo "🎉 No missing keys! All used keys have translations.\n";
}

echo "\n📁 TOP 10 FILES WITH MOST KEYS:\n";
echo "===============================\n";
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

if ($utilizationPercent < 30) {
    echo "📝 Consider cleaning up unused translation keys (low utilization).\n";
} elseif ($utilizationPercent > 80) {
    echo "✅ Good utilization of available translation keys.\n";
}

if ($missingCount > 0) {
    echo "🔧 Priority: Add translations for the " . min(10, $missingCount) . " most frequently used missing keys.\n";
}

echo "\n📋 FINAL SUMMARY:\n";
echo "=================\n";
echo "Blade files scanned: " . count($bladeFiles) . "\n";
echo "Translation keys in use: $totalUsedKeys\n";
echo "Translation keys available: $totalAvailableKeys\n";
echo "Coverage rate: " . round($coveragePercent, 1) . "%\n";
echo "Utilization rate: " . round($utilizationPercent, 1) . "%\n";
echo "Overall health score: " . round(($coveragePercent + $utilizationPercent) / 2, 1) . "%\n\n";

if ($coveragePercent >= 80 && $utilizationPercent >= 20) {
    echo "🎉 TRANSLATION SYSTEM STATUS: HEALTHY\n";
} elseif ($coveragePercent >= 60) {
    echo "✅ TRANSLATION SYSTEM STATUS: GOOD\n";
} else {
    echo "⚠️  TRANSLATION SYSTEM STATUS: NEEDS IMPROVEMENT\n";
}

echo "\n🎯 KEY INSIGHTS:\n";
echo "================\n";
echo "• You have $totalAvailableKeys translation keys available\n";
echo "• Only $totalUsedKeys keys are actually being used in templates\n";
echo "• " . round($utilizationPercent, 1) . "% of available keys are being utilized\n";
echo "• " . round($coveragePercent, 1) . "% of used keys have translations\n";

if ($unusedCount > 1000) {
    echo "• Consider cleaning up " . number_format($unusedCount) . " unused keys\n";
}

if ($missingCount > 0) {
    echo "• Need to add $missingCount missing translations\n";
}
