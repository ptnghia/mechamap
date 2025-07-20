<?php
/**
 * VI/EN Synchronization Analysis
 * Compare language files and identify sync gaps
 */

echo "🔍 VI/EN Synchronization Analysis...\n";
echo "====================================\n\n";

// 1. Compare file structure
echo "📁 Comparing File Structure...\n";
$viFiles = getLanguageFiles('resources/lang/vi');
$enFiles = getLanguageFiles('resources/lang/en');

$fileComparison = compareFileStructure($viFiles, $enFiles);
displayFileComparison($fileComparison);

// 2. Compare keys within matching files
echo "\n🔑 Comparing Keys in Matching Files...\n";
$keyComparisons = [];

foreach ($fileComparison['matching'] as $filename) {
    $viKeys = loadAndFlattenFile("resources/lang/vi/$filename");
    $enKeys = loadAndFlattenFile("resources/lang/en/$filename");
    
    $keyComparisons[$filename] = compareKeys($viKeys, $enKeys, $filename);
}

displayKeyComparisons($keyComparisons);

// 3. Generate sync recommendations
echo "\n💡 Generating Sync Recommendations...\n";
$recommendations = generateSyncRecommendations($fileComparison, $keyComparisons);
displayRecommendations($recommendations);

// 4. Save detailed results
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'file_comparison' => $fileComparison,
    'key_comparisons' => $keyComparisons,
    'recommendations' => $recommendations,
    'summary' => generateSummary($fileComparison, $keyComparisons)
];

if (!is_dir('storage/localization')) {
    mkdir('storage/localization', 0755, true);
}

file_put_contents(
    'storage/localization/sync_analysis.json',
    json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

// Generate readable report
generateSyncReport($results);

echo "\n✅ Sync analysis completed!\n";
echo "📊 Results saved to: storage/localization/sync_analysis.json\n";
echo "📋 Report saved to: storage/localization/task_1_3_sync_report.md\n";

// Helper Functions

function getLanguageFiles($directory) {
    if (!is_dir($directory)) {
        return [];
    }
    
    $files = glob($directory . '/*.php');
    return array_map(function($file) {
        return basename($file);
    }, $files);
}

function compareFileStructure($viFiles, $enFiles) {
    return [
        'vi_files' => $viFiles,
        'en_files' => $enFiles,
        'vi_only' => array_diff($viFiles, $enFiles),
        'en_only' => array_diff($enFiles, $viFiles),
        'matching' => array_intersect($viFiles, $enFiles),
        'vi_count' => count($viFiles),
        'en_count' => count($enFiles),
        'matching_count' => count(array_intersect($viFiles, $enFiles))
    ];
}

function displayFileComparison($comparison) {
    echo "   VI files: " . $comparison['vi_count'] . "\n";
    echo "   EN files: " . $comparison['en_count'] . "\n";
    echo "   Matching: " . $comparison['matching_count'] . "\n";
    
    if (!empty($comparison['vi_only'])) {
        echo "   ⚠️ Missing in EN: " . implode(', ', $comparison['vi_only']) . "\n";
    }
    
    if (!empty($comparison['en_only'])) {
        echo "   ⚠️ Missing in VI: " . implode(', ', $comparison['en_only']) . "\n";
    }
    
    if (empty($comparison['vi_only']) && empty($comparison['en_only'])) {
        echo "   ✅ All files present in both languages\n";
    }
}

function loadAndFlattenFile($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }
    
    $content = include $filepath;
    return flattenArray($content);
}

function flattenArray($array, $prefix = '') {
    $result = [];
    
    foreach ($array as $key => $value) {
        $newKey = $prefix ? $prefix . '.' . $key : $key;
        
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value, $newKey));
        } else {
            $result[$newKey] = $value;
        }
    }
    
    return $result;
}

