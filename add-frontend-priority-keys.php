<?php

/**
 * ADD FRONTEND PRIORITY KEYS
 * Thêm các keys thiếu quan trọng cho frontend user trước
 */

echo "=== ADDING FRONTEND PRIORITY KEYS ===\n\n";

// Priority keys for frontend user experience
$frontendKeys = [
    // Header and navigation keys
    'marketplace' => [
        'cart.empty_message' => ['vi' => 'Giỏ hàng trống', 'en' => 'Cart is empty'],
        'cart.add_items' => ['vi' => 'Thêm sản phẩm', 'en' => 'Add items'],
        'suppliers.title' => ['vi' => 'Nhà cung cấp', 'en' => 'Suppliers'],
        'rfq.title' => ['vi' => 'Yêu cầu báo giá', 'en' => 'Request for Quote'],
        'bulk_orders' => ['vi' => 'Đặt hàng số lượng lớn', 'en' => 'Bulk orders'],
        'downloads' => ['vi' => 'Tải xuống', 'en' => 'Downloads'],
        'in_stock' => ['vi' => 'Còn hàng', 'en' => 'In stock'],
        'out_of_stock' => ['vi' => 'Hết hàng', 'en' => 'Out of stock'],
        'advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced search'],
        'close' => ['vi' => 'Đóng', 'en' => 'Close'],
        'keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
        'search_descriptions' => ['vi' => 'Tìm trong mô tả', 'en' => 'Search descriptions'],
        'use_quotes_help' => ['vi' => 'Sử dụng dấu ngoặc kép để tìm cụm từ chính xác', 'en' => 'Use quotes for exact phrase search'],
    ],
    
    // UI common keys
    'ui' => [
        'common.light_mode' => ['vi' => 'Chế độ sáng', 'en' => 'Light mode'],
        'common.dark_mode' => ['vi' => 'Chế độ tối', 'en' => 'Dark mode'],
        'common.popular_searches' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular searches'],
        'common.no_results_found' => ['vi' => 'Không tìm thấy kết quả', 'en' => 'No results found'],
        'common.auto_saving' => ['vi' => 'Tự động lưu...', 'en' => 'Auto saving...'],
        'language.switched_successfully' => ['vi' => 'Đã chuyển ngôn ngữ thành công', 'en' => 'Language switched successfully'],
        'language.switch_failed' => ['vi' => 'Chuyển ngôn ngữ thất bại', 'en' => 'Language switch failed'],
        'language.auto_detected' => ['vi' => 'Tự động phát hiện', 'en' => 'Auto detected'],
    ],
    
    // Forum keys
    'forum' => [
        'actions.login_to_follow' => ['vi' => 'Đăng nhập để theo dõi', 'en' => 'Login to follow'],
        'actions.error_occurred' => ['vi' => 'Đã xảy ra lỗi', 'en' => 'An error occurred'],
        'actions.request_error' => ['vi' => 'Lỗi yêu cầu', 'en' => 'Request error'],
        'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'no_threads' => ['vi' => 'Không có chủ đề nào', 'en' => 'No threads'],
        'search.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular categories'],
        'search.placeholder' => ['vi' => 'Tìm kiếm trong diễn đàn...', 'en' => 'Search in forum...'],
        'threads.sticky' => ['vi' => 'Ghim', 'en' => 'Sticky'],
        'edit.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'edit.update' => ['vi' => 'Cập nhật', 'en' => 'Update'],
    ],
    
    // Forums keys
    'forums' => [
        'actions.create_thread' => ['vi' => 'Tạo chủ đề', 'en' => 'Create thread'],
        'actions.new_thread' => ['vi' => 'Chủ đề mới', 'en' => 'New thread'],
        'actions.view_more' => ['vi' => 'Xem thêm', 'en' => 'View more'],
        'actions.clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear filters'],
        'actions.create_first_thread' => ['vi' => 'Tạo chủ đề đầu tiên', 'en' => 'Create first thread'],
        'threads.start_discussion' => ['vi' => 'Bắt đầu thảo luận', 'en' => 'Start discussion'],
    ],
    
    // Common keys
    'common' => [
        'marketplace' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
        'oldest' => ['vi' => 'Cũ nhất', 'en' => 'Oldest'],
        'most_commented' => ['vi' => 'Nhiều bình luận nhất', 'en' => 'Most commented'],
        'cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'create_showcase' => ['vi' => 'Tạo showcase', 'en' => 'Create showcase'],
        'showcase_reason' => ['vi' => 'Lý do showcase', 'en' => 'Showcase reason'],
        'no_showcases_found' => ['vi' => 'Không tìm thấy showcase nào', 'en' => 'No showcases found'],
        'create_first_showcase' => ['vi' => 'Tạo showcase đầu tiên', 'en' => 'Create first showcase'],
        'members.online_title' => ['vi' => 'Thành viên trực tuyến', 'en' => 'Online members'],
        'members.online_description' => ['vi' => 'Danh sách thành viên đang trực tuyến', 'en' => 'List of members currently online'],
        'members.online_now' => ['vi' => 'Đang trực tuyến', 'en' => 'Online now'],
        'members.staff' => ['vi' => 'Ban quản trị', 'en' => 'Staff'],
        'members.staff_title' => ['vi' => 'Ban quản trị', 'en' => 'Staff members'],
        'members.staff_description' => ['vi' => 'Danh sách ban quản trị cộng đồng', 'en' => 'List of community staff members'],
        'members.administrators' => ['vi' => 'Quản trị viên', 'en' => 'Administrators'],
        'members.moderators' => ['vi' => 'Điều hành viên', 'en' => 'Moderators'],
        'members.online_members_info' => ['vi' => 'thành viên đang trực tuyến', 'en' => 'members online'],
        'members.leaderboard_title' => ['vi' => 'Bảng xếp hạng', 'en' => 'Leaderboard'],
        'members.leaderboard_description' => ['vi' => 'Thành viên tích cực nhất', 'en' => 'Most active members'],
    ],
    
    // Content keys
    'content' => [
        'welcome' => ['vi' => 'Chào mừng', 'en' => 'Welcome'],
        'logged_in_message' => ['vi' => 'Bạn đã đăng nhập thành công!', 'en' => 'You are logged in successfully!'],
        'pages.community_rules' => ['vi' => 'Quy định cộng đồng', 'en' => 'Community rules'],
        'pages.contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
        'pages.about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About us'],
        'pages.terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of service'],
        'pages.privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy policy'],
    ],
    
    // Auth keys
    'auth' => [
        'register.security_note' => ['vi' => 'Thông tin của bạn được bảo mật', 'en' => 'Your information is secure'],
        'register.auto_saving' => ['vi' => 'Tự động lưu...', 'en' => 'Auto saving...'],
    ],
    
    // Showcase keys
    'showcase' => [
        'max_files_exceeded' => ['vi' => 'Vượt quá số file tối đa', 'en' => 'Maximum files exceeded'],
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

$totalAdded = 0;

foreach ($frontendKeys as $category => $keys) {
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
echo "Categories processed: " . count($frontendKeys) . "\n";

echo "\n✅ Frontend priority keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
