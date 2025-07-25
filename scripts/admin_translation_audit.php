<?php
/**
 * Admin Panel Translation Audit Script
 * Quét và phân tích tất cả translation keys trong admin panel
 */

echo "🔍 ADMIN PANEL TRANSLATION AUDIT\n";
echo "================================\n\n";

$basePath = __DIR__ . '/../';
$adminViewsPath = $basePath . 'resources/views/admin/';

// Kiểm tra thư mục admin có tồn tại không
if (!is_dir($adminViewsPath)) {
    echo "❌ Thư mục admin views không tồn tại: {$adminViewsPath}\n";
    exit(1);
}

// Tìm tất cả file Blade trong admin
function findAdminBladeFiles($directory) {
    $bladeFiles = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() &&
            $file->getExtension() === 'php' &&
            strpos($file->getFilename(), '.blade.') !== false) {

            $relativePath = str_replace($directory, '', $file->getPathname());
            $bladeFiles[] = [
                'path' => $file->getPathname(),
                'relative' => ltrim($relativePath, '/\\'),
                'size' => $file->getSize()
            ];
        }
    }

    return $bladeFiles;
}

// Patterns để tìm translation keys
$translationPatterns = [
    'standard_trans' => [
        'pattern' => '/__\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Standard __() function',
        'keys' => []
    ],
    'blade_lang' => [
        'pattern' => '/@lang\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'Blade @lang directive',
        'keys' => []
    ],
    'trans_function' => [
        'pattern' => '/trans\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 'trans() function',
        'keys' => []
    ],
    'trans_choice' => [
        'pattern' => '/trans_choice\([\'"]([^\'"]+)[\'"]/m',
        'description' => 'trans_choice() function',
        'keys' => []
    ],
    't_admin' => [
        'pattern' => '/t_admin\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => 't_admin() helper function',
        'keys' => []
    ],
    'blade_admin' => [
        'pattern' => '/@admin\([\'"]([^\'"]+)[\'"]\)/m',
        'description' => '@admin blade directive',
        'keys' => []
    ]
];

// Tìm tất cả file admin
$adminFiles = findAdminBladeFiles($adminViewsPath);
echo "📁 Tìm thấy " . count($adminFiles) . " file Blade trong admin panel\n\n";

$totalKeys = 0;
$fileAnalysis = [];
$allKeys = [];

// Phân tích từng file
foreach ($adminFiles as $fileInfo) {
    $content = file_get_contents($fileInfo['path']);
    $fileKeys = [];
    $fileKeyCount = 0;

    echo "📄 Đang phân tích: {$fileInfo['relative']}\n";

    foreach ($translationPatterns as $patternName => $patternInfo) {
        preg_match_all($patternInfo['pattern'], $content, $matches);

        // Debug: In ra matches để kiểm tra
        if (!empty($matches[0])) {
            echo "   🔍 Found " . count($matches[0]) . " matches for {$patternName}\n";
            foreach ($matches[0] as $match) {
                echo "      - {$match}\n";
            }
        }

        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                if (empty($key)) continue;

                $fileKeys[] = [
                    'type' => $patternName,
                    'key' => $key
                ];

                // Thêm vào danh sách tổng
                if (!isset($allKeys[$key])) {
                    $allKeys[$key] = [];
                }
                $allKeys[$key][] = $fileInfo['relative'];

                $fileKeyCount++;
                $totalKeys++;
            }
        }

        // Kiểm tra matches[2] cho pattern có 2 groups
        if (!empty($matches[2])) {
            foreach ($matches[2] as $key) {
                if (empty($key)) continue;

                $fileKeys[] = [
                    'type' => $patternName,
                    'key' => $key
                ];

                if (!isset($allKeys[$key])) {
                    $allKeys[$key] = [];
                }
                $allKeys[$key][] = $fileInfo['relative'];

                $fileKeyCount++;
                $totalKeys++;
            }
        }
    }

    $fileAnalysis[$fileInfo['relative']] = [
        'path' => $fileInfo['path'],
        'size' => $fileInfo['size'],
        'key_count' => $fileKeyCount,
        'keys' => $fileKeys
    ];

    echo "   ✅ {$fileKeyCount} translation keys\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 TỔNG KẾT PHÂN TÍCH\n";
echo str_repeat("=", 50) . "\n";
echo "📁 Tổng số file: " . count($adminFiles) . "\n";
echo "🔑 Tổng số translation keys: {$totalKeys}\n";
echo "🔑 Số key duy nhất: " . count($allKeys) . "\n\n";

// Sắp xếp file theo số lượng keys (nhiều nhất trước)
uasort($fileAnalysis, function($a, $b) {
    return $b['key_count'] <=> $a['key_count'];
});

echo "📋 TOP 10 FILE CÓ NHIỀU TRANSLATION KEYS NHẤT:\n";
echo str_repeat("-", 50) . "\n";
$count = 0;
foreach ($fileAnalysis as $file => $analysis) {
    if ($count >= 10) break;
    echo sprintf("%-40s %3d keys\n", $file, $analysis['key_count']);
    $count++;
}

echo "\n📋 CÁC KEY ĐƯỢC SỬ DỤNG NHIỀU NHẤT:\n";
echo str_repeat("-", 50) . "\n";
$keyUsage = [];
foreach ($allKeys as $key => $files) {
    $keyUsage[$key] = count($files);
}
arsort($keyUsage);

$count = 0;
foreach ($keyUsage as $key => $usage) {
    if ($count >= 15) break;
    echo sprintf("%-40s %2d files\n", $key, $usage);
    $count++;
}

// Lưu kết quả vào file JSON
$outputFile = $basePath . 'storage/admin_translation_audit.json';
$output = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [
        'total_files' => count($adminFiles),
        'total_keys' => $totalKeys,
        'unique_keys' => count($allKeys)
    ],
    'files' => $fileAnalysis,
    'all_keys' => $allKeys,
    'key_usage' => $keyUsage
];

file_put_contents($outputFile, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\n💾 Kết quả đã được lưu vào: {$outputFile}\n";

echo "\n🎯 KHUYẾN NGHỊ THỰC HIỆN:\n";
echo str_repeat("-", 50) . "\n";
echo "1. Bắt đầu với các file có nhiều keys nhất\n";
echo "2. Ưu tiên các file layout (header, sidebar, dashboard)\n";
echo "3. Chuyển đổi từng file một cách có hệ thống\n";
echo "4. Backup trước khi thực hiện thay đổi\n";
echo "5. Test từng file sau khi chuyển đổi\n";

echo "\n✅ Audit hoàn thành!\n";
