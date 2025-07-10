<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--tables=* : Specific tables to backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a safe backup of the database before making changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🛡️  MechaMap Database Backup Tool');
        $this->info('================================');
        $this->newLine();

        try {
            // Test connection
            $this->info('🔄 Kiểm tra kết nối database...');
            DB::connection()->getPdo();
            $this->info('✅ Kết nối database thành công');

            // Create backup directory
            $backupDir = storage_path('backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $backupDir . "/mechamap_backup_{$timestamp}.sql";

            $this->info("📦 Đang tạo backup: mechamap_backup_{$timestamp}.sql");

            // Get tables to backup
            $tables = $this->option('tables');
            if (empty($tables)) {
                $tables = $this->getCriticalTables();
            }

            $this->info('📋 Backup các bảng: ' . implode(', ', $tables));

            // Create backup content
            $backupContent = $this->generateBackupContent($tables);

            // Write to file
            file_put_contents($backupFile, $backupContent);

            // Verify backup
            if (!file_exists($backupFile) || filesize($backupFile) === 0) {
                throw new \Exception('Backup file không được tạo hoặc rỗng');
            }

            $fileSize = $this->formatBytes(filesize($backupFile));
            $this->info("✅ Backup thành công!");
            $this->info("📊 Kích thước: {$fileSize}");
            $this->info("📍 Đường dẫn: {$backupFile}");

            // Clean old backups
            $this->cleanOldBackups($backupDir);

            $this->newLine();
            $this->info('💡 Sử dụng backup này để khôi phục nếu có sự cố trong quá trình implementation.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Backup thất bại: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Get critical tables for backup
     */
    private function getCriticalTables(): array
    {
        $allTables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";

        $criticalTables = [
            'users', 'threads', 'comments', 'forums', 'categories',
            'notifications', 'user_devices', 'showcases', 'marketplace_products',
            'orders', 'roles', 'permissions'
        ];

        $existingTables = [];
        foreach ($allTables as $table) {
            $tableName = $table->$tableKey;
            if (in_array($tableName, $criticalTables)) {
                $existingTables[] = $tableName;
            }
        }

        return $existingTables;
    }

    /**
     * Generate backup content
     */
    private function generateBackupContent(array $tables): string
    {
        $content = [];
        
        // Header
        $content[] = "-- MechaMap Database Backup";
        $content[] = "-- Created: " . date('Y-m-d H:i:s');
        $content[] = "-- Database: " . DB::getDatabaseName();
        $content[] = "-- Laravel Version: " . app()->version();
        $content[] = "-- PHP Version: " . PHP_VERSION;
        $content[] = "-- ";
        $content[] = "-- IMPORTANT: This backup was created before notification system changes";
        $content[] = "-- Use this backup to restore if any issues occur during implementation";
        $content[] = "-- ";
        $content[] = "";
        $content[] = "SET FOREIGN_KEY_CHECKS=0;";
        $content[] = "";

        foreach ($tables as $table) {
            $this->info("  📄 Backup table: {$table}");
            
            // Table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
            $content[] = "-- Structure for table `{$table}`";
            $content[] = "DROP TABLE IF EXISTS `{$table}`;";
            $content[] = $createTable->{'Create Table'} . ";";
            $content[] = "";

            // Table data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $content[] = "-- Data for table `{$table}`";
                
                foreach ($rows as $row) {
                    $values = [];
                    foreach ((array)$row as $value) {
                        if (is_null($value)) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $content[] = "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");";
                }
                $content[] = "";
            }
        }

        $content[] = "SET FOREIGN_KEY_CHECKS=1;";
        $content[] = "";
        $content[] = "-- Backup completed at " . date('Y-m-d H:i:s');

        return implode("\n", $content);
    }

    /**
     * Clean old backups (keep 5 most recent)
     */
    private function cleanOldBackups(string $backupDir): void
    {
        $backupFiles = glob($backupDir . '/mechamap_backup_*.sql');
        
        if (count($backupFiles) > 5) {
            // Sort by modification time (oldest first)
            usort($backupFiles, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Delete old backups, keep 5 most recent
            $filesToDelete = array_slice($backupFiles, 0, -5);
            
            foreach ($filesToDelete as $file) {
                unlink($file);
                $this->info("🗑️  Đã xóa backup cũ: " . basename($file));
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
}
