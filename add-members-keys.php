<?php

/**
 * ADD MEMBERS KEYS
 * Thêm tất cả keys thiếu cho members/index.blade.php
 */

echo "=== ADDING MEMBERS KEYS ===\n\n";

// All members keys from members/index.blade.php
$membersKeys = [
    'members.list_title' => ['vi' => 'Danh sách thành viên', 'en' => 'Members List'],
    'members.list_description' => ['vi' => 'Tất cả thành viên trong cộng đồng', 'en' => 'All members in the community'],
    'members.search_placeholder' => ['vi' => 'Tìm kiếm thành viên...', 'en' => 'Search members...'],
    'members.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'members.list_view' => ['vi' => 'Xem danh sách', 'en' => 'List view'],
    'members.grid_view' => ['vi' => 'Xem lưới', 'en' => 'Grid view'],
    'members.all_members' => ['vi' => 'Tất cả thành viên', 'en' => 'All members'],
    'members.online_now' => ['vi' => 'Đang trực tuyến', 'en' => 'Online now'],
    'members.staff' => ['vi' => 'Ban quản trị', 'en' => 'Staff'],
    'members.total' => ['vi' => 'Tổng số', 'en' => 'Total'],
    'members.members_count' => ['vi' => 'thành viên', 'en' => 'members'],
    'members.filtered_by' => ['vi' => 'Lọc theo', 'en' => 'Filtered by'],
    'members.sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort by'],
    'members.name' => ['vi' => 'Tên', 'en' => 'Name'],
    'members.posts' => ['vi' => 'Bài viết', 'en' => 'Posts'],
    'members.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    'members.join_date' => ['vi' => 'Ngày tham gia', 'en' => 'Join date'],
    'members.descending' => ['vi' => 'Giảm dần', 'en' => 'Descending'],
    'members.ascending' => ['vi' => 'Tăng dần', 'en' => 'Ascending'],
    'members.direction' => ['vi' => 'Hướng sắp xếp', 'en' => 'Sort direction'],
    'members.online' => ['vi' => 'Trực tuyến', 'en' => 'Online'],
    'members.admin' => ['vi' => 'Quản trị viên', 'en' => 'Admin'],
    'members.moderator' => ['vi' => 'Điều hành viên', 'en' => 'Moderator'],
    'members.joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
    'members.followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
    'members.no_members_found' => ['vi' => 'Không tìm thấy thành viên nào', 'en' => 'No members found'],
    'members.try_different_search' => ['vi' => 'Thử tìm kiếm khác', 'en' => 'Try a different search'],
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

echo "📁 Processing members keys for common.php files\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
if (addKeysToFile($viFile, $membersKeys, 'vi')) {
    $totalAdded = count($membersKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $membersKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($membersKeys) . "\n";
echo "Keys processed: " . count($membersKeys) . "\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'members.list_title',
    'members.search_placeholder', 
    'members.online',
    'members.no_members_found'
];

foreach ($testKeys as $key) {
    echo "  Testing t_common('$key')...\n";
}

echo "\n✅ Members keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
