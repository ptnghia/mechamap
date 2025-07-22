<?php

/**
 * BALANCE TRANSLATIONS
 * Sync missing English keys từ Vietnamese để đạt perfect balance
 */

echo "=== BALANCING TRANSLATIONS ===\n\n";
echo "🎯 Goal: Sync 1,184 missing English keys from Vietnamese\n\n";

// Function to flatten nested arrays with dot notation
function flattenArray($array, $prefix = '') {
    $result = [];
    foreach ($array as $key => $value) {
        $newKey = $prefix === '' ? $key : $prefix . '.' . $key;
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value, $newKey));
        } else {
            $result[$newKey] = $value;
        }
    }
    return $result;
}

// Function to unflatten array back to nested structure
function unflattenArray($array) {
    $result = [];
    foreach ($array as $key => $value) {
        $keys = explode('.', $key);
        $temp = &$result;
        foreach ($keys as $k) {
            if (!isset($temp[$k])) {
                $temp[$k] = [];
            }
            $temp = &$temp[$k];
        }
        $temp = $value;
    }
    return $result;
}

// Function to array to PHP file format
function arrayToPhpFile($array, $indent = 0) {
    $spaces = str_repeat('  ', $indent);
    $result = "[\n";
    
    foreach ($array as $key => $value) {
        $result .= $spaces . "  '$key' => ";
        if (is_array($value)) {
            $result .= arrayToPhpFile($value, $indent + 1);
        } else {
            $escapedValue = str_replace("'", "\\'", $value);
            $result .= "'$escapedValue'";
        }
        $result .= ",\n";
    }
    
    $result .= $spaces . "]";
    return $result;
}

// Priority files to balance (top 10 imbalanced files)
$priorityFiles = [
    'common' => 219,
    'showcase' => 213,
    'user' => 154,
    'ui' => 148,
    'search' => 122,
    'forums' => 108,
    'thread' => 68,
    'business' => 62,
    'companies' => 38,
    'dashboard' => 37,
];

$totalSynced = 0;
$totalFiles = 0;

foreach ($priorityFiles as $filename => $expectedDiff) {
    echo "📁 Processing: $filename.php (expected +$expectedDiff keys)\n";
    
    $viFile = __DIR__ . "/resources/lang/vi/$filename.php";
    $enFile = __DIR__ . "/resources/lang/en/$filename.php";
    
    if (!file_exists($viFile) || !file_exists($enFile)) {
        echo "❌ Files not found for $filename\n";
        continue;
    }
    
    // Load both files
    $viData = include $viFile;
    $enData = include $enFile;
    
    if (!is_array($viData) || !is_array($enData)) {
        echo "❌ Invalid data in $filename files\n";
        continue;
    }
    
    // Flatten arrays
    $viFlat = flattenArray($viData, '');
    $enFlat = flattenArray($enData, '');
    
    // Find missing keys in English
    $missingInEn = array_diff(array_keys($viFlat), array_keys($enFlat));
    
    if (empty($missingInEn)) {
        echo "✅ $filename: Already balanced\n\n";
        continue;
    }
    
    echo "🔍 Found " . count($missingInEn) . " missing English keys\n";
    
    // Add missing keys to English data
    $syncedCount = 0;
    foreach ($missingInEn as $key) {
        $viValue = $viFlat[$key];
        
        // Simple translation mapping (you can enhance this)
        $enValue = translateViToEn($viValue);
        $enFlat[$key] = $enValue;
        $syncedCount++;
        
        if ($syncedCount <= 5) { // Show first 5
            echo "  + $key: '$viValue' → '$enValue'\n";
        }
    }
    
    if ($syncedCount > 5) {
        echo "  ... and " . ($syncedCount - 5) . " more keys\n";
    }
    
    // Convert back to nested array
    $newEnData = unflattenArray($enFlat);
    
    // Generate PHP file content
    $phpContent = "<?php\n\nreturn " . arrayToPhpFile($newEnData) . ";\n";
    
    // Backup original file
    $backupFile = $enFile . '.backup.' . date('Y-m-d-H-i-s');
    copy($enFile, $backupFile);
    
    // Write new file
    if (file_put_contents($enFile, $phpContent)) {
        echo "✅ Synced $syncedCount keys to $filename.php\n";
        echo "💾 Backup saved: " . basename($backupFile) . "\n";
        $totalSynced += $syncedCount;
        $totalFiles++;
    } else {
        echo "❌ Failed to write $filename.php\n";
    }
    
    echo "\n";
}

