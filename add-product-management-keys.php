<?php

/**
 * ADD PRODUCT MANAGEMENT KEYS
 * Thêm tất cả keys thiếu cho supplier/products/create.blade.php
 */

echo "=== ADDING PRODUCT MANAGEMENT KEYS ===\n\n";

// All product management keys from supplier/products/create.blade.php
$productKeys = [
    // Main actions
    'product_management.create_product' => ['vi' => 'Tạo sản phẩm', 'en' => 'Create Product'],
    'product_management.create_physical_product' => ['vi' => 'Tạo sản phẩm vật lý mới', 'en' => 'Create new physical product'],
    'product_management.back' => ['vi' => 'Quay lại', 'en' => 'Back'],
    
    // Basic Information
    'product_management.basic_information' => ['vi' => 'Thông tin cơ bản', 'en' => 'Basic Information'],
    'product_management.product_name' => ['vi' => 'Tên sản phẩm', 'en' => 'Product Name'],
    'product_management.category' => ['vi' => 'Danh mục', 'en' => 'Category'],
    'product_management.select_category' => ['vi' => 'Chọn danh mục', 'en' => 'Select Category'],
    'product_management.material' => ['vi' => 'Vật liệu', 'en' => 'Material'],
    'product_management.material_placeholder' => ['vi' => 'Ví dụ: Thép không gỉ, Nhôm, Nhựa...', 'en' => 'e.g., Stainless Steel, Aluminum, Plastic...'],
    'product_management.short_description' => ['vi' => 'Mô tả ngắn', 'en' => 'Short Description'],
    'product_management.short_description_placeholder' => ['vi' => 'Mô tả ngắn gọn về sản phẩm...', 'en' => 'Brief description of the product...'],
    'product_management.detailed_description' => ['vi' => 'Mô tả chi tiết', 'en' => 'Detailed Description'],
    'product_management.detailed_description_placeholder' => ['vi' => 'Mô tả chi tiết về sản phẩm, tính năng, ứng dụng...', 'en' => 'Detailed description of product, features, applications...'],
    
    // Pricing & Inventory
    'product_management.pricing_inventory' => ['vi' => 'Giá cả & Kho hàng', 'en' => 'Pricing & Inventory'],
    'product_management.selling_price' => ['vi' => 'Giá bán', 'en' => 'Selling Price'],
    'product_management.currency_vnd' => ['vi' => 'VNĐ', 'en' => 'VND'],
    'product_management.sale_price' => ['vi' => 'Giá khuyến mãi', 'en' => 'Sale Price'],
    'product_management.stock_quantity' => ['vi' => 'Số lượng tồn kho', 'en' => 'Stock Quantity'],
    'product_management.inventory_management' => ['vi' => 'Quản lý kho', 'en' => 'Inventory Management'],
    'product_management.auto_manage_stock' => ['vi' => 'Tự động quản lý tồn kho', 'en' => 'Automatically manage stock'],
    'product_management.auto_manage_stock_help' => ['vi' => 'Hệ thống sẽ tự động cập nhật số lượng tồn kho khi có đơn hàng', 'en' => 'System will automatically update stock quantity when orders are placed'],
    
    // Technical Specifications
    'product_management.technical_specifications' => ['vi' => 'Thông số kỹ thuật', 'en' => 'Technical Specifications'],
    'product_management.manufacturing_process' => ['vi' => 'Quy trình sản xuất', 'en' => 'Manufacturing Process'],
    'product_management.manufacturing_process_placeholder' => ['vi' => 'Ví dụ: Gia công CNC, Đúc, Hàn...', 'en' => 'e.g., CNC Machining, Casting, Welding...'],
    'product_management.tags' => ['vi' => 'Thẻ', 'en' => 'Tags'],
    'product_management.tags_placeholder' => ['vi' => 'Nhập các thẻ, cách nhau bằng dấu phẩy', 'en' => 'Enter tags separated by commas'],
    'product_management.detailed_technical_specs' => ['vi' => 'Thông số kỹ thuật chi tiết', 'en' => 'Detailed Technical Specifications'],
    'product_management.spec_name_placeholder' => ['vi' => 'Tên thông số', 'en' => 'Specification name'],
    'product_management.spec_value_placeholder' => ['vi' => 'Giá trị', 'en' => 'Value'],
    'product_management.spec_unit_placeholder' => ['vi' => 'Đơn vị', 'en' => 'Unit'],
    'product_management.add_specification' => ['vi' => 'Thêm thông số', 'en' => 'Add Specification'],
    
    // Product Images
    'product_management.product_images' => ['vi' => 'Hình ảnh sản phẩm', 'en' => 'Product Images'],
    'product_management.upload_images' => ['vi' => 'Tải lên hình ảnh', 'en' => 'Upload Images'],
    'product_management.image_upload_help' => ['vi' => 'Tải lên tối đa 10 hình ảnh. Định dạng: JPG, PNG, WEBP. Kích thước tối đa: 5MB mỗi file.', 'en' => 'Upload up to 10 images. Formats: JPG, PNG, WEBP. Max size: 5MB per file.'],
    
    // Actions
    'product_management.actions' => ['vi' => 'Hành động', 'en' => 'Actions'],
    'product_management.create_product_btn' => ['vi' => 'Tạo sản phẩm', 'en' => 'Create Product'],
    'product_management.save_draft' => ['vi' => 'Lưu bản nháp', 'en' => 'Save Draft'],
    'product_management.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
    
    // Help Guide
    'product_management.help_guide' => ['vi' => 'Hướng dẫn', 'en' => 'Help Guide'],
    'product_management.help_complete_info' => ['vi' => 'Điền đầy đủ thông tin sản phẩm để tăng khả năng bán hàng', 'en' => 'Complete product information to increase sales potential'],
    'product_management.help_quality_images' => ['vi' => 'Sử dụng hình ảnh chất lượng cao để thu hút khách hàng', 'en' => 'Use high-quality images to attract customers'],
    'product_management.help_detailed_description' => ['vi' => 'Mô tả chi tiết giúp khách hàng hiểu rõ sản phẩm', 'en' => 'Detailed descriptions help customers understand the product'],
    'product_management.help_approval_time' => ['vi' => 'Sản phẩm sẽ được duyệt trong vòng 24-48 giờ', 'en' => 'Products will be approved within 24-48 hours'],
    
    // Validation
    'product_management.price_validation_error' => ['vi' => 'Giá khuyến mãi phải thấp hơn giá bán thường', 'en' => 'Sale price must be lower than regular selling price'],
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

echo "📁 Processing product management keys for marketplace.php files\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $productKeys, 'vi')) {
    $totalAdded = count($productKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $productKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($productKeys) . "\n";
echo "Keys processed: " . count($productKeys) . "\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'marketplace.product_management.create_product',
    'marketplace.product_management.basic_information', 
    'marketplace.product_management.pricing_inventory',
    'marketplace.product_management.technical_specifications'
];

foreach ($testKeys as $key) {
    echo "  Testing __('$key')...\n";
}

echo "\n✅ Product management keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
