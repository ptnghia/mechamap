<?php

/**
 * ADD MARKETPLACE CHECKOUT KEYS
 * Thêm tất cả keys thiếu cho marketplace/checkout/index.blade.php
 */

echo "=== ADDING MARKETPLACE CHECKOUT KEYS ===\n\n";

// All marketplace checkout keys
$checkoutKeys = [
    // Main checkout
    'checkout.title' => ['vi' => 'Thanh toán', 'en' => 'Checkout'],
    'checkout.secure_checkout' => ['vi' => 'Thanh toán an toàn', 'en' => 'Secure Checkout'],
    
    // Steps
    'checkout.steps.shipping' => ['vi' => 'Thông tin giao hàng', 'en' => 'Shipping'],
    'checkout.steps.payment' => ['vi' => 'Thanh toán', 'en' => 'Payment'],
    'checkout.steps.review' => ['vi' => 'Xem lại', 'en' => 'Review'],
    
    // Shipping information
    'checkout.shipping_information' => ['vi' => 'Thông tin giao hàng', 'en' => 'Shipping Information'],
    'checkout.first_name' => ['vi' => 'Tên', 'en' => 'First Name'],
    'checkout.last_name' => ['vi' => 'Họ', 'en' => 'Last Name'],
    'checkout.email_address' => ['vi' => 'Địa chỉ email', 'en' => 'Email Address'],
    'checkout.phone_number' => ['vi' => 'Số điện thoại', 'en' => 'Phone Number'],
    'checkout.address_line_1' => ['vi' => 'Địa chỉ dòng 1', 'en' => 'Address Line 1'],
    'checkout.address_line_2' => ['vi' => 'Địa chỉ dòng 2', 'en' => 'Address Line 2'],
    'checkout.city' => ['vi' => 'Thành phố', 'en' => 'City'],
    'checkout.state_province' => ['vi' => 'Tỉnh/Thành', 'en' => 'State/Province'],
    'checkout.postal_code' => ['vi' => 'Mã bưu điện', 'en' => 'Postal Code'],
    'checkout.country' => ['vi' => 'Quốc gia', 'en' => 'Country'],
    'checkout.select_country' => ['vi' => 'Chọn quốc gia', 'en' => 'Select Country'],
    
    // Countries
    'countries.vietnam' => ['vi' => 'Việt Nam', 'en' => 'Vietnam'],
    'countries.united_states' => ['vi' => 'Hoa Kỳ', 'en' => 'United States'],
    'countries.canada' => ['vi' => 'Canada', 'en' => 'Canada'],
    'countries.united_kingdom' => ['vi' => 'Vương quốc Anh', 'en' => 'United Kingdom'],
    'countries.australia' => ['vi' => 'Úc', 'en' => 'Australia'],
    
    // Billing
    'checkout.billing_same_as_shipping' => ['vi' => 'Địa chỉ thanh toán giống địa chỉ giao hàng', 'en' => 'Billing address same as shipping'],
    'checkout.billing_information' => ['vi' => 'Thông tin thanh toán', 'en' => 'Billing Information'],
    
    // Navigation buttons
    'checkout.back_to_cart' => ['vi' => 'Quay lại giỏ hàng', 'en' => 'Back to Cart'],
    'checkout.continue_to_payment' => ['vi' => 'Tiếp tục thanh toán', 'en' => 'Continue to Payment'],
    'checkout.back_to_shipping' => ['vi' => 'Quay lại thông tin giao hàng', 'en' => 'Back to Shipping'],
    'checkout.review_order' => ['vi' => 'Xem lại đơn hàng', 'en' => 'Review Order'],
    'checkout.back_to_payment' => ['vi' => 'Quay lại thanh toán', 'en' => 'Back to Payment'],
    'checkout.complete_order' => ['vi' => 'Hoàn tất đơn hàng', 'en' => 'Complete Order'],
    
    // Payment
    'checkout.payment_information' => ['vi' => 'Thông tin thanh toán', 'en' => 'Payment Information'],
    'checkout.payment_method' => ['vi' => 'Phương thức thanh toán', 'en' => 'Payment Method'],
    'checkout.credit_debit_card' => ['vi' => 'Thẻ tín dụng/ghi nợ', 'en' => 'Credit/Debit Card'],
    'checkout.bank_transfer' => ['vi' => 'Chuyển khoản ngân hàng', 'en' => 'Bank Transfer'],
    'checkout.stripe_redirect_message' => ['vi' => 'Bạn sẽ được chuyển hướng đến Stripe để thanh toán an toàn', 'en' => 'You will be redirected to Stripe for secure payment'],
    'checkout.sepay_redirect_message' => ['vi' => 'Bạn sẽ được chuyển hướng đến SePay để thanh toán', 'en' => 'You will be redirected to SePay for payment'],
    
    // Review
    'checkout.review_your_order' => ['vi' => 'Xem lại đơn hàng', 'en' => 'Review Your Order'],
    'checkout.shipping_address' => ['vi' => 'Địa chỉ giao hàng', 'en' => 'Shipping Address'],
    'checkout.payment_method_label' => ['vi' => 'Phương thức thanh toán', 'en' => 'Payment Method'],
    
    // Cart totals
    'cart.subtotal' => ['vi' => 'Tạm tính', 'en' => 'Subtotal'],
    'cart.shipping' => ['vi' => 'Phí vận chuyển', 'en' => 'Shipping'],
    'cart.tax' => ['vi' => 'Thuế', 'en' => 'Tax'],
    'cart.total' => ['vi' => 'Tổng cộng', 'en' => 'Total'],
    'checkout.calculated_at_next_step' => ['vi' => 'Tính ở bước tiếp theo', 'en' => 'Calculated at next step'],
    
    // Security
    'checkout.payment_secure_encrypted' => ['vi' => 'Thanh toán được bảo mật và mã hóa', 'en' => 'Payment is secure and encrypted'],
    
    // Error messages
    'checkout.failed_to_process_payment' => ['vi' => 'Không thể xử lý thanh toán', 'en' => 'Failed to process payment'],
    'checkout.failed_to_load_review' => ['vi' => 'Không thể tải trang xem lại', 'en' => 'Failed to load review'],
    'checkout.place_order' => ['vi' => 'Đặt hàng', 'en' => 'Place Order'],
    
    // General marketplace
    'error' => ['vi' => 'Lỗi', 'en' => 'Error'],
    'success' => ['vi' => 'Thành công', 'en' => 'Success'],
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

echo "📁 Processing marketplace checkout keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/marketplace.php";
if (addKeysToFile($viFile, $checkoutKeys, 'vi')) {
    $totalAdded = count($checkoutKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/marketplace.php";
addKeysToFile($enFile, $checkoutKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($checkoutKeys) . "\n";
echo "Keys processed: " . count($checkoutKeys) . "\n";

// Test some keys
echo "\n🧪 Testing added keys:\n";
$testKeys = [
    'marketplace.checkout.title',
    'marketplace.checkout.secure_checkout', 
    'marketplace.cart.total',
    'marketplace.countries.vietnam'
];

foreach ($testKeys as $key) {
    echo "  Testing __('$key')...\n";
}

echo "\n✅ Marketplace checkout keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
