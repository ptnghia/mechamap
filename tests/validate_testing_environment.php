<?php
/**
 * 🧪 Testing Environment Validation Script
 * 
 * Script để validate testing environment setup
 */

require_once __DIR__ . '/../vendor/autoload.php';

class TestingEnvironmentValidator
{
    private $errors = [];
    private $warnings = [];
    private $passed = [];
    
    public function validate()
    {
        echo "🧪 Validating MechaMap Testing Environment...\n\n";
        
        $this->checkPHPEnvironment();
        $this->checkLaravelEnvironment();
        $this->checkTestingConfiguration();
        $this->checkFileStructure();
        $this->checkBackupIntegration();
        $this->runBasicTests();
        
        $this->displayResults();
        
        return empty($this->errors);
    }
    
    private function checkPHPEnvironment()
    {
        echo "🔍 Checking PHP Environment...\n";
        
        // PHP Version
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            $this->passed[] = "PHP Version: " . PHP_VERSION;
        } else {
            $this->errors[] = "PHP Version too old: " . PHP_VERSION . " (requires 8.1+)";
        }
        
        // Required Extensions
        $requiredExtensions = ['pdo', 'pdo_sqlite', 'mbstring', 'openssl', 'json'];
        foreach ($requiredExtensions as $ext) {
            if (extension_loaded($ext)) {
                $this->passed[] = "Extension loaded: {$ext}";
            } else {
                $this->errors[] = "Missing extension: {$ext}";
            }
        }
        
