<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckPermissionSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mechamap:check-permissions';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check MechaMap permission system status and generate report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Kiểm tra hệ thống phân quyền MechaMap...');
        $this->newLine();

        // 1. System Overview
        $this->info('📊 TỔNG QUAN HỆ THỐNG');
        $this->line('═══════════════════════════════════════');
        
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalUsers = User::count();
        $usersWithRoles = User::whereHas('roles')->count();

        $this->table(['Thống kê', 'Số lượng'], [
            ['Tổng Roles', $totalRoles],
            ['Tổng Permissions', $totalPermissions],
            ['Tổng Users', $totalUsers],
            ['Users có Spatie roles', $usersWithRoles],
            ['Coverage', round(($usersWithRoles / $totalUsers) * 100, 2) . '%'],
        ]);

        // 2. Role Groups Analysis
        $this->newLine();
        $this->info('🏢 PHÂN TÍCH THEO NHÓM ROLES');
        $this->line('═══════════════════════════════════════');

        $roleGroups = [
            'system_management' => 'Quản lý hệ thống',
            'community_management' => 'Quản lý cộng đồng',
            'community_members' => 'Thành viên cộng đồng',
            'business_partners' => 'Đối tác kinh doanh',
        ];

        $groupData = [];
        foreach ($roleGroups as $group => $name) {
            $count = User::where('role_group', $group)->count();
            $groupData[] = [$name, $group, $count];
        }

        $this->table(['Nhóm', 'Key', 'Số Users'], $groupData);

        // 3. Individual Roles Analysis
        $this->newLine();
        $this->info('👥 PHÂN TÍCH THEO TỪNG ROLE');
        $this->line('═══════════════════════════════════════');

        $roles = Role::withCount(['users', 'permissions'])->get();
        $roleData = [];
        
        foreach ($roles as $role) {
            $roleData[] = [
                $role->name,
                $role->users_count,
                $role->permissions_count,
            ];
        }

        $this->table(['Role Name', 'Users', 'Permissions'], $roleData);

        // 4. Permission Coverage
        $this->newLine();
        $this->info('🔐 PHÂN TÍCH PERMISSIONS');
        $this->line('═══════════════════════════════════════');

        $permissionGroups = [
            'manage-system' => 'System Management',
            'manage-content' => 'Content Management',
            'manage-marketplace' => 'Marketplace',
            'manage-community' => 'Community',
            'view-content' => 'Basic Access',
            'sell-products' => 'Business Features',
        ];

        $permissionData = [];
        foreach ($permissionGroups as $prefix => $name) {
            $count = Permission::where('name', 'like', $prefix . '%')->count();
            $permissionData[] = [$name, $prefix . '*', $count];
        }

        $this->table(['Nhóm Permission', 'Pattern', 'Số lượng'], $permissionData);

        // 5. System Health Check
        $this->newLine();
        $this->info('🏥 KIỂM TRA SỨC KHỎE HỆ THỐNG');
        $this->line('═══════════════════════════════════════');

        $issues = [];
        
        // Check users without roles
        $usersWithoutRoles = User::doesntHave('roles')->count();
        if ($usersWithoutRoles > 0) {
            $issues[] = "❌ {$usersWithoutRoles} users chưa có Spatie roles";
        }

        // Check users without role_group
        $usersWithoutGroup = User::whereNull('role_group')->count();
        if ($usersWithoutGroup > 0) {
            $issues[] = "⚠️ {$usersWithoutGroup} users chưa có role_group";
        }

        // Check orphaned roles
        $orphanedRoles = Role::doesntHave('users')->count();
        if ($orphanedRoles > 0) {
            $issues[] = "⚠️ {$orphanedRoles} roles không có users nào";
        }

        if (empty($issues)) {
            $this->info('✅ Hệ thống phân quyền hoạt động tốt!');
        } else {
            foreach ($issues as $issue) {
                $this->line($issue);
            }
        }

        // 6. Recommendations
        $this->newLine();
        $this->info('💡 KHUYẾN NGHỊ');
        $this->line('═══════════════════════════════════════');
        
        if ($usersWithRoles == $totalUsers) {
            $this->info('✅ Tất cả users đã được gán roles');
        } else {
            $this->warn('⚠️ Chạy: php artisan mechamap:assign-roles --force');
        }

        if ($totalRoles >= 14 && $totalPermissions >= 60) {
            $this->info('✅ Hệ thống roles & permissions đầy đủ');
        } else {
            $this->warn('⚠️ Chạy: php artisan db:seed --class=RolesAndPermissionsSeeder');
        }

        $this->newLine();
        $this->info('🎯 MechaMap User Management Restructure - Phase 1 hoàn thành!');

        return Command::SUCCESS;
    }
}
