<?php
/**
 * Analyze Direct Translation Keys
 * Phân tích chi tiết 2,767 direct translation keys để tìm patterns và tạo strategy sửa
 */

echo "🔍 ANALYZING DIRECT TRANSLATION KEYS\n";
echo "====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/comprehensive_blade_audit.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found. Please run comprehensive_blade_audit.php first.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

echo "📊 DIRECT TRANSLATION KEYS OVERVIEW\n";
echo "===================================\n";

$directKeys = $analysis['patterns']['direct_underscore']['keys'] ?? [];
echo "Total direct __() keys found: " . count($directKeys) . "\n\n";

// Analyze key patterns
$patterns = [
    'dot_notation' => [],      // ui.common.loading
    'slash_notation' => [],    // ui/common/loading  
    'simple_keys' => [],       // loading, cancel
    'mixed_notation' => [],    // other patterns
];

$categoryStats = [];

foreach ($directKeys as $key => $files) {
    // Categorize by pattern
    if (strpos($key, '/') !== false) {
        $patterns['slash_notation'][$key] = $files;
    } elseif (strpos($key, '.') !== false) {
        $patterns['dot_notation'][$key] = $files;
    } elseif (strlen($key) < 20 && !strpos($key, '.') && !strpos($key, '/')) {
        $patterns['simple_keys'][$key] = $files;
    } else {
        $patterns['mixed_notation'][$key] = $files;
    }
    
    // Categorize by prefix
    $prefix = 'other';
    if (strpos($key, 'ui.') === 0 || strpos($key, 'ui/') === 0) {
        $prefix = 'ui';
    } elseif (strpos($key, 'core.') === 0 || strpos($key, 'core/') === 0) {
        $prefix = 'core';
    } elseif (strpos($key, 'content.') === 0 || strpos($key, 'content/') === 0) {
        $prefix = 'content';
    } elseif (strpos($key, 'features.') === 0 || strpos($key, 'features/') === 0) {
        $prefix = 'features';
    } elseif (strpos($key, 'user.') === 0 || strpos($key, 'user/') === 0) {
        $prefix = 'user';
    } elseif (strpos($key, 'admin.') === 0 || strpos($key, 'admin/') === 0) {
        $prefix = 'admin';
    } elseif (strpos($key, 'forum.') === 0 || strpos($key, 'forum/') === 0) {
        $prefix = 'forum';
    } elseif (strpos($key, 'auth.') === 0 || strpos($key, 'auth/') === 0) {
        $prefix = 'auth';
    } elseif (strpos($key, 'common.') === 0 || strpos($key, 'common/') === 0) {
        $prefix = 'common';
    }
    
    if (!isset($categoryStats[$prefix])) {
        $categoryStats[$prefix] = 0;
    }
    $categoryStats[$prefix]++;
}

echo "📋 PATTERN ANALYSIS\n";
echo "===================\n";
foreach ($patterns as $patternName => $keys) {
    echo "🔸 " . ucfirst(str_replace('_', ' ', $patternName)) . ": " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 5);
    foreach ($examples as $example) {
        echo "   - $example\n";
    }
    if (count($keys) > 5) {
        echo "   - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

echo "📊 CATEGORY BREAKDOWN\n";
echo "=====================\n";
arsort($categoryStats);
foreach ($categoryStats as $category => $count) {
    echo "🔸 $category: $count keys (" . round(($count / count($directKeys)) * 100, 1) . "%)\n";
}

echo "\n🎯 PRIORITY ANALYSIS\n";
echo "====================\n";

// Find most used keys (appear in multiple files)
$mostUsedKeys = [];
foreach ($directKeys as $key => $files) {
    if (count($files) > 1) {
        $mostUsedKeys[$key] = count($files);
    }
}
arsort($mostUsedKeys);

echo "📈 Most used keys (appear in multiple files):\n";
$count = 0;
foreach ($mostUsedKeys as $key => $fileCount) {
    $count++;
    if ($count > 10) break;
    echo "   $count. $key (used in $fileCount files)\n";
}

// Test sample keys to see current status
echo "\n🧪 TESTING SAMPLE KEYS\n";
echo "======================\n";

$sampleKeys = array_slice(array_keys($directKeys), 0, 20);
$workingCount = 0;

foreach ($sampleKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nSample success rate: " . round(($workingCount / count($sampleKeys)) * 100, 1) . "%\n";

// Generate recommendations
echo "\n💡 RECOMMENDATIONS\n";
echo "==================\n";

echo "🔥 HIGH PRIORITY (Quick wins):\n";
echo "1. Focus on 'ui.*' keys (" . ($categoryStats['ui'] ?? 0) . " keys) - most visible to users\n";
echo "2. Fix most used keys first (appear in multiple files)\n";
echo "3. Convert dot notation to proper file structure\n\n";

echo "🟡 MEDIUM PRIORITY:\n";
echo "1. 'forum.*' keys (" . ($categoryStats['forum'] ?? 0) . " keys) - core functionality\n";
echo "2. 'auth.*' keys (" . ($categoryStats['auth'] ?? 0) . " keys) - user experience\n";
echo "3. 'common.*' keys (" . ($categoryStats['common'] ?? 0) . " keys) - shared components\n\n";

echo "🟢 LOW PRIORITY:\n";
echo "1. 'other' category (" . ($categoryStats['other'] ?? 0) . " keys) - review individually\n";
echo "2. Single-use keys - fix as needed\n\n";

// Save detailed analysis
$outputFile = $basePath . '/storage/localization/direct_keys_analysis.json';
$analysisData = [
    'analysis_date' => date('Y-m-d H:i:s'),
    'total_keys' => count($directKeys),
    'patterns' => array_map('count', $patterns),
    'categories' => $categoryStats,
    'most_used_keys' => array_slice($mostUsedKeys, 0, 50, true),
    'sample_test_results' => [
        'tested' => count($sampleKeys),
        'working' => $workingCount,
        'success_rate' => round(($workingCount / count($sampleKeys)) * 100, 1)
    ],
    'detailed_patterns' => $patterns
];

file_put_contents($outputFile, json_encode($analysisData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "💾 Detailed analysis saved to: storage/localization/direct_keys_analysis.json\n\n";

echo "🚀 NEXT STEPS\n";
echo "=============\n";
echo "1. Start with UI keys (highest visibility)\n";
echo "2. Create missing translation files for dot notation keys\n";
echo "3. Test and validate fixes incrementally\n";
echo "4. Focus on most-used keys for maximum impact\n";
echo "5. Consider converting some keys to helper functions\n\n";

echo "🎯 SUGGESTED APPROACH\n";
echo "=====================\n";
echo "Phase 1: Fix top 100 most-used keys (80/20 rule)\n";
echo "Phase 2: Fix all UI keys (user-facing)\n";
echo "Phase 3: Fix core functionality keys\n";
echo "Phase 4: Clean up remaining keys\n";
