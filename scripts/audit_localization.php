<?php
/**
 * MechaMap Localization Audit Script
 * Tìm và liệt kê tất cả text hard-coded trong frontend views
 */

require_once __DIR__ . '/../vendor/autoload.php';

echo "🔍 MECHAMAP LOCALIZATION AUDIT\n";
echo "==============================\n";
echo "Tìm kiếm text hard-coded trong frontend views...\n\n";

// Danh sách thư mục cần kiểm tra
$directories = [
    'resources/views',
    'public/js'
];

// Danh sách file extensions cần kiểm tra
$extensions = ['php', 'blade.php', 'js'];

// Patterns để tìm text hard-coded
$patterns = [
    // Text tiếng Việt trong quotes
    '/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ][^"\']*)["\']/',

    // Text tiếng Anh thường gặp trong UI
    '/["\']([A-Z][a-z]+(?: [A-Z][a-z]+)*)["\']/',

    // Các từ khóa UI phổ biến
    '/["\'](?:Home|About|Contact|Login|Register|Search|Profile|Settings|Dashboard|Forum|Thread|Post|Comment|Reply|Edit|Delete|Save|Cancel|Submit|Back|Next|Previous|Load More|Show More)["\']/',

    // Text trong HTML content
    '/>([^<>]*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐ][^<>]*)</i',
];

$hardcodedTexts = [];
$fileCount = 0;
$totalIssues = 0;

/**
 * Quét file để tìm text hard-coded
 */
function scanFile($filePath, $patterns) {
    global $hardcodedTexts, $totalIssues;

    $content = file_get_contents($filePath);
    $issues = [];
    $lines = explode("\n", $content);

    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            // Kiểm tra xem có capture group không
            if (isset($matches[1]) && is_array($matches[1])) {
                foreach ($matches[1] as $match) {
                    $text = $match[0];
                    $offset = $match[1];

                    // Tìm số dòng
                    $lineNumber = substr_count(substr($content, 0, $offset), "\n") + 1;

                    // Bỏ qua một số trường hợp đặc biệt
                    if (shouldIgnoreText($text, $filePath)) {
                        continue;
                    }

                    $issues[] = [
                        'text' => $text,
                        'line' => $lineNumber,
                        'context' => trim($lines[$lineNumber - 1] ?? '')
                    ];
                    $totalIssues++;
                }
            }
        }
    }

    if (!empty($issues)) {
        $hardcodedTexts[$filePath] = $issues;
    }
}

/**
 * Kiểm tra xem có nên bỏ qua text này không
 */
function shouldIgnoreText($text, $filePath) {
    // Bỏ qua text quá ngắn
    if (strlen($text) < 3) return true;

    // Bỏ qua các từ khóa kỹ thuật
    $technicalWords = [
        'UTF-8', 'HTTP', 'HTTPS', 'JSON', 'XML', 'CSS', 'JS', 'PHP', 'SQL',
        'API', 'URL', 'URI', 'ID', 'UUID', 'CSRF', 'XSS', 'AJAX',
        'Bootstrap', 'jQuery', 'Laravel', 'Blade', 'Eloquent',
        'fa-solid', 'fa-regular', 'btn-primary', 'text-center',
        'container', 'row', 'col', 'navbar', 'modal', 'dropdown'
    ];

    if (in_array($text, $technicalWords)) return true;

    // Bỏ qua các pattern đặc biệt
    if (preg_match('/^[A-Z_]+$/', $text)) return true; // Constants
    if (preg_match('/^\d+$/', $text)) return true; // Numbers only
    if (preg_match('/^[a-z-]+$/', $text)) return true; // CSS classes
    if (preg_match('/\.(jpg|jpeg|png|gif|svg|css|js)$/i', $text)) return true; // File extensions

    // Bỏ qua nếu đã có __() hoặc @lang()
    if (strpos($text, '__') !== false || strpos($text, '@lang') !== false) return true;

    return false;
}

/**
 * Quét thư mục đệ quy
 */
function scanDirectory($dir, $patterns, $extensions) {
    global $fileCount;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $extension = $file->getExtension();
            $fileName = $file->getFilename();

            // Kiểm tra extension
            $shouldScan = false;
            foreach ($extensions as $ext) {
                if ($extension === $ext || str_ends_with($fileName, '.' . $ext)) {
                    $shouldScan = true;
                    break;
                }
            }

            if ($shouldScan) {
                $fileCount++;
                scanFile($file->getPathname(), $patterns);

                if ($fileCount % 10 === 0) {
                    echo "Đã quét {$fileCount} files...\n";
                }
            }
        }
    }
}

// Bắt đầu quét
$startTime = microtime(true);

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "📁 Quét thư mục: {$dir}\n";
        scanDirectory($dir, $patterns, $extensions);
    }
}

$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 KẾT QUẢ AUDIT\n";
echo str_repeat("=", 50) . "\n";
echo "⏱️  Thời gian: {$duration} giây\n";
echo "📁 Số file đã quét: {$fileCount}\n";
echo "🔍 Tổng số vấn đề: {$totalIssues}\n";
echo "📄 File có vấn đề: " . count($hardcodedTexts) . "\n\n";

// Hiển thị kết quả chi tiết
if (!empty($hardcodedTexts)) {
    echo "📋 CHI TIẾT CÁC VẤN ĐỀ:\n";
    echo str_repeat("-", 50) . "\n";

    foreach ($hardcodedTexts as $file => $issues) {
        echo "\n📄 {$file} (" . count($issues) . " vấn đề):\n";

        foreach (array_slice($issues, 0, 5) as $issue) { // Chỉ hiển thị 5 vấn đề đầu
            echo "   Dòng {$issue['line']}: \"{$issue['text']}\"\n";
            echo "   Context: " . substr($issue['context'], 0, 80) . "...\n";
        }

        if (count($issues) > 5) {
            echo "   ... và " . (count($issues) - 5) . " vấn đề khác\n";
        }
    }
}

echo "\n✅ Audit hoàn thành!\n";
echo "💡 Tiếp theo: Chạy script update_localization.php để cập nhật\n";
