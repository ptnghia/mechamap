<?php

/**
 * ADD USER DASHBOARD GUEST KEYS
 * Thêm tất cả keys thiếu cho user/dashboard-guest.blade.php
 */

echo "=== ADDING USER DASHBOARD GUEST KEYS ===\n\n";

// All user dashboard guest keys organized by category
$userDashboardGuestKeys = [
    // Navigation keys
    'nav_keys' => [
        'user.dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
    ],
    
    // Auth keys
    'auth_keys' => [
        'guest_role' => ['vi' => 'Khách', 'en' => 'Guest'],
        'guest_role_desc' => ['vi' => 'Bạn đang sử dụng tài khoản khách với quyền hạn xem nội dung. Nâng cấp để tham gia thảo luận và tạo nội dung.', 'en' => 'You are using a guest account with view-only permissions. Upgrade to participate in discussions and create content.'],
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
    'nav_keys' => 'nav',
    'auth_keys' => 'auth',
];

$totalAdded = 0;

foreach ($userDashboardGuestKeys as $category => $keys) {
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
echo "Total user dashboard guest keys added: $totalAdded\n";
echo "Categories processed: " . count($userDashboardGuestKeys) . "\n";

echo "\n✅ User dashboard guest keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
