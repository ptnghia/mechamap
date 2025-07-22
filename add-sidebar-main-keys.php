<?php

/**
 * ADD SIDEBAR MAIN KEYS
 * Thêm tất cả keys thiếu cho components/sidebar.blade.php
 */

echo "=== ADDING SIDEBAR MAIN KEYS ===\n\n";

// All sidebar main keys
$sidebarMainKeys = [
    'main.threads' => ['vi' => 'chủ đề', 'en' => 'threads'],
    'main.members' => ['vi' => 'thành viên', 'en' => 'members'],
    'main.established_since' => ['vi' => 'Thành lập từ', 'en' => 'Established since'],
    'main.join_community' => ['vi' => 'Tham gia cộng đồng', 'en' => 'Join Community'],
    'main.business_development' => ['vi' => 'Phát triển kinh doanh', 'en' => 'Business Development'],
    'main.featured_topics' => ['vi' => 'Chủ đề nổi bật', 'en' => 'Featured Topics'],
    'main.no_featured_topics' => ['vi' => 'Không có chủ đề nổi bật nào.', 'en' => 'No featured topics available.'],
    'main.view_more' => ['vi' => 'Xem thêm', 'en' => 'View More'],
    'main.popular_forums' => ['vi' => 'Diễn đàn phổ biến', 'en' => 'Popular Forums'],
    'main.view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
    'main.no_forums' => ['vi' => 'Không có diễn đàn nào.', 'en' => 'No forums available.'],
    'main.active_members' => ['vi' => 'Thành viên tích cực', 'en' => 'Active Members'],
    'main.contributions' => ['vi' => 'đóng góp', 'en' => 'contributions'],
    'main.no_active_members' => ['vi' => 'Không có thành viên tích cực nào.', 'en' => 'No active members found.'],
    'main.related_communities' => ['vi' => 'Cộng đồng liên quan', 'en' => 'Related Communities'],
    'main.topics' => ['vi' => 'chủ đề', 'en' => 'topics'],
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

echo "📁 Processing sidebar main keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $sidebarMainKeys, 'vi')) {
    $totalAdded = count($sidebarMainKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $sidebarMainKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total sidebar main keys added: " . count($sidebarMainKeys) . "\n";

echo "\n✅ Sidebar main keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
