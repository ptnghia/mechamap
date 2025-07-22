<?php

/**
 * ADD REMAINING SHOWCASE KEYS
 * Thêm các keys còn thiếu cho showcase từ threads/partials/showcase.blade.php
 */

echo "=== ADDING REMAINING SHOWCASE KEYS ===\n\n";

// Keys that need to be added to different files
$additionalKeys = [
    // For showcase.php - specific showcase keys found in the file
    'showcase' => [
        'related' => ['vi' => 'Liên quan', 'en' => 'Related'],
        'for_thread' => ['vi' => 'cho chủ đề', 'en' => 'for thread'],
        'create_from_thread' => ['vi' => 'Tạo từ chủ đề', 'en' => 'Create from thread'],
        'create_showcase_info' => ['vi' => 'Tạo showcase để giới thiệu dự án của bạn', 'en' => 'Create a showcase to present your project'],
        'create_showcase_button' => ['vi' => 'Tạo Showcase', 'en' => 'Create Showcase'],
        'create_from_thread_title' => ['vi' => 'Tạo Showcase từ chủ đề', 'en' => 'Create Showcase from Thread'],
        'basic_info' => ['vi' => 'Thông tin cơ bản', 'en' => 'Basic Information'],
        'content' => ['vi' => 'Nội dung', 'en' => 'Content'],
        'complete' => ['vi' => 'Hoàn thành', 'en' => 'Complete'],
        'step_1_title' => ['vi' => 'Bước 1: Thông tin cơ bản', 'en' => 'Step 1: Basic Information'],
        'showcase_title_help' => ['vi' => 'Nhập tiêu đề mô tả cho showcase của bạn', 'en' => 'Enter a descriptive title for your showcase'],
        'category' => ['vi' => 'Danh mục', 'en' => 'Category'],
        'project_type' => ['vi' => 'Loại dự án', 'en' => 'Project Type'],
        'select_project_type' => ['vi' => 'Chọn loại dự án', 'en' => 'Select project type'],
        'step_2_title' => ['vi' => 'Bước 2: Nội dung', 'en' => 'Step 2: Content'],
        'project_description' => ['vi' => 'Mô tả dự án', 'en' => 'Project Description'],
        'project_description_help' => ['vi' => 'Mô tả chi tiết về dự án của bạn', 'en' => 'Describe your project in detail'],
        'cover_image' => ['vi' => 'Ảnh bìa', 'en' => 'Cover Image'],
        'cover_image_help' => ['vi' => 'Chọn ảnh bìa cho showcase', 'en' => 'Select a cover image for your showcase'],
        'current_thread_image' => ['vi' => 'Ảnh chủ đề hiện tại', 'en' => 'Current thread image'],
        'complexity_level' => ['vi' => 'Mức độ phức tạp', 'en' => 'Complexity Level'],
        'complexity_levels.beginner' => ['vi' => 'Người mới bắt đầu', 'en' => 'Beginner'],
        'complexity_levels.intermediate' => ['vi' => 'Trung cấp', 'en' => 'Intermediate'],
        'complexity_levels.advanced' => ['vi' => 'Nâng cao', 'en' => 'Advanced'],
        'complexity_levels.expert' => ['vi' => 'Chuyên gia', 'en' => 'Expert'],
        'industry_application' => ['vi' => 'Ứng dụng ngành', 'en' => 'Industry Application'],
        'industry_placeholder' => ['vi' => 'Ví dụ: Xây dựng, Cơ khí, Điện tử...', 'en' => 'e.g., Construction, Mechanical, Electronics...'],
        'file_attachments' => ['vi' => 'Tệp đính kèm', 'en' => 'File Attachments'],
        'file_attachments_optional' => ['vi' => 'Tệp đính kèm (tùy chọn)', 'en' => 'File Attachments (Optional)'],
        'file_upload_area' => ['vi' => 'Kéo thả tệp vào đây hoặc nhấp để chọn', 'en' => 'Drag and drop files here or click to select'],
        'browse_files' => ['vi' => 'Duyệt tệp', 'en' => 'Browse Files'],
        'file_upload_help' => ['vi' => 'Tải lên tệp CAD, hình ảnh, tài liệu liên quan', 'en' => 'Upload CAD files, images, related documents'],
        'file_upload_limits' => ['vi' => 'Tối đa 10 tệp, mỗi tệp không quá 50MB', 'en' => 'Maximum 10 files, 50MB each'],
        'files_selected' => ['vi' => 'tệp đã chọn', 'en' => 'files selected'],
        'file_upload_description' => ['vi' => 'Tải lên các tệp liên quan đến dự án', 'en' => 'Upload files related to your project'],
        'step_3_title' => ['vi' => 'Bước 3: Hoàn thành', 'en' => 'Step 3: Complete'],
        'confirm_info' => ['vi' => 'Xác nhận thông tin', 'en' => 'Confirm Information'],
        'agree_terms' => ['vi' => 'Tôi đồng ý với điều khoản sử dụng', 'en' => 'I agree to the terms of use'],
        'previous' => ['vi' => 'Trước', 'en' => 'Previous'],
        'next' => ['vi' => 'Tiếp theo', 'en' => 'Next'],
        'file_size_error' => ['vi' => 'Kích thước tệp quá lớn', 'en' => 'File size too large'],
        'terms_required' => ['vi' => 'Bạn phải đồng ý với điều khoản', 'en' => 'You must agree to the terms'],
        'creating' => ['vi' => 'Đang tạo...', 'en' => 'Creating...'],
        'cover_image_required' => ['vi' => 'Ảnh bìa là bắt buộc', 'en' => 'Cover image is required'],
    ],
    
    // For common.php - common labels
    'common' => [
        'labels.by' => ['vi' => 'bởi', 'en' => 'by'],
    ],
    
    // For ui.php - UI actions
    'ui' => [
        'actions.view_full_showcase' => ['vi' => 'Xem showcase đầy đủ', 'en' => 'View full showcase'],
        'actions.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
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

$totalAdded = 0;

foreach ($additionalKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$category.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$category.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total keys added: $totalAdded\n";
echo "Categories processed: " . count($additionalKeys) . "\n";

echo "\n✅ Remaining showcase keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
