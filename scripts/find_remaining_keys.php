<?php
/**
 * Find Remaining Translation Keys Script
 * Tìm các translation keys còn lại sau khi conversion
 */

echo "🔍 FINDING REMAINING TRANSLATION KEYS\n";
echo "=====================================\n\n";

$basePath = __DIR__ . '/../';
$adminViewsPath = $basePath . 'resources/views/admin/';

// Function để tìm tất cả file Blade trong admin
function findAdminBladeFiles($directory) {
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && 
            $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.') !== false) {
            
            $bladeFiles[] = $file->getPathname();
        }
    }

    return $bladeFiles;
}

// Function để tìm translation keys trong file
function findTranslationKeys($filePath) {
    $content = file_get_contents($filePath);
    $keys = [];
    
    // Patterns để tìm translation keys
    $patterns = [
        '/__\([\'"]([^\'"]+)[\'"]\)/',
        '/@lang\([\'"]([^\'"]+)[\'"]\)/',
        '/trans\([\'"]([^\'"]+)[\'"]\)/',
        '/trans_choice\([\'"]([^\'"]+)[\'"]\)/',
    ];
    
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                if (!empty($key)) {
                    $keys[] = $key;
                }
            }
        }
    }
    
    return array_unique($keys);
}

// Tìm tất cả file admin
$adminFiles = findAdminBladeFiles($adminViewsPath);
echo "📁 Tìm thấy " . count($adminFiles) . " file Blade trong admin panel\n\n";

// Thu thập tất cả keys còn lại
$remainingKeys = [];
$fileCount = 0;

foreach ($adminFiles as $filePath) {
    $relativePath = str_replace($adminViewsPath, '', $filePath);
    $keys = findTranslationKeys($filePath);
    
    if (!empty($keys)) {
        $fileCount++;
        echo "📄 {$relativePath}: " . count($keys) . " keys\n";
        
        foreach ($keys as $key) {
            if (!isset($remainingKeys[$key])) {
                $remainingKeys[$key] = [];
            }
            $remainingKeys[$key][] = $relativePath;
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 TỔNG KẾT\n";
echo str_repeat("=", 50) . "\n";
echo "📁 Files có translation keys: {$fileCount}\n";
echo "🔑 Tổng số keys còn lại: " . count($remainingKeys) . "\n\n";

// Sắp xếp keys theo tần suất sử dụng
$keyUsage = [];
foreach ($remainingKeys as $key => $files) {
    $keyUsage[$key] = count($files);
}
arsort($keyUsage);

echo "📋 TOP 50 KEYS CÒN LẠI (theo tần suất):\n";
echo str_repeat("-", 50) . "\n";
$count = 0;
foreach ($keyUsage as $key => $usage) {
    if ($count >= 50) break;
    echo sprintf("%-40s %2d files\n", $key, $usage);
    $count++;
}

// Tạo mapping suggestions
echo "\n📝 MAPPING SUGGESTIONS:\n";
echo str_repeat("-", 50) . "\n";
$count = 0;
foreach ($keyUsage as $key => $usage) {
    if ($count >= 30) break;
    
    // Tự động suggest mapping dựa trên key
    $suggestion = $key; // Default: giữ nguyên
    
    // Một số rules đơn giản
    if (strpos($key, 'Tổng') === 0) {
        $suggestion = $key; // Giữ nguyên các key bắt đầu bằng "Tổng"
    } elseif (strpos($key, 'Số') === 0) {
        $suggestion = $key; // Giữ nguyên các key bắt đầu bằng "Số"
    } elseif (strpos($key, 'Không') === 0) {
        $suggestion = $key; // Giữ nguyên các key bắt đầu bằng "Không"
    }
    
    echo "'{$key}' => '{$suggestion}',\n";
    $count++;
}

// Lưu kết quả vào file
$outputFile = $basePath . 'storage/remaining_translation_keys.json';
$output = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [
        'files_with_keys' => $fileCount,
        'total_remaining_keys' => count($remainingKeys)
    ],
    'keys' => $remainingKeys,
    'key_usage' => $keyUsage
];

file_put_contents($outputFile, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n💾 Kết quả đã được lưu vào: {$outputFile}\n";

echo "\n✅ Scan hoàn thành!\n";
