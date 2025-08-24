<?php

/**
 * Script tìm kiếm và di chuyển các file .blade.php backup
 * Chạy: php scripts/find_backup_blade_files.php
 */

echo "🔍 MechaMap Backup Blade Files Finder\n";
echo "====================================\n\n";

// Tạo thư mục backup
$backupDir = 'resources/views/backup';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "📁 Created backup directory: {$backupDir}\n\n";
}

// Patterns để nhận diện file backup
$backupPatterns = [
    'backup',
    'bak',
    'old',
    'copy',
    'temp',
    'tmp',
    'original',
    'prev',
    'previous',
    '_old',
    '_bak',
    '_backup',
    '_copy',
    '_temp',
    '_tmp',
    '_original',
    '_prev',
    '_previous',
    '-old',
    '-bak',
    '-backup',
    '-copy',
    '-temp',
    '-tmp',
    '-original',
    '-prev',
    '-previous',
    '.old',
    '.bak',
    '.backup',
    '.copy',
    '.temp',
    '.tmp',
    '.original',
    '.prev',
    '.previous'
];

// Tìm tất cả file .blade.php
function findBladeFiles($dir, $excludeBackup = true) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.php') !== false) {
            
            // Bỏ qua thư mục backup nếu được yêu cầu
            if ($excludeBackup && strpos($file->getPathname(), '/backup/') !== false) {
                continue;
            }
            
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

// Kiểm tra xem file có phải backup không
function isBackupFile($filename, $patterns) {
    $filename = strtolower($filename);
    
    foreach ($patterns as $pattern) {
        // Kiểm tra pattern ở cuối tên file (trước .blade.php)
        if (strpos($filename, $pattern . '.blade.php') !== false) {
            return true;
        }
        
        // Kiểm tra pattern ở giữa tên file
        if (strpos($filename, $pattern) !== false && 
            strpos($filename, '.blade.php') !== false) {
            return true;
        }
    }
    
    return false;
}

// Kiểm tra file duplicate (có version gốc và backup)
function findDuplicateFiles($files) {
    $duplicates = [];
    $baseNames = [];
    
    foreach ($files as $file) {
        $filename = basename($file);
        $dirname = dirname($file);
        
        // Tạo base name bằng cách loại bỏ các pattern backup
        $baseName = $filename;
        $patterns = [
            'backup', 'bak', 'old', 'copy', 'temp', 'tmp', 'original', 'prev', 'previous',
            '_old', '_bak', '_backup', '_copy', '_temp', '_tmp', '_original', '_prev', '_previous',
            '-old', '-bak', '-backup', '-copy', '-temp', '-tmp', '-original', '-prev', '-previous',
            '.old', '.bak', '.backup', '.copy', '.temp', '.tmp', '.original', '.prev', '.previous'
        ];
        
        foreach ($patterns as $pattern) {
            $baseName = str_ireplace($pattern, '', $baseName);
        }
        
        // Làm sạch tên file
        $baseName = preg_replace('/[_\-\.]+/', '.', $baseName);
        $baseName = trim($baseName, '._-');
        
        if (!isset($baseNames[$dirname])) {
            $baseNames[$dirname] = [];
        }
        
        if (!isset($baseNames[$dirname][$baseName])) {
            $baseNames[$dirname][$baseName] = [];
        }
        
        $baseNames[$dirname][$baseName][] = $file;
    }
    
    // Tìm các file có nhiều version
    foreach ($baseNames as $dir => $files) {
        foreach ($files as $baseName => $versions) {
            if (count($versions) > 1) {
                $duplicates[$baseName] = $versions;
            }
        }
    }
    
    return $duplicates;
}

echo "🔍 Scanning for blade files...\n";
$bladeFiles = findBladeFiles('resources/views', true);
echo "   Found " . count($bladeFiles) . " blade files\n\n";

echo "🔍 Analyzing backup files...\n\n";

$backupFiles = [];
$duplicateGroups = [];

// Tìm file backup theo pattern
foreach ($bladeFiles as $file) {
    $filename = basename($file);
    
    if (isBackupFile($filename, $backupPatterns)) {
        $backupFiles[] = $file;
        echo "   📦 Backup pattern found: " . str_replace('resources/views/', '', $file) . "\n";
    }
}

// Tìm file duplicate
echo "\n🔍 Analyzing duplicate files...\n";
$duplicates = findDuplicateFiles($bladeFiles);

