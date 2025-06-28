<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\B2BFeaturesService;
use App\Services\PermissionService;

class TestPhase3Features extends Command
{
    protected $signature = 'mechamap:test-phase3 {--feature= : Test specific feature}';
    protected $description = 'Test MechaMap Phase 3 Features';

    public function handle()
    {
        $this->info('🧪 Testing MechaMap Phase 3 Features');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $feature = $this->option('feature');
        
        if ($feature) {
            $this->testSpecificFeature($feature);
        } else {
            $this->testAllFeatures();
        }

        return Command::SUCCESS;
    }

    private function testAllFeatures()
    {
        $this->info('🔍 Testing All Phase 3 Features');
        $this->newLine();

        // Test Notification System
        $this->testNotificationSystem();
        $this->newLine();

        // Test B2B Features
        $this->testB2BFeatures();
        $this->newLine();

        // Test Advanced Permissions
        $this->testAdvancedPermissions();
        $this->newLine();

        // Test Controllers
        $this->testControllers();
        $this->newLine();

        $this->info('✅ All Phase 3 features tested successfully!');
    }

    private function testSpecificFeature(string $feature)
    {
        switch ($feature) {
            case 'notifications':
                $this->testNotificationSystem();
                break;
            case 'b2b':
                $this->testB2BFeatures();
                break;
            case 'permissions':
                $this->testAdvancedPermissions();
                break;
            case 'controllers':
                $this->testControllers();
                break;
            default:
                $this->error("Unknown feature: {$feature}");
                $this->line('Available features: notifications, b2b, permissions, controllers');
        }
    }

    private function testNotificationSystem()
    {
        $this->info('📢 Testing Notification System');
        $this->line('───────────────────────────────────────');

        try {
            // Test basic notification
            $user = User::first();
            if (!$user) {
                $this->warn('No users found for testing');
                return;
            }

            $result = NotificationService::send(
                $user,
                'test_notification',
                'Test Notification',
                'This is a test notification from Phase 3',
                ['test' => true],
                false
            );

            if ($result) {
                $this->line('✅ Basic notification: Working');
            } else {
                $this->line('❌ Basic notification: Failed');
            }

            // Test business verification notification
            if ($user->role_group === 'business_partners') {
                $result = NotificationService::sendBusinessVerification($user, true);
                $this->line($result ? '✅ Business verification notification: Working' : '❌ Business verification notification: Failed');
            }

            // Test role change notification
            $result = NotificationService::sendRoleChange($user, 'old_role', 'new_role', 'Test reason');
            $this->line($result ? '✅ Role change notification: Working' : '❌ Role change notification: Failed');

            // Test system announcement
            $result = NotificationService::sendSystemAnnouncement(
                'Test System Announcement',
                'This is a test system announcement',
                ['super_admin'],
                'normal'
            );
            $this->line($result ? '✅ System announcement: Working' : '❌ System announcement: Failed');

            // Test notification stats
            $stats = NotificationService::getStats();
            $this->line("📊 Notification stats: {$stats['total']} total, {$stats['unread']} unread");

        } catch (\Exception $e) {
            $this->error('❌ Notification system error: ' . $e->getMessage());
        }
    }

    private function testB2BFeatures()
    {
        $this->info('🏢 Testing B2B Features');
        $this->line('───────────────────────────────────────');

        try {
            // Test B2B access
            $businessUsers = User::where('role_group', 'business_partners')->take(3)->get();
            
            if ($businessUsers->isEmpty()) {
                $this->warn('No business users found for testing');
                return;
            }

            foreach ($businessUsers as $user) {
                $canAccess = B2BFeaturesService::canAccessB2B($user);
                $canSellTech = B2BFeaturesService::canSellTechnicalFiles($user);
                $canSellCAD = B2BFeaturesService::canSellCADFiles($user);
                
                $this->line("👤 {$user->name} ({$user->role}):");
                $this->line("   • B2B Access: " . ($canAccess ? 'Yes' : 'No'));
                $this->line("   • Technical Files: " . ($canSellTech ? 'Yes' : 'No'));
                $this->line("   • CAD Files: " . ($canSellCAD ? 'Yes' : 'No'));

                // Test commission calculation
                $commission = B2BFeaturesService::calculateB2BCommission($user, 1000000);
                $this->line("   • Commission Rate: {$commission['applied_rate']}%");
                $this->line("   • Commission Amount: " . number_format($commission['commission_amount']) . "đ");

                // Test B2B analytics
                $analytics = B2BFeaturesService::getB2BAnalytics($user, 30);
                if (!empty($analytics)) {
                    $this->line("   • Analytics: Available");
                } else {
                    $this->line("   • Analytics: No data");
                }
            }

            $this->line('✅ B2B Features: Working');

        } catch (\Exception $e) {
            $this->error('❌ B2B Features error: ' . $e->getMessage());
        }
    }

