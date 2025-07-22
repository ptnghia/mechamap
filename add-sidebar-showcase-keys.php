<?php

/**
 * ADD SIDEBAR SHOWCASE KEYS
 * Thêm tất cả keys thiếu cho components/sidebar-showcase.blade.php
 */

echo "=== ADDING SIDEBAR SHOWCASE KEYS ===\n\n";

// All sidebar showcase keys
$sidebarShowcaseKeys = [
    'showcase.project_showcase' => ['vi' => 'Showcase Dự án', 'en' => 'Project Showcase'],
    'showcase.discover_engineering_projects' => ['vi' => 'Khám phá các dự án kỹ thuật', 'en' => 'Discover engineering projects'],
    'showcase.total_projects' => ['vi' => 'Tổng dự án', 'en' => 'Total Projects'],
    'showcase.downloads' => ['vi' => 'Lượt tải', 'en' => 'Downloads'],
    'showcase.avg_rating' => ['vi' => 'Đánh giá TB', 'en' => 'Avg Rating'],
    'showcase.total_views' => ['vi' => 'Tổng lượt xem', 'en' => 'Total Views'],
    'showcase.create_project' => ['vi' => 'Tạo dự án', 'en' => 'Create Project'],
    'showcase.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular Categories'],
    'showcase.projects' => ['vi' => 'dự án', 'en' => 'projects'],
    'showcase.featured_projects' => ['vi' => 'Dự án nổi bật', 'en' => 'Featured Projects'],
    'showcase.complexity_levels.beginner' => ['vi' => 'Cơ bản', 'en' => 'Beginner'],
    'showcase.complexity_levels.intermediate' => ['vi' => 'Trung bình', 'en' => 'Intermediate'],
    'showcase.complexity_levels.advanced' => ['vi' => 'Nâng cao', 'en' => 'Advanced'],
    'showcase.complexity_levels.expert' => ['vi' => 'Chuyên gia', 'en' => 'Expert'],
    'showcase.popular_software' => ['vi' => 'Phần mềm phổ biến', 'en' => 'Popular Software'],
    'showcase.top_contributors' => ['vi' => 'Người đóng góp hàng đầu', 'en' => 'Top Contributors'],
    'showcase.views' => ['vi' => 'lượt xem', 'en' => 'views'],
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

echo "📁 Processing sidebar showcase keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $sidebarShowcaseKeys, 'vi')) {
    $totalAdded = count($sidebarShowcaseKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $sidebarShowcaseKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total sidebar showcase keys added: " . count($sidebarShowcaseKeys) . "\n";

echo "\n✅ Sidebar showcase keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
