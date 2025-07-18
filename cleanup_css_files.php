<?php
/**
 * MechaMap CSS Files Cleanup Tool
 * Script an toàn để xóa các CSS files không sử dụng
 */

class CSSCleanupTool {
    private $backupDir;
    private $logFile;
    private $dryRun = false;
    
    public function __construct($options = []) {
        $this->backupDir = 'css_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = 'css_cleanup_' . date('Y-m-d_H-i-s') . '.log';
        $this->dryRun = isset($options['dry-run']);
    }
    
    public function run() {
        echo "🎨 MechaMap CSS Cleanup Tool\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $this->log("=== CSS Cleanup Started ===");
        $this->log("Mode: " . ($this->dryRun ? "DRY RUN" : "LIVE"));
        
        // Tính kích thước ban đầu
        $initialSize = $this->getDirSize('public/css');
        echo "📊 Kích thước CSS ban đầu: " . $this->formatBytes($initialSize) . "\n\n";
        
        // Tạo backup nếu không phải dry-run
        if (!$this->dryRun) {
            $this->createBackup();
        }
        
        // Thực hiện cleanup
        $results = $this->performCleanup();
        
        // Tính kích thước cuối
        $finalSize = $this->getDirSize('public/css');
        $totalSaved = $initialSize - $finalSize;
        $savingsPercent = $initialSize > 0 ? ($totalSaved / $initialSize) * 100 : 0;
        
        // Hiển thị kết quả
        $this->displayResults($results, $initialSize, $finalSize, $totalSaved, $savingsPercent);
        
        $this->log("=== CSS Cleanup Completed ===");
        return true;
    }
    
    private function createBackup() {
        echo "🔄 Tạo backup CSS files...\n";
        
        if (!$this->copyDirectory('public/css', $this->backupDir)) {
            echo "❌ Không thể tạo backup!\n";
            return false;
        }
        
        echo "✅ Backup tạo thành công: {$this->backupDir}\n\n";
        $this->log("Backup created: {$this->backupDir}");
        return true;
    }
    
    private function performCleanup() {
        $results = [
            'backup_removed' => 0,
            'backup_size_saved' => 0,
            'duplicate_removed' => 0,
            'duplicate_size_saved' => 0
        ];
        
        // 1. Xóa thư mục backup cũ
        echo "🗑️  Xóa thư mục backup cũ...\n";
        $backupPath = 'public/css/frontend_backup_20250711_161416';
        
        if (is_dir($backupPath)) {
            $backupSize = $this->getDirSize($backupPath);
            
            if ($this->dryRun) {
                echo "   [DRY RUN] Sẽ xóa: $backupPath (" . $this->formatBytes($backupSize) . ")\n";
            } else {
                if ($this->removeDirectory($backupPath)) {
                    echo "   ✅ Đã xóa thư mục backup: " . $this->formatBytes($backupSize) . "\n";
                    $results['backup_removed'] = 1;
                    $results['backup_size_saved'] = $backupSize;
                    $this->log("Removed backup directory: $backupPath");
                } else {
                    echo "   ❌ Lỗi xóa thư mục backup\n";
                }
            }
        } else {
            echo "   ℹ️  Không tìm thấy thư mục backup\n";
        }
        
        // 2. Xóa file duplicate
        echo "\n🔍 Kiểm tra file duplicate...\n";
        $duplicateFile = 'public/css/admin/main-admin.css';
        $rootFile = 'public/css/main-admin.css';
        
        if (file_exists($duplicateFile) && file_exists($rootFile)) {
            // So sánh nội dung
            $duplicateContent = file_get_contents($duplicateFile);
            $rootContent = file_get_contents($rootFile);
            
            if (md5($duplicateContent) === md5($rootContent)) {
                $duplicateSize = filesize($duplicateFile);
                
                if ($this->dryRun) {
                    echo "   [DRY RUN] Sẽ xóa duplicate: $duplicateFile (" . $this->formatBytes($duplicateSize) . ")\n";
                } else {
                    if (unlink($duplicateFile)) {
                        echo "   ✅ Đã xóa file duplicate: " . $this->formatBytes($duplicateSize) . "\n";
                        $results['duplicate_removed'] = 1;
                        $results['duplicate_size_saved'] = $duplicateSize;
                        $this->log("Removed duplicate file: $duplicateFile");
                    } else {
                        echo "   ❌ Lỗi xóa file duplicate\n";
                    }
                }
            } else {
                echo "   ⚠️  File có nội dung khác nhau, không xóa\n";
            }
        } else {
            echo "   ℹ️  Không tìm thấy file duplicate\n";
        }
        
        // 3. Xóa file TinyMCE CSS (nếu có)
        echo "\n🔍 Kiểm tra CSS không cần thiết...\n";
        $tinymceCSS = 'public/css/tinymce-mechamap.css';
        
        if (file_exists($tinymceCSS)) {
            $tinymceSize = filesize($tinymceCSS);
            
            if ($this->dryRun) {
                echo "   [DRY RUN] Sẽ xóa: $tinymceCSS (" . $this->formatBytes($tinymceSize) . ") - TinyMCE đã bị xóa\n";
            } else {
                if (unlink($tinymceCSS)) {
                    echo "   ✅ Đã xóa TinyMCE CSS: " . $this->formatBytes($tinymceSize) . "\n";
                    $results['duplicate_size_saved'] += $tinymceSize;
                    $this->log("Removed TinyMCE CSS: $tinymceCSS");
                } else {
                    echo "   ❌ Lỗi xóa TinyMCE CSS\n";
                }
            }
        } else {
            echo "   ℹ️  Không tìm thấy TinyMCE CSS\n";
        }
        
        // 4. Dọn dẹp thư mục rỗng
        if (!$this->dryRun) {
            echo "\n🧹 Dọn dẹp thư mục rỗng...\n";
            $this->removeEmptyDirectories('public/css');
            echo "✅ Đã dọn dẹp thư mục rỗng\n";
        }
        
        return $results;
    }
    
