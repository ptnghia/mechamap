<?php

/**
 * ADD MARKETPLACE MEGA MENU KEYS
 * Thêm tất cả keys thiếu cho components/menu/marketplace-mega-menu.blade.php
 */

echo "=== ADDING MARKETPLACE MEGA MENU KEYS ===\n\n";

// All marketplace mega menu keys
$marketplaceMegaMenuKeys = [
    // Main sections
    'discover_shopping' => ['vi' => 'Khám phá mua sắm', 'en' => 'Discover Shopping'],
    'by_purpose' => ['vi' => 'Theo mục đích', 'en' => 'By Purpose'],
    'suppliers_partners' => ['vi' => 'Nhà cung cấp & Đối tác', 'en' => 'Suppliers & Partners'],
    'account_support' => ['vi' => 'Tài khoản & Hỗ trợ', 'en' => 'Account & Support'],
    
    // Products section
    'products.all' => ['vi' => 'Tất cả sản phẩm', 'en' => 'All Products'],
    'products.all_desc' => ['vi' => 'Duyệt toàn bộ marketplace', 'en' => 'Browse entire marketplace'],
    'products.featured' => ['vi' => 'Sản phẩm nổi bật', 'en' => 'Featured Products'],
    'products.featured_desc' => ['vi' => 'Sản phẩm được đề xuất hàng đầu', 'en' => 'Top recommended products'],
    'products.newest' => ['vi' => 'Hàng mới nhất', 'en' => 'Newest Items'],
    'products.newest_desc' => ['vi' => 'Sản phẩm mới được thêm', 'en' => 'Recently added products'],
    'products.discounts' => ['vi' => 'Ưu đãi & Giảm giá', 'en' => 'Deals & Discounts'],
    'products.discounts_desc' => ['vi' => 'Tiết kiệm với các ưu đãi tốt nhất', 'en' => 'Save with best deals'],
    
    // Search
    'search.advanced' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
    'search.advanced_desc' => ['vi' => 'Tìm kiếm với bộ lọc chi tiết', 'en' => 'Search with detailed filters'],
    
    // Product types
    'products.digital' => ['vi' => 'Sản phẩm số', 'en' => 'Digital Products'],
    'products.digital_desc' => ['vi' => 'Tệp CAD, bản vẽ, phần mềm', 'en' => 'CAD files, drawings, software'],
    'products.new' => ['vi' => 'Sản phẩm mới', 'en' => 'New Products'],
    'products.new_desc' => ['vi' => 'Linh kiện và thiết bị mới', 'en' => 'New components and equipment'],
    'products.used' => ['vi' => 'Sản phẩm đã qua sử dụng', 'en' => 'Used Products'],
    'products.used_desc' => ['vi' => 'Máy móc và thiết bị đã qua sử dụng', 'en' => 'Pre-owned machinery and equipment'],
    
    // Suppliers
    'suppliers.all' => ['vi' => 'Tất cả nhà cung cấp', 'en' => 'All Suppliers'],
    'suppliers.all_desc' => ['vi' => 'Duyệt danh sách nhà cung cấp', 'en' => 'Browse supplier directory'],
    'suppliers.verified' => ['vi' => 'Nhà cung cấp đã xác thực', 'en' => 'Verified Suppliers'],
    'suppliers.verified_desc' => ['vi' => 'Đối tác đáng tin cậy đã xác thực', 'en' => 'Trusted verified partners'],
    'suppliers.top_sellers' => ['vi' => 'Nhà bán hàng hàng đầu', 'en' => 'Top Sellers'],
    'suppliers.top_sellers_desc' => ['vi' => 'Người bán có đánh giá cao nhất', 'en' => 'Highest rated sellers'],
    'company_profiles' => ['vi' => 'Hồ sơ công ty', 'en' => 'Company Profiles'],
    'company_profiles_desc' => ['vi' => 'Thông tin chi tiết về doanh nghiệp', 'en' => 'Detailed business information'],
    
    // Account & Support
    'cart.title' => ['vi' => 'Giỏ hàng', 'en' => 'Shopping Cart'],
    'cart.desc' => ['vi' => 'Xem và quản lý giỏ hàng', 'en' => 'View and manage your cart'],
    'my_orders' => ['vi' => 'Đơn hàng của tôi', 'en' => 'My Orders'],
    'my_orders_desc' => ['vi' => 'Theo dõi đơn hàng và lịch sử', 'en' => 'Track orders and history'],
    'wishlist' => ['vi' => 'Danh sách yêu thích', 'en' => 'Wishlist'],
    'wishlist_desc' => ['vi' => 'Lưu sản phẩm để mua sau', 'en' => 'Save items for later'],
    'seller_dashboard' => ['vi' => 'Bảng điều khiển người bán', 'en' => 'Seller Dashboard'],
    'seller_dashboard_desc' => ['vi' => 'Quản lý sản phẩm và bán hàng', 'en' => 'Manage products and sales'],
    'login_desc' => ['vi' => 'Đăng nhập vào tài khoản', 'en' => 'Sign in to your account'],
    'register_desc' => ['vi' => 'Tạo tài khoản mới', 'en' => 'Create new account'],
    'help_support' => ['vi' => 'Trợ giúp & Hỗ trợ', 'en' => 'Help & Support'],
    'help_support_desc' => ['vi' => 'Nhận trợ giúp và hỗ trợ', 'en' => 'Get help and support'],
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

echo "📁 Processing marketplace mega menu keys for marketplace.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceMegaMenuKeys, 'vi')) {
    $totalAdded = count($marketplaceMegaMenuKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceMegaMenuKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace mega menu keys added: " . count($marketplaceMegaMenuKeys) . "\n";

echo "\n✅ Marketplace mega menu keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
