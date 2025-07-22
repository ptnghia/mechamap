<?php

/**
 * ADD SPECIFIC MISSING KEYS
 * Thêm các keys cụ thể còn thiếu từ các files frontend quan trọng (bỏ qua /admin)
 */

echo "=== ADDING SPECIFIC MISSING KEYS (EXCLUDING ADMIN) ===\n\n";

// Specific missing keys found in various frontend files
$specificMissingKeys = [
    // Pages and content keys
    'pages' => [
        'home_title' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        'about_title' => ['vi' => 'Giới thiệu', 'en' => 'About'],
        'contact_title' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
        'privacy_title' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
        'terms_title' => ['vi' => 'Điều khoản sử dụng', 'en' => 'Terms of Service'],
        'faq_title' => ['vi' => 'Câu hỏi thường gặp', 'en' => 'Frequently Asked Questions'],
        'help_title' => ['vi' => 'Trợ giúp', 'en' => 'Help'],
        'support_title' => ['vi' => 'Hỗ trợ', 'en' => 'Support'],
        'documentation_title' => ['vi' => 'Tài liệu', 'en' => 'Documentation'],
        'news_title' => ['vi' => 'Tin tức', 'en' => 'News'],
        'blog_title' => ['vi' => 'Blog', 'en' => 'Blog'],
        'events_title' => ['vi' => 'Sự kiện', 'en' => 'Events'],
        'careers_title' => ['vi' => 'Tuyển dụng', 'en' => 'Careers'],
        'partners_title' => ['vi' => 'Đối tác', 'en' => 'Partners'],
        'testimonials_title' => ['vi' => 'Đánh giá', 'en' => 'Testimonials'],
        'gallery_title' => ['vi' => 'Thư viện', 'en' => 'Gallery'],
        'portfolio_title' => ['vi' => 'Danh mục', 'en' => 'Portfolio'],
        'services_title' => ['vi' => 'Dịch vụ', 'en' => 'Services'],
        'products_title' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
        'solutions_title' => ['vi' => 'Giải pháp', 'en' => 'Solutions'],
    ],

    // Media and files keys
    'media' => [
        'upload_file' => ['vi' => 'Tải lên tệp', 'en' => 'Upload File'],
        'choose_file' => ['vi' => 'Chọn tệp', 'en' => 'Choose File'],
        'drag_drop' => ['vi' => 'Kéo thả tệp vào đây', 'en' => 'Drag and drop files here'],
        'file_size_limit' => ['vi' => 'Kích thước tệp tối đa', 'en' => 'Maximum file size'],
        'allowed_formats' => ['vi' => 'Định dạng cho phép', 'en' => 'Allowed formats'],
        'upload_progress' => ['vi' => 'Tiến trình tải lên', 'en' => 'Upload progress'],
        'upload_complete' => ['vi' => 'Tải lên hoàn tất', 'en' => 'Upload complete'],
        'upload_failed' => ['vi' => 'Tải lên thất bại', 'en' => 'Upload failed'],
        'file_too_large' => ['vi' => 'Tệp quá lớn', 'en' => 'File too large'],
        'invalid_format' => ['vi' => 'Định dạng không hợp lệ', 'en' => 'Invalid format'],
        'no_file_selected' => ['vi' => 'Chưa chọn tệp', 'en' => 'No file selected'],
        'replace_file' => ['vi' => 'Thay thế tệp', 'en' => 'Replace file'],
        'remove_file' => ['vi' => 'Xóa tệp', 'en' => 'Remove file'],
        'download_file' => ['vi' => 'Tải xuống tệp', 'en' => 'Download file'],
        'view_file' => ['vi' => 'Xem tệp', 'en' => 'View file'],
        'edit_image' => ['vi' => 'Chỉnh sửa ảnh', 'en' => 'Edit image'],
        'crop_image' => ['vi' => 'Cắt ảnh', 'en' => 'Crop image'],
        'resize_image' => ['vi' => 'Thay đổi kích thước ảnh', 'en' => 'Resize image'],
        'rotate_image' => ['vi' => 'Xoay ảnh', 'en' => 'Rotate image'],
        'image_gallery' => ['vi' => 'Thư viện ảnh', 'en' => 'Image gallery'],
        'video_player' => ['vi' => 'Trình phát video', 'en' => 'Video player'],
    ],

    // Validation and forms keys
    'validation' => [
        'field_required' => ['vi' => 'Trường này là bắt buộc', 'en' => 'This field is required'],
        'invalid_email' => ['vi' => 'Email không hợp lệ', 'en' => 'Invalid email address'],
        'password_too_short' => ['vi' => 'Mật khẩu quá ngắn', 'en' => 'Password too short'],
        'passwords_not_match' => ['vi' => 'Mật khẩu không khớp', 'en' => 'Passwords do not match'],
        'invalid_url' => ['vi' => 'URL không hợp lệ', 'en' => 'Invalid URL'],
        'invalid_phone' => ['vi' => 'Số điện thoại không hợp lệ', 'en' => 'Invalid phone number'],
        'invalid_date' => ['vi' => 'Ngày không hợp lệ', 'en' => 'Invalid date'],
        'value_too_small' => ['vi' => 'Giá trị quá nhỏ', 'en' => 'Value too small'],
        'value_too_large' => ['vi' => 'Giá trị quá lớn', 'en' => 'Value too large'],
        'text_too_short' => ['vi' => 'Văn bản quá ngắn', 'en' => 'Text too short'],
        'text_too_long' => ['vi' => 'Văn bản quá dài', 'en' => 'Text too long'],
        'invalid_format' => ['vi' => 'Định dạng không hợp lệ', 'en' => 'Invalid format'],
        'already_exists' => ['vi' => 'Đã tồn tại', 'en' => 'Already exists'],
        'not_found' => ['vi' => 'Không tìm thấy', 'en' => 'Not found'],
        'access_denied' => ['vi' => 'Truy cập bị từ chối', 'en' => 'Access denied'],
        'operation_failed' => ['vi' => 'Thao tác thất bại', 'en' => 'Operation failed'],
        'operation_successful' => ['vi' => 'Thao tác thành công', 'en' => 'Operation successful'],
        'please_try_again' => ['vi' => 'Vui lòng thử lại', 'en' => 'Please try again'],
        'something_went_wrong' => ['vi' => 'Có lỗi xảy ra', 'en' => 'Something went wrong'],
        'connection_error' => ['vi' => 'Lỗi kết nối', 'en' => 'Connection error'],
    ],

    // Notifications and messages keys
    'notifications' => [
        'new_message' => ['vi' => 'Tin nhắn mới', 'en' => 'New message'],
        'new_comment' => ['vi' => 'Bình luận mới', 'en' => 'New comment'],
        'new_reply' => ['vi' => 'Phản hồi mới', 'en' => 'New reply'],
        'new_follower' => ['vi' => 'Người theo dõi mới', 'en' => 'New follower'],
        'new_like' => ['vi' => 'Lượt thích mới', 'en' => 'New like'],
        'new_share' => ['vi' => 'Chia sẻ mới', 'en' => 'New share'],
        'new_mention' => ['vi' => 'Nhắc đến mới', 'en' => 'New mention'],
        'new_invitation' => ['vi' => 'Lời mời mới', 'en' => 'New invitation'],
        'new_request' => ['vi' => 'Yêu cầu mới', 'en' => 'New request'],
        'system_update' => ['vi' => 'Cập nhật hệ thống', 'en' => 'System update'],
        'maintenance_notice' => ['vi' => 'Thông báo bảo trì', 'en' => 'Maintenance notice'],
        'security_alert' => ['vi' => 'Cảnh báo bảo mật', 'en' => 'Security alert'],
        'account_verified' => ['vi' => 'Tài khoản đã xác thực', 'en' => 'Account verified'],
        'password_changed' => ['vi' => 'Mật khẩu đã thay đổi', 'en' => 'Password changed'],
        'email_verified' => ['vi' => 'Email đã xác thực', 'en' => 'Email verified'],
        'profile_updated' => ['vi' => 'Hồ sơ đã cập nhật', 'en' => 'Profile updated'],
        'settings_saved' => ['vi' => 'Cài đặt đã lưu', 'en' => 'Settings saved'],
        'subscription_expired' => ['vi' => 'Đăng ký đã hết hạn', 'en' => 'Subscription expired'],
        'payment_received' => ['vi' => 'Đã nhận thanh toán', 'en' => 'Payment received'],
        'order_confirmed' => ['vi' => 'Đơn hàng đã xác nhận', 'en' => 'Order confirmed'],
    ],

    // Time and date keys
    'time' => [
        'just_now' => ['vi' => 'Vừa xong', 'en' => 'Just now'],
        'minutes_ago' => ['vi' => ':count phút trước', 'en' => ':count minutes ago'],
        'hours_ago' => ['vi' => ':count giờ trước', 'en' => ':count hours ago'],
        'days_ago' => ['vi' => ':count ngày trước', 'en' => ':count days ago'],
        'weeks_ago' => ['vi' => ':count tuần trước', 'en' => ':count weeks ago'],
        'months_ago' => ['vi' => ':count tháng trước', 'en' => ':count months ago'],
        'years_ago' => ['vi' => ':count năm trước', 'en' => ':count years ago'],
        'in_minutes' => ['vi' => 'trong :count phút', 'en' => 'in :count minutes'],
        'in_hours' => ['vi' => 'trong :count giờ', 'en' => 'in :count hours'],
        'in_days' => ['vi' => 'trong :count ngày', 'en' => 'in :count days'],
        'in_weeks' => ['vi' => 'trong :count tuần', 'en' => 'in :count weeks'],
        'in_months' => ['vi' => 'trong :count tháng', 'en' => 'in :count months'],
        'in_years' => ['vi' => 'trong :count năm', 'en' => 'in :count years'],
        'last_seen' => ['vi' => 'Lần cuối truy cập', 'en' => 'Last seen'],
        'online_now' => ['vi' => 'Đang trực tuyến', 'en' => 'Online now'],
        'offline' => ['vi' => 'Ngoại tuyến', 'en' => 'Offline'],
        'never' => ['vi' => 'Chưa bao giờ', 'en' => 'Never'],
        'always' => ['vi' => 'Luôn luôn', 'en' => 'Always'],
        'sometimes' => ['vi' => 'Thỉnh thoảng', 'en' => 'Sometimes'],
        'frequently' => ['vi' => 'Thường xuyên', 'en' => 'Frequently'],
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

foreach ($specificMissingKeys as $category => $keys) {
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
echo "Total specific keys added: $totalAdded\n";
echo "Categories processed: " . count($specificMissingKeys) . "\n";

echo "\n✅ Specific missing keys addition completed at " . date('Y-m-d H:i:s') . "\n";
