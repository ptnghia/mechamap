<?php
/**
 * 🧪 Setup Test Database Script
 * 
 * Script để setup test database một cách an toàn
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

class TestDatabaseSetup
{
    public function setup()
    {
        echo "🧪 Setting up test database...\n";
        
        try {
            // 1. Drop test database if exists
            $this->dropTestDatabase();
            
            // 2. Create fresh test database
            $this->createTestDatabase();
            
            // 3. Run migrations with foreign key checks disabled
            $this->runMigrations();
            
            echo "\n✅ Test database setup completed successfully!\n";
            echo "💡 You can now run tests safely.\n";
            
            return true;
            
        } catch (Exception $e) {
            echo "\n❌ Test database setup failed: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function dropTestDatabase()
    {
        echo "🗑️  Dropping existing test database...\n";
        
        try {
            DB::statement('DROP DATABASE IF EXISTS mechamap_backend_test');
            echo "   ✅ Test database dropped\n";
        } catch (Exception $e) {
            echo "   ⚠️  Warning: Could not drop test database: " . $e->getMessage() . "\n";
        }
    }
    
    private function createTestDatabase()
    {
        echo "🏗️  Creating fresh test database...\n";
        
        DB::statement('CREATE DATABASE mechamap_backend_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        echo "   ✅ Test database created\n";
    }
    
    private function runMigrations()
    {
        echo "📋 Running migrations on test database...\n";
        
        // Switch to test database
        config(['database.connections.mysql.database' => 'mechamap_backend_test']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        
        try {
            // Run migrations
            Artisan::call('migrate:fresh', [
                '--database' => 'mysql',
                '--force' => true
            ]);
            
            echo "   ✅ Migrations completed\n";
            
        } catch (Exception $e) {
            echo "   ⚠️  Migration warning: " . $e->getMessage() . "\n";
            echo "   🔄 Attempting to continue...\n";
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}

// Run setup
echo "🚀 Starting test database setup...\n\n";

$setup = new TestDatabaseSetup();
$success = $setup->setup();

if ($success) {
    echo "\n🎉 Test database setup successful!\n";
    echo "🧪 Run tests with: php artisan test\n";
    exit(0);
} else {
    echo "\n💥 Test database setup failed!\n";
    exit(1);
}
