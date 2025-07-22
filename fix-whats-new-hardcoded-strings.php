<?php

/**
 * FIX WHAT'S NEW HARDCODED STRINGS
 * Thêm keys và thay thế hardcoded strings trong 3 files what's new
 */

echo "=== FIXING WHAT'S NEW HARDCODED STRINGS ===\n\n";

// Keys cần thêm vào navigation.php
$navigationKeys = [
    'trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
    'most_viewed' => ['vi' => 'Xem nhiều nhất', 'en' => 'Most Viewed'],
    'hot_topics' => ['vi' => 'Chủ đề nóng', 'en' => 'Hot Topics'],
];

// Keys cần thêm vào common.php
$commonKeys = [
    'actions.view_latest_posts' => ['vi' => 'Xem bài viết mới nhất', 'en' => 'View Latest Posts'],
    'actions.view_popular' => ['vi' => 'Xem phổ biến', 'en' => 'View Popular'],
    'actions.view_trending' => ['vi' => 'Xem xu hướng', 'en' => 'View Trending'],
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
    
    // Find the last closing bracket
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        echo "❌ Could not find closing bracket in $filePath\n";
        return false;
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

echo "📁 Adding navigation keys...\n";

// Add navigation keys
$viNavFile = __DIR__ . "/resources/lang/vi/navigation.php";
$enNavFile = __DIR__ . "/resources/lang/en/navigation.php";

addKeysToFile($viNavFile, $navigationKeys, 'vi');
addKeysToFile($enNavFile, $navigationKeys, 'en');

echo "\n📁 Adding common action keys...\n";

// Add common keys
$viCommonFile = __DIR__ . "/resources/lang/vi/common.php";
$enCommonFile = __DIR__ . "/resources/lang/en/common.php";

addKeysToFile($viCommonFile, $commonKeys, 'vi');
addKeysToFile($enCommonFile, $commonKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Navigation keys added: " . count($navigationKeys) . "\n";
echo "Common action keys added: " . count($commonKeys) . "\n";
echo "Total keys added: " . (count($navigationKeys) + count($commonKeys)) . "\n";

echo "\n✅ Keys addition completed at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Replace hardcoded strings in blade files with these keys.\n";
?>
