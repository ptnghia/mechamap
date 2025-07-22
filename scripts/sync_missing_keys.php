<?php

/**
 * Sync missing translation keys from Vietnamese to English
 * 
 * This script identifies missing keys in English files and adds them
 * with appropriate English translations or placeholders.
 */

function loadTranslationFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    
    try {
        $content = include $filePath;
        return is_array($content) ? $content : [];
    } catch (Exception $e) {
        echo "Error loading $filePath: " . $e->getMessage() . "\n";
        return [];
    }
}

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

function unflattenArray($array) {
    $result = [];
    
    foreach ($array as $key => $value) {
        $keys = explode('.', $key);
        $current = &$result;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;
    }
    
    return $result;
}

function translateToEnglish($vietnameseText) {
    // Basic Vietnamese to English translation mapping
    $translations = [
        // Common UI terms
        'Trang chủ' => 'Home',
        'Đăng nhập' => 'Login',
        'Đăng ký' => 'Register',
        'Đăng xuất' => 'Logout',
        'Tìm kiếm' => 'Search',
        'Thông báo' => 'Notification',
        'Tin nhắn' => 'Message',
        'Hồ sơ' => 'Profile',
        'Cài đặt' => 'Settings',
        'Quản lý' => 'Management',
        'Danh sách' => 'List',
        'Chi tiết' => 'Details',
        'Thêm mới' => 'Add New',
        'Chỉnh sửa' => 'Edit',
        'Xóa' => 'Delete',
        'Lưu' => 'Save',
        'Hủy' => 'Cancel',
        'Xác nhận' => 'Confirm',
        'Gửi' => 'Send',
        'Tải lên' => 'Upload',
        'Tải xuống' => 'Download',
        'Xem' => 'View',
        'Chia sẻ' => 'Share',
        'Thích' => 'Like',
        'Bình luận' => 'Comment',
        'Trả lời' => 'Reply',
        'Theo dõi' => 'Follow',
        'Yêu thích' => 'Favorite',
        'Đánh giá' => 'Rating',
        'Báo cáo' => 'Report',
        'Thống kê' => 'Statistics',
        'Phân tích' => 'Analysis',
        'Xuất' => 'Export',
        'Nhập' => 'Import',
        'Sao chép' => 'Copy',
        'Di chuyển' => 'Move',
        'Sắp xếp' => 'Sort',
        'Lọc' => 'Filter',
        'Tìm kiếm nâng cao' => 'Advanced Search',
        'Kết quả' => 'Results',
        'Không có dữ liệu' => 'No data available',
        'Đang tải' => 'Loading',
        'Thành công' => 'Success',
        'Lỗi' => 'Error',
        'Cảnh báo' => 'Warning',
        'Thông tin' => 'Information',
        
        // Forum terms
        'Diễn đàn' => 'Forum',
        'Chủ đề' => 'Thread',
        'Bài viết' => 'Post',
        'Danh mục' => 'Category',
        'Thành viên' => 'Member',
        'Moderator' => 'Moderator',
        'Quản trị viên' => 'Administrator',
        'Bình chọn' => 'Poll',
        'Cuộc thảo luận' => 'Discussion',
        'Câu hỏi' => 'Question',
        'Câu trả lời' => 'Answer',
        'Giải pháp' => 'Solution',
        'Hướng dẫn' => 'Guide',
        'Tài liệu' => 'Documentation',
        
        // Marketplace terms
        'Thị trường' => 'Marketplace',
        'Sản phẩm' => 'Product',
        'Dịch vụ' => 'Service',
        'Mua' => 'Buy',
        'Bán' => 'Sell',
        'Giá' => 'Price',
        'Giỏ hàng' => 'Cart',
        'Thanh toán' => 'Payment',
        'Đơn hàng' => 'Order',
        'Giao hàng' => 'Delivery',
        'Nhà cung cấp' => 'Supplier',
        'Nhà sản xuất' => 'Manufacturer',
        'Thương hiệu' => 'Brand',
        'Đánh giá sản phẩm' => 'Product Review',
        'Khuyến mãi' => 'Promotion',
        'Giảm giá' => 'Discount',
        
        // User roles
        'Khách' => 'Guest',
        'Thành viên cấp cao' => 'Senior Member',
        'Đối tác đã xác minh' => 'Verified Partner',
        'Sinh viên' => 'Student',
        
        // Common actions
        'Tạo' => 'Create',
        'Cập nhật' => 'Update',
        'Xóa bỏ' => 'Remove',
        'Kích hoạt' => 'Activate',
        'Vô hiệu hóa' => 'Disable',
        'Phê duyệt' => 'Approve',
        'Từ chối' => 'Reject',
        'Xuất bản' => 'Publish',
        'Nháp' => 'Draft',
        'Riêng tư' => 'Private',
        'Công khai' => 'Public',
        
        // Time and dates
        'Hôm nay' => 'Today',
        'Hôm qua' => 'Yesterday',
        'Tuần này' => 'This week',
        'Tháng này' => 'This month',
        'Năm nay' => 'This year',
        'Mới nhất' => 'Latest',
        'Cũ nhất' => 'Oldest',
        
        // Status
        'Hoạt động' => 'Active',
        'Không hoạt động' => 'Inactive',
        'Đang chờ' => 'Pending',
        'Đã hoàn thành' => 'Completed',
        'Đã hủy' => 'Cancelled',
        'Đang xử lý' => 'Processing',
    ];
    
    // Try exact match first
    if (isset($translations[$vietnameseText])) {
        return $translations[$vietnameseText];
    }
    
    // Try partial matches
    foreach ($translations as $vi => $en) {
        if (strpos($vietnameseText, $vi) !== false) {
            return str_replace($vi, $en, $vietnameseText);
        }
    }
    
    // If no translation found, return the Vietnamese text with a note
    return $vietnameseText . ' [VI]';
}

