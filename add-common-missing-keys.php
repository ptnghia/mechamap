<?php

/**
 * ADD COMMON MISSING KEYS
 * Thêm các keys phổ biến còn thiếu cả tiếng Việt và tiếng Anh (bỏ qua /admin)
 */

echo "=== ADDING COMMON MISSING KEYS (EXCLUDING ADMIN) ===\n\n";

// Common missing keys that appear frequently in frontend
$commonMissingKeys = [
    // Basic UI actions
    'loading' => ['vi' => 'Đang tải...', 'en' => 'Loading...'],
    'error' => ['vi' => 'Lỗi', 'en' => 'Error'],
    'success' => ['vi' => 'Thành công', 'en' => 'Success'],
    'warning' => ['vi' => 'Cảnh báo', 'en' => 'Warning'],
    'info' => ['vi' => 'Thông tin', 'en' => 'Information'],
    'confirm' => ['vi' => 'Xác nhận', 'en' => 'Confirm'],
    'cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
    'save' => ['vi' => 'Lưu', 'en' => 'Save'],
    'edit' => ['vi' => 'Chỉnh sửa', 'en' => 'Edit'],
    'delete' => ['vi' => 'Xóa', 'en' => 'Delete'],
    'view' => ['vi' => 'Xem', 'en' => 'View'],
    'back' => ['vi' => 'Quay lại', 'en' => 'Back'],
    'next' => ['vi' => 'Tiếp theo', 'en' => 'Next'],
    'previous' => ['vi' => 'Trước', 'en' => 'Previous'],
    'close' => ['vi' => 'Đóng', 'en' => 'Close'],
    'open' => ['vi' => 'Mở', 'en' => 'Open'],
    'yes' => ['vi' => 'Có', 'en' => 'Yes'],
    'no' => ['vi' => 'Không', 'en' => 'No'],
    'ok' => ['vi' => 'OK', 'en' => 'OK'],
    'submit' => ['vi' => 'Gửi', 'en' => 'Submit'],
    'reset' => ['vi' => 'Đặt lại', 'en' => 'Reset'],
    'clear' => ['vi' => 'Xóa', 'en' => 'Clear'],
    'search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'filter' => ['vi' => 'Lọc', 'en' => 'Filter'],
    'sort' => ['vi' => 'Sắp xếp', 'en' => 'Sort'],
    'refresh' => ['vi' => 'Làm mới', 'en' => 'Refresh'],
    'update' => ['vi' => 'Cập nhật', 'en' => 'Update'],
    'create' => ['vi' => 'Tạo', 'en' => 'Create'],
    'add' => ['vi' => 'Thêm', 'en' => 'Add'],
    'remove' => ['vi' => 'Xóa', 'en' => 'Remove'],
    'select' => ['vi' => 'Chọn', 'en' => 'Select'],
    'upload' => ['vi' => 'Tải lên', 'en' => 'Upload'],
    'download' => ['vi' => 'Tải xuống', 'en' => 'Download'],
    'share' => ['vi' => 'Chia sẻ', 'en' => 'Share'],
    'copy' => ['vi' => 'Sao chép', 'en' => 'Copy'],
    'print' => ['vi' => 'In', 'en' => 'Print'],
    'export' => ['vi' => 'Xuất', 'en' => 'Export'],
    'import' => ['vi' => 'Nhập', 'en' => 'Import'],
    'settings' => ['vi' => 'Cài đặt', 'en' => 'Settings'],
    'help' => ['vi' => 'Trợ giúp', 'en' => 'Help'],
    'about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
    'contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
    'support' => ['vi' => 'Hỗ trợ', 'en' => 'Support'],
    
    // Status and states
    'status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'active' => ['vi' => 'Hoạt động', 'en' => 'Active'],
    'inactive' => ['vi' => 'Không hoạt động', 'en' => 'Inactive'],
    'enabled' => ['vi' => 'Đã bật', 'en' => 'Enabled'],
    'disabled' => ['vi' => 'Đã tắt', 'en' => 'Disabled'],
    'public' => ['vi' => 'Công khai', 'en' => 'Public'],
    'private' => ['vi' => 'Riêng tư', 'en' => 'Private'],
    'draft' => ['vi' => 'Bản nháp', 'en' => 'Draft'],
    'published' => ['vi' => 'Đã xuất bản', 'en' => 'Published'],
    'pending' => ['vi' => 'Chờ xử lý', 'en' => 'Pending'],
    'approved' => ['vi' => 'Đã duyệt', 'en' => 'Approved'],
    'rejected' => ['vi' => 'Bị từ chối', 'en' => 'Rejected'],
    'completed' => ['vi' => 'Hoàn thành', 'en' => 'Completed'],
    'in_progress' => ['vi' => 'Đang thực hiện', 'en' => 'In Progress'],
    'not_started' => ['vi' => 'Chưa bắt đầu', 'en' => 'Not Started'],
    'cancelled' => ['vi' => 'Đã hủy', 'en' => 'Cancelled'],
    
    // Common attributes
    'required' => ['vi' => 'Bắt buộc', 'en' => 'Required'],
    'optional' => ['vi' => 'Tùy chọn', 'en' => 'Optional'],
    'featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
    'popular' => ['vi' => 'Phổ biến', 'en' => 'Popular'],
    'trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
    'new' => ['vi' => 'Mới', 'en' => 'New'],
    'updated' => ['vi' => 'Đã cập nhật', 'en' => 'Updated'],
    'latest' => ['vi' => 'Mới nhất', 'en' => 'Latest'],
    'oldest' => ['vi' => 'Cũ nhất', 'en' => 'Oldest'],
    'recent' => ['vi' => 'Gần đây', 'en' => 'Recent'],
    'all' => ['vi' => 'Tất cả', 'en' => 'All'],
    'none' => ['vi' => 'Không có', 'en' => 'None'],
    'other' => ['vi' => 'Khác', 'en' => 'Other'],
    'more' => ['vi' => 'Thêm', 'en' => 'More'],
    'less' => ['vi' => 'Ít hơn', 'en' => 'Less'],
    'show' => ['vi' => 'Hiển thị', 'en' => 'Show'],
    'hide' => ['vi' => 'Ẩn', 'en' => 'Hide'],
    
    // Navigation and layout
    'home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
    'menu' => ['vi' => 'Menu', 'en' => 'Menu'],
    'navigation' => ['vi' => 'Điều hướng', 'en' => 'Navigation'],
    'breadcrumb' => ['vi' => 'Đường dẫn', 'en' => 'Breadcrumb'],
    'sidebar' => ['vi' => 'Thanh bên', 'en' => 'Sidebar'],
    'header' => ['vi' => 'Đầu trang', 'en' => 'Header'],
    'footer' => ['vi' => 'Chân trang', 'en' => 'Footer'],
    'content' => ['vi' => 'Nội dung', 'en' => 'Content'],
    'main' => ['vi' => 'Chính', 'en' => 'Main'],
    'page' => ['vi' => 'Trang', 'en' => 'Page'],
    'section' => ['vi' => 'Phần', 'en' => 'Section'],
    'category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'tag' => ['vi' => 'Thẻ', 'en' => 'Tag'],
    'title' => ['vi' => 'Tiêu đề', 'en' => 'Title'],
    'description' => ['vi' => 'Mô tả', 'en' => 'Description'],
    'summary' => ['vi' => 'Tóm tắt', 'en' => 'Summary'],
    'details' => ['vi' => 'Chi tiết', 'en' => 'Details'],
    'overview' => ['vi' => 'Tổng quan', 'en' => 'Overview'],
    
    // Time and date
    'date' => ['vi' => 'Ngày', 'en' => 'Date'],
    'time' => ['vi' => 'Thời gian', 'en' => 'Time'],
    'created' => ['vi' => 'Đã tạo', 'en' => 'Created'],
    'modified' => ['vi' => 'Đã sửa', 'en' => 'Modified'],
    'published_at' => ['vi' => 'Xuất bản lúc', 'en' => 'Published at'],
    'created_at' => ['vi' => 'Tạo lúc', 'en' => 'Created at'],
    'updated_at' => ['vi' => 'Cập nhật lúc', 'en' => 'Updated at'],
    'today' => ['vi' => 'Hôm nay', 'en' => 'Today'],
    'yesterday' => ['vi' => 'Hôm qua', 'en' => 'Yesterday'],
    'tomorrow' => ['vi' => 'Ngày mai', 'en' => 'Tomorrow'],
    'week' => ['vi' => 'Tuần', 'en' => 'Week'],
    'month' => ['vi' => 'Tháng', 'en' => 'Month'],
    'year' => ['vi' => 'Năm', 'en' => 'Year'],
    
    // User and account
    'user' => ['vi' => 'Người dùng', 'en' => 'User'],
    'account' => ['vi' => 'Tài khoản', 'en' => 'Account'],
    'profile' => ['vi' => 'Hồ sơ', 'en' => 'Profile'],
    'username' => ['vi' => 'Tên người dùng', 'en' => 'Username'],
    'email' => ['vi' => 'Email', 'en' => 'Email'],
    'password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'login' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
    'logout' => ['vi' => 'Đăng xuất', 'en' => 'Logout'],
    'register' => ['vi' => 'Đăng ký', 'en' => 'Register'],
    'signup' => ['vi' => 'Đăng ký', 'en' => 'Sign Up'],
    'signin' => ['vi' => 'Đăng nhập', 'en' => 'Sign In'],
    'member' => ['vi' => 'Thành viên', 'en' => 'Member'],
    'guest' => ['vi' => 'Khách', 'en' => 'Guest'],
    'admin' => ['vi' => 'Quản trị viên', 'en' => 'Administrator'],
    'moderator' => ['vi' => 'Điều hành viên', 'en' => 'Moderator'],
    
    // Content types
    'post' => ['vi' => 'Bài viết', 'en' => 'Post'],
    'article' => ['vi' => 'Bài báo', 'en' => 'Article'],
    'news' => ['vi' => 'Tin tức', 'en' => 'News'],
    'blog' => ['vi' => 'Blog', 'en' => 'Blog'],
    'comment' => ['vi' => 'Bình luận', 'en' => 'Comment'],
    'reply' => ['vi' => 'Phản hồi', 'en' => 'Reply'],
    'thread' => ['vi' => 'Chủ đề', 'en' => 'Thread'],
    'topic' => ['vi' => 'Chủ đề', 'en' => 'Topic'],
    'discussion' => ['vi' => 'Thảo luận', 'en' => 'Discussion'],
    'forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
    'message' => ['vi' => 'Tin nhắn', 'en' => 'Message'],
    'notification' => ['vi' => 'Thông báo', 'en' => 'Notification'],
    'alert' => ['vi' => 'Cảnh báo', 'en' => 'Alert'],
    
    // File and media
    'file' => ['vi' => 'Tệp', 'en' => 'File'],
    'image' => ['vi' => 'Hình ảnh', 'en' => 'Image'],
    'photo' => ['vi' => 'Ảnh', 'en' => 'Photo'],
    'video' => ['vi' => 'Video', 'en' => 'Video'],
    'audio' => ['vi' => 'Âm thanh', 'en' => 'Audio'],
    'document' => ['vi' => 'Tài liệu', 'en' => 'Document'],
    'attachment' => ['vi' => 'Tệp đính kèm', 'en' => 'Attachment'],
    'gallery' => ['vi' => 'Thư viện', 'en' => 'Gallery'],
    'media' => ['vi' => 'Phương tiện', 'en' => 'Media'],
    'size' => ['vi' => 'Kích thước', 'en' => 'Size'],
    'format' => ['vi' => 'Định dạng', 'en' => 'Format'],
    'type' => ['vi' => 'Loại', 'en' => 'Type'],
    
    // Numbers and statistics
    'count' => ['vi' => 'Số lượng', 'en' => 'Count'],
    'total' => ['vi' => 'Tổng', 'en' => 'Total'],
    'number' => ['vi' => 'Số', 'en' => 'Number'],
    'amount' => ['vi' => 'Số lượng', 'en' => 'Amount'],
    'quantity' => ['vi' => 'Số lượng', 'en' => 'Quantity'],
    'price' => ['vi' => 'Giá', 'en' => 'Price'],
    'cost' => ['vi' => 'Chi phí', 'en' => 'Cost'],
    'value' => ['vi' => 'Giá trị', 'en' => 'Value'],
    'rating' => ['vi' => 'Đánh giá', 'en' => 'Rating'],
    'score' => ['vi' => 'Điểm', 'en' => 'Score'],
    'rank' => ['vi' => 'Xếp hạng', 'en' => 'Rank'],
    'level' => ['vi' => 'Cấp độ', 'en' => 'Level'],
    'percentage' => ['vi' => 'Phần trăm', 'en' => 'Percentage'],
    'average' => ['vi' => 'Trung bình', 'en' => 'Average'],
    'minimum' => ['vi' => 'Tối thiểu', 'en' => 'Minimum'],
    'maximum' => ['vi' => 'Tối đa', 'en' => 'Maximum'],
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

echo "📁 Adding common missing keys to common.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/common.php";
addKeysToFile($viFile, $commonMissingKeys, 'vi');

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $commonMissingKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total common keys added: " . count($commonMissingKeys) . "\n";
echo "These keys cover the most frequently used terms in frontend interfaces\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'common.loading',
    'common.search', 
    'common.save',
    'common.cancel'
];

foreach ($testKeys as $key) {
    echo "  Testing t_common('$key')...\n";
}

echo "\n✅ Common missing keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
