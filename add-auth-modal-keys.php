<?php

/**
 * ADD AUTH MODAL KEYS
 * Thêm tất cả keys thiếu cho components/auth-modal.blade.php
 */

echo "=== ADDING AUTH MODAL KEYS ===\n\n";

// All auth modal keys organized by file
$authModalKeys = [
    // Navigation keys
    'navigation_keys' => [
        'auth.login' => ['vi' => 'Đăng nhập', 'en' => 'Login'],
    ],

    // Auth keys
    'auth_keys' => [
        'login.welcome_back' => ['vi' => 'Chào mừng bạn trở lại', 'en' => 'Welcome back'],
        'login.email_or_username' => ['vi' => 'Email hoặc tên người dùng', 'en' => 'Email or username'],
        'login.remember' => ['vi' => 'Ghi nhớ đăng nhập', 'en' => 'Remember me'],
        'login.or_login_with' => ['vi' => 'Hoặc đăng nhập bằng', 'en' => 'Or login with'],
        'login.login_with_google' => ['vi' => 'Đăng nhập với Google', 'en' => 'Login with Google'],
        'login.login_with_facebook' => ['vi' => 'Đăng nhập với Facebook', 'en' => 'Login with Facebook'],
        'login.dont_have_account' => ['vi' => 'Chưa có tài khoản?', 'en' => "Don't have an account?"],
        'register.create_business_account' => ['vi' => 'Tạo tài khoản doanh nghiệp', 'en' => 'Create Business Account'],
        'password.forgot_description' => ['vi' => 'Nhập email của bạn để nhận liên kết đặt lại mật khẩu', 'en' => 'Enter your email to receive a password reset link'],
        'password.send_reset_link' => ['vi' => 'Gửi liên kết đặt lại', 'en' => 'Send Reset Link'],
        'login.back_to_login' => ['vi' => 'Quay lại đăng nhập', 'en' => 'Back to Login'],
    ],

    // Common keys
    'common_keys' => [
        'forgot_password' => ['vi' => 'Quên mật khẩu', 'en' => 'Forgot Password'],
        'messages.forgot_password' => ['vi' => 'Quên mật khẩu', 'en' => 'Forgot Password'],
    ],
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

// Map categories to files
$categoryFileMap = [
    'navigation_keys' => 'navigation',
    'auth_keys' => 'auth',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($authModalKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";

    $file = $categoryFileMap[$category];

    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$file.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }

    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$file.php";
    addKeysToFile($enFile, $keys, 'en');

    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total auth modal keys added: $totalAdded\n";
echo "Categories processed: " . count($authModalKeys) . "\n";

echo "\n✅ Auth modal keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
