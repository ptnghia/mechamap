<?php
/**
 * Manual Assets Check - Kiểm tra thủ công các thư viện lớn
 */

echo "=== KIỂM TRA THỦ CÔNG CÁC THƯ VIỆN ASSETS ===\n\n";

// Danh sách các thư viện cần kiểm tra
$suspiciousLibraries = [
    'tinymce' => 'Text editor - có thể thay bằng CKEditor',
    'leaflet' => 'Maps library - chỉ cần nếu có maps',
    'echarts' => 'Charts library - trùng với ApexCharts',
    'swiper' => 'Slider - có thể dùng Bootstrap carousel',
    '@fullcalendar' => 'Calendar component',
    'sweetalert2' => 'Modal alerts - có thể dùng native',
    'alertifyjs' => 'Notifications - trùng với toastr',
    'choices.js' => 'Select enhancement - trùng với select2',
    'masonry-layout' => 'Grid layout - có thể dùng CSS Grid',
    'jquery-validation' => 'Form validation - có pristine',
    'dropzone' => 'File upload component',
    'glightbox' => 'Image gallery - có fancybox',
    'pace-js' => 'Loading progress',
    'nouislider' => 'Range slider',
    'twitter-bootstrap-wizard' => 'Multi-step forms'
];

$libsPath = 'public/assets/libs';
$totalSuspiciousSize = 0;
$foundLibraries = [];

echo "🔍 KIỂM TRA CÁC THƯ VIỆN ĐÁNG NGHI:\n\n";

foreach ($suspiciousLibraries as $lib => $description) {
    $libPath = $libsPath . '/' . $lib;
    
    if (is_dir($libPath)) {
        $size = getDirSize($libPath);
        $totalSuspiciousSize += $size;
        $foundLibraries[$lib] = $size;
        
        $sizeMB = number_format($size / 1024 / 1024, 2);
        echo "❌ $lib: {$sizeMB} MB - $description\n";
        
        // Kiểm tra có được sử dụng không
        $usage = checkLibraryUsage($lib);
        if (empty($usage)) {
            echo "   → KHÔNG tìm thấy usage trong code\n";
        } else {
            echo "   → Tìm thấy " . count($usage) . " references\n";
        }
        echo "\n";
    }
}

// Kiểm tra CSS không dùng
echo "🎨 KIỂM TRA CSS FILES:\n\n";
$cssPath = 'public/assets/css';
$suspiciousCSS = [
    'preloader.css' => 'Preloader styles',
    'preloader.min.css' => 'Preloader styles (minified)',
    'preloader.rtl.css' => 'Preloader RTL styles',
    'realtime.css' => 'Realtime styles',
    'app.rtl.css' => 'RTL styles',
    'bootstrap.rtl.css' => 'Bootstrap RTL',
    'icons.rtl.css' => 'Icons RTL'
];

$totalCSSSize = 0;
foreach ($suspiciousCSS as $css => $description) {
    $cssFile = $cssPath . '/' . $css;
    if (file_exists($cssFile)) {
        $size = filesize($cssFile);
        $totalCSSSize += $size;
        
        $sizeKB = number_format($size / 1024, 1);
        echo "❌ $css: {$sizeKB} KB - $description\n";
        
        $usage = checkFileUsage($css);
        if (empty($usage)) {
            echo "   → KHÔNG được sử dụng\n";
        } else {
            echo "   → Được reference " . count($usage) . " lần\n";
        }
        echo "\n";
    }
}

// Kiểm tra fonts không dùng
echo "🔤 KIỂM TRA FONTS:\n\n";
$fontsPath = 'public/assets/fonts';
$suspiciousFonts = ['boxicons', 'dripicons'];

$totalFontsSize = 0;
foreach ($suspiciousFonts as $fontPrefix) {
    $fontFiles = glob($fontsPath . '/' . $fontPrefix . '*');
    
    if (!empty($fontFiles)) {
        $fontSize = 0;
        foreach ($fontFiles as $fontFile) {
            $fontSize += filesize($fontFile);
        }
        
        $totalFontsSize += $fontSize;
        $sizeMB = number_format($fontSize / 1024 / 1024, 2);
        echo "❌ $fontPrefix fonts: {$sizeMB} MB (" . count($fontFiles) . " files)\n";
        
        $usage = checkFontUsage($fontPrefix);
        if (empty($usage)) {
            echo "   → KHÔNG được sử dụng (chỉ dùng FontAwesome)\n";
        } else {
            echo "   → Được sử dụng\n";
        }
        echo "\n";
    }
}

