<?php

/**
 * ADD MEGA MENU KEYS
 * Thêm tất cả keys thiếu cho components/menu/add-mega-menu.blade.php
 */

echo "=== ADDING MEGA MENU KEYS ===\n\n";

// All add mega menu keys
$addMenuKeys = [
    // Create Content Section
    'add_menu.create_content.title' => ['vi' => 'Tạo nội dung', 'en' => 'Create Content'],
    'add_menu.create_content.new_thread' => ['vi' => 'Chủ đề mới', 'en' => 'New Thread'],
    'add_menu.create_content.new_thread_desc' => ['vi' => 'Bắt đầu thảo luận mới trong cộng đồng', 'en' => 'Start a new discussion in the community'],
    'add_menu.create_content.new_showcase' => ['vi' => 'Showcase mới', 'en' => 'New Showcase'],
    'add_menu.create_content.new_showcase_desc' => ['vi' => 'Chia sẻ dự án và thiết kế của bạn', 'en' => 'Share your projects and designs'],
    'add_menu.create_content.upload_photo' => ['vi' => 'Tải ảnh lên', 'en' => 'Upload Photo'],
    'add_menu.create_content.upload_photo_desc' => ['vi' => 'Chia sẻ hình ảnh với cộng đồng', 'en' => 'Share images with the community'],
    'add_menu.create_content.add_product' => ['vi' => 'Thêm sản phẩm', 'en' => 'Add Product'],
    'add_menu.create_content.add_product_desc' => ['vi' => 'Đăng sản phẩm lên marketplace', 'en' => 'List your product on marketplace'],
    'add_menu.create_content.become_seller' => ['vi' => 'Trở thành người bán', 'en' => 'Become Seller'],
    'add_menu.create_content.become_seller_desc' => ['vi' => 'Đăng ký tài khoản người bán', 'en' => 'Register as a seller account'],
    'add_menu.create_content.create_document' => ['vi' => 'Tạo tài liệu', 'en' => 'Create Document'],
    'add_menu.create_content.create_document_desc' => ['vi' => 'Viết hướng dẫn hoặc tài liệu kỹ thuật', 'en' => 'Write guides or technical documentation'],
    
    // Discovery Section
    'add_menu.discovery.title' => ['vi' => 'Khám phá', 'en' => 'Discovery'],
    'add_menu.discovery.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'add_menu.discovery.advanced_search_desc' => ['vi' => 'Tìm kiếm chi tiết với bộ lọc', 'en' => 'Detailed search with filters'],
    'add_menu.discovery.browse_tags' => ['vi' => 'Duyệt theo thẻ', 'en' => 'Browse Tags'],
    'add_menu.discovery.browse_tags_desc' => ['vi' => 'Khám phá nội dung theo chủ đề', 'en' => 'Explore content by topics'],
    'add_menu.discovery.community_stats' => ['vi' => 'Thống kê cộng đồng', 'en' => 'Community Stats'],
    'add_menu.discovery.community_stats_desc' => ['vi' => 'Xem số liệu và xu hướng', 'en' => 'View metrics and trends'],
    'add_menu.discovery.tech_trends' => ['vi' => 'Xu hướng công nghệ', 'en' => 'Tech Trends'],
    'add_menu.discovery.tech_trends_desc' => ['vi' => 'Theo dõi công nghệ mới nhất', 'en' => 'Follow latest technology'],
    'add_menu.discovery.recommendations' => ['vi' => 'Gợi ý', 'en' => 'Recommendations'],
    'add_menu.discovery.recommendations_desc' => ['vi' => 'Nội dung được đề xuất cho bạn', 'en' => 'Content recommended for you'],
    
    // Tools Section
    'add_menu.tools.title' => ['vi' => 'Công cụ', 'en' => 'Tools'],
    'add_menu.tools.calculator' => ['vi' => 'Máy tính', 'en' => 'Calculator'],
    'add_menu.tools.calculator_desc' => ['vi' => 'Công cụ tính toán kỹ thuật', 'en' => 'Engineering calculation tools'],
    'add_menu.tools.unit_converter' => ['vi' => 'Chuyển đổi đơn vị', 'en' => 'Unit Converter'],
    'add_menu.tools.unit_converter_desc' => ['vi' => 'Chuyển đổi các đơn vị đo lường', 'en' => 'Convert measurement units'],
    'add_menu.tools.material_lookup' => ['vi' => 'Tra cứu vật liệu', 'en' => 'Material Lookup'],
    'add_menu.tools.material_lookup_desc' => ['vi' => 'Tìm thông tin về vật liệu', 'en' => 'Find material information'],
    'add_menu.tools.design_tools' => ['vi' => 'Công cụ thiết kế', 'en' => 'Design Tools'],
    'add_menu.tools.design_tools_desc' => ['vi' => 'Công cụ hỗ trợ thiết kế', 'en' => 'Design assistance tools'],
    'add_menu.tools.mobile_app' => ['vi' => 'Ứng dụng di động', 'en' => 'Mobile App'],
    'add_menu.tools.mobile_app_desc' => ['vi' => 'Tải ứng dụng MechaMap', 'en' => 'Download MechaMap app'],
    'add_menu.tools.api_integration' => ['vi' => 'Tích hợp API', 'en' => 'API Integration'],
    'add_menu.tools.api_integration_desc' => ['vi' => 'Kết nối với hệ thống của bạn', 'en' => 'Connect with your systems'],
    
    // Community Section
    'add_menu.community.title' => ['vi' => 'Cộng đồng', 'en' => 'Community'],
    'add_menu.community.find_experts' => ['vi' => 'Tìm chuyên gia', 'en' => 'Find Experts'],
    'add_menu.community.find_experts_desc' => ['vi' => 'Kết nối với các chuyên gia', 'en' => 'Connect with experts'],
    'add_menu.community.business_connect' => ['vi' => 'Kết nối doanh nghiệp', 'en' => 'Business Connect'],
    'add_menu.community.business_connect_desc' => ['vi' => 'Mạng lưới doanh nghiệp', 'en' => 'Business networking'],
    'add_menu.community.mentorship' => ['vi' => 'Cố vấn', 'en' => 'Mentorship'],
    'add_menu.community.mentorship_desc' => ['vi' => 'Chương trình cố vấn', 'en' => 'Mentorship program'],
    'add_menu.community.job_opportunities' => ['vi' => 'Cơ hội việc làm', 'en' => 'Job Opportunities'],
    'add_menu.community.job_opportunities_desc' => ['vi' => 'Tìm việc làm trong ngành', 'en' => 'Find industry jobs'],
    'add_menu.community.professional_groups' => ['vi' => 'Nhóm chuyên nghiệp', 'en' => 'Professional Groups'],
    'add_menu.community.professional_groups_desc' => ['vi' => 'Tham gia nhóm chuyên ngành', 'en' => 'Join specialized groups'],
    'add_menu.community.events' => ['vi' => 'Sự kiện', 'en' => 'Events'],
    'add_menu.community.events_desc' => ['vi' => 'Hội thảo và sự kiện ngành', 'en' => 'Industry seminars and events'],
    
    // Support Section
    'add_menu.support.title' => ['vi' => 'Hỗ trợ', 'en' => 'Support'],
    'add_menu.support.faq' => ['vi' => 'Câu hỏi thường gặp', 'en' => 'FAQ'],
    'add_menu.support.faq_desc' => ['vi' => 'Câu trả lời cho các thắc mắc', 'en' => 'Answers to common questions'],
    'add_menu.support.contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
    'add_menu.support.contact_desc' => ['vi' => 'Liên hệ đội ngũ hỗ trợ', 'en' => 'Contact support team'],
    'add_menu.support.about' => ['vi' => 'Về chúng tôi', 'en' => 'About Us'],
    'add_menu.support.about_desc' => ['vi' => 'Tìm hiểu về MechaMap', 'en' => 'Learn about MechaMap'],
    
    // Status badges
    'add_menu.status.coming_soon' => ['vi' => 'Sắp ra mắt', 'en' => 'Coming Soon'],
    'add_menu.status.beta' => ['vi' => 'Beta', 'en' => 'Beta'],
    'add_menu.status.new' => ['vi' => 'Mới', 'en' => 'New'],
    
    // Footer
    'add_menu.footer.quick_tip' => ['vi' => 'Mẹo: Sử dụng phím tắt để tạo nội dung nhanh hơn', 'en' => 'Tip: Use shortcuts to create content faster'],
    'add_menu.footer.keyboard_shortcut' => ['vi' => 'Phím tắt tạo nội dung', 'en' => 'Create content shortcut'],
    'add_menu.footer.dark_mode' => ['vi' => 'Chế độ tối có sẵn trong cài đặt', 'en' => 'Dark mode available in settings'],
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

echo "📁 Processing add mega menu keys for navigation.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/navigation.php";
if (addKeysToFile($viFile, $addMenuKeys, 'vi')) {
    $totalAdded = count($addMenuKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/navigation.php";
addKeysToFile($enFile, $addMenuKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total add menu keys added: " . count($addMenuKeys) . "\n";

echo "\n✅ Add mega menu keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
