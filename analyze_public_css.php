<?php
/**
 * Phân tích các file CSS trong public/css
 * Tìm file không được sử dụng và có thể xóa
 */

echo "=== PHÂN TÍCH CSS FILES TRONG PUBLIC/CSS ===\n\n";

$cssPath = 'public/css';
$allCssFiles = [];
$usedCssFiles = [];

// 1. Quét tất cả CSS files
function scanCssFiles($dir, $prefix = '') {
    global $allCssFiles;
    
    if (!is_dir($dir)) return;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $dir . '/' . $file;
        $relativePath = $prefix . '/' . $file;
        
        if (is_dir($fullPath)) {
            scanCssFiles($fullPath, $relativePath);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'css') {
            $allCssFiles[] = [
                'path' => ltrim($relativePath, '/'),
                'fullPath' => $fullPath,
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath)
            ];
        }
    }
}

scanCssFiles($cssPath, 'css');

echo "Tổng số CSS files: " . count($allCssFiles) . "\n\n";

// 2. Tìm CSS được sử dụng trong views
function findUsedCss() {
    global $usedCssFiles;
    
    $searchPaths = [
        'resources/views/',
        'public/js/',
        'resources/js/'
    ];
    
    foreach ($searchPaths as $searchPath) {
        if (is_dir($searchPath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($searchPath)
            );
            
            foreach ($iterator as $file) {
                if (in_array($file->getExtension(), ['php', 'js', 'blade'])) {
                    $content = file_get_contents($file->getPathname());
                    
                    // Tìm các pattern CSS references
                    $patterns = [
                        '/asset\([\'"]css\/([^\'")]+)[\'"]\)/',  // asset('css/file.css')
                        '/[\'"]css\/([^\'")]+\.css)[\'"]/',      // 'css/file.css'
                        '/href=[\'"]css\/([^\'")]+\.css)[\'"]/', // href="css/file.css"
                        '/url\([\'"]?css\/([^\'")]+\.css)[\'"]?\)/' // url(css/file.css)
                    ];
                    
                    foreach ($patterns as $pattern) {
                        preg_match_all($pattern, $content, $matches);
                        foreach ($matches[1] as $match) {
                            if (!in_array($match, $usedCssFiles)) {
                                $usedCssFiles[] = $match;
                            }
                        }
                    }
                }
            }
        }
    }
}

findUsedCss();

echo "CSS files được reference: " . count($usedCssFiles) . "\n\n";

// 3. Phân loại CSS files
$categories = [
    'admin' => [],
    'frontend' => [],
    'backup' => [],
    'root' => [],
    'unused' => []
];

$suspiciousFiles = [];
$backupFiles = [];
$duplicateFiles = [];

foreach ($allCssFiles as $css) {
    $path = $css['path'];
    
    // Phân loại theo thư mục
    if (strpos($path, 'css/admin/') === 0) {
        $categories['admin'][] = $css;
    } elseif (strpos($path, 'css/frontend/') === 0) {
        $categories['frontend'][] = $css;
    } elseif (strpos($path, 'backup') !== false) {
        $categories['backup'][] = $css;
        $backupFiles[] = $css;
    } else {
        $categories['root'][] = $css;
    }
    
    // Tìm file backup
    if (strpos($path, 'backup') !== false || strpos($path, '_backup_') !== false) {
        $backupFiles[] = $css;
    }
    
    // Tìm file duplicate
    $filename = basename($path);
    if (strpos($path, 'css/admin/') === 0 && file_exists('public/css/' . $filename)) {
        $duplicateFiles[] = $css;
    }
    
    // Kiểm tra có được sử dụng không
    $isUsed = false;
    $relativeCssPath = str_replace('css/', '', $path);
    
    foreach ($usedCssFiles as $used) {
        if (strpos($used, $relativeCssPath) !== false || 
            strpos($relativeCssPath, $used) !== false ||
            basename($used) === basename($relativeCssPath)) {
            $isUsed = true;
            break;
        }
    }
    
    if (!$isUsed) {
        $categories['unused'][] = $css;
    }
}

// 4. Hiển thị báo cáo
echo "=== PHÂN LOẠI CSS FILES ===\n";
foreach ($categories as $category => $files) {
    if (!empty($files)) {
        $totalSize = array_sum(array_column($files, 'size'));
        echo sprintf("%-12s: %2d files (%s)\n", 
            strtoupper($category), 
            count($files), 
            formatBytes($totalSize)
        );
    }
}