function compareKeys($viKeys, $enKeys, $filename) {
    $viKeyNames = array_keys($viKeys);
    $enKeyNames = array_keys($enKeys);
    
    $comparison = [
        'filename' => $filename,
        'vi_count' => count($viKeys),
        'en_count' => count($enKeys),
        'vi_only' => array_diff($viKeyNames, $enKeyNames),
        'en_only' => array_diff($enKeyNames, $viKeyNames),
        'matching' => array_intersect($viKeyNames, $enKeyNames),
        'sync_percentage' => 0
    ];
    
    // Calculate sync percentage
    $totalKeys = count(array_unique(array_merge($viKeyNames, $enKeyNames)));
    $matchingKeys = count($comparison['matching']);
    $comparison['sync_percentage'] = $totalKeys > 0 ? round(($matchingKeys / $totalKeys) * 100, 2) : 100;
    
    return $comparison;
}

function displayKeyComparisons($comparisons) {
    foreach ($comparisons as $filename => $comparison) {
        echo "   📄 $filename:\n";
        echo "      VI: {$comparison['vi_count']} keys, EN: {$comparison['en_count']} keys\n";
        echo "      Sync: {$comparison['sync_percentage']}%\n";
        
        if (!empty($comparison['vi_only'])) {
            $count = count($comparison['vi_only']);
            echo "      ⚠️ Missing in EN: $count keys\n";
        }
        
        if (!empty($comparison['en_only'])) {
            $count = count($comparison['en_only']);
            echo "      ⚠️ Missing in VI: $count keys\n";
        }
        
        echo "\n";
    }
}

function generateSyncRecommendations($fileComparison, $keyComparisons) {
    $recommendations = [];
    
    // File-level recommendations
    foreach ($fileComparison['vi_only'] as $file) {
        $recommendations[] = [
            'type' => 'create_file',
            'priority' => 'high',
            'action' => "Create resources/lang/en/$file",
            'description' => "Missing English translation file",
            'estimated_keys' => getEstimatedKeyCount("resources/lang/vi/$file")
        ];
    }
    
    foreach ($fileComparison['en_only'] as $file) {
        $recommendations[] = [
            'type' => 'create_file',
            'priority' => 'medium',
            'action' => "Create resources/lang/vi/$file",
            'description' => "Missing Vietnamese translation file",
            'estimated_keys' => getEstimatedKeyCount("resources/lang/en/$file")
        ];
    }
    
    // Key-level recommendations
    foreach ($keyComparisons as $filename => $comparison) {
        if ($comparison['sync_percentage'] < 100) {
            $priority = $comparison['sync_percentage'] < 50 ? 'high' : 'medium';
            
            if (!empty($comparison['vi_only'])) {
                $recommendations[] = [
                    'type' => 'translate_keys',
                    'priority' => $priority,
                    'action' => "Translate " . count($comparison['vi_only']) . " keys to EN in $filename",
                    'description' => "Keys missing in English version",
                    'keys' => array_slice($comparison['vi_only'], 0, 10), // Sample
                    'total_keys' => count($comparison['vi_only'])
                ];
            }
            
            if (!empty($comparison['en_only'])) {
                $recommendations[] = [
                    'type' => 'translate_keys',
                    'priority' => $priority,
                    'action' => "Translate " . count($comparison['en_only']) . " keys to VI in $filename",
                    'description' => "Keys missing in Vietnamese version",
                    'keys' => array_slice($comparison['en_only'], 0, 10), // Sample
                    'total_keys' => count($comparison['en_only'])
                ];
            }
        }
    }
    
    // Sort by priority
    usort($recommendations, function($a, $b) {
        $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
        return $priorityOrder[$b['priority']] - $priorityOrder[$a['priority']];
    });
    
    return $recommendations;
}

function getEstimatedKeyCount($filepath) {
    if (!file_exists($filepath)) {
        return 0;
    }
    
    $content = include $filepath;
    return count(flattenArray($content));
}

function displayRecommendations($recommendations) {
    $highPriority = array_filter($recommendations, function($r) { return $r['priority'] === 'high'; });
    $mediumPriority = array_filter($recommendations, function($r) { return $r['priority'] === 'medium'; });
    
    echo "   🔥 High Priority: " . count($highPriority) . " items\n";
    echo "   ⚠️ Medium Priority: " . count($mediumPriority) . " items\n";
    
    echo "\n   Top 5 High Priority Actions:\n";
    foreach (array_slice($highPriority, 0, 5) as $i => $rec) {
        echo "   " . ($i + 1) . ". " . $rec['action'] . "\n";
    }
}

