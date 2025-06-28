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
     * 🎯 MechaMap User Management Restructure - Phase 1
     * Tạo roles và permissions cho hệ thống phân quyền MechaMap mới
     * theo 4 nhóm chính với 12 roles chi tiết
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Bắt đầu tạo hệ thống phân quyền MechaMap mới...');

        // ====================================================================
        // PERMISSIONS - Định nghĩa các quyền theo từng module
        // ====================================================================

        $permissions = [
            // 🔧 SYSTEM MANAGEMENT PERMISSIONS
            'manage-system',
            'manage-infrastructure',
            'manage-database',
            'manage-security',
            'access-super-admin',
            'manage-all-users',
            'manage-system-settings',
            'view-system-logs',
            'manage-backups',

            // 👥 USER MANAGEMENT PERMISSIONS
            'view-users',
            'create-users',
            'update-users',
            'delete-users',
            'ban-users',
            'manage-user-roles',
            'verify-business-accounts',
            'manage-subscriptions',

            // 📝 CONTENT MANAGEMENT PERMISSIONS
            'manage-content',
            'moderate-content',
            'approve-content',
            'delete-content',
            'manage-categories',
            'manage-forums',
            'pin-threads',
            'lock-threads',
            'feature-content',

            // 🛒 MARKETPLACE PERMISSIONS
            'manage-marketplace',
            'approve-products',
            'manage-orders',
            'manage-payments',
            'view-marketplace-analytics',
            'manage-seller-accounts',
            'handle-disputes',
            'manage-commissions',

            // 🏘️ COMMUNITY PERMISSIONS
            'manage-community',
            'moderate-discussions',
            'manage-events',
            'send-announcements',
            'manage-user-groups',

            // 📊 ANALYTICS & REPORTS
            'view-analytics',
            'view-reports',
            'export-data',
            'manage-reports',

            // 🔐 ADMIN PANEL ACCESS
            'access-admin-panel',
            'access-system-admin',
            'access-content-admin',
            'access-marketplace-admin',
            'access-community-admin',

            // 📱 BASIC USER PERMISSIONS
            'view-content',
            'create-threads',
            'create-comments',
            'upload-files',
            'send-messages',
            'create-polls',
            'rate-products',
            'write-reviews',

            // 🏢 BUSINESS PERMISSIONS
            'sell-products',
            'manage-own-products',
            'view-sales-analytics',
            'manage-business-profile',
            'access-seller-dashboard',
            'upload-technical-files',
            'manage-cad-files',
            'access-b2b-features',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $this->command->info('✅ Đã tạo ' . count($permissions) . ' permissions');

        // ====================================================================
        // ROLES - Tạo 4 nhóm với 12 roles chi tiết
        // ====================================================================

        // 🔧 SYSTEM MANAGEMENT GROUP
        $this->command->info('🔧 Tạo System Management roles...');

        // 👑 SUPER ADMIN - Toàn quyền hệ thống
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // 🛡️ SYSTEM ADMIN - Quản lý hệ thống & users
        $systemAdminRole = Role::create(['name' => 'system_admin']);
        $systemAdminRole->givePermissionTo([
            'manage-infrastructure',
            'manage-database',
            'manage-security',
            'manage-all-users',
            'manage-system-settings',
            'view-system-logs',
            'manage-backups',
            'access-admin-panel',
            'access-system-admin',
            'view-analytics',
            'view-reports',
            'export-data',
        ]);

        // 🎯 CONTENT ADMIN - Quản lý nội dung & forum
        $contentAdminRole = Role::create(['name' => 'content_admin']);
        $contentAdminRole->givePermissionTo([
            'manage-content',
            'moderate-content',
            'approve-content',
            'delete-content',
            'manage-categories',
            'manage-forums',
            'pin-threads',
            'lock-threads',
            'feature-content',
            'access-admin-panel',
            'access-content-admin',
            'view-reports',
            'manage-reports',
        ]);

        // 👥 COMMUNITY MANAGEMENT GROUP
        $this->command->info('👥 Tạo Community Management roles...');

        // 📝 CONTENT MODERATOR - Kiểm duyệt nội dung
        $contentModeratorRole = Role::create(['name' => 'content_moderator']);
        $contentModeratorRole->givePermissionTo([
            'moderate-content',
            'approve-content',
            'delete-content',
            'pin-threads',
            'lock-threads',
            'feature-content',
            'view-reports',
            'manage-reports',
            'view-content',
            'create-threads',
            'create-comments',
            'upload-files',
            'send-messages',
        ]);

        // 🛒 MARKETPLACE MODERATOR - Quản lý marketplace
        $marketplaceModeratorRole = Role::create(['name' => 'marketplace_moderator']);
        $marketplaceModeratorRole->givePermissionTo([
            'manage-marketplace',
            'approve-products',
            'manage-orders',
            'view-marketplace-analytics',
            'handle-disputes',
            'manage-seller-accounts',
            'access-admin-panel',
            'access-marketplace-admin',
            'view-reports',
            'manage-reports',
        ]);

        // 🏘️ COMMUNITY MODERATOR - Quản lý cộng đồng
        $communityModeratorRole = Role::create(['name' => 'community_moderator']);
        $communityModeratorRole->givePermissionTo([
            'manage-community',
            'moderate-discussions',
            'manage-events',
            'send-announcements',
            'manage-user-groups',
            'view-users',
            'ban-users',
            'access-admin-panel',
            'access-community-admin',
            'view-reports',
        ]);

        // 🌟 COMMUNITY MEMBERS GROUP
        $this->command->info('🌟 Tạo Community Members roles...');

        // ⭐ SENIOR MEMBER - Thành viên cao cấp
        $seniorMemberRole = Role::create(['name' => 'senior_member']);
        $seniorMemberRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'upload-files',
            'send-messages',
            'create-polls',
            'rate-products',
            'write-reviews',
        ]);

        // 👤 MEMBER - Thành viên cơ bản
        $memberRole = Role::create(['name' => 'member']);
        $memberRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'upload-files',
            'send-messages',
            'rate-products',
            'write-reviews',
        ]);

        // 👁️ GUEST - Khách tham quan
        $guestRole = Role::create(['name' => 'guest']);
        $guestRole->givePermissionTo([
            'view-content',
        ]);

        // 🎓 STUDENT - Sinh viên
        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'upload-files',
            'send-messages',
        ]);

        // 🏢 BUSINESS PARTNERS GROUP
        $this->command->info('🏢 Tạo Business Partners roles...');

        // 🏭 MANUFACTURER - Nhà sản xuất
        $manufacturerRole = Role::create(['name' => 'manufacturer']);
        $manufacturerRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'sell-products',
            'manage-own-products',
            'view-sales-analytics',
            'manage-business-profile',
            'access-seller-dashboard',
            'upload-technical-files',
            'manage-cad-files',
            'access-b2b-features',
            'upload-files',
            'send-messages',
            'rate-products',
            'write-reviews',
        ]);

        // 🏪 SUPPLIER - Nhà cung cấp
        $supplierRole = Role::create(['name' => 'supplier']);
        $supplierRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'sell-products',
            'manage-own-products',
            'view-sales-analytics',
            'manage-business-profile',
            'access-seller-dashboard',
            'access-b2b-features',
            'upload-files',
            'send-messages',
            'rate-products',
            'write-reviews',
        ]);

        // 🏷️ BRAND - Nhãn hàng/Thương hiệu
        $brandRole = Role::create(['name' => 'brand']);
        $brandRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'manage-business-profile',
            'upload-files',
            'send-messages',
            'rate-products',
            'write-reviews',
        ]);

        // ✅ VERIFIED PARTNER - Đối tác xác thực
        $verifiedPartnerRole = Role::create(['name' => 'verified_partner']);
        $verifiedPartnerRole->givePermissionTo([
            'view-content',
            'create-threads',
            'create-comments',
            'sell-products',
            'manage-own-products',
            'view-sales-analytics',
            'manage-business-profile',
            'access-seller-dashboard',
            'upload-technical-files',
            'manage-cad-files',
            'access-b2b-features',
            'upload-files',
            'send-messages',
            'rate-products',
            'write-reviews',
        ]);

        $this->command->info('✅ MechaMap User Management Restructure - Phase 1 hoàn thành!');
        $this->command->info('🔧 System Management: 3 roles');
        $this->command->info('👥 Community Management: 3 roles');
        $this->command->info('🌟 Community Members: 4 roles');
        $this->command->info('🏢 Business Partners: 4 roles');
        $this->command->info('📊 Tổng cộng: ' . Role::count() . ' roles với ' . Permission::count() . ' permissions');
    }
}
