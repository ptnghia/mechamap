<?php

/**
 * Comprehensive Hardcoded Text Scanner
 * Scans entire codebase for remaining hardcoded Vietnamese and English text
 * Excludes /admin directory and already converted priority files
 */

class HardcodedTextScanner
{
    private $basePath;
    private $excludedDirs = [
        'admin',
        'vendor',
        'node_modules',
        'storage/app',
        'storage/logs',
        'storage/framework',
        'bootstrap/cache',
        '.git',
        'public/build',
        'public/vendor'
    ];
    
    private $excludedFiles = [
        // Already converted priority files
        'threads/partials/showcase.blade.php',
        'threads/create.blade.php',
        'showcase/show.blade.php',
        'devices/index.blade.php',
        'layouts/app.blade.php'
    ];
    
    private $fileExtensions = [
        'php',
        'blade.php',
        'js',
        'vue',
        'ts'
    ];
    
    private $vietnamesePatterns = [
        // Vietnamese text patterns
        '/["\']([^"\']*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^"\']*)["\']/',
        // Common Vietnamese words
        '/["\']([^"\']*(?:của|và|với|trong|trên|dưới|bên|giữa|sau|trước|theo|về|từ|đến|cho|bởi|bằng|như|nếu|khi|mà|để|có|là|được|sẽ|đã|đang|vào|ra|lên|xuống|qua|tại|do|vì|nên|phải|cần|muốn|thích|biết|hiểu|làm|tạo|xóa|sửa|thêm|bớt|tăng|giảm|mở|đóng|bật|tắt|chọn|bỏ|lưu|tải|gửi|nhận|xem|đọc|viết|nghe|nói|đi|đến|về|ở|tại|trong|ngoài|trên|dưới)[^"\']*)["\']/',
        // Vietnamese sentences/phrases
        '/["\']([^"\']*(?:Vui lòng|Xin chào|Cảm ơn|Xin lỗi|Không có|Có lỗi|Thành công|Thất bại|Đăng nhập|Đăng ký|Đăng xuất|Quên mật khẩu|Mật khẩu|Tài khoản|Người dùng|Quản lý|Cài đặt|Tìm kiếm|Kết quả|Danh sách|Chi tiết|Thông tin|Liên hệ|Hỗ trợ|Giúp đỡ|Hướng dẫn|Báo cáo|Thống kê|Phân tích|Đánh giá|Bình luận|Chia sẻ|Tải xuống|Tải lên|Xuất|Nhập|In|Sao chép|Dán|Cắt|Hoàn tác|Làm lại|Lưu|Hủy|Xác nhận|Đồng ý|Từ chối|Chấp nhận|Bỏ qua|Tiếp tục|Dừng|Tạm dừng|Bắt đầu|Kết thúc|Hoàn thành|Chưa hoàn thành|Đang xử lý|Đã xử lý|Chờ xử lý|Đã duyệt|Chưa duyệt|Từ chối|Hủy bỏ|Xóa|Sửa|Thêm|Cập nhật|Làm mới|Tải lại|Khôi phục|Sao lưu|Xuất bản|Ẩn|Hiện|Bật|Tắt|Kích hoạt|Vô hiệu hóa)[^"\']*)["\']/'
    ];
    
    private $englishPatterns = [
        // English sentences/phrases (not single words to avoid false positives)
        '/["\']([A-Z][a-z]+ [a-z]+ [a-z]+[^"\']*)["\']/',
        // Common English UI phrases
        '/["\']([^"\']*(?:Please|Thank you|Sorry|Error|Success|Failed|Login|Register|Logout|Forgot password|Password|Account|User|Manage|Settings|Search|Results|List|Details|Information|Contact|Support|Help|Guide|Report|Statistics|Analysis|Review|Comment|Share|Download|Upload|Export|Import|Print|Copy|Paste|Cut|Undo|Redo|Save|Cancel|Confirm|Agree|Disagree|Accept|Reject|Skip|Continue|Stop|Pause|Start|End|Complete|Incomplete|Processing|Processed|Pending|Approved|Not approved|Rejected|Cancelled|Delete|Edit|Add|Update|Refresh|Reload|Restore|Backup|Publish|Hide|Show|Enable|Disable|Activate|Deactivate)[^"\']*)["\']/'
    ];
    
    private $results = [];
    private $stats = [
        'total_files_scanned' => 0,
        'files_with_hardcoded' => 0,
        'total_hardcoded_strings' => 0,
        'vietnamese_strings' => 0,
        'english_strings' => 0
    ];

    public function __construct($basePath = null)
    {
        $this->basePath = $basePath ?: dirname(__DIR__);
    }

    public function scan()
    {
        echo "🔍 Starting comprehensive hardcoded text scan...\n";
        echo "📁 Base path: {$this->basePath}\n";
        echo "🚫 Excluding: " . implode(', ', $this->excludedDirs) . "\n\n";

        $this->scanDirectory($this->basePath);
        $this->generateReport();
    }

    private function scanDirectory($dir)
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = str_replace($this->basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);

