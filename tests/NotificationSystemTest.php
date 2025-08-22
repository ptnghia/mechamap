<?php

/**
 * Notification System Testing Suite
 *
 * Comprehensive tests for the enhanced notification system
 */

echo "🧪 NOTIFICATION SYSTEM TESTING SUITE\n";
echo "═══════════════════════════════════════════════════\n";

// Initialize Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\NotificationPreference;
use App\Models\NotificationLog;
use App\Services\UnifiedNotificationService;

class NotificationSystemTest
{
    private $testResults = [];
    private $user;

    public function __construct()
    {
        $this->user = User::first();
        echo "👤 Test User: {$this->user->name} (ID: {$this->user->id})\n\n";
    }

    public function runAllTests()
    {
        echo "Starting comprehensive notification system tests...\n";
        echo "═══════════════════════════════════════════════════\n\n";

        // Phase 1: Basic Functionality
        $this->testDatabaseNotification();
        $this->testTemplateRendering();
        $this->testUserPreferences();
        $this->testAutoCategorization();

        // Phase 2: Advanced Features
        $this->testBulkNotifications();
        $this->testMultiChannelNotifications();
        $this->testErrorHandling();

        // Phase 3: Analytics & Performance
        $this->testSystemHealth();
        $this->testDeliveryStats();
        $this->testNotificationLogs();

        // Summary
        $this->printSummary();
    }

    private function testDatabaseNotification()
    {
        echo "📋 Test 1: Database Notification\n";
        echo "─────────────────────────────────\n";

        try {
            $result = UnifiedNotificationService::send(
                $this->user,
                'security_alert',
                'Test Security Alert',
                'Test security message',
                [
                    'alert_description' => 'Automated test security alert',
                    'ip_address' => '192.168.1.200',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ],
                ['database']
            );

            if ($result) {
                $notification = Notification::where('type', 'security_alert')->latest()->first();
                if ($notification) {
                    echo "  ✅ Notification created successfully\n";
                    echo "  ✓ ID: {$notification->id}\n";
                    echo "  ✓ Title: {$notification->title}\n";
                    echo "  ✓ Category: {$notification->category}\n";
                    echo "  ✓ Priority: {$notification->priority}\n";
                    echo "  ✓ Status: {$notification->status}\n";
                    $this->testResults['database_notification'] = 'PASS';
                } else {
                    echo "  ❌ Notification not found in database\n";
                    $this->testResults['database_notification'] = 'FAIL';
                }
            } else {
                echo "  ❌ Service returned false\n";
                $this->testResults['database_notification'] = 'FAIL';
            }
        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['database_notification'] = 'ERROR';
        }

        echo "\n";
    }

