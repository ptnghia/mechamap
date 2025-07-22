<?php

/**
 * CHECK HARDCODED STRINGS IN BLADE FILES
 * Kiểm tra tất cả file blade (trừ /admin) xem còn hardcoded strings không
 */

echo "=== CHECKING HARDCODED STRINGS IN BLADE FILES ===\n\n";

// Function to scan directory for blade files
function scanBladeFiles($directory, $excludePaths = []) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getPathname();
            
            // Check if path should be excluded
            $shouldExclude = false;
            foreach ($excludePaths as $excludePath) {
                if (strpos($path, $excludePath) !== false) {
                    $shouldExclude = true;
                    break;
                }
            }
            
            if (!$shouldExclude) {
                $files[] = $path;
            }
        }
    }
    
    return $files;
}

// Function to detect hardcoded strings
function detectHardcodedStrings($content, $filePath) {
    $hardcodedStrings = [];
    
    // Patterns for hardcoded Vietnamese strings
    $patterns = [
        // Vietnamese text in quotes (not in translation functions)
        '/(?<!__\(|@lang\(|trans\()[\'"]([^\'\"]*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^\'\"]*)[\'"]/',
        
        // Common Vietnamese words in quotes
        '/[\'\"](.*(?:trang chủ|diễn đàn|thị trường|đăng nhập|đăng ký|đăng xuất|tìm kiếm|hồ sơ|cài đặt|thông báo|tin nhắn|bài viết|chủ đề|bình luận|thích|chia sẻ|lưu|chỉnh sửa|xóa|tạo|cập nhật|xem|tải xuống|tải lên|gửi|nhận|mở|đóng|bắt đầu|kết thúc|tiếp tục|hủy|xác nhận|thành công|thất bại|lỗi|cảnh báo|thông tin|chi tiết|danh sách|báo cáo|thống kê|kết quả|dữ liệu|hình ảnh|tài liệu|liên kết|email|điện thoại|địa chỉ|mô tả|tiêu đề|nội dung|ngày|thời gian|hôm nay|hôm qua|ngày mai|bây giờ|hoạt động|công khai|riêng tư|miễn phí|trả phí).*)[\'\"]/i',
        
        // Common English hardcoded strings (not in translation functions)
        '/(?<!__\(|@lang\(|trans\()[\'\"]((?:Home|Forums?|Marketplace|Login|Register|Logout|Search|Profile|Settings|Notifications?|Messages?|Posts?|Topics?|Comments?|Like|Share|Save|Edit|Delete|Create|Update|View|Download|Upload|Send|Receive|Open|Close|Start|End|Continue|Cancel|Confirm|Success|Failed?|Error|Warning|Information|Details?|List|Report|Statistics|Results?|Data|Image|Document|Link|Email|Phone|Address|Description|Title|Content|Date|Time|Today|Yesterday|Tomorrow|Now|Active|Public|Private|Free|Paid|Loading|Please wait|Click here|Read more|Show more|Show less|Back|Next|Previous|First|Last|All|None|Yes|No|OK|Submit|Reset|Clear|Apply|Filter|Sort|Export|Import|Print|Copy|Cut|Paste|Undo|Redo|Help|About|Contact|Support|FAQ|Terms|Privacy|Policy|License|Version|Admin|User|Guest|Member|Moderator|Administrator)[^\'\"]*)[\'\"]/i',
        
        // Text in HTML elements (not in translation functions)
        '/(?<!__\(|@lang\(|trans\()>([^<]*[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ][^<]*)</u',
    ];
    
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach ($matches[1] as $match) {
            $text = trim($match[0]);
            $position = $match[1];
            
            // Skip if empty or too short
            if (strlen($text) < 3) continue;
            
            // Skip if it's a variable or PHP code
            if (preg_match('/^\$|->|::|function|class|namespace|use |return |echo |print /', $text)) continue;
            
            // Skip if it's a URL, email, or technical string
            if (preg_match('/^(https?:\/\/|mailto:|[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}|[a-zA-Z0-9._-]+\.(css|js|png|jpg|jpeg|gif|svg|pdf|doc|docx|xls|xlsx|zip|rar))/', $text)) continue;
            
            // Skip if it's a CSS class or ID
            if (preg_match('/^[a-zA-Z0-9._-]+$/', $text) && strlen($text) < 20) continue;
            
            // Skip if it's already a translation key format
            if (preg_match('/^[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+/', $text)) continue;
            
            // Get line number
            $lineNumber = substr_count(substr($content, 0, $position), "\n") + 1;
            
            $hardcodedStrings[] = [
                'text' => $text,
                'line' => $lineNumber,
                'context' => trim(substr($content, max(0, $position - 50), 100))
            ];
        }
    }
    
    return $hardcodedStrings;
}

