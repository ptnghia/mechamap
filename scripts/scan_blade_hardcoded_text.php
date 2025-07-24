<?php

/**
 * Blade Files Hardcoded Text Scanner
 * Scans only .blade.php files for remaining hardcoded Vietnamese text
 * Excludes /admin directory and already converted priority files
 */

class BladeHardcodedScanner
{
    private $basePath;
    private $results = [];
    private $stats = [
        'total_files_scanned' => 0,
        'files_with_hardcoded' => 0,
        'total_hardcoded_strings' => 0
    ];

    // Already converted priority files
    private $excludedFiles = [
        'threads/partials/showcase.blade.php',
        'threads/create.blade.php',
        'showcase/show.blade.php',
        'devices/index.blade.php',
        'layouts/app.blade.php'
    ];

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__);
    }

    public function scan()
    {
        echo "🔍 Scanning Blade files for hardcoded Vietnamese text...\n";
        echo "📁 Base path: {$this->basePath}\n";
        echo "🚫 Excluding: /admin directory and converted files\n\n";

        $this->scanBladeFiles();
        $this->generateReport();
    }

    private function scanBladeFiles()
    {
        $viewsPath = $this->basePath . '/resources/views';
        
        if (!is_dir($viewsPath)) {
            echo "❌ Views directory not found: {$viewsPath}\n";
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($viewsPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filename = $file->getFilename();
                
                // Only process .blade.php files
                if (strpos($filename, '.blade.php') === false) {
                    continue;
                }

                $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);

                // Skip admin directory
                if (strpos($relativePath, 'admin/') === 0) {
                    continue;
                }

                // Skip already converted files
                if (in_array($relativePath, $this->excludedFiles)) {
                    continue;
                }

                $this->scanFile($file->getPathname(), $relativePath);
            }
        }
    }

    private function scanFile($filePath, $relativePath)
    {
        $this->stats['total_files_scanned']++;
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            return;
        }

        $lines = explode("\n", $content);
        $hardcodedStrings = [];

        foreach ($lines as $lineNumber => $line) {
            // Skip comments and already translated lines
            if ($this->isSkippableLine($line)) {
                continue;
            }

            // Look for Vietnamese text patterns
            $matches = $this->findVietnameseText($line);
            
            foreach ($matches as $match) {
                $hardcodedStrings[] = [
                    'line' => $lineNumber + 1,
                    'text' => $match,
                    'context' => trim($line)
                ];
                $this->stats['total_hardcoded_strings']++;
            }
        }

        if (!empty($hardcodedStrings)) {
            $this->results[$relativePath] = $hardcodedStrings;
            $this->stats['files_with_hardcoded']++;
        }
    }

    private function isSkippableLine($line)
    {
        $trimmed = trim($line);
        
        // Skip comments
        if (strpos($trimmed, '{{--') !== false || 
            strpos($trimmed, '//') === 0 || 
            strpos($trimmed, '#') === 0) {
            return true;
        }
        
        // Skip lines with translation calls
        if (strpos($trimmed, '__(') !== false || 
            strpos($trimmed, 'trans(') !== false || 
            strpos($trimmed, '@lang(') !== false ||
            strpos($trimmed, '@t(') !== false) {
            return true;
        }
        
        // Skip empty lines
        if (empty($trimmed)) {
            return true;
        }
        
        return false;
    }

    private function findVietnameseText($line)
    {
        $matches = [];
        
        // Pattern 1: Vietnamese characters in quotes
        if (preg_match_all('/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/', $line, $vietnameseMatches)) {
            foreach ($vietnameseMatches[1] as $match) {
                if (strlen(trim($match)) > 2) {
                    $matches[] = trim($match);
                }
            }
        }
        
        // Pattern 2: Common Vietnamese words/phrases
        $vietnameseWords = [
            'Trang chủ', 'Đăng nhập', 'Đăng ký', 'Đăng xuất', 'Quên mật khẩu',
            'Tài khoản', 'Người dùng', 'Quản lý', 'Cài đặt', 'Tìm kiếm',
            'Kết quả', 'Danh sách', 'Chi tiết', 'Thông tin', 'Liên hệ',
            'Hỗ trợ', 'Giúp đỡ', 'Hướng dẫn', 'Báo cáo', 'Thống kê',
            'Vui lòng', 'Xin chào', 'Cảm ơn', 'Xin lỗi', 'Không có',
            'Có lỗi', 'Thành công', 'Thất bại', 'Xác nhận', 'Hủy bỏ',
            'Lưu', 'Xóa', 'Sửa', 'Thêm', 'Cập nhật', 'Tải xuống',
            'Tải lên', 'Chia sẻ', 'Bình luận', 'Đánh giá', 'Theo dõi',
            'Yêu thích', 'Bookmark', 'Phân loại', 'Danh mục', 'Thẻ tag',
            'Bài viết', 'Tin tức', 'Sự kiện', 'Thông báo', 'Tin nhắn'
        ];
        
        foreach ($vietnameseWords as $word) {
            if (preg_match_all('/["\']([^"\']*' . preg_quote($word, '/') . '[^"\']*)["\']/', $line, $wordMatches)) {
                foreach ($wordMatches[1] as $match) {
                    if (strlen(trim($match)) > 2) {
                        $matches[] = trim($match);
                    }
                }
            }
        }
        
        return array_unique($matches);
    }

    private function generateReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 BLADE FILES HARDCODED TEXT SCAN RESULTS\n";
        echo str_repeat("=", 80) . "\n\n";

        // Statistics
        echo "📈 SCAN STATISTICS:\n";
        echo "├─ Total Blade files scanned: {$this->stats['total_files_scanned']}\n";
        echo "├─ Files with hardcoded text: {$this->stats['files_with_hardcoded']}\n";
        echo "└─ Total hardcoded strings: {$this->stats['total_hardcoded_strings']}\n\n";

        if (empty($this->results)) {
            echo "🎉 No hardcoded Vietnamese text found in Blade files!\n";
            return;
        }

        // Top files with most hardcoded text
        echo "🔥 FILES WITH HARDCODED TEXT (sorted by count):\n";
        $sortedFiles = $this->results;
        uasort($sortedFiles, function($a, $b) {
            return count($b) - count($a);
        });

        foreach ($sortedFiles as $file => $strings) {
            echo "├─ {$file}: " . count($strings) . " strings\n";
        }

        // Detailed results
        echo "\n📋 DETAILED RESULTS:\n";
        echo str_repeat("-", 80) . "\n";

        foreach ($sortedFiles as $file => $strings) {
            echo "\n📁 {$file} (" . count($strings) . " strings):\n";
            
            foreach ($strings as $string) {
                echo "   Line {$string['line']}: \"{$string['text']}\"\n";
                echo "      Context: " . substr($string['context'], 0, 100) . "...\n";
            }
        }

        // Save report
        $this->saveReport();
    }

    private function saveReport()
    {
        $reportPath = 'storage/localization/blade_hardcoded_scan.json';
        $reportDir = dirname($reportPath);
        
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        $report = [
            'scan_date' => date('Y-m-d H:i:s'),
            'statistics' => $this->stats,
            'results' => $this->results
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "\n💾 Detailed report saved to: {$reportPath}\n";
        echo "📊 Use this data for planning next conversion phases.\n";
    }
}

// Run the scanner
$scanner = new BladeHardcodedScanner();
$scanner->scan();
