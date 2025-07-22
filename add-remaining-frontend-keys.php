<?php

/**
 * ADD REMAINING FRONTEND KEYS
 * Thêm các keys còn lại cho frontend user experience
 */

echo "=== ADDING REMAINING FRONTEND KEYS ===\n\n";

// Additional frontend keys based on analysis
$additionalKeys = [
    // Gallery and media
    'gallery' => [
        'search_placeholder' => ['vi' => 'Tìm kiếm thư viện...', 'en' => 'Search gallery...'],
        'uploaded_by' => ['vi' => 'Tải lên bởi', 'en' => 'Uploaded by'],
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'no_media_found' => ['vi' => 'Không tìm thấy media nào', 'en' => 'No media items found'],
        'upload_media' => ['vi' => 'Tải lên media', 'en' => 'Upload Media'],
        'select_file' => ['vi' => 'Chọn file', 'en' => 'Select File'],
        'title' => ['vi' => 'Tiêu đề', 'en' => 'Title'],
        'title_help' => ['vi' => 'Đặt tiêu đề mô tả cho media (tùy chọn)', 'en' => 'Give your media a descriptive title (optional)'],
        'description' => ['vi' => 'Mô tả', 'en' => 'Description'],
        'description_help' => ['vi' => 'Thêm mô tả cho media (tùy chọn)', 'en' => 'Add a description for your media (optional)'],
        'upload' => ['vi' => 'Tải lên', 'en' => 'Upload'],
        'comments' => ['vi' => 'Bình luận', 'en' => 'Comments'],
        'comments_coming_soon' => ['vi' => 'Tính năng bình luận sắp ra mắt', 'en' => 'Comments feature coming soon'],
        'media_information' => ['vi' => 'Thông tin media', 'en' => 'Media Information'],
        'uploaded' => ['vi' => 'Đã tải lên', 'en' => 'Uploaded'],
    ],
    
    // Profile and user
    'profile' => [
        'last_seen' => ['vi' => 'Lần cuối truy cập:', 'en' => 'Last seen:'],
        'replies' => ['vi' => 'Trả lời', 'en' => 'Replies'],
        'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'reactions' => ['vi' => 'Phản ứng', 'en' => 'Reactions'],
        'about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
        'registered' => ['vi' => 'Đã đăng ký', 'en' => 'Registered'],
        'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
        'never' => ['vi' => 'Chưa bao giờ', 'en' => 'Never'],
        'viewing_member_profile' => ['vi' => 'Đang xem hồ sơ thành viên', 'en' => 'Viewing member profile'],
        'see_all' => ['vi' => 'Xem tất cả', 'en' => 'See All'],
        'about_me' => ['vi' => 'Về tôi', 'en' => 'About Me'],
        'edit_in_settings' => ['vi' => 'Chỉnh sửa trong cài đặt tài khoản', 'en' => 'Edit in account settings'],
        'website' => ['vi' => 'Website', 'en' => 'Website'],
        'activity' => ['vi' => 'Hoạt động', 'en' => 'Activity'],
        'created_thread' => ['vi' => 'Đã tạo chủ đề:', 'en' => 'Created thread:'],
        'created_new_thread' => ['vi' => 'Đã tạo chủ đề mới', 'en' => 'Created a new thread'],
        'commented_on' => ['vi' => 'Đã bình luận về:', 'en' => 'Commented on:'],
        'profile_posts' => ['vi' => 'Bài viết hồ sơ', 'en' => 'Profile Posts'],
        'write_something' => ['vi' => 'Viết gì đó cho', 'en' => 'Write something on'],
        'profile' => ['vi' => 'hồ sơ', 'en' => 'profile'],
        'post' => ['vi' => 'Đăng', 'en' => 'Post'],
        'no_profile_posts' => ['vi' => 'Chưa có bài viết hồ sơ nào', 'en' => 'No profile posts yet'],
        'activities' => ['vi' => 'Hoạt động', 'en' => 'Activities'],
        'back_to_profile' => ['vi' => 'Quay lại hồ sơ', 'en' => 'Back to Profile'],
    ],
    
    // Search and filters
    'search' => [
        'criteria' => ['vi' => 'Tiêu chí tìm kiếm', 'en' => 'Search Criteria'],
        'keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
        'author' => ['vi' => 'Tác giả', 'en' => 'Author'],
        'forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
        'date_range' => ['vi' => 'Khoảng thời gian', 'en' => 'Date Range'],
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
        'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
    ],
    
    // Following and social
    'following' => [
        'following' => ['vi' => 'Đang theo dõi', 'en' => 'Following'],
        'followers' => ['vi' => 'Người theo dõi', 'en' => 'Followers'],
        'followed_threads' => ['vi' => 'Chủ đề đã theo dõi', 'en' => 'Followed Threads'],
        'participated_discussions' => ['vi' => 'Thảo luận đã tham gia', 'en' => 'Participated Discussions'],
        'filters' => ['vi' => 'Bộ lọc', 'en' => 'Filters'],
        'people_following_you' => ['vi' => 'Người đang theo dõi bạn', 'en' => 'People Following You'],
        'people_you_follow' => ['vi' => 'Người bạn đang theo dõi', 'en' => 'People You Follow'],
        'follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
    ],
    
    // Bookmarks
    'bookmarks' => [
        'thread_in' => ['vi' => 'Chủ đề trong', 'en' => 'Thread in'],
        'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
        'bookmarked_item' => ['vi' => 'Mục đã đánh dấu', 'en' => 'Bookmarked item'],
        'notes' => ['vi' => 'Ghi chú', 'en' => 'Notes'],
        'bookmarked' => ['vi' => 'Đã đánh dấu', 'en' => 'Bookmarked'],
    ],
    
    // Conversations
    'conversations' => [
        'conversation' => ['vi' => 'Cuộc trò chuyện', 'en' => 'Conversation'],
        'invite_participants' => ['vi' => 'Mời người tham gia', 'en' => 'Invite participants'],
        'mute_conversation' => ['vi' => 'Tắt tiếng cuộc trò chuyện', 'en' => 'Mute conversation'],
        'report' => ['vi' => 'Báo cáo', 'en' => 'Report'],
        'leave_conversation' => ['vi' => 'Rời khỏi cuộc trò chuyện', 'en' => 'Leave conversation'],
    ],
    
    // FAQ
    'faq' => [
        'categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
        'still_have_questions' => ['vi' => 'Vẫn còn thắc mắc?', 'en' => 'Still have questions?'],
        'contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
    ],
    
    // New content
    'new_content' => [
        'newest_threads' => ['vi' => 'Chủ đề mới nhất', 'en' => 'Newest Threads'],
        'view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'in' => ['vi' => 'trong', 'en' => 'in'],
        'no_threads_found' => ['vi' => 'Không tìm thấy chủ đề nào', 'en' => 'No threads found'],
    ],
    
    // Business and features
    'business' => [
        'premium_listing_description' => ['vi' => 'Danh sách cao cấp xuất hiện ở đầu kết quả tìm kiếm và trang danh mục, mang lại khả năng hiển thị tối đa cho doanh nghiệp của bạn. Chúng cũng bao gồm các yếu tố hình ảnh nâng cao để làm cho danh sách của bạn nổi bật so với đối thủ cạnh tranh.', 'en' => 'Premium listings appear at the top of search results and category pages, giving your business maximum visibility. They also include enhanced visual elements to make your listing stand out from the competition.'],
        'can_cancel_anytime' => ['vi' => 'Có, bạn có thể hủy đăng ký bất cứ lúc nào. Dịch vụ của bạn sẽ tiếp tục cho đến hết chu kỳ thanh toán hiện tại.', 'en' => 'Yes, you can cancel your subscription at any time. Your services will continue until the end of your current billing period.'],
        'enterprise_packages' => ['vi' => 'Có, chúng tôi cung cấp các gói doanh nghiệp tùy chỉnh cho các doanh nghiệp lớn hơn có nhu cầu cụ thể. Vui lòng liên hệ với đội ngũ bán hàng của chúng tôi để thảo luận về yêu cầu của bạn và nhận giải pháp phù hợp.', 'en' => 'Yes, we offer custom enterprise packages for larger businesses with specific needs. Please contact our sales team to discuss your requirements and get a tailored solution.'],
    ],
];

// Function to add keys to translation files
function addKeysToFile($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        // Create file if it doesn't exist
        $template = "<?php\n\n/**\n * " . ucfirst(basename($filePath, '.php')) . " Translation File - " . 
                   ($lang === 'vi' ? 'Vietnamese' : 'English') . "\n */\n\nreturn [\n];\n";
        
        if (!file_put_contents($filePath, $template)) {
            echo "❌ Failed to create $filePath\n";
            return false;
        }
        echo "📄 Created new file: $filePath\n";
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

$totalAdded = 0;

foreach ($additionalKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$category.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$category.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total keys added: $totalAdded\n";
echo "Categories processed: " . count($additionalKeys) . "\n";

echo "\n✅ Remaining frontend keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
