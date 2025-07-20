<?php
/**
 * Analyze Duplicate Translation Keys
 * Find keys with same content but different names
 */

echo "🔍 Analyzing Duplicate Translation Keys...\n";
echo "==========================================\n\n";

// Load all language files
$viKeys = loadLanguageFiles('resources/lang/vi');
$enKeys = loadLanguageFiles('resources/lang/en');

echo "📊 Loaded Keys:\n";
echo "   VI: " . count($viKeys) . " keys\n";
echo "   EN: " . count($enKeys) . " keys\n\n";

// 1. Find exact content duplicates within VI
echo "🔍 Finding VI Content Duplicates...\n";
$viDuplicates = findContentDuplicates($viKeys);
echo "   Found " . count($viDuplicates) . " groups of duplicate content\n\n";

// 2. Find exact content duplicates within EN  
echo "🔍 Finding EN Content Duplicates...\n";
$enDuplicates = findContentDuplicates($enKeys);
echo "   Found " . count($enDuplicates) . " groups of duplicate content\n\n";

// 3. Find similar content (fuzzy matching)
echo "🔍 Finding Similar Content...\n";
$viSimilar = findSimilarContent($viKeys);
$enSimilar = findSimilarContent($enKeys);
echo "   VI similar groups: " . count($viSimilar) . "\n";
echo "   EN similar groups: " . count($enSimilar) . "\n\n";

// 4. Cross-language analysis
echo "🔍 Cross-Language Analysis...\n";
$crossAnalysis = analyzeCrossLanguage($viKeys, $enKeys);
echo "   Keys only in VI: " . count($crossAnalysis['vi_only']) . "\n";
echo "   Keys only in EN: " . count($crossAnalysis['en_only']) . "\n";
echo "   Matching keys: " . count($crossAnalysis['matching']) . "\n\n";

// 5. Generate merge recommendations
echo "💡 Generating Merge Recommendations...\n";
$recommendations = generateMergeRecommendations($viDuplicates, $enDuplicates);
echo "   Generated " . count($recommendations) . " merge recommendations\n\n";

// 6. Save results
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [
        'vi_total_keys' => count($viKeys),
        'en_total_keys' => count($enKeys),
        'vi_duplicate_groups' => count($viDuplicates),
        'en_duplicate_groups' => count($enDuplicates),
        'vi_similar_groups' => count($viSimilar),
        'en_similar_groups' => count($enSimilar)
    ],
    'vi_duplicates' => $viDuplicates,
    'en_duplicates' => $enDuplicates,
    'vi_similar' => $viSimilar,
    'en_similar' => $enSimilar,
    'cross_language' => $crossAnalysis,
    'merge_recommendations' => $recommendations
];

if (!is_dir('storage/localization')) {
    mkdir('storage/localization', 0755, true);
}