echo "\n=== FILE BACKUP CÓ THỂ XÓA ===\n";
if (!empty($backupFiles)) {
    $backupSize = 0;
    foreach ($backupFiles as $backup) {
        $backupSize += $backup['size'];
        echo "❌ {$backup['path']} (" . formatBytes($backup['size']) . ")\n";
    }
    echo "Tổng backup size: " . formatBytes($backupSize) . "\n";
} else {
    echo "✅ Không có file backup\n";
}

echo "\n=== FILE DUPLICATE CÓ THỂ XÓA ===\n";
if (!empty($duplicateFiles)) {
    foreach ($duplicateFiles as $dup) {
        echo "❌ {$dup['path']} (" . formatBytes($dup['size']) . ") - Trùng với file root\n";
    }
} else {
    echo "✅ Không có file duplicate rõ ràng\n";
}

echo "\n=== CSS FILES KHÔNG ĐƯỢC SỬ DỤNG ===\n";
$unusedSize = 0;
$safeToDelete = [];

foreach ($categories['unused'] as $unused) {
    $unusedSize += $unused['size'];
    
    // Kiểm tra file có an toàn để xóa không
    $path = $unused['path'];
    $isSafe = false;
    
    // File backup - an toàn xóa
    if (strpos($path, 'backup') !== false) {
        $isSafe = true;
        $safeToDelete[] = $unused;
    }
    
    // File cũ (> 6 tháng) và nhỏ (< 10KB)
    if ($unused['size'] < 10240 && (time() - $unused['modified']) > (6 * 30 * 24 * 3600)) {
        $isSafe = true;
    }
    
    $status = $isSafe ? "🟢 AN TOÀN" : "🟡 CẨN THẬN";
    echo "$status {$unused['path']} (" . formatBytes($unused['size']) . ")\n";
}

echo "\nTổng unused size: " . formatBytes($unusedSize) . "\n";

// 5. Tìm CSS files lớn
echo "\n=== CSS FILES LỚN NHẤT ===\n";
usort($allCssFiles, function($a, $b) {
    return $b['size'] - $a['size'];
});

$largeCss = array_slice($allCssFiles, 0, 10);
foreach ($largeCss as $css) {
    if ($css['size'] > 5120) { // > 5KB
        echo "📦 {$css['path']} (" . formatBytes($css['size']) . ")\n";
    }
}

// 6. Khuyến nghị
echo "\n=== KHUYẾN NGHỊ ===\n";

$totalSavings = 0;

if (!empty($backupFiles)) {
    $backupSavings = array_sum(array_column($backupFiles, 'size'));
    $totalSavings += $backupSavings;
    echo "1. 🗑️  XÓA BACKUP FILES: " . formatBytes($backupSavings) . "\n";
    echo "   - Các thư mục/file backup cũ không cần thiết\n";
}

if (!empty($safeToDelete)) {
    $safeSavings = array_sum(array_column($safeToDelete, 'size'));
    $totalSavings += $safeSavings;
    echo "2. 🗑️  XÓA FILES AN TOÀN: " . formatBytes($safeSavings) . "\n";
    echo "   - File backup và file cũ không sử dụng\n";
}

if (!empty($duplicateFiles)) {
    $dupSavings = array_sum(array_column($duplicateFiles, 'size'));
    echo "3. 🔍 KIỂM TRA DUPLICATE: " . formatBytes($dupSavings) . "\n";
    echo "   - So sánh nội dung trước khi xóa\n";
}

echo "\nTổng có thể tiết kiệm: " . formatBytes($totalSavings) . "\n";

// 7. Tạo script cleanup
if ($totalSavings > 0) {
    echo "\n=== SCRIPT CLEANUP ===\n";
    echo "#!/bin/bash\n";
    echo "# Backup trước khi xóa\n";
    echo "cp -r public/css public/css_backup_" . date('Y-m-d') . "\n\n";
    
    echo "# Xóa backup folders\n";
    foreach ($backupFiles as $backup) {
        if (is_dir($backup['fullPath'])) {
            echo "rm -rf \"{$backup['fullPath']}\"\n";
        } else {
            echo "rm \"{$backup['fullPath']}\"\n";
        }
    }
    
    echo "\necho 'CSS cleanup hoàn thành!'\n";
}

// Helper function
function formatBytes($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
