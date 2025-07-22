<?php

/**
 * ADD SHOWCASE PARTIAL KEYS
 * Thêm tất cả keys thiếu cho threads/partials/showcase.blade.php
 */

echo "=== ADDING SHOWCASE PARTIAL KEYS ===\n\n";

// All showcase partial keys organized by category
$showcaseKeys = [
    // Showcase keys
    'showcase_keys' => [
        'related' => ['vi' => 'Showcase liên quan', 'en' => 'Related Showcase'],
        'for_thread' => ['vi' => 'cho chủ đề này', 'en' => 'for this thread'],
        'create_from_thread' => ['vi' => 'Tạo Showcase từ chủ đề', 'en' => 'Create Showcase from Thread'],
        'create_showcase_info' => ['vi' => 'Biến chủ đề này thành một showcase chuyên nghiệp', 'en' => 'Turn this thread into a professional showcase'],
        'create_showcase_button' => ['vi' => 'Tạo Showcase', 'en' => 'Create Showcase'],
        'create_from_thread_title' => ['vi' => 'Tạo Showcase từ chủ đề', 'en' => 'Create Showcase from Thread'],
        'basic_info' => ['vi' => 'Thông tin cơ bản', 'en' => 'Basic Info'],
        'content' => ['vi' => 'Nội dung', 'en' => 'Content'],
        'complete' => ['vi' => 'Hoàn thành', 'en' => 'Complete'],
        'step_1_title' => ['vi' => 'Thông tin cơ bản về showcase', 'en' => 'Basic showcase information'],
        'showcase_title' => ['vi' => 'Tiêu đề showcase', 'en' => 'Showcase title'],
        'showcase_title_help' => ['vi' => 'Tiêu đề ngắn gọn và thu hút cho showcase của bạn', 'en' => 'A concise and attractive title for your showcase'],
        'category' => ['vi' => 'Danh mục', 'en' => 'Category'],
        'select_category' => ['vi' => 'Chọn danh mục', 'en' => 'Select category'],
        'project_type' => ['vi' => 'Loại dự án', 'en' => 'Project type'],
        'select_project_type' => ['vi' => 'Chọn loại dự án', 'en' => 'Select project type'],
        'step_2_title' => ['vi' => 'Nội dung và hình ảnh', 'en' => 'Content and images'],
        'project_description' => ['vi' => 'Mô tả dự án', 'en' => 'Project description'],
        'project_description_help' => ['vi' => 'Mô tả chi tiết về dự án, quy trình thực hiện và kết quả đạt được', 'en' => 'Detailed description of the project, implementation process and results achieved'],
        'cover_image' => ['vi' => 'Ảnh bìa', 'en' => 'Cover image'],
        'cover_image_help' => ['vi' => 'Ảnh đại diện chính cho showcase (tỷ lệ 16:9 được khuyến nghị)', 'en' => 'Main representative image for showcase (16:9 ratio recommended)'],
        'current_thread_image' => ['vi' => 'Ảnh hiện tại của chủ đề', 'en' => 'Current thread image'],
        'complexity_level' => ['vi' => 'Mức độ phức tạp', 'en' => 'Complexity level'],
        'complexity_levels' => [
            'vi' => [
                'beginner' => 'Cơ bản',
                'intermediate' => 'Trung bình',
                'advanced' => 'Nâng cao',
                'expert' => 'Chuyên gia'
            ],
            'en' => [
                'beginner' => 'Beginner',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced',
                'expert' => 'Expert'
            ]
        ],
        'industry_application' => ['vi' => 'Ứng dụng trong ngành', 'en' => 'Industry application'],
        'industry_placeholder' => ['vi' => 'VD: Ô tô, Hàng không, Xây dựng...', 'en' => 'e.g: Automotive, Aerospace, Construction...'],
        'file_attachments' => ['vi' => 'Tệp đính kèm', 'en' => 'File attachments'],
        'file_attachments_optional' => ['vi' => 'tùy chọn', 'en' => 'optional'],
        'file_upload_area' => ['vi' => 'Kéo thả tệp vào đây hoặc', 'en' => 'Drag and drop files here or'],
        'browse_files' => ['vi' => 'duyệt tệp', 'en' => 'browse files'],
        'file_upload_help' => ['vi' => 'Hỗ trợ: JPG, PNG, PDF, DWG, STEP, STL', 'en' => 'Supported: JPG, PNG, PDF, DWG, STEP, STL'],
        'file_upload_limits' => ['vi' => 'Tối đa 10 tệp, mỗi tệp không quá 50MB', 'en' => 'Maximum 10 files, 50MB each'],
        'files_selected' => ['vi' => 'Tệp đã chọn', 'en' => 'Selected files'],
        'file_upload_description' => ['vi' => 'Tải lên hình ảnh, bản vẽ kỹ thuật, tệp CAD hoặc tài liệu liên quan đến dự án', 'en' => 'Upload images, technical drawings, CAD files or project-related documents'],
        'step_3_title' => ['vi' => 'Xác nhận và hoàn thành', 'en' => 'Confirm and complete'],
        'confirm_info' => ['vi' => 'Xác nhận thông tin', 'en' => 'Confirm information'],
        'confirm_points' => [
            'vi' => [
                'Showcase sẽ được tạo và liên kết với chủ đề này',
                'Thông tin sẽ được hiển thị công khai',
                'Bạn có thể chỉnh sửa showcase sau khi tạo',
                'Showcase sẽ được xem xét trước khi xuất bản'
            ],
            'en' => [
                'Showcase will be created and linked to this thread',
                'Information will be displayed publicly',
                'You can edit the showcase after creation',
                'Showcase will be reviewed before publication'
            ]
        ],
        'agree_terms' => ['vi' => 'Tôi đồng ý với điều khoản sử dụng và chính sách bảo mật', 'en' => 'I agree to the terms of use and privacy policy'],
        'previous' => ['vi' => 'Trước', 'en' => 'Previous'],
        'next' => ['vi' => 'Tiếp theo', 'en' => 'Next'],
        'create_showcase' => ['vi' => 'Tạo Showcase', 'en' => 'Create Showcase'],
        'title_required' => ['vi' => 'Vui lòng nhập tiêu đề showcase', 'en' => 'Please enter showcase title'],
        'category_required' => ['vi' => 'Vui lòng chọn danh mục', 'en' => 'Please select category'],
        'description_required' => ['vi' => 'Vui lòng nhập mô tả dự án', 'en' => 'Please enter project description'],
        'cover_image_required' => ['vi' => 'Vui lòng chọn ảnh bìa', 'en' => 'Please select cover image'],
        'file_size_error' => ['vi' => 'Kích thước tệp quá lớn (tối đa 5MB)', 'en' => 'File size too large (maximum 5MB)'],
        'terms_required' => ['vi' => 'Vui lòng đồng ý với điều khoản sử dụng', 'en' => 'Please agree to the terms of use'],
        'creating' => ['vi' => 'Đang tạo...', 'en' => 'Creating...'],
        'max_files_exceeded' => ['vi' => 'Không thể tải lên quá 10 tệp', 'en' => 'Cannot upload more than 10 files'],
        'file_too_large' => ['vi' => 'Tệp :filename quá lớn (tối đa 50MB)', 'en' => 'File :filename is too large (maximum 50MB)'],
        'file_type_not_supported' => ['vi' => 'Loại tệp :filename không được hỗ trợ', 'en' => 'File type :filename is not supported'],
    ],
    
    // UI keys
    'ui_keys' => [
        'actions.view_full_showcase' => ['vi' => 'Xem showcase đầy đủ', 'en' => 'View full showcase'],
        'actions.view_details' => ['vi' => 'Xem chi tiết', 'en' => 'View details'],
        'actions.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
    ],
    
    // Common keys
    'common_keys' => [
        'labels.by' => ['vi' => 'bởi', 'en' => 'by'],
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
            
            // Handle array values (like complexity_levels and confirm_points)
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

// Map categories to files
$categoryFileMap = [
    'showcase_keys' => 'showcase',
    'ui_keys' => 'ui',
    'common_keys' => 'common',
];

$totalAdded = 0;

foreach ($showcaseKeys as $category => $keys) {
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
echo "Total showcase partial keys added: $totalAdded\n";
echo "Categories processed: " . count($showcaseKeys) . "\n";

echo "\n✅ Showcase partial keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