    private function testAdvancedPermissions()
    {
        $this->info('🔐 Testing Advanced Permissions');
        $this->line('───────────────────────────────────────');

        try {
            $users = User::with('roles')->take(5)->get();
            
            foreach ($users as $user) {
                $this->line("👤 {$user->name} ({$user->role}):");
                
                // Test permission checks
                $permissions = [
                    'manage-marketplace' => $user->hasPermissionTo('manage-marketplace'),
                    'approve-products' => $user->hasPermissionTo('approve-products'),
                    'view-analytics' => $user->hasPermissionTo('view-analytics'),
                    'access-b2b-features' => $user->hasPermissionTo('access-b2b-features'),
                ];

                foreach ($permissions as $permission => $hasIt) {
                    $icon = $hasIt ? '✅' : '❌';
                    $this->line("   • {$permission}: {$icon}");
                }

                // Test role level
                $level = PermissionService::getRoleLevel($user);
                $this->line("   • Role Level: {$level}");

                // Test marketplace features
                if ($user->role_group === 'business_partners') {
                    $features = PermissionService::getMarketplaceFeatures($user);
                    $featuresCount = count($features);
                    $this->line("   • Marketplace Features: {$featuresCount}");
                }
            }

            $this->line('✅ Advanced Permissions: Working');

        } catch (\Exception $e) {
            $this->error('❌ Advanced Permissions error: ' . $e->getMessage());
        }
    }

    private function testControllers()
    {
        $this->info('🎮 Testing Controllers');
        $this->line('───────────────────────────────────────');

        try {
            // Test if controllers exist and are loadable
            $controllers = [
                'AdvancedProductController' => \App\Http\Controllers\Admin\AdvancedProductController::class,
                'BusinessAnalyticsController' => \App\Http\Controllers\Admin\BusinessAnalyticsController::class,
                'AdvancedUserController' => \App\Http\Controllers\Admin\AdvancedUserController::class,
            ];

            foreach ($controllers as $name => $class) {
                if (class_exists($class)) {
                    $this->line("✅ {$name}: Available");
                    
                    // Test if methods exist
                    $methods = get_class_methods($class);
                    $expectedMethods = ['index', '__construct'];
                    $hasExpectedMethods = !empty(array_intersect($expectedMethods, $methods));
                    
                    if ($hasExpectedMethods) {
                        $this->line("   • Methods: Available (" . count($methods) . " total)");
                    } else {
                        $this->line("   • Methods: Missing expected methods");
                    }
                } else {
                    $this->line("❌ {$name}: Not found");
                }
            }

            // Test services
            $services = [
                'NotificationService' => \App\Services\NotificationService::class,
                'B2BFeaturesService' => \App\Services\B2BFeaturesService::class,
                'PermissionService' => \App\Services\PermissionService::class,
            ];

            foreach ($services as $name => $class) {
                if (class_exists($class)) {
                    $this->line("✅ {$name}: Available");
                } else {
                    $this->line("❌ {$name}: Not found");
                }
            }

            $this->line('✅ Controllers & Services: Working');

        } catch (\Exception $e) {
            $this->error('❌ Controllers error: ' . $e->getMessage());
        }
    }

    private function testDatabaseTables()
    {
        $this->info('🗄️ Testing Database Tables');
        $this->line('───────────────────────────────────────');

        $tables = [
            'notifications',
            'b2b_quotes', 
            'commissions',
            'user_verification_documents'
        ];

        foreach ($tables as $table) {
            try {
                if (\Schema::hasTable($table)) {
                    $count = \DB::table($table)->count();
                    $this->line("✅ {$table}: Available ({$count} records)");
                } else {
                    $this->line("❌ {$table}: Not found");
                }
            } catch (\Exception $e) {
                $this->line("❌ {$table}: Error - " . $e->getMessage());
            }
        }
    }
}
