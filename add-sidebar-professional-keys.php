<?php

/**
 * ADD SIDEBAR PROFESSIONAL KEYS
 * Thêm tất cả keys thiếu cho components/sidebar-professional.blade.php
 */

echo "=== ADDING SIDEBAR PROFESSIONAL KEYS ===\n\n";

// All sidebar professional keys organized by category
$sidebarProfessionalKeys = [
    // Professional sidebar keys
    'professional.mechamap_community' => ['vi' => 'Cộng đồng MechaMap', 'en' => 'MechaMap Community'],
    'professional.professional_network' => ['vi' => 'Mạng lưới chuyên nghiệp', 'en' => 'Professional Network'],
    'professional.technical_discussions' => ['vi' => 'Thảo luận kỹ thuật', 'en' => 'Technical Discussions'],
    'professional.engineers' => ['vi' => 'Kỹ sư', 'en' => 'Engineers'],
    'professional.weekly_activity' => ['vi' => 'Hoạt động hàng tuần', 'en' => 'Weekly Activity'],
    'professional.growth_rate' => ['vi' => 'Tỷ lệ tăng trưởng', 'en' => 'Growth Rate'],
    'professional.join_professional_network' => ['vi' => 'Tham gia mạng lưới chuyên nghiệp', 'en' => 'Join Professional Network'],
    'professional.weekly_trends' => ['vi' => 'Xu hướng hàng tuần', 'en' => 'Weekly Trends'],
    'professional.points' => ['vi' => 'điểm', 'en' => 'points'],
    'professional.discussions' => ['vi' => 'thảo luận', 'en' => 'discussions'],
    'professional.featured_discussions' => ['vi' => 'Thảo luận nổi bật', 'en' => 'Featured Discussions'],
    'professional.top_engineers' => ['vi' => 'Kỹ sư hàng đầu', 'en' => 'Top Engineers'],
    'professional.leaderboard' => ['vi' => 'Bảng xếp hạng', 'en' => 'Leaderboard'],
    'professional.recently' => ['vi' => 'gần đây', 'en' => 'recently'],
    'professional.recommendations_for_you' => ['vi' => 'Đề xuất cho bạn', 'en' => 'Recommendations for You'],
    'professional.by' => ['vi' => 'bởi', 'en' => 'by'],
    'professional.in' => ['vi' => 'trong', 'en' => 'in'],
    'professional.active_forums' => ['vi' => 'Diễn đàn hoạt động', 'en' => 'Active Forums'],
    'professional.new_this_month' => ['vi' => 'mới trong tháng', 'en' => 'new this month'],
    'professional.high_activity' => ['vi' => 'Hoạt động cao', 'en' => 'High Activity'],
    'professional.medium_activity' => ['vi' => 'Hoạt động trung bình', 'en' => 'Medium Activity'],
    'professional.low_activity' => ['vi' => 'Hoạt động thấp', 'en' => 'Low Activity'],
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

echo "📁 Processing sidebar professional keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $sidebarProfessionalKeys, 'vi')) {
    $totalAdded = count($sidebarProfessionalKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $sidebarProfessionalKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total sidebar professional keys added: " . count($sidebarProfessionalKeys) . "\n";

echo "\n✅ Sidebar professional keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