// Simple Vietnamese to English translation function
function translateViToEn($viText) {
    // Basic translation mappings
    $translations = [
        // Common words
        'Trang chủ' => 'Home',
        'Diễn đàn' => 'Forums',
        'Thị trường' => 'Marketplace',
        'Hồ sơ' => 'Profile',
        'Tìm kiếm' => 'Search',
        'Đăng nhập' => 'Login',
        'Đăng ký' => 'Register',
        'Đăng xuất' => 'Logout',
        'Cài đặt' => 'Settings',
        'Thông báo' => 'Notifications',
        'Tin nhắn' => 'Messages',
        'Bài viết' => 'Posts',
        'Chủ đề' => 'Topics',
        'Bình luận' => 'Comments',
        'Thích' => 'Like',
        'Chia sẻ' => 'Share',
        'Lưu' => 'Save',
        'Chỉnh sửa' => 'Edit',
        'Xóa' => 'Delete',
        'Tạo' => 'Create',
        'Cập nhật' => 'Update',
        'Xem' => 'View',
        'Tải xuống' => 'Download',
        'Tải lên' => 'Upload',
        'Gửi' => 'Send',
        'Nhận' => 'Receive',
        'Mở' => 'Open',
        'Đóng' => 'Close',
        'Bắt đầu' => 'Start',
        'Kết thúc' => 'End',
        'Tiếp tục' => 'Continue',
        'Hủy' => 'Cancel',
        'Xác nhận' => 'Confirm',
        'Từ chối' => 'Reject',
        'Chấp nhận' => 'Accept',
        'Thành công' => 'Success',
        'Thất bại' => 'Failed',
        'Lỗi' => 'Error',
        'Cảnh báo' => 'Warning',
        'Thông tin' => 'Information',
        'Chi tiết' => 'Details',
        'Tóm tắt' => 'Summary',
        'Danh sách' => 'List',
        'Bảng' => 'Table',
        'Biểu đồ' => 'Chart',
        'Báo cáo' => 'Report',
        'Thống kê' => 'Statistics',
        'Phân tích' => 'Analysis',
        'Kết quả' => 'Results',
        'Dữ liệu' => 'Data',
        'Tệp' => 'File',
        'Thư mục' => 'Folder',
        'Hình ảnh' => 'Image',
        'Video' => 'Video',
        'Âm thanh' => 'Audio',
        'Tài liệu' => 'Document',
        'Liên kết' => 'Link',
        'URL' => 'URL',
        'Email' => 'Email',
        'Điện thoại' => 'Phone',
        'Địa chỉ' => 'Address',
        'Tên' => 'Name',
        'Mô tả' => 'Description',
        'Tiêu đề' => 'Title',
        'Nội dung' => 'Content',
        'Ngày' => 'Date',
        'Thời gian' => 'Time',
        'Giờ' => 'Hour',
        'Phút' => 'Minute',
        'Giây' => 'Second',
        'Tuần' => 'Week',
        'Tháng' => 'Month',
        'Năm' => 'Year',
        'Hôm nay' => 'Today',
        'Hôm qua' => 'Yesterday',
        'Ngày mai' => 'Tomorrow',
        'Bây giờ' => 'Now',
        'Trước' => 'Before',
        'Sau' => 'After',
        'Trong' => 'In',
        'Ngoài' => 'Out',
        'Trên' => 'Above',
        'Dưới' => 'Below',
        'Bên trái' => 'Left',
        'Bên phải' => 'Right',
        'Giữa' => 'Center',
        'Đầu' => 'Top',
        'Cuối' => 'Bottom',
        'Đầu tiên' => 'First',
        'Cuối cùng' => 'Last',
        'Tiếp theo' => 'Next',
        'Trước đó' => 'Previous',
        'Tất cả' => 'All',
        'Không có' => 'None',
        'Một số' => 'Some',
        'Nhiều' => 'Many',
        'Ít' => 'Few',
        'Mới' => 'New',
        'Cũ' => 'Old',
        'Nóng' => 'Hot',
        'Lạnh' => 'Cold',
        'Nhanh' => 'Fast',
        'Chậm' => 'Slow',
        'Lớn' => 'Large',
        'Nhỏ' => 'Small',
        'Cao' => 'High',
        'Thấp' => 'Low',
        'Dài' => 'Long',
        'Ngắn' => 'Short',
        'Rộng' => 'Wide',
        'Hẹp' => 'Narrow',
        'Dày' => 'Thick',
        'Mỏng' => 'Thin',
        'Nặng' => 'Heavy',
        'Nhẹ' => 'Light',
        'Mạnh' => 'Strong',
        'Yếu' => 'Weak',
        'Tốt' => 'Good',
        'Xấu' => 'Bad',
        'Đúng' => 'Correct',
        'Sai' => 'Wrong',
        'Đẹp' => 'Beautiful',
        'Xấu xí' => 'Ugly',
        'Sạch' => 'Clean',
        'Bẩn' => 'Dirty',
        'An toàn' => 'Safe',
        'Nguy hiểm' => 'Dangerous',
        'Dễ' => 'Easy',
        'Khó' => 'Difficult',
        'Miễn phí' => 'Free',
        'Trả phí' => 'Paid',
        'Công khai' => 'Public',
        'Riêng tư' => 'Private',
        'Hoạt động' => 'Active',
        'Không hoạt động' => 'Inactive',
        'Bật' => 'On',
        'Tắt' => 'Off',
        'Có' => 'Yes',
        'Không' => 'No',
        'Được' => 'OK',
        'Không được' => 'Not OK',
    ];
    
    // Check for exact matches first
    if (isset($translations[$viText])) {
        return $translations[$viText];
    }
    
    // Check for partial matches
    foreach ($translations as $vi => $en) {
        if (strpos($viText, $vi) !== false) {
            return str_replace($vi, $en, $viText);
        }
    }
    
    // If no translation found, return original with [VI] prefix
    return "[VI] $viText";
}

echo "=== BALANCE SUMMARY ===\n";
echo "Files processed: $totalFiles\n";
echo "Total keys synced: $totalSynced\n";
echo "Estimated remaining imbalance: " . (1184 - $totalSynced) . " keys\n";

echo "\n✅ Translation balancing completed at " . date('Y-m-d H:i:s') . "\n";
echo "\n🔄 Run check-translation-balance.php again to verify results.\n";
?>
