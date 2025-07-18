<?php
/**
 * MechaMap Assets Safe Cleanup Script (PHP Version)
 * Script an toàn để xóa các assets không sử dụng - tương thích Windows
 * 
 * Usage: php safe_cleanup_assets.php [--dry-run] [--force]
 */

class SafeAssetsCleanup {
    private $backupDir;
    private $logFile;
    private $dryRun = false;
    private $force = false;
    
    // Danh sách thư viện sẽ xóa (đã xác nhận không sử dụng)
    private $librariesToRemove = [
        'tinymce' => 'Text editor - có CKEditor thay thế',
        'echarts' => 'Charts library - trùng với ApexCharts', 
        'leaflet' => 'Maps library - không được sử dụng',
        '@fullcalendar' => 'Calendar component - không được sử dụng',
        'dropzone' => 'File upload - không được sử dụng',
        'choices.js' => 'Select enhancement - trùng với select2',
        'glightbox' => 'Image gallery - có fancybox',
        'sweetalert2' => 'Modal alerts - có thể dùng native',
        'alertifyjs' => 'Notifications - trùng với toastr',
        'masonry-layout' => 'Grid layout - có thể dùng CSS Grid',
        'jquery-validation' => 'Form validation - có pristine',
        'pace-js' => 'Loading progress - không dùng',
        'nouislider' => 'Range slider - không dùng',
        'twitter-bootstrap-wizard' => 'Multi-step forms - không dùng'
    ];
    
    // CSS files sẽ xóa
    private $cssToRemove = [
        'preloader.css' => 'Preloader styles',
        'preloader.min.css' => 'Preloader styles (minified)',
        'preloader.rtl.css' => 'Preloader RTL styles',
        'realtime.css' => 'Realtime styles - không dùng',
        'app.rtl.css' => 'RTL styles - không cần',
        'bootstrap.rtl.css' => 'Bootstrap RTL - không cần',
        'icons.rtl.css' => 'Icons RTL - không cần'
    ];
    
    public function __construct($options = []) {
        $this->backupDir = 'assets_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = 'assets_cleanup_' . date('Y-m-d_H-i-s') . '.log';
        $this->dryRun = isset($options['dry-run']);
        $this->force = isset($options['force']);
    }
    
    public function run() {
        $this->log("=== MechaMap Assets Safe Cleanup Started ===");
        $this->log("Time: " . date('Y-m-d H:i:s'));
        $this->log("Mode: " . ($this->dryRun ? "DRY RUN" : "LIVE"));
        
        echo "🔧 MechaMap Assets Safe Cleanup Tool\n";
        echo str_repeat("=", 50) . "\n\n";
        
        // Kiểm tra môi trường
        if (!$this->checkEnvironment()) {
            return false;
        }
        
        // Tính toán kích thước ban đầu
        $initialSize = $this->getDirSize('public/assets');
        echo "📊 Kích thước assets ban đầu: " . $this->formatBytes($initialSize) . "\n\n";
        
        // Tạo backup (nếu không phải dry-run)
        if (!$this->dryRun) {
            if (!$this->createBackup()) {
                return false;
            }
        }
        
        // Thực hiện cleanup
        $results = $this->performCleanup();
        
        // Tính toán kết quả
        $finalSize = $this->getDirSize('public/assets');
        $totalSaved = $initialSize - $finalSize;
        $savingsPercent = $initialSize > 0 ? ($totalSaved / $initialSize) * 100 : 0;
        
        // Hiển thị kết quả
        $this->displayResults($results, $initialSize, $finalSize, $totalSaved, $savingsPercent);
        
        // Tạo script restore
        if (!$this->dryRun) {
            $this->createRestoreScript();
        }
        
        $this->log("=== Cleanup Completed ===");
        return true;
    }
    
    private function checkEnvironment() {
        if (!file_exists('composer.json')) {
            echo "❌ Error: Không tìm thấy composer.json. Chạy script từ thư mục gốc Laravel!\n";
            return false;
        }
        
        if (!is_dir('public/assets')) {
            echo "❌ Error: Không tìm thấy thư mục public/assets!\n";
            return false;
        }
        
        echo "✅ Môi trường hợp lệ - tìm thấy Laravel project với assets\n";
        return true;
    }
    