        // Optional Extensions
        $optionalExtensions = ['xdebug', 'pcov'];
        foreach ($optionalExtensions as $ext) {
            if (extension_loaded($ext)) {
                $this->passed[] = "Optional extension loaded: {$ext}";
            } else {
                $this->warnings[] = "Optional extension not loaded: {$ext} (for coverage)";
            }
        }
    }
    
    private function checkLaravelEnvironment()
    {
        echo "🏗️  Checking Laravel Environment...\n";
        
        // Laravel Classes
        $laravelClasses = [
            'Illuminate\Foundation\Application',
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Support\Facades\DB',
            'Illuminate\Foundation\Testing\TestCase'
        ];
        
        foreach ($laravelClasses as $class) {
            if (class_exists($class)) {
                $this->passed[] = "Laravel class available: {$class}";
            } else {
                $this->errors[] = "Laravel class missing: {$class}";
            }
        }
        
        // Composer autoload
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            $this->passed[] = "Composer autoload available";
        } else {
            $this->errors[] = "Composer autoload missing - run 'composer install'";
        }
    }
    
    private function checkTestingConfiguration()
    {
        echo "⚙️  Checking Testing Configuration...\n";
        
        // PHPUnit configuration
        $phpunitPath = __DIR__ . '/../phpunit.xml';
        if (file_exists($phpunitPath)) {
            $this->passed[] = "PHPUnit configuration exists";
            
            $content = file_get_contents($phpunitPath);
            if (strpos($content, 'APP_ENV') !== false) {
                $this->passed[] = "PHPUnit has environment configuration";
            } else {
                $this->warnings[] = "PHPUnit missing environment configuration";
            }
        } else {
            $this->errors[] = "PHPUnit configuration missing";
        }
        
        // Environment variables
        $envVars = [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'CACHE_STORE' => 'array',
            'MAIL_MAILER' => 'array'
        ];
        
        foreach ($envVars as $var => $expected) {
            $actual = $_ENV[$var] ?? 'not set';
            if ($actual === $expected) {
                $this->passed[] = "Environment variable {$var} = {$expected}";
            } else {
                $this->warnings[] = "Environment variable {$var} = {$actual} (expected: {$expected})";
            }
        }
    }
    
    private function checkFileStructure()
    {
        echo "📁 Checking File Structure...\n";
        
        // Required directories
        $requiredDirs = [
            __DIR__ . '/../app',
            __DIR__ . '/../database',
            __DIR__ . '/../tests',
            __DIR__ . '/../tests/Unit',
            __DIR__ . '/../tests/Feature',
            __DIR__ . '/../database/factories',
            __DIR__ . '/../database/seeders'
        ];
        
        foreach ($requiredDirs as $dir) {
            if (is_dir($dir)) {
                $this->passed[] = "Directory exists: " . basename($dir);
            } else {
                $this->errors[] = "Directory missing: " . basename($dir);
            }
        }
        
        // Required files
        $requiredFiles = [
            __DIR__ . '/TestCase.php' => 'Enhanced TestCase',
            __DIR__ . '/../database/factories/UserFactory.php' => 'User Factory',
            __DIR__ . '/../database/seeders/TestDataSeeder.php' => 'Test Data Seeder',
            __DIR__ . '/Unit/TestEnvironmentSetupTest.php' => 'Environment Test',
            __DIR__ . '/README.md' => 'Testing Documentation'
        ];
        
        foreach ($requiredFiles as $file => $description) {
            if (file_exists($file)) {
                $this->passed[] = "{$description} exists";
            } else {
                $this->errors[] = "{$description} missing";
            }
        }
    }
    
    private function checkBackupIntegration()
    {
        echo "🔒 Checking Backup Integration...\n";
        
        $backupDir = __DIR__ . '/../database/backups';
        if (is_dir($backupDir)) {
            $this->passed[] = "Backup directory exists";
            
            // Check for backup files
            $backupFiles = glob($backupDir . '/mechamap_backup_*');
            if (count($backupFiles) > 0) {
                $this->passed[] = "Backup files exist (" . count($backupFiles) . " files)";
            } else {
                $this->warnings[] = "No backup files found - run task 0.1 first";
            }
            
            // Check for backup script
            if (file_exists($backupDir . '/backup_script.php')) {
                $this->passed[] = "Backup script exists";
            } else {
                $this->errors[] = "Backup script missing";
            }
        } else {
            $this->errors[] = "Backup directory missing";
        }
        
        // Check migration plan
        $migrationPlan = __DIR__ . '/../docs/migration-plan.md';
        if (file_exists($migrationPlan)) {
            $this->passed[] = "Migration plan exists";
        } else {
            $this->errors[] = "Migration plan missing";
        }
    }
    
    private function runBasicTests()
    {
        echo "🧪 Running Basic Tests...\n";
        
        // Run the environment test
        $testCommand = 'cd ' . __DIR__ . '/.. && php artisan test tests/Unit/TestEnvironmentSetupTest.php --no-coverage 2>&1';
        $output = shell_exec($testCommand);
        
        if ($output && strpos($output, 'PASS') !== false) {
            $this->passed[] = "Basic tests passing";
        } else {
            $this->warnings[] = "Basic tests may have issues - check manually";
        }
    }
    
    private function displayResults()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🧪 TESTING ENVIRONMENT VALIDATION RESULTS\n";
        echo str_repeat("=", 60) . "\n\n";
        
        if (!empty($this->passed)) {
            echo "✅ PASSED (" . count($this->passed) . " items):\n";
            foreach ($this->passed as $item) {
                echo "   ✓ {$item}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️  WARNINGS (" . count($this->warnings) . " items):\n";
            foreach ($this->warnings as $item) {
                echo "   ⚠ {$item}\n";
            }
            echo "\n";
        }
        
        if (!empty($this->errors)) {
            echo "❌ ERRORS (" . count($this->errors) . " items):\n";
            foreach ($this->errors as $item) {
                echo "   ✗ {$item}\n";
            }
            echo "\n";
        }
        
        // Summary
        $total = count($this->passed) + count($this->warnings) + count($this->errors);
        $passRate = round((count($this->passed) / $total) * 100, 1);
        
        echo "📊 SUMMARY:\n";
        echo "   Total Checks: {$total}\n";
        echo "   Passed: " . count($this->passed) . "\n";
        echo "   Warnings: " . count($this->warnings) . "\n";
        echo "   Errors: " . count($this->errors) . "\n";
        echo "   Pass Rate: {$passRate}%\n\n";
        
        if (empty($this->errors)) {
            echo "🎉 Testing environment is ready!\n";
            echo "💡 You can now proceed with Phase 1 development.\n";
        } else {
            echo "🔧 Please fix the errors before proceeding.\n";
            echo "💡 Run this script again after fixing issues.\n";
        }
    }
}

// Run validation
$validator = new TestingEnvironmentValidator();
$success = $validator->validate();

exit($success ? 0 : 1);