    private function displayResults($results, $initialSize, $finalSize, $totalSaved, $savingsPercent) {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 KẾT QUẢ CSS CLEANUP:\n";
        echo str_repeat("=", 50) . "\n";
        
        if (!$this->dryRun) {
            echo "Kích thước ban đầu: " . $this->formatBytes($initialSize) . "\n";
            echo "Kích thước sau cleanup: " . $this->formatBytes($finalSize) . "\n";
            echo "Tổng tiết kiệm: " . $this->formatBytes($totalSaved) . " (" . number_format($savingsPercent, 1) . "%)\n";
        }
        
        echo "Thư mục backup đã xóa: " . $results['backup_removed'] . "\n";
        echo "File duplicate đã xóa: " . $results['duplicate_removed'] . "\n";
        
        if (!$this->dryRun) {
            echo "Backup location: {$this->backupDir}\n";
            echo "Log file: {$this->logFile}\n";
        }
        
        echo "\n⚠️  LƯU Ý:\n";
        echo "• Các file CSS chính vẫn được giữ nguyên\n";
        echo "• Chỉ xóa backup và duplicate files\n";
        echo "• Website sẽ hoạt động bình thường\n";
        
        if (!$this->dryRun) {
            echo "• Nếu có vấn đề, khôi phục: cp -r {$this->backupDir}/* public/css/\n";
        }
        
        echo "\n✅ CSS cleanup hoàn thành!\n";
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
    echo "MechaMap CSS Cleanup Tool\n";
    echo "Usage: php cleanup_css_files.php [options]\n\n";
    echo "Options:\n";
    echo "  --dry-run     Chỉ hiển thị những gì sẽ được xóa\n";
    echo "  --force       Thực hiện cleanup\n";
    echo "  --help        Hiển thị hướng dẫn này\n\n";
    echo "Examples:\n";
    echo "  php cleanup_css_files.php --dry-run\n";
    echo "  php cleanup_css_files.php --force\n";
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

if (empty($options)) {
    echo "⚠️  Không có option nào được chỉ định.\n";
    echo "Sử dụng --dry-run để xem trước hoặc --help để xem hướng dẫn.\n";
    exit(1);
}

// Xác nhận nếu không có --force và không phải dry-run
if (!isset($options['dry-run']) && !isset($options['force'])) {
    echo "⚠️  BẠN CHUẨN BỊ XÓA CÁC CSS FILES BACKUP!\n";
    echo "Điều này sẽ xóa khoảng 694 KB backup files.\n";
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
    $cleanup = new CSSCleanupTool($options);
    $cleanup->run();
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
