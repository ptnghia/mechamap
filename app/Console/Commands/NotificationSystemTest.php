<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\NotificationLocalizationService;
use App\Services\NotificationTargetingService;
use App\Services\NotificationPerformanceService;
use App\Services\NotificationCacheOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NotificationSystemTest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:system-test 
                            {--comprehensive : Run comprehensive test suite}
                            {--integration : Run integration tests}
                            {--end-to-end : Run end-to-end tests}
                            {--production-ready : Check production readiness}
                            {--fix-issues : Automatically fix found issues}';

    /**
     * The console command description.
     */
    protected $description = 'Comprehensive notification system testing and validation';

    /**
     * Test results storage
     */
    private array $testResults = [];
    private int $passedTests = 0;
    private int $failedTests = 0;
    private array $issues = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 MechaMap Notification System - Comprehensive Testing');
        $this->newLine();

        if ($this->option('comprehensive')) {
            return $this->runComprehensiveTests();
        }

        if ($this->option('integration')) {
            return $this->runIntegrationTests();
        }

        if ($this->option('end-to-end')) {
            return $this->runEndToEndTests();
        }

        if ($this->option('production-ready')) {
            return $this->checkProductionReadiness();
        }

        // Default: run all tests
        return $this->runAllTests();
    }

    /**
     * Run all tests
     */
    private function runAllTests(): int
    {
        $this->info('🔥 Running Complete Test Suite');
        $this->newLine();

        // 1. Unit Tests
        $this->runUnitTests();
        
        // 2. Integration Tests
        $this->runIntegrationTests();
        
        // 3. End-to-End Tests
        $this->runEndToEndTests();
        
        // 4. Performance Tests
        $this->runPerformanceValidation();
        
        // 5. Production Readiness Check
        $this->checkProductionReadiness();
        
        // 6. Generate Final Report
        $this->generateFinalReport();

        return $this->failedTests > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Run comprehensive tests
     */
    private function runComprehensiveTests(): int
    {
        $this->info('🔬 Running Comprehensive Test Suite');
        $this->newLine();

        $this->runUnitTests();
        $this->runIntegrationTests();
        $this->runPerformanceValidation();
        
        $this->generateTestReport();
        return $this->failedTests > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Run unit tests
     */
    private function runUnitTests(): void
    {
        $this->info('1️⃣ Unit Tests');
        $this->newLine();

        // Test notification creation
        $this->test('Notification Creation', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'unit_test',
                'title' => 'Unit Test Notification',
                'message' => 'This is a unit test notification',
                'data' => ['test' => true],
                'priority' => 'normal',
            ]);

            return $notification->exists;
        });

        // Test localization service
        $this->test('Localization Service', function () {
            $testData = [
                'type' => 'thread_replied',
                'data' => ['user_name' => 'Test User', 'thread_title' => 'Test Thread'],
            ];

            $viData = NotificationLocalizationService::getLocalizedNotification($testData, 'vi');
            $enData = NotificationLocalizationService::getLocalizedNotification($testData, 'en');

            return !empty($viData['title']) && !empty($enData['title']) && $viData['title'] !== $enData['title'];
        });

        // Test targeting service
        $this->test('Targeting Service', function () {
            $criteria = ['roles' => ['member'], 'active_within_days' => 30];
            $users = NotificationTargetingService::getTargetUsers($criteria);
            
            return $users instanceof \Illuminate\Support\Collection;
        });

        // Test notification service
        $this->test('Notification Service', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            $result = NotificationService::sendBulkNotifications([
                [
                    'user_id' => $user->id,
                    'type' => 'service_test',
                    'title' => 'Service Test',
                    'message' => 'Testing notification service',
                    'data' => ['test' => true],
                ]
            ]);

            return $result === true;
        });

        $this->newLine();
    }

    /**
     * Run integration tests
     */
    private function runIntegrationTests(): void
    {
        $this->info('2️⃣ Integration Tests');
        $this->newLine();

        // Test forum notification integration
        $this->test('Forum Notification Integration', function () {
            // Test thread creation notification
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            // Simulate thread creation
            $beforeCount = Notification::where('user_id', $user->id)->count();
            
            NotificationService::sendBulkNotifications([
                [
                    'user_id' => $user->id,
                    'type' => 'thread_created',
                    'title' => 'New Thread Created',
                    'message' => 'A new thread has been created',
                    'data' => ['thread_id' => 1, 'forum_id' => 1],
                ]
            ]);

            $afterCount = Notification::where('user_id', $user->id)->count();
            
            return $afterCount > $beforeCount;
        });

        // Test marketplace notification integration
        $this->test('Marketplace Notification Integration', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            $beforeCount = Notification::where('user_id', $user->id)->where('type', 'price_drop_alert')->count();
            
            NotificationService::sendBulkNotifications([
                [
                    'user_id' => $user->id,
                    'type' => 'price_drop_alert',
                    'title' => 'Price Drop Alert',
                    'message' => 'Product price has dropped',
                    'data' => ['product_id' => 1, 'old_price' => 100, 'new_price' => 80],
                ]
            ]);

            $afterCount = Notification::where('user_id', $user->id)->where('type', 'price_drop_alert')->count();
            
            return $afterCount > $beforeCount;
        });

        // Test security notification integration
        $this->test('Security Notification Integration', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            $beforeCount = Notification::where('user_id', $user->id)->where('type', 'login_from_new_device')->count();
            
            NotificationService::sendBulkNotifications([
                [
                    'user_id' => $user->id,
                    'type' => 'login_from_new_device',
                    'title' => 'New Device Login',
                    'message' => 'Login from new device detected',
                    'data' => ['device_info' => 'Test Device', 'location' => 'Test Location'],
                ]
            ]);

            $afterCount = Notification::where('user_id', $user->id)->where('type', 'login_from_new_device')->count();
            
            return $afterCount > $beforeCount;
        });

        // Test cache integration
        $this->test('Cache Integration', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            // Test cache operations
            $cacheKey = "test_notification_{$user->id}";
            Cache::put($cacheKey, 'test_value', 300);
            
            $cached = Cache::get($cacheKey);
            Cache::forget($cacheKey);
            
            return $cached === 'test_value';
        });

        $this->newLine();
    }

    /**
     * Run end-to-end tests
     */
    private function runEndToEndTests(): void
    {
        $this->info('3️⃣ End-to-End Tests');
        $this->newLine();

        // Test complete notification flow
        $this->test('Complete Notification Flow', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            // 1. Create notification
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'e2e_test',
                'title' => 'E2E Test Notification',
                'message' => 'End-to-end test notification',
                'data' => ['test' => 'e2e'],
                'priority' => 'normal',
            ]);

            // 2. Verify creation
            if (!$notification->exists) {
                throw new \Exception('Notification creation failed');
            }

            // 3. Test retrieval
            $retrieved = Notification::find($notification->id);
            if (!$retrieved) {
                throw new \Exception('Notification retrieval failed');
            }

            // 4. Test update (mark as read)
            $retrieved->update(['is_read' => true]);
            if (!$retrieved->fresh()->is_read) {
                throw new \Exception('Notification update failed');
            }

            // 5. Test deletion
            $retrieved->delete();
            if (Notification::find($notification->id)) {
                throw new \Exception('Notification deletion failed');
            }

            return true;
        });

        // Test multi-language flow
        $this->test('Multi-language Notification Flow', function () {
            $user = User::first();
            if (!$user) {
                throw new \Exception('No users found for testing');
            }

            $testData = [
                'type' => 'user_followed',
                'data' => ['follower_name' => 'Test Follower'],
            ];

            // Test multiple languages
            $languages = ['vi', 'en'];
            foreach ($languages as $locale) {
                $localized = NotificationLocalizationService::getLocalizedNotification($testData, $locale);
                
                if (empty($localized['title']) || empty($localized['message'])) {
                    throw new \Exception("Localization failed for {$locale}");
                }
            }

            return true;
        });

        // Test targeting flow
        $this->test('User Targeting Flow', function () {
            // Test different targeting criteria
            $criteria = [
                ['roles' => ['member']],
                ['active_within_days' => 30],
                ['email_notifications_enabled' => true],
            ];

            foreach ($criteria as $criterion) {
                $users = NotificationTargetingService::getTargetUsers($criterion);
                
                if (!($users instanceof \Illuminate\Support\Collection)) {
                    throw new \Exception('Targeting failed for criterion: ' . json_encode($criterion));
                }
            }

            return true;
        });

        $this->newLine();
    }

    /**
     * Run performance validation
     */
    private function runPerformanceValidation(): void
    {
        $this->info('4️⃣ Performance Validation');
        $this->newLine();

        // Test database performance
        $this->test('Database Performance', function () {
            $startTime = microtime(true);
            
            // Test query performance
            $notifications = Notification::with('user')
                ->where('created_at', '>=', now()->subDays(7))
                ->limit(100)
                ->get();
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            // Should complete within 1 second
            if ($duration > 1.0) {
                throw new \Exception("Database query too slow: {$duration}s");
            }

            return true;
        });

        // Test cache performance
        $this->test('Cache Performance', function () {
            $startTime = microtime(true);
            
            // Test cache operations
            for ($i = 0; $i < 100; $i++) {
                Cache::put("perf_test_{$i}", "value_{$i}", 60);
                Cache::get("perf_test_{$i}");
            }
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            // Cleanup
            for ($i = 0; $i < 100; $i++) {
                Cache::forget("perf_test_{$i}");
            }
            
            // Should complete within 0.5 seconds
            if ($duration > 0.5) {
                throw new \Exception("Cache operations too slow: {$duration}s");
            }

            return true;
        });

        // Test memory usage
        $this->test('Memory Usage', function () {
            $initialMemory = memory_get_usage(true);
            
            // Create many notifications in memory
            $notifications = [];
            for ($i = 0; $i < 1000; $i++) {
                $notifications[] = [
                    'user_id' => 1,
                    'type' => 'memory_test',
                    'title' => "Memory Test {$i}",
                    'message' => "Memory test notification {$i}",
                    'data' => ['test' => $i],
                ];
            }
            
            $peakMemory = memory_get_usage(true);
            $memoryUsed = $peakMemory - $initialMemory;
            
            // Should use less than 50MB
            if ($memoryUsed > 50 * 1024 * 1024) {
                throw new \Exception("Memory usage too high: " . round($memoryUsed / 1024 / 1024, 2) . "MB");
            }

            return true;
        });

        $this->newLine();
    }

    /**
     * Check production readiness
     */
    private function checkProductionReadiness(): int
    {
        $this->info('5️⃣ Production Readiness Check');
        $this->newLine();

        $readinessChecks = [
            'Database Tables' => $this->checkDatabaseTables(),
            'Required Indexes' => $this->checkDatabaseIndexes(),
            'Configuration' => $this->checkConfiguration(),
            'Dependencies' => $this->checkDependencies(),
            'Performance' => $this->checkPerformanceMetrics(),
            'Security' => $this->checkSecurity(),
            'Monitoring' => $this->checkMonitoring(),
        ];

        $allPassed = true;
        foreach ($readinessChecks as $check => $result) {
            if ($result['status']) {
                $this->info("✅ {$check}: {$result['message']}");
            } else {
                $this->error("❌ {$check}: {$result['message']}");
                $allPassed = false;
                $this->issues[] = "{$check}: {$result['message']}";
            }
        }

        $this->newLine();
        
        if ($allPassed) {
            $this->info('🎉 System is PRODUCTION READY!');
        } else {
            $this->warn('⚠️ System has issues that need to be addressed before production deployment.');
            
            if ($this->option('fix-issues')) {
                $this->fixProductionIssues();
            }
        }

        return $allPassed ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Test helper method
     */
    private function test(string $name, callable $test): void
    {
        try {
            $startTime = microtime(true);
            $result = $test();
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            if ($result) {
                $this->line("✅ {$name} ({$duration}ms)");
                $this->passedTests++;
                $this->testResults[$name] = ['status' => 'PASS', 'duration' => $duration];
            } else {
                $this->line("❌ {$name} - Test returned false");
                $this->failedTests++;
                $this->testResults[$name] = ['status' => 'FAIL', 'duration' => $duration, 'error' => 'Test returned false'];
            }
        } catch (\Exception $e) {
            $this->line("❌ {$name} - {$e->getMessage()}");
            $this->failedTests++;
            $this->testResults[$name] = ['status' => 'FAIL', 'error' => $e->getMessage()];
        }
    }

    /**
     * Check database tables
     */
    private function checkDatabaseTables(): array
    {
        $requiredTables = ['notifications', 'users'];
        $missingTables = [];

        foreach ($requiredTables as $table) {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (empty($missingTables)) {
            return ['status' => true, 'message' => 'All required tables exist'];
        } else {
            return ['status' => false, 'message' => 'Missing tables: ' . implode(', ', $missingTables)];
        }
    }

    /**
     * Check database indexes
     */
    private function checkDatabaseIndexes(): array
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM notifications WHERE Key_name != 'PRIMARY'");
            $indexCount = count($indexes);
            
            if ($indexCount >= 3) {
                return ['status' => true, 'message' => "{$indexCount} indexes found"];
            } else {
                return ['status' => false, 'message' => "Only {$indexCount} indexes found, need at least 3"];
            }
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Could not check indexes: ' . $e->getMessage()];
        }
    }

    /**
     * Check configuration
     */
    private function checkConfiguration(): array
    {
        $requiredConfigs = [
            'app.name',
            'app.env',
            'database.default',
            'cache.default',
            'queue.default',
        ];

        $missingConfigs = [];
        foreach ($requiredConfigs as $config) {
            if (config($config) === null) {
                $missingConfigs[] = $config;
            }
        }

        if (empty($missingConfigs)) {
            return ['status' => true, 'message' => 'All required configurations are set'];
        } else {
            return ['status' => false, 'message' => 'Missing configurations: ' . implode(', ', $missingConfigs)];
        }
    }

    /**
     * Check dependencies
     */
    private function checkDependencies(): array
    {
        $requiredClasses = [
            'App\Models\Notification',
            'App\Models\User',
            'App\Services\NotificationService',
            'App\Services\NotificationLocalizationService',
            'App\Services\NotificationTargetingService',
        ];

        $missingClasses = [];
        foreach ($requiredClasses as $class) {
            if (!class_exists($class)) {
                $missingClasses[] = $class;
            }
        }

        if (empty($missingClasses)) {
            return ['status' => true, 'message' => 'All required classes exist'];
        } else {
            return ['status' => false, 'message' => 'Missing classes: ' . implode(', ', $missingClasses)];
        }
    }

    /**
     * Check performance metrics
     */
    private function checkPerformanceMetrics(): array
    {
        try {
            $metrics = NotificationPerformanceService::getQueryMetrics();
            return ['status' => true, 'message' => 'Performance metrics available'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Performance metrics unavailable: ' . $e->getMessage()];
        }
    }

    /**
     * Check security
     */
    private function checkSecurity(): array
    {
        $securityChecks = [];
        
        // Check if debug mode is off in production
        if (config('app.env') === 'production' && config('app.debug') === true) {
            $securityChecks[] = 'Debug mode should be disabled in production';
        }
        
        // Check if APP_KEY is set
        if (empty(config('app.key'))) {
            $securityChecks[] = 'APP_KEY is not set';
        }

        if (empty($securityChecks)) {
            return ['status' => true, 'message' => 'Security checks passed'];
        } else {
            return ['status' => false, 'message' => implode(', ', $securityChecks)];
        }
    }

    /**
     * Check monitoring
     */
    private function checkMonitoring(): array
    {
        // Check if logging is configured
        if (config('logging.default') && config('logging.channels.' . config('logging.default'))) {
            return ['status' => true, 'message' => 'Logging is configured'];
        } else {
            return ['status' => false, 'message' => 'Logging is not properly configured'];
        }
    }

    /**
     * Fix production issues
     */
    private function fixProductionIssues(): void
    {
        $this->info('🔧 Attempting to fix production issues...');
        
        // Run performance optimization
        NotificationPerformanceService::optimizeQueries();
        NotificationCacheOptimizationService::optimizeCaching();
        
        $this->info('✅ Applied performance optimizations');
    }

    /**
     * Generate test report
     */
    private function generateTestReport(): void
    {
        $this->newLine();
        $this->info('📊 Test Report');
        $this->line('═══════════════════════════════════════');
        $this->line("Total Tests: " . ($this->passedTests + $this->failedTests));
        $this->line("Passed: {$this->passedTests}");
        $this->line("Failed: {$this->failedTests}");
        $this->line("Success Rate: " . round(($this->passedTests / ($this->passedTests + $this->failedTests)) * 100, 2) . "%");
        
        if (!empty($this->issues)) {
            $this->newLine();
            $this->warn('Issues Found:');
            foreach ($this->issues as $issue) {
                $this->line("  • {$issue}");
            }
        }
    }

    /**
     * Generate final report
     */
    private function generateFinalReport(): void
    {
        $this->newLine();
        $this->info('🎯 Final System Report');
        $this->line('═══════════════════════════════════════');
        
        $this->generateTestReport();
        
        $this->newLine();
        $this->info('📈 System Statistics:');
        $this->line("• Total Notifications: " . Notification::count());
        $this->line("• Total Users: " . User::count());
        $this->line("• Unread Notifications: " . Notification::where('is_read', false)->count());
        
        $this->newLine();
        if ($this->failedTests === 0) {
            $this->info('🎉 MechaMap Notification System is READY FOR PRODUCTION!');
        } else {
            $this->warn('⚠️ System needs attention before production deployment.');
        }
    }
}
