<?php
/**
 * Process Auth Translation Keys
 * Xử lý 138 Auth keys - high user experience impact
 */

echo "🔐 PROCESSING AUTH TRANSLATION KEYS\n";
echo "===================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/direct_keys_analysis.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

// Extract Auth keys
$authKeys = [];
$allDirectKeys = array_merge(
    $analysis['detailed_patterns']['dot_notation'] ?? [],
    $analysis['detailed_patterns']['slash_notation'] ?? [],
    $analysis['detailed_patterns']['simple_keys'] ?? [],
    $analysis['detailed_patterns']['mixed_notation'] ?? []
);

foreach ($allDirectKeys as $key => $files) {
    if (strpos($key, 'auth.') === 0 || strpos($key, 'auth/') === 0) {
        $authKeys[$key] = $files;
    }
}

echo "📊 AUTH KEYS ANALYSIS\n";
echo "=====================\n";
echo "Total auth keys found: " . count($authKeys) . "\n";

// Analyze auth key patterns
$authPatterns = [];
foreach ($authKeys as $key => $files) {
    // Extract pattern: auth.section.subsection.key
    if (preg_match('/^auth\.([^.]+)\.(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($authPatterns[$section])) {
            $authPatterns[$section] = [];
        }
        $authPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    } elseif (preg_match('/^auth\/([^\/]+)\/(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($authPatterns[$section])) {
            $authPatterns[$section] = [];
        }
        $authPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    } else {
        // Simple auth keys
        if (!isset($authPatterns['general'])) {
            $authPatterns['general'] = [];
        }
        $authPatterns['general'][$key] = [
            'full_key' => $key,
            'files' => $files
        ];
    }
}

