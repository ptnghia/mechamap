<?php
/**
 * Add Missing UI Common Keys
 * Add all missing ui/common keys found in header.blade.php
 */

echo "🔧 ADDING MISSING UI COMMON KEYS\n";
echo "================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Keys found in header.blade.php that need to be added
$missingKeys = [
    'about_mechamap' => ['vi' => 'Về MechaMap', 'en' => 'About MechaMap'],
    'about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About Us'],
    'admin_dashboard' => ['vi' => 'Bảng điều khiển Admin', 'en' => 'Admin Dashboard'],
    'advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'brand_dashboard' => ['vi' => 'Bảng điều khiển Thương hiệu', 'en' => 'Brand Dashboard'],
    'browse_by_tags' => ['vi' => 'Duyệt theo thẻ', 'en' => 'Browse by Tags'],
    'cad_library' => ['vi' => 'Thư viện CAD', 'en' => 'CAD Library'],
    'contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
    'dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
    'design_management' => ['vi' => 'Quản lý thiết kế', 'en' => 'Design Management'],
    'design_resources' => ['vi' => 'Tài nguyên thiết kế', 'en' => 'Design Resources'],
    'download_orders' => ['vi' => 'Đơn hàng tải xuống', 'en' => 'Download Orders'],
    'engineering_standards' => ['vi' => 'Tiêu chuẩn kỹ thuật', 'en' => 'Engineering Standards'],
    'faq' => ['vi' => 'Câu hỏi thường gặp', 'en' => 'FAQ'],
    'help_center' => ['vi' => 'Trung tâm trợ giúp', 'en' => 'Help Center'],
    'help_support' => ['vi' => 'Trợ giúp & Hỗ trợ', 'en' => 'Help & Support'],
    'industry_news' => ['vi' => 'Tin tức ngành', 'en' => 'Industry News'],
    'industry_reports' => ['vi' => 'Báo cáo ngành', 'en' => 'Industry Reports'],
    'industry_updates' => ['vi' => 'Cập nhật ngành', 'en' => 'Industry Updates'],
    'knowledge' => ['vi' => 'Kiến thức', 'en' => 'Knowledge'],
    'knowledge_base' => ['vi' => 'Cơ sở kiến thức', 'en' => 'Knowledge Base'],
    'learning_resources' => ['vi' => 'Tài nguyên học tập', 'en' => 'Learning Resources'],
    'manufacturer_dashboard' => ['vi' => 'Bảng điều khiển Nhà sản xuất', 'en' => 'Manufacturer Dashboard'],
    'manufacturing_processes' => ['vi' => 'Quy trình sản xuất', 'en' => 'Manufacturing Processes'],
    'market_analysis' => ['vi' => 'Phân tích thị trường', 'en' => 'Market Analysis'],
    'material_cost_calculator' => ['vi' => 'Máy tính chi phí vật liệu', 'en' => 'Material Cost Calculator'],
    'materials_database' => ['vi' => 'Cơ sở dữ liệu vật liệu', 'en' => 'Materials Database'],
    'messages' => ['vi' => 'Tin nhắn', 'en' => 'Messages'],
    'more' => ['vi' => 'Thêm', 'en' => 'More'],
    'my_business' => ['vi' => 'Doanh nghiệp của tôi', 'en' => 'My Business'],
    'my_orders' => ['vi' => 'Đơn hàng của tôi', 'en' => 'My Orders'],
    'my_products' => ['vi' => 'Sản phẩm của tôi', 'en' => 'My Products'],
    'my_subscription' => ['vi' => 'Gói đăng ký của tôi', 'en' => 'My Subscription'],
    'notifications' => ['vi' => 'Thông báo', 'en' => 'Notifications'],
    'orders' => ['vi' => 'Đơn hàng', 'en' => 'Orders'],
    'photo_gallery' => ['vi' => 'Thư viện ảnh', 'en' => 'Photo Gallery'],
    'privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
    'process_selector' => ['vi' => 'Bộ chọn quy trình', 'en' => 'Process Selector'],
    'product_management' => ['vi' => 'Quản lý sản phẩm', 'en' => 'Product Management'],
    'reports' => ['vi' => 'Báo cáo', 'en' => 'Reports'],
    'saved' => ['vi' => 'Đã lưu', 'en' => 'Saved'],
    'search_discovery' => ['vi' => 'Tìm kiếm & Khám phá', 'en' => 'Search & Discovery'],
    'standards_compliance' => ['vi' => 'Tuân thủ tiêu chuẩn', 'en' => 'Standards Compliance'],
    'supplier_dashboard' => ['vi' => 'Bảng điều khiển Nhà cung cấp', 'en' => 'Supplier Dashboard'],
    'technical_database' => ['vi' => 'Cơ sở dữ liệu kỹ thuật', 'en' => 'Technical Database'],
    'technical_documentation' => ['vi' => 'Tài liệu kỹ thuật', 'en' => 'Technical Documentation'],
    'technical_drawings' => ['vi' => 'Bản vẽ kỹ thuật', 'en' => 'Technical Drawings'],
    'technical_resources' => ['vi' => 'Tài nguyên kỹ thuật', 'en' => 'Technical Resources'],
    'terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of Service'],
    'tools_calculators' => ['vi' => 'Công cụ & Máy tính', 'en' => 'Tools & Calculators'],
    'tutorials_guides' => ['vi' => 'Hướng dẫn & Thủ thuật', 'en' => 'Tutorials & Guides'],
    'user_management' => ['vi' => 'Quản lý người dùng', 'en' => 'User Management'],
    'whats_new' => ['vi' => 'Có gì mới', 'en' => 'What\'s New'],
];

