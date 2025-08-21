<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Helpers\UuidMigrationHelper;

return new class extends Migration
{
    private string $tableName = 'marketplace_download_history';
    private string $backupTableName = '';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🚀 Starting UUID migration for {$this->tableName}...\n";

        try {
            // Step 1: Create backup
            $this->backupTableName = UuidMigrationHelper::backupTable($this->tableName);

            // Step 2: Convert UUID column from native uuid to CHAR(36)
            UuidMigrationHelper::convertUuidColumn($this->tableName, 'uuid');

            // Step 3: Verify data integrity
            $isValid = UuidMigrationHelper::verifyDataIntegrity(
                $this->tableName, 
                $this->backupTableName, 
                'uuid'
            );

            if (!$isValid) {
                throw new \Exception("Data integrity check failed for {$this->tableName}");
            }

            echo "✅ UUID migration completed successfully for {$this->tableName}\n";

        } catch (\Exception $e) {
            echo "❌ Migration failed for {$this->tableName}: " . $e->getMessage() . "\n";
            
            // Rollback if backup exists
            if (!empty($this->backupTableName)) {
                echo "🔄 Rolling back changes...\n";
                UuidMigrationHelper::rollbackMigration($this->tableName, $this->backupTableName);
            }
            
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        echo "🔄 Reversing UUID migration for {$this->tableName}...\n";

        try {
            // Drop unique constraint first
            DB::statement("ALTER TABLE `{$this->tableName}` DROP INDEX `{$this->tableName}_uuid_unique`");

            // Change column type back to uuid
            DB::statement("ALTER TABLE `{$this->tableName}` MODIFY COLUMN `uuid` UUID NOT NULL");

            // Re-add unique constraint
            DB::statement("ALTER TABLE `{$this->tableName}` ADD UNIQUE KEY `{$this->tableName}_uuid_unique` (`uuid`)");

            echo "✅ UUID migration reversed successfully for {$this->tableName}\n";

        } catch (\Exception $e) {
            echo "❌ Migration reversal failed for {$this->tableName}: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
};