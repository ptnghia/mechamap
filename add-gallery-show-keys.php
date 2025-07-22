<?php

/**
 * ADD GALLERY SHOW KEYS
 * Thêm tất cả keys thiếu cho gallery/show.blade.php
 */

echo "=== ADDING GALLERY SHOW KEYS ===\n\n";

// All gallery show keys
$galleryShowKeys = [
    'description' => ['vi' => 'Mô tả', 'en' => 'Description'],
    'comments' => ['vi' => 'Bình luận', 'en' => 'Comments'],
    'comments_coming_soon' => ['vi' => 'Tính năng bình luận sẽ sớm ra mắt.', 'en' => 'Comments feature coming soon.'],
    'media_information' => ['vi' => 'Thông tin phương tiện', 'en' => 'Media Information'],
    'uploaded' => ['vi' => 'Đã tải lên', 'en' => 'Uploaded'],
    'file_name' => ['vi' => 'Tên tệp', 'en' => 'File Name'],
    'file_type' => ['vi' => 'Loại tệp', 'en' => 'File Type'],
    'file_size' => ['vi' => 'Kích thước tệp', 'en' => 'File Size'],
    'dimensions' => ['vi' => 'Kích thước', 'en' => 'Dimensions'],
    'unknown' => ['vi' => 'Không xác định', 'en' => 'Unknown'],
    'download' => ['vi' => 'Tải xuống', 'en' => 'Download'],
    'delete_confirm' => ['vi' => 'Bạn có chắc chắn muốn xóa phương tiện này không?', 'en' => 'Are you sure you want to delete this media?'],
    'delete' => ['vi' => 'Xóa', 'en' => 'Delete'],
    'share' => ['vi' => 'Chia sẻ', 'en' => 'Share'],
    'check_out_image' => ['vi' => 'Xem hình ảnh này', 'en' => 'Check out this image'],
    'url_copied' => ['vi' => 'URL đã được sao chép vào clipboard!', 'en' => 'URL copied to clipboard!'],
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

echo "📁 Processing gallery show keys for gallery.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/gallery.php";
if (addKeysToFile($viFile, $galleryShowKeys, 'vi')) {
    $totalAdded = count($galleryShowKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/gallery.php";
addKeysToFile($enFile, $galleryShowKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total gallery show keys added: " . count($galleryShowKeys) . "\n";

echo "\n✅ Gallery show keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
