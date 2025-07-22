<?php

/**
 * ADD POLL KEYS
 * Thêm tất cả keys thiếu cho threads/partials/poll.blade.php
 */

echo "=== ADDING POLL KEYS ===\n\n";

// All poll keys
$pollKeys = [
    'poll.closed' => ['vi' => 'Đã đóng', 'en' => 'Closed'],
    'poll.closes_at' => ['vi' => 'Đóng vào :time', 'en' => 'Closes :time'],
    'poll.vote' => ['vi' => 'Bình chọn', 'en' => 'Vote'],
    'poll.view_results' => ['vi' => 'Xem kết quả', 'en' => 'View Results'],
    'poll.total_votes' => ['vi' => 'Tổng số phiếu', 'en' => 'Total Votes'],
    'poll.change_vote' => ['vi' => 'Thay đổi phiếu bầu', 'en' => 'Change Vote'],
    'poll.update_vote' => ['vi' => 'Cập nhật phiếu bầu', 'en' => 'Update Vote'],
    'poll.voters' => ['vi' => 'Người bình chọn', 'en' => 'Voters'],
    'poll.loading_results' => ['vi' => 'Đang tải kết quả...', 'en' => 'Loading results...'],
    'poll.max_options_exceeded' => ['vi' => 'Bạn chỉ có thể chọn tối đa :max tùy chọn', 'en' => 'You can only select a maximum of :max options'],
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

echo "📁 Processing poll keys for forum.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/forum.php";
if (addKeysToFile($viFile, $pollKeys, 'vi')) {
    $totalAdded = count($pollKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/forum.php";
addKeysToFile($enFile, $pollKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total poll keys added: " . count($pollKeys) . "\n";

echo "\n✅ Poll keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
