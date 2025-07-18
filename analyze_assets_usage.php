<?php
/**
 * Script để phân tích việc sử dụng assets trong MechaMap
 */

echo "=== PHÂN TÍCH SỬ DỤNG ASSETS TRONG MECHAMAP ===\n\n";

// Lấy danh sách tất cả file assets
$assetsPath = 'public/assets';
$allAssets = [];

function scanAssetsDirectory($dir, $basePath = '') {
    global $allAssets;
    
    if (!is_dir($dir)) return;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $dir . '/' . $file;
        $relativePath = $basePath . '/' . $file;
        
        if (is_dir($fullPath)) {
            scanAssetsDirectory($fullPath, $relativePath);
        } else {
            $allAssets[] = ltrim($relativePath, '/');
        }
    }
}

scanAssetsDirectory($assetsPath, 'assets');

echo "Tổng số file assets: " . count($allAssets) . "\n\n";

// Tìm các file được sử dụng
$usedAssets = [];
$viewsPath = 'resources/views';

function findUsedAssets($dir) {
    global $usedAssets;
    
    if (!is_dir($dir)) return;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Tìm các reference đến assets
            preg_match_all('/assets\/[^\'"\s)]+/', $content, $matches);
            
            foreach ($matches[0] as $asset) {
                if (!in_array($asset, $usedAssets)) {
                    $usedAssets[] = $asset;
                }
            }
        }
    }
}

// Tìm trong views
findUsedAssets($viewsPath);

// Tìm trong public files (JS, CSS)
$publicFiles = ['public/admin-sw.js', 'public/sw.js', 'public/manifest.json'];
foreach ($publicFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        preg_match_all('/assets\/[^\'"\s)]+/', $content, $matches);
        
        foreach ($matches[0] as $asset) {
            if (!in_array($asset, $usedAssets)) {
                $usedAssets[] = $asset;
            }
        }
    }
}

echo "Số file assets được sử dụng: " . count($usedAssets) . "\n\n";

// Tìm file không được sử dụng
$unusedAssets = [];
foreach ($allAssets as $asset) {
    $found = false;
    foreach ($usedAssets as $used) {
        if (strpos($used, $asset) !== false || strpos($asset, str_replace('assets/', '', $used)) !== false) {
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $unusedAssets[] = $asset;
    }
}

echo "=== FILE ASSETS ĐƯỢC SỬ DỤNG NHIỀU NHẤT ===\n";
$assetCount = [];
foreach ($usedAssets as $asset) {
    $assetCount[$asset] = ($assetCount[$asset] ?? 0) + 1;
}
arsort($assetCount);

$top10 = array_slice($assetCount, 0, 10, true);
foreach ($top10 as $asset => $count) {
    echo "- $asset (sử dụng $count lần)\n";
}

echo "\n=== CÁC THƯ MỤC ASSETS CHÍNH ===\n";
$directories = [];
foreach ($allAssets as $asset) {
    $parts = explode('/', $asset);
    if (count($parts) >= 2) {
        $dir = $parts[0] . '/' . $parts[1];
        $directories[$dir] = ($directories[$dir] ?? 0) + 1;
    }
}
arsort($directories);

foreach ($directories as $dir => $count) {
    echo "- $dir: $count files\n";
}

echo "\n=== THỐNG KÊ THEO LOẠI FILE ===\n";
$extensions = [];
foreach ($allAssets as $asset) {
    $ext = pathinfo($asset, PATHINFO_EXTENSION);
    $extensions[$ext] = ($extensions[$ext] ?? 0) + 1;
}
arsort($extensions);

foreach ($extensions as $ext => $count) {
    echo "- .$ext: $count files\n";
}

echo "\n=== KẾT LUẬN ===\n";
echo "- Tổng assets: " . count($allAssets) . " files\n";
echo "- Được sử dụng: " . count($usedAssets) . " files\n";
echo "- Không sử dụng: " . count($unusedAssets) . " files\n";
echo "- Tỷ lệ sử dụng: " . round((count($usedAssets) / count($allAssets)) * 100, 2) . "%\n";

if (count($unusedAssets) > 0) {
    echo "\n=== MỘT SỐ FILE KHÔNG ĐƯỢC SỬ DỤNG ===\n";
    $sampleUnused = array_slice($unusedAssets, 0, 20);
    foreach ($sampleUnused as $asset) {
        echo "- $asset\n";
    }
    
    if (count($unusedAssets) > 20) {
        echo "... và " . (count($unusedAssets) - 20) . " files khác\n";
    }
}
