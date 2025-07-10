<?php

namespace App\Console\Commands;

use App\Services\NotificationPerformanceService;
use App\Services\NotificationCacheOptimizationService;
use App\Services\NotificationMemoryOptimizationService;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotificationPerformanceTest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notification:performance-test
                            {--optimize : Run performance optimizations}
                            {--load-test : Run load testing}
                            {--memory-test : Run memory testing}
                            {--cache-test : Run cache testing}
                            {--full : Run all tests and optimizations}
                            {--users=100 : Number of users for load testing}
                            {--notifications=1000 : Number of notifications for load testing}';

    /**
     * The console command description.
     */
    protected $description = 'Test and optimize notification system performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Notification Performance Testing & Optimization');
        $this->newLine();

        if ($this->option('full')) {
            return $this->runFullTest();
        }

        if ($this->option('optimize')) {
            return $this->runOptimizations();
        }

        if ($this->option('load-test')) {
            return $this->runLoadTest();
        }

        if ($this->option('memory-test')) {
            return $this->runMemoryTest();
        }

        if ($this->option('cache-test')) {
            return $this->runCacheTest();
        }

        // Default: show performance metrics
        return $this->showPerformanceMetrics();
    }

    /**
     * Run full performance test suite
     */
    private function runFullTest(): int
    {
        $this->info('🔥 Running Full Performance Test Suite');
        $this->newLine();

        // 1. Run optimizations
        $this->line('1️⃣ Running Performance Optimizations...');
        $this->runOptimizations();
        $this->newLine();

        // 2. Run cache test
        $this->line('2️⃣ Running Cache Performance Test...');
        $this->runCacheTest();
        $this->newLine();

        // 3. Run memory test
        $this->line('3️⃣ Running Memory Performance Test...');
        $this->runMemoryTest();
        $this->newLine();

        // 4. Run load test
        $this->line('4️⃣ Running Load Performance Test...');
        $this->runLoadTest();
        $this->newLine();

        // 5. Final metrics
        $this->line('5️⃣ Final Performance Metrics...');
        $this->showPerformanceMetrics();

        $this->info('✅ Full Performance Test Suite Completed!');
        return Command::SUCCESS;
    }

    /**
     * Run performance optimizations
     */
    private function runOptimizations(): int
    {
        $this->info('⚡ Running Performance Optimizations');
        $this->newLine();

        // 1. Database optimization
        $this->line('🗄️ Database Optimization...');
        $dbResult = NotificationPerformanceService::optimizeQueries();

        if ($dbResult['success']) {
            $this->info('✅ Database optimization completed');
            foreach ($dbResult['results']['indexes'] ?? [] as $index) {
                $this->line("  • {$index}");
            }
        } else {
            $this->error('❌ Database optimization failed: ' . $dbResult['message']);
        }
        $this->newLine();

        // 2. Cache optimization
        $this->line('🚀 Cache Optimization...');
        $cacheResult = NotificationCacheOptimizationService::optimizeCaching();

        if ($cacheResult['success']) {
            $this->info('✅ Cache optimization completed');
            foreach ($cacheResult['results']['cache_warming'] ?? [] as $warming) {
                $this->line("  • {$warming}");
            }
        } else {
            $this->error('❌ Cache optimization failed: ' . $cacheResult['message']);
        }
        $this->newLine();

        // 3. Memory optimization
        $this->line('🧠 Memory Optimization...');
        $memoryResult = NotificationMemoryOptimizationService::optimizeMemory();

        if ($memoryResult['success']) {
            $this->info('✅ Memory optimization completed');
            $this->line("  • Initial memory: {$memoryResult['results']['initial_memory']}");
            $this->line("  • Final memory: {$memoryResult['results']['final_memory']}");
            $this->line("  • Memory saved: {$memoryResult['results']['memory_saved']}");
        } else {
            $this->error('❌ Memory optimization failed: ' . $memoryResult['message']);
        }

        return Command::SUCCESS;
    }

    /**
     * Run load testing
     */
    private function runLoadTest(): int
    {
        $userCount = (int) $this->option('users');
        $notificationCount = (int) $this->option('notifications');

        $this->info("📊 Load Testing ({$userCount} users, {$notificationCount} notifications)");
        $this->newLine();

        // 1. Test notification creation performance
        $this->line('1️⃣ Testing Notification Creation Performance...');
        $creationResult = $this->testNotificationCreation($notificationCount);
        $this->displayTestResult('Notification Creation', $creationResult);
        $this->newLine();

        // 2. Test bulk notification sending
        $this->line('2️⃣ Testing Bulk Notification Sending...');
        $bulkResult = $this->testBulkNotificationSending($userCount);
        $this->displayTestResult('Bulk Notification Sending', $bulkResult);
        $this->newLine();

        // 3. Test notification retrieval
        $this->line('3️⃣ Testing Notification Retrieval...');
        $retrievalResult = $this->testNotificationRetrieval($userCount);
        $this->displayTestResult('Notification Retrieval', $retrievalResult);
        $this->newLine();

        // 4. Test concurrent operations
        $this->line('4️⃣ Testing Concurrent Operations...');
        $concurrentResult = $this->testConcurrentOperations();
        $this->displayTestResult('Concurrent Operations', $concurrentResult);

        return Command::SUCCESS;
    }

    /**
     * Test notification creation performance
     */
    private function testNotificationCreation(int $count): array
    {
        $users = User::limit(min($count, 100))->pluck('id')->toArray();
        if (empty($users)) {
            return ['error' => 'No users found for testing'];
        }

        return NotificationMemoryOptimizationService::monitorMemoryUsage(function () use ($users, $count) {
            $startTime = microtime(true);
            $created = 0;

            for ($i = 0; $i < $count; $i++) {
                $userId = $users[array_rand($users)];

                Notification::create([
                    'user_id' => $userId,
                    'type' => 'performance_test',
                    'title' => "Performance Test Notification {$i}",
                    'message' => "This is a performance test notification #{$i}",
                    'data' => ['test' => true, 'iteration' => $i],
                    'priority' => 'normal',
                ]);

                $created++;
            }

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            return [
                'created' => $created,
                'duration' => round($duration, 3),
                'rate' => round($created / $duration, 2),
            ];
        }, 'Notification Creation');
    }

    /**
     * Test bulk notification sending
     */
    private function testBulkNotificationSending(int $userCount): array
    {
        $users = User::limit($userCount)->get();
        if ($users->isEmpty()) {
            return ['error' => 'No users found for testing'];
        }

        return NotificationMemoryOptimizationService::monitorMemoryUsage(function () use ($users) {
            $startTime = microtime(true);

            // Create bulk notifications array
            $notifications = [];
            foreach ($users as $user) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'type' => 'bulk_performance_test',
                    'title' => 'Bulk Performance Test',
                    'message' => 'This is a bulk performance test notification',
                    'data' => ['test' => true, 'bulk' => true],
                    'priority' => 'normal',
                ];
            }

            $result = NotificationService::sendBulkNotifications($notifications);

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            return [
                'users_targeted' => $users->count(),
                'notifications_sent' => $result ? $users->count() : 0,
                'duration' => round($duration, 3),
                'rate' => round(($result ? $users->count() : 0) / $duration, 2),
            ];
        }, 'Bulk Notification Sending');
    }

    /**
     * Test notification retrieval performance
     */
    private function testNotificationRetrieval(int $userCount): array
    {
        $users = User::whereHas('userNotifications')->limit($userCount)->pluck('id')->toArray();
        if (empty($users)) {
            return ['error' => 'No users with notifications found'];
        }

        return NotificationMemoryOptimizationService::monitorMemoryUsage(function () use ($users) {
            $startTime = microtime(true);
            $retrieved = 0;

            foreach ($users as $userId) {
                $notifications = Notification::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

                $retrieved += $notifications->count();
            }

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            return [
                'users_queried' => count($users),
                'notifications_retrieved' => $retrieved,
                'duration' => round($duration, 3),
                'rate' => round($retrieved / $duration, 2),
            ];
        }, 'Notification Retrieval');
    }

    /**
     * Test concurrent operations
     */
    private function testConcurrentOperations(): array
    {
        return NotificationMemoryOptimizationService::monitorMemoryUsage(function () {
            $startTime = microtime(true);

            // Simulate concurrent operations
            $operations = [
                'create' => 0,
                'read' => 0,
                'update' => 0,
            ];

            // Create notifications
            for ($i = 0; $i < 50; $i++) {
                $user = User::inRandomOrder()->first();
                if ($user) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'concurrent_test',
                        'title' => "Concurrent Test {$i}",
                        'message' => "Concurrent operation test #{$i}",
                        'data' => ['concurrent' => true],
                    ]);
                    $operations['create']++;
                }
            }

            // Read notifications
            $notifications = Notification::where('type', 'concurrent_test')
                ->limit(100)
                ->get();
            $operations['read'] = $notifications->count();

            // Update notifications
            $updated = Notification::where('type', 'concurrent_test')
                ->where('is_read', false)
                ->limit(25)
                ->update(['is_read' => true]);
            $operations['update'] = $updated;

            $endTime = microtime(true);
            $duration = $endTime - $startTime;

            return [
                'operations' => $operations,
                'total_operations' => array_sum($operations),
                'duration' => round($duration, 3),
                'ops_per_second' => round(array_sum($operations) / $duration, 2),
            ];
        }, 'Concurrent Operations');
    }

    /**
     * Run memory testing
     */
    private function runMemoryTest(): int
    {
        $this->info('🧠 Memory Performance Testing');
        $this->newLine();

        $report = NotificationMemoryOptimizationService::getMemoryReport();

        $this->line('📊 Current Memory Usage:');
        $this->line("  • Current: {$report['current_usage']['formatted']} ({$report['current_usage']['percentage']}%)");
        $this->line("  • Peak: {$report['peak_usage']['formatted']}");
        $this->line("  • Limit: {$report['memory_limit']['formatted']}");
        $this->line("  • Status: " . ucfirst($report['status']));
        $this->newLine();

        $this->line('⚠️ Memory Thresholds:');
        $this->line("  • Warning: {$report['thresholds']['warning']}");
        $this->line("  • Critical: {$report['thresholds']['critical']}");
        $this->line("  • Limit: {$report['thresholds']['limit']}");

        if ($report['gc_status']) {
            $this->newLine();
            $this->line('🗑️ Garbage Collection Status:');
            $this->line("  • Runs: {$report['gc_status']['runs']}");
            $this->line("  • Collected: {$report['gc_status']['collected']}");
        }

        return Command::SUCCESS;
    }

    /**
     * Run cache testing
     */
    private function runCacheTest(): int
    {
        $this->info('🚀 Cache Performance Testing');
        $this->newLine();

        $report = NotificationCacheOptimizationService::getCacheReport();

        $this->line('📊 Cache Performance:');
        $this->line("  • Hit Ratio: {$report['hit_ratio']}%");

        if (!empty($report['memory_usage'])) {
            $this->line("  • Memory Usage: {$report['memory_usage']['used_memory_human']}");
            $this->line("  • Peak Memory: {$report['memory_usage']['used_memory_peak_human']}");
        }

        $this->newLine();
        $this->line('🏷️ Cache Tags Configured:');
        foreach ($report['cache_tags'] as $tag => $subtags) {
            $this->line("  • {$tag}: " . implode(', ', $subtags));
        }

        return Command::SUCCESS;
    }

    /**
     * Show performance metrics
     */
    private function showPerformanceMetrics(): int
    {
        $this->info('📈 Performance Metrics Dashboard');
        $this->newLine();

        // Database metrics
        $this->line('🗄️ Database Performance:');
        $dbMetrics = NotificationPerformanceService::getQueryMetrics();

        if (!empty($dbMetrics['table_sizes'])) {
            foreach ($dbMetrics['table_sizes'] as $table) {
                $this->line("  • {$table['table']}: {$table['rows']} rows, {$table['total_size_mb']} MB");
            }
        }

        $this->newLine();

        // Cache metrics
        $this->line('🚀 Cache Performance:');
        $cacheReport = NotificationCacheOptimizationService::getCacheReport();
        $this->line("  • Hit Ratio: {$cacheReport['hit_ratio']}%");

        $this->newLine();

        // Memory metrics
        $this->line('🧠 Memory Usage:');
        $memoryReport = NotificationMemoryOptimizationService::getMemoryReport();
        $this->line("  • Current: {$memoryReport['current_usage']['formatted']}");
        $this->line("  • Status: " . ucfirst($memoryReport['status']));

        return Command::SUCCESS;
    }

    /**
     * Display test result
     */
    private function displayTestResult(string $testName, array $result): void
    {
        if (isset($result['error'])) {
            $this->error("❌ {$testName}: {$result['error']}");
            return;
        }

        $this->info("✅ {$testName} Results:");

        if (isset($result['result'])) {
            foreach ($result['result'] as $key => $value) {
                if (is_array($value)) {
                    $this->line("  • {$key}: " . json_encode($value));
                } else {
                    $this->line("  • {$key}: {$value}");
                }
            }
        }

        $this->line("  • Execution Time: {$result['execution_time']}");
        $this->line("  • Memory Usage: {$result['memory_usage']['difference']}");
        $this->line("  • Peak Memory: {$result['memory_usage']['peak_during_operation']}");
    }
}
