<?php

/**
 * ADD AUTH LOGIN KEYS
 * Thêm keys thiếu cho auth/login.blade.php
 */

echo "=== ADDING AUTH LOGIN KEYS ===\n\n";

// Extract keys from auth/login.blade.php
$loginFile = __DIR__ . '/resources/views/auth/login.blade.php';

if (!file_exists($loginFile)) {
    echo "❌ File not found: $loginFile\n";
    exit(1);
}

$content = file_get_contents($loginFile);

// Extract all translation keys
preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
preg_match_all('/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $helperMatches);

$allKeys = [];

// Process direct __ calls
foreach ($directMatches[1] as $key) {
    $allKeys[] = $key;
}

// Process t_helper calls
foreach ($helperMatches[1] as $i => $helper) {
    $key = $helperMatches[1][$i] . '.' . $helperMatches[2][$i];
    $allKeys[] = $key;
}

$allKeys = array_unique($allKeys);

echo "Found " . count($allKeys) . " unique keys in auth login file:\n";
foreach ($allKeys as $key) {
    echo "  - $key\n";
}

// Define translations for auth login keys
$authLoginKeys = [
    // Login page
    'login.title' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
    'login.welcome_back' => ['vi' => 'Chào mừng trở lại', 'en' => 'Welcome Back'],
    'login.sign_in_to_continue' => ['vi' => 'Đăng nhập để tiếp tục', 'en' => 'Sign in to continue'],
    'login.email_or_username' => ['vi' => 'Email hoặc tên người dùng', 'en' => 'Email or Username'],
    'login.email_placeholder' => ['vi' => 'Nhập email hoặc tên người dùng', 'en' => 'Enter email or username'],
    'login.password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'login.password_placeholder' => ['vi' => 'Nhập mật khẩu', 'en' => 'Enter password'],
    'login.remember_me' => ['vi' => 'Ghi nhớ đăng nhập', 'en' => 'Remember me'],
    'login.forgot_password' => ['vi' => 'Quên mật khẩu?', 'en' => 'Forgot password?'],
    'login.sign_in' => ['vi' => 'Đăng nhập', 'en' => 'Sign In'],
    'login.signing_in' => ['vi' => 'Đang đăng nhập...', 'en' => 'Signing in...'],
    
    // Social login
    'login.or_continue_with' => ['vi' => 'Hoặc tiếp tục với', 'en' => 'Or continue with'],
    'login.google' => ['vi' => 'Google', 'en' => 'Google'],
    'login.facebook' => ['vi' => 'Facebook', 'en' => 'Facebook'],
    'login.github' => ['vi' => 'GitHub', 'en' => 'GitHub'],
    'login.linkedin' => ['vi' => 'LinkedIn', 'en' => 'LinkedIn'],
    
    // Registration link
    'login.dont_have_account' => ['vi' => 'Chưa có tài khoản?', 'en' => "Don't have an account?"],
    'login.create_account' => ['vi' => 'Tạo tài khoản', 'en' => 'Create account'],
    'login.sign_up' => ['vi' => 'Đăng ký', 'en' => 'Sign up'],
    'login.register_now' => ['vi' => 'Đăng ký ngay', 'en' => 'Register now'],
    
    // Error messages
    'login.invalid_credentials' => ['vi' => 'Thông tin đăng nhập không chính xác', 'en' => 'Invalid credentials'],
    'login.account_disabled' => ['vi' => 'Tài khoản đã bị vô hiệu hóa', 'en' => 'Account has been disabled'],
    'login.too_many_attempts' => ['vi' => 'Quá nhiều lần thử. Vui lòng thử lại sau.', 'en' => 'Too many attempts. Please try again later.'],
    'login.email_not_verified' => ['vi' => 'Email chưa được xác thực', 'en' => 'Email not verified'],
    
    // Success messages
    'login.welcome_message' => ['vi' => 'Chào mừng bạn đến với MechaMap!', 'en' => 'Welcome to MechaMap!'],
    'login.login_successful' => ['vi' => 'Đăng nhập thành công', 'en' => 'Login successful'],
    
    // Form validation
    'login.email_required' => ['vi' => 'Email là bắt buộc', 'en' => 'Email is required'],
    'login.password_required' => ['vi' => 'Mật khẩu là bắt buộc', 'en' => 'Password is required'],
    'login.email_invalid' => ['vi' => 'Email không hợp lệ', 'en' => 'Invalid email format'],
    'login.password_min_length' => ['vi' => 'Mật khẩu phải có ít nhất 8 ký tự', 'en' => 'Password must be at least 8 characters'],
    
    // Additional features
    'login.stay_logged_in' => ['vi' => 'Duy trì đăng nhập', 'en' => 'Stay logged in'],
    'login.secure_login' => ['vi' => 'Đăng nhập an toàn', 'en' => 'Secure login'],
    'login.privacy_policy' => ['vi' => 'Chính sách bảo mật', 'en' => 'Privacy Policy'],
    'login.terms_of_service' => ['vi' => 'Điều khoản dịch vụ', 'en' => 'Terms of Service'],
    'login.help' => ['vi' => 'Trợ giúp', 'en' => 'Help'],
    'login.contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
    
    // Mobile specific
    'login.mobile_app' => ['vi' => 'Ứng dụng di động', 'en' => 'Mobile App'],
    'login.download_app' => ['vi' => 'Tải ứng dụng', 'en' => 'Download App'],
    
    // Security
    'login.two_factor_auth' => ['vi' => 'Xác thực hai yếu tố', 'en' => 'Two-Factor Authentication'],
    'login.enter_2fa_code' => ['vi' => 'Nhập mã xác thực', 'en' => 'Enter verification code'],
    'login.resend_code' => ['vi' => 'Gửi lại mã', 'en' => 'Resend code'],
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

echo "\n📁 Processing auth login keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authLoginKeys, 'vi')) {
    $totalAdded = count($authLoginKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authLoginKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($authLoginKeys) . "\n";
echo "Keys processed: " . count($authLoginKeys) . "\n";

echo "\n✅ Auth login keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
