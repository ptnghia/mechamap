<?php

/**
 * ADD WHATS NEW SHOWCASES KEYS
 * Thêm tất cả keys thiếu cho whats-new/showcases.blade.php
 */

echo "=== ADDING WHATS NEW SHOWCASES KEYS ===\n\n";

// All whats new showcases keys organized by file
$whatsNewShowcasesKeys = [
    // Navigation keys
    'navigation_keys' => [
        'main.whats_new' => ['vi' => 'Có gì mới', 'en' => "What's New"],
    ],
    
    // Forum keys
    'forum_keys' => [
        'posts.new' => ['vi' => 'Bài viết mới', 'en' => 'New Posts'],
        'threads.new' => ['vi' => 'Chủ đề mới', 'en' => 'New Threads'],
        'threads.looking_for_replies' => ['vi' => 'Tìm kiếm trả lời', 'en' => 'Looking for Replies'],
    ],
    
    // Common keys
    'common_keys' => [
        'buttons.popular' => ['vi' => 'Phổ biến', 'en' => 'Popular'],
        'labels.forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
    ],
    
    // Showcase keys
    'showcase_keys' => [
        'new' => ['vi' => 'Showcase mới', 'en' => 'New Showcases'],
    ],
    
    // Media keys
    'media_keys' => [
        'new' => ['vi' => 'Phương tiện mới', 'en' => 'New Media'],
    ],
    
    // UI keys
    'ui_keys' => [
        'pagination.page' => ['vi' => 'Trang', 'en' => 'Page'],
        'pagination.of' => ['vi' => 'của', 'en' => 'of'],
        'pagination.previous' => ['vi' => 'Trước', 'en' => 'Previous'],
        'pagination.next' => ['vi' => 'Tiếp', 'en' => 'Next'],
        'actions.view_details' => ['vi' => 'Xem chi tiết', 'en' => 'View Details'],
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
    'navigation_keys' => 'navigation',
    'forum_keys' => 'forum',
    'common_keys' => 'common',
    'showcase_keys' => 'showcase',
    'media_keys' => 'media',
    'ui_keys' => 'ui',
];

$totalAdded = 0;

foreach ($whatsNewShowcasesKeys as $category => $keys) {
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
echo "Total whats new showcases keys added: $totalAdded\n";
echo "Categories processed: " . count($whatsNewShowcasesKeys) . "\n";

echo "\n✅ Whats new showcases keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
