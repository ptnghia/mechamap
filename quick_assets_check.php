<?php
/**
 * Quick Assets Usage Check
 * Kiểm tra nhanh việc sử dụng assets
 */

echo "=== KIỂM TRA NHANH VIỆC SỬ DỤNG ASSETS ===\n\n";

// 1. Lấy danh sách tất cả assets
$assetsPath = 'public/assets';
$allAssets = [];

function scanAssets($dir, $prefix = '') {
    global $allAssets;
    
    if (!is_dir($dir)) return;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $dir . '/' . $file;
        $relativePath = $prefix . '/' . $file;
        
        if (is_dir($fullPath)) {
            scanAssets($fullPath, $relativePath);
        } else {
            $allAssets[] = [
                'path' => ltrim($relativePath, '/'),
                'fullPath' => $fullPath,
                'size' => filesize($fullPath)
            ];
        }
    }
}

scanAssets($assetsPath, 'assets');

echo "Tổng số assets: " . count($allAssets) . "\n";

// 2. Tìm assets được reference trong code
$usedAssets = [];

// Scan views
$viewsPath = 'resources/views';
if (is_dir($viewsPath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsPath)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Tìm asset() calls
            preg_match_all("/asset\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
            foreach ($matches[1] as $match) {
                if (strpos($match, 'assets/') === 0) {
                    $usedAssets[] = $match;
                }
            }
            
            // Tìm direct references
            preg_match_all("/['\"]([^'\"]*assets\/[^'\"]*)['\"]/",$content, $matches);
            foreach ($matches[1] as $match) {
                if (strpos($match, 'assets/') !== false) {
                    $pos = strpos($match, 'assets/');
                    $usedAssets[] = substr($match, $pos);
                }
            }
        }
    }
}

// Scan service worker
$swFiles = ['public/admin-sw.js', 'public/sw.js'];
foreach ($swFiles as $swFile) {
    if (file_exists($swFile)) {
        $content = file_get_contents($swFile);
        preg_match_all("/['\"]([^'\"]*assets\/[^'\"]*)['\"]/",$content, $matches);
        foreach ($matches[1] as $match) {
            if (strpos($match, 'assets/') !== false) {
                $pos = strpos($match, 'assets/');
                $usedAssets[] = substr($match, $pos);
            }
        }
    }
}

$usedAssets = array_unique($usedAssets);
echo "Assets được reference: " . count($usedAssets) . "\n\n";

// 3. Tìm assets không được sử dụng
$unusedAssets = [];
$protectedPatterns = [
    'assets/libs/bootstrap',
    'assets/libs/jquery',
    'assets/css/bootstrap',
    'assets/css/app',
    'assets/css/icons',
    'assets/js/app',
    'assets/libs/apexcharts',
    'assets/libs/datatables',
    'assets/libs/select2',
    'assets/fonts/fa-',
    'assets/images/favicon',
    'assets/images/logo'
];

foreach ($allAssets as $asset) {
    $assetPath = $asset['path'];
    
    // Kiểm tra protected
    $isProtected = false;
    foreach ($protectedPatterns as $pattern) {
        if (strpos($assetPath, $pattern) !== false) {
            $isProtected = true;
            break;
        }
    }
    
    if ($isProtected) continue;
    
    // Kiểm tra có được sử dụng không
    $isUsed = false;
    foreach ($usedAssets as $used) {
        if (strpos($assetPath, str_replace('assets/', '', $used)) !== false ||
            strpos($used, $assetPath) !== false) {
            $isUsed = true;
            break;
        }
    }
    
    if (!$isUsed) {
        $unusedAssets[] = $asset;
    }
}

echo "Assets không sử dụng: " . count($unusedAssets) . "\n";

// 4. Phân loại theo thư viện
$libraryStats = [];
foreach ($unusedAssets as $asset) {
    if (strpos($asset['path'], 'assets/libs/') === 0) {
        $parts = explode('/', $asset['path']);
        $lib = $parts[2] ?? 'unknown';
        
        if (!isset($libraryStats[$lib])) {
            $libraryStats[$lib] = ['count' => 0, 'size' => 0];
        }
        
        $libraryStats[$lib]['count']++;
        $libraryStats[$lib]['size'] += $asset['size'];
    }
}

// Sắp xếp theo size
uasort($libraryStats, function($a, $b) {
    return $b['size'] - $a['size'];
});

echo "\n=== THƯ VIỆN KHÔNG SỬ DỤNG (TOP 10) ===\n";
$count = 0;
foreach ($libraryStats as $lib => $stats) {
    if ($count >= 10) break;
    
    $sizeMB = number_format($stats['size'] / 1024 / 1024, 2);
    echo sprintf("%-20s: %3d files (%s MB)\n", $lib, $stats['count'], $sizeMB);
    $count++;
}

// 5. Tính tổng tiết kiệm
$totalUnusedSize = array_sum(array_column($unusedAssets, 'size'));
$totalSize = array_sum(array_column($allAssets, 'size'));

echo "\n=== TỔNG KẾT ===\n";
echo "Tổng dung lượng assets: " . number_format($totalSize / 1024 / 1024, 2) . " MB\n";
echo "Dung lượng không sử dụng: " . number_format($totalUnusedSize / 1024 / 1024, 2) . " MB\n";
echo "Tỷ lệ có thể tiết kiệm: " . number_format(($totalUnusedSize / $totalSize) * 100, 1) . "%\n";

// 6. Danh sách một số file lớn không dùng
echo "\n=== MỘT SỐ FILE LỚN KHÔNG SỬ DỤNG ===\n";
usort($unusedAssets, function($a, $b) {
    return $b['size'] - $a['size'];
});

$largeUnused = array_slice($unusedAssets, 0, 20);
foreach ($largeUnused as $asset) {
    if ($asset['size'] > 50000) { // > 50KB
        $sizeKB = number_format($asset['size'] / 1024, 1);
        echo "- {$asset['path']} ({$sizeKB} KB)\n";
    }
}

echo "\n=== KHUYẾN NGHỊ ===\n";
echo "1. Có thể xóa an toàn các thư viện lớn không dùng\n";
echo "2. Backup trước khi xóa\n";
echo "3. Test kỹ sau khi xóa\n";
echo "4. Sử dụng script cleanup_unused_assets.php để xóa tự động\n";

// 7. Tạo danh sách xóa nhanh
echo "\n=== LỆNH XÓA NHANH (CẨN THẬN!) ===\n";
echo "# Backup trước:\n";
echo "cp -r public/assets public/assets_backup_" . date('Y-m-d') . "\n\n";

echo "# Xóa các thư viện lớn không dùng:\n";
$topLibs = array_slice($libraryStats, 0, 5, true);
foreach ($topLibs as $lib => $stats) {
    if ($stats['size'] > 1000000) { // > 1MB
        echo "rm -rf public/assets/libs/$lib/\n";
    }
}

echo "\n# Xóa CSS không dùng:\n";
$cssFiles = ['preloader.css', 'preloader.min.css', 'preloader.rtl.css', 'realtime.css'];
foreach ($cssFiles as $css) {
    if (file_exists("public/assets/css/$css")) {
        echo "rm public/assets/css/$css\n";
    }
}

echo "\n# Xóa fonts không dùng (nếu chỉ dùng FontAwesome):\n";
$fontPrefixes = ['boxicons', 'dripicons'];
foreach ($fontPrefixes as $prefix) {
    echo "rm public/assets/fonts/{$prefix}*\n";
}
