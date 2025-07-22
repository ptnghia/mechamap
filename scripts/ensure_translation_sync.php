<?php

/**
 * Ensure Perfect Translation Synchronization
 * 
 * This script ensures 100% synchronization between Vietnamese and English
 * translation keys and provides detailed analysis.
 */

echo "🔄 ENSURING PERFECT TRANSLATION SYNCHRONIZATION\n";
echo "==============================================\n\n";

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

function analyzeFileSync($viFile, $enFile, $relativePath) {
    $viData = loadTranslationFile($viFile);
    $enData = loadTranslationFile($enFile);
    
    $viFlat = flattenArray($viData);
    $enFlat = flattenArray($enData);
    
    $missingInEn = array_diff_key($viFlat, $enFlat);
    $missingInVi = array_diff_key($enFlat, $viFlat);
    
    $result = [
        'file' => $relativePath,
        'vi_keys' => count($viFlat),
        'en_keys' => count($enFlat),
        'missing_in_en' => $missingInEn,
        'missing_in_vi' => $missingInVi,
        'is_synced' => empty($missingInEn) && empty($missingInVi),
        'coverage' => count($enFlat) > 0 ? (count($viFlat) / count($enFlat)) * 100 : 0
    ];
    
    return $result;
}

function generateSyncReport($basePath) {
    $viPath = $basePath . '/resources/lang/vi';
    $enPath = $basePath . '/resources/lang/en';
    
    $report = [
        'total_files' => 0,
        'synced_files' => 0,
        'files_with_issues' => [],
        'total_vi_keys' => 0,
        'total_en_keys' => 0,
        'categories' => []
    ];
    
    if (!is_dir($viPath) || !is_dir($enPath)) {
        echo "❌ Language directories not found\n";
        return $report;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $viFile) {
        if ($viFile->getExtension() === 'php') {
            $relativePath = str_replace($viPath . DIRECTORY_SEPARATOR, '', $viFile->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);
            
            $enFile = $enPath . '/' . $relativePath;
            
            $analysis = analyzeFileSync($viFile->getPathname(), $enFile, $relativePath);
            
            $report['total_files']++;
            $report['total_vi_keys'] += $analysis['vi_keys'];
            $report['total_en_keys'] += $analysis['en_keys'];
            
            if ($analysis['is_synced']) {
                $report['synced_files']++;
            } else {
                $report['files_with_issues'][] = $analysis;
            }
            
            // Categorize
            $category = dirname($relativePath);
            if ($category === '.') {
                $category = 'root';
            }
            
            if (!isset($report['categories'][$category])) {
                $report['categories'][$category] = [
                    'files' => 0,
                    'synced' => 0,
                    'vi_keys' => 0,
                    'en_keys' => 0
                ];
            }
            
            $report['categories'][$category]['files']++;
            $report['categories'][$category]['vi_keys'] += $analysis['vi_keys'];
            $report['categories'][$category]['en_keys'] += $analysis['en_keys'];
            
            if ($analysis['is_synced']) {
                $report['categories'][$category]['synced']++;
            }
        }
    }
    
    return $report;
}

// Generate comprehensive sync report
$basePath = dirname(__DIR__);
$report = generateSyncReport($basePath);

echo "📊 SYNCHRONIZATION ANALYSIS RESULTS\n";
echo "===================================\n\n";

echo "🔢 OVERALL STATISTICS:\n";
echo "----------------------\n";
printf("Total files: %d\n", $report['total_files']);
printf("Synced files: %d\n", $report['synced_files']);
printf("Files with issues: %d\n", count($report['files_with_issues']));
printf("Sync rate: %.1f%%\n", ($report['synced_files'] / $report['total_files']) * 100);
printf("Vietnamese keys: %s\n", number_format($report['total_vi_keys']));
printf("English keys: %s\n", number_format($report['total_en_keys']));
printf("Overall coverage: %.1f%%\n", ($report['total_vi_keys'] / $report['total_en_keys']) * 100);

echo "\n📂 CATEGORY BREAKDOWN:\n";
echo "----------------------\n";
printf("%-15s %8s %8s %8s %8s %10s\n", "Category", "Files", "Synced", "VI Keys", "EN Keys", "Coverage");
echo str_repeat("-", 70) . "\n";

foreach ($report['categories'] as $category => $data) {
    $coverage = $data['en_keys'] > 0 ? ($data['vi_keys'] / $data['en_keys']) * 100 : 0;
    $syncRate = ($data['synced'] / $data['files']) * 100;
    
    printf("%-15s %8d %8d %8d %8d %9.1f%%\n", 
        $category, 
        $data['files'], 
        $data['synced'], 
        $data['vi_keys'], 
        $data['en_keys'], 
        $coverage
    );
}