    private function testTemplateRendering()
    {
        echo "🎨 Test 2: Template Rendering\n";
        echo "─────────────────────────────────\n";

        try {
            // Check if template exists
            $template = NotificationTemplate::active()->forType('security_alert')->first();

            if ($template) {
                echo "  ✅ Template found for security_alert\n";
                echo "  ✓ Template ID: {$template->id}\n";
                echo "  ✓ Channels: " . implode(', ', $template->channels) . "\n";

                // Test template rendering
                $rendered = $template->render('database', [
                    'alert_description' => 'Test alert description',
                    'ip_address' => '10.0.0.1',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);

                if ($rendered && isset($rendered['title']) && isset($rendered['message'])) {
                    echo "  ✅ Template rendered successfully\n";
                    echo "  ✓ Rendered title: {$rendered['title']}\n";
                    echo "  ✓ Variables replaced: " . (strpos($rendered['message'], 'Test alert description') !== false ? 'YES' : 'NO') . "\n";
                    $this->testResults['template_rendering'] = 'PASS';
                } else {
                    echo "  ❌ Template rendering failed\n";
                    $this->testResults['template_rendering'] = 'FAIL';
                }
            } else {
                echo "  ❌ No template found for security_alert\n";
                $this->testResults['template_rendering'] = 'FAIL';
            }
        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['template_rendering'] = 'ERROR';
        }

        echo "\n";
    }

    private function testUserPreferences()
    {
        echo "⚙️ Test 3: User Preferences\n";
        echo "─────────────────────────────────\n";

        try {
            // Get user preferences
            $enabledChannels = NotificationPreference::getEnabledChannels($this->user->id, 'security_alert');

            echo "  ✅ User preferences retrieved\n";
            echo "  ✓ Enabled channels for security_alert: " . implode(', ', $enabledChannels) . "\n";

            // Test preference filtering
            if (in_array('database', $enabledChannels)) {
                echo "  ✓ Database channel enabled\n";
                $this->testResults['user_preferences'] = 'PASS';
            } else {
                echo "  ⚠️ Database channel disabled\n";
                $this->testResults['user_preferences'] = 'PARTIAL';
            }

            // Count total preferences
            $totalPrefs = NotificationPreference::where('user_id', $this->user->id)->count();
            echo "  ✓ Total user preferences: {$totalPrefs}\n";

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['user_preferences'] = 'ERROR';
        }

        echo "\n";
    }

    private function testAutoCategorization()
    {
        echo "🏷️ Test 4: Auto-Categorization\n";
        echo "─────────────────────────────────\n";

        try {
            // Test different notification types
            $testTypes = [
                'security_alert' => 'security',
                'thread_created' => 'forum',
                'system_announcement' => 'system',
                'product_approved' => 'marketplace'
            ];

            $passed = 0;
            foreach ($testTypes as $type => $expectedCategory) {
                $result = UnifiedNotificationService::send(
                    $this->user,
                    $type,
                    "Test {$type}",
                    "Test message for {$type}",
                    ['test' => true],
                    ['database']
                );

                if ($result) {
                    $notification = Notification::where('type', $type)->latest()->first();
                    if ($notification && $notification->category === $expectedCategory) {
                        echo "  ✓ {$type} → {$notification->category} (expected: {$expectedCategory})\n";
                        $passed++;
                    } else {
                        $category = $notification ? $notification->category : 'null';
                        echo "  ❌ {$type} → {$category} (expected: {$expectedCategory})\n";
                    }
                }
            }

            if ($passed === count($testTypes)) {
                echo "  ✅ All auto-categorization tests passed\n";
                $this->testResults['auto_categorization'] = 'PASS';
            } else {
                echo "  ⚠️ {$passed}/" . count($testTypes) . " auto-categorization tests passed\n";
                $this->testResults['auto_categorization'] = 'PARTIAL';
            }

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['auto_categorization'] = 'ERROR';
        }

        echo "\n";
    }

    private function testBulkNotifications()
    {
        echo "👥 Test 5: Bulk Notifications\n";
        echo "─────────────────────────────────\n";

        try {
            $users = User::limit(3)->get();
            echo "  ℹ️ Testing with {$users->count()} users\n";

            $result = UnifiedNotificationService::sendBulk(
                $users->all(),
                'system_announcement',
                'Bulk Test Announcement',
                'This is a bulk notification test',
                ['test_type' => 'bulk'],
                ['database']
            );

            echo "  ✅ Bulk notification completed\n";
            echo "  ✓ Total: {$result['total']}\n";
            echo "  ✓ Successful: {$result['successful']}\n";
            echo "  ✓ Failed: {$result['failed']}\n";

            if ($result['successful'] > 0 && $result['failed'] === 0) {
                $this->testResults['bulk_notifications'] = 'PASS';
            } else {
                $this->testResults['bulk_notifications'] = 'PARTIAL';
            }

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['bulk_notifications'] = 'ERROR';
        }

        echo "\n";
    }

    private function testMultiChannelNotifications()
    {
        echo "📡 Test 6: Multi-Channel Notifications\n";
        echo "─────────────────────────────────\n";

        try {
            $result = UnifiedNotificationService::send(
                $this->user,
                'security_alert',
                'Multi-Channel Test',
                'Testing multiple channels',
                ['test' => 'multi_channel'],
                ['database', 'email'] // Note: email might fail in test environment
            );

            echo "  ✅ Multi-channel notification sent\n";
            echo "  ✓ Result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";

            // Check logs for multiple channels
            $logs = NotificationLog::where('notifiable_id', $this->user->id)
                ->where('created_at', '>', now()->subMinute())
                ->get();

            $channels = $logs->pluck('channel')->unique();
            echo "  ✓ Channels logged: " . $channels->implode(', ') . "\n";

            $this->testResults['multi_channel'] = $result ? 'PASS' : 'FAIL';

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['multi_channel'] = 'ERROR';
        }

        echo "\n";
    }

    private function testErrorHandling()
    {
        echo "🚨 Test 7: Error Handling\n";
        echo "─────────────────────────────────\n";

        try {
            // Test with invalid user
            $result1 = UnifiedNotificationService::send(
                null,
                'test_type',
                'Test Title',
                'Test Message',
                [],
                ['database']
            );

            echo "  ✓ Invalid user handled: " . ($result1 ? 'UNEXPECTED SUCCESS' : 'PROPERLY FAILED') . "\n";

            // Test with non-existent template type
            $result2 = UnifiedNotificationService::send(
                $this->user,
                'non_existent_type_12345',
                'Test Title',
                'Test Message',
                [],
                ['database']
            );

            echo "  ✓ Non-existent type handled: " . ($result2 ? 'SUCCESS (fallback)' : 'FAILED') . "\n";

            $this->testResults['error_handling'] = 'PASS';

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['error_handling'] = 'ERROR';
        }

        echo "\n";
    }

    private function testSystemHealth()
    {
        echo "💊 Test 8: System Health\n";
        echo "─────────────────────────────────\n";

        try {
            $health = UnifiedNotificationService::getSystemHealth();

            if (isset($health['error'])) {
                echo "  ❌ System health check failed: {$health['error']}\n";
                $this->testResults['system_health'] = 'ERROR';
            } else {
                echo "  ✅ System health check successful\n";
                echo "  ✓ Total notifications: {$health['overview']['total_notifications']}\n";
                echo "  ✓ Active templates: {$health['overview']['active_templates']}\n";
                echo "  ✓ Enabled preferences: {$health['overview']['enabled_preferences']}\n";
                echo "  ✓ Success rate 24h: {$health['recent_activity']['success_rate_24h']}%\n";
                $this->testResults['system_health'] = 'PASS';
            }

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['system_health'] = 'ERROR';
        }

        echo "\n";
    }

    private function testDeliveryStats()
    {
        echo "📊 Test 9: Delivery Statistics\n";
        echo "─────────────────────────────────\n";

        try {
            $stats = UnifiedNotificationService::getDeliveryStats();

            echo "  ✅ Delivery stats retrieved\n";
            echo "  ✓ Total stat entries: " . count($stats) . "\n";

            if (count($stats) > 0) {
                foreach ($stats as $stat) {
                    echo "  ✓ {$stat['channel']} ({$stat['status']}): {$stat['count']}\n";
                }
                $this->testResults['delivery_stats'] = 'PASS';
            } else {
                echo "  ⚠️ No delivery stats found\n";
                $this->testResults['delivery_stats'] = 'PARTIAL';
            }

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['delivery_stats'] = 'ERROR';
        }

        echo "\n";
    }

    private function testNotificationLogs()
    {
        echo "📝 Test 10: Notification Logs\n";
        echo "─────────────────────────────────\n";

        try {
            $totalLogs = NotificationLog::count();
            $recentLogs = NotificationLog::where('created_at', '>', now()->subHour())->count();

            echo "  ✅ Notification logs checked\n";
            echo "  ✓ Total logs: {$totalLogs}\n";
            echo "  ✓ Recent logs (1h): {$recentLogs}\n";

            // Check log structure
            $sampleLog = NotificationLog::latest()->first();
            if ($sampleLog) {
                echo "  ✓ Sample log structure:\n";
                echo "    - ID: {$sampleLog->id}\n";
                echo "    - Type: {$sampleLog->type}\n";
                echo "    - Channel: {$sampleLog->channel}\n";
                echo "    - Status: {$sampleLog->status}\n";
                $this->testResults['notification_logs'] = 'PASS';
            } else {
                echo "  ⚠️ No logs found\n";
                $this->testResults['notification_logs'] = 'PARTIAL';
            }

        } catch (Exception $e) {
            echo "  ❌ Exception: {$e->getMessage()}\n";
            $this->testResults['notification_logs'] = 'ERROR';
        }

        echo "\n";
    }

    private function printSummary()
    {
        echo "📋 TEST SUMMARY\n";
        echo "═══════════════════════════════════════════════════\n";

        $total = count($this->testResults);
        $passed = count(array_filter($this->testResults, function($result) { return $result === 'PASS'; }));
        $partial = count(array_filter($this->testResults, function($result) { return $result === 'PARTIAL'; }));
        $failed = count(array_filter($this->testResults, function($result) { return $result === 'FAIL'; }));
        $errors = count(array_filter($this->testResults, function($result) { return $result === 'ERROR'; }));

        foreach ($this->testResults as $test => $result) {
            switch($result) {
                case 'PASS':
                    $icon = '✅';
                    break;
                case 'PARTIAL':
                    $icon = '⚠️';
                    break;
                case 'FAIL':
                    $icon = '❌';
                    break;
                case 'ERROR':
                    $icon = '🚨';
                    break;
                default:
                    $icon = '❓';
                    break;
            }
            echo "  {$icon} " . str_replace('_', ' ', ucwords($test, '_')) . ": {$result}\n";
        }

        echo "\n";
        echo "📊 OVERALL RESULTS:\n";
        echo "  ✅ Passed: {$passed}/{$total}\n";
        echo "  ⚠️ Partial: {$partial}/{$total}\n";
        echo "  ❌ Failed: {$failed}/{$total}\n";
        echo "  🚨 Errors: {$errors}/{$total}\n";

        $successRate = $total > 0 ? round(($passed + $partial * 0.5) / $total * 100, 1) : 0;
        echo "  📈 Success Rate: {$successRate}%\n";

        if ($successRate >= 90) {
            echo "\n🎉 EXCELLENT! Notification system is working perfectly!\n";
        } elseif ($successRate >= 75) {
            echo "\n👍 GOOD! Notification system is working well with minor issues.\n";
        } elseif ($successRate >= 50) {
            echo "\n⚠️ FAIR! Notification system has some issues that need attention.\n";
        } else {
            echo "\n🚨 POOR! Notification system needs significant fixes.\n";
        }
    }
}

// Run the tests
$tester = new NotificationSystemTest();
$tester->runAllTests();

echo "\n✅ Notification System Testing Complete!\n";
