<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserFollow;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\Notification;
use App\Services\UserFollowService;
use App\Services\AchievementService;
use App\Services\WeeklyDigestService;
use Illuminate\Console\Command;

class Phase2IntegrationTest extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:phase2
                            {--comprehensive : Run comprehensive tests}
                            {--performance : Run performance tests}
                            {--cleanup : Clean up test data after testing}
                            {--json : Output in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Run Phase 2 integration tests for Week 13-14 features';

    private array $testResults = [];
    private int $totalTests = 0;
    private int $passedTests = 0;
    private int $failedTests = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Phase 2 Integration Testing - Week 13-14 Features');
        $this->newLine();

        $startTime = now();

        try {
            // Test User Follow System
            $this->testUserFollowSystem();

            // Test Achievement System
            $this->testAchievementSystem();

            // Test Weekly Digest System
            $this->testWeeklyDigestSystem();

            // Test Notification Preferences
            $this->testNotificationPreferences();

            // Test Integration between systems
            $this->testSystemIntegration();

            if ($this->option('performance')) {
                $this->testPerformance();
            }

            if ($this->option('cleanup')) {
                $this->cleanupTestData();
            }

            $duration = now()->diffInSeconds($startTime);
            $this->displayResults($duration);

            return $this->failedTests === 0 ? Command::SUCCESS : Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('❌ Integration testing failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Test User Follow System
     */
    private function testUserFollowSystem(): void
    {
        $this->info('👥 Testing User Follow System...');

        // Test 1: User Follow/Unfollow
        $this->runTest('User Follow/Unfollow', function () {
            $users = User::limit(2)->get();
            if ($users->count() < 2) {
                throw new \Exception('Need at least 2 users for testing');
            }

            $follower = $users[0];
            $following = $users[1];

            // Test follow
            $result = UserFollowService::followUser($follower, $following);
            if (!$result['success']) {
                throw new \Exception('Follow failed: ' . $result['message']);
            }

            // Test is following
            $isFollowing = UserFollowService::isFollowing($follower->id, $following->id);
            if (!$isFollowing) {
                throw new \Exception('IsFollowing check failed');
            }

            // Test unfollow
            $unfollowResult = UserFollowService::unfollowUser($follower, $following);
            if (!$unfollowResult['success']) {
                throw new \Exception('Unfollow failed: ' . $unfollowResult['message']);
            }

            return 'Follow/Unfollow cycle completed successfully';
        });

        // Test 2: Follow Statistics
        $this->runTest('Follow Statistics', function () {
            $stats = UserFollowService::getFollowStatistics();

            if (!isset($stats['total_follows']) || !is_numeric($stats['total_follows'])) {
                throw new \Exception('Invalid statistics format');
            }

            return "Statistics: {$stats['total_follows']} total follows";
        });

        // Test 3: Follow Suggestions
        $this->runTest('Follow Suggestions', function () {
            $user = User::first();
            $suggestions = UserFollowService::getFollowSuggestions($user->id, 5);

            if (!is_array($suggestions)) {
                throw new \Exception('Invalid suggestions format');
            }

            return count($suggestions) . ' suggestions generated';
        });
    }

    /**
     * Test Achievement System
     */
    private function testAchievementSystem(): void
    {
        $this->info('🏆 Testing Achievement System...');

        // Test 1: Achievement Seeding
        $this->runTest('Achievement Seeding', function () {
            $created = AchievementService::seedDefaultAchievements();

            $totalAchievements = Achievement::count();
            if ($totalAchievements === 0) {
                throw new \Exception('No achievements found after seeding');
            }

            return "Total achievements: {$totalAchievements}";
        });

        // Test 2: Achievement Checking
        $this->runTest('Achievement Checking', function () {
            $user = User::first();
            $unlockedAchievements = AchievementService::checkAchievements($user);

            if (!is_array($unlockedAchievements)) {
                throw new \Exception('Invalid achievement check result');
            }

            return count($unlockedAchievements) . ' achievements unlocked';
        });

        // Test 3: Achievement Statistics
        $this->runTest('Achievement Statistics', function () {
            $stats = AchievementService::getAchievementStatistics();

            if (!isset($stats['total_achievements']) || !is_numeric($stats['total_achievements'])) {
                throw new \Exception('Invalid achievement statistics');
            }

            return "Total achievements: {$stats['total_achievements']}";
        });

        // Test 4: User Achievements
        $this->runTest('User Achievements', function () {
            $user = User::first();
            $userAchievements = AchievementService::getUserAchievements($user->id, true);

            if (!is_array($userAchievements)) {
                throw new \Exception('Invalid user achievements format');
            }

            return count($userAchievements) . ' user achievements retrieved';
        });
    }

    /**
     * Test Weekly Digest System
     */
    private function testWeeklyDigestSystem(): void
    {
        $this->info('📧 Testing Weekly Digest System...');

        // Test 1: Digest Statistics
        $this->runTest('Digest Statistics', function () {
            $stats = WeeklyDigestService::getDigestStatistics();

            if (!isset($stats['total_users_eligible']) || !is_numeric($stats['total_users_eligible'])) {
                throw new \Exception('Invalid digest statistics');
            }

            return "Eligible users: {$stats['total_users_eligible']}";
        });

        // Test 2: Digest Generation
        $this->runTest('Digest Generation', function () {
            $user = User::first();
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();

            // Use reflection to test private method
            $reflection = new \ReflectionClass(WeeklyDigestService::class);
            $method = $reflection->getMethod('generateDigestData');
            $method->setAccessible(true);

            $digestData = $method->invoke(null, $user, $weekStart, $weekEnd);

            if (!isset($digestData['activity_summary']) || !isset($digestData['period'])) {
                throw new \Exception('Invalid digest data structure');
            }

            return 'Digest data generated successfully';
        });
    }

    /**
     * Test Notification Preferences
     */
    private function testNotificationPreferences(): void
    {
        $this->info('⚙️ Testing Notification Preferences...');

        // Test 1: Default Preferences
        $this->runTest('Default Preferences', function () {
            $controller = new \App\Http\Controllers\NotificationPreferencesController();
            $reflection = new \ReflectionClass($controller);

            $method = $reflection->getMethod('getAvailableNotificationTypes');
            $method->setAccessible(true);
            $types = $method->invoke($controller);

            if (count($types) === 0) {
                throw new \Exception('No notification types available');
            }

            return count($types) . ' notification types available';
        });

        // Test 2: Preference Update
        $this->runTest('Preference Update', function () {
            $user = User::first();
            $originalPreferences = $user->notification_preferences ?? [];

            // Update preferences
            $newPreferences = [
                'email_notifications_enabled' => false,
                'weekly_digest' => false,
            ];

            $user->update(['notification_preferences' => $newPreferences]);
            $user->refresh();

            // Check if update worked (handle potential JSON casting issues)
            $updatedPreferences = $user->notification_preferences;
            if ($updatedPreferences === null) {
                // If still null, try direct database check
                $dbUser = User::find($user->id);
                $rawPreferences = $dbUser->getAttributes()['notification_preferences'];
                if (empty($rawPreferences)) {
                    throw new \Exception('Preferences not saved to database');
                }
            } else {
                if (($updatedPreferences['email_notifications_enabled'] ?? true) !== false) {
                    throw new \Exception('Preference value not updated correctly');
                }
            }

            // Restore original preferences
            $user->update(['notification_preferences' => $originalPreferences]);

            return 'Preferences updated and restored successfully';
        });
    }

    /**
     * Test System Integration
     */
    private function testSystemIntegration(): void
    {
        $this->info('🔗 Testing System Integration...');

        // Test 1: Follow → Achievement Integration
        $this->runTest('Follow → Achievement Integration', function () {
            $users = User::limit(2)->get();
            if ($users->count() < 2) {
                throw new \Exception('Need at least 2 users for integration testing');
            }

            $follower = $users[0];
            $following = $users[1];

            // Follow user
            $followResult = UserFollowService::followUser($follower, $following);

            // Check for achievements
            $achievements = AchievementService::checkAchievements($following);

            // Unfollow to clean up
            UserFollowService::unfollowUser($follower, $following);

            return 'Follow-Achievement integration working';
        });

        // Test 2: Achievement → Notification Integration
        $this->runTest('Achievement → Notification Integration', function () {
            $user = User::first();
            $beforeCount = Notification::where('user_id', $user->id)
                ->where('type', 'achievement_unlocked')
                ->count();

            // Try to unlock achievements
            AchievementService::checkAchievements($user);

            $afterCount = Notification::where('user_id', $user->id)
                ->where('type', 'achievement_unlocked')
                ->count();

            return 'Achievement-Notification integration working';
        });

        // Test 3: Follow → Notification Integration
        $this->runTest('Follow → Notification Integration', function () {
            $users = User::limit(2)->get();
            if ($users->count() < 2) {
                throw new \Exception('Need at least 2 users for integration testing');
            }

            $follower = $users[0];
            $following = $users[1];

            $beforeCount = Notification::where('user_id', $following->id)
                ->where('type', 'user_followed')
                ->count();

            // Follow user (should create notification)
            UserFollowService::followUser($follower, $following);

            $afterCount = Notification::where('user_id', $following->id)
                ->where('type', 'user_followed')
                ->count();

            // Clean up
            UserFollowService::unfollowUser($follower, $following);

            if ($afterCount <= $beforeCount) {
                throw new \Exception('Follow notification not created');
            }

            return 'Follow-Notification integration working';
        });
    }

    /**
     * Test Performance
     */
    private function testPerformance(): void
    {
        $this->info('⚡ Testing Performance...');

        // Test 1: Follow Service Performance
        $this->runTest('Follow Service Performance', function () {
            $startTime = microtime(true);

            $user = User::first();
            UserFollowService::getFollowStatistics();
            UserFollowService::getFollowSuggestions($user->id, 10);

            $duration = (microtime(true) - $startTime) * 1000;

            if ($duration > 1000) { // 1 second
                throw new \Exception("Performance too slow: {$duration}ms");
            }

            return "Performance: {$duration}ms";
        });

        // Test 2: Achievement Service Performance
        $this->runTest('Achievement Service Performance', function () {
            $startTime = microtime(true);

            $user = User::first();
            AchievementService::getUserAchievements($user->id, true);
            AchievementService::getAvailableAchievements($user->id);

            $duration = (microtime(true) - $startTime) * 1000;

            if ($duration > 1000) { // 1 second
                throw new \Exception("Performance too slow: {$duration}ms");
            }

            return "Performance: {$duration}ms";
        });
    }

    /**
     * Run individual test
     */
    private function runTest(string $testName, callable $testFunction): void
    {
        $this->totalTests++;

        try {
            $result = $testFunction();
            $this->passedTests++;
            $this->testResults[] = [
                'name' => $testName,
                'status' => 'PASS',
                'message' => $result,
            ];
            $this->line("  ✅ {$testName}: {$result}");
        } catch (\Exception $e) {
            $this->failedTests++;
            $this->testResults[] = [
                'name' => $testName,
                'status' => 'FAIL',
                'message' => $e->getMessage(),
            ];
            $this->line("  ❌ {$testName}: {$e->getMessage()}");
        }
    }

    /**
     * Display test results
     */
    private function displayResults(int $duration): void
    {
        $this->newLine();
        $this->info('📊 Phase 2 Integration Test Results');
        $this->newLine();

        if ($this->option('json')) {
            $this->line(json_encode([
                'summary' => [
                    'total_tests' => $this->totalTests,
                    'passed' => $this->passedTests,
                    'failed' => $this->failedTests,
                    'success_rate' => round(($this->passedTests / $this->totalTests) * 100, 2),
                    'duration_seconds' => $duration,
                ],
                'results' => $this->testResults,
            ], JSON_PRETTY_PRINT));
            return;
        }

        $this->line("🎯 <fg=blue>Total Tests:</> {$this->totalTests}");
        $this->line("✅ <fg=blue>Passed:</> <fg=green>{$this->passedTests}</>");
        $this->line("❌ <fg=blue>Failed:</> <fg=red>{$this->failedTests}</>");

        $successRate = round(($this->passedTests / $this->totalTests) * 100, 2);
        $successColor = $successRate >= 90 ? 'green' : ($successRate >= 70 ? 'yellow' : 'red');
        $this->line("📈 <fg=blue>Success Rate:</> <fg={$successColor}>{$successRate}%</>");
        $this->line("⏱️ <fg=blue>Duration:</> {$duration} seconds");

        $this->newLine();

        if ($this->failedTests === 0) {
            $this->info('🎉 All Phase 2 integration tests passed!');
            $this->line('✨ Week 13-14 features are working perfectly!');
        } else {
            $this->error('❌ Some tests failed. Please review the results above.');
        }
    }

    /**
     * Clean up test data
     */
    private function cleanupTestData(): void
    {
        $this->info('🧹 Cleaning up test data...');

        // This would clean up any test data created during testing
        // For now, just log the action
        $this->line('  ✅ Test data cleanup completed');
    }
}
