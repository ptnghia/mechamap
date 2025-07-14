<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class UuidMigrationHelper
{
    /**
     * Backup table data before migration
     */
    public static function backupTable(string $tableName): string
    {
        $backupTableName = $tableName . '_uuid_backup_' . date('Ymd_His');
        
        // Create backup table with same structure
        DB::statement("CREATE TABLE `{$backupTableName}` LIKE `{$tableName}`");
        
        // Copy all data
        DB::statement("INSERT INTO `{$backupTableName}` SELECT * FROM `{$tableName}`");
        
        echo "✅ Backup created: {$backupTableName}\n";
        return $backupTableName;
    }

    /**
     * Convert UUID column from native uuid to CHAR(36)
     */
    public static function convertUuidColumn(string $tableName, string $columnName = 'uuid'): void
    {
        echo "🔄 Converting {$tableName}.{$columnName} from uuid to CHAR(36)...\n";
        
        // Step 1: Check if column exists and is uuid type
        $columnInfo = DB::select("
            SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
        ", [DB::getDatabaseName(), $tableName, $columnName]);

        if (empty($columnInfo)) {
            throw new \Exception("Column {$columnName} not found in table {$tableName}");
        }

        $column = $columnInfo[0];
        
        // Step 2: Drop unique constraint if exists
        $hasUniqueKey = $column->COLUMN_KEY === 'UNI';
        if ($hasUniqueKey) {
            echo "  📝 Dropping unique constraint...\n";
            // Find constraint name
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
                AND CONSTRAINT_NAME != 'PRIMARY'
            ", [DB::getDatabaseName(), $tableName, $columnName]);
            
            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE `{$tableName}` DROP INDEX `{$constraint->CONSTRAINT_NAME}`");
            }
        }

        // Step 3: Convert column type
        $nullable = $column->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
        echo "  🔧 Changing column type to CHAR(36)...\n";
        
        DB::statement("ALTER TABLE `{$tableName}` MODIFY COLUMN `{$columnName}` CHAR(36) {$nullable}");

        // Step 4: Re-add unique constraint if it existed
        if ($hasUniqueKey) {
            echo "  📝 Re-adding unique constraint...\n";
            DB::statement("ALTER TABLE `{$tableName}` ADD UNIQUE KEY `{$tableName}_{$columnName}_unique` (`{$columnName}`)");
        }

        echo "✅ Successfully converted {$tableName}.{$columnName}\n";
    }

    /**
     * Verify data integrity after migration
     */
    public static function verifyDataIntegrity(string $tableName, string $backupTableName, string $columnName = 'uuid'): bool
    {
        echo "🔍 Verifying data integrity for {$tableName}...\n";
        
        // Check record count
        $originalCount = DB::table($backupTableName)->count();
        $newCount = DB::table($tableName)->count();
        
        if ($originalCount !== $newCount) {
            echo "❌ Record count mismatch: {$originalCount} vs {$newCount}\n";
            return false;
        }

        // Check UUID values are preserved (only if there are records)
        if ($originalCount > 0) {
            $mismatchCount = DB::select("
                SELECT COUNT(*) as count FROM (
                    SELECT o.{$columnName} as original_uuid, n.{$columnName} as new_uuid
                    FROM `{$backupTableName}` o
                    JOIN `{$tableName}` n ON o.id = n.id
                    WHERE o.{$columnName} != n.{$columnName}
                ) as mismatches
            ")[0]->count;

            if ($mismatchCount > 0) {
                echo "❌ UUID value mismatch found: {$mismatchCount} records\n";
                return false;
            }

            // Check for NULL values where they shouldn't be
            $nullCount = DB::table($tableName)->whereNull($columnName)->count();
            $originalNullCount = DB::table($backupTableName)->whereNull($columnName)->count();
            
            if ($nullCount !== $originalNullCount) {
                echo "❌ NULL value count mismatch: {$originalNullCount} vs {$nullCount}\n";
                return false;
            }
        }

        echo "✅ Data integrity verified successfully\n";
        return true;
    }

    /**
     * Rollback migration if needed
     */
    public static function rollbackMigration(string $tableName, string $backupTableName): void
    {
        echo "🔄 Rolling back migration for {$tableName}...\n";
        
        // Drop current table
        Schema::dropIfExists($tableName);
        
        // Rename backup table back to original
        DB::statement("RENAME TABLE `{$backupTableName}` TO `{$tableName}`");
        
        echo "✅ Rollback completed for {$tableName}\n";
    }

    /**
     * Cleanup backup table after successful migration
     */
    public static function cleanupBackup(string $backupTableName): void
    {
        echo "🧹 Cleaning up backup table {$backupTableName}...\n";
        Schema::dropIfExists($backupTableName);
        echo "✅ Backup table cleaned up\n";
    }

    /**
     * Get table statistics
     */
    public static function getTableStats(string $tableName): array
    {
        $stats = DB::select("
            SELECT 
                COUNT(*) as record_count,
                COUNT(uuid) as uuid_count,
                COUNT(*) - COUNT(uuid) as null_uuid_count
            FROM `{$tableName}`
        ")[0];

        return [
            'record_count' => $stats->record_count,
            'uuid_count' => $stats->uuid_count,
            'null_uuid_count' => $stats->null_uuid_count
        ];
    }

    /**
     * Validate UUID format
     */
    public static function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
}
