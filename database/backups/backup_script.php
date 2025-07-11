<?php
/**
 * 🔒 MechaMap Database Backup Script
 *
 * Script để backup database trước khi thực hiện User Registration & Permission System Overhaul
 *
 * Usage: php database/backups/backup_script.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class DatabaseBackup
{
    private $backupPath;
    private $timestamp;

    public function __construct()
    {
        $this->timestamp = date('Y-m-d_H-i-s');
        $this->backupPath = __DIR__ . "/mechamap_backup_{$this->timestamp}";

        if (!is_dir(__DIR__)) {
            mkdir(__DIR__, 0755, true);
        }
    }

    /**
     * Thực hiện backup toàn bộ database
     */
    public function performFullBackup()
    {
        echo "🔄 Bắt đầu backup database MechaMap...\n";
        echo "📅 Timestamp: {$this->timestamp}\n";
        echo "📁 Backup path: {$this->backupPath}\n\n";

        try {
            // 1. Backup structure và data
            $this->backupDatabaseStructure();
            $this->backupCriticalTables();
            $this->backupUserData();
            $this->createRestoreScript();

            echo "\n✅ Backup hoàn thành thành công!\n";
            echo "📁 Files được tạo:\n";
            echo "   - {$this->backupPath}_structure.sql\n";
            echo "   - {$this->backupPath}_users.sql\n";
            echo "   - {$this->backupPath}_critical_data.sql\n";
            echo "   - {$this->backupPath}_restore.php\n\n";

            return true;

        } catch (Exception $e) {
            echo "❌ Lỗi backup: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Backup database structure
     */
    private function backupDatabaseStructure()
    {
        echo "📋 Backup database structure...\n";

        $tables = DB::select('SHOW TABLES');
        $databaseName = config('database.connections.mysql.database');

        $sql = "-- MechaMap Database Structure Backup\n";
        $sql .= "-- Generated: {$this->timestamp}\n";
        $sql .= "-- Database: {$databaseName}\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            try {
                // Get CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Table: {$tableName}\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                // Handle different property names
                $createStatement = '';
                if (isset($createTable[0]->{'Create Table'})) {
                    $createStatement = $createTable[0]->{'Create Table'};
                } elseif (isset($createTable[0]->{'Create View'})) {
                    $createStatement = $createTable[0]->{'Create View'};
                } else {
                    // Fallback: get all properties
                    $props = get_object_vars($createTable[0]);
                    $createStatement = array_values($props)[1] ?? '';
                }

                $sql .= $createStatement . ";\n\n";

            } catch (Exception $e) {
                echo "   ⚠️  Warning: Could not backup table {$tableName}: " . $e->getMessage() . "\n";
                $sql .= "-- WARNING: Could not backup table {$tableName}\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        file_put_contents($this->backupPath . '_structure.sql', $sql);
        echo "   ✅ Structure backup completed\n";
    }

    /**
     * Backup critical tables data
     */
    private function backupCriticalTables()
    {
        echo "🔑 Backup critical tables data...\n";

        $criticalTables = [
            'users',
            'roles',
            'permissions',
            'role_has_permissions',
            'user_has_roles',
            'categories',
            'forums',
            'threads',
            'comments',
            'showcases',
            'marketplace_products',
            'marketplace_sellers'
        ];

        $sql = "-- MechaMap Critical Data Backup\n";
        $sql .= "-- Generated: {$this->timestamp}\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($criticalTables as $table) {
            if (Schema::hasTable($table)) {
                echo "   📊 Backing up table: {$table}\n";

                $data = DB::table($table)->get();

                if ($data->count() > 0) {
                    $sql .= "-- Data for table: {$table}\n";
                    $sql .= "DELETE FROM `{$table}`;\n";

                    foreach ($data as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }

                        $columns = implode('`, `', array_keys((array)$row));
                        $valueString = implode(', ', $values);

                        $sql .= "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$valueString});\n";
                    }
                    $sql .= "\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        file_put_contents($this->backupPath . '_critical_data.sql', $sql);
        echo "   ✅ Critical data backup completed\n";
    }

    /**
     * Backup user data separately
     */
    private function backupUserData()
    {
        echo "👥 Backup user data...\n";

        $users = DB::table('users')->get();

        $sql = "-- MechaMap Users Data Backup\n";
        $sql .= "-- Generated: {$this->timestamp}\n";
        $sql .= "-- Total users: " . $users->count() . "\n\n";

        $sql .= "-- User statistics:\n";
        $roleStats = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();

        foreach ($roleStats as $stat) {
            $sql .= "-- {$stat->role}: {$stat->count} users\n";
        }
        $sql .= "\n";

        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $sql .= "DELETE FROM `users`;\n\n";

        foreach ($users as $user) {
            $values = [];
            foreach ((array)$user as $value) {
                if (is_null($value)) {
                    $values[] = 'NULL';
                } else {
                    $values[] = "'" . addslashes($value) . "'";
                }
            }

            $columns = implode('`, `', array_keys((array)$user));
            $valueString = implode(', ', $values);

            $sql .= "INSERT INTO `users` (`{$columns}`) VALUES ({$valueString});\n";
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";

        file_put_contents($this->backupPath . '_users.sql', $sql);
        echo "   ✅ User data backup completed ({$users->count()} users)\n";
    }

    /**
     * Tạo restore script
     */
    private function createRestoreScript()
    {
        echo "🔧 Creating restore script...\n";

        $restoreScript = '<?php
/**
 * 🔄 MechaMap Database Restore Script
 * Generated: ' . $this->timestamp . '
 */

require_once __DIR__ . "/../../vendor/autoload.php";

$app = require_once __DIR__ . "/../../bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Restoring MechaMap database from backup ' . $this->timestamp . '...\n";

try {
    // Restore structure
    echo "📋 Restoring database structure...\n";
    $structureSql = file_get_contents(__DIR__ . "/mechamap_backup_' . $this->timestamp . '_structure.sql");
    DB::unprepared($structureSql);

    // Restore critical data
    echo "🔑 Restoring critical data...\n";
    $criticalSql = file_get_contents(__DIR__ . "/mechamap_backup_' . $this->timestamp . '_critical_data.sql");
    DB::unprepared($criticalSql);

    // Restore users
    echo "👥 Restoring user data...\n";
    $usersSql = file_get_contents(__DIR__ . "/mechamap_backup_' . $this->timestamp . '_users.sql");
    DB::unprepared($usersSql);

    echo "✅ Database restore completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Restore failed: " . $e->getMessage() . "\n";
    exit(1);
}
';

        file_put_contents($this->backupPath . '_restore.php', $restoreScript);
        echo "   ✅ Restore script created\n";
    }
}

// Thực hiện backup
$backup = new DatabaseBackup();
$success = $backup->performFullBackup();

if ($success) {
    echo "🎉 Backup process completed successfully!\n";
    echo "💡 Để restore, chạy: php database/backups/mechamap_backup_" . date('Y-m-d_H-i-s') . "_restore.php\n";
    exit(0);
} else {
    echo "💥 Backup process failed!\n";
    exit(1);
}
