<?php

/**
 * ADD HEADER KEYS
 * Thêm tất cả keys thiếu cho components/header.blade.php
 */

echo "=== ADDING HEADER KEYS ===\n\n";

// All header keys organized by category
$headerKeys = [
    // Search keys
    'search' => [
        'form.placeholder' => ['vi' => 'Tìm kiếm...', 'en' => 'Search...'],
        'scope.all_content' => ['vi' => 'Tất cả nội dung', 'en' => 'All content'],
        'scope.in_thread' => ['vi' => 'Trong chủ đề', 'en' => 'In thread'],
        'scope.in_forum' => ['vi' => 'Trong diễn đàn', 'en' => 'In forum'],
        'actions.advanced' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced search'],
        'history.recent' => ['vi' => 'Tìm kiếm gần đây', 'en' => 'Recent searches'],
        'history.empty' => ['vi' => 'Chưa có tìm kiếm nào', 'en' => 'No recent searches'],
        'suggestions.popular' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular searches'],
    ],
    
    // Navigation keys
    'navigation' => [
        'main.community' => ['vi' => 'Cộng đồng', 'en' => 'Community'],
        'main.showcase' => ['vi' => 'Showcase', 'en' => 'Showcase'],
        'main.marketplace' => ['vi' => 'Marketplace', 'en' => 'Marketplace'],
        'actions.add' => ['vi' => 'Thêm', 'en' => 'Add'],
        'main.more' => ['vi' => 'Thêm', 'en' => 'More'],
        'sections.search_discovery' => ['vi' => 'Tìm kiếm & Khám phá', 'en' => 'Search & Discovery'],
        'sections.help_support' => ['vi' => 'Trợ giúp & Hỗ trợ', 'en' => 'Help & Support'],
        'sections.about_mechamap' => ['vi' => 'Về MechaMap', 'en' => 'About MechaMap'],
        'pages.photo_gallery' => ['vi' => 'Thư viện ảnh', 'en' => 'Photo Gallery'],
        'pages.browse_by_tags' => ['vi' => 'Duyệt theo thẻ', 'en' => 'Browse by Tags'],
        'pages.faq' => ['vi' => 'Câu hỏi thường gặp', 'en' => 'FAQ'],
        'pages.help_center' => ['vi' => 'Trung tâm trợ giúp', 'en' => 'Help Center'],
        'pages.contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
        'pages.about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About Us'],
        'pages.terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of Service'],
        'pages.privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
        
        // Admin navigation
        'admin.dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
        'admin.user_management' => ['vi' => 'Quản lý người dùng', 'en' => 'User Management'],
        'admin.forum_management' => ['vi' => 'Quản lý diễn đàn', 'en' => 'Forum Management'],
        'admin.marketplace_management' => ['vi' => 'Quản lý marketplace', 'en' => 'Marketplace Management'],
        
        // Supplier navigation
        'supplier.dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
        'supplier.my_products' => ['vi' => 'Sản phẩm của tôi', 'en' => 'My Products'],
        'supplier.orders' => ['vi' => 'Đơn hàng', 'en' => 'Orders'],
        'supplier.reports' => ['vi' => 'Báo cáo', 'en' => 'Reports'],
        'supplier.product_management' => ['vi' => 'Quản lý sản phẩm', 'en' => 'Product Management'],
        'supplier.my_orders' => ['vi' => 'Đơn hàng của tôi', 'en' => 'My Orders'],
        
        // Brand navigation
        'brand.dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
        'brand.market_insights' => ['vi' => 'Thông tin thị trường', 'en' => 'Market Insights'],
        'brand.marketplace_analytics' => ['vi' => 'Phân tích marketplace', 'en' => 'Marketplace Analytics'],
        'brand.promotion_opportunities' => ['vi' => 'Cơ hội quảng bá', 'en' => 'Promotion Opportunities'],
        'brand.market_analysis' => ['vi' => 'Phân tích thị trường', 'en' => 'Market Analysis'],
        
        // Manufacturer navigation
        'manufacturer.dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
        'manufacturer.design_management' => ['vi' => 'Quản lý thiết kế', 'en' => 'Design Management'],
        'manufacturer.download_orders' => ['vi' => 'Đơn hàng tải xuống', 'en' => 'Download Orders'],
        
        // User navigation
        'user.messages' => ['vi' => 'Tin nhắn', 'en' => 'Messages'],
        'user.notifications' => ['vi' => 'Thông báo', 'en' => 'Notifications'],
        'user.saved' => ['vi' => 'Đã lưu', 'en' => 'Saved'],
        'user.my_showcase' => ['vi' => 'Showcase của tôi', 'en' => 'My Showcase'],
        'user.my_business' => ['vi' => 'Doanh nghiệp của tôi', 'en' => 'My Business'],
        'user.verification_status' => ['vi' => 'Trạng thái xác thực', 'en' => 'Verification Status'],
        'user.my_subscription' => ['vi' => 'Gói đăng ký', 'en' => 'My Subscription'],
    ],
    
    // Common technical keys
    'common' => [
        'buttons.search' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'technical.resources' => ['vi' => 'Tài nguyên kỹ thuật', 'en' => 'Technical Resources'],
        'technical.database' => ['vi' => 'Cơ sở dữ liệu', 'en' => 'Database'],
        'technical.materials_database' => ['vi' => 'Cơ sở dữ liệu vật liệu', 'en' => 'Materials Database'],
        'technical.engineering_standards' => ['vi' => 'Tiêu chuẩn kỹ thuật', 'en' => 'Engineering Standards'],
        'technical.manufacturing_processes' => ['vi' => 'Quy trình sản xuất', 'en' => 'Manufacturing Processes'],
        'technical.design_resources' => ['vi' => 'Tài nguyên thiết kế', 'en' => 'Design Resources'],
        'technical.cad_library' => ['vi' => 'Thư viện CAD', 'en' => 'CAD Library'],
        'technical.technical_drawings' => ['vi' => 'Bản vẽ kỹ thuật', 'en' => 'Technical Drawings'],
        'technical.tools_calculators' => ['vi' => 'Công cụ & Máy tính', 'en' => 'Tools & Calculators'],
        'technical.material_cost_calculator' => ['vi' => 'Máy tính chi phí vật liệu', 'en' => 'Material Cost Calculator'],
        'technical.process_selector' => ['vi' => 'Bộ chọn quy trình', 'en' => 'Process Selector'],
        'technical.standards_compliance' => ['vi' => 'Tuân thủ tiêu chuẩn', 'en' => 'Standards Compliance'],
        'knowledge.title' => ['vi' => 'Kiến thức', 'en' => 'Knowledge'],
        'knowledge.learning_resources' => ['vi' => 'Tài nguyên học tập', 'en' => 'Learning Resources'],
        'knowledge.knowledge_base' => ['vi' => 'Cơ sở kiến thức', 'en' => 'Knowledge Base'],
        'knowledge.tutorials_guides' => ['vi' => 'Hướng dẫn & Bài học', 'en' => 'Tutorials & Guides'],
        'knowledge.technical_documentation' => ['vi' => 'Tài liệu kỹ thuật', 'en' => 'Technical Documentation'],
        'knowledge.industry_updates' => ['vi' => 'Cập nhật ngành', 'en' => 'Industry Updates'],
        'knowledge.industry_news' => ['vi' => 'Tin tức ngành', 'en' => 'Industry News'],
        'knowledge.whats_new' => ['vi' => 'Có gì mới', 'en' => "What's New"],
        'knowledge.industry_reports' => ['vi' => 'Báo cáo ngành', 'en' => 'Industry Reports'],
        'messages.marked_all_read' => ['vi' => 'Đã đánh dấu tất cả là đã đọc', 'en' => 'Marked all as read'],
    ],
    
    // Marketplace cart keys
    'marketplace' => [
        'cart.title' => ['vi' => 'Giỏ hàng', 'en' => 'Shopping Cart'],
        'cart.empty_message' => ['vi' => 'Giỏ hàng trống', 'en' => 'Your cart is empty'],
        'cart.add_items' => ['vi' => 'Thêm sản phẩm vào giỏ hàng', 'en' => 'Add items to your cart'],
    ],
    
    // Nav user keys
    'nav' => [
        'user.profile' => ['vi' => 'Hồ sơ', 'en' => 'Profile'],
        'user.settings' => ['vi' => 'Cài đặt', 'en' => 'Settings'],
    ],
    
    // Auth keys
    'auth' => [
        'register.title' => ['vi' => 'Đăng ký', 'en' => 'Register'],
        'logout.title' => ['vi' => 'Đăng xuất', 'en' => 'Logout'],
    ],
    
    // UI keys
    'ui' => [
        'buttons.add' => ['vi' => 'Thêm', 'en' => 'Add'],
        'common.light_mode' => ['vi' => 'Chế độ sáng', 'en' => 'Light mode'],
        'common.dark_mode' => ['vi' => 'Chế độ tối', 'en' => 'Dark mode'],
    ],
    
    // Forum search keys
    'forum' => [
        'search.cad_files' => ['vi' => 'tệp CAD', 'en' => 'CAD files'],
        'search.iso_standards' => ['vi' => 'tiêu chuẩn ISO', 'en' => 'ISO standards'],
        'search.forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
        'search.threads' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
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

foreach ($headerKeys as $category => $keys) {
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
echo "Total header keys added: $totalAdded\n";
echo "Categories processed: " . count($headerKeys) . "\n";

echo "\n✅ Header keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
