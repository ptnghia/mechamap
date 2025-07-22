<?php

/**
 * ADD AUTH SPECIFIC KEYS
 * Thêm các keys auth.* cụ thể thiếu từ login.blade.php
 */

echo "=== ADDING AUTH SPECIFIC KEYS ===\n\n";

// Auth specific keys found in login.blade.php
$authSpecificKeys = [
    // Knowledge and community
    'knowledge_hub' => ['vi' => 'Trung tâm kiến thức', 'en' => 'Knowledge Hub'],
    'connect_engineers' => ['vi' => 'Kết nối kỹ sư', 'en' => 'Connect Engineers'],
    'join_discussions' => ['vi' => 'Tham gia thảo luận', 'en' => 'Join Discussions'],
    'share_experience' => ['vi' => 'Chia sẻ kinh nghiệm', 'en' => 'Share Experience'],
    'marketplace_products' => ['vi' => 'Sản phẩm Marketplace', 'en' => 'Marketplace Products'],
    
    // Trust badges
    'trusted_by' => ['vi' => 'Được tin tưởng bởi', 'en' => 'Trusted by'],
    'members_badge' => ['vi' => 'thành viên', 'en' => 'members'],
    'individual_partners_badge' => ['vi' => 'đối tác cá nhân', 'en' => 'individual partners'],
    'business_badge' => ['vi' => 'doanh nghiệp', 'en' => 'businesses'],
    
    // Login form
    'welcome_back' => ['vi' => 'Chào mừng trở lại', 'en' => 'Welcome Back'],
    'login_journey_description' => ['vi' => 'Tiếp tục hành trình khám phá công nghệ của bạn', 'en' => 'Continue your technology exploration journey'],
    'email_or_username_label' => ['vi' => 'Email hoặc tên người dùng', 'en' => 'Email or Username'],
    'password_label' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'remember_login' => ['vi' => 'Ghi nhớ đăng nhập', 'en' => 'Remember login'],
    'forgot_password_link' => ['vi' => 'Quên mật khẩu?', 'en' => 'Forgot password?'],
    'login_button' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
    
    // Social login
    'or_login_with' => ['vi' => 'Hoặc đăng nhập với', 'en' => 'Or login with'],
    'login_with_google' => ['vi' => 'Đăng nhập với Google', 'en' => 'Login with Google'],
    'login_with_facebook' => ['vi' => 'Đăng nhập với Facebook', 'en' => 'Login with Facebook'],
    
    // Registration
    'no_account' => ['vi' => 'Chưa có tài khoản?', 'en' => 'No account?'],
    'register_now' => ['vi' => 'Đăng ký ngay', 'en' => 'Register now'],
    
    // Security
    'ssl_security' => ['vi' => 'Bảo mật SSL', 'en' => 'SSL Security'],
    
    // Community features
    'join_community_title' => ['vi' => 'Tham gia cộng đồng', 'en' => 'Join Community'],
    'join_community_description' => ['vi' => 'Kết nối với hàng nghìn kỹ sư và chuyên gia', 'en' => 'Connect with thousands of engineers and experts'],
    
    // Trending and features
    'trending_topics' => ['vi' => 'Chủ đề xu hướng', 'en' => 'Trending Topics'],
    'trending_topics_desc' => ['vi' => 'Theo dõi các chủ đề công nghệ hot nhất', 'en' => 'Follow the hottest technology topics'],
    'expert_network' => ['vi' => 'Mạng lưới chuyên gia', 'en' => 'Expert Network'],
    'expert_network_desc' => ['vi' => 'Kết nối với các chuyên gia hàng đầu', 'en' => 'Connect with leading experts'],
    'knowledge_base' => ['vi' => 'Cơ sở kiến thức', 'en' => 'Knowledge Base'],
    'knowledge_base_desc' => ['vi' => 'Truy cập kho tài liệu phong phú', 'en' => 'Access rich document repository'],
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

echo "📁 Processing auth specific keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authSpecificKeys, 'vi')) {
    $totalAdded = count($authSpecificKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authSpecificKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($authSpecificKeys) . "\n";
echo "Keys processed: " . count($authSpecificKeys) . "\n";

echo "\n✅ Auth specific keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
