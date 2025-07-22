<?php

/**
 * ADD USER DASHBOARD SIDEBAR KEYS
 * Thêm tất cả keys thiếu cho components/user-dashboard-sidebar.blade.php
 */

echo "=== ADDING USER DASHBOARD SIDEBAR KEYS ===\n\n";

// All user dashboard sidebar keys
$userDashboardSidebarKeys = [
    'user_dashboard.quick_stats' => ['vi' => 'Thống kê nhanh', 'en' => 'Quick Stats'],
    'user_dashboard.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    'user_dashboard.comments' => ['vi' => 'Bình luận', 'en' => 'Comments'],
    'user_dashboard.following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
    'user_dashboard.points' => ['vi' => 'Điểm', 'en' => 'Points'],
    'user_dashboard.upgrade_account' => ['vi' => 'Nâng cấp tài khoản', 'en' => 'Upgrade Account'],
    'user_dashboard.upgrade_to_member_desc' => ['vi' => 'Nâng cấp lên thành viên để truy cập đầy đủ tính năng', 'en' => 'Upgrade to member for full feature access'],
    'user_dashboard.upgrade_now' => ['vi' => 'Nâng cấp ngay', 'en' => 'Upgrade Now'],
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

echo "📁 Processing user dashboard sidebar keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $userDashboardSidebarKeys, 'vi')) {
    $totalAdded = count($userDashboardSidebarKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $userDashboardSidebarKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total user dashboard sidebar keys added: " . count($userDashboardSidebarKeys) . "\n";

echo "\n✅ User dashboard sidebar keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