foreach ($duplicates as $baseName => $versions) {
    echo "\n   📋 Duplicate group for: {$baseName}\n";
    
    $originalFile = null;
    $backupVersions = [];
    
    foreach ($versions as $version) {
        $filename = basename($version);
        echo "      - " . str_replace('resources/views/', '', $version);
        
        if (isBackupFile($filename, $backupPatterns)) {
            $backupVersions[] = $version;
            echo " → BACKUP\n";
        } else {
            if ($originalFile === null) {
                $originalFile = $version;
                echo " → ORIGINAL\n";
            } else {
                // Nếu có nhiều file "original", chọn file mới nhất
                if (filemtime($version) > filemtime($originalFile)) {
                    $backupVersions[] = $originalFile;
                    $originalFile = $version;
                    echo " → NEWER ORIGINAL\n";
                } else {
                    $backupVersions[] = $version;
                    echo " → OLDER VERSION\n";
                }
            }
        }
    }
    
    if (!empty($backupVersions)) {
        $duplicateGroups[$baseName] = [
            'original' => $originalFile,
            'backups' => $backupVersions
        ];
    }
}

// Tổng hợp tất cả file cần di chuyển
$filesToMove = array_unique(array_merge($backupFiles, 
    array_reduce($duplicateGroups, function($carry, $group) {
        return array_merge($carry, $group['backups']);
    }, [])
));

echo "\n📊 Analysis Results:\n";
echo "===================\n";
echo "📦 Backup pattern files: " . count($backupFiles) . "\n";
echo "📋 Duplicate groups: " . count($duplicateGroups) . "\n";
echo "🗂️ Total files to move: " . count($filesToMove) . "\n";

if (count($filesToMove) > 0) {
    echo "\n📦 Moving backup files...\n";
    
    $movedCount = 0;
    foreach ($filesToMove as $file) {
        // Tạo cấu trúc thư mục trong backup
        $relativePath = str_replace('resources/views/', '', $file);
        $backupPath = $backupDir . '/' . $relativePath;
        $backupDirPath = dirname($backupPath);
        
        if (!is_dir($backupDirPath)) {
            mkdir($backupDirPath, 0755, true);
        }
        
        if (file_exists($file) && rename($file, $backupPath)) {
            echo "   ✅ Moved: {$relativePath}\n";
            $movedCount++;
        } else {
            echo "   ❌ Failed to move: {$relativePath}\n";
        }
    }
    
    echo "\n✅ Successfully moved {$movedCount} backup files\n";
} else {
    echo "\n🎉 No backup files found!\n";
}

// Tạo backup report
$reportContent = "# Backup Blade Files Report\n\n";
$reportContent .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
$reportContent .= "## Summary\n";
$reportContent .= "- Total blade files scanned: " . count($bladeFiles) . "\n";
$reportContent .= "- Backup pattern files: " . count($backupFiles) . "\n";
$reportContent .= "- Duplicate groups: " . count($duplicateGroups) . "\n";
$reportContent .= "- Files moved to backup: " . count($filesToMove) . "\n\n";

if (count($backupFiles) > 0) {
    $reportContent .= "## Backup Pattern Files\n";
    foreach ($backupFiles as $file) {
        $relativePath = str_replace('resources/views/', '', $file);
        $reportContent .= "- `{$relativePath}`\n";
    }
    $reportContent .= "\n";
}

if (count($duplicateGroups) > 0) {
    $reportContent .= "## Duplicate Groups\n";
    foreach ($duplicateGroups as $baseName => $group) {
        $reportContent .= "### {$baseName}\n";
        if ($group['original']) {
            $originalPath = str_replace('resources/views/', '', $group['original']);
            $reportContent .= "- **Original**: `{$originalPath}`\n";
        }
        $reportContent .= "- **Backups moved**:\n";
        foreach ($group['backups'] as $backup) {
            $backupPath = str_replace('resources/views/', '', $backup);
            $reportContent .= "  - `{$backupPath}`\n";
        }
        $reportContent .= "\n";
    }
}

if (count($filesToMove) > 0) {
    file_put_contents($backupDir . '/backup_report.md', $reportContent);
    echo "\n📄 Backup report saved to: {$backupDir}/backup_report.md\n";
}

echo "\n🔍 Recommendations:\n";
echo "===================\n";
echo "1. Review the moved files in {$backupDir}/\n";
echo "2. Test your application to ensure nothing is broken\n";
echo "3. If everything works fine, you can delete the backup folder\n";
echo "4. Consider using version control instead of file naming for backups\n";

echo "\n⚠️ Important Notes:\n";
echo "==================\n";
echo "- Only files with clear backup patterns were moved\n";
echo "- Original files were preserved when duplicates were found\n";
echo "- Always test your application after moving files\n";
echo "- Use Git for version control instead of backup file naming\n";

echo "\n✨ Backup blade files cleanup completed!\n";
