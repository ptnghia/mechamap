<?php

/**
 * MechaMap Database Backup Script
 * 
 * Tạo backup an toàn cho database trước khi thực hiện thay đổi
 * Usage: php scripts/backup_database.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

// Load Laravel configuration
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class DatabaseBackup
{
    private $backupDir;
    private $timestamp;
    private $dbConfig;

    public function __construct()
    {
        $this->timestamp = date('Y-m-d_H-i-s');
        $this->backupDir = __DIR__ . '/../storage/backups';
        $this->dbConfig = config('database.connections.mysql');
        
        // Tạo thư mục backup nếu chưa có
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Tạo backup database
     */
    public function createBackup(): string
    {
        echo "🔄 Bắt đầu backup database MechaMap...\n";
        
        $backupFile = $this->backupDir . "/mechamap_backup_{$this->timestamp}.sql";
        
        try {
            // Kiểm tra kết nối database
            $this->testConnection();
            
            // Tạo backup command
            $command = $this->buildMysqldumpCommand($backupFile);
            
            echo "📦 Đang tạo backup: " . basename($backupFile) . "\n";
            
            // Thực hiện backup
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new Exception("Mysqldump failed with return code: $returnCode\n" . implode("\n", $output));
            }
            
            // Kiểm tra file backup
            if (!file_exists($backupFile) || filesize($backupFile) === 0) {
                throw new Exception("Backup file không được tạo hoặc rỗng");
            }
            
            // Thêm metadata vào đầu file
            $this->addMetadata($backupFile);
            
            // Nén file backup
            $compressedFile = $this->compressBackup($backupFile);
            
            echo "✅ Backup thành công: " . basename($compressedFile) . "\n";
            echo "📊 Kích thước: " . $this->formatBytes(filesize($compressedFile)) . "\n";
            echo "📍 Đường dẫn: $compressedFile\n";
            
            // Xóa file SQL gốc sau khi nén
            unlink($backupFile);
            
            // Dọn dẹp backup cũ
            $this->cleanOldBackups();
            
            return $compressedFile;
            
        } catch (Exception $e) {
            echo "❌ Lỗi backup: " . $e->getMessage() . "\n";
            
            // Xóa file backup lỗi nếu có
            if (file_exists($backupFile)) {
                unlink($backupFile);
            }
            
            throw $e;
        }
    }

    /**
     * Test kết nối database
     */
    private function testConnection(): void
    {
        try {
            DB::connection()->getPdo();
            echo "✅ Kết nối database thành công\n";
        } catch (Exception $e) {
            throw new Exception("Không thể kết nối database: " . $e->getMessage());
        }
    }

    /**
     * Tạo mysqldump command
     */
    private function buildMysqldumpCommand(string $backupFile): string
    {
        $host = $this->dbConfig['host'];
        $port = $this->dbConfig['port'] ?? 3306;
        $database = $this->dbConfig['database'];
        $username = $this->dbConfig['username'];
        $password = $this->dbConfig['password'];

        // Escape password để tránh lỗi với special characters
        $escapedPassword = escapeshellarg($password);

        return sprintf(
            'mysqldump --host=%s --port=%d --user=%s --password=%s --single-transaction --routines --triggers --add-drop-table --extended-insert --quick --lock-tables=false %s > %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            $escapedPassword,
            escapeshellarg($database),
            escapeshellarg($backupFile)
        );
    }

    /**
     * Thêm metadata vào backup file
     */
    private function addMetadata(string $backupFile): void
    {
        $metadata = [
            "-- MechaMap Database Backup",
            "-- Created: " . date('Y-m-d H:i:s'),
            "-- Database: " . $this->dbConfig['database'],
            "-- Laravel Version: " . app()->version(),
            "-- PHP Version: " . PHP_VERSION,
            "-- Backup Script Version: 1.0",
            "-- ",
            "-- IMPORTANT: This backup was created before notification system changes",
            "-- Use this backup to restore if any issues occur during implementation",
            "-- ",
            ""
        ];

        $content = file_get_contents($backupFile);
        $contentWithMetadata = implode("\n", $metadata) . "\n" . $content;
        file_put_contents($backupFile, $contentWithMetadata);
    }

    /**
     * Nén backup file
     */
    private function compressBackup(string $backupFile): string
    {
        $compressedFile = $backupFile . '.gz';
        
        $fp_out = gzopen($compressedFile, 'wb9');
        $fp_in = fopen($backupFile, 'rb');
        
        if (!$fp_out || !$fp_in) {
            throw new Exception("Không thể nén backup file");
        }
        
        while (!feof($fp_in)) {
            gzwrite($fp_out, fread($fp_in, 1024 * 512));
        }
        
        fclose($fp_in);
        gzclose($fp_out);
        
        return $compressedFile;
    }

    /**
     * Dọn dẹp backup cũ (giữ lại 5 backup gần nhất)
     */
    private function cleanOldBackups(): void
    {
        $backupFiles = glob($this->backupDir . '/mechamap_backup_*.sql.gz');
        
        if (count($backupFiles) > 5) {
            // Sắp xếp theo thời gian (cũ nhất trước)
            usort($backupFiles, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Xóa các backup cũ, chỉ giữ lại 5 cái mới nhất
            $filesToDelete = array_slice($backupFiles, 0, -5);
            
            foreach ($filesToDelete as $file) {
                unlink($file);
                echo "🗑️  Đã xóa backup cũ: " . basename($file) . "\n";
            }
        }
    }

    /**
     * Format file size
     */
    private function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Liệt kê các backup có sẵn
     */
    public function listBackups(): array
    {
        $backupFiles = glob($this->backupDir . '/mechamap_backup_*.sql.gz');
        $backups = [];
        
        foreach ($backupFiles as $file) {
            $backups[] = [
                'file' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'created' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        
        // Sắp xếp theo thời gian (mới nhất trước)
        usort($backups, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });
        
        return $backups;
    }
}

// Chạy backup nếu script được gọi trực tiếp
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $backup = new DatabaseBackup();
        
        echo "🛡️  MechaMap Database Backup Tool\n";
        echo "================================\n\n";
        
        // Tạo backup
        $backupFile = $backup->createBackup();
        
        echo "\n📋 Danh sách backup hiện có:\n";
        $backups = $backup->listBackups();
        
        foreach ($backups as $index => $backup) {
            echo sprintf(
                "%d. %s (%s) - %s\n",
                $index + 1,
                $backup['file'],
                $backup['size'],
                $backup['created']
            );
        }
        
        echo "\n✅ Backup hoàn tất! Database đã được bảo vệ an toàn.\n";
        echo "💡 Sử dụng backup này để khôi phục nếu có sự cố trong quá trình implementation.\n";
        
    } catch (Exception $e) {
        echo "\n❌ Backup thất bại: " . $e->getMessage() . "\n";
        exit(1);
    }
}
