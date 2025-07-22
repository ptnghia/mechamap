<?php

/**
 * ADD THREAD ITEM KEYS
 * Thêm tất cả keys thiếu cho partials/thread-item.blade.php
 */

echo "=== ADDING THREAD ITEM KEYS ===\n\n";

// All thread item keys
$threadItemKeys = [
    'status.pinned' => ['vi' => 'Đã ghim', 'en' => 'Pinned'],
    'status.locked' => ['vi' => 'Đã khóa', 'en' => 'Locked'],
    'actions.bookmark_remove' => ['vi' => 'Bỏ đánh dấu', 'en' => 'Remove bookmark'],
    'actions.bookmarked' => ['vi' => 'Đã đánh dấu', 'en' => 'Bookmarked'],
    'actions.bookmark_add' => ['vi' => 'Đánh dấu', 'en' => 'Add bookmark'],
    'actions.bookmark' => ['vi' => 'Đánh dấu', 'en' => 'Bookmark'],
    'actions.unfollow_thread' => ['vi' => 'Bỏ theo dõi chủ đề', 'en' => 'Unfollow thread'],
    'actions.following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
    'actions.follow_thread' => ['vi' => 'Theo dõi chủ đề', 'en' => 'Follow thread'],
    'actions.follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
    'meta.views' => ['vi' => 'lượt xem', 'en' => 'views'],
    'meta.replies' => ['vi' => 'trả lời', 'en' => 'replies'],
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

echo "📁 Processing thread item keys for forums.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forums.php";
if (addKeysToFile($viFile, $threadItemKeys, 'vi')) {
    $totalAdded = count($threadItemKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forums.php";
addKeysToFile($enFile, $threadItemKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total thread item keys added: " . count($threadItemKeys) . "\n";

echo "\n✅ Thread item keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
