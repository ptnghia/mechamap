<?php
/**
 * MechaMap Assets Cleanup Tool
 * Script an toàn để xóa các file assets không được sử dụng
 *
 * Usage:
 * php cleanup_unused_assets.php --dry-run          # Chỉ hiển thị danh sách
 * php cleanup_unused_assets.php --backup           # Tạo backup trước khi xóa
 * php cleanup_unused_assets.php --force            # Xóa thực sự
 * php cleanup_unused_assets.php --restore          # Khôi phục từ backup
 */

class AssetsCleanupTool {
    private $basePath;
    private $backupPath;
    private $logFile;
    private $protectedAssets = [];
    private $usedAssets = [];
    private $allAssets = [];
    private $scanPaths = [];

    public function __construct() {
        $this->basePath = __DIR__;
        $this->backupPath = $this->basePath . '/assets_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->basePath . '/assets_cleanup.log';

        // Định nghĩa các đường dẫn cần quét
        $this->scanPaths = [
            'resources/views/',
            'public/js/',
            'resources/js/',
            'public/css/',
            'public/admin-sw.js',
            'public/sw.js',
            'public/manifest.json',
            'public/admin-manifest.json'
        ];

        // Assets được bảo vệ - KHÔNG được xóa
        $this->protectedAssets = [
            // Core frameworks
            'assets/libs/bootstrap/',
            'assets/libs/jquery/',
            'assets/css/bootstrap',
            'assets/css/app',
            'assets/css/icons',
            'assets/js/app',

            // Admin essentials
            'assets/libs/apexcharts/',
            'assets/libs/datatables',
            'assets/libs/select2/',
            'assets/libs/metismenu/',
            'assets/libs/simplebar/',
            'assets/libs/node-waves/',

            // Fonts essentials
            'assets/fonts/fa-',
            'assets/fonts/materialdesignicons',

            // Core images
            'assets/images/favicon.ico',
            'assets/images/logo',
            'assets/images/icons/',

            // Custom files
            'assets/css/admin-mobile.css',
            'assets/css/hide-pwa-prompt.css',
            'assets/css/cart-ux-enhancements.css',
            'assets/js/admin-mobile.js',
            'assets/js/cart-ux-enhancements.js',
            'assets/js/mini-cart-enhancements.js',
            'assets/js/workflow-builder.js'
        ];
    }

    public function run($options = []) {
        $this->log("=== MechaMap Assets Cleanup Tool Started ===");
        $this->log("Time: " . date('Y-m-d H:i:s'));

        if (isset($options['restore'])) {
            return $this->restore();
        }

        // Bước 1: Quét tất cả assets
        $this->log("Step 1: Scanning all assets...");
        $this->scanAllAssets();

        // Bước 2: Tìm assets được sử dụng
        $this->log("Step 2: Finding used assets...");
        $this->findUsedAssets();

        // Bước 3: Phân tích và tạo danh sách xóa
        $this->log("Step 3: Analyzing unused assets...");
        $unusedAssets = $this->analyzeUnusedAssets();

        // Bước 4: Hiển thị báo cáo
        $this->displayReport($unusedAssets);

        // Bước 5: Thực hiện xóa (nếu không phải dry-run)
        if (!isset($options['dry-run'])) {
            if (isset($options['backup'])) {
                $this->createBackup($unusedAssets);
            }

            if (isset($options['force']) || $this->confirmAction()) {
                $this->deleteAssets($unusedAssets);
            }
        }

        $this->log("=== Cleanup Tool Finished ===");
    }

    private function scanAllAssets() {
        $assetsPath = $this->basePath . '/public/assets';
        $this->scanDirectory($assetsPath, 'assets');

        $this->log("Found " . count($this->allAssets) . " total assets");
    }