file_put_contents(
    'storage/localization/duplicate_analysis.json',
    json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

// Generate readable report
generateDuplicateReport($results);

echo "✅ Duplicate analysis completed!\n";
echo "📊 Results saved to: storage/localization/duplicate_analysis.json\n";
echo "📋 Report saved to: storage/localization/task_1_2_duplicate_report.md\n";

// Helper Functions

function loadLanguageFiles($directory) {
    $allKeys = [];
    
    if (!is_dir($directory)) {
        return $allKeys;
    }
    
    $files = glob($directory . '/*.php');
    
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        $content = include $file;
        $flatKeys = flattenArray($content, $filename);
        $allKeys = array_merge($allKeys, $flatKeys);
    }
    
    return $allKeys;
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

function findContentDuplicates($keys) {
    $contentGroups = [];
    
    // Group keys by content
    foreach ($keys as $key => $content) {
        $normalizedContent = normalizeContent($content);
        $contentGroups[$normalizedContent][] = $key;
    }
    
    // Filter groups with more than 1 key
    $duplicates = [];
    foreach ($contentGroups as $content => $keyList) {
        if (count($keyList) > 1) {
            $duplicates[] = [
                'content' => $content,
                'keys' => $keyList,
                'count' => count($keyList)
            ];
        }
    }
    
    // Sort by count descending
    usort($duplicates, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    return $duplicates;
}

function findSimilarContent($keys) {
    $similar = [];
    $processed = [];
    
    foreach ($keys as $key1 => $content1) {
        if (isset($processed[$key1])) continue;
        
        $group = [$key1];
        $processed[$key1] = true;
        
        foreach ($keys as $key2 => $content2) {
            if ($key1 === $key2 || isset($processed[$key2])) continue;
            
            $similarity = calculateSimilarity($content1, $content2);
            if ($similarity > 0.8) { // 80% similarity threshold
                $group[] = $key2;
                $processed[$key2] = true;
            }
        }
        
        if (count($group) > 1) {
            $similar[] = [
                'keys' => $group,
                'count' => count($group),
                'sample_content' => substr($keys[$key1], 0, 100) . '...'
            ];
        }
    }
    
    return $similar;
}

function calculateSimilarity($str1, $str2) {
    $str1 = normalizeContent($str1);
    $str2 = normalizeContent($str2);
    
    return similar_text($str1, $str2) / max(strlen($str1), strlen($str2));
}

function normalizeContent($content) {
    // Remove extra spaces, convert to lowercase, remove punctuation
    $content = strtolower(trim($content));
    $content = preg_replace('/[^\w\s]/', '', $content);
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

function analyzeCrossLanguage($viKeys, $enKeys) {
    $viKeyNames = array_keys($viKeys);
    $enKeyNames = array_keys($enKeys);
    
    return [
        'vi_only' => array_diff($viKeyNames, $enKeyNames),
        'en_only' => array_diff($enKeyNames, $viKeyNames),
        'matching' => array_intersect($viKeyNames, $enKeyNames)
    ];
}

function generateMergeRecommendations($viDuplicates, $enDuplicates) {
    $recommendations = [];
    
    // Process VI duplicates
    foreach ($viDuplicates as $group) {
        if ($group['count'] > 1) {
            $recommendation = generateMergeRecommendation($group['keys'], 'vi');
            if ($recommendation) {
                $recommendations[] = $recommendation;
            }
        }
    }
    
    // Process EN duplicates
    foreach ($enDuplicates as $group) {
        if ($group['count'] > 1) {
            $recommendation = generateMergeRecommendation($group['keys'], 'en');
            if ($recommendation) {
                $recommendations[] = $recommendation;
            }
        }
    }
    
    return $recommendations;
}

function generateMergeRecommendation($keys, $language) {
    // Choose the "best" key as the target
    $targetKey = chooseBestKey($keys);
    $keysToMerge = array_filter($keys, function($key) use ($targetKey) {
        return $key !== $targetKey;
    });
    
    if (empty($keysToMerge)) {
        return null;
    }
    
    return [
        'language' => $language,
        'target_key' => $targetKey,
        'keys_to_merge' => array_values($keysToMerge),
        'action' => 'merge',
        'priority' => calculateMergePriority($keys)
    ];
}

function chooseBestKey($keys) {
    // Prefer shorter, more generic keys
    // Prefer keys without "messages." prefix
    // Prefer keys with common patterns
    
    $scored = [];
    foreach ($keys as $key) {
        $score = 0;
        
        // Shorter is better
        $score += (100 - strlen($key));
        
        // Avoid "messages." prefix
        if (!str_starts_with($key, 'messages.')) {
            $score += 50;
        }
        
        // Prefer common patterns
        if (str_contains($key, 'ui.') || str_contains($key, 'common.')) {
            $score += 30;
        }
        
        $scored[$key] = $score;
    }
    
    arsort($scored);
    return array_key_first($scored);
}

function calculateMergePriority($keys) {
    // Higher priority for more keys to merge
    $priority = count($keys) * 10;
    
    // Higher priority if involves "messages." keys
    foreach ($keys as $key) {
        if (str_starts_with($key, 'messages.')) {
            $priority += 20;
        }
    }
    
    return min($priority, 100); // Cap at 100
}

function generateDuplicateReport($results) {
    $report = "# Task 1.2: Phân Tích Keys Trùng Lặp - Báo Cáo\n\n";
    $report .= "**Thời gian thực hiện:** " . $results['timestamp'] . "\n";
    $report .= "**Trạng thái:** ✅ HOÀN THÀNH\n\n";
    
    $report .= "## 📊 Tổng Quan\n\n";
    $report .= "- **VI total keys:** " . number_format($results['summary']['vi_total_keys']) . "\n";
    $report .= "- **EN total keys:** " . number_format($results['summary']['en_total_keys']) . "\n";
    $report .= "- **VI duplicate groups:** " . $results['summary']['vi_duplicate_groups'] . "\n";
    $report .= "- **EN duplicate groups:** " . $results['summary']['en_duplicate_groups'] . "\n\n";
    
    $report .= "## 🔍 Top Duplicate Groups\n\n";
    $report .= "### Vietnamese (VI)\n";
    foreach (array_slice($results['vi_duplicates'], 0, 10) as $i => $group) {
        $report .= ($i + 1) . ". **" . $group['count'] . " keys** with content: `" . substr($group['content'], 0, 50) . "...`\n";
        foreach ($group['keys'] as $key) {
            $report .= "   - `$key`\n";
        }
        $report .= "\n";
    }
    
    $report .= "### English (EN)\n";
    foreach (array_slice($results['en_duplicates'], 0, 10) as $i => $group) {
        $report .= ($i + 1) . ". **" . $group['count'] . " keys** with content: `" . substr($group['content'], 0, 50) . "...`\n";
        foreach ($group['keys'] as $key) {
            $report .= "   - `$key`\n";
        }
        $report .= "\n";
    }
    
    $report .= "## 💡 Merge Recommendations\n\n";
    foreach (array_slice($results['merge_recommendations'], 0, 15) as $i => $rec) {
        $report .= ($i + 1) . ". **Priority " . $rec['priority'] . "** (" . strtoupper($rec['language']) . ")\n";
        $report .= "   - **Target:** `" . $rec['target_key'] . "`\n";
        $report .= "   - **Merge:** " . implode(', ', array_map(function($k) { return "`$k`"; }, $rec['keys_to_merge'])) . "\n\n";
    }
    
    $report .= "## ✅ Task 1.2 Completion\n\n";
    $report .= "- [x] Xác định keys có nội dung giống nhau ✅\n";
    $report .= "- [x] Tạo danh sách merge candidates ✅\n";
    $report .= "- [x] Đề xuất key chuẩn cho mỗi nhóm ✅\n";
    $report .= "- [x] Phân tích cross-language ✅\n\n";
    $report .= "**Next Task:** 1.3 Kiểm tra đồng bộ VI/EN\n";
    
    file_put_contents('storage/localization/task_1_2_duplicate_report.md', $report);
}
