<?php

/**
 * Script để di chuyển hình ảnh từ root/images vào public/images
 * Đảm bảo sử dụng đúng hình ảnh thực tế cho Thread và Showcase
 */

class ImageMigrationScript
{
    private $sourceDir;
    private $targetDir;
    private $movedFiles = [];
    private $errors = [];
    
    public function __construct()
    {
        $this->sourceDir = __DIR__ . '/../images';
        $this->targetDir = __DIR__ . '/../public/images';
    }
    
    /**
     * Thực hiện di chuyển tất cả hình ảnh
     */
    public function migrate()
    {
        echo "🚀 STARTING IMAGE MIGRATION TO PUBLIC/IMAGES\n";
        echo "============================================\n\n";
        
        // 1. Tạo backup trước khi di chuyển
        $this->createBackup();
        
        // 2. Tạo cấu trúc thư mục đích
        $this->createTargetDirectories();
        
        // 3. Di chuyển từng category
        $this->migrateThreadImages();
        $this->migrateShowcaseImages();
        $this->migrateUserImages();
        $this->migrateCategoryImages();
        $this->migrateSettingImages();
        
        // 4. Tạo báo cáo
        $this->generateReport();
        
        echo "✅ Migration completed successfully!\n";
    }
    
    /**
     * Tạo backup
     */
    private function createBackup()
    {
        echo "💾 Creating backup...\n";
        
        $backupDir = __DIR__ . '/../storage/app/backup/images_migration_' . date('Y-m-d_H-i-s');
        
        if (!file_exists(dirname($backupDir))) {
            mkdir(dirname($backupDir), 0755, true);
        }
        
        $this->copyDirectory($this->targetDir, $backupDir);
        echo "  ✅ Backup created: " . basename($backupDir) . "\n\n";
    }
    
    /**
     * Tạo cấu trúc thư mục đích
     */
    private function createTargetDirectories()
    {
        echo "📁 Creating target directories...\n";
        
        $directories = [
            'threads',
            'showcases', 
            'users/avatars',
            'categories',
            'brand',
            'temp'
        ];
        
        foreach ($directories as $dir) {
            $fullPath = $this->targetDir . '/' . $dir;
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                echo "  📂 Created: {$dir}\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Di chuyển thread images
     */
    private function migrateThreadImages()
    {
        echo "📝 Migrating thread images...\n";
        
        $sourceDir = $this->sourceDir . '/threads';
        $targetDir = $this->targetDir . '/threads';
        
        if (!file_exists($sourceDir)) {
            echo "  ⚠️  Source directory not found: {$sourceDir}\n";
            return;
        }
        
        $files = glob($sourceDir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;
                
                if (!file_exists($targetFile)) {
                    if (copy($file, $targetFile)) {
                        $this->movedFiles[] = [
                            'from' => $file,
                            'to' => $targetFile,
                            'category' => 'threads'
                        ];
                        $moved++;
                        echo "  📄 Moved: {$filename}\n";
                    } else {
                        $this->errors[] = "Failed to copy: {$filename}";
                    }
                } else {
                    echo "  ⏭️  Skipped (exists): {$filename}\n";
                }
            }
        }
        
        echo "  ✅ Moved {$moved} thread images\n\n";
    }
    
    /**
     * Di chuyển showcase images
     */
    private function migrateShowcaseImages()
    {
        echo "🏆 Migrating showcase images...\n";
        
        $sourceDir = $this->sourceDir . '/showcase';
        $targetDir = $this->targetDir . '/showcases';
        
        if (!file_exists($sourceDir)) {
            echo "  ⚠️  Source directory not found: {$sourceDir}\n";
            return;
        }
        
        $files = glob($sourceDir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;
                
                if (!file_exists($targetFile)) {
                    if (copy($file, $targetFile)) {
                        $this->movedFiles[] = [
                            'from' => $file,
                            'to' => $targetFile,
                            'category' => 'showcases'
                        ];
                        $moved++;
                        echo "  📄 Moved: {$filename}\n";
                    } else {
                        $this->errors[] = "Failed to copy: {$filename}";
                    }
                } else {
                    echo "  ⏭️  Skipped (exists): {$filename}\n";
                }
            }
        }
        
        echo "  ✅ Moved {$moved} showcase images\n\n";
    }
    
