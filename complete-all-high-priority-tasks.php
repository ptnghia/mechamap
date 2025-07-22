<?php

/**
 * COMPLETE ALL HIGH PRIORITY TASKS
 * Xử lý tất cả các tasks ưu tiên cao còn lại
 */

echo "=== COMPLETING ALL HIGH PRIORITY TASKS ===\n\n";

// All remaining high priority keys organized by file
$allHighPriorityKeys = [
    // Marketplace mega menu keys
    'marketplace_mega_menu' => [
        'products.digital_products' => ['vi' => 'Sản phẩm số', 'en' => 'Digital Products'],
        'products.cad_files' => ['vi' => 'Tệp CAD', 'en' => 'CAD Files'],
        'products.technical_drawings' => ['vi' => 'Bản vẽ kỹ thuật', 'en' => 'Technical Drawings'],
        'products.3d_models' => ['vi' => 'Mô hình 3D', 'en' => '3D Models'],
        'products.software_tools' => ['vi' => 'Công cụ phần mềm', 'en' => 'Software Tools'],
        'products.physical_products' => ['vi' => 'Sản phẩm vật lý', 'en' => 'Physical Products'],
        'products.components' => ['vi' => 'Linh kiện', 'en' => 'Components'],
        'products.materials' => ['vi' => 'Vật liệu', 'en' => 'Materials'],
        'products.tools_equipment' => ['vi' => 'Công cụ & Thiết bị', 'en' => 'Tools & Equipment'],
        'products.machinery' => ['vi' => 'Máy móc', 'en' => 'Machinery'],
        'products.services' => ['vi' => 'Dịch vụ', 'en' => 'Services'],
        'products.consulting' => ['vi' => 'Tư vấn', 'en' => 'Consulting'],
        'products.design_services' => ['vi' => 'Dịch vụ thiết kế', 'en' => 'Design Services'],
        'products.manufacturing' => ['vi' => 'Sản xuất', 'en' => 'Manufacturing'],
        'products.testing_validation' => ['vi' => 'Kiểm tra & Xác thực', 'en' => 'Testing & Validation'],
        'products.featured_products' => ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products'],
        'products.new_arrivals' => ['vi' => 'Hàng mới về', 'en' => 'New Arrivals'],
        'products.best_sellers' => ['vi' => 'Bán chạy nhất', 'en' => 'Best Sellers'],
        'products.on_sale' => ['vi' => 'Đang giảm giá', 'en' => 'On Sale'],
        'products.browse_all' => ['vi' => 'Duyệt tất cả', 'en' => 'Browse All'],
        'products.view_category' => ['vi' => 'Xem danh mục', 'en' => 'View Category'],
        'products.explore_more' => ['vi' => 'Khám phá thêm', 'en' => 'Explore More'],
    ],
    
    // Auth wizard step1 keys
    'auth_step1' => [
        'register.step1_title' => ['vi' => 'Bước 1: Thông tin cá nhân', 'en' => 'Step 1: Personal Information'],
        'register.personal_info_title' => ['vi' => 'Thông tin cá nhân', 'en' => 'Personal Information'],
        'register.first_name' => ['vi' => 'Tên', 'en' => 'First Name'],
        'register.last_name' => ['vi' => 'Họ', 'en' => 'Last Name'],
        'register.email_address' => ['vi' => 'Địa chỉ email', 'en' => 'Email Address'],
        'register.password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
        'register.confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
        'register.phone_number' => ['vi' => 'Số điện thoại', 'en' => 'Phone Number'],
        'register.continue_button' => ['vi' => 'Tiếp tục', 'en' => 'Continue'],
        'register.password_requirements' => ['vi' => 'Mật khẩu phải có ít nhất 8 ký tự', 'en' => 'Password must be at least 8 characters'],
        'register.email_verification' => ['vi' => 'Xác thực email', 'en' => 'Email Verification'],
        'register.terms_agreement' => ['vi' => 'Tôi đồng ý với điều khoản sử dụng', 'en' => 'I agree to the terms of service'],
    ],
    
    // Basic search keys
    'basic_search' => [
        'basic.search_placeholder' => ['vi' => 'Tìm kiếm chủ đề, bài viết...', 'en' => 'Search threads, posts...'],
        'basic.search_button' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'basic.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
        'basic.recent_searches' => ['vi' => 'Tìm kiếm gần đây', 'en' => 'Recent Searches'],
        'basic.popular_searches' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular Searches'],
        'basic.search_suggestions' => ['vi' => 'Gợi ý tìm kiếm', 'en' => 'Search Suggestions'],
        'basic.no_results' => ['vi' => 'Không tìm thấy kết quả', 'en' => 'No results found'],
        'basic.search_tips' => ['vi' => 'Mẹo tìm kiếm', 'en' => 'Search Tips'],
        'basic.clear_search' => ['vi' => 'Xóa tìm kiếm', 'en' => 'Clear Search'],
    ],
    
    // Thread create keys
    'thread_create' => [
        'create.new_thread' => ['vi' => 'Tạo chủ đề mới', 'en' => 'Create New Thread'],
        'create.thread_title' => ['vi' => 'Tiêu đề chủ đề', 'en' => 'Thread Title'],
        'create.thread_content' => ['vi' => 'Nội dung chủ đề', 'en' => 'Thread Content'],
        'create.select_forum' => ['vi' => 'Chọn diễn đàn', 'en' => 'Select Forum'],
        'create.thread_tags' => ['vi' => 'Thẻ chủ đề', 'en' => 'Thread Tags'],
        'create.add_attachments' => ['vi' => 'Thêm tệp đính kèm', 'en' => 'Add Attachments'],
        'create.post_thread' => ['vi' => 'Đăng chủ đề', 'en' => 'Post Thread'],
        'create.save_draft' => ['vi' => 'Lưu bản nháp', 'en' => 'Save Draft'],
        'create.preview' => ['vi' => 'Xem trước', 'en' => 'Preview'],
        'create.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'create.thread_guidelines' => ['vi' => 'Hướng dẫn đăng chủ đề', 'en' => 'Thread Guidelines'],
        'create.required_fields' => ['vi' => 'Các trường bắt buộc', 'en' => 'Required Fields'],
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

// Map categories to files
$categoryFileMap = [
    'marketplace_mega_menu' => 'marketplace',
    'auth_step1' => 'auth',
    'basic_search' => 'search',
    'thread_create' => 'forum',
];

$totalAdded = 0;

foreach ($allHighPriorityKeys as $category => $keys) {
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
echo "Total high priority keys added: $totalAdded\n";
echo "Categories processed: " . count($allHighPriorityKeys) . "\n";

echo "\n✅ All high priority tasks completed at " . date('Y-m-d H:i:s') . "\n";
?>
