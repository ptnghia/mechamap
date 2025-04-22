<?php

// Đường dẫn đến thư mục views
$viewsPath = 'resources/views';

// Danh sách các file cần bỏ qua
$skipFiles = [
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/home.blade.php',
    'resources/views/auth/login.blade.php',
    'resources/views/dashboard.blade.php',
    // Thêm các file đã chuyển đổi vào đây
];

// Hàm để tìm tất cả các file .blade.php
function findBladeFiles($dir, &$results = []) {
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            findBladeFiles($path, $results);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php' && strpos($path, '.blade.php') !== false) {
            $results[] = $path;
        }
    }

    return $results;
}

// Tìm tất cả các file .blade.php
$bladeFiles = findBladeFiles($viewsPath);

// Lọc ra các file không cần bỏ qua
$bladeFiles = array_filter($bladeFiles, function($file) use ($skipFiles) {
    return !in_array($file, $skipFiles);
});

// Đếm số file đã chuyển đổi
$convertedCount = 0;

// Chuyển đổi từng file
foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $modified = false;
    
    // Kiểm tra xem file có sử dụng <x-app-layout> không
    if (strpos($content, '<x-app-layout>') !== false) {
        // Chuyển đổi từ <x-app-layout> sang @extends('layouts.app')
        $content = preg_replace(
            '/<x-app-layout>\s*(?:<x-slot name="header">\s*(.*?)\s*<\/x-slot>)?/s',
            "@extends('layouts.app')\n\n@section('title', 'Page Title')\n\n@section('content')",
            $content
        );
        
        // Chuyển đổi từ </x-app-layout> sang @endsection
        $content = str_replace('</x-app-layout>', '@endsection', $content);
        
        $modified = true;
    }
    
    // Kiểm tra xem file có sử dụng <x-guest-layout> không
    if (strpos($content, '<x-guest-layout>') !== false) {
        // Chuyển đổi từ <x-guest-layout> sang @extends('layouts.guest')
        $content = preg_replace(
            '/<x-guest-layout>\s*(?:<x-auth-session-status class="mb-4" :status="session\(\'status\'\)" \/>)?/s',
            "@extends('layouts.guest')\n\n@section('title', 'Page Title')\n\n@section('content')\n    @if (session('status'))\n        <div class=\"alert alert-success mb-4\">\n            {{ session('status') }}\n        </div>\n    @endif",
            $content
        );
        
        // Chuyển đổi từ </x-guest-layout> sang @endsection
        $content = str_replace('</x-guest-layout>', '@endsection', $content);
        
        $modified = true;
    }
    
    // Chuyển đổi các component phổ biến
    $componentReplacements = [
        // Input label
        '/<x-input-label for="([^"]*)" :value="([^"]*)" \/>/i' => 
            '<label for="$1" class="form-label">{{ $2 }}</label>',
        
        // Text input
        '/<x-text-input id="([^"]*)" type="([^"]*)" name="([^"]*)" :value="([^"]*)"([^>]*)\/>/i' => 
            '<input id="$1" type="$2" name="$3" value="{{ $4 }}" class="form-control @error(\'$3\') is-invalid @enderror"$5>',
        
        // Input error
        '/<x-input-error :messages="\$errors->get\(\'([^\']*)\'\)" \/>/i' => 
            '@error(\'$1\')<div class="invalid-feedback">{{ $message }}</div>@enderror',
        
        // Primary button
        '/<x-primary-button>\s*(.*?)\s*<\/x-primary-button>/s' => 
            '<button type="submit" class="btn btn-primary">$1</button>',
        
        // Secondary button
        '/<x-secondary-button>\s*(.*?)\s*<\/x-secondary-button>/s' => 
            '<button type="button" class="btn btn-secondary">$1</button>',
        
        // Danger button
        '/<x-danger-button>\s*(.*?)\s*<\/x-danger-button>/s' => 
            '<button type="button" class="btn btn-danger">$1</button>',
    ];
    
    foreach ($componentReplacements as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $content = $newContent;
            $modified = true;
        }
    }
    
    // Lưu file nếu có thay đổi
    if ($modified) {
        file_put_contents($file, $content);
        $convertedCount++;
        echo "Converted: $file\n";
    }
}

echo "Conversion completed. Converted $convertedCount files.\n";
