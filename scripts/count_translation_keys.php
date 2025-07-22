<?php

/**
 * Count and compare translation keys between Vietnamese and English
 * 
 * This script analyzes all translation files in both languages
 * and provides detailed statistics about translation coverage.
 */

function countKeysInFile($filePath) {
    if (!file_exists($filePath)) {
        return 0;
    }
    
    try {
        $content = include $filePath;
        if (!is_array($content)) {
            return 0;
        }
        
        return countArrayKeys($content);
    } catch (Exception $e) {
        echo "Error reading file $filePath: " . $e->getMessage() . "\n";
        return 0;
    }
}

function countArrayKeys($array, $prefix = '') {
    $count = 0;
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            // Recursive count for nested arrays
            $count += countArrayKeys($value, $prefix . $key . '.');
        } else {
            $count++;
        }
    }
    
    return $count;
}

function scanLanguageDirectory($langPath) {
    $stats = [
        'total_keys' => 0,
        'files' => [],
        'categories' => []
    ];
    
    if (!is_dir($langPath)) {
        return $stats;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($langPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $relativePath = str_replace($langPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);
            
            $keyCount = countKeysInFile($file->getPathname());
            $stats['files'][$relativePath] = $keyCount;
            $stats['total_keys'] += $keyCount;
            
            // Categorize by directory
            $category = dirname($relativePath);
            if ($category === '.') {
                $category = 'root';
            }
            
            if (!isset($stats['categories'][$category])) {
                $stats['categories'][$category] = 0;
            }
            $stats['categories'][$category] += $keyCount;
        }
    }
    
    return $stats;
}

function displayStats($language, $stats) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 TRANSLATION STATISTICS - " . strtoupper($language) . "\n";
    echo str_repeat("=", 60) . "\n";
    
    echo "🔢 Total Keys: " . number_format($stats['total_keys']) . "\n";
    echo "📁 Total Files: " . count($stats['files']) . "\n";
    echo "📂 Categories: " . count($stats['categories']) . "\n\n";
    
    // Sort categories by key count (descending)
    arsort($stats['categories']);
    
    echo "📂 BREAKDOWN BY CATEGORY:\n";
    echo str_repeat("-", 40) . "\n";
    foreach ($stats['categories'] as $category => $count) {
        $percentage = ($count / $stats['total_keys']) * 100;
        printf("%-20s: %6d keys (%5.1f%%)\n", $category, $count, $percentage);
    }
    
    echo "\n📄 TOP 10 FILES BY KEY COUNT:\n";
    echo str_repeat("-", 40) . "\n";
    
    // Sort files by key count (descending)
    arsort($stats['files']);
    $topFiles = array_slice($stats['files'], 0, 10, true);
    
    foreach ($topFiles as $file => $count) {
        printf("%-30s: %6d keys\n", $file, $count);
    }
}

function compareLanguages($viStats, $enStats) {
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🔍 LANGUAGE COMPARISON ANALYSIS\n";
    echo str_repeat("=", 60) . "\n";
    
    $viTotal = $viStats['total_keys'];
    $enTotal = $enStats['total_keys'];
    
    echo "📊 OVERALL STATISTICS:\n";
    echo str_repeat("-", 40) . "\n";
    printf("Vietnamese (vi): %8d keys\n", $viTotal);
    printf("English (en):    %8d keys\n", $enTotal);
    printf("Difference:      %8d keys\n", abs($viTotal - $enTotal));
    
    if ($enTotal > 0) {
        $coverage = ($viTotal / $enTotal) * 100;
        printf("VI Coverage:     %8.1f%%\n", $coverage);
    }
    
    echo "\n📂 CATEGORY COMPARISON:\n";
    echo str_repeat("-", 60) . "\n";
    printf("%-20s %10s %10s %10s\n", "Category", "VI Keys", "EN Keys", "Coverage");
    echo str_repeat("-", 60) . "\n";
    
    $allCategories = array_unique(array_merge(
        array_keys($viStats['categories']),
        array_keys($enStats['categories'])
    ));
    
    sort($allCategories);
    
    foreach ($allCategories as $category) {
        $viCount = $viStats['categories'][$category] ?? 0;
        $enCount = $enStats['categories'][$category] ?? 0;
        
        $coverage = $enCount > 0 ? ($viCount / $enCount) * 100 : 0;
        
        printf("%-20s %10d %10d %9.1f%%\n", 
            $category, $viCount, $enCount, $coverage);
    }
    
    echo "\n🎯 MISSING TRANSLATIONS:\n";
    echo str_repeat("-", 40) . "\n";
    
    $missingInVi = [];
    $extraInVi = [];
    
    foreach ($enStats['files'] as $file => $enCount) {
        $viCount = $viStats['files'][$file] ?? 0;
        if ($viCount < $enCount) {
            $missingInVi[$file] = $enCount - $viCount;
        }
    }
    
    foreach ($viStats['files'] as $file => $viCount) {
        $enCount = $enStats['files'][$file] ?? 0;
        if ($viCount > $enCount) {
            $extraInVi[$file] = $viCount - $enCount;
        }
    }
    
    if (!empty($missingInVi)) {
        echo "❌ Files with missing VI translations:\n";
        arsort($missingInVi);
        foreach (array_slice($missingInVi, 0, 10, true) as $file => $missing) {
            printf("   %-30s: %d missing keys\n", $file, $missing);
        }
    }
    
    if (!empty($extraInVi)) {
        echo "\n✅ Files with extra VI translations:\n";
        arsort($extraInVi);
        foreach (array_slice($extraInVi, 0, 5, true) as $file => $extra) {
            printf("   %-30s: %d extra keys\n", $file, $extra);
        }
    }
}

// Main execution
echo "🔍 MECHAMAP TRANSLATION KEY ANALYSIS\n";
echo "====================================\n";
echo "Analyzing translation files...\n";

$basePath = dirname(__DIR__) . '/resources/lang';
$viPath = $basePath . '/vi';
$enPath = $basePath . '/en';

echo "📁 Vietnamese path: $viPath\n";
echo "📁 English path: $enPath\n";

// Scan both language directories
$viStats = scanLanguageDirectory($viPath);
$enStats = scanLanguageDirectory($enPath);

// Display individual statistics
displayStats('Vietnamese', $viStats);
displayStats('English', $enStats);

// Compare languages
compareLanguages($viStats, $enStats);

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ ANALYSIS COMPLETE\n";
echo str_repeat("=", 60) . "\n";

// Summary
$viTotal = $viStats['total_keys'];
$enTotal = $enStats['total_keys'];
$coverage = $enTotal > 0 ? ($viTotal / $enTotal) * 100 : 0;

echo "\n🎯 SUMMARY:\n";
echo "- Vietnamese: " . number_format($viTotal) . " keys\n";
echo "- English: " . number_format($enTotal) . " keys\n";
echo "- Coverage: " . number_format($coverage, 1) . "%\n";
echo "- Status: " . ($coverage >= 90 ? "✅ Excellent" : ($coverage >= 70 ? "⚠️ Good" : "❌ Needs Work")) . "\n";

?>