    private function createBackup() {
        echo "🔄 Tạo backup...\n";
        
        if (!$this->copyDirectory('public/assets', $this->backupDir)) {
            echo "❌ Không thể tạo backup!\n";
            return false;
        }
        
        echo "✅ Backup tạo thành công: {$this->backupDir}\n\n";
        $this->log("Backup created: {$this->backupDir}");
        return true;
    }
    
    private function performCleanup() {
        $results = [
            'libraries_removed' => 0,
            'libraries_size_saved' => 0,
            'css_removed' => 0,
            'css_size_saved' => 0
        ];
        
        // Xóa thư viện
        echo "🗑️  Xóa các thư viện không sử dụng...\n";
        foreach ($this->librariesToRemove as $lib => $description) {
            $libPath = "public/assets/libs/$lib";
            
            if (is_dir($libPath)) {
                $libSize = $this->getDirSize($libPath);
                
                if ($this->dryRun) {
                    echo "   [DRY RUN] Sẽ xóa: $lib (" . $this->formatBytes($libSize) . ") - $description\n";
                } else {
                    if ($this->removeDirectory($libPath)) {
                        echo "   ✅ Đã xóa: $lib (" . $this->formatBytes($libSize) . ")\n";
                        $results['libraries_removed']++;
                        $results['libraries_size_saved'] += $libSize;
                        $this->log("Removed library: $lib ({$this->formatBytes($libSize)})");
                    } else {
                        echo "   ❌ Lỗi xóa: $lib\n";
                        $this->log("ERROR: Failed to remove library: $lib");
                    }
                }
            }
        }
        
        echo "\n🎨 Xóa CSS files không sử dụng...\n";
        foreach ($this->cssToRemove as $css => $description) {
            $cssPath = "public/assets/css/$css";
            
            if (file_exists($cssPath)) {
                $cssSize = filesize($cssPath);
                
                if ($this->dryRun) {
                    echo "   [DRY RUN] Sẽ xóa: $css (" . $this->formatBytes($cssSize) . ") - $description\n";
                } else {
                    if (unlink($cssPath)) {
                        echo "   ✅ Đã xóa: $css (" . $this->formatBytes($cssSize) . ")\n";
                        $results['css_removed']++;
                        $results['css_size_saved'] += $cssSize;
                        $this->log("Removed CSS: $css ({$this->formatBytes($cssSize)})");
                    } else {
                        echo "   ❌ Lỗi xóa: $css\n";
                        $this->log("ERROR: Failed to remove CSS: $css");
                    }
                }
            }
        }
        
        // Dọn dẹp thư mục rỗng
        if (!$this->dryRun) {
            echo "\n🧹 Dọn dẹp thư mục rỗng...\n";
            $this->removeEmptyDirectories('public/assets');
            echo "✅ Đã dọn dẹp thư mục rỗng\n";
        }
        
        return $results;
    }
    
    private function displayResults($results, $initialSize, $finalSize, $totalSaved, $savingsPercent) {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 KẾT QUẢ CLEANUP:\n";
        echo str_repeat("=", 50) . "\n";
        
        if (!$this->dryRun) {
            echo "Kích thước ban đầu: " . $this->formatBytes($initialSize) . "\n";
            echo "Kích thước sau cleanup: " . $this->formatBytes($finalSize) . "\n";
            echo "Tổng tiết kiệm: " . $this->formatBytes($totalSaved) . " (" . number_format($savingsPercent, 1) . "%)\n";
        }
        
        echo "Thư viện đã xóa: " . $results['libraries_removed'] . "\n";
        echo "CSS files đã xóa: " . $results['css_removed'] . "\n";
        
        if (!$this->dryRun) {
            echo "Backup location: {$this->backupDir}\n";
            echo "Log file: {$this->logFile}\n";
        }
        
        echo "\n⚠️  QUAN TRỌNG - CÁC BƯỚC TIẾP THEO:\n";
        echo "1. Kiểm tra website kỹ lưỡng\n";
        echo "2. Test admin panel và tất cả chức năng\n";
        echo "3. Kiểm tra charts, forms, và UI components\n";
        
        if (!$this->dryRun) {
            echo "4. Nếu có lỗi, chạy: php restore_assets.php\n";
        }
        
        echo "\n✅ Cleanup hoàn thành!\n";
    }
    
