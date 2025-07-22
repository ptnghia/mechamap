<?php

/**
 * ADD MOBILE NAV KEYS
 * Thêm tất cả keys thiếu cho components/mobile-nav.blade.php
 */

echo "=== ADDING MOBILE NAV KEYS ===\n\n";

// All mobile nav keys organized by category
$mobileNavKeys = [
    // Forum keys
    'forum_keys' => [
        'threads.title' => ['vi' => 'Chủ đề', 'en' => 'Threads'],
    ],
    
    // UI keys
    'ui_keys' => [
        'community.browse_categories' => ['vi' => 'Duyệt danh mục', 'en' => 'Browse Categories'],
        'search.advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    ],
    
    // Common keys
    'common_keys' => [
        'home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
        'community' => ['vi' => 'Cộng đồng', 'en' => 'Community'],
        'popular_topics' => ['vi' => 'Chủ đề phổ biến', 'en' => 'Popular Topics'],
        'recent_discussions' => ['vi' => 'Thảo luận gần đây', 'en' => 'Recent Discussions'],
        'trending' => ['vi' => 'Xu hướng', 'en' => 'Trending'],
        'most_viewed' => ['vi' => 'Xem nhiều nhất', 'en' => 'Most Viewed'],
        'hot_topics' => ['vi' => 'Chủ đề nóng', 'en' => 'Hot Topics'],
        'member_directory' => ['vi' => 'Danh bạ thành viên', 'en' => 'Member Directory'],
        'events_webinars' => ['vi' => 'Sự kiện & Webinar', 'en' => 'Events & Webinars'],
        'job_board' => ['vi' => 'Bảng việc làm', 'en' => 'Job Board'],
        'coming_soon' => ['vi' => 'Sắp ra mắt', 'en' => 'Coming Soon'],
        'showcase' => ['vi' => 'Showcase', 'en' => 'Showcase'],
        'marketplace_title' => ['vi' => 'Marketplace', 'en' => 'Marketplace'],
        'company_profiles' => ['vi' => 'Hồ sơ công ty', 'en' => 'Company Profiles'],
        'search_discovery' => ['vi' => 'Tìm kiếm & Khám phá', 'en' => 'Search & Discovery'],
        'advanced_search' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
        'photo_gallery' => ['vi' => 'Thư viện ảnh', 'en' => 'Photo Gallery'],
        'browse_by_tags' => ['vi' => 'Duyệt theo thẻ', 'en' => 'Browse by Tags'],
        'help_support' => ['vi' => 'Trợ giúp & Hỗ trợ', 'en' => 'Help & Support'],
        'faq' => ['vi' => 'Câu hỏi thường gặp', 'en' => 'FAQ'],
        'help_center' => ['vi' => 'Trung tâm trợ giúp', 'en' => 'Help Center'],
        'contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
        'about_mechamap' => ['vi' => 'Về MechaMap', 'en' => 'About MechaMap'],
        'about_us' => ['vi' => 'Về chúng tôi', 'en' => 'About Us'],
        'terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of Service'],
        'privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
        'my_account' => ['vi' => 'Tài khoản của tôi', 'en' => 'My Account'],
        'my_profile' => ['vi' => 'Hồ sơ của tôi', 'en' => 'My Profile'],
        'account_settings' => ['vi' => 'Cài đặt tài khoản', 'en' => 'Account Settings'],
        'notifications' => ['vi' => 'Thông báo', 'en' => 'Notifications'],
        'logout' => ['vi' => 'Đăng xuất', 'en' => 'Logout'],
        'login' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
        'register' => ['vi' => 'Đăng ký', 'en' => 'Register'],
    ],
    
    // Marketplace keys
    'marketplace_keys' => [
        'products.all' => ['vi' => 'Tất cả sản phẩm', 'en' => 'All Products'],
        'categories.title' => ['vi' => 'Danh mục', 'en' => 'Categories'],
        'suppliers.title' => ['vi' => 'Nhà cung cấp', 'en' => 'Suppliers'],
        'products.featured' => ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products'],
        'rfq.title' => ['vi' => 'Yêu cầu báo giá', 'en' => 'Request for Quote'],
        'bulk_orders' => ['vi' => 'Đơn hàng số lượng lớn', 'en' => 'Bulk Orders'],
        'my_orders' => ['vi' => 'Đơn hàng của tôi', 'en' => 'My Orders'],
        'cart.title' => ['vi' => 'Giỏ hàng', 'en' => 'Shopping Cart'],
        'downloads' => ['vi' => 'Tải xuống', 'en' => 'Downloads'],
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
    'forum_keys' => 'forum',
    'ui_keys' => 'ui',
    'common_keys' => 'common',
    'marketplace_keys' => 'marketplace',
];

$totalAdded = 0;

foreach ($mobileNavKeys as $category => $keys) {
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
echo "Total mobile nav keys added: $totalAdded\n";
echo "Categories processed: " . count($mobileNavKeys) . "\n";

echo "\n✅ Mobile nav keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
