<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\NotificationCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TestNotificationPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test-performance
                            {--users=10 : Number of test users}
                            {--notifications=100 : Number of notifications per user}
                            {--cache : Test with cache enabled}
                            {--cleanup : Clean up test data after testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification system performance with various scenarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Enable query logging
        DB::enableQueryLog();

        $this->info('🚀 Starting Notification Performance Testing');
        $this->info('==========================================');
        $this->newLine();

        $userCount = (int) $this->option('users');
        $notificationCount = (int) $this->option('notifications');
        $useCache = $this->option('cache');
        $cleanup = $this->option('cleanup');

        $this->info("Test Parameters:");
        $this->info("- Users: {$userCount}");
        $this->info("- Notifications per user: {$notificationCount}");
        $this->info("- Cache enabled: " . ($useCache ? 'Yes' : 'No'));
        $this->info("- Cleanup after test: " . ($cleanup ? 'Yes' : 'No'));
        $this->newLine();

        // Test scenarios
        $results = [];

        try {
            // 1. Test notification creation performance
            $results['creation'] = $this->testNotificationCreation($userCount, $notificationCount);

            // 2. Test notification retrieval performance
            $results['retrieval'] = $this->testNotificationRetrieval($userCount, $useCache);

            // 3. Test bulk operations
            $results['bulk_operations'] = $this->testBulkOperations($userCount);

            // 4. Test cache performance (if enabled)
            if ($useCache) {
                $results['cache'] = $this->testCachePerformance($userCount);
            }

            // 5. Test database query optimization
            $results['query_optimization'] = $this->testQueryOptimization();

            // Display results
            $this->displayResults($results);

            // Cleanup if requested
            if ($cleanup) {
                $this->cleanupTestData();
            }

        } catch (\Exception $e) {
            $this->error('Performance test failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Test notification creation performance
     */
    private function testNotificationCreation(int $userCount, int $notificationCount): array
    {
        $this->info('📝 Testing notification creation performance...');

        $users = User::inRandomOrder()->limit($userCount)->get();

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        foreach ($users as $user) {
            for ($i = 0; $i < $notificationCount; $i++) {
                NotificationService::send(
                    $user,
                    'performance_test',
                    "Test Notification #{$i}",
                    "This is a performance test notification #{$i}",
                    ['test_id' => $i, 'priority' => 'normal'],
                    false
                );
            }
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $totalNotifications = $userCount * $notificationCount;
        $duration = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;

        return [
            'total_notifications' => $totalNotifications,
            'duration_seconds' => round($duration, 3),
            'notifications_per_second' => round($totalNotifications / $duration, 2),
            'memory_used_mb' => round($memoryUsed / 1024 / 1024, 2),
            'avg_time_per_notification_ms' => round(($duration / $totalNotifications) * 1000, 3)
        ];
    }

    /**
     * Test notification retrieval performance
     */
    private function testNotificationRetrieval(int $userCount, bool $useCache): array
    {
        $this->info('📖 Testing notification retrieval performance...');

        $users = User::whereHas('userNotifications')->inRandomOrder()->limit($userCount)->get();

        $startTime = microtime(true);
        $totalQueries = 0;

        foreach ($users as $user) {
            $queryCountBefore = $this->getQueryCount();

            if ($useCache) {
                NotificationCacheService::getUserNotifications($user, 1, 20);
                NotificationCacheService::getUnreadCount($user);
            } else {
                Notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

                Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            }

            $queryCountAfter = $this->getQueryCount();
            $totalQueries += ($queryCountAfter - $queryCountBefore);
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        return [
            'users_tested' => $userCount,
            'duration_seconds' => round($duration, 3),
            'total_queries' => $totalQueries,
            'avg_queries_per_user' => round($totalQueries / $userCount, 2),
            'avg_time_per_user_ms' => round(($duration / $userCount) * 1000, 3),
            'cache_enabled' => $useCache
        ];
    }

    /**
     * Test bulk operations performance
     */
    private function testBulkOperations(int $userCount): array
    {
        $this->info('📦 Testing bulk operations performance...');

        $users = User::inRandomOrder()->limit($userCount)->get();

        // Test bulk notification creation
        $startTime = microtime(true);

        $bulkNotifications = [];
        foreach ($users as $user) {
            $bulkNotifications[] = [
                'user_id' => $user->id,
                'type' => 'bulk_test',
                'title' => 'Bulk Test Notification',
                'message' => 'This is a bulk test notification',
                'data' => ['bulk_test' => true],
                'priority' => 'normal',
                'send_email' => false
            ];
        }

        NotificationService::sendBulkNotifications($bulkNotifications);

        $bulkCreationTime = microtime(true) - $startTime;

        // Test bulk mark as read
        $startTime = microtime(true);

        foreach ($users as $user) {
            NotificationService::markAsReadBulk($user);
        }

        $bulkMarkReadTime = microtime(true) - $startTime;

        return [
            'bulk_creation_time_seconds' => round($bulkCreationTime, 3),
            'bulk_mark_read_time_seconds' => round($bulkMarkReadTime, 3),
            'notifications_created' => count($bulkNotifications),
            'creation_rate_per_second' => round(count($bulkNotifications) / $bulkCreationTime, 2)
        ];
    }

    /**
     * Test cache performance
     */
    private function testCachePerformance(int $userCount): array
    {
        $this->info('⚡ Testing cache performance...');

        $users = User::whereHas('userNotifications')->inRandomOrder()->limit($userCount)->get();

        // Warm up cache
        $warmupStart = microtime(true);
        NotificationCacheService::warmUpCache();
        $warmupTime = microtime(true) - $warmupStart;

        // Test cache hit performance
        $startTime = microtime(true);

        foreach ($users as $user) {
            NotificationCacheService::getUnreadCount($user);
            NotificationCacheService::getUserNotifications($user, 1, 20);
        }

        $cacheHitTime = microtime(true) - $startTime;

        // Get cache stats
        $cacheStats = NotificationCacheService::getCacheStats();

        return [
            'warmup_time_seconds' => round($warmupTime, 3),
            'cache_hit_time_seconds' => round($cacheHitTime, 3),
            'avg_cache_hit_time_ms' => round(($cacheHitTime / ($userCount * 2)) * 1000, 3),
            'cache_stats' => $cacheStats
        ];
    }

    /**
     * Test query optimization
     */
    private function testQueryOptimization(): array
    {
        $this->info('🔍 Testing query optimization...');

        // Test with and without indexes
        $user = User::whereHas('userNotifications')->first();

        if (!$user) {
            return ['error' => 'No user with notifications found'];
        }

        // Test optimized query
        $startTime = microtime(true);
        $queryCountBefore = $this->getQueryCount();

        Notification::getOptimizedNotifications($user, 20);

        $queryCountAfter = $this->getQueryCount();
        $optimizedTime = microtime(true) - $startTime;
        $optimizedQueries = $queryCountAfter - $queryCountBefore;

        // Test unoptimized query
        $startTime = microtime(true);
        $queryCountBefore = $this->getQueryCount();

        Notification::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $queryCountAfter = $this->getQueryCount();
        $unoptimizedTime = microtime(true) - $startTime;
        $unoptimizedQueries = $queryCountAfter - $queryCountBefore;

        return [
            'optimized_time_ms' => round($optimizedTime * 1000, 3),
            'unoptimized_time_ms' => round($unoptimizedTime * 1000, 3),
            'optimized_queries' => $optimizedQueries,
            'unoptimized_queries' => $unoptimizedQueries,
            'performance_improvement' => round((($unoptimizedTime - $optimizedTime) / $unoptimizedTime) * 100, 2) . '%'
        ];
    }

    /**
     * Display test results
     */
    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Performance Test Results');
        $this->info('===========================');

        foreach ($results as $testName => $result) {
            $this->newLine();
            $this->info("🔸 " . ucwords(str_replace('_', ' ', $testName)) . ":");

            if (isset($result['error'])) {
                $this->error("  Error: " . $result['error']);
                continue;
            }

            foreach ($result as $key => $value) {
                if (is_array($value)) {
                    $this->line("  " . ucwords(str_replace('_', ' ', $key)) . ":");
                    foreach ($value as $subKey => $subValue) {
                        $this->line("    " . ucwords(str_replace('_', ' ', $subKey)) . ": " . $subValue);
                    }
                } else {
                    $this->line("  " . ucwords(str_replace('_', ' ', $key)) . ": " . $value);
                }
            }
        }

        $this->newLine();
        $this->info('✅ Performance testing completed!');
    }

    /**
     * Get current query count
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Clean up test data
     */
    private function cleanupTestData(): void
    {
        $this->info('🧹 Cleaning up test data...');

        $deleted = Notification::whereIn('type', ['performance_test', 'bulk_test'])->delete();

        $this->info("Deleted {$deleted} test notifications");
    }
}
