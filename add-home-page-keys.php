<?php

/**
 * ADD HOME PAGE KEYS
 * Thêm tất cả keys thiếu cho home.blade.php
 */

echo "=== ADDING HOME PAGE KEYS ===\n\n";

// All home page keys organized by file
$homePageKeys = [
    // Homepage keys
    'homepage_keys' => [
        'sections.featured_showcases' => ['vi' => 'Showcase nổi bật', 'en' => 'Featured Showcases'],
        'sections.featured_showcases_desc' => ['vi' => 'Khám phá các dự án kỹ thuật xuất sắc từ cộng đồng', 'en' => 'Discover outstanding engineering projects from the community'],
    ],
    
    // UI keys
    'ui_keys' => [
        'buttons.view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
        'pagination.load_more' => ['vi' => 'Tải thêm', 'en' => 'Load More'],
        'status.sticky' => ['vi' => 'Đã ghim', 'en' => 'Sticky'],
        'status.locked' => ['vi' => 'Đã khóa', 'en' => 'Locked'],
        'common.loading' => ['vi' => 'Đang tải...', 'en' => 'Loading...'],
        'pagination.no_more_posts' => ['vi' => 'Không còn bài viết nào', 'en' => 'No more posts'],
        'common.error_occurred' => ['vi' => 'Đã xảy ra lỗi', 'en' => 'An error occurred'],
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
    'homepage_keys' => 'homepage',
    'ui_keys' => 'ui',
];

$totalAdded = 0;

foreach ($homePageKeys as $category => $keys) {
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
echo "Total home page keys added: $totalAdded\n";
echo "Categories processed: " . count($homePageKeys) . "\n";

echo "\n✅ Home page keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
