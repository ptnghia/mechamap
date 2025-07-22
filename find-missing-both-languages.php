<?php

/**
 * FIND MISSING BOTH LANGUAGES KEYS
 * Tìm và thêm tất cả keys thiếu cả tiếng Việt và tiếng Anh
 */

echo "=== FINDING MISSING BOTH LANGUAGES KEYS ===\n\n";

// Load translation files
$viPath = __DIR__ . '/resources/lang/vi/';
$enPath = __DIR__ . '/resources/lang/en/';

$viTranslations = [];
$enTranslations = [];

// Load all VI translation files
foreach (glob($viPath . '*.php') as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        foreach ($translations as $key => $value) {
            $viTranslations[$filename . '.' . $key] = $value;
        }
    }
}

// Load all EN translation files
foreach (glob($enPath . '*.php') as $file) {
    $filename = basename($file, '.php');
    $translations = include $file;
    if (is_array($translations)) {
        foreach ($translations as $key => $value) {
            $enTranslations[$filename . '.' . $key] = $value;
        }
    }
}

echo "📊 Loaded translations:\n";
echo "  VI: " . count($viTranslations) . " keys\n";
echo "  EN: " . count($enTranslations) . " keys\n\n";

// Scan blade files for translation calls
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        // Skip admin and components directories
        if (strpos($path, '/admin/') === false && strpos($path, '/components/') === false) {
            $bladeFiles[] = $path;
        }
    }
}

echo "📁 Found " . count($bladeFiles) . " blade files to scan\n\n";

$allUsedKeys = [];

// Extract translation keys from blade files
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    
    // Extract __('key') calls
    preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
    foreach ($directMatches[1] as $key) {
        $allUsedKeys[] = $key;
    }
    
    // Extract t_helper('key') calls
    preg_match_all('/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $helperMatches);
    foreach ($helperMatches[1] as $i => $helper) {
        $key = $helperMatches[1][$i] . '.' . $helperMatches[2][$i];
        $allUsedKeys[] = $key;
    }
    
    // Extract @section('title', __('key')) calls
    preg_match_all('/@section\([\'"]title[\'"],\s*__\([\'"]([^\'"]+)[\'"]\)/', $content, $titleMatches);
    foreach ($titleMatches[1] as $key) {
        $allUsedKeys[] = $key;
    }
    
    // Extract other patterns
    preg_match_all('/[\'"]([a-zA-Z_]+\.[a-zA-Z_\.]+)[\'"]/', $content, $otherMatches);
    foreach ($otherMatches[1] as $key) {
        if (strpos($key, '.') !== false && !strpos($key, 'http') && !strpos($key, 'www')) {
            $allUsedKeys[] = $key;
        }
    }
}

$allUsedKeys = array_unique($allUsedKeys);
echo "🔑 Found " . count($allUsedKeys) . " unique translation keys in blade files\n\n";

// Find keys missing in both languages
$missingBoth = [];
foreach ($allUsedKeys as $key) {
    if (!isset($viTranslations[$key]) && !isset($enTranslations[$key])) {
        $missingBoth[] = $key;
    }
}

echo "❌ Keys missing in BOTH languages: " . count($missingBoth) . "\n\n";

// Group missing keys by file/category
$groupedMissing = [];
foreach ($missingBoth as $key) {
    $parts = explode('.', $key);
    $file = $parts[0];
    $keyName = implode('.', array_slice($parts, 1));
    
    if (!isset($groupedMissing[$file])) {
        $groupedMissing[$file] = [];
    }
    $groupedMissing[$file][] = $keyName;
}

// Display missing keys by category
foreach ($groupedMissing as $file => $keys) {
    echo "📄 $file.php - " . count($keys) . " missing keys:\n";
    foreach (array_slice($keys, 0, 10) as $key) {
        echo "  - $key\n";
    }
    if (count($keys) > 10) {
        echo "  ... and " . (count($keys) - 10) . " more\n";
    }
    echo "\n";
}