$languages = ['vi', 'en'];
$totalAdded = 0;

foreach ($languages as $lang) {
    $filePath = "$basePath/resources/lang/$lang/ui/common.php";
    
    if (!file_exists($filePath)) {
        echo "⚠️ File not found: $filePath\n";
        continue;
    }
    
    echo "📁 Processing: $lang/ui/common.php\n";
    
    // Read current file
    $currentTranslations = include $filePath;
    
    if (!is_array($currentTranslations)) {
        echo "❌ Error: File does not return an array\n";
        continue;
    }
    
    $added = 0;
    
    // Add missing keys
    foreach ($missingKeys as $key => $translations) {
        if (!isset($currentTranslations[$key])) {
            $currentTranslations[$key] = $translations[$lang];
            $added++;
            $totalAdded++;
        }
    }
    
    if ($added > 0) {
        // Sort keys alphabetically
        ksort($currentTranslations);
        
        // Generate new file content
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * " . ucfirst($lang === 'vi' ? 'Vietnamese' : 'English') . " translations for ui/common\n";
        $content .= " * Components localization - Updated: " . date('Y-m-d H:i:s') . "\n";
        $content .= " * Keys: " . count($currentTranslations) . "\n";
        $content .= " */\n\n";
        $content .= "return [\n";
        
        foreach ($currentTranslations as $key => $value) {
            if (is_array($value)) {
                $content .= "    '$key' => [\n";
                foreach ($value as $subKey => $subValue) {
                    $content .= "        '$subKey' => '" . addslashes($subValue) . "',\n";
                }
                $content .= "    ],\n";
            } else {
                $content .= "    '$key' => '" . addslashes($value) . "',\n";
            }
        }
        
        $content .= "];\n";
        
        // Write file
        file_put_contents($filePath, $content);
        echo "   ✅ Added $added keys to $lang/ui/common.php\n";
    } else {
        echo "   ℹ️ No new keys to add to $lang/ui/common.php\n";
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Total keys added: $totalAdded\n";
echo "Languages updated: " . count($languages) . "\n";

if ($totalAdded > 0) {
    echo "\n🧪 Testing some added keys:\n";
    
    // Bootstrap Laravel to test
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $testKeys = [
        'ui/common.admin_dashboard',
        'ui/common.technical_resources',
        'ui/common.cad_library',
        'ui/common.about_us'
    ];
    
    foreach ($testKeys as $key) {
        $result = __($key);
        $status = ($result === $key) ? "❌ FAIL" : "✅ WORK";
        echo "   $status __('$key') → '$result'\n";
    }
}

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Clear cache: php artisan view:clear && php artisan cache:clear\n";
echo "2. Test the website navigation\n";
echo "3. Check if all navigation items now display proper text\n";
