<?php

/**
 * ADD REMAINING HARDCODED STRINGS - 68 KEYS
 * Thêm tất cả hardcoded strings còn lại để đạt 100% coverage
 */

echo "=== ADDING REMAINING HARDCODED STRINGS - TARGET 100% COVERAGE ===\n\n";

// All missing keys organized by translation file
$translationKeys = [
    // Auth keys (4 keys) - thêm vào auth.php
    'auth_keys' => [
        'confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
        'password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
        'confirm' => ['vi' => 'Xác nhận', 'en' => 'Confirm'],
        'secure_area_message' => ['vi' => 'Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu trước khi tiếp tục.', 'en' => 'This is a secure area of the application. Please confirm your password before continuing.'],
    ],
    
    // Bookmarks keys (7 keys) - thêm vào bookmarks.php
    'bookmarks_keys' => [
        'thread_in' => ['vi' => 'Chủ đề trong', 'en' => 'Thread in'],
        'reply_in' => ['vi' => 'Trả lời trong', 'en' => 'Reply in'],
        'bookmarked_item' => ['vi' => 'Mục đã đánh dấu', 'en' => 'Bookmarked item'],
        'notes' => ['vi' => 'Ghi chú', 'en' => 'Notes'],
        'bookmarked' => ['vi' => 'Đã đánh dấu', 'en' => 'Bookmarked'],
        'remove' => ['vi' => 'Xóa', 'en' => 'Remove'],
        'help_text' => ['vi' => 'Đánh dấu chủ đề và bài viết để dễ dàng tìm thấy sau này.', 'en' => 'Bookmark threads and posts to find them easily later.'],
    ],
    
    // Common marketplace keys (7 keys) - thêm vào common.php
    'common_keys' => [
        'marketplace.in_stock' => ['vi' => 'Còn hàng', 'en' => 'In Stock'],
        'marketplace.out_of_stock' => ['vi' => 'Hết hàng', 'en' => 'Out of Stock'],
        'marketplace_actions.by' => ['vi' => 'bởi', 'en' => 'by'],
        'marketplace_actions.add_to_wishlist' => ['vi' => 'Thêm vào danh sách yêu thích', 'en' => 'Add to Wishlist'],
        'marketplace_actions.add_to_cart' => ['vi' => 'Thêm vào giỏ hàng', 'en' => 'Add to Cart'],
        'marketplace_actions.view_details' => ['vi' => 'Xem chi tiết', 'en' => 'View Details'],
        'marketplace_actions.quick_view' => ['vi' => 'Xem nhanh', 'en' => 'Quick View'],
    ],
    
    // UI Language keys (3 keys) - thêm vào ui.php
    'ui_keys' => [
        'language.switched_successfully' => ['vi' => 'Đã chuyển ngôn ngữ thành công', 'en' => 'Language switched successfully'],
        'language.switch_failed' => ['vi' => 'Chuyển ngôn ngữ thất bại', 'en' => 'Language switch failed'],
        'language.auto_detected' => ['vi' => 'Tự động phát hiện', 'en' => 'Auto detected'],
    ],
    
    // Features keys (6 keys) - thêm vào features.php
    'features_keys' => [
        'community.labels.events' => ['vi' => 'Sự kiện', 'en' => 'Events'],
        'community.labels.jobs' => ['vi' => 'Việc làm', 'en' => 'Jobs'],
        'supplier.labels.revenue' => ['vi' => 'Doanh thu', 'en' => 'Revenue'],
        'supplier.labels.orders' => ['vi' => 'Đơn hàng', 'en' => 'Orders'],
        'supplier.labels.customers' => ['vi' => 'Khách hàng', 'en' => 'Customers'],
        'supplier.labels.products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
    ],
    
    // Gallery keys (6 keys) - thêm vào gallery.php
    'gallery_keys' => [
        'select_file' => ['vi' => 'Chọn tệp', 'en' => 'Select File'],
        'title' => ['vi' => 'Tiêu đề', 'en' => 'Title'],
        'title_description' => ['vi' => 'Đặt tiêu đề mô tả cho phương tiện của bạn (tùy chọn).', 'en' => 'Give your media a descriptive title (optional).'],
        'description' => ['vi' => 'Mô tả', 'en' => 'Description'],
        'description_help' => ['vi' => 'Thêm mô tả cho phương tiện của bạn (tùy chọn).', 'en' => 'Add a description for your media (optional).'],
        'upload_media' => ['vi' => 'Tải lên phương tiện', 'en' => 'Upload Media'],
        'search_gallery' => ['vi' => 'Tìm kiếm thư viện...', 'en' => 'Search gallery...'],
        'uploaded_by' => ['vi' => 'Được tải lên bởi', 'en' => 'Uploaded by'],
        'by' => ['vi' => 'Bởi', 'en' => 'By'],
        'no_media_found' => ['vi' => 'Không tìm thấy phương tiện nào.', 'en' => 'No media items found.'],
    ],
    
    // Profile keys (11 keys) - thêm vào profile.php
    'profile_keys' => [
        'update_password' => ['vi' => 'Cập nhật mật khẩu', 'en' => 'Update Password'],
        'password_security_message' => ['vi' => 'Đảm bảo tài khoản của bạn sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn.', 'en' => 'Ensure your account is using a long, random password to stay secure.'],
        'current_password' => ['vi' => 'Mật khẩu hiện tại', 'en' => 'Current Password'],
        'new_password' => ['vi' => 'Mật khẩu mới', 'en' => 'New Password'],
        'delete_account' => ['vi' => 'Xóa tài khoản', 'en' => 'Delete Account'],
        'delete_warning' => ['vi' => 'Khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu sẽ bị xóa vĩnh viễn. Trước khi xóa tài khoản, vui lòng tải xuống bất kỳ dữ liệu hoặc thông tin nào bạn muốn giữ lại.', 'en' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'],
        'delete_confirmation' => ['vi' => 'Bạn có chắc chắn muốn xóa tài khoản của mình không?', 'en' => 'Are you sure you want to delete your account?'],
        'delete_password_confirmation' => ['vi' => 'Khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.', 'en' => 'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.'],
        'posts' => ['vi' => 'Bài viết hồ sơ', 'en' => 'Profile Posts'],
        'write_something_on' => ['vi' => 'Viết gì đó trên', 'en' => 'Write something on'],
        'post' => ['vi' => 'Đăng', 'en' => 'Post'],
        'no_posts_yet' => ['vi' => 'Chưa có bài viết hồ sơ nào.', 'en' => 'No profile posts yet.'],
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
    
    // Find the last closing bracket or parenthesis
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        $lastBracketPos = strrpos($content, ');');
    }
    if ($lastBracketPos === false) {
        echo "❌ Could not find closing bracket in $filePath\n";
        return false;
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
    'auth_keys' => 'auth',
    'bookmarks_keys' => 'bookmarks',
    'common_keys' => 'common',
    'ui_keys' => 'ui',
    'features_keys' => 'features',
    'gallery_keys' => 'gallery',
    'profile_keys' => 'profile',
];

$totalAdded = 0;

foreach ($translationKeys as $category => $keys) {
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
echo "Total hardcoded string keys added: $totalAdded\n";
echo "Categories processed: " . count($translationKeys) . "\n";
echo "Target: 100% coverage\n";

echo "\n✅ Remaining hardcoded strings addition completed at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Run scan to verify 100% coverage achieved.\n";
?>
