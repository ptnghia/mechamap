<?php

/**
 * ADD FORUMS INDEX KEYS
 * Thêm tất cả keys thiếu cho forums/index.blade.php
 */

echo "=== ADDING FORUMS INDEX KEYS ===\n\n";

// All forums index keys organized by file
$forumsIndexKeys = [
    // Navigation keys
    'navigation_keys' => [
        'main.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        'main.forums' => ['vi' => 'Diễn đàn', 'en' => 'Forums'],
    ],

    // Forum keys
    'forum_keys' => [
        'threads.actions.create' => ['vi' => 'Tạo chủ đề', 'en' => 'Create Thread'],
        'stats.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'stats.posts' => ['vi' => 'Bài viết', 'en' => 'Posts'],
        'stats.members' => ['vi' => 'Thành viên', 'en' => 'Members'],
        'stats.forums' => ['vi' => 'Diễn đàn', 'en' => 'Forums'],
        'stats.views' => ['vi' => 'Lượt xem', 'en' => 'Views'],
        'stats.comments' => ['vi' => 'Bình luận', 'en' => 'Comments'],
        'newest_member' => ['vi' => 'Thành viên mới nhất', 'en' => 'Newest Member'],
        'search.placeholder_main' => ['vi' => 'Tìm kiếm chủ đề, bài viết...', 'en' => 'Search threads, posts...'],
        'search.description' => ['vi' => 'Tìm kiếm trong tất cả diễn đàn và chủ đề', 'en' => 'Search across all forums and threads'],
        'category.recent_threads' => ['vi' => ':count chủ đề gần đây', 'en' => ':count Recent Threads'],
        'actions.view_more' => ['vi' => 'Xem thêm', 'en' => 'View More'],
        'category.no_threads' => ['vi' => 'Không có chủ đề nào trong danh mục này', 'en' => 'No threads in this category'],
        'category.forums_in_category' => ['vi' => 'Diễn đàn trong danh mục', 'en' => 'Forums in Category'],
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
    'navigation_keys' => 'navigation',
    'forum_keys' => 'forums',
];

$totalAdded = 0;

foreach ($forumsIndexKeys as $category => $keys) {
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
echo "Total forums index keys added: $totalAdded\n";
echo "Categories processed: " . count($forumsIndexKeys) . "\n";

echo "\n✅ Forums index keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