    private function createRestoreScript() {
        $restoreScript = "<?php\n";
        $restoreScript .= "// MechaMap Assets Restore Script\n";
        $restoreScript .= "echo \"🔄 Khôi phục assets từ backup...\\n\";\n\n";
        $restoreScript .= "if (is_dir('{$this->backupDir}')) {\n";
        $restoreScript .= "    // Xóa assets hiện tại\n";
        $restoreScript .= "    if (is_dir('public/assets')) {\n";
        $restoreScript .= "        exec('rmdir /s /q public\\\\assets 2>nul || rm -rf public/assets');\n";
        $restoreScript .= "    }\n\n";
        $restoreScript .= "    // Khôi phục từ backup\n";
        $restoreScript .= "    exec('xcopy /e /i {$this->backupDir} public\\\\assets 2>nul || cp -r {$this->backupDir} public/assets');\n";
        $restoreScript .= "    echo \"✅ Assets đã được khôi phục thành công!\\n\";\n";
        $restoreScript .= "} else {\n";
        $restoreScript .= "    echo \"❌ Không tìm thấy backup: {$this->backupDir}\\n\";\n";
        $restoreScript .= "}\n";
        
        file_put_contents('restore_assets.php', $restoreScript);
        echo "📝 Đã tạo script khôi phục: restore_assets.php\n";
    }
    
    // Helper methods
    private function getDirSize($dir) {
        if (!is_dir($dir)) return 0;
        
        $size = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    private function formatBytes($bytes) {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
    
    private function copyDirectory($source, $dest) {
        if (!is_dir($source)) return false;
        
        if (!mkdir($dest, 0755, true)) return false;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $destPath = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!mkdir($destPath, 0755, true)) return false;
            } else {
                if (!copy($item, $destPath)) return false;
            }
        }
        
        return true;
    }
    
    private function removeDirectory($dir) {
        if (!is_dir($dir)) return false;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        
        return rmdir($dir);
    }
    
    private function removeEmptyDirectories($dir) {
        if (!is_dir($dir)) return;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $dirPath = $file->getRealPath();
                if (count(scandir($dirPath)) == 2) { // Only . and ..
                    rmdir($dirPath);
                }
            }
        }
    }
    
    private function log($message) {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}

// Command line interface
function showUsage() {
    echo "MechaMap Assets Safe Cleanup Tool\n";
    echo "Usage: php safe_cleanup_assets.php [options]\n\n";
    echo "Options:\n";
    echo "  --dry-run     Chỉ hiển thị những gì sẽ được xóa, không xóa thực sự\n";
    echo "  --force       Xóa mà không cần xác nhận\n";
    echo "  --help        Hiển thị hướng dẫn này\n\n";
    echo "Examples:\n";
    echo "  php safe_cleanup_assets.php --dry-run\n";
    echo "  php safe_cleanup_assets.php --force\n";
}

// Main execution
if (php_sapi_name() !== 'cli') {
    die("Script này chỉ chạy được từ command line!\n");
}

$options = [];
$args = array_slice($argv, 1);

foreach ($args as $arg) {
    if ($arg === '--help') {
        showUsage();
        exit(0);
    }
    
    $options[ltrim($arg, '-')] = true;
}

// Xác nhận nếu không có --force và không phải dry-run
if (!isset($options['dry-run']) && !isset($options['force'])) {
    echo "⚠️  BẠN CHUẨN BỊ XÓA CÁC ASSETS KHÔNG SỬ DỤNG!\n";
    echo "Điều này sẽ xóa khoảng 9-12 MB assets.\n";
    echo "Backup sẽ được tạo tự động.\n\n";
    echo "Tiếp tục? (y/N): ";
    
    $handle = fopen("php://stdin", "r");
    $input = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($input) !== 'y' && strtolower($input) !== 'yes') {
        echo "Đã hủy.\n";
        exit(0);
    }
}

try {
    $cleanup = new SafeAssetsCleanup($options);
    $cleanup->run();
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
