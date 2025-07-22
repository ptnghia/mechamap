<?php

/**
 * ADD AUTH WIZARD STEP2 KEYS
 * Thêm tất cả keys thiếu cho auth/wizard/step2.blade.php
 */

echo "=== ADDING AUTH WIZARD STEP2 KEYS ===\n\n";

// All auth wizard step2 keys
$authStep2Keys = [
    // Main titles
    'register.step2_title' => ['vi' => 'Bước 2: Thông tin doanh nghiệp', 'en' => 'Step 2: Business Information'],
    'register.wizard_title' => ['vi' => 'Đăng ký tài khoản doanh nghiệp', 'en' => 'Business Account Registration'],
    'register.step2_subtitle' => ['vi' => 'Hoàn tất thông tin doanh nghiệp của bạn', 'en' => 'Complete your business information'],
    'register.complete_button' => ['vi' => 'Hoàn tất đăng ký', 'en' => 'Complete Registration'],
    'register.back_button' => ['vi' => 'Quay lại', 'en' => 'Back'],
    
    // Account type labels
    'register.account_type_label' => ['vi' => 'Loại tài khoản', 'en' => 'Account Type'],
    'register.manufacturer_role' => ['vi' => 'Nhà sản xuất', 'en' => 'Manufacturer'],
    'register.supplier_role' => ['vi' => 'Nhà cung cấp', 'en' => 'Supplier'],
    'register.brand_role' => ['vi' => 'Thương hiệu', 'en' => 'Brand'],
    'register.business_partner_title' => ['vi' => 'Đối tác kinh doanh', 'en' => 'Business Partner'],
    
    // Company information section
    'register.company_info_title' => ['vi' => 'Thông tin công ty', 'en' => 'Company Information'],
    'register.company_info_description' => ['vi' => 'Cung cấp thông tin chi tiết về công ty của bạn', 'en' => 'Provide detailed information about your company'],
    'register.company_name_label' => ['vi' => 'Tên công ty', 'en' => 'Company Name'],
    'register.company_name_placeholder' => ['vi' => 'Nhập tên công ty đầy đủ', 'en' => 'Enter full company name'],
    'register.company_name_help' => ['vi' => 'Tên công ty phải chính xác như trong giấy phép kinh doanh', 'en' => 'Company name must match your business license'],
    
    // Business license
    'register.business_license_label' => ['vi' => 'Số giấy phép kinh doanh', 'en' => 'Business License Number'],
    'register.business_license_placeholder' => ['vi' => 'Nhập số giấy phép kinh doanh', 'en' => 'Enter business license number'],
    
    // Tax code
    'register.tax_code_label' => ['vi' => 'Mã số thuế', 'en' => 'Tax Code'],
    'register.tax_code_placeholder' => ['vi' => 'Nhập mã số thuế (10-13 chữ số)', 'en' => 'Enter tax code (10-13 digits)'],
    'register.tax_code_help' => ['vi' => 'Mã số thuế do cơ quan thuế cấp', 'en' => 'Tax code issued by tax authority'],
    
    // Company description
    'register.company_description_label' => ['vi' => 'Mô tả công ty', 'en' => 'Company Description'],
    'register.company_description_help' => ['vi' => 'Mô tả ngắn gọn về hoạt động kinh doanh chính của công ty', 'en' => 'Brief description of your main business activities'],
    
    // Business field
    'register.business_field_label' => ['vi' => 'Lĩnh vực kinh doanh', 'en' => 'Business Field'],
    'register.business_field_help' => ['vi' => 'Chọn các lĩnh vực kinh doanh chính của công ty', 'en' => 'Select your main business fields'],
    
    // Business categories
    'register.business_categories' => [
        'vi' => [
            'manufacturing' => 'Sản xuất',
            'trading' => 'Thương mại',
            'services' => 'Dịch vụ',
            'technology' => 'Công nghệ',
            'construction' => 'Xây dựng',
            'automotive' => 'Ô tô',
            'electronics' => 'Điện tử',
            'machinery' => 'Máy móc',
            'materials' => 'Vật liệu',
            'energy' => 'Năng lượng',
            'healthcare' => 'Y tế',
            'education' => 'Giáo dục',
            'finance' => 'Tài chính',
            'logistics' => 'Logistics',
            'agriculture' => 'Nông nghiệp',
            'food' => 'Thực phẩm',
            'textile' => 'Dệt may',
            'chemical' => 'Hóa chất',
            'pharmaceutical' => 'Dược phẩm',
            'aerospace' => 'Hàng không vũ trụ',
            'marine' => 'Hàng hải',
            'mining' => 'Khai thác',
            'oil_gas' => 'Dầu khí',
            'renewable_energy' => 'Năng lượng tái tạo',
            'telecommunications' => 'Viễn thông',
            'software' => 'Phần mềm',
            'consulting' => 'Tư vấn',
            'research' => 'Nghiên cứu',
            'design' => 'Thiết kế',
            'other' => 'Khác'
        ],
        'en' => [
            'manufacturing' => 'Manufacturing',
            'trading' => 'Trading',
            'services' => 'Services',
            'technology' => 'Technology',
            'construction' => 'Construction',
            'automotive' => 'Automotive',
            'electronics' => 'Electronics',
            'machinery' => 'Machinery',
            'materials' => 'Materials',
            'energy' => 'Energy',
            'healthcare' => 'Healthcare',
            'education' => 'Education',
            'finance' => 'Finance',
            'logistics' => 'Logistics',
            'agriculture' => 'Agriculture',
            'food' => 'Food',
            'textile' => 'Textile',
            'chemical' => 'Chemical',
            'pharmaceutical' => 'Pharmaceutical',
            'aerospace' => 'Aerospace',
            'marine' => 'Marine',
            'mining' => 'Mining',
            'oil_gas' => 'Oil & Gas',
            'renewable_energy' => 'Renewable Energy',
            'telecommunications' => 'Telecommunications',
            'software' => 'Software',
            'consulting' => 'Consulting',
            'research' => 'Research',
            'design' => 'Design',
            'other' => 'Other'
        ]
    ],
    
    // Contact information
    'register.contact_info_title' => ['vi' => 'Thông tin liên hệ', 'en' => 'Contact Information'],
    'register.contact_info_description' => ['vi' => 'Thông tin liên hệ chính thức của công ty', 'en' => 'Official contact information of your company'],
    'register.company_phone' => ['vi' => 'Số điện thoại công ty', 'en' => 'Company Phone'],
    'register.company_email_label' => ['vi' => 'Email công ty', 'en' => 'Company Email'],
    'register.company_email_help' => ['vi' => 'Email chính thức của công ty (khác với email đăng ký)', 'en' => 'Official company email (different from registration email)'],
    'register.company_address' => ['vi' => 'Địa chỉ công ty', 'en' => 'Company Address'],
    
    // Verification documents
    'register.verification_docs_title' => ['vi' => 'Tài liệu xác thực', 'en' => 'Verification Documents'],
    'register.verification_docs_description' => ['vi' => 'Tải lên các tài liệu để xác thực doanh nghiệp', 'en' => 'Upload documents to verify your business'],
    'register.file_upload_title' => ['vi' => 'Tải lên tài liệu', 'en' => 'Upload Documents'],
    'register.file_upload_support' => ['vi' => 'Hỗ trợ: PDF, JPG, PNG', 'en' => 'Supported: PDF, JPG, PNG'],
    'register.file_upload_size' => ['vi' => 'Kích thước tối đa: 10MB mỗi file', 'en' => 'Maximum size: 10MB per file'],
    'register.choose_documents' => ['vi' => 'Chọn tài liệu', 'en' => 'Choose Documents'],
    'register.document_suggestions' => ['vi' => 'Gợi ý: Giấy phép kinh doanh, Giấy chứng nhận đăng ký thuế, Hợp đồng thuê văn phòng', 'en' => 'Suggestions: Business license, Tax registration certificate, Office lease contract'],
    
    // Important notes
    'register.important_notes_title' => ['vi' => 'Lưu ý quan trọng', 'en' => 'Important Notes'],
    'register.note_verification_required' => ['vi' => 'Tài khoản cần được xác thực trước khi sử dụng đầy đủ tính năng', 'en' => 'Account needs verification before full feature access'],
    'register.note_verification_time' => ['vi' => 'Quá trình xác thực có thể mất 1-3 ngày làm việc', 'en' => 'Verification process may take 1-3 business days'],
    'register.note_email_notification' => ['vi' => 'Bạn sẽ nhận được email thông báo kết quả xác thực', 'en' => 'You will receive email notification of verification results'],
    'register.note_pending_access' => ['vi' => 'Trong thời gian chờ xác thực, bạn có thể sử dụng các tính năng cơ bản', 'en' => 'While pending verification, you can use basic features'],
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
            
            // Handle array values (like business_categories)
            if (is_array($value)) {
                $arrayString = "[\n";
                foreach ($value as $subKey => $subValue) {
                    $subValue = str_replace("'", "\\'", $subValue);
                    $arrayString .= "        '$subKey' => '$subValue',\n";
                }
                $arrayString .= "    ]";
                $newKeysString .= "  '$key' => $arrayString,\n";
            } else {
                // Escape single quotes in the value
                $value = str_replace("'", "\\'", $value);
                $newKeysString .= "  '$key' => '$value',\n";
            }
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

echo "📁 Processing auth wizard step2 keys for auth.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/auth.php";
if (addKeysToFile($viFile, $authStep2Keys, 'vi')) {
    $totalAdded = count($authStep2Keys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/auth.php";
addKeysToFile($enFile, $authStep2Keys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total auth wizard step2 keys added: " . count($authStep2Keys) . "\n";

echo "\n✅ Auth wizard step2 keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
