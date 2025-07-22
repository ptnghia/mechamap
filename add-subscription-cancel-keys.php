<?php

/**
 * ADD SUBSCRIPTION CANCEL KEYS
 * Thêm tất cả keys thiếu cho subscription/cancel.blade.php
 */

echo "=== ADDING SUBSCRIPTION CANCEL KEYS ===\n\n";

// All subscription cancel keys organized by category
$subscriptionCancelKeys = [
    // Subscription cancel keys
    'sorry_to_see_you_go' => ['vi' => 'Chúng tôi rất tiếc khi bạn rời đi', 'en' => 'We\'re Sorry to See You Go'],
    'confirm_cancel_question' => ['vi' => 'Bạn có chắc chắn muốn hủy đăng ký của mình không?', 'en' => 'Are you sure you want to cancel your subscription?'],
    'what_happens_when_cancel' => ['vi' => 'Điều gì xảy ra khi bạn hủy', 'en' => 'What Happens When You Cancel'],
    'continue_access_until_end' => ['vi' => 'Bạn sẽ tiếp tục có quyền truy cập vào các tính năng cao cấp cho đến khi kết thúc chu kỳ thanh toán hiện tại.', 'en' => 'You will continue to have access to premium features until the end of your current billing period.'],
    'no_auto_renewal' => ['vi' => 'Đăng ký của bạn sẽ không tự động gia hạn.', 'en' => 'Your subscription will not renew automatically.'],
    'can_resubscribe_anytime' => ['vi' => 'Bạn có thể đăng ký lại bất cứ lúc nào.', 'en' => 'You can resubscribe at any time.'],
    'current_subscription_details' => ['vi' => 'Chi tiết đăng ký hiện tại', 'en' => 'Current Subscription Details'],
    'plan' => ['vi' => 'Gói', 'en' => 'Plan'],
    'status' => ['vi' => 'Trạng thái', 'en' => 'Status'],
    'billing_period_ends' => ['vi' => 'Chu kỳ thanh toán kết thúc', 'en' => 'Billing Period Ends'],
    'price' => ['vi' => 'Giá', 'en' => 'Price'],
    'month' => ['vi' => 'tháng', 'en' => 'month'],
    'tell_us_why_cancelling' => ['vi' => 'Vui lòng cho chúng tôi biết lý do bạn hủy', 'en' => 'Please tell us why you\'re cancelling'],
    'too_expensive' => ['vi' => 'Quá đắt', 'en' => 'Too expensive'],
    'not_using_premium_features' => ['vi' => 'Không sử dụng các tính năng cao cấp', 'en' => 'Not using the premium features'],
    'missing_features_needed' => ['vi' => 'Thiếu các tính năng tôi cần', 'en' => 'Missing features I need'],
    'switching_to_another_service' => ['vi' => 'Chuyển sang dịch vụ khác', 'en' => 'Switching to another service'],
    'other_reason' => ['vi' => 'Lý do khác', 'en' => 'Other reason'],
    'additional_feedback_optional' => ['vi' => 'Phản hồi bổ sung (tùy chọn)', 'en' => 'Additional feedback (optional)'],
    'keep_my_subscription' => ['vi' => 'Giữ đăng ký của tôi', 'en' => 'Keep My Subscription'],
    'cancel_subscription' => ['vi' => 'Hủy đăng ký', 'en' => 'Cancel Subscription'],
    'confirm_cancellation' => ['vi' => 'Xác nhận hủy', 'en' => 'Confirm Cancellation'],
    'absolutely_sure_cancel' => ['vi' => 'Bạn có hoàn toàn chắc chắn muốn hủy đăng ký của mình không?', 'en' => 'Are you absolutely sure you want to cancel your subscription?'],
    'lose_access_premium_features' => ['vi' => 'Bạn sẽ mất quyền truy cập vào các tính năng cao cấp vào cuối chu kỳ thanh toán hiện tại.', 'en' => 'You will lose access to premium features at the end of your current billing period.'],
    'no_keep_subscription' => ['vi' => 'Không, giữ đăng ký của tôi', 'en' => 'No, Keep My Subscription'],
    'yes_cancel_subscription' => ['vi' => 'Có, hủy đăng ký của tôi', 'en' => 'Yes, Cancel My Subscription'],
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

echo "📁 Processing subscription cancel keys for subscription.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/subscription.php";
if (addKeysToFile($viFile, $subscriptionCancelKeys, 'vi')) {
    $totalAdded = count($subscriptionCancelKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/subscription.php";
addKeysToFile($enFile, $subscriptionCancelKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total subscription cancel keys added: " . count($subscriptionCancelKeys) . "\n";

echo "\n✅ Subscription cancel keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
