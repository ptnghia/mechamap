<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Tạo roles và permissions cho hệ thống phân quyền MechaMap
     * theo hệ thống 5 cấp độ: Admin, Moderator, Senior, Member, Guest
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ====================================================================
        // PERMISSIONS - Định nghĩa các quyền cơ bản
        // ====================================================================

        $permissions = [
            // Forum & Thread Permissions
            'view-threads',
            'create-threads',
            'update-own-threads',
            'update-any-threads',
            'delete-own-threads',
            'delete-any-threads',
            'pin-threads',
            'lock-threads',

            // Comment Permissions
            'view-comments',
            'create-comments',
            'update-own-comments',
            'update-any-comments',
            'delete-own-comments',
            'delete-any-comments',

            // User Management
            'view-users',
            'create-users',
            'update-users',
            'delete-users',
            'ban-users',
            'manage-user-roles',

            // Admin Panel
            'access-admin-panel',
            'manage-settings',
            'manage-categories',
            'view-reports',
            'manage-reports',

            // Content Moderation
            'moderate-content',
            'approve-content',
            'review-reports',

            // Advanced Features
            'upload-files',
            'send-messages',
            'create-polls',
            'access-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ====================================================================
        // ROLES - Tạo 5 cấp độ người dùng
        // ====================================================================

        // 👑 ADMIN - Toàn quyền
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 🛡️ MODERATOR - Quản lý nội dung
        $moderatorRole = Role::create(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view-threads',
            'create-threads',
            'update-own-threads',
            'update-any-threads',
            'delete-any-threads',
            'pin-threads',
            'lock-threads',
            'view-comments',
            'create-comments',
            'update-own-comments',
            'update-any-comments',
            'delete-any-comments',
            'view-users',
            'ban-users',
            'access-admin-panel',
            'manage-categories',
            'view-reports',
            'manage-reports',
            'moderate-content',
            'approve-content',
            'review-reports',
            'upload-files',
            'send-messages',
        ]);

        // ⭐ SENIOR - Thành viên cao cấp
        $seniorRole = Role::create(['name' => 'senior']);
        $seniorRole->givePermissionTo([
            'view-threads',
            'create-threads',
            'update-own-threads',
            'delete-own-threads',
            'view-comments',
            'create-comments',
            'update-own-comments',
            'delete-own-comments',
            'view-users',
            'upload-files',
            'send-messages',
            'create-polls',
            'view-reports', // Có thể xem reports để báo cáo
        ]);

        // 👤 MEMBER - Thành viên cơ bản
        $memberRole = Role::create(['name' => 'member']);
        $memberRole->givePermissionTo([
            'view-threads',
            'create-threads',
            'update-own-threads',
            'delete-own-threads',
            'view-comments',
            'create-comments',
            'update-own-comments',
            'delete-own-comments',
            'view-users',
            'upload-files',
            'send-messages',
        ]);

        // 👁️ GUEST - Chỉ xem
        $guestRole = Role::create(['name' => 'guest']);
        $guestRole->givePermissionTo([
            'view-threads',
            'view-comments',
            'view-users',
        ]);

        $this->command->info('✅ Roles and Permissions Seeder completed!');
        $this->command->info('👑 Admin: ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('🛡️ Moderator: ' . $moderatorRole->permissions->count() . ' permissions');
        $this->command->info('⭐ Senior: ' . $seniorRole->permissions->count() . ' permissions');
        $this->command->info('👤 Member: ' . $memberRole->permissions->count() . ' permissions');
        $this->command->info('👁️ Guest: ' . $guestRole->permissions->count() . ' permissions');
        $this->command->info('🔐 Total permissions: ' . Permission::count());
    }
}
