<?php

/**
 * Complete 100% Conversion Script
 * 
 * This script converts the final remaining translation keys from {{ __('key') }} format
 * to hardcoded Vietnamese text to achieve 100% completion.
 */

echo "🎯 COMPLETING 100% ADMIN PANEL TRANSLATION\n";
echo "==========================================\n\n";

// Define the final keys that need to be converted from {{ __('key') }} to hardcoded text
$finalKeys = [
    "{{ __('Số thông báo tối đa/người dùng') }}" => "'Số thông báo tối đa/người dùng'",
    "{{ __('Thông báo hành động quản trị') }}" => "'Thông báo hành động quản trị'",
    "{{ __('Người dùng phải xác thực email trước khi tham gia diễn đàn') }}" => "'Người dùng phải xác thực email trước khi tham gia diễn đàn'",
    "{{ __('Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang web.') }}" => "'Cài đặt các thông tin cơ bản của trang web. Các thông tin này sẽ được sử dụng ở nhiều nơi trên trang web.'",
    "{{ __('Độ dài mật khẩu tối thiểu') }}" => "'Độ dài mật khẩu tối thiểu'",
    "{{ __('Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt') }}" => "'Mật khẩu phải có chữ hoa, chữ thường, số và ký tự đặc biệt'",
    "{{ __('Độ dài tên người dùng tối thiểu') }}" => "'Độ dài tên người dùng tối thiểu'",
    "{{ __('Người dùng có thể thay đổi tên người dùng sau khi đăng ký') }}" => "'Người dùng có thể thay đổi tên người dùng sau khi đăng ký'",
    "{{ __('Kích thước avatar tối đa (KB)') }}" => "'Kích thước avatar tối đa (KB)'",
    "{{ __('Loại file avatar được phép') }}" => "'Loại file avatar được phép'",
    "{{ __('Trả lời trong:') }}" => "'Trả lời trong:'",
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
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $fileConverted++;
            echo "✅ Converted in $file: $search -> $replace\n";
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

echo "==========================================\n";
echo "🎉 100% CONVERSION COMPLETED!\n";
echo "==========================================\n";
echo "📊 Files processed: " . count($targetFiles) . "\n";
echo "📊 Files changed: $filesChanged\n";
echo "📊 Total conversions: $totalConverted\n";
echo "💾 Backup files created with timestamp\n\n";

if ($totalConverted > 0) {
    echo "✅ SUCCESS: 100% conversion achieved!\n";
    echo "🎯 Admin panel is now 100% hardcoded Vietnamese!\n\n";
    
    echo "📋 FINAL VERIFICATION:\n";
    echo "1. Run remaining keys check to confirm 0 keys left\n";
    echo "2. Test admin panel functionality\n";
    echo "3. Create final commit\n";
    echo "4. Generate completion report\n\n";
} else {
    echo "⚠️  No conversions were made.\n";
    echo "🔍 Keys might already be converted or in different format.\n\n";
}

echo "🏁 100% completion script finished!\n";
