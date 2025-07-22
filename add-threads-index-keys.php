<?php

/**
 * ADD THREADS INDEX KEYS
 * Thêm tất cả keys thiếu cho threads/index.blade.php
 */

echo "=== ADDING THREADS INDEX KEYS ===\n\n";

// All threads index keys organized by file
$threadsIndexKeys = [
    // Forum keys
    'forum_keys' => [
        'forums.title' => ['vi' => 'Diễn đàn', 'en' => 'Forums'],
        'threads.create' => ['vi' => 'Tạo chủ đề', 'en' => 'Create Thread'],
        'forums.all' => ['vi' => 'Tất cả diễn đàn', 'en' => 'All Forums'],
        'threads.title' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'threads.count' => ['vi' => 'chủ đề', 'en' => 'threads'],
        'threads.no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào', 'en' => 'No threads found'],
        'search.placeholder' => ['vi' => 'Tìm kiếm chủ đề...', 'en' => 'Search threads...'],
    ],
    
    // Common keys
    'common_keys' => [
        'labels.category' => ['vi' => 'Danh mục', 'en' => 'Category'],
        'buttons.latest' => ['vi' => 'Mới nhất', 'en' => 'Latest'],
    ],
    
    // Marketplace keys
    'marketplace_keys' => [
        'categories.all' => ['vi' => 'Tất cả danh mục', 'en' => 'All Categories'],
    ],
    
    // UI keys
    'ui_keys' => [
        'actions.sort' => ['vi' => 'Sắp xếp', 'en' => 'Sort'],
        'actions.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'actions.apply_filters' => ['vi' => 'Áp dụng bộ lọc', 'en' => 'Apply Filters'],
        'actions.clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear Filters'],
    ],
];

// Function to add keys to translation files
function addKeysToFile($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Find the last closing bracket (either ]; or );)
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        $lastBracketPos = strrpos($content, ');');
        if ($lastBracketPos === false) {
            echo "❌ Could not find closing bracket in $filePath\n";
            return false;
        }
    }
    
    // Build new keys string
    $newKeysString = '';
    $addedCount = 0;
    
    foreach ($keys as $key => $translations) {
        if (isset($translations[$lang])) {
            $value = $translations[$lang];
            // Escape single quotes in the value
            $value = str_replace("'", "\\'", $value);
            $newKeysString .= "  '$key' => '$value',\n";
            $addedCount++;
        }
    }
    
    if (empty($newKeysString)) {
        echo "ℹ️  No keys to add for $lang\n";
        return true;
    }
    
    // Insert new keys before the closing bracket
    $beforeClosing = substr($content, 0, $lastBracketPos);
    $afterClosing = substr($content, $lastBracketPos);
    
    $newContent = $beforeClosing . $newKeysString . $afterClosing;
    
    // Write back to file
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Added $addedCount keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

// Map categories to files
$categoryFileMap = [
    'forum_keys' => 'forum',
    'common_keys' => 'common',
    'marketplace_keys' => 'marketplace',
    'ui_keys' => 'ui',
];

$totalAdded = 0;

foreach ($threadsIndexKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    $file = $categoryFileMap[$category];
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$file.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$file.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total threads index keys added: $totalAdded\n";
echo "Categories processed: " . count($threadsIndexKeys) . "\n";

echo "\n✅ Threads index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
