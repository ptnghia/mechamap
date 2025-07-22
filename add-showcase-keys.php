<?php

/**
 * ADD SHOWCASE KEYS
 * Thêm keys thiếu cho threads/partials/showcase.blade.php
 */

echo "=== ADDING SHOWCASE KEYS ===\n\n";

// Extract keys from threads/partials/showcase.blade.php
$showcaseFile = __DIR__ . '/resources/views/threads/partials/showcase.blade.php';

if (!file_exists($showcaseFile)) {
    echo "❌ File not found: $showcaseFile\n";
    exit(1);
}

$content = file_get_contents($showcaseFile);

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

echo "Found " . count($allKeys) . " unique keys in showcase file:\n";
foreach ($allKeys as $key) {
    echo "  - $key\n";
}

// Define translations for showcase keys
$showcaseKeys = [
    // Showcase creation and management
    'showcase.create_showcase' => ['vi' => 'Tạo Showcase', 'en' => 'Create Showcase'],
    'showcase.edit_showcase' => ['vi' => 'Chỉnh sửa Showcase', 'en' => 'Edit Showcase'],
    'showcase.showcase_title' => ['vi' => 'Tiêu đề Showcase', 'en' => 'Showcase Title'],
    'showcase.showcase_description' => ['vi' => 'Mô tả Showcase', 'en' => 'Showcase Description'],
    'showcase.showcase_category' => ['vi' => 'Danh mục Showcase', 'en' => 'Showcase Category'],
    'showcase.select_category' => ['vi' => 'Chọn danh mục', 'en' => 'Select category'],
    'showcase.showcase_tags' => ['vi' => 'Thẻ Showcase', 'en' => 'Showcase Tags'],
    'showcase.add_tags' => ['vi' => 'Thêm thẻ', 'en' => 'Add tags'],
    'showcase.showcase_images' => ['vi' => 'Hình ảnh Showcase', 'en' => 'Showcase Images'],
    'showcase.upload_images' => ['vi' => 'Tải lên hình ảnh', 'en' => 'Upload images'],
    'showcase.showcase_attachments' => ['vi' => 'Tệp đính kèm', 'en' => 'Attachments'],
    'showcase.upload_files' => ['vi' => 'Tải lên tệp', 'en' => 'Upload files'],
    'showcase.max_files_exceeded' => ['vi' => 'Vượt quá số file tối đa', 'en' => 'Maximum files exceeded'],
    'showcase.file_too_large' => ['vi' => 'File quá lớn', 'en' => 'File too large'],
    'showcase.invalid_file_type' => ['vi' => 'Loại file không hợp lệ', 'en' => 'Invalid file type'],
    
    // Showcase display
    'showcase.view_showcase' => ['vi' => 'Xem Showcase', 'en' => 'View Showcase'],
    'showcase.showcase_details' => ['vi' => 'Chi tiết Showcase', 'en' => 'Showcase Details'],
    'showcase.created_by' => ['vi' => 'Tạo bởi', 'en' => 'Created by'],
    'showcase.created_at' => ['vi' => 'Tạo lúc', 'en' => 'Created at'],
    'showcase.updated_at' => ['vi' => 'Cập nhật lúc', 'en' => 'Updated at'],
    'showcase.views_count' => ['vi' => 'Lượt xem', 'en' => 'Views'],
    'showcase.likes_count' => ['vi' => 'Lượt thích', 'en' => 'Likes'],
    'showcase.downloads_count' => ['vi' => 'Lượt tải', 'en' => 'Downloads'],
    
    // Showcase actions
    'showcase.like_showcase' => ['vi' => 'Thích Showcase', 'en' => 'Like Showcase'],
    'showcase.unlike_showcase' => ['vi' => 'Bỏ thích', 'en' => 'Unlike'],
    'showcase.share_showcase' => ['vi' => 'Chia sẻ Showcase', 'en' => 'Share Showcase'],
    'showcase.download_files' => ['vi' => 'Tải xuống tệp', 'en' => 'Download files'],
    'showcase.report_showcase' => ['vi' => 'Báo cáo Showcase', 'en' => 'Report Showcase'],
    'showcase.delete_showcase' => ['vi' => 'Xóa Showcase', 'en' => 'Delete Showcase'],
    
    // Showcase status
    'showcase.status.draft' => ['vi' => 'Bản nháp', 'en' => 'Draft'],
    'showcase.status.published' => ['vi' => 'Đã xuất bản', 'en' => 'Published'],
    'showcase.status.pending' => ['vi' => 'Chờ duyệt', 'en' => 'Pending'],
    'showcase.status.rejected' => ['vi' => 'Bị từ chối', 'en' => 'Rejected'],
    
    // Showcase validation
    'showcase.title_required' => ['vi' => 'Tiêu đề là bắt buộc', 'en' => 'Title is required'],
    'showcase.description_required' => ['vi' => 'Mô tả là bắt buộc', 'en' => 'Description is required'],
    'showcase.category_required' => ['vi' => 'Danh mục là bắt buộc', 'en' => 'Category is required'],
    'showcase.images_required' => ['vi' => 'Ít nhất một hình ảnh là bắt buộc', 'en' => 'At least one image is required'],
    
    // Showcase messages
    'showcase.created_successfully' => ['vi' => 'Tạo Showcase thành công', 'en' => 'Showcase created successfully'],
    'showcase.updated_successfully' => ['vi' => 'Cập nhật Showcase thành công', 'en' => 'Showcase updated successfully'],
    'showcase.deleted_successfully' => ['vi' => 'Xóa Showcase thành công', 'en' => 'Showcase deleted successfully'],
    'showcase.no_showcases_found' => ['vi' => 'Không tìm thấy Showcase nào', 'en' => 'No showcases found'],
    
    // Common showcase actions
    'showcase.save' => ['vi' => 'Lưu', 'en' => 'Save'],
    'showcase.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
    'showcase.submit' => ['vi' => 'Gửi', 'en' => 'Submit'],
    'showcase.publish' => ['vi' => 'Xuất bản', 'en' => 'Publish'],
    'showcase.save_draft' => ['vi' => 'Lưu bản nháp', 'en' => 'Save Draft'],
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

echo "\n📁 Processing showcase keys\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/showcase.php";
if (addKeysToFile($viFile, $showcaseKeys, 'vi')) {
    $totalAdded = count($showcaseKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/showcase.php";
addKeysToFile($enFile, $showcaseKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total keys added: " . count($showcaseKeys) . "\n";
echo "Keys processed: " . count($showcaseKeys) . "\n";

echo "\n✅ Showcase keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