// Generate common missing keys that we can add
$commonMissingKeys = [
    // Common UI elements
    'common.loading' => ['vi' => 'Đang tải...', 'en' => 'Loading...'],
    'common.error' => ['vi' => 'Lỗi', 'en' => 'Error'],
    'common.success' => ['vi' => 'Thành công', 'en' => 'Success'],
    'common.warning' => ['vi' => 'Cảnh báo', 'en' => 'Warning'],
    'common.info' => ['vi' => 'Thông tin', 'en' => 'Information'],
    'common.confirm' => ['vi' => 'Xác nhận', 'en' => 'Confirm'],
    'common.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
    'common.save' => ['vi' => 'Lưu', 'en' => 'Save'],
    'common.edit' => ['vi' => 'Chỉnh sửa', 'en' => 'Edit'],
    'common.delete' => ['vi' => 'Xóa', 'en' => 'Delete'],
    'common.view' => ['vi' => 'Xem', 'en' => 'View'],
    'common.back' => ['vi' => 'Quay lại', 'en' => 'Back'],
    'common.next' => ['vi' => 'Tiếp theo', 'en' => 'Next'],
    'common.previous' => ['vi' => 'Trước', 'en' => 'Previous'],
    'common.close' => ['vi' => 'Đóng', 'en' => 'Close'],
    'common.open' => ['vi' => 'Mở', 'en' => 'Open'],
    'common.yes' => ['vi' => 'Có', 'en' => 'Yes'],
    'common.no' => ['vi' => 'Không', 'en' => 'No'],
    'common.ok' => ['vi' => 'OK', 'en' => 'OK'],
    'common.submit' => ['vi' => 'Gửi', 'en' => 'Submit'],
    'common.reset' => ['vi' => 'Đặt lại', 'en' => 'Reset'],
    'common.clear' => ['vi' => 'Xóa', 'en' => 'Clear'],
    'common.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
    'common.filter' => ['vi' => 'Lọc', 'en' => 'Filter'],
    'common.sort' => ['vi' => 'Sắp xếp', 'en' => 'Sort'],
    'common.refresh' => ['vi' => 'Làm mới', 'en' => 'Refresh'],
    'common.update' => ['vi' => 'Cập nhật', 'en' => 'Update'],
    'common.create' => ['vi' => 'Tạo', 'en' => 'Create'],
    'common.add' => ['vi' => 'Thêm', 'en' => 'Add'],
    'common.remove' => ['vi' => 'Xóa', 'en' => 'Remove'],
    'common.select' => ['vi' => 'Chọn', 'en' => 'Select'],
    'common.upload' => ['vi' => 'Tải lên', 'en' => 'Upload'],
    'common.download' => ['vi' => 'Tải xuống', 'en' => 'Download'],
    'common.share' => ['vi' => 'Chia sẻ', 'en' => 'Share'],
    'common.copy' => ['vi' => 'Sao chép', 'en' => 'Copy'],
    'common.paste' => ['vi' => 'Dán', 'en' => 'Paste'],
    'common.cut' => ['vi' => 'Cắt', 'en' => 'Cut'],
    'common.print' => ['vi' => 'In', 'en' => 'Print'],
    'common.export' => ['vi' => 'Xuất', 'en' => 'Export'],
    'common.import' => ['vi' => 'Nhập', 'en' => 'Import'],
    'common.settings' => ['vi' => 'Cài đặt', 'en' => 'Settings'],
    'common.preferences' => ['vi' => 'Tùy chọn', 'en' => 'Preferences'],
    'common.help' => ['vi' => 'Trợ giúp', 'en' => 'Help'],
    'common.about' => ['vi' => 'Giới thiệu', 'en' => 'About'],
    'common.contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
    'common.support' => ['vi' => 'Hỗ trợ', 'en' => 'Support'],
    'common.feedback' => ['vi' => 'Phản hồi', 'en' => 'Feedback'],
    'common.report' => ['vi' => 'Báo cáo', 'en' => 'Report'],
    'common.status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'common.active' => ['vi' => 'Hoạt động', 'en' => 'Active'],
    'common.inactive' => ['vi' => 'Không hoạt động', 'en' => 'Inactive'],
    'common.enabled' => ['vi' => 'Đã bật', 'en' => 'Enabled'],
    'common.disabled' => ['vi' => 'Đã tắt', 'en' => 'Disabled'],
    'common.public' => ['vi' => 'Công khai', 'en' => 'Public'],
    'common.private' => ['vi' => 'Riêng tư', 'en' => 'Private'],
    'common.draft' => ['vi' => 'Bản nháp', 'en' => 'Draft'],
    'common.published' => ['vi' => 'Đã xuất bản', 'en' => 'Published'],
    'common.pending' => ['vi' => 'Chờ xử lý', 'en' => 'Pending'],
    'common.approved' => ['vi' => 'Đã duyệt', 'en' => 'Approved'],
    'common.rejected' => ['vi' => 'Bị từ chối', 'en' => 'Rejected'],
    'common.completed' => ['vi' => 'Hoàn thành', 'en' => 'Completed'],
    'common.in_progress' => ['vi' => 'Đang thực hiện', 'en' => 'In Progress'],
    'common.not_started' => ['vi' => 'Chưa bắt đầu', 'en' => 'Not Started'],
    'common.cancelled' => ['vi' => 'Đã hủy', 'en' => 'Cancelled'],
    'common.expired' => ['vi' => 'Đã hết hạn', 'en' => 'Expired'],
    'common.valid' => ['vi' => 'Hợp lệ', 'en' => 'Valid'],
    'common.invalid' => ['vi' => 'Không hợp lệ', 'en' => 'Invalid'],
    'common.required' => ['vi' => 'Bắt buộc', 'en' => 'Required'],
    'common.optional' => ['vi' => 'Tùy chọn', 'en' => 'Optional'],
    'common.recommended' => ['vi' => 'Khuyến nghị', 'en' => 'Recommended'],
    'common.featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
    'common.popular' => ['vi' => 'Phổ biến', 'en' => 'Popular'],
    'common.trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
    'common.new' => ['vi' => 'Mới', 'en' => 'New'],
    'common.updated' => ['vi' => 'Đã cập nhật', 'en' => 'Updated'],
    'common.latest' => ['vi' => 'Mới nhất', 'en' => 'Latest'],
    'common.oldest' => ['vi' => 'Cũ nhất', 'en' => 'Oldest'],
    'common.recent' => ['vi' => 'Gần đây', 'en' => 'Recent'],
    'common.all' => ['vi' => 'Tất cả', 'en' => 'All'],
    'common.none' => ['vi' => 'Không có', 'en' => 'None'],
    'common.other' => ['vi' => 'Khác', 'en' => 'Other'],
    'common.more' => ['vi' => 'Thêm', 'en' => 'More'],
    'common.less' => ['vi' => 'Ít hơn', 'en' => 'Less'],
    'common.show' => ['vi' => 'Hiển thị', 'en' => 'Show'],
    'common.hide' => ['vi' => 'Ẩn', 'en' => 'Hide'],
    'common.expand' => ['vi' => 'Mở rộng', 'en' => 'Expand'],
    'common.collapse' => ['vi' => 'Thu gọn', 'en' => 'Collapse'],
    'common.toggle' => ['vi' => 'Chuyển đổi', 'en' => 'Toggle'],
    'common.enable' => ['vi' => 'Bật', 'en' => 'Enable'],
    'common.disable' => ['vi' => 'Tắt', 'en' => 'Disable'],
    'common.activate' => ['vi' => 'Kích hoạt', 'en' => 'Activate'],
    'common.deactivate' => ['vi' => 'Vô hiệu hóa', 'en' => 'Deactivate'],
    'common.install' => ['vi' => 'Cài đặt', 'en' => 'Install'],
    'common.uninstall' => ['vi' => 'Gỡ cài đặt', 'en' => 'Uninstall'],
    'common.configure' => ['vi' => 'Cấu hình', 'en' => 'Configure'],
    'common.customize' => ['vi' => 'Tùy chỉnh', 'en' => 'Customize'],
    'common.preview' => ['vi' => 'Xem trước', 'en' => 'Preview'],
    'common.review' => ['vi' => 'Xem xét', 'en' => 'Review'],
    'common.approve' => ['vi' => 'Duyệt', 'en' => 'Approve'],
    'common.reject' => ['vi' => 'Từ chối', 'en' => 'Reject'],
    'common.publish' => ['vi' => 'Xuất bản', 'en' => 'Publish'],
    'common.unpublish' => ['vi' => 'Hủy xuất bản', 'en' => 'Unpublish'],
    'common.archive' => ['vi' => 'Lưu trữ', 'en' => 'Archive'],
    'common.restore' => ['vi' => 'Khôi phục', 'en' => 'Restore'],
    'common.backup' => ['vi' => 'Sao lưu', 'en' => 'Backup'],
    'common.duplicate' => ['vi' => 'Nhân bản', 'en' => 'Duplicate'],
    'common.move' => ['vi' => 'Di chuyển', 'en' => 'Move'],
    'common.rename' => ['vi' => 'Đổi tên', 'en' => 'Rename'],
    'common.resize' => ['vi' => 'Thay đổi kích thước', 'en' => 'Resize'],
    'common.rotate' => ['vi' => 'Xoay', 'en' => 'Rotate'],
    'common.crop' => ['vi' => 'Cắt', 'en' => 'Crop'],
    'common.zoom' => ['vi' => 'Phóng to', 'en' => 'Zoom'],
    'common.fit' => ['vi' => 'Vừa khít', 'en' => 'Fit'],
    'common.fullscreen' => ['vi' => 'Toàn màn hình', 'en' => 'Fullscreen'],
    'common.minimize' => ['vi' => 'Thu nhỏ', 'en' => 'Minimize'],
    'common.maximize' => ['vi' => 'Phóng to', 'en' => 'Maximize'],
];

echo "🔧 Generated " . count($commonMissingKeys) . " common missing keys\n\n";

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

// Add common missing keys to common.php
echo "📁 Adding common missing keys to common.php\n";

$viFile = __DIR__ . "/resources/lang/vi/common.php";
addKeysToFile($viFile, $commonMissingKeys, 'vi');

$enFile = __DIR__ . "/resources/lang/en/common.php";
addKeysToFile($enFile, $commonMissingKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total missing both languages: " . count($missingBoth) . "\n";
echo "Common keys added: " . count($commonMissingKeys) . "\n";
echo "Files with most missing keys:\n";

$sortedGroups = $groupedMissing;
arsort($sortedGroups);
$top5 = array_slice($sortedGroups, 0, 5, true);

foreach ($top5 as $file => $keys) {
    echo "  - $file.php: " . count($keys) . " keys\n";
}

echo "\n✅ Missing both languages analysis completed at " . date('Y-m-d H:i:s') . "\n";
?>