// Tổng kết
echo "📊 TỔNG KẾT:\n";
echo "Thư viện đáng nghi: " . number_format($totalSuspiciousSize / 1024 / 1024, 2) . " MB\n";
echo "CSS không dùng: " . number_format($totalCSSSize / 1024, 1) . " KB\n";
echo "Fonts không dùng: " . number_format($totalFontsSize / 1024 / 1024, 2) . " MB\n";
echo "Tổng có thể tiết kiệm: " . number_format(($totalSuspiciousSize + $totalCSSSize + $totalFontsSize) / 1024 / 1024, 2) . " MB\n\n";

// Tạo script xóa
echo "🗑️ SCRIPT XÓA AN TOÀN:\n\n";
echo "#!/bin/bash\n";
echo "# MechaMap Assets Cleanup Script\n";
echo "# Tạo backup trước\n";
echo "echo 'Tạo backup...'\n";
echo "cp -r public/assets public/assets_backup_" . date('Y-m-d_H-i-s') . "\n\n";

echo "# Xóa thư viện không dùng\n";
foreach ($foundLibraries as $lib => $size) {
    if ($size > 500000) { // > 500KB
        echo "echo 'Xóa $lib...'\n";
        echo "rm -rf public/assets/libs/$lib/\n";
    }
}

echo "\n# Xóa CSS không dùng\n";
foreach ($suspiciousCSS as $css => $description) {
    if (file_exists($cssPath . '/' . $css)) {
        echo "rm public/assets/css/$css\n";
    }
}

echo "\n# Xóa fonts không dùng (nếu chỉ dùng FontAwesome)\n";
foreach ($suspiciousFonts as $fontPrefix) {
    $fontFiles = glob($fontsPath . '/' . $fontPrefix . '*');
    if (!empty($fontFiles)) {
        echo "rm public/assets/fonts/{$fontPrefix}*\n";
    }
}

echo "\necho 'Cleanup hoàn thành!'\n";
echo "echo 'Kiểm tra website để đảm bảo mọi thứ hoạt động bình thường'\n";

// Helper functions
function getDirSize($dir) {
    $size = 0;
    
    if (!is_dir($dir)) return 0;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    
    return $size;
}

function checkLibraryUsage($libName) {
    $usage = [];
    
    // Tìm trong views
    $viewsPath = 'resources/views';
    if (is_dir($viewsPath)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                if (strpos($content, $libName) !== false) {
                    $usage[] = $file->getPathname();
                }
            }
        }
    }
    
    return $usage;
}

function checkFileUsage($filename) {
    $usage = [];
    
    // Tìm trong views
    $viewsPath = 'resources/views';
    if (is_dir($viewsPath)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                if (strpos($content, $filename) !== false) {
                    $usage[] = $file->getPathname();
                }
            }
        }
    }
    
    // Tìm trong service workers
    $swFiles = ['public/admin-sw.js', 'public/sw.js'];
    foreach ($swFiles as $swFile) {
        if (file_exists($swFile)) {
            $content = file_get_contents($swFile);
            if (strpos($content, $filename) !== false) {
                $usage[] = $swFile;
            }
        }
    }
    
    return $usage;
}

function checkFontUsage($fontPrefix) {
    $usage = [];
    
    // Tìm trong CSS files
    $cssFiles = glob('public/assets/css/*.css');
    foreach ($cssFiles as $cssFile) {
        $content = file_get_contents($cssFile);
        if (strpos($content, $fontPrefix) !== false) {
            $usage[] = $cssFile;
        }
    }
    
    // Tìm trong views
    $viewsPath = 'resources/views';
    if (is_dir($viewsPath)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Tìm class names
                if (preg_match('/class=["\'][^"\']*' . $fontPrefix . '[^"\']*["\']/', $content)) {
                    $usage[] = $file->getPathname();
                }
            }
        }
    }
    
    return $usage;
}
