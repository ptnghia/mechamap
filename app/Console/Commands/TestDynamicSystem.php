<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AdminMenuService;
use App\Services\PermissionService;

class TestDynamicSystem extends Command
{
    protected $signature = 'mechamap:test-dynamic {--user= : Test with specific user ID}';
    protected $description = 'Test MechaMap Dynamic Permission System - Phase 2';

    public function handle()
    {
        $this->info('🧪 Testing MechaMap Dynamic Permission System - Phase 2');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $userId = $this->option('user');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return Command::FAILURE;
            }
            $this->testUserPermissions($user);
        } else {
            $this->testAllRoleGroups();
        }

        return Command::SUCCESS;
    }

    private function testAllRoleGroups()
    {
        $roleGroups = [
            'system_management' => 'System Management',
            'community_management' => 'Community Management', 
            'community_members' => 'Community Members',
            'business_partners' => 'Business Partners'
        ];

        foreach ($roleGroups as $group => $name) {
            $this->info("🔍 Testing {$name} ({$group})");
            $this->line('───────────────────────────────────────');
            
            $users = User::where('role_group', $group)->take(2)->get();
            
            if ($users->isEmpty()) {
                $this->warn("No users found in {$group}");
                $this->newLine();
                continue;
            }

            foreach ($users as $user) {
                $this->testUserPermissions($user, false);
            }
            
            $this->newLine();
        }
    }

    private function testUserPermissions(User $user, bool $detailed = true)
    {
        if ($detailed) {
            $this->info("👤 Testing User: {$user->name}");
            $this->line('═══════════════════════════════════════');
        } else {
            $this->line("👤 {$user->name} ({$user->role})");
        }

        // 1. Basic Role Info
        $roleInfo = [
            ['Property', 'Value'],
            ['Role', $user->role],
            ['Role Group', $user->role_group ?? 'Not set'],
            ['Display Name', $user->getRoleDisplayName()],
            ['Group Name', $user->getRoleGroupDisplayName()],
            ['Role Level', PermissionService::getRoleLevel($user)],
            ['Permissions Count', $user->getAllPermissions()->count()],
        ];

        if ($detailed) {
            $this->table(['Property', 'Value'], array_slice($roleInfo, 1));
        }

        // 2. Access Permissions
        $accessTests = [
            'canAccessAdmin' => $user->canAccessAdmin(),
            'canAccessSystemAdmin' => $user->canAccessSystemAdmin(),
            'canAccessContentAdmin' => $user->canAccessContentAdmin(),
            'canAccessMarketplaceAdmin' => $user->canAccessMarketplaceAdmin(),
        ];

        if ($detailed) {
            $this->newLine();
            $this->info('🔐 Access Permissions:');
            foreach ($accessTests as $test => $result) {
                $icon = $result ? '✅' : '❌';
                $this->line("  {$icon} {$test}: " . ($result ? 'Yes' : 'No'));
            }
        }

        // 3. Admin Menu Test
        try {
            $adminMenu = AdminMenuService::getAdminMenu($user);
            $menuCount = count($adminMenu);
            
            if ($detailed) {
                $this->newLine();
                $this->info("📋 Admin Menu ({$menuCount} items):");
                foreach ($adminMenu as $item) {
                    $childrenCount = isset($item['children']) ? count($item['children']) : 0;
                    $childrenText = $childrenCount > 0 ? " ({$childrenCount} children)" : '';
                    $this->line("  • {$item['title']}{$childrenText}");
                }
            } else {
                $this->line("  📋 Menu: {$menuCount} items");
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Menu Error: " . $e->getMessage());
        }

        // 4. Key Permissions Test
        $keyPermissions = [
            'manage-system',
            'manage-content', 
            'manage-marketplace',
            'manage-community',
            'view-analytics',
            'moderate-content',
            'sell-products',
            'view-content'
        ];

        if ($detailed) {
            $this->newLine();
            $this->info('🔑 Key Permissions:');
            foreach ($keyPermissions as $permission) {
                $hasPermission = $user->hasPermissionTo($permission);
                $icon = $hasPermission ? '✅' : '❌';
                $this->line("  {$icon} {$permission}");
            }
        } else {
            $hasCount = 0;
            foreach ($keyPermissions as $permission) {
                if ($user->hasPermissionTo($permission)) {
                    $hasCount++;
                }
            }
            $this->line("  🔑 Key Permissions: {$hasCount}/{count($keyPermissions)}");
        }

        // 5. Marketplace Features (for business partners)
        if ($user->role_group === 'business_partners') {
            $marketplaceFeatures = PermissionService::getMarketplaceFeatures($user);
            
            if ($detailed) {
                $this->newLine();
                $this->info('🏪 Marketplace Features:');
                foreach ($marketplaceFeatures as $feature => $value) {
                    $this->line("  • {$feature}: " . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value));
                }
            } else {
                $featuresCount = count($marketplaceFeatures);
                $this->line("  🏪 Marketplace: {$featuresCount} features");
            }
        }

        if ($detailed) {
            $this->newLine();
            $this->line('═══════════════════════════════════════');
        }
    }

    private function testSystemIntegration()
    {
        $this->info('🔧 Testing System Integration');
        $this->line('───────────────────────────────────────');

        // Test AdminMenuService
        try {
            $testUser = User::where('role', 'super_admin')->first();
            if ($testUser) {
                $menu = AdminMenuService::getAdminMenu($testUser);
                $this->line("✅ AdminMenuService: Working ({count($menu)} menu items)");
            } else {
                $this->warn("⚠️ No super_admin user found for testing");
            }
        } catch (\Exception $e) {
            $this->error("❌ AdminMenuService: " . $e->getMessage());
        }

        // Test PermissionService
        try {
            $testUser = User::first();
            if ($testUser) {
                $level = PermissionService::getRoleLevel($testUser);
                $this->line("✅ PermissionService: Working (level: {$level})");
            }
        } catch (\Exception $e) {
            $this->error("❌ PermissionService: " . $e->getMessage());
        }

        // Test Config
        try {
            $roleGroups = config('mechamap_permissions.role_groups');
            $this->line("✅ Config: Working (" . count($roleGroups) . " role groups)");
        } catch (\Exception $e) {
            $this->error("❌ Config: " . $e->getMessage());
        }
    }
}
