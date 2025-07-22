<?php

/**
 * CREATE MISSING ENGLISH TRANSLATION FILES
 * Tạo các file EN còn thiếu dựa trên file VI
 */

echo "=== CREATING MISSING ENGLISH TRANSLATION FILES ===\n\n";

$viDir = __DIR__ . '/resources/lang/vi/';
$enDir = __DIR__ . '/resources/lang/en/';

// Ensure EN directory exists
if (!is_dir($enDir)) {
    mkdir($enDir, 0755, true);
    echo "✅ Created EN directory\n";
}

// Get all VI files
$viFiles = glob($viDir . '*.php');
$missingFiles = [];
$createdFiles = [];

foreach ($viFiles as $viFile) {
    $filename = basename($viFile);
    $enFile = $enDir . $filename;
    
    if (!file_exists($enFile)) {
        $missingFiles[] = $filename;
    }
}

echo "📊 Found " . count($missingFiles) . " missing EN files:\n";
foreach ($missingFiles as $file) {
    echo "   - $file\n";
}
echo "\n";

// Function to translate Vietnamese to English (basic mapping)
function translateViToEn($text) {
    $translations = [
        // Common buttons
        'Lưu' => 'Save',
        'Hủy' => 'Cancel',
        'Xóa' => 'Delete',
        'Sửa' => 'Edit',
        'Tạo' => 'Create',
        'Thêm' => 'Add',
        'Xóa bỏ' => 'Remove',
        'Gửi' => 'Send',
        'Quay lại' => 'Back',
        'Tiếp theo' => 'Next',
        'Trước' => 'Previous',
        'Đóng' => 'Close',
        'Mở' => 'Open',
        'Xem' => 'View',
        'Tìm kiếm' => 'Search',
        'Phổ biến' => 'Popular',
        'Mới nhất' => 'Latest',
        'Lọc' => 'Filter',
        'Sắp xếp' => 'Sort',
        
        // Status
        'Hoạt động' => 'Active',
        'Không hoạt động' => 'Inactive',
        'Trực tuyến' => 'Online',
        'Ngoại tuyến' => 'Offline',
        'Đã duyệt' => 'Approved',
        'Đã từ chối' => 'Rejected',
        'Đang chờ' => 'Pending',
        'Hoàn thành' => 'Completed',
        'Đang thực hiện' => 'In Progress',
        
        // Common words
        'Trang chủ' => 'Home',
        'Diễn đàn' => 'Forums',
        'Thị trường' => 'Marketplace',
        'Cộng đồng' => 'Community',
        'Thành viên' => 'Members',
        'Quản trị viên' => 'Administrator',
        'Kiểm duyệt viên' => 'Moderator',
        'Người dùng' => 'User',
        'Tài khoản' => 'Account',
        'Hồ sơ' => 'Profile',
        'Cài đặt' => 'Settings',
        'Thông báo' => 'Notifications',
        'Tin nhắn' => 'Messages',
        'Đăng nhập' => 'Login',
        'Đăng ký' => 'Register',
        'Đăng xuất' => 'Logout',
        'Mật khẩu' => 'Password',
        'Email' => 'Email',
        'Tên' => 'Name',
        'Tiêu đề' => 'Title',
        'Mô tả' => 'Description',
        'Nội dung' => 'Content',
        'Danh mục' => 'Category',
        'Thẻ' => 'Tags',
        'Ngày' => 'Date',
        'Thời gian' => 'Time',
        'Trạng thái' => 'Status',
        'Hành động' => 'Actions',
        'Chi tiết' => 'Details',
        'Thông tin' => 'Information',
        'Kết quả' => 'Results',
        'Tìm thấy' => 'Found',
        'Không tìm thấy' => 'Not found',
        'Tất cả' => 'All',
        'Không có' => 'None',
        'Có' => 'Yes',
        'Không' => 'No',
        'Đúng' => 'True',
        'Sai' => 'False',
        'Bật' => 'Enable',
        'Tắt' => 'Disable',
        'Hiển thị' => 'Show',
        'Ẩn' => 'Hide',
        'Công khai' => 'Public',
        'Riêng tư' => 'Private',
        'Chọn' => 'Select',
        'Chọn tất cả' => 'Select All',
        'Xác nhận' => 'Confirm',
        'Hủy bỏ' => 'Cancel',
        'Áp dụng' => 'Apply',
        'Làm mới' => 'Refresh',
        'Tải lại' => 'Reload',
        'Tải xuống' => 'Download',
        'Tải lên' => 'Upload',
        'Chia sẻ' => 'Share',
        'Sao chép' => 'Copy',
        'Dán' => 'Paste',
        'Cắt' => 'Cut',
        'In' => 'Print',
        'Xuất' => 'Export',
        'Nhập' => 'Import',
        'Trợ giúp' => 'Help',
        'Hỗ trợ' => 'Support',
        'Liên hệ' => 'Contact',
        'Về chúng tôi' => 'About Us',
        'Điều khoản' => 'Terms',
        'Chính sách' => 'Policy',
        'Bảo mật' => 'Privacy',
        'Bản quyền' => 'Copyright',
        'Phiên bản' => 'Version',
        'Cập nhật' => 'Update',
        'Nâng cấp' => 'Upgrade',
        'Cài đặt' => 'Install',
        'Gỡ cài đặt' => 'Uninstall',
        'Kích hoạt' => 'Activate',
        'Vô hiệu hóa' => 'Deactivate',
        'Xuất bản' => 'Publish',
        'Hủy xuất bản' => 'Unpublish',
        'Lưu trữ' => 'Archive',
        'Khôi phục' => 'Restore',
        'Sao lưu' => 'Backup',
        'Nhân bản' => 'Duplicate',
        'Di chuyển' => 'Move',
        'Đổi tên' => 'Rename',
        'Xem trước' => 'Preview',
        'Toàn màn hình' => 'Fullscreen',
        'Thu nhỏ' => 'Minimize',
        'Phóng to' => 'Maximize',
        'Mở rộng' => 'Expand',
        'Thu gọn' => 'Collapse',
        'Xem thêm' => 'Show More',
        'Ẩn bớt' => 'Show Less',
        'Tải thêm' => 'Load More',
        'Xem tất cả' => 'View All',
        'Chuyển đổi' => 'Toggle',
        'Bỏ chọn tất cả' => 'Deselect All',
        'Bỏ qua' => 'Skip',
        'Hoàn thành' => 'Finish',
        'Xong' => 'Done',
        'Bắt đầu' => 'Start',
        'Dừng' => 'Stop',
        'Tạm dừng' => 'Pause',
        'Tiếp tục' => 'Resume',
        'Phát' => 'Play',
        'Phát lại' => 'Replay',
        'Ghi' => 'Record',
        'Đánh dấu' => 'Bookmark',
        'Yêu thích' => 'Favorite',
        'Thích' => 'Like',
        'Không thích' => 'Dislike',
        'Theo dõi' => 'Follow',
        'Bỏ theo dõi' => 'Unfollow',
        'Đăng ký' => 'Subscribe',
        'Hủy đăng ký' => 'Unsubscribe',
        'Tham gia' => 'Join',
        'Rời khỏi' => 'Leave',
        'Mời' => 'Invite',
        'Chấp nhận' => 'Accept',
        'Từ chối' => 'Decline',
        'Chặn' => 'Block',
        'Bỏ chặn' => 'Unblock',
        'Báo cáo' => 'Report',
        'Gắn cờ' => 'Flag',
        'Ghim' => 'Pin',
        'Bỏ ghim' => 'Unpin',
        'Khóa' => 'Lock',
        'Mở khóa' => 'Unlock',
        'Nổi bật' => 'Feature',
        'Bỏ nổi bật' => 'Unfeature',
        'Dính' => 'Sticky',
        'Bỏ dính' => 'Unsticky',
        'Đánh dấu đã đọc' => 'Mark Read',
        'Đánh dấu chưa đọc' => 'Mark Unread',
        'Trả lời' => 'Reply',
        'Trích dẫn' => 'Quote',
        'Nhắc đến' => 'Mention',
        'Gắn thẻ' => 'Tag',
        'Bỏ thẻ' => 'Untag',
        'Đánh giá' => 'Rate',
        'Nhận xét' => 'Review',
        'Bình luận' => 'Comment',
        'Bình chọn' => 'Vote',
        'Bình chọn tích cực' => 'Upvote',
        'Bình chọn tiêu cực' => 'Downvote',
        'Theo dõi' => 'Watch',
        'Bỏ theo dõi' => 'Unwatch',
        'Thông báo' => 'Notify',
        'Tắt tiếng' => 'Mute',
        'Bật tiếng' => 'Unmute',
    ];
    
    return $translations[$text] ?? $text;
}

