<?php

/**
 * ADD CART KEYS
 * Thêm tất cả keys thiếu cho marketplace/cart/index.blade.php
 */

echo "=== ADDING CART KEYS ===\n\n";

// All cart keys from marketplace/cart/index.blade.php
$cartKeys = [
    // Main cart
    'cart.title' => ['vi' => 'Giỏ hàng', 'en' => 'Shopping Cart'],
    'cart.shopping_cart' => ['vi' => 'Giỏ hàng', 'en' => 'Shopping Cart'],
    'cart.items' => ['vi' => 'sản phẩm', 'en' => 'items'],
    
    // Cart actions
    'cart.select_all' => ['vi' => 'Chọn tất cả', 'en' => 'Select All'],
    'cart.remove_selected' => ['vi' => 'Xóa đã chọn', 'en' => 'Remove Selected'],
    'cart.clear_cart' => ['vi' => 'Xóa giỏ hàng', 'en' => 'Clear Cart'],
    'cart.remove' => ['vi' => 'Xóa', 'en' => 'Remove'],
    'cart.save_for_later' => ['vi' => 'Lưu để mua sau', 'en' => 'Save for Later'],
    
    // Empty cart
    'cart.empty_cart' => ['vi' => 'Giỏ hàng trống', 'en' => 'Your cart is empty'],
    'cart.empty_cart_message' => ['vi' => 'Bạn chưa có sản phẩm nào trong giỏ hàng', 'en' => 'You have no items in your cart'],
    'cart.continue_shopping' => ['vi' => 'Tiếp tục mua sắm', 'en' => 'Continue Shopping'],
    
    // Product status
    'cart.product_no_longer_available' => ['vi' => 'Sản phẩm không còn khả dụng', 'en' => 'Product no longer available'],
    'cart.available' => ['vi' => 'có sẵn', 'en' => 'available'],
    
    // Order summary
    'cart.order_summary' => ['vi' => 'Tóm tắt đơn hàng', 'en' => 'Order Summary'],
    'cart.subtotal' => ['vi' => 'Tạm tính', 'en' => 'Subtotal'],
    'cart.shipping' => ['vi' => 'Phí vận chuyển', 'en' => 'Shipping'],
    'cart.free' => ['vi' => 'Miễn phí', 'en' => 'Free'],
    'cart.calculate_shipping' => ['vi' => 'Tính phí vận chuyển', 'en' => 'Calculate Shipping'],
    'cart.tax' => ['vi' => 'Thuế', 'en' => 'Tax'],
    'cart.total' => ['vi' => 'Tổng cộng', 'en' => 'Total'],
    
    // Coupon
    'cart.coupon_code' => ['vi' => 'Mã giảm giá', 'en' => 'Coupon Code'],
    'cart.apply' => ['vi' => 'Áp dụng', 'en' => 'Apply'],
    
    // Checkout
    'cart.proceed_to_checkout' => ['vi' => 'Tiến hành thanh toán', 'en' => 'Proceed to Checkout'],
    'cart.ssl_encryption' => ['vi' => 'Bảo mật SSL', 'en' => 'SSL Encryption'],
    
    // Shipping information
    'cart.shipping_information' => ['vi' => 'Thông tin vận chuyển', 'en' => 'Shipping Information'],
    'cart.free_shipping_over' => ['vi' => 'Miễn phí vận chuyển cho đơn hàng trên 500.000 VNĐ', 'en' => 'Free shipping for orders over 500,000 VND'],
    'cart.standard_delivery' => ['vi' => 'Giao hàng tiêu chuẩn: 2-3 ngày làm việc', 'en' => 'Standard delivery: 2-3 business days'],
    'cart.express_delivery_available' => ['vi' => 'Có giao hàng nhanh trong ngày', 'en' => 'Express same-day delivery available'],
    
    // Recently viewed
    'cart.recently_viewed_products' => ['vi' => 'Sản phẩm đã xem gần đây', 'en' => 'Recently Viewed Products'],
    'cart.no_recently_viewed' => ['vi' => 'Chưa có sản phẩm nào được xem', 'en' => 'No recently viewed products'],
    
    // JavaScript messages
    'cart.clear_cart_confirm' => ['vi' => 'Bạn có chắc chắn muốn xóa tất cả sản phẩm khỏi giỏ hàng?', 'en' => 'Are you sure you want to clear all items from your cart?'],
    'cart.clear_cart_success' => ['vi' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng', 'en' => 'All items removed from cart'],
    'cart.clear_cart_failed' => ['vi' => 'Không thể xóa giỏ hàng', 'en' => 'Failed to clear cart'],
    'cart.remove_selected_confirm' => ['vi' => 'Bạn có chắc chắn muốn xóa :count sản phẩm đã chọn?', 'en' => 'Are you sure you want to remove :count selected items?'],
    'cart.remove_selected_failed' => ['vi' => 'Không thể xóa sản phẩm đã chọn', 'en' => 'Failed to remove selected items'],
    'cart.please_select_items' => ['vi' => 'Vui lòng chọn sản phẩm để xóa', 'en' => 'Please select items to remove'],
    'cart.coupon_required' => ['vi' => 'Vui lòng nhập mã giảm giá', 'en' => 'Please enter coupon code'],
    'cart.coupon_apply_failed' => ['vi' => 'Không thể áp dụng mã giảm giá', 'en' => 'Failed to apply coupon'],
    'cart.save_for_later_message' => ['vi' => 'Đã lưu sản phẩm để mua sau', 'en' => 'Item saved for later'],
    
    // General marketplace messages
    'loading' => ['vi' => 'Đang tải...', 'en' => 'Loading...'],
    'error' => ['vi' => 'Lỗi', 'en' => 'Error'],
    'success' => ['vi' => 'Thành công', 'en' => 'Success'],
    'warning' => ['vi' => 'Cảnh báo', 'en' => 'Warning'],
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

echo "📁 Processing cart keys for marketplace.php files\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $cartKeys, 'vi')) {
    $totalAdded = count($cartKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $cartKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($cartKeys) . "\n";
echo "Keys processed: " . count($cartKeys) . "\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'marketplace.cart.shopping_cart',
    'marketplace.cart.order_summary', 
    'marketplace.cart.proceed_to_checkout',
    'marketplace.cart.shipping_information'
];

foreach ($testKeys as $key) {
    echo "  Testing __('$key')...\n";
}

echo "\n✅ Cart keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
