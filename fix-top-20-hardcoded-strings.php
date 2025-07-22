<?php

/**
 * FIX TOP 20 HARDCODED STRINGS
 * Thêm keys và thay thế hardcoded strings từ top 20 missing keys
 */

echo "=== FIXING TOP 20 HARDCODED STRINGS ===\n\n";

// All missing keys organized by translation file
$translationKeys = [
    // Auth keys
    'auth_keys' => [
        'confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
        'password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
        'confirm' => ['vi' => 'Xác nhận', 'en' => 'Confirm'],
        'secure_area_message' => ['vi' => 'Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu trước khi tiếp tục.', 'en' => 'This is a secure area of the application. Please confirm your password before continuing.'],
    ],
    
    // Forum keys
    'forum_keys' => [
        'thread_in' => ['vi' => 'Chủ đề trong', 'en' => 'Thread in'],
        'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
    ],
    
    // Bookmark keys
    'bookmark_keys' => [
        'bookmarked_item' => ['vi' => 'Mục đã đánh dấu', 'en' => 'Bookmarked item'],
        'bookmarked' => ['vi' => 'Đã đánh dấu', 'en' => 'Bookmarked'],
        'bookmark_help_text' => ['vi' => 'Đánh dấu chủ đề và bài viết để dễ dàng tìm thấy sau này.', 'en' => 'Bookmark threads and posts to find them easily later.'],
    ],
    
    // Common keys
    'common_keys' => [
        'notes' => ['vi' => 'Ghi chú', 'en' => 'Notes'],
        'remove' => ['vi' => 'Xóa', 'en' => 'Remove'],
    ],
    
    // Features keys
    'features_keys' => [
        'brand.actions.search' => ['vi' => 'Tìm kiếm thương hiệu', 'en' => 'Search brands'],
        'community.labels.events' => ['vi' => 'Sự kiện', 'en' => 'Events'],
        'community.labels.jobs' => ['vi' => 'Việc làm', 'en' => 'Jobs'],
    ],
    
    // Forms upload keys
    'forms_keys' => [
        'upload.drag_drop_here' => ['vi' => 'Kéo và thả tệp vào đây', 'en' => 'Drag and drop files here'],
        'upload.or' => ['vi' => 'hoặc', 'en' => 'or'],
        'upload.select_from_computer' => ['vi' => 'Chọn từ máy tính', 'en' => 'Select from computer'],
        'upload.select_files' => ['vi' => 'Chọn tệp', 'en' => 'Select files'],
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

// Map categories to files
$categoryFileMap = [
    'auth_keys' => 'auth',
    'forum_keys' => 'forum',
    'bookmark_keys' => 'bookmarks',
    'common_keys' => 'common',
    'features_keys' => 'features',
    'forms_keys' => 'forms',
];

$totalAdded = 0;

foreach ($translationKeys as $category => $keys) {
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
echo "Total hardcoded string keys added: $totalAdded\n";
echo "Categories processed: " . count($translationKeys) . "\n";

echo "\n✅ Keys addition completed at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Replace hardcoded strings in blade files with these keys.\n";
?>
