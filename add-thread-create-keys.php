<?php

/**
 * ADD THREAD CREATE KEYS
 * Thêm tất cả keys thiếu cho threads/create.blade.php
 */

echo "=== ADDING THREAD CREATE KEYS ===\n\n";

// All thread create keys
$threadCreateKeys = [
    // Forum create keys
    'forum_create' => [
        'create.title' => ['vi' => 'Tạo chủ đề mới', 'en' => 'Create New Thread'],
        'create.subtitle' => ['vi' => 'Chia sẻ ý tưởng, đặt câu hỏi hoặc bắt đầu thảo luận', 'en' => 'Share ideas, ask questions or start discussions'],
        'create.back' => ['vi' => 'Quay lại', 'en' => 'Back'],
        'create.progress_label' => ['vi' => 'Tiến trình tạo chủ đề', 'en' => 'Thread creation progress'],
        
        // Steps
        'create.step_basic' => ['vi' => 'Thông tin cơ bản', 'en' => 'Basic Info'],
        'create.step_basic_aria' => ['vi' => 'Bước 1: Nhập thông tin cơ bản', 'en' => 'Step 1: Enter basic information'],
        'create.step_content' => ['vi' => 'Nội dung', 'en' => 'Content'],
        'create.step_content_aria' => ['vi' => 'Bước 2: Viết nội dung chủ đề', 'en' => 'Step 2: Write thread content'],
        'create.step_poll' => ['vi' => 'Bình chọn', 'en' => 'Poll'],
        'create.step_poll_aria' => ['vi' => 'Bước 4: Tạo bình chọn (tùy chọn)', 'en' => 'Step 4: Create poll (optional)'],
        'create.step_review' => ['vi' => 'Xem lại', 'en' => 'Review'],
        'create.step_review_aria' => ['vi' => 'Bước 5: Xem lại và đăng chủ đề', 'en' => 'Step 5: Review and post thread'],
        
        // Actions
        'create.previous' => ['vi' => 'Trước', 'en' => 'Previous'],
        'create.create_button' => ['vi' => 'Tạo chủ đề', 'en' => 'Create Thread'],
    ],
    
    // Common thread showcase keys
    'common_showcase' => [
        'thread_showcase.step_title' => ['vi' => 'Showcase', 'en' => 'Showcase'],
        'thread_showcase.step_description' => ['vi' => 'Tạo showcase để giới thiệu dự án của bạn', 'en' => 'Create showcase to present your project'],
        'thread_showcase.step_aria' => ['vi' => 'Bước 3: Tạo showcase (tùy chọn)', 'en' => 'Step 3: Create showcase (optional)'],
        'thread_showcase.enable_showcase' => ['vi' => 'Kích hoạt showcase', 'en' => 'Enable showcase'],
        'thread_showcase.enable_showcase_help' => ['vi' => 'Tạo showcase để trưng bày dự án một cách chuyên nghiệp', 'en' => 'Create showcase to display your project professionally'],
        'thread_showcase.create_new' => ['vi' => 'Tạo showcase mới', 'en' => 'Create new showcase'],
        'thread_showcase.attach_existing' => ['vi' => 'Đính kèm showcase có sẵn', 'en' => 'Attach existing showcase'],
        'thread_showcase.select_existing' => ['vi' => 'Chọn showcase có sẵn', 'en' => 'Select existing showcase'],
        'thread_showcase.no_existing_showcases' => ['vi' => 'Bạn chưa có showcase nào', 'en' => 'You don\'t have any showcases yet'],
        'thread_showcase.showcase_title' => ['vi' => 'Tiêu đề showcase', 'en' => 'Showcase title'],
        'thread_showcase.showcase_title_placeholder' => ['vi' => 'Nhập tiêu đề cho showcase...', 'en' => 'Enter showcase title...'],
        'thread_showcase.showcase_description' => ['vi' => 'Mô tả showcase', 'en' => 'Showcase description'],
        'thread_showcase.showcase_description_placeholder' => ['vi' => 'Mô tả chi tiết về dự án, quy trình thực hiện...', 'en' => 'Detailed description of project, implementation process...'],
        'thread_showcase.project_type' => ['vi' => 'Loại dự án', 'en' => 'Project type'],
        'thread_showcase.project_type_placeholder' => ['vi' => 'VD: Thiết kế cơ khí, Sản xuất...', 'en' => 'e.g: Mechanical Design, Manufacturing...'],
        'thread_showcase.complexity_level' => ['vi' => 'Mức độ phức tạp', 'en' => 'Complexity level'],
        'thread_showcase.title_required' => ['vi' => 'Vui lòng nhập tiêu đề showcase', 'en' => 'Please enter showcase title'],
        'thread_showcase.description_required' => ['vi' => 'Vui lòng nhập mô tả showcase', 'en' => 'Please enter showcase description'],
        'thread_showcase.description_min' => ['vi' => 'Mô tả phải có ít nhất 50 ký tự', 'en' => 'Description must be at least 50 characters'],
    ],
    
    // Showcase file keys
    'showcase_files' => [
        'file_attachments' => ['vi' => 'Tệp đính kèm', 'en' => 'File Attachments'],
        'file_attachments_optional' => ['vi' => 'tùy chọn', 'en' => 'optional'],
        'file_upload_area' => ['vi' => 'Kéo thả tệp vào đây hoặc', 'en' => 'Drag and drop files here or'],
        'browse_files' => ['vi' => 'duyệt tệp', 'en' => 'browse files'],
        'file_upload_help' => ['vi' => 'Hỗ trợ: JPG, PNG, PDF, DWG, STEP, STL', 'en' => 'Supported: JPG, PNG, PDF, DWG, STEP, STL'],
        'file_upload_limits' => ['vi' => 'Tối đa 10 tệp, mỗi tệp không quá 50MB', 'en' => 'Maximum 10 files, 50MB each'],
        'files_selected' => ['vi' => 'Tệp đã chọn', 'en' => 'Selected files'],
        'file_upload_description' => ['vi' => 'Tải lên hình ảnh, bản vẽ kỹ thuật, tệp CAD hoặc tài liệu liên quan đến dự án', 'en' => 'Upload images, technical drawings, CAD files or project-related documents'],
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
    'forum_create' => 'forum',
    'common_showcase' => 'common',
    'showcase_files' => 'showcase',
];

$totalAdded = 0;

foreach ($threadCreateKeys as $category => $keys) {
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
echo "Total thread create keys added: $totalAdded\n";
echo "Categories processed: " . count($threadCreateKeys) . "\n";

echo "\n✅ Thread create keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