function syncMissingKeys($viFile, $enFile) {
    echo "🔄 Syncing: $viFile -> $enFile\n";
    
    $viData = loadTranslationFile($viFile);
    $enData = loadTranslationFile($enFile);
    
    if (empty($viData)) {
        echo "   ⚠️ Vietnamese file is empty or invalid\n";
        return false;
    }
    
    // Flatten arrays for comparison
    $viFlat = flattenArray($viData);
    $enFlat = flattenArray($enData);
    
    $missingKeys = array_diff_key($viFlat, $enFlat);
    
    if (empty($missingKeys)) {
        echo "   ✅ No missing keys\n";
        return true;
    }
    
    echo "   📝 Found " . count($missingKeys) . " missing keys\n";
    
    // Add missing keys with translations
    foreach ($missingKeys as $key => $value) {
        $translation = translateToEnglish($value);
        $enFlat[$key] = $translation;
        echo "   + $key: '$value' -> '$translation'\n";
    }
    
    // Unflatten and save
    $newEnData = unflattenArray($enFlat);
    
    // Create backup
    if (file_exists($enFile)) {
        $backupFile = $enFile . '.backup.' . date('Y-m-d-H-i-s');
        copy($enFile, $backupFile);
        echo "   💾 Backup created: $backupFile\n";
    }
    
    // Write new file
    $content = "<?php\n\n/**\n * English translations\n * Auto-synced from Vietnamese on " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($newEnData, true) . ";\n";
    
    if (file_put_contents($enFile, $content)) {
        echo "   ✅ File updated successfully\n";
        return true;
    } else {
        echo "   ❌ Failed to write file\n";
        return false;
    }
}

// Main execution
echo "🔄 SYNCING MISSING ENGLISH TRANSLATION KEYS\n";
echo "==========================================\n";

$basePath = dirname(__DIR__) . '/resources/lang';
$viPath = $basePath . '/vi';
$enPath = $basePath . '/en';

if (!is_dir($viPath) || !is_dir($enPath)) {
    echo "❌ Language directories not found\n";
    exit(1);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viPath, RecursiveDirectoryIterator::SKIP_DOTS)
);

$totalFiles = 0;
$updatedFiles = 0;

foreach ($iterator as $viFile) {
    if ($viFile->getExtension() === 'php') {
        $relativePath = str_replace($viPath . DIRECTORY_SEPARATOR, '', $viFile->getPathname());
        $relativePath = str_replace('\\', '/', $relativePath);
        
        $enFile = $enPath . '/' . $relativePath;
        
        // Create directory if it doesn't exist
        $enDir = dirname($enFile);
        if (!is_dir($enDir)) {
            mkdir($enDir, 0755, true);
            echo "📁 Created directory: $enDir\n";
        }
        
        $totalFiles++;
        
        if (syncMissingKeys($viFile->getPathname(), $enFile)) {
            $updatedFiles++;
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ SYNC COMPLETED\n";
echo str_repeat("=", 50) . "\n";
echo "📊 Total files processed: $totalFiles\n";
echo "📝 Files updated: $updatedFiles\n";
echo "🎯 Success rate: " . round(($updatedFiles / $totalFiles) * 100, 1) . "%\n";

?>