                // Skip excluded directories
                if ($this->isExcluded($relativePath)) {
                    continue;
                }

                // Skip excluded files
                if (in_array($relativePath, $this->excludedFiles)) {
                    continue;
                }

                // Check file extension
                if ($this->isValidFileExtension($file->getExtension())) {
                    $this->scanFile($file->getPathname(), $relativePath);
                }
            }
        }
    }

    private function isExcluded($path)
    {
        foreach ($this->excludedDirs as $excludedDir) {
            if (strpos($path, $excludedDir . '/') === 0 || strpos($path, $excludedDir . '\\') === 0) {
                return true;
            }
        }
        return false;
    }

    private function isValidFileExtension($extension)
    {
        return in_array(strtolower($extension), $this->fileExtensions) || 
               strpos($extension, 'blade.php') !== false;
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
            // Skip comments and translation calls
            if ($this->isSkippableLine($line)) {
                continue;
            }

            // Check for Vietnamese text
            foreach ($this->vietnamesePatterns as $pattern) {
                if (preg_match_all($pattern, $line, $matches)) {
                    foreach ($matches[1] as $match) {
                        if (strlen(trim($match)) > 2) { // Skip very short strings
                            $hardcodedStrings[] = [
                                'line' => $lineNumber + 1,
                                'text' => trim($match),
                                'type' => 'vietnamese',
                                'context' => trim($line)
                            ];
                            $this->stats['vietnamese_strings']++;
                        }
                    }
                }
            }

            // Check for English text
            foreach ($this->englishPatterns as $pattern) {
                if (preg_match_all($pattern, $line, $matches)) {
                    foreach ($matches[1] as $match) {
                        if (strlen(trim($match)) > 5) { // Skip short English strings
                            $hardcodedStrings[] = [
                                'line' => $lineNumber + 1,
                                'text' => trim($match),
                                'type' => 'english',
                                'context' => trim($line)
                            ];
                            $this->stats['english_strings']++;
                        }
                    }
                }
            }
        }

        if (!empty($hardcodedStrings)) {
            $this->results[$relativePath] = $hardcodedStrings;
            $this->stats['files_with_hardcoded']++;
            $this->stats['total_hardcoded_strings'] += count($hardcodedStrings);
        }
    }

    private function isSkippableLine($line)
    {
        $trimmed = trim($line);
        
        // Skip comments
        if (strpos($trimmed, '//') === 0 || strpos($trimmed, '#') === 0 || strpos($trimmed, '/*') === 0) {
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

    private function generateReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 COMPREHENSIVE HARDCODED TEXT SCAN RESULTS\n";
        echo str_repeat("=", 80) . "\n\n";

        // Statistics
        echo "📈 SCAN STATISTICS:\n";
        echo "├─ Total files scanned: {$this->stats['total_files_scanned']}\n";
        echo "├─ Files with hardcoded text: {$this->stats['files_with_hardcoded']}\n";
        echo "├─ Total hardcoded strings: {$this->stats['total_hardcoded_strings']}\n";
        echo "├─ Vietnamese strings: {$this->stats['vietnamese_strings']}\n";
        echo "└─ English strings: {$this->stats['english_strings']}\n\n";

        // Top files with most hardcoded text
        echo "🔥 TOP FILES WITH MOST HARDCODED TEXT:\n";
        $sortedFiles = $this->results;
        uasort($sortedFiles, function($a, $b) {
            return count($b) - count($a);
        });

        $topFiles = array_slice($sortedFiles, 0, 20, true);
        foreach ($topFiles as $file => $strings) {
            $viCount = count(array_filter($strings, fn($s) => $s['type'] === 'vietnamese'));
            $enCount = count(array_filter($strings, fn($s) => $s['type'] === 'english'));
            echo "├─ {$file}: " . count($strings) . " strings (VI: {$viCount}, EN: {$enCount})\n";
        }

        // Detailed results
        echo "\n📋 DETAILED RESULTS BY FILE:\n";
        echo str_repeat("-", 80) . "\n";

        foreach ($this->results as $file => $strings) {
            echo "\n📁 {$file} (" . count($strings) . " strings):\n";
            
            foreach (array_slice($strings, 0, 10) as $string) { // Show first 10 strings per file
                $typeIcon = $string['type'] === 'vietnamese' ? '🇻🇳' : '🇺🇸';
                echo "   {$typeIcon} Line {$string['line']}: \"{$string['text']}\"\n";
                echo "      Context: " . substr($string['context'], 0, 100) . "...\n";
            }
            
            if (count($strings) > 10) {
                echo "   ... and " . (count($strings) - 10) . " more strings\n";
            }
        }

        // Save detailed report to file
        $this->saveDetailedReport();
    }

    private function saveDetailedReport()
    {
        $reportPath = 'storage/localization/remaining_hardcoded_text_scan.json';
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
        echo "📊 Use this data for prioritizing next conversion phases.\n";
    }
}

// Run the scanner
$scanner = new HardcodedTextScanner();
$scanner->scan();
