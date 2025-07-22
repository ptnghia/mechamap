<?php

/**
 * ADD FORUMS SHOW KEYS
 * Thêm tất cả keys thiếu cho forums/show.blade.php
 */

echo "=== ADDING FORUMS SHOW KEYS ===\n\n";

// All forums show keys
$forumsShowKeys = [
    'actions.new_thread' => ['vi' => 'Chủ đề mới', 'en' => 'New Thread'],
    'search.placeholder' => ['vi' => 'Tìm kiếm trong diễn đàn...', 'en' => 'Search in forum...'],
    'sort.latest_activity' => ['vi' => 'Hoạt động mới nhất', 'en' => 'Latest Activity'],
    'sort.oldest_first' => ['vi' => 'Cũ nhất trước', 'en' => 'Oldest First'],
    'sort.most_replies' => ['vi' => 'Nhiều trả lời nhất', 'en' => 'Most Replies'],
    'sort.most_views' => ['vi' => 'Nhiều lượt xem nhất', 'en' => 'Most Views'],
    'filter.all' => ['vi' => 'Tất cả', 'en' => 'All'],
    'filter.recent' => ['vi' => 'Gần đây', 'en' => 'Recent'],
    'filter.unanswered' => ['vi' => 'Chưa trả lời', 'en' => 'Unanswered'],
    'threads.title' => ['vi' => 'chủ đề', 'en' => 'threads'],
    'actions.clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear Filters'],
    'threads.no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào', 'en' => 'No threads found'],
    'threads.no_threads_found_desc' => ['vi' => 'Thử điều chỉnh bộ lọc hoặc tìm kiếm của bạn', 'en' => 'Try adjusting your filters or search'],
    'threads.no_threads_yet' => ['vi' => 'Chưa có chủ đề nào', 'en' => 'No threads yet'],
    'threads.be_first_to_post' => ['vi' => 'Hãy là người đầu tiên tạo chủ đề trong diễn đàn này', 'en' => 'Be the first to create a thread in this forum'],
    'actions.create_first_thread' => ['vi' => 'Tạo chủ đề đầu tiên', 'en' => 'Create First Thread'],
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

echo "📁 Processing forums show keys for forums.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forums.php";
if (addKeysToFile($viFile, $forumsShowKeys, 'vi')) {
    $totalAdded = count($forumsShowKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forums.php";
addKeysToFile($enFile, $forumsShowKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total forums show keys added: " . count($forumsShowKeys) . "\n";

echo "\n✅ Forums show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
