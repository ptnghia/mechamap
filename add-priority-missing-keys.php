<?php

/**
 * ADD PRIORITY MISSING KEYS
 * Thêm các keys thiếu quan trọng nhất trước
 */

echo "=== ADDING PRIORITY MISSING KEYS ===\n\n";

// Priority keys to add first (most commonly used)
$priorityKeys = [
    // Common category - most used
    'common' => [
        'messages.processing' => ['vi' => 'Đang xử lý...', 'en' => 'Processing...'],
        'messages.error_occurred' => ['vi' => 'Đã xảy ra lỗi', 'en' => 'An error occurred'],
        'marketplace' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
        'members.list_title' => ['vi' => 'Danh sách thành viên', 'en' => 'Members List'],
        'members.list_description' => ['vi' => 'Tất cả thành viên trong cộng đồng', 'en' => 'All members in the community'],
        'members.search_placeholder' => ['vi' => 'Tìm kiếm thành viên...', 'en' => 'Search members...'],
        'members.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'members.list_view' => ['vi' => 'Xem danh sách', 'en' => 'List view'],
        'members.grid_view' => ['vi' => 'Xem lưới', 'en' => 'Grid view'],
        'members.all_members' => ['vi' => 'Tất cả thành viên', 'en' => 'All members'],
    ],

    // UI category - interface elements
    'ui' => [
        'common.light_mode' => ['vi' => 'Chế độ sáng', 'en' => 'Light mode'],
        'common.dark_mode' => ['vi' => 'Chế độ tối', 'en' => 'Dark mode'],
        'common.popular_searches' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular searches'],
        'common.no_results_found' => ['vi' => 'Không tìm thấy kết quả', 'en' => 'No results found'],
        'common.auto_saving' => ['vi' => 'Tự động lưu...', 'en' => 'Auto saving...'],
    ],

    // Forum category - forum functionality
    'forum' => [
        'actions.login_to_follow' => ['vi' => 'Đăng nhập để theo dõi', 'en' => 'Login to follow'],
        'actions.error_occurred' => ['vi' => 'Đã xảy ra lỗi', 'en' => 'An error occurred'],
        'actions.request_error' => ['vi' => 'Lỗi yêu cầu', 'en' => 'Request error'],
        'threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
        'no_threads' => ['vi' => 'Không có chủ đề nào', 'en' => 'No threads'],
        'search.popular_categories' => ['vi' => 'Danh mục phổ biến', 'en' => 'Popular categories'],
        'edit.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'edit.update' => ['vi' => 'Cập nhật', 'en' => 'Update'],
        'search.placeholder' => ['vi' => 'Tìm kiếm trong diễn đàn...', 'en' => 'Search in forum...'],
        'threads.sticky' => ['vi' => 'Ghim', 'en' => 'Sticky'],
    ],

    // Forums category - forum management
    'forums' => [
        'actions.create_thread' => ['vi' => 'Tạo chủ đề', 'en' => 'Create thread'],
        'actions.new_thread' => ['vi' => 'Chủ đề mới', 'en' => 'New thread'],
        'actions.view_more' => ['vi' => 'Xem thêm', 'en' => 'View more'],
        'actions.clear_filters' => ['vi' => 'Xóa bộ lọc', 'en' => 'Clear filters'],
        'actions.create_first_thread' => ['vi' => 'Tạo chủ đề đầu tiên', 'en' => 'Create first thread'],
        'threads.start_discussion' => ['vi' => 'Bắt đầu thảo luận', 'en' => 'Start discussion'],
    ],

    // Content category - page content
    'content' => [
        'welcome' => ['vi' => 'Chào mừng', 'en' => 'Welcome'],
        'logged_in_message' => ['vi' => 'Bạn đã đăng nhập thành công!', 'en' => 'You are logged in successfully!'],
        'pages.community_rules' => ['vi' => 'Quy định cộng đồng', 'en' => 'Community rules'],
        'pages.contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
        'pages.about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About us'],
        'pages.terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of service'],
        'pages.privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy policy'],
    ],

    // Marketplace category - marketplace functionality
    'marketplace' => [
        'cart.empty_message' => ['vi' => 'Giỏ hàng trống', 'en' => 'Cart is empty'],
        'cart.add_items' => ['vi' => 'Thêm sản phẩm', 'en' => 'Add items'],
        'suppliers.title' => ['vi' => 'Nhà cung cấp', 'en' => 'Suppliers'],
        'rfq.title' => ['vi' => 'Yêu cầu báo giá', 'en' => 'Request for Quote'],
        'bulk_orders' => ['vi' => 'Đặt hàng số lượng lớn', 'en' => 'Bulk orders'],
        'downloads' => ['vi' => 'Tải xuống', 'en' => 'Downloads'],
    ],

    // Auth category - authentication
    'auth' => [
        'register.security_note' => ['vi' => 'Thông tin của bạn được bảo mật', 'en' => 'Your information is secure'],
        'register.auto_saving' => ['vi' => 'Tự động lưu...', 'en' => 'Auto saving...'],
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
    foreach ($keys as $key => $translations) {
        if (isset($translations[$lang])) {
            $value = $translations[$lang];
            $newKeysString .= "  '$key' => '$value',\n";
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
        echo "✅ Added " . count($keys) . " keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

$totalAdded = 0;

foreach ($priorityKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";

    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$category.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }

    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$category.php";
    if (addKeysToFile($enFile, $keys, 'en')) {
        // Don't double count
    }

    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total keys added: $totalAdded\n";
echo "Categories processed: " . count($priorityKeys) . "\n";

echo "\n✅ Priority keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
