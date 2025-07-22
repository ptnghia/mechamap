<?php

/**
 * ADD AUTH WIZARD KEYS
 * Thêm keys thiếu cho auth/wizard/step1.blade.php và step2.blade.php
 */

echo "=== ADDING AUTH WIZARD KEYS ===\n\n";

// Extract keys from both wizard files
$wizardFiles = [
    'resources/views/auth/wizard/step1.blade.php',
    'resources/views/auth/wizard/step2.blade.php'
];

$allKeys = [];

foreach ($wizardFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (!file_exists($fullPath)) {
        echo "❌ File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    
    // Extract all translation keys
    preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $directMatches);
    preg_match_all('/\{\{\s*t_(\w+)\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $helperMatches);
    
    // Process direct __ calls
    foreach ($directMatches[1] as $key) {
        $allKeys[] = $key;
    }
    
    // Process t_helper calls
    foreach ($helperMatches[1] as $i => $helper) {
        $key = $helperMatches[1][$i] . '.' . $helperMatches[2][$i];
        $allKeys[] = $key;
    }
    
    echo "📄 Found " . (count($directMatches[1]) + count($helperMatches[1])) . " keys in $file\n";
}

$allKeys = array_unique($allKeys);
echo "\n🔑 Total unique keys found: " . count($allKeys) . "\n\n";

// Define translations for auth wizard keys
$authWizardKeys = [
    // Registration wizard
    'register.wizard.step1_title' => ['vi' => 'Bước 1: Thông tin cơ bản', 'en' => 'Step 1: Basic Information'],
    'register.wizard.step2_title' => ['vi' => 'Bước 2: Thông tin bổ sung', 'en' => 'Step 2: Additional Information'],
    'register.wizard.step3_title' => ['vi' => 'Bước 3: Hoàn thành', 'en' => 'Step 3: Complete'],
    
    // Step 1 - Basic Information
    'register.wizard.personal_info' => ['vi' => 'Thông tin cá nhân', 'en' => 'Personal Information'],
    'register.wizard.first_name' => ['vi' => 'Tên', 'en' => 'First Name'],
    'register.wizard.last_name' => ['vi' => 'Họ', 'en' => 'Last Name'],
    'register.wizard.email' => ['vi' => 'Email', 'en' => 'Email'],
    'register.wizard.password' => ['vi' => 'Mật khẩu', 'en' => 'Password'],
    'register.wizard.confirm_password' => ['vi' => 'Xác nhận mật khẩu', 'en' => 'Confirm Password'],
    'register.wizard.phone' => ['vi' => 'Số điện thoại', 'en' => 'Phone Number'],
    'register.wizard.phone_placeholder' => ['vi' => 'Nhập số điện thoại', 'en' => 'Enter phone number'],
    
    // Account type
    'register.wizard.account_type' => ['vi' => 'Loại tài khoản', 'en' => 'Account Type'],
    'register.wizard.select_account_type' => ['vi' => 'Chọn loại tài khoản phù hợp với bạn', 'en' => 'Select the account type that suits you'],
    'register.wizard.individual' => ['vi' => 'Cá nhân', 'en' => 'Individual'],
    'register.wizard.individual_desc' => ['vi' => 'Dành cho người dùng cá nhân', 'en' => 'For individual users'],
    'register.wizard.business' => ['vi' => 'Doanh nghiệp', 'en' => 'Business'],
    'register.wizard.business_desc' => ['vi' => 'Dành cho doanh nghiệp và tổ chức', 'en' => 'For businesses and organizations'],
    'register.wizard.student' => ['vi' => 'Sinh viên', 'en' => 'Student'],
    'register.wizard.student_desc' => ['vi' => 'Dành cho sinh viên và học sinh', 'en' => 'For students and learners'],
    
    // Step 2 - Additional Information
    'register.wizard.additional_info' => ['vi' => 'Thông tin bổ sung', 'en' => 'Additional Information'],
    'register.wizard.company_name' => ['vi' => 'Tên công ty', 'en' => 'Company Name'],
    'register.wizard.company_name_placeholder' => ['vi' => 'Nhập tên công ty', 'en' => 'Enter company name'],
    'register.wizard.job_title' => ['vi' => 'Chức vụ', 'en' => 'Job Title'],
    'register.wizard.job_title_placeholder' => ['vi' => 'Nhập chức vụ', 'en' => 'Enter job title'],
    'register.wizard.industry' => ['vi' => 'Ngành nghề', 'en' => 'Industry'],
    'register.wizard.select_industry' => ['vi' => 'Chọn ngành nghề', 'en' => 'Select industry'],
    'register.wizard.company_size' => ['vi' => 'Quy mô công ty', 'en' => 'Company Size'],
    'register.wizard.select_company_size' => ['vi' => 'Chọn quy mô công ty', 'en' => 'Select company size'],
    
    // Industries
    'register.wizard.industries.manufacturing' => ['vi' => 'Sản xuất', 'en' => 'Manufacturing'],
    'register.wizard.industries.construction' => ['vi' => 'Xây dựng', 'en' => 'Construction'],
    'register.wizard.industries.automotive' => ['vi' => 'Ô tô', 'en' => 'Automotive'],
    'register.wizard.industries.electronics' => ['vi' => 'Điện tử', 'en' => 'Electronics'],
    'register.wizard.industries.aerospace' => ['vi' => 'Hàng không vũ trụ', 'en' => 'Aerospace'],
    'register.wizard.industries.energy' => ['vi' => 'Năng lượng', 'en' => 'Energy'],
    'register.wizard.industries.healthcare' => ['vi' => 'Y tế', 'en' => 'Healthcare'],
    'register.wizard.industries.education' => ['vi' => 'Giáo dục', 'en' => 'Education'],
    'register.wizard.industries.other' => ['vi' => 'Khác', 'en' => 'Other'],
    
    // Company sizes
    'register.wizard.company_sizes.1_10' => ['vi' => '1-10 nhân viên', 'en' => '1-10 employees'],
    'register.wizard.company_sizes.11_50' => ['vi' => '11-50 nhân viên', 'en' => '11-50 employees'],
    'register.wizard.company_sizes.51_200' => ['vi' => '51-200 nhân viên', 'en' => '51-200 employees'],
    'register.wizard.company_sizes.201_500' => ['vi' => '201-500 nhân viên', 'en' => '201-500 employees'],
    'register.wizard.company_sizes.500_plus' => ['vi' => '500+ nhân viên', 'en' => '500+ employees'],
    
    // Student information
    'register.wizard.school_name' => ['vi' => 'Tên trường', 'en' => 'School Name'],
    'register.wizard.school_name_placeholder' => ['vi' => 'Nhập tên trường', 'en' => 'Enter school name'],
    'register.wizard.major' => ['vi' => 'Chuyên ngành', 'en' => 'Major'],
    'register.wizard.major_placeholder' => ['vi' => 'Nhập chuyên ngành', 'en' => 'Enter major'],
    'register.wizard.graduation_year' => ['vi' => 'Năm tốt nghiệp', 'en' => 'Graduation Year'],
    'register.wizard.select_graduation_year' => ['vi' => 'Chọn năm tốt nghiệp', 'en' => 'Select graduation year'],
    
    // Interests and preferences
    'register.wizard.interests' => ['vi' => 'Sở thích', 'en' => 'Interests'],
    'register.wizard.select_interests' => ['vi' => 'Chọn các lĩnh vực bạn quan tâm', 'en' => 'Select areas of interest'],
    'register.wizard.interests.mechanical_engineering' => ['vi' => 'Kỹ thuật cơ khí', 'en' => 'Mechanical Engineering'],
    'register.wizard.interests.electrical_engineering' => ['vi' => 'Kỹ thuật điện', 'en' => 'Electrical Engineering'],
    'register.wizard.interests.civil_engineering' => ['vi' => 'Kỹ thuật xây dựng', 'en' => 'Civil Engineering'],
    'register.wizard.interests.software_engineering' => ['vi' => 'Kỹ thuật phần mềm', 'en' => 'Software Engineering'],
    'register.wizard.interests.industrial_design' => ['vi' => 'Thiết kế công nghiệp', 'en' => 'Industrial Design'],
    'register.wizard.interests.manufacturing' => ['vi' => 'Sản xuất', 'en' => 'Manufacturing'],
    
    // Marketing preferences
    'register.wizard.marketing_preferences' => ['vi' => 'Tùy chọn tiếp thị', 'en' => 'Marketing Preferences'],
    'register.wizard.newsletter_subscription' => ['vi' => 'Đăng ký nhận bản tin', 'en' => 'Newsletter Subscription'],
    'register.wizard.newsletter_desc' => ['vi' => 'Nhận thông tin cập nhật và tin tức mới nhất', 'en' => 'Receive updates and latest news'],
    'register.wizard.promotional_emails' => ['vi' => 'Email khuyến mãi', 'en' => 'Promotional Emails'],
    'register.wizard.promotional_emails_desc' => ['vi' => 'Nhận thông tin về ưu đãi và khuyến mãi', 'en' => 'Receive information about offers and promotions'],
    
    // Navigation
    'register.wizard.previous' => ['vi' => 'Trước', 'en' => 'Previous'],
    'register.wizard.next' => ['vi' => 'Tiếp theo', 'en' => 'Next'],
    'register.wizard.complete_registration' => ['vi' => 'Hoàn thành đăng ký', 'en' => 'Complete Registration'],
    'register.wizard.skip_step' => ['vi' => 'Bỏ qua bước này', 'en' => 'Skip this step'],
    
    // Progress
    'register.wizard.step_progress' => ['vi' => 'Bước :current của :total', 'en' => 'Step :current of :total'],
    'register.wizard.almost_done' => ['vi' => 'Sắp hoàn thành!', 'en' => 'Almost done!'],
    
    // Validation messages
    'register.wizard.required_field' => ['vi' => 'Trường này là bắt buộc', 'en' => 'This field is required'],
    'register.wizard.invalid_email' => ['vi' => 'Email không hợp lệ', 'en' => 'Invalid email address'],
    'register.wizard.password_mismatch' => ['vi' => 'Mật khẩu không khớp', 'en' => 'Passwords do not match'],
    'register.wizard.phone_invalid' => ['vi' => 'Số điện thoại không hợp lệ', 'en' => 'Invalid phone number'],
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

echo "📁 Processing auth wizard keys for auth.php files\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authWizardKeys, 'vi')) {
    $totalAdded = count($authWizardKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authWizardKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($authWizardKeys) . "\n";
echo "Keys processed: " . count($authWizardKeys) . "\n";

echo "\n✅ Auth wizard keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