function generateSummary($fileComparison, $keyComparisons) {
    $totalViKeys = 0;
    $totalEnKeys = 0;
    $totalMissingInEn = 0;
    $totalMissingInVi = 0;
    
    foreach ($keyComparisons as $comparison) {
        $totalViKeys += $comparison['vi_count'];
        $totalEnKeys += $comparison['en_count'];
        $totalMissingInEn += count($comparison['vi_only']);
        $totalMissingInVi += count($comparison['en_only']);
    }
    
    return [
        'total_vi_keys' => $totalViKeys,
        'total_en_keys' => $totalEnKeys,
        'missing_files_in_en' => count($fileComparison['vi_only']),
        'missing_files_in_vi' => count($fileComparison['en_only']),
        'missing_keys_in_en' => $totalMissingInEn,
        'missing_keys_in_vi' => $totalMissingInVi,
        'overall_sync_percentage' => $totalViKeys > 0 ? round((($totalViKeys - $totalMissingInEn) / $totalViKeys) * 100, 2) : 100
    ];
}

function generateSyncReport($results) {
    $report = "# Task 1.3: Kiểm Tra Đồng Bộ VI/EN - Báo Cáo\n\n";
    $report .= "**Thời gian thực hiện:** " . $results['timestamp'] . "\n";
    $report .= "**Trạng thái:** ✅ HOÀN THÀNH\n\n";
    
    $summary = $results['summary'];
    
    $report .= "## 📊 Tổng Quan Đồng Bộ\n\n";
    $report .= "- **Total VI keys:** " . number_format($summary['total_vi_keys']) . "\n";
    $report .= "- **Total EN keys:** " . number_format($summary['total_en_keys']) . "\n";
    $report .= "- **Overall sync:** {$summary['overall_sync_percentage']}%\n";
    $report .= "- **Missing files in EN:** {$summary['missing_files_in_en']}\n";
    $report .= "- **Missing files in VI:** {$summary['missing_files_in_vi']}\n";
    $report .= "- **Missing keys in EN:** " . number_format($summary['missing_keys_in_en']) . "\n";
    $report .= "- **Missing keys in VI:** " . number_format($summary['missing_keys_in_vi']) . "\n\n";
    
    $report .= "## 📁 Missing Files\n\n";
    if (!empty($results['file_comparison']['vi_only'])) {
        $report .= "### Missing in EN:\n";
        foreach ($results['file_comparison']['vi_only'] as $file) {
            $report .= "- `$file`\n";
        }
        $report .= "\n";
    }
    
    if (!empty($results['file_comparison']['en_only'])) {
        $report .= "### Missing in VI:\n";
        foreach ($results['file_comparison']['en_only'] as $file) {
            $report .= "- `$file`\n";
        }
        $report .= "\n";
    }
    
    $report .= "## 🔑 Key Sync Status by File\n\n";
    foreach ($results['key_comparisons'] as $comparison) {
        $filename = $comparison['filename'];
        $sync = $comparison['sync_percentage'];
        $status = $sync >= 95 ? '✅' : ($sync >= 80 ? '⚠️' : '❌');
        
        $report .= "- **$filename**: $status {$sync}% ({$comparison['vi_count']} VI, {$comparison['en_count']} EN)\n";
    }
    
    $report .= "\n## 🚨 Priority Actions\n\n";
    $highPriority = array_filter($results['recommendations'], function($r) { return $r['priority'] === 'high'; });
    
    foreach (array_slice($highPriority, 0, 10) as $i => $rec) {
        $report .= ($i + 1) . ". **" . strtoupper($rec['priority']) . "**: " . $rec['action'] . "\n";
        $report .= "   - " . $rec['description'] . "\n\n";
    }
    
    $report .= "## ✅ Task 1.3 Completion\n\n";
    $report .= "- [x] So sánh file structure VI/EN ✅\n";
    $report .= "- [x] Xác định keys thiếu và thừa ✅\n";
    $report .= "- [x] Tạo báo cáo chi tiết về gaps ✅\n";
    $report .= "- [x] Generate sync recommendations ✅\n\n";
    $report .= "**Next Task:** 1.4 Tạo mapping matrix\n";
    
    file_put_contents('storage/localization/task_1_3_sync_report.md', $report);
}