if (!empty($report['files_with_issues'])) {
    echo "\n❌ FILES WITH SYNCHRONIZATION ISSUES:\n";
    echo "=====================================\n";
    
    foreach ($report['files_with_issues'] as $issue) {
        echo "\n📄 File: {$issue['file']}\n";
        printf("   VI Keys: %d | EN Keys: %d | Coverage: %.1f%%\n", 
            $issue['vi_keys'], 
            $issue['en_keys'], 
            $issue['coverage']
        );
        
        if (!empty($issue['missing_in_en'])) {
            echo "   ❌ Missing in English (" . count($issue['missing_in_en']) . " keys):\n";
            foreach (array_slice($issue['missing_in_en'], 0, 5, true) as $key => $value) {
                echo "      - '$key' => '$value'\n";
            }
            if (count($issue['missing_in_en']) > 5) {
                echo "      ... and " . (count($issue['missing_in_en']) - 5) . " more\n";
            }
        }
        
        if (!empty($issue['missing_in_vi'])) {
            echo "   ❌ Missing in Vietnamese (" . count($issue['missing_in_vi']) . " keys):\n";
            foreach (array_slice($issue['missing_in_vi'], 0, 5, true) as $key => $value) {
                echo "      - '$key' => '$value'\n";
            }
            if (count($issue['missing_in_vi']) > 5) {
                echo "      ... and " . (count($issue['missing_in_vi']) - 5) . " more\n";
            }
        }
    }
} else {
    echo "\n✅ PERFECT SYNCHRONIZATION!\n";
    echo "===========================\n";
    echo "🎉 All translation files are perfectly synchronized!\n";
    echo "🌟 Vietnamese and English have identical key structures!\n";
    echo "🏆 100% coverage achieved across all categories!\n";
}

echo "\n🎯 SYNCHRONIZATION STATUS:\n";
echo "==========================\n";

$syncRate = ($report['synced_files'] / $report['total_files']) * 100;
$overallCoverage = ($report['total_vi_keys'] / $report['total_en_keys']) * 100;

if ($syncRate == 100 && abs($overallCoverage - 100) < 0.1) {
    echo "🏆 STATUS: PERFECT SYNCHRONIZATION\n";
    echo "✅ All files synchronized\n";
    echo "✅ 100% key coverage\n";
    echo "✅ Production ready\n";
} elseif ($syncRate >= 95 && $overallCoverage >= 95) {
    echo "🌟 STATUS: EXCELLENT SYNCHRONIZATION\n";
    echo "✅ Nearly perfect sync\n";
    echo "⚠️ Minor issues to fix\n";
} elseif ($syncRate >= 80 && $overallCoverage >= 80) {
    echo "⚠️ STATUS: GOOD SYNCHRONIZATION\n";
    echo "✅ Most files synchronized\n";
    echo "🔧 Some work needed\n";
} else {
    echo "❌ STATUS: NEEDS WORK\n";
    echo "🔧 Significant sync issues\n";
    echo "📝 Manual intervention required\n";
}

echo "\n📋 RECOMMENDATIONS:\n";
echo "===================\n";

if (empty($report['files_with_issues'])) {
    echo "🎉 No action needed - perfect synchronization!\n";
    echo "✅ Maintain current quality standards\n";
    echo "📊 Monitor for future changes\n";
} else {
    echo "🔧 Actions needed:\n";
    echo "1. Fix " . count($report['files_with_issues']) . " files with sync issues\n";
    echo "2. Add missing keys to achieve 100% coverage\n";
    echo "3. Run sync script to auto-fix simple issues\n";
    echo "4. Verify translations are contextually correct\n";
}

echo "\n📈 QUALITY METRICS:\n";
echo "===================\n";
printf("Sync Rate: %.1f%% (%d/%d files)\n", $syncRate, $report['synced_files'], $report['total_files']);
printf("Coverage: %.1f%% (%s/%s keys)\n", $overallCoverage, number_format($report['total_vi_keys']), number_format($report['total_en_keys']));
printf("Categories: %d (all analyzed)\n", count($report['categories']));
printf("Quality Score: %.1f/100\n", ($syncRate + $overallCoverage) / 2);

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ SYNCHRONIZATION ANALYSIS COMPLETE\n";
echo str_repeat("=", 50) . "\n";

?>