// Scan all blade files
$viewsDirectory = __DIR__ . '/resources/views';
$excludePaths = ['/admin/', '\\admin\\'];

echo "🔍 Scanning blade files in: $viewsDirectory\n";
echo "❌ Excluding: " . implode(', ', $excludePaths) . "\n\n";

$bladeFiles = scanBladeFiles($viewsDirectory, $excludePaths);

echo "📁 Found " . count($bladeFiles) . " blade files to check\n\n";

$totalHardcodedStrings = 0;
$filesWithHardcoded = 0;
$allHardcodedStrings = [];

foreach ($bladeFiles as $filePath) {
    $relativePath = str_replace(__DIR__ . '/resources/views/', '', $filePath);
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read: $relativePath\n";
        continue;
    }
    
    $hardcodedStrings = detectHardcodedStrings($content, $filePath);
    
    if (!empty($hardcodedStrings)) {
        $filesWithHardcoded++;
        $fileHardcodedCount = count($hardcodedStrings);
        $totalHardcodedStrings += $fileHardcodedCount;
        
        echo "⚠️  $relativePath ($fileHardcodedCount hardcoded strings)\n";
        
        // Show first 5 hardcoded strings
        $showCount = min(5, $fileHardcodedCount);
        for ($i = 0; $i < $showCount; $i++) {
            $string = $hardcodedStrings[$i];
            echo "  Line {$string['line']}: \"{$string['text']}\"\n";
        }
        
        if ($fileHardcodedCount > 5) {
            echo "  ... and " . ($fileHardcodedCount - 5) . " more\n";
        }
        
        echo "\n";
        
        // Store for summary
        $allHardcodedStrings[$relativePath] = $hardcodedStrings;
    }
}

// Summary
echo "=== SUMMARY ===\n";
echo "Total blade files checked: " . count($bladeFiles) . "\n";
echo "Files with hardcoded strings: $filesWithHardcoded\n";
echo "Total hardcoded strings found: $totalHardcodedStrings\n";

if ($filesWithHardcoded > 0) {
    $percentage = round(($filesWithHardcoded / count($bladeFiles)) * 100, 2);
    echo "Percentage of files with hardcoded: $percentage%\n";
    
    echo "\n📊 TOP 10 FILES WITH MOST HARDCODED STRINGS:\n";
    
    // Sort files by number of hardcoded strings
    uasort($allHardcodedStrings, function($a, $b) {
        return count($b) - count($a);
    });
    
    $count = 0;
    foreach ($allHardcodedStrings as $file => $strings) {
        if ($count >= 10) break;
        echo "  " . ($count + 1) . ". $file (" . count($strings) . " strings)\n";
        $count++;
    }
    
    echo "\n🔧 RECOMMENDATIONS:\n";
    echo "1. Replace hardcoded strings with translation keys\n";
    echo "2. Use __('key') or @lang('key') for all user-facing text\n";
    echo "3. Move hardcoded strings to appropriate translation files\n";
    echo "4. Test translations after making changes\n";
} else {
    echo "\n✅ EXCELLENT! No hardcoded strings found in blade files!\n";
    echo "🎉 All user-facing text is properly using translation functions.\n";
}

echo "\n✅ Hardcoded strings check completed at " . date('Y-m-d H:i:s') . "\n";
?>
