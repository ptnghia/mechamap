<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ConfirmRoleSystem extends Command
{
    protected $signature = 'mechamap:confirm-roles';
    protected $description = 'Xác nhận hệ thống phân quyền MechaMap';

    public function handle()
    {
        $this->info('🎯 XÁC NHẬN HỆ THỐNG PHÂN QUYỀN MECHAMAP');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        // Tổng quan
        $this->info('📊 TỔNG QUAN:');
        $this->table(['Thống kê', 'Số lượng'], [
            ['Tổng roles', Role::count()],
            ['Tổng permissions', Permission::count()],
            ['Tổng users', User::count()],
        ]);

        // Chi tiết từng nhóm
        $roleGroups = [
            'system_management' => [
                'name' => '🔧 QUẢN LÝ HỆ THỐNG',
                'description' => 'Nhóm có toàn quyền quản lý hệ thống, infrastructure và bảo mật'
            ],
            'community_management' => [
                'name' => '👥 QUẢN LÝ CỘNG ĐỒNG', 
                'description' => 'Nhóm kiểm duyệt và quản lý nội dung, marketplace, cộng đồng'
            ],
            'community_members' => [
                'name' => '🌟 THÀNH VIÊN CỘNG ĐỒNG',
                'description' => 'Các thành viên tham gia cộng đồng với quyền hạn theo cấp độ'
            ],
            'business_partners' => [
                'name' => '🏢 ĐỐI TÁC KINH DOANH',
                'description' => 'Các đối tác kinh doanh với quyền marketplace và B2B'
            ]
        ];

        foreach ($roleGroups as $group => $info) {
            $this->newLine();
            $this->info($info['name']);
            $this->line('───────────────────────────────────────');
            $this->line($info['description']);
            $this->newLine();
            
            $users = User::where('role_group', $group)->get();
            $roles = $users->pluck('role')->unique();
            
            $this->line("👥 Tổng users: {$users->count()}");
            $this->line("🎭 Roles: " . $roles->implode(', '));
            $this->newLine();
            
            // Chi tiết từng role
            $roleData = [];
            foreach ($roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $userCount = User::where('role', $roleName)->count();
                    $permissionCount = $role->permissions->count();
                    
                    // Lấy một số permissions quan trọng
                    $keyPermissions = $role->permissions->take(3)->pluck('name')->toArray();
                    $permissionsList = implode(', ', $keyPermissions);
                    if ($role->permissions->count() > 3) {
                        $permissionsList .= '...';
                    }
                    
                    $roleData[] = [
                        $roleName,
                        $userCount,
                        $permissionCount,
                        $permissionsList
                    ];
                }
            }
            
            if (!empty($roleData)) {
                $this->table(['Role', 'Users', 'Permissions', 'Key Permissions'], $roleData);
            }
        }

        // Permissions summary
        $this->newLine();
        $this->info('🔐 TÓM TẮT PERMISSIONS THEO NHÓM:');
        $this->line('───────────────────────────────────────');
        
        $permissionGroups = [
            'manage-system' => 'Quản lý hệ thống',
            'manage-content' => 'Quản lý nội dung', 
            'manage-marketplace' => 'Quản lý marketplace',
            'manage-community' => 'Quản lý cộng đồng',
            'view-content' => 'Truy cập cơ bản',
            'sell-products' => 'Tính năng kinh doanh',
            'access-admin' => 'Truy cập admin'
        ];

        $permissionData = [];
        foreach ($permissionGroups as $prefix => $name) {
            $count = Permission::where('name', 'like', $prefix . '%')->count();
            $permissionData[] = [$name, $prefix . '*', $count];
        }
        
        $this->table(['Nhóm Permission', 'Pattern', 'Số lượng'], $permissionData);

        $this->newLine();
        $this->info('✅ Hệ thống phân quyền MechaMap đã được xác nhận!');
        
        return Command::SUCCESS;
    }
}
