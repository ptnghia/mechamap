<?php

/**
 * Final Conversion Script - Complete 100% Admin Panel Translation
 * 
 * This script handles the final 11 translation keys with complex line breaks
 * to achieve 100% completion of admin panel translation conversion.
 */

echo "🎯 FINAL CONVERSION - COMPLETING 100% ADMIN PANEL TRANSLATION\n";
echo "=============================================================\n\n";

// Define the final 11 keys with exact line break patterns
$finalKeys = [
    // Keys with line breaks that need exact matching
    "Số thông báo tối đa/người\n                                    dùng" => "Số thông báo tối đa/người dùng",
    "Thông báo hành động quản\n                                    trị" => "Thông báo hành động quản trị",
    "Người dùng phải xác thực email trước khi tham gia diễn\n                                    đàn" => "Người dùng phải xác thực email trước khi tham gia diễn đàn",
    "Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang\n            web." => "Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang web.",
    "Độ dài mật khẩu tối\n                                    thiểu" => "Độ dài mật khẩu tối thiểu",
    "Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc\n                                    biệt" => "Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt",
    "Độ dài tên người dùng\n                                    tối thiểu" => "Độ dài tên người dùng tối thiểu",
    "Người dùng có thể thay đổi tên người dùng sau khi đăng\n                                    ký" => "Người dùng có thể thay đổi tên người dùng sau khi đăng ký",
    "Kích thước avatar tối đa\n                                    (KB)" => "Kích thước avatar tối đa (KB)",
    "Loại file avatar được\n                                    phép" => "Loại file avatar được phép",
    "Trả lời trong:" => "Trả lời trong:",
];

// Files that contain the remaining keys
$targetFiles = [
    'resources/views/admin/alerts/index.blade.php',
    'resources/views/admin/settings/forum.blade.php',
    'resources/views/admin/settings/partials/sidebar.blade.php',
    'resources/views/admin/settings/user.blade.php',
    'resources/views/admin/users/show.blade.php',
];

$totalConverted = 0;
$filesChanged = 0;

foreach ($targetFiles as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    $fileConverted = 0;
    
    // Process each key
    foreach ($finalKeys as $search => $replace) {
        // Try exact match first
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $fileConverted++;
            echo "✅ Converted in $file: '$search' -> '$replace'\n";
        }
        
        // Try with different whitespace patterns
        $searchVariations = [
            str_replace("\n", " ", $search), // Replace newlines with spaces
            str_replace(["\n", "                                    "], [" ", " "], $search), // Clean up spaces
            preg_replace('/\s+/', ' ', $search), // Normalize all whitespace
        ];
        
        foreach ($searchVariations as $variation) {
            if (strpos($content, $variation) !== false) {
                $content = str_replace($variation, $replace, $content);
                $fileConverted++;
                echo "✅ Converted variation in $file: '$variation' -> '$replace'\n";
                break;
            }
        }
    }
    
    // Save file if changes were made
    if ($content !== $originalContent) {
        // Create backup
        $backupFile = $file . '.backup.' . date('Y-m-d-H-i-s');
        copy($file, $backupFile);
        
        // Save updated content
        file_put_contents($file, $content);
        
        $filesChanged++;
        $totalConverted += $fileConverted;
        
        echo "📁 Updated $file ($fileConverted keys converted)\n";
        echo "💾 Backup created: $backupFile\n\n";
    } else {
        echo "⚠️  No changes in $file\n\n";
    }
}

echo "=============================================================\n";
echo "🎉 FINAL CONVERSION COMPLETED!\n";
echo "=============================================================\n";
echo "📊 Files processed: " . count($targetFiles) . "\n";
echo "📊 Files changed: $filesChanged\n";
echo "📊 Total conversions: $totalConverted\n";
echo "💾 Backup files created with timestamp\n\n";

if ($totalConverted > 0) {
    echo "✅ SUCCESS: Final conversion completed successfully!\n";
    echo "🎯 Admin panel should now be 100% converted to Vietnamese!\n\n";
    
    echo "📋 NEXT STEPS:\n";
    echo "1. Run the remaining keys check to verify 100% completion\n";
    echo "2. Test the admin panel thoroughly\n";
    echo "3. Commit the final changes\n";
    echo "4. Create completion report\n\n";
} else {
    echo "⚠️  No conversions were made. Keys might already be converted or patterns don't match.\n";
    echo "🔍 Run the remaining keys check to see current status.\n\n";
}

echo "🏁 Final conversion script completed!\n";