// Function to recursively translate array
function translateArray($array) {
    $result = [];
    
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result[$key] = translateArray($value);
        } elseif (is_string($value)) {
            $result[$key] = translateViToEn($value);
        } else {
            $result[$key] = $value;
        }
    }
    
    return $result;
}

// Create missing files
foreach ($missingFiles as $filename) {
    $viFile = $viDir . $filename;
    $enFile = $enDir . $filename;
    
    echo "🔄 Processing $filename...\n";
    
    try {
        // Load VI file
        $viContent = include $viFile;
        
        if (!is_array($viContent)) {
            echo "❌ Error: $filename does not return an array\n";
            continue;
        }
        
        // Translate content
        $enContent = translateArray($viContent);
        
        // Generate EN file content
        $fileContent = "<?php\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * " . ucfirst(str_replace('.php', '', $filename)) . " Translation File - English (AUTO-GENERATED)\n";
        $fileContent .= " * Auto-generated from Vietnamese file\n";
        $fileContent .= " * Auto-updated: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= " */\n\n";
        $fileContent .= "return " . var_export($enContent, true) . ";\n";
        
        // Write EN file
        if (file_put_contents($enFile, $fileContent)) {
            echo "✅ Created $filename\n";
            $createdFiles[] = $filename;
        } else {
            echo "❌ Failed to create $filename\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error processing $filename: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total missing files: " . count($missingFiles) . "\n";
echo "Successfully created: " . count($createdFiles) . "\n";
echo "Failed: " . (count($missingFiles) - count($createdFiles)) . "\n\n";

if (!empty($createdFiles)) {
    echo "✅ Created files:\n";
    foreach ($createdFiles as $file) {
        echo "   - $file\n";
    }
}

echo "\n✅ Process completed at " . date('Y-m-d H:i:s') . "\n";
?>
