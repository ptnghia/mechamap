<?php

/**
 * ADD MARKETPLACE PRODUCTS SHOW KEYS
 * Thêm tất cả keys thiếu cho marketplace/products/show.blade.php
 */

echo "=== ADDING MARKETPLACE PRODUCTS SHOW KEYS ===\n\n";

// All marketplace products show keys organized by category
$marketplaceProductsShowKeys = [
    // Marketplace navigation keys
    'marketplace.home' => ['vi' => 'Trang chủ', 'en' => 'Home'],
    'marketplace.marketplace' => ['vi' => 'Thị trường', 'en' => 'Marketplace'],
    
    // Product keys
    'products.title' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
    'products.verified' => ['vi' => 'Đã xác minh', 'en' => 'Verified'],
    'products.service' => ['vi' => 'Dịch vụ', 'en' => 'Service'],
    'products.manufacturer' => ['vi' => 'Nhà sản xuất', 'en' => 'Manufacturer'],
    
    // Marketplace general keys
    'marketplace.sold_by' => ['vi' => 'Được bán bởi', 'en' => 'Sold by'],
    'marketplace.seller_not_available' => ['vi' => 'Người bán không có sẵn', 'en' => 'Seller not available'],
    'marketplace.reviews' => ['vi' => 'đánh giá', 'en' => 'reviews'],
    'marketplace.in_stock' => ['vi' => 'Còn hàng', 'en' => 'In Stock'],
    'marketplace.out_of_stock' => ['vi' => 'Hết hàng', 'en' => 'Out of Stock'],
    'marketplace.add_to_cart' => ['vi' => 'Thêm vào giỏ hàng', 'en' => 'Add to Cart'],
    'marketplace.add_to_wishlist' => ['vi' => 'Thêm vào danh sách yêu thích', 'en' => 'Add to Wishlist'],
    'marketplace.product_description' => ['vi' => 'Mô tả sản phẩm', 'en' => 'Product Description'],
    'marketplace.technical_specifications' => ['vi' => 'Thông số kỹ thuật', 'en' => 'Technical Specifications'],
    'marketplace.lead_time' => ['vi' => 'Thời gian giao hàng', 'en' => 'Lead Time'],
    'marketplace.minimum_order' => ['vi' => 'Đơn hàng tối thiểu', 'en' => 'Minimum Order'],
    'marketplace.precision' => ['vi' => 'Độ chính xác', 'en' => 'Precision'],
    'marketplace.quality_standard' => ['vi' => 'Tiêu chuẩn chất lượng', 'en' => 'Quality Standard'],
    'marketplace.material_options' => ['vi' => 'Tùy chọn vật liệu', 'en' => 'Material Options'],
    'marketplace.delivery' => ['vi' => 'Giao hàng', 'en' => 'Delivery'],
    'marketplace.related_products' => ['vi' => 'Sản phẩm liên quan', 'en' => 'Related Products'],
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

echo "📁 Processing marketplace products show keys for marketplace.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $marketplaceProductsShowKeys, 'vi')) {
    $totalAdded = count($marketplaceProductsShowKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $marketplaceProductsShowKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total marketplace products show keys added: " . count($marketplaceProductsShowKeys) . "\n";

echo "\n✅ Marketplace products show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