    private function scanDirectory($dir, $prefix = '') {
        if (!is_dir($dir)) return;

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = $dir . '/' . $file;
            $relativePath = $prefix . '/' . $file;

            if (is_dir($fullPath)) {
                $this->scanDirectory($fullPath, $relativePath);
            } else {
                $this->allAssets[] = [
                    'path' => ltrim($relativePath, '/'),
                    'fullPath' => $fullPath,
                    'size' => filesize($fullPath)
                ];
            }
        }
    }

    private function findUsedAssets() {
        foreach ($this->scanPaths as $path) {
            $fullPath = $this->basePath . '/' . $path;

            if (is_file($fullPath)) {
                $this->scanFile($fullPath);
            } elseif (is_dir($fullPath)) {
                $this->scanDirectoryForUsage($fullPath);
            }
        }

        $this->log("Found " . count($this->usedAssets) . " used assets");
    }

    private function scanDirectoryForUsage($dir) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if (in_array($file->getExtension(), ['php', 'js', 'css', 'json'])) {
                $this->scanFile($file->getPathname());
            }
        }
    }

    private function scanFile($filePath) {
        if (!file_exists($filePath)) return;

        $content = file_get_contents($filePath);

        // Tìm các pattern khác nhau
        $patterns = [
            '/assets\/[^\'"\s)}\]]+/',           // assets/path/file.ext
            '/asset\([\'"]([^\'")]+)[\'"]\)/',   // asset('path')
            '/url\([\'"]([^\'")]+)[\'"]\)/',     // url('path')
            '/"([^"]*assets[^"]*)"/',            // "path/assets/file"
            "/'([^']*assets[^']*)'/",            // 'path/assets/file'
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);

            foreach ($matches[0] as $match) {
                // Làm sạch match
                $asset = trim($match, '"\'()');
                if (strpos($asset, 'assets/') !== false) {
                    // Chuẩn hóa path
                    if (strpos($asset, 'assets/') === 0) {
                        $this->usedAssets[] = $asset;
                    } else {
                        // Extract assets part
                        $pos = strpos($asset, 'assets/');
                        if ($pos !== false) {
                            $this->usedAssets[] = substr($asset, $pos);
                        }
                    }
                }
            }
        }
    }

    private function analyzeUnusedAssets() {
        $unused = [];

        foreach ($this->allAssets as $asset) {
            $assetPath = $asset['path'];

            // Kiểm tra xem có được bảo vệ không
            if ($this->isProtected($assetPath)) {
                continue;
            }

            // Kiểm tra xem có được sử dụng không
            $isUsed = false;
            foreach ($this->usedAssets as $used) {
                if (strpos($used, $assetPath) !== false ||
                    strpos($assetPath, str_replace('assets/', '', $used)) !== false) {
                    $isUsed = true;
                    break;
                }
            }

            if (!$isUsed) {
                $unused[] = $asset;
            }
        }

        return $unused;
    }

    private function isProtected($assetPath) {
        foreach ($this->protectedAssets as $protected) {
            if (strpos($assetPath, $protected) !== false) {
                return true;
            }
        }
        return false;
    }

    private function displayReport($unusedAssets) {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "           MECHAMAP ASSETS CLEANUP REPORT\n";
        echo str_repeat("=", 80) . "\n";

        $totalSize = array_sum(array_column($this->allAssets, 'size'));
        $unusedSize = array_sum(array_column($unusedAssets, 'size'));

        echo sprintf("📊 TỔNG QUAN:\n");
        echo sprintf("   • Tổng assets: %d files (%.2f MB)\n",
            count($this->allAssets), $totalSize / 1024 / 1024);
        echo sprintf("   • Đang sử dụng: %d files\n",
            count($this->allAssets) - count($unusedAssets));
        echo sprintf("   • Không sử dụng: %d files (%.2f MB)\n",
            count($unusedAssets), $unusedSize / 1024 / 1024);
        echo sprintf("   • Tiết kiệm được: %.1f%%\n\n",
            ($unusedSize / $totalSize) * 100);

        // Phân loại theo thư mục
        $categories = $this->categorizeAssets($unusedAssets);

        echo "📁 PHÂN LOẠI FILE KHÔNG SỬ DỤNG:\n";
        foreach ($categories as $category => $files) {
            $categorySize = array_sum(array_column($files, 'size'));
            echo sprintf("   • %s: %d files (%.2f MB)\n",
                $category, count($files), $categorySize / 1024 / 1024);
        }

        echo "\n🔴 CÁC THƯ VIỆN LỚN CÓ THỂ XÓA:\n";
        $this->displayLargeLibraries($unusedAssets);

        echo "\n⚠️  CẢNH BÁO:\n";
        echo "   • Luôn tạo backup trước khi xóa\n";
        echo "   • Test kỹ sau khi xóa\n";
        echo "   • Một số assets có thể được load động\n";

        echo "\n" . str_repeat("=", 80) . "\n";
    }

    private function categorizeAssets($assets) {
        $categories = [];

        foreach ($assets as $asset) {
            $path = $asset['path'];

            if (strpos($path, 'assets/libs/') === 0) {
                $lib = explode('/', $path)[2];
                $categories["Thư viện: $lib"][] = $asset;
            } elseif (strpos($path, 'assets/css/') === 0) {
                $categories['CSS Files'][] = $asset;
            } elseif (strpos($path, 'assets/js/') === 0) {
                $categories['JavaScript Files'][] = $asset;
            } elseif (strpos($path, 'assets/images/') === 0) {
                $categories['Images'][] = $asset;
            } elseif (strpos($path, 'assets/fonts/') === 0) {
                $categories['Fonts'][] = $asset;
            } else {
                $categories['Other'][] = $asset;
            }
        }

        return $categories;
    }

    private function displayLargeLibraries($unusedAssets) {
        $libraries = [];

        foreach ($unusedAssets as $asset) {
            if (strpos($asset['path'], 'assets/libs/') === 0) {
                $lib = explode('/', $asset['path'])[2];
                if (!isset($libraries[$lib])) {
                    $libraries[$lib] = ['count' => 0, 'size' => 0];
                }
                $libraries[$lib]['count']++;
                $libraries[$lib]['size'] += $asset['size'];
            }
        }

        // Sắp xếp theo size
        uasort($libraries, function($a, $b) {
            return $b['size'] - $a['size'];
        });

        $largeLibs = array_slice($libraries, 0, 10, true);
        foreach ($largeLibs as $lib => $info) {
            if ($info['size'] > 100000) { // > 100KB
                echo sprintf("   • %s: %d files (%.2f MB)\n",
                    $lib, $info['count'], $info['size'] / 1024 / 1024);
            }
        }
    }

    private function createBackup($unusedAssets) {
        echo "\n🔄 Tạo backup...\n";

        if (!mkdir($this->backupPath, 0755, true)) {
            throw new Exception("Không thể tạo thư mục backup: " . $this->backupPath);
        }

        $backupList = [];

        foreach ($unusedAssets as $asset) {
            $sourcePath = $asset['fullPath'];
            $relativePath = $asset['path'];
            $backupFilePath = $this->backupPath . '/' . $relativePath;

            // Tạo thư mục con nếu cần
            $backupDir = dirname($backupFilePath);
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Copy file
            if (copy($sourcePath, $backupFilePath)) {
                $backupList[] = $relativePath;
            }
        }

        // Tạo file manifest cho backup
        $manifest = [
            'created_at' => date('Y-m-d H:i:s'),
            'total_files' => count($backupList),
            'files' => $backupList
        ];

        file_put_contents($this->backupPath . '/manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT));

        echo "✅ Backup hoàn thành: " . $this->backupPath . "\n";
        echo "   Đã backup " . count($backupList) . " files\n";

        $this->log("Backup created: " . $this->backupPath);
    }

    private function deleteAssets($unusedAssets) {
        echo "\n🗑️  Bắt đầu xóa assets...\n";

        $deleted = 0;
        $errors = 0;
        $totalSize = 0;

        foreach ($unusedAssets as $asset) {
            $filePath = $asset['fullPath'];

            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    $deleted++;
                    $totalSize += $asset['size'];
                    $this->log("Deleted: " . $asset['path']);

                    // Hiển thị progress
                    if ($deleted % 50 == 0) {
                        echo "   Đã xóa $deleted files...\n";
                    }
                } else {
                    $errors++;
                    $this->log("ERROR: Cannot delete " . $asset['path']);
                }
            }
        }

        // Xóa thư mục rỗng
        $this->removeEmptyDirectories($this->basePath . '/public/assets');

        echo "\n✅ Hoàn thành:\n";
        echo "   • Đã xóa: $deleted files\n";
        echo "   • Lỗi: $errors files\n";
        echo "   • Tiết kiệm: " . number_format($totalSize / 1024 / 1024, 2) . " MB\n";

        $this->log("Cleanup completed: $deleted deleted, $errors errors");
    }

    private function removeEmptyDirectories($dir) {
        if (!is_dir($dir)) return;

        $files = scandir($dir);
        $hasFiles = false;

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $fullPath = $dir . '/' . $file;
            if (is_dir($fullPath)) {
                $this->removeEmptyDirectories($fullPath);
                if (is_dir($fullPath) && count(scandir($fullPath)) == 2) {
                    rmdir($fullPath);
                    $this->log("Removed empty directory: " . $fullPath);
                } else {
                    $hasFiles = true;
                }
            } else {
                $hasFiles = true;
            }
        }
    }

    private function restore() {
        echo "🔄 Khôi phục từ backup...\n";

        // Tìm backup gần nhất
        $backupDirs = glob($this->basePath . '/assets_backup_*');
        if (empty($backupDirs)) {
            echo "❌ Không tìm thấy backup nào!\n";
            return false;
        }

        // Sắp xếp theo thời gian
        rsort($backupDirs);
        $latestBackup = $backupDirs[0];

        echo "Sử dụng backup: " . basename($latestBackup) . "\n";

        // Đọc manifest
        $manifestPath = $latestBackup . '/manifest.json';
        if (!file_exists($manifestPath)) {
            echo "❌ Không tìm thấy manifest file!\n";
            return false;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        echo "Khôi phục " . $manifest['total_files'] . " files...\n";

        $restored = 0;
        foreach ($manifest['files'] as $file) {
            $backupFile = $latestBackup . '/' . $file;
            $targetFile = $this->basePath . '/public/' . $file;

            // Tạo thư mục nếu cần
            $targetDir = dirname($targetFile);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (copy($backupFile, $targetFile)) {
                $restored++;
            }
        }

        echo "✅ Đã khôi phục $restored files\n";
        $this->log("Restored $restored files from backup: $latestBackup");

        return true;
    }

    private function confirmAction() {
        echo "\n⚠️  BẠN CÓ CHẮC CHẮN MUỐN XÓA CÁC FILE NÀY?\n";
        echo "Nhập 'yes' để xác nhận: ";

        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));
        fclose($handle);

        return strtolower($input) === 'yes';
    }

    private function log($message) {
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);

        // Cũng hiển thị trong console nếu là debug
        if (defined('DEBUG') && DEBUG) {
            echo $logMessage;
        }
    }
}

// Command line interface
function showUsage() {
    echo "MechaMap Assets Cleanup Tool\n";
    echo "Usage: php cleanup_unused_assets.php [options]\n\n";
    echo "Options:\n";
    echo "  --dry-run     Chỉ hiển thị danh sách, không xóa thực sự\n";
    echo "  --backup      Tạo backup trước khi xóa\n";
    echo "  --force       Xóa mà không cần xác nhận\n";
    echo "  --restore     Khôi phục từ backup gần nhất\n";
    echo "  --help        Hiển thị hướng dẫn này\n\n";
    echo "Examples:\n";
    echo "  php cleanup_unused_assets.php --dry-run\n";
    echo "  php cleanup_unused_assets.php --backup --force\n";
    echo "  php cleanup_unused_assets.php --restore\n";
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
    echo "⚠️  Không có option nào được chỉ định. Chạy với --dry-run để xem trước.\n";
    echo "Sử dụng --help để xem hướng dẫn.\n";
    exit(1);
}

try {
    $tool = new AssetsCleanupTool();
    $tool->run($options);
} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
