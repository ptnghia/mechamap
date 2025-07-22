<?php

/**
 * ADD THREAD CREATION SIDEBAR KEYS
 * Thêm tất cả keys thiếu cho components/thread-creation-sidebar.blade.php
 */

echo "=== ADDING THREAD CREATION SIDEBAR KEYS ===\n\n";

// All thread creation sidebar keys organized by category
$threadCreationSidebarKeys = [
    // Thread creation keys
    'thread_creation.writing_tips' => ['vi' => 'Mẹo viết bài', 'en' => 'Writing Tips'],
    'thread_creation.clear_title' => ['vi' => 'Tiêu đề rõ ràng', 'en' => 'Clear Title'],
    'thread_creation.clear_title_desc' => ['vi' => 'Sử dụng tiêu đề mô tả chính xác nội dung bài viết của bạn', 'en' => 'Use a title that accurately describes the content of your post'],
    'thread_creation.detailed_content' => ['vi' => 'Nội dung chi tiết', 'en' => 'Detailed Content'],
    'thread_creation.detailed_content_desc' => ['vi' => 'Cung cấp thông tin đầy đủ và chi tiết để người đọc hiểu rõ vấn đề', 'en' => 'Provide complete and detailed information so readers understand the issue clearly'],
    'thread_creation.use_images' => ['vi' => 'Sử dụng hình ảnh', 'en' => 'Use Images'],
    'thread_creation.use_images_desc' => ['vi' => 'Thêm hình ảnh, sơ đồ hoặc ảnh chụp màn hình để minh họa', 'en' => 'Add images, diagrams, or screenshots to illustrate your points'],
    'thread_creation.choose_right_category' => ['vi' => 'Chọn đúng danh mục', 'en' => 'Choose Right Category'],
    'thread_creation.choose_right_category_desc' => ['vi' => 'Đặt bài viết vào danh mục phù hợp để dễ tìm kiếm', 'en' => 'Place your post in the appropriate category for easy discovery'],
    'thread_creation.community_rules' => ['vi' => 'Quy tắc cộng đồng', 'en' => 'Community Rules'],
    'thread_creation.respect_opinions' => ['vi' => 'Tôn trọng ý kiến của người khác', 'en' => 'Respect others\' opinions'],
    'thread_creation.no_spam' => ['vi' => 'Không spam hoặc quảng cáo', 'en' => 'No spam or advertising'],
    'thread_creation.appropriate_language' => ['vi' => 'Sử dụng ngôn ngữ phù hợp', 'en' => 'Use appropriate language'],
    'thread_creation.no_personal_info' => ['vi' => 'Không chia sẻ thông tin cá nhân', 'en' => 'Don\'t share personal information'],
    'thread_creation.verify_info' => ['vi' => 'Kiểm tra thông tin trước khi đăng', 'en' => 'Verify information before posting'],
    'thread_creation.read_full_rules' => ['vi' => 'Đọc đầy đủ quy tắc', 'en' => 'Read Full Rules'],
    'thread_creation.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular Categories'],
    'thread_creation.posts' => ['vi' => 'bài viết', 'en' => 'posts'],
    'thread_creation.no_categories' => ['vi' => 'Chưa có danh mục nào', 'en' => 'No categories available'],
    'thread_creation.need_support' => ['vi' => 'Cần hỗ trợ?', 'en' => 'Need Support?'],
    'thread_creation.support_description' => ['vi' => 'Nếu bạn cần giúp đỡ trong việc tạo bài viết hoặc có thắc mắc, chúng tôi sẵn sàng hỗ trợ.', 'en' => 'If you need help creating a post or have questions, we\'re here to help.'],
    'thread_creation.detailed_guide' => ['vi' => 'Hướng dẫn chi tiết', 'en' => 'Detailed Guide'],
    'thread_creation.contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
    'thread_creation.your_activity' => ['vi' => 'Hoạt động của bạn', 'en' => 'Your Activity'],
    'thread_creation.posts_count' => ['vi' => 'Số bài viết', 'en' => 'Posts Count'],
    'thread_creation.comments_count' => ['vi' => 'Số bình luận', 'en' => 'Comments Count'],
    'thread_creation.recent_post' => ['vi' => 'Bài viết gần đây', 'en' => 'Recent Post'],
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

echo "📁 Processing thread creation sidebar keys for sidebar.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/sidebar.php";
if (addKeysToFile($viFile, $threadCreationSidebarKeys, 'vi')) {
    $totalAdded = count($threadCreationSidebarKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/sidebar.php";
addKeysToFile($enFile, $threadCreationSidebarKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total thread creation sidebar keys added: " . count($threadCreationSidebarKeys) . "\n";

echo "\n✅ Thread creation sidebar keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
