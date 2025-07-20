<?php
/**
 * Process Simple Vietnamese Translation Keys
 * Xử lý 685 Vietnamese keys - biggest opportunity for quick wins
 */

echo "🇻🇳 PROCESSING SIMPLE VIETNAMESE KEYS\n";
echo "=====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/direct_keys_analysis.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

// Extract Vietnamese keys
$vietnameseKeys = [];
$allDirectKeys = array_merge(
    $analysis['detailed_patterns']['dot_notation'] ?? [],
    $analysis['detailed_patterns']['slash_notation'] ?? [],
    $analysis['detailed_patterns']['simple_keys'] ?? [],
    $analysis['detailed_patterns']['mixed_notation'] ?? []
);

foreach ($allDirectKeys as $key => $files) {
    // Detect Vietnamese text (contains Vietnamese characters or common Vietnamese words)
    if (preg_match('/[àáâãèéêìíòóôõùúăđĩũơưăạảấầẩẫậắằẳẵặẹẻẽềềểễệỉịọỏốồổỗộớờởỡợụủứừửữựỳỵỷỹ]/u', $key) ||
        preg_match('/^(Hủy|Xóa|Thêm|Sửa|Lưu|Thoát|Đóng|Mở|Xem|Tìm|Chọn|Chỉnh sửa|Xác nhận|Trạng thái|Thao tác|Thứ tự|Tên|Mô tả|Ngày|Giờ|Tháng|Năm|Tuần|Thống kê|Báo cáo|Cài đặt|Quản lý|Danh sách|Chi tiết|Tổng quan|Hoạt động|Kết quả|Thông tin|Dữ liệu|Phân tích|Đánh giá|Nhận xét|Bình luận|Phản hồi|Tin nhắn|Thông báo|Cảnh báo|Lỗi|Thành công|Thất bại|Đang xử lý|Hoàn thành|Chờ duyệt|Đã duyệt|Từ chối|Hủy bỏ|Tạm dừng|Kích hoạt|Vô hiệu|Công khai|Riêng tư|Ẩn|Hiện|Bật|Tắt|Có|Không|Đúng|Sai|Mới|Cũ|Trước|Sau|Đầu|Cuối|Trên|Dưới|Trái|Phải|Trong|Ngoài|Tất cả|Một số|Không có|Rỗng|Đầy|Nhỏ|Lớn|Cao|Thấp|Nhanh|Chậm|Dễ|Khó|Tốt|Xấu|Đẹp|Xấu xí|Sáng|Tối|Nóng|Lạnh|Ấm|Mát|Khô|Ướt|Sạch|Bẩn|Mới|Cũ|Trẻ|Già|Giàu|Nghèo|Khỏe|Yếu|Mạnh|Nhẹ|Nặng|Dài|Ngắn|Rộng|Hẹp|Dày|Mỏng|Cứng|Mềm|Thẳng|Cong|Tròn|Vuông|Tam giác|Chữ nhật|Hình|Màu|Đỏ|Xanh|Vàng|Tím|Hồng|Cam|Nâu|Đen|Trắng|Xám|Số|Chữ|Ký tự|Từ|Câu|Đoạn|Trang|Sách|Báo|Tạp chí|Phim|Nhạc|Hình ảnh|Video|Âm thanh|File|Thư mục|Đường dẫn|Link|URL|Email|Điện thoại|Địa chỉ|Quốc gia|Thành phố|Quận|Phường|Đường|Số nhà|Mã bưu điện)/', $key)) {
        $vietnameseKeys[$key] = $files;
    }
}

echo "📊 VIETNAMESE KEYS ANALYSIS\n";
echo "===========================\n";
echo "Total Vietnamese keys found: " . count($vietnameseKeys) . "\n";

// Categorize Vietnamese keys by type
$categories = [
    'actions' => [], // Hủy, Xóa, Thêm, Sửa, etc.
    'status' => [], // Trạng thái, Hoạt động, etc.
    'ui_elements' => [], // Thao tác, Thứ tự, etc.
    'time_date' => [], // Ngày, Tháng, Năm, etc.
    'descriptions' => [], // Mô tả, Chi tiết, etc.
    'other' => []
];

$actionWords = ['Hủy', 'Xóa', 'Thêm', 'Sửa', 'Lưu', 'Thoát', 'Đóng', 'Mở', 'Xem', 'Tìm', 'Chọn', 'Chỉnh sửa', 'Xác nhận'];
$statusWords = ['Trạng thái', 'Hoạt động', 'Kết quả', 'Thành công', 'Thất bại', 'Đang xử lý', 'Hoàn thành', 'Chờ duyệt', 'Đã duyệt'];
$uiWords = ['Thao tác', 'Thứ tự', 'Danh sách', 'Chi tiết', 'Tổng quan', 'Thông tin', 'Dữ liệu'];
$timeWords = ['Ngày', 'Giờ', 'Tháng', 'Năm', 'Tuần'];
$descWords = ['Mô tả', 'Tên', 'Nhận xét', 'Bình luận', 'Phản hồi', 'Tin nhắn', 'Thông báo'];