    /**
     * Di chuyển user images
     */
    private function migrateUserImages()
    {
        echo "👤 Migrating user images...\n";
        
        $sourceDir = $this->sourceDir . '/users';
        $targetDir = $this->targetDir . '/users/avatars';
        
        if (!file_exists($sourceDir)) {
            echo "  ⚠️  Source directory not found: {$sourceDir}\n";
            return;
        }
        
        $files = glob($sourceDir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;
                
                if (!file_exists($targetFile)) {
                    if (copy($file, $targetFile)) {
                        $this->movedFiles[] = [
                            'from' => $file,
                            'to' => $targetFile,
                            'category' => 'users'
                        ];
                        $moved++;
                        echo "  📄 Moved: {$filename}\n";
                    } else {
                        $this->errors[] = "Failed to copy: {$filename}";
                    }
                } else {
                    echo "  ⏭️  Skipped (exists): {$filename}\n";
                }
            }
        }
        
        echo "  ✅ Moved {$moved} user images\n\n";
    }
    
    /**
     * Di chuyển category images
     */
    private function migrateCategoryImages()
    {
        echo "🏷️  Migrating category images...\n";
        
        $sourceDir = $this->sourceDir . '/category-forum';
        $targetDir = $this->targetDir . '/categories';
        
        if (!file_exists($sourceDir)) {
            echo "  ⚠️  Source directory not found: {$sourceDir}\n";
            return;
        }
        
        $files = glob($sourceDir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;
                
                if (!file_exists($targetFile)) {
                    if (copy($file, $targetFile)) {
                        $this->movedFiles[] = [
                            'from' => $file,
                            'to' => $targetFile,
                            'category' => 'categories'
                        ];
                        $moved++;
                        echo "  📄 Moved: {$filename}\n";
                    } else {
                        $this->errors[] = "Failed to copy: {$filename}";
                    }
                } else {
                    echo "  ⏭️  Skipped (exists): {$filename}\n";
                }
            }
        }
        
        echo "  ✅ Moved {$moved} category images\n\n";
    }
    
    /**
     * Di chuyển setting images
     */
    private function migrateSettingImages()
    {
        echo "⚙️  Migrating setting images...\n";
        
        $sourceDir = $this->sourceDir . '/setting';
        $targetDir = $this->targetDir . '/brand';
        
        if (!file_exists($sourceDir)) {
            echo "  ⚠️  Source directory not found: {$sourceDir}\n";
            return;
        }
        
        $files = glob($sourceDir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;
                
                if (!file_exists($targetFile)) {
                    if (copy($file, $targetFile)) {
                        $this->movedFiles[] = [
                            'from' => $file,
                            'to' => $targetFile,
                            'category' => 'brand'
                        ];
                        $moved++;
                        echo "  📄 Moved: {$filename}\n";
                    } else {
                        $this->errors[] = "Failed to copy: {$filename}";
                    }
                } else {
                    echo "  ⏭️  Skipped (exists): {$filename}\n";
                }
            }
        }
        
        echo "  ✅ Moved {$moved} setting images\n\n";
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!file_exists($source)) return;
        
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!file_exists($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }
    
    /**
     * Tạo báo cáo
     */
    private function generateReport()
    {
        echo "📋 MIGRATION REPORT\n";
        echo "==================\n";
        
        $totalMoved = count($this->movedFiles);
        $totalErrors = count($this->errors);
        
        echo "📊 Total files moved: {$totalMoved}\n";
        echo "❌ Total errors: {$totalErrors}\n\n";
        
        // Group by category
        $byCategory = [];
        foreach ($this->movedFiles as $file) {
            $category = $file['category'];
            if (!isset($byCategory[$category])) {
                $byCategory[$category] = 0;
            }
            $byCategory[$category]++;
        }
        
        echo "📁 Files moved by category:\n";
        foreach ($byCategory as $category => $count) {
            echo "  - {$category}: {$count} files\n";
        }
        
        if (!empty($this->errors)) {
            echo "\n❌ Errors:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
        }
        
        // Save detailed report
        $reportData = [
            'migration_date' => date('Y-m-d H:i:s'),
            'total_moved' => $totalMoved,
            'total_errors' => $totalErrors,
            'moved_files' => $this->movedFiles,
            'errors' => $this->errors,
            'by_category' => $byCategory
        ];
        
        $reportPath = __DIR__ . '/image_migration_report_' . date('Y-m-d_H-i-s') . '.json';
        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT));
        
        echo "\n📄 Detailed report saved: " . basename($reportPath) . "\n";
    }
}

// Chạy migration nếu script được gọi trực tiếp
if (php_sapi_name() === 'cli') {
    $migration = new ImageMigrationScript();
    $migration->migrate();
}
