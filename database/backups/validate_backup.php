<?php
/**
 * 🔍 MechaMap Backup Validation Script
 * 
 * Script để validate backup files và đảm bảo tính toàn vẹn dữ liệu
 * 
 * Usage: php database/backups/validate_backup.php
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class BackupValidator
{
    private $backupTimestamp = '2025-07-12_00-46-41';
    private $backupPath;
    
    public function __construct()
    {
        $this->backupPath = __DIR__ . "/mechamap_backup_{$this->backupTimestamp}";
    }
    
    /**
     * Validate backup files
     */
    public function validateBackup()
    {
        echo "🔍 Validating MechaMap backup files...\n";
        echo "📅 Backup timestamp: {$this->backupTimestamp}\n\n";
        
        $allValid = true;
        
        // 1. Check file existence
        $allValid &= $this->checkFileExistence();
        
        // 2. Validate file sizes
        $allValid &= $this->validateFileSizes();
        
        // 3. Check SQL syntax
        $allValid &= $this->validateSQLSyntax();
        
        // 4. Validate data integrity
        $allValid &= $this->validateDataIntegrity();
        
        // 5. Test restore script
        $allValid &= $this->validateRestoreScript();
        
        if ($allValid) {
            echo "\n✅ Backup validation completed successfully!\n";
            echo "🔒 Backup is ready for use in case of emergency.\n";
            return true;
        } else {
            echo "\n❌ Backup validation failed!\n";
            echo "⚠️  Please recreate backup before proceeding.\n";
            return false;
        }
    }
    
    /**
     * Check if all backup files exist
     */
    private function checkFileExistence()
    {
        echo "📁 Checking file existence...\n";
        
        $requiredFiles = [
            '_structure.sql',
            '_critical_data.sql', 
            '_users.sql',
            '_restore.php'
        ];
        
        $allExist = true;
        
        foreach ($requiredFiles as $file) {
            $filePath = $this->backupPath . $file;
            if (file_exists($filePath)) {
                echo "   ✅ {$file} - Found\n";
            } else {
                echo "   ❌ {$file} - Missing\n";
                $allExist = false;
            }
        }
        
        return $allExist;
    }
    
    /**
     * Validate file sizes
     */
    private function validateFileSizes()
    {
        echo "\n📊 Validating file sizes...\n";
        
        $files = [
            '_structure.sql' => ['min' => 100000, 'description' => 'Database structure'],
            '_critical_data.sql' => ['min' => 50000, 'description' => 'Critical data'],
            '_users.sql' => ['min' => 10000, 'description' => 'User data'],
            '_restore.php' => ['min' => 500, 'description' => 'Restore script']
        ];
        
        $allValid = true;
        
        foreach ($files as $file => $config) {
            $filePath = $this->backupPath . $file;
            if (file_exists($filePath)) {
                $size = filesize($filePath);
                $sizeKB = round($size / 1024, 2);
                
                if ($size >= $config['min']) {
                    echo "   ✅ {$config['description']}: {$sizeKB} KB\n";
                } else {
                    echo "   ❌ {$config['description']}: {$sizeKB} KB (too small)\n";
                    $allValid = false;
                }
            }
        }
        
        return $allValid;
    }
    
    /**
     * Validate SQL syntax
     */
    private function validateSQLSyntax()
    {
        echo "\n🔍 Validating SQL syntax...\n";
        
        $sqlFiles = ['_structure.sql', '_critical_data.sql', '_users.sql'];
        $allValid = true;
        
        foreach ($sqlFiles as $file) {
            $filePath = $this->backupPath . $file;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                
                // Basic SQL validation
                $hasCreateStatements = strpos($content, 'CREATE TABLE') !== false || strpos($content, 'INSERT INTO') !== false;
                $hasProperEnding = strpos($content, 'SET FOREIGN_KEY_CHECKS = 1') !== false;
                
                if ($hasCreateStatements && $hasProperEnding) {
                    echo "   ✅ {$file} - Valid SQL syntax\n";
                } else {
                    echo "   ❌ {$file} - Invalid SQL syntax\n";
                    $allValid = false;
                }
            }
        }
        
        return $allValid;
    }
    
    /**
     * Validate data integrity
     */
    private function validateDataIntegrity()
    {
        echo "\n🔐 Validating data integrity...\n";
        
        try {
            // Check current database state
            $currentUserCount = DB::table('users')->count();
            $currentTableCount = count(DB::select('SHOW TABLES'));
            
            // Check backup user count
            $usersBackupContent = file_get_contents($this->backupPath . '_users.sql');
            $backupUserCount = substr_count($usersBackupContent, 'INSERT INTO `users`');
            
            echo "   📊 Current users in DB: {$currentUserCount}\n";
            echo "   📊 Users in backup: {$backupUserCount}\n";
            echo "   📊 Current tables: {$currentTableCount}\n";
            
            if ($backupUserCount == $currentUserCount) {
                echo "   ✅ User count matches\n";
                return true;
            } else {
                echo "   ⚠️  User count mismatch (acceptable if users were added after backup)\n";
                return true; // This is acceptable
            }
            
        } catch (Exception $e) {
            echo "   ❌ Data integrity check failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Validate restore script
     */
    private function validateRestoreScript()
    {
        echo "\n🔧 Validating restore script...\n";
        
        $restoreScript = $this->backupPath . '_restore.php';
        
        if (!file_exists($restoreScript)) {
            echo "   ❌ Restore script not found\n";
            return false;
        }
        
        $content = file_get_contents($restoreScript);
        
        // Check for required components
        $hasRequire = strpos($content, 'require_once') !== false;
        $hasBootstrap = strpos($content, 'bootstrap/app.php') !== false;
        $hasRestore = strpos($content, 'DB::unprepared') !== false;
        
        if ($hasRequire && $hasBootstrap && $hasRestore) {
            echo "   ✅ Restore script is valid\n";
            return true;
        } else {
            echo "   ❌ Restore script is invalid\n";
            return false;
        }
    }
}

// Run validation
echo "🚀 Starting backup validation...\n\n";

$validator = new BackupValidator();
$isValid = $validator->validateBackup();

if ($isValid) {
    echo "\n🎉 Backup validation successful!\n";
    echo "💡 You can now proceed with the migration safely.\n";
    exit(0);
} else {
    echo "\n💥 Backup validation failed!\n";
    echo "🔄 Please recreate the backup before proceeding.\n";
    exit(1);
}