echo "\n📋 AUTH SECTIONS FOUND\n";
echo "======================\n";
foreach ($authPatterns as $section => $keys) {
    echo "🔸 auth.$section: " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 5);
    foreach ($examples as $example) {
        if ($section === 'general') {
            echo "   - $example\n";
        } else {
            echo "   - auth.$section.$example\n";
        }
    }
    if (count($keys) > 5) {
        echo "   - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

// Define comprehensive auth translations
$authTranslations = [
    'login' => [
        'title' => ['en' => 'Login', 'vi' => 'Đăng nhập'],
        'email' => ['en' => 'Email', 'vi' => 'Email'],
        'username' => ['en' => 'Username', 'vi' => 'Tên đăng nhập'],
        'password' => ['en' => 'Password', 'vi' => 'Mật khẩu'],
        'remember_me' => ['en' => 'Remember Me', 'vi' => 'Ghi nhớ đăng nhập'],
        'forgot_password' => ['en' => 'Forgot Password?', 'vi' => 'Quên mật khẩu?'],
        'login_button' => ['en' => 'Login', 'vi' => 'Đăng nhập'],
        'sign_in' => ['en' => 'Sign In', 'vi' => 'Đăng nhập'],
        'welcome_back' => ['en' => 'Welcome Back', 'vi' => 'Chào mừng trở lại'],
        'please_login' => ['en' => 'Please login to continue', 'vi' => 'Vui lòng đăng nhập để tiếp tục'],
        'login_required' => ['en' => 'Login Required', 'vi' => 'Yêu cầu đăng nhập'],
        'login_to_access' => ['en' => 'Login to access this feature', 'vi' => 'Đăng nhập để truy cập tính năng này'],
        'login_to_view_notifications' => ['en' => 'Login to view notifications', 'vi' => 'Đăng nhập để xem thông báo'],
        'login_to_continue' => ['en' => 'Login to continue', 'vi' => 'Đăng nhập để tiếp tục'],
        'invalid_credentials' => ['en' => 'Invalid credentials', 'vi' => 'Thông tin đăng nhập không hợp lệ'],
        'login_failed' => ['en' => 'Login failed', 'vi' => 'Đăng nhập thất bại'],
        'login_successful' => ['en' => 'Login successful', 'vi' => 'Đăng nhập thành công'],
        'account_locked' => ['en' => 'Account locked', 'vi' => 'Tài khoản bị khóa'],
        'account_suspended' => ['en' => 'Account suspended', 'vi' => 'Tài khoản bị tạm ngưng'],
        'email_not_verified' => ['en' => 'Email not verified', 'vi' => 'Email chưa được xác thực'],
        'too_many_attempts' => ['en' => 'Too many login attempts', 'vi' => 'Quá nhiều lần đăng nhập'],
        'session_expired' => ['en' => 'Session expired', 'vi' => 'Phiên đăng nhập đã hết hạn'],
        'logout_successful' => ['en' => 'Logout successful', 'vi' => 'Đăng xuất thành công'],
        'already_logged_in' => ['en' => 'Already logged in', 'vi' => 'Đã đăng nhập'],
    ],
    
    'register' => [
        'title' => ['en' => 'Register', 'vi' => 'Đăng ký'],
        'create_account' => ['en' => 'Create Account', 'vi' => 'Tạo tài khoản'],
        'sign_up' => ['en' => 'Sign Up', 'vi' => 'Đăng ký'],
        'register_mechamap_account' => ['en' => 'Register MechaMap Account', 'vi' => 'Đăng ký tài khoản MechaMap'],
        'join_community' => ['en' => 'Join Our Community', 'vi' => 'Tham gia cộng đồng'],
        'first_name' => ['en' => 'First Name', 'vi' => 'Tên'],
        'last_name' => ['en' => 'Last Name', 'vi' => 'Họ'],
        'full_name' => ['en' => 'Full Name', 'vi' => 'Họ và tên'],
        'display_name' => ['en' => 'Display Name', 'vi' => 'Tên hiển thị'],
        'username' => ['en' => 'Username', 'vi' => 'Tên đăng nhập'],
        'email' => ['en' => 'Email Address', 'vi' => 'Địa chỉ email'],
        'password' => ['en' => 'Password', 'vi' => 'Mật khẩu'],
        'confirm_password' => ['en' => 'Confirm Password', 'vi' => 'Xác nhận mật khẩu'],
        'date_of_birth' => ['en' => 'Date of Birth', 'vi' => 'Ngày sinh'],
        'gender' => ['en' => 'Gender', 'vi' => 'Giới tính'],
        'country' => ['en' => 'Country', 'vi' => 'Quốc gia'],
        'city' => ['en' => 'City', 'vi' => 'Thành phố'],
        'phone' => ['en' => 'Phone Number', 'vi' => 'Số điện thoại'],
        'agree_terms' => ['en' => 'I agree to the Terms of Service', 'vi' => 'Tôi đồng ý với Điều khoản dịch vụ'],
        'agree_privacy' => ['en' => 'I agree to the Privacy Policy', 'vi' => 'Tôi đồng ý với Chính sách bảo mật'],
        'newsletter_subscribe' => ['en' => 'Subscribe to newsletter', 'vi' => 'Đăng ký nhận bản tin'],
        'register_button' => ['en' => 'Register', 'vi' => 'Đăng ký'],
        'create_account_button' => ['en' => 'Create Account', 'vi' => 'Tạo tài khoản'],
        'registration_successful' => ['en' => 'Registration successful', 'vi' => 'Đăng ký thành công'],
        'registration_failed' => ['en' => 'Registration failed', 'vi' => 'Đăng ký thất bại'],
        'email_already_exists' => ['en' => 'Email already exists', 'vi' => 'Email đã tồn tại'],
        'username_already_exists' => ['en' => 'Username already exists', 'vi' => 'Tên đăng nhập đã tồn tại'],
        'password_too_weak' => ['en' => 'Password too weak', 'vi' => 'Mật khẩu quá yếu'],
        'passwords_dont_match' => ['en' => 'Passwords don\'t match', 'vi' => 'Mật khẩu không khớp'],
        'invalid_email' => ['en' => 'Invalid email address', 'vi' => 'Địa chỉ email không hợp lệ'],
        'username_too_short' => ['en' => 'Username too short', 'vi' => 'Tên đăng nhập quá ngắn'],
        'username_invalid' => ['en' => 'Username contains invalid characters', 'vi' => 'Tên đăng nhập chứa ký tự không hợp lệ'],
        'terms_required' => ['en' => 'You must agree to the terms', 'vi' => 'Bạn phải đồng ý với điều khoản'],
        'verification_email_sent' => ['en' => 'Verification email sent', 'vi' => 'Email xác thực đã được gửi'],
        'please_verify_email' => ['en' => 'Please verify your email', 'vi' => 'Vui lòng xác thực email của bạn'],
        'already_have_account' => ['en' => 'Already have an account?', 'vi' => 'Đã có tài khoản?'],
        'login_here' => ['en' => 'Login here', 'vi' => 'Đăng nhập tại đây'],
    ],
    
    'password' => [
        'forgot_title' => ['en' => 'Forgot Password', 'vi' => 'Quên mật khẩu'],
        'reset_title' => ['en' => 'Reset Password', 'vi' => 'Đặt lại mật khẩu'],
        'change_title' => ['en' => 'Change Password', 'vi' => 'Đổi mật khẩu'],
        'forgot_description' => ['en' => 'Enter your email to reset password', 'vi' => 'Nhập email để đặt lại mật khẩu'],
        'reset_description' => ['en' => 'Enter your new password', 'vi' => 'Nhập mật khẩu mới'],
        'current_password' => ['en' => 'Current Password', 'vi' => 'Mật khẩu hiện tại'],
        'new_password' => ['en' => 'New Password', 'vi' => 'Mật khẩu mới'],
        'confirm_new_password' => ['en' => 'Confirm New Password', 'vi' => 'Xác nhận mật khẩu mới'],
        'send_reset_link' => ['en' => 'Send Reset Link', 'vi' => 'Gửi liên kết đặt lại'],
        'reset_password_button' => ['en' => 'Reset Password', 'vi' => 'Đặt lại mật khẩu'],
        'change_password_button' => ['en' => 'Change Password', 'vi' => 'Đổi mật khẩu'],
        'back_to_login' => ['en' => 'Back to Login', 'vi' => 'Quay lại đăng nhập'],
        'reset_link_sent' => ['en' => 'Reset link sent to your email', 'vi' => 'Liên kết đặt lại đã được gửi đến email'],
        'password_reset_successful' => ['en' => 'Password reset successful', 'vi' => 'Đặt lại mật khẩu thành công'],
        'password_changed_successful' => ['en' => 'Password changed successful', 'vi' => 'Đổi mật khẩu thành công'],
        'invalid_reset_token' => ['en' => 'Invalid reset token', 'vi' => 'Mã đặt lại không hợp lệ'],
        'reset_token_expired' => ['en' => 'Reset token expired', 'vi' => 'Mã đặt lại đã hết hạn'],
        'current_password_incorrect' => ['en' => 'Current password incorrect', 'vi' => 'Mật khẩu hiện tại không đúng'],
        'password_requirements' => ['en' => 'Password must be at least 8 characters', 'vi' => 'Mật khẩu phải có ít nhất 8 ký tự'],
        'password_strength_weak' => ['en' => 'Weak', 'vi' => 'Yếu'],
        'password_strength_medium' => ['en' => 'Medium', 'vi' => 'Trung bình'],
        'password_strength_strong' => ['en' => 'Strong', 'vi' => 'Mạnh'],
        'show_password' => ['en' => 'Show Password', 'vi' => 'Hiện mật khẩu'],
        'hide_password' => ['en' => 'Hide Password', 'vi' => 'Ẩn mật khẩu'],
    ],
    
    'verification' => [
        'title' => ['en' => 'Email Verification', 'vi' => 'Xác thực email'],
        'verify_email' => ['en' => 'Verify Email', 'vi' => 'Xác thực email'],
        'email_verified' => ['en' => 'Email Verified', 'vi' => 'Email đã được xác thực'],
        'verification_required' => ['en' => 'Email verification required', 'vi' => 'Yêu cầu xác thực email'],
        'check_email' => ['en' => 'Check your email for verification link', 'vi' => 'Kiểm tra email để lấy liên kết xác thực'],
        'resend_verification' => ['en' => 'Resend Verification Email', 'vi' => 'Gửi lại email xác thực'],
        'verification_sent' => ['en' => 'Verification email sent', 'vi' => 'Email xác thực đã được gửi'],
        'verification_successful' => ['en' => 'Email verification successful', 'vi' => 'Xác thực email thành công'],
        'verification_failed' => ['en' => 'Email verification failed', 'vi' => 'Xác thực email thất bại'],
        'invalid_verification_link' => ['en' => 'Invalid verification link', 'vi' => 'Liên kết xác thực không hợp lệ'],
        'verification_expired' => ['en' => 'Verification link expired', 'vi' => 'Liên kết xác thực đã hết hạn'],
        'already_verified' => ['en' => 'Email already verified', 'vi' => 'Email đã được xác thực'],
        'verify_to_continue' => ['en' => 'Please verify your email to continue', 'vi' => 'Vui lòng xác thực email để tiếp tục'],
    ],
    
    'profile' => [
        'title' => ['en' => 'Profile', 'vi' => 'Hồ sơ'],
        'my_profile' => ['en' => 'My Profile', 'vi' => 'Hồ sơ của tôi'],
        'edit_profile' => ['en' => 'Edit Profile', 'vi' => 'Chỉnh sửa hồ sơ'],
        'view_profile' => ['en' => 'View Profile', 'vi' => 'Xem hồ sơ'],
        'profile_updated' => ['en' => 'Profile updated successfully', 'vi' => 'Cập nhật hồ sơ thành công'],
        'profile_picture' => ['en' => 'Profile Picture', 'vi' => 'Ảnh đại diện'],
        'upload_picture' => ['en' => 'Upload Picture', 'vi' => 'Tải lên ảnh'],
        'remove_picture' => ['en' => 'Remove Picture', 'vi' => 'Gỡ bỏ ảnh'],
        'cover_photo' => ['en' => 'Cover Photo', 'vi' => 'Ảnh bìa'],
        'bio' => ['en' => 'Bio', 'vi' => 'Tiểu sử'],
        'about_me' => ['en' => 'About Me', 'vi' => 'Về tôi'],
        'location' => ['en' => 'Location', 'vi' => 'Vị trí'],
        'website' => ['en' => 'Website', 'vi' => 'Trang web'],
        'social_links' => ['en' => 'Social Links', 'vi' => 'Liên kết mạng xã hội'],
        'privacy_settings' => ['en' => 'Privacy Settings', 'vi' => 'Cài đặt riêng tư'],
        'notification_settings' => ['en' => 'Notification Settings', 'vi' => 'Cài đặt thông báo'],
        'account_settings' => ['en' => 'Account Settings', 'vi' => 'Cài đặt tài khoản'],
        'security_settings' => ['en' => 'Security Settings', 'vi' => 'Cài đặt bảo mật'],
        'delete_account' => ['en' => 'Delete Account', 'vi' => 'Xóa tài khoản'],
        'deactivate_account' => ['en' => 'Deactivate Account', 'vi' => 'Vô hiệu hóa tài khoản'],
    ],
    
    'logout' => [
        'title' => ['en' => 'Logout', 'vi' => 'Đăng xuất'],
        'logout_button' => ['en' => 'Logout', 'vi' => 'Đăng xuất'],
        'sign_out' => ['en' => 'Sign Out', 'vi' => 'Đăng xuất'],
        'logout_confirm' => ['en' => 'Are you sure you want to logout?', 'vi' => 'Bạn có chắc muốn đăng xuất?'],
        'logout_successful' => ['en' => 'You have been logged out', 'vi' => 'Bạn đã đăng xuất'],
        'goodbye' => ['en' => 'Goodbye!', 'vi' => 'Tạm biệt!'],
        'see_you_soon' => ['en' => 'See you soon!', 'vi' => 'Hẹn gặp lại!'],
        'logout_all_devices' => ['en' => 'Logout from all devices', 'vi' => 'Đăng xuất khỏi tất cả thiết bị'],
    ],
    
    'social' => [
        'login_with_google' => ['en' => 'Login with Google', 'vi' => 'Đăng nhập bằng Google'],
        'login_with_facebook' => ['en' => 'Login with Facebook', 'vi' => 'Đăng nhập bằng Facebook'],
        'login_with_twitter' => ['en' => 'Login with Twitter', 'vi' => 'Đăng nhập bằng Twitter'],
        'login_with_github' => ['en' => 'Login with GitHub', 'vi' => 'Đăng nhập bằng GitHub'],
        'or_login_with' => ['en' => 'Or login with', 'vi' => 'Hoặc đăng nhập bằng'],
        'or_register_with' => ['en' => 'Or register with', 'vi' => 'Hoặc đăng ký bằng'],
        'social_login_failed' => ['en' => 'Social login failed', 'vi' => 'Đăng nhập mạng xã hội thất bại'],
        'account_linked' => ['en' => 'Account linked successfully', 'vi' => 'Liên kết tài khoản thành công'],
        'account_unlinked' => ['en' => 'Account unlinked successfully', 'vi' => 'Hủy liên kết tài khoản thành công'],
        'link_account' => ['en' => 'Link Account', 'vi' => 'Liên kết tài khoản'],
        'unlink_account' => ['en' => 'Unlink Account', 'vi' => 'Hủy liên kết tài khoản'],
    ]
];

echo "\n🔧 CREATING AUTH TRANSLATION FILES...\n";
echo "=====================================\n";

$createdFiles = 0;
$totalTranslations = 0;

foreach ($authTranslations as $section => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/auth/$section.php";
        $dirPath = dirname($filePath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            echo "📁 Created directory: " . str_replace($basePath . '/', '', $dirPath) . "\n";
        }
        
        // Load existing translations
        $existingTranslations = [];
        if (file_exists($filePath)) {
            $existingTranslations = include $filePath;
            if (!is_array($existingTranslations)) {
                $existingTranslations = [];
            }
        }
        
        // Add new translations
        $newTranslations = [];
        foreach ($translations as $key => $localeTranslations) {
            $newTranslations[$key] = $localeTranslations[$locale];
        }
        
        // Merge with existing
        $mergedTranslations = array_merge($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * Auth $section translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Updated: $locale/auth/$section.php (" . count($newTranslations) . " translations)\n";
        $createdFiles++;
        $totalTranslations += count($newTranslations);
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Auth sections processed: " . count($authTranslations) . "\n";
echo "Translation files created/updated: $createdFiles\n";
echo "Total translations added: $totalTranslations\n";

// Clear caches
echo "\n🧹 CLEARING CACHES...\n";
echo "=====================\n";

$commands = [
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan config:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec("cd $basePath && $command 2>&1");
    echo "   " . trim($output) . "\n";
}

echo "\n🧪 TESTING AUTH KEYS...\n";
echo "=======================\n";

$testAuthKeys = [
    'auth.login.title',
    'auth.login.login_to_view_notifications',
    'auth.register.register_mechamap_account',
    'auth.register.title',
    'auth.password.forgot_title',
    'auth.verification.title',
    'auth.profile.title',
    'auth.logout.title',
    'auth.social.login_with_google'
];

$workingCount = 0;
foreach ($testAuthKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nAuth keys success rate: " . round(($workingCount / count($testAuthKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Created comprehensive auth translations\n";
echo "2. 🔄 Process content keys (54 keys) next\n";
echo "3. 🔄 Run comprehensive validation to see overall improvement\n";
echo "4. 🔄 Test auth functionality in browser\n";
echo "5. 🔄 Consider processing remaining categories\n\n";

echo "💡 IMPACT ASSESSMENT\n";
echo "====================\n";
echo "Auth keys have highest user experience impact.\n";
echo "Login, registration, and password functionality should now display properly.\n";
echo "User journey from registration to login should be fully translated.\n";
