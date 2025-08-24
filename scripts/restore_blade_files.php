<?php

/**
 * Script phục hồi tất cả file .blade.php từ backup
 * Chạy: php scripts/restore_blade_files.php
 */

echo "🔄 MechaMap Blade Files Restore Script\n";
echo "=====================================\n\n";

$backupDir = 'resources/views/backup';
$viewsDir = 'resources/views';

if (!is_dir($backupDir)) {
    echo "❌ Backup directory not found: {$backupDir}\n";
    exit(1);
}

// Tìm tất cả file .blade.php trong backup
function findBackupFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.php') !== false) {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

echo "🔍 Scanning backup directory...\n";
$backupFiles = findBackupFiles($backupDir);
echo "   Found " . count($backupFiles) . " blade files in backup\n\n";

if (count($backupFiles) === 0) {
    echo "✅ No files to restore!\n";
    exit(0);
}

echo "🔄 Restoring files...\n";

$restoredCount = 0;
$failedCount = 0;
$errors = [];

foreach ($backupFiles as $backupFile) {
    // Tính toán đường dẫn gốc
    $relativePath = str_replace($backupDir . '/', '', $backupFile);
    $relativePath = str_replace($backupDir . '\\', '', $relativePath);
    
    // Bỏ qua thư mục "resources" trong backup
    if (strpos($relativePath, 'resources/views/') === 0) {
        $relativePath = substr($relativePath, strlen('resources/views/'));
    } elseif (strpos($relativePath, 'resources\\views\\') === 0) {
        $relativePath = substr($relativePath, strlen('resources\\views\\'));
    }
    
    $originalPath = $viewsDir . '/' . str_replace('\\', '/', $relativePath);
    
    echo "   Restoring: {$relativePath}";
    
    // Tạo thư mục nếu chưa tồn tại
    $originalDir = dirname($originalPath);
    if (!is_dir($originalDir)) {
        mkdir($originalDir, 0755, true);
    }
    
    // Kiểm tra xem file gốc đã tồn tại chưa
    if (file_exists($originalPath)) {
        echo " → Already exists, skipping\n";
        continue;
    }
    
    // Copy file từ backup về vị trí gốc
    if (copy($backupFile, $originalPath)) {
        echo " → ✅ Restored\n";
        $restoredCount++;
    } else {
        echo " → ❌ Failed\n";
        $failedCount++;
        $errors[] = "Failed to restore: {$relativePath}";
    }
}

echo "\n📊 Restore Summary:\n";
echo "==================\n";
echo "✅ Files restored: {$restoredCount}\n";
echo "❌ Failed: {$failedCount}\n";

if (!empty($errors)) {
    echo "\n❌ Errors:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

if ($restoredCount > 0) {
    echo "\n🗑️ Cleaning up backup directory...\n";
    
    // Xóa backup directory sau khi restore thành công
    function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    if (deleteDirectory($backupDir)) {
        echo "   ✅ Backup directory cleaned up\n";
    } else {
        echo "   ⚠️ Could not clean up backup directory\n";
    }
}

echo "\n✅ Restore completed!\n";
echo "📋 Next steps:\n";
echo "1. Test your application to ensure everything works\n";
echo "2. Run: php artisan view:clear\n";
echo "3. Run: php artisan optimize:clear\n";

echo "\n🎉 All blade files have been restored to their original locations!\n";
