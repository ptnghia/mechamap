<?php

/**
 * ADD AUTH WIZARD STEP1 KEYS
 * Thêm tất cả keys thiếu cho auth/wizard/step1.blade.php
 */

echo "=== ADDING AUTH WIZARD STEP1 KEYS ===\n\n";

// All auth wizard step1 keys
$authStep1Keys = [
    // Main titles
    'register.step1_title' => ['vi' => 'Bước 1: Thông tin cá nhân', 'en' => 'Step 1: Personal Information'],
    'register.step1_subtitle' => ['vi' => 'Tạo tài khoản và chọn loại thành viên', 'en' => 'Create account and choose membership type'],
    'register.personal_info_description' => ['vi' => 'Nhập thông tin cá nhân để tạo tài khoản của bạn', 'en' => 'Enter your personal information to create your account'],
    
    // Form fields
    'full_name_label' => ['vi' => 'Họ và tên', 'en' => 'Full Name'],
    'full_name_placeholder' => ['vi' => 'Nhập họ và tên đầy đủ', 'en' => 'Enter your full name'],
    'username_label' => ['vi' => 'Tên người dùng', 'en' => 'Username'],
    'username_placeholder' => ['vi' => 'Chọn tên người dùng', 'en' => 'Choose a username'],
    'username_help' => ['vi' => 'Tên người dùng chỉ chứa chữ cái, số và dấu gạch dưới', 'en' => 'Username can only contain letters, numbers and underscores'],
    'email_label' => ['vi' => 'Địa chỉ email', 'en' => 'Email Address'],
    'email_placeholder' => ['vi' => 'Nhập địa chỉ email', 'en' => 'Enter your email address'],
    'password_label' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'password_placeholder' => ['vi' => 'Tạo mật khẩu mạnh', 'en' => 'Create a strong password'],
    'password_help' => ['vi' => 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số', 'en' => 'Password must be at least 8 characters with uppercase, lowercase and numbers'],
    'confirm_password_label' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
    'confirm_password_placeholder' => ['vi' => 'Nhập lại mật khẩu', 'en' => 'Re-enter your password'],
    
    // Validation messages
    'register.name_valid' => ['vi' => 'Tên hợp lệ', 'en' => 'Name is valid'],
    'register.username_available' => ['vi' => 'Tên người dùng có sẵn', 'en' => 'Username is available'],
    'register.email_valid' => ['vi' => 'Email hợp lệ', 'en' => 'Email is valid'],
    'register.email_help' => ['vi' => 'Chúng tôi sẽ gửi email xác thực đến địa chỉ này', 'en' => 'We will send a verification email to this address'],
    
    // Account type section
    'register.account_type_title' => ['vi' => 'Chọn loại tài khoản', 'en' => 'Choose Account Type'],
    'register.account_type_description' => ['vi' => 'Chọn loại tài khoản phù hợp với mục đích sử dụng của bạn', 'en' => 'Select the account type that best fits your intended use'],
    
    // Community member section
    'register.community_member_title' => ['vi' => 'Thành viên cộng đồng', 'en' => 'Community Member'],
    'register.community_member_description' => ['vi' => 'Tham gia cộng đồng để học hỏi, chia sẻ và kết nối', 'en' => 'Join the community to learn, share and connect'],
    'register.member_role' => ['vi' => 'Thành viên', 'en' => 'Member'],
    'register.recommended' => ['vi' => 'Đề xuất', 'en' => 'Recommended'],
    'register.member_role_desc' => ['vi' => 'Truy cập đầy đủ diễn đàn, tạo bài viết và tham gia thảo luận', 'en' => 'Full forum access, create posts and participate in discussions'],
    'register.guest_role' => ['vi' => 'Khách', 'en' => 'Guest'],
    'register.guest_role_desc' => ['vi' => 'Chỉ xem nội dung, không thể tạo bài viết hoặc bình luận', 'en' => 'View-only access, cannot create posts or comments'],
    'register.note_community' => ['vi' => 'Bạn có thể nâng cấp tài khoản sau khi đăng ký.', 'en' => 'You can upgrade your account after registration.'],
    
    // Business partner section
    'register.business_partner_description' => ['vi' => 'Dành cho doanh nghiệp muốn bán sản phẩm hoặc dịch vụ', 'en' => 'For businesses looking to sell products or services'],
    'register.manufacturer_role_desc' => ['vi' => 'Sản xuất và bán sản phẩm cơ khí, thiết bị công nghiệp', 'en' => 'Manufacture and sell mechanical products, industrial equipment'],
    'register.supplier_role_desc' => ['vi' => 'Cung cấp linh kiện, vật liệu và dịch vụ hỗ trợ', 'en' => 'Supply components, materials and support services'],
    'register.brand_role_desc' => ['vi' => 'Quảng bá thương hiệu và sản phẩm trên marketplace', 'en' => 'Promote brand and products on marketplace'],
    'register.note_business' => ['vi' => 'Tài khoản doanh nghiệp cần xác thực trước khi sử dụng đầy đủ tính năng.', 'en' => 'Business accounts require verification before full feature access.'],
    
    // Terms
    'register.terms_agreement' => ['vi' => 'Tôi đồng ý với <a href="/terms" target="_blank">Điều khoản sử dụng</a> và <a href="/privacy" target="_blank">Chính sách bảo mật</a>', 'en' => 'I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>'],
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

echo "📁 Processing auth wizard step1 keys for auth.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authStep1Keys, 'vi')) {
    $totalAdded = count($authStep1Keys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authStep1Keys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total auth wizard step1 keys added: " . count($authStep1Keys) . "\n";

echo "\n✅ Auth wizard step1 keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