foreach ($vietnameseKeys as $key => $files) {
    $categorized = false;
    
    foreach ($actionWords as $word) {
        if (strpos($key, $word) !== false) {
            $categories['actions'][$key] = $files;
            $categorized = true;
            break;
        }
    }
    
    if (!$categorized) {
        foreach ($statusWords as $word) {
            if (strpos($key, $word) !== false) {
                $categories['status'][$key] = $files;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        foreach ($uiWords as $word) {
            if (strpos($key, $word) !== false) {
                $categories['ui_elements'][$key] = $files;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        foreach ($timeWords as $word) {
            if (strpos($key, $word) !== false) {
                $categories['time_date'][$key] = $files;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        foreach ($descWords as $word) {
            if (strpos($key, $word) !== false) {
                $categories['descriptions'][$key] = $files;
                $categorized = true;
                break;
            }
        }
    }
    
    if (!$categorized) {
        $categories['other'][$key] = $files;
    }
}

echo "\n📋 CATEGORIZATION\n";
echo "=================\n";
foreach ($categories as $category => $keys) {
    echo "🔸 " . ucfirst(str_replace('_', ' ', $category)) . ": " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 5);
    foreach ($examples as $example) {
        echo "   - $example\n";
    }
    if (count($keys) > 5) {
        echo "   - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

echo "🔧 CREATING VIETNAMESE TRANSLATION MAPPINGS\n";
echo "===========================================\n";

// Create a comprehensive mapping for Vietnamese keys
$vietnameseTranslations = [];

// Process each category
foreach ($categories as $category => $keys) {
    $targetFile = "vi_$category.php";
    
    foreach ($keys as $key => $files) {
        // For Vietnamese keys, the key itself is often the translation
        // We need to create English equivalents
        
        $englishTranslation = '';
        $vietnameseTranslation = $key;
        
        // Common translations
        $commonMappings = [
            'Hủy' => 'Cancel',
            'Xóa' => 'Delete',
            'Thêm' => 'Add',
            'Sửa' => 'Edit',
            'Lưu' => 'Save',
            'Thoát' => 'Exit',
            'Đóng' => 'Close',
            'Mở' => 'Open',
            'Xem' => 'View',
            'Tìm' => 'Search',
            'Chọn' => 'Select',
            'Chỉnh sửa' => 'Edit',
            'Xác nhận' => 'Confirm',
            'Trạng thái' => 'Status',
            'Thao tác' => 'Actions',
            'Thứ tự' => 'Order',
            'Tên' => 'Name',
            'Mô tả' => 'Description',
            'Ngày' => 'Date',
            'Giờ' => 'Time',
            'Tháng' => 'Month',
            'Năm' => 'Year',
            'Tuần' => 'Week',
            'Danh sách' => 'List',
            'Chi tiết' => 'Details',
            'Tổng quan' => 'Overview',
            'Hoạt động' => 'Activity',
            'Kết quả' => 'Result',
            'Thông tin' => 'Information',
            'Dữ liệu' => 'Data',
            'Thành công' => 'Success',
            'Thất bại' => 'Failed',
            'Đang xử lý' => 'Processing',
            'Hoàn thành' => 'Completed',
            'ID' => 'ID'
        ];
        
        // Find English translation
        if (isset($commonMappings[$key])) {
            $englishTranslation = $commonMappings[$key];
        } else {
            // Try to find partial matches
            foreach ($commonMappings as $viText => $enText) {
                if (strpos($key, $viText) !== false) {
                    $englishTranslation = str_replace($viText, $enText, $key);
                    break;
                }
            }
            
            // If no match found, use a generic approach
            if (empty($englishTranslation)) {
                $englishTranslation = '[EN] ' . $key;
            }
        }
        
        // Create safe key name
        $safeKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $key));
        $safeKey = trim($safeKey, '_');
        
        if (!isset($vietnameseTranslations[$targetFile])) {
            $vietnameseTranslations[$targetFile] = [];
        }
        
        $vietnameseTranslations[$targetFile][$safeKey] = [
            'en' => $englishTranslation,
            'vi' => $vietnameseTranslation,
            'original_key' => $key,
            'files' => $files
        ];
    }
}

// Create translation files
$createdFiles = 0;
$totalTranslations = 0;

foreach ($vietnameseTranslations as $fileName => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/$fileName";
        
        // Prepare translations for this locale
        $localeTranslations = [];
        foreach ($translations as $safeKey => $data) {
            $localeTranslations[$safeKey] = $data[$locale];
        }
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * Vietnamese keys translations ($locale)\n * Auto-generated: " . date('Y-m-d H:i:s') . "\n * Keys: " . count($localeTranslations) . "\n */\n\nreturn " . var_export($localeTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Created: $locale/$fileName (" . count($localeTranslations) . " translations)\n";
        $createdFiles++;
        $totalTranslations += count($localeTranslations);
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Vietnamese keys processed: " . count($vietnameseKeys) . "\n";
echo "Translation files created: $createdFiles\n";
echo "Total translations added: $totalTranslations\n";

echo "\n🧪 TESTING SAMPLE KEYS...\n";
echo "=========================\n";

// Test some common Vietnamese keys
$testKeys = ['Hủy', 'Xóa', 'Thêm', 'Trạng thái', 'Thao tác'];
$workingCount = 0;

foreach ($testKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Still not working\n";
    }
}

echo "\nSample success rate: " . round(($workingCount / count($testKeys)) * 100, 1) . "%\n";

echo "\n💡 IMPORTANT NOTE\n";
echo "=================\n";
echo "Vietnamese keys like 'Hủy', 'Xóa' are used directly in code as __('Hủy').\n";
echo "To make them work, we need to either:\n";
echo "1. Create files with Vietnamese key names (complex)\n";
echo "2. Replace usage in code to use safe keys (recommended)\n";
echo "3. Use a mapping system in the application\n\n";

echo "🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Analyzed and categorized Vietnamese keys\n";
echo "2. 🔄 Consider code replacement strategy\n";
echo "3. 🔄 Process UI keys (280 keys) next\n";
echo "4. 🔄 Continue with forum keys\n";
echo "5. 🔄 Run comprehensive validation\n";
