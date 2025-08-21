<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * 🔐 MechaMap Permission Seeder
 *
 * Tạo tất cả permissions theo cấu trúc MechaMap
 * Dựa trên config/mechamap_permissions.php
 */
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🔐 Creating MechaMap Permissions...');

        // Lấy permission groups từ config
        $permissionGroups = config('mechamap_permissions.permission_groups');

        foreach ($permissionGroups as $category => $group) {
            $this->command->info("📁 Creating {$group['name']} permissions...");

            foreach ($group['permissions'] as $permissionName) {
                $this->createPermission($permissionName, $category, $group['name']);
            }
        }

        // Tạo thêm permissions đặc biệt cho MechaMap
        $this->createSpecialPermissions();

        $this->command->info('✅ All permissions created successfully!');
    }

    /**
     * Tạo một permission
     */
    private function createPermission(string $name, string $category, string $categoryDisplayName): void
    {
        // Parse permission name để lấy module và action
        $parts = explode('-', $name);
        $action = array_pop($parts);
        $module = implode('_', $parts);

        Permission::create([
            'name' => $name,
            'display_name' => $this->getDisplayName($name),
            'description' => $this->getDescription($name),
            'category' => $category,
            'module' => $module,
            'action' => $action,
            'is_system' => true,
            'is_active' => true,
            'created_by' => 1, // Super Admin
        ]);
    }

    /**
     * Lấy display name cho permission
     */
    private function getDisplayName(string $name): string
    {
        $displayNames = [
            // System permissions
            'manage-system' => 'Quản lý hệ thống',
            'manage-infrastructure' => 'Quản lý hạ tầng',
            'manage-database' => 'Quản lý cơ sở dữ liệu',
            'manage-security' => 'Quản lý bảo mật',
            'access-super-admin' => 'Truy cập Super Admin',
            'view-system-logs' => 'Xem logs hệ thống',
            'manage-backups' => 'Quản lý backup',

            // User permissions
            'view-users' => 'Xem người dùng',
            'create-users' => 'Tạo người dùng',
            'update-users' => 'Cập nhật người dùng',
            'delete-users' => 'Xóa người dùng',
            'ban-users' => 'Cấm người dùng',
            'manage-user-roles' => 'Quản lý vai trò người dùng',
            'verify-business-accounts' => 'Xác thực tài khoản doanh nghiệp',
            'manage-subscriptions' => 'Quản lý gói đăng ký',

            // Content permissions
            'manage-content' => 'Quản lý nội dung',
            'moderate-content' => 'Kiểm duyệt nội dung',
            'approve-content' => 'Phê duyệt nội dung',
            'delete-content' => 'Xóa nội dung',
            'manage-categories' => 'Quản lý danh mục',
            'manage-forums' => 'Quản lý diễn đàn',
            'pin-threads' => 'Ghim bài viết',
            'lock-threads' => 'Khóa bài viết',
            'feature-content' => 'Nổi bật nội dung',

            // Marketplace permissions
            'manage-marketplace' => 'Quản lý marketplace',
            'approve-products' => 'Phê duyệt sản phẩm',
            'manage-orders' => 'Quản lý đơn hàng',
            'manage-payments' => 'Quản lý thanh toán',
            'view-marketplace-analytics' => 'Xem phân tích marketplace',
            'manage-seller-accounts' => 'Quản lý tài khoản bán hàng',
            'handle-disputes' => 'Xử lý tranh chấp',
            'manage-commissions' => 'Quản lý hoa hồng',

            // Community permissions
            'manage-community' => 'Quản lý cộng đồng',
            'moderate-discussions' => 'Kiểm duyệt thảo luận',
            'manage-events' => 'Quản lý sự kiện',
            'send-announcements' => 'Gửi thông báo',
            'manage-user-groups' => 'Quản lý nhóm người dùng',

            // Analytics permissions
            'view-analytics' => 'Xem phân tích',
            'view-reports' => 'Xem báo cáo',
            'export-data' => 'Xuất dữ liệu',
            'manage-reports' => 'Quản lý báo cáo',

            // Admin access permissions
            'access-admin-panel' => 'Truy cập panel admin',
            'access-system-admin' => 'Truy cập quản trị hệ thống',
            'access-content-admin' => 'Truy cập quản trị nội dung',
            'access-marketplace-admin' => 'Truy cập quản trị marketplace',
            'access-community-admin' => 'Truy cập quản trị cộng đồng',

            // Basic permissions
            'view-content' => 'Xem nội dung',
            'create-threads' => 'Tạo bài viết',
            'create-comments' => 'Tạo bình luận',
            'upload-files' => 'Tải lên tệp',
            'send-messages' => 'Gửi tin nhắn',
            'create-polls' => 'Tạo bình chọn',
            'rate-products' => 'Đánh giá sản phẩm',
            'write-reviews' => 'Viết đánh giá',

            // Business permissions
            'sell-products' => 'Bán sản phẩm',
            'manage-own-products' => 'Quản lý sản phẩm của mình',
            'view-sales-analytics' => 'Xem phân tích bán hàng',
            'manage-business-profile' => 'Quản lý hồ sơ doanh nghiệp',
            'access-seller-dashboard' => 'Truy cập dashboard bán hàng',
            'upload-technical-files' => 'Tải lên tệp kỹ thuật',
            'manage-cad-files' => 'Quản lý tệp CAD',
            'access-b2b-features' => 'Truy cập tính năng B2B',
        ];

        return $displayNames[$name] ?? ucwords(str_replace('-', ' ', $name));
    }

    /**
     * Lấy description cho permission
     */
    private function getDescription(string $name): string
    {
        $descriptions = [
            'manage-system' => 'Quyền quản lý toàn bộ hệ thống, cấu hình server và infrastructure',
            'access-super-admin' => 'Quyền truy cập cao nhất, có thể thực hiện mọi hành động trong hệ thống',
            'view-users' => 'Quyền xem danh sách và thông tin người dùng',
            'manage-user-roles' => 'Quyền gán và thay đổi vai trò của người dùng',
            'moderate-content' => 'Quyền kiểm duyệt, phê duyệt hoặc từ chối nội dung do người dùng tạo',
            'manage-marketplace' => 'Quyền quản lý toàn bộ marketplace, sản phẩm và giao dịch',
            'access-admin-panel' => 'Quyền truy cập vào khu vực quản trị admin',
            // Thêm descriptions khác nếu cần
        ];

        return $descriptions[$name] ?? "Quyền {$this->getDisplayName($name)}";
    }

    /**
     * Tạo permissions đặc biệt cho MechaMap
     */
    private function createSpecialPermissions(): void
    {
        $this->command->info("🎯 Creating special MechaMap permissions...");

        $specialPermissions = [
            // FAQ Management
            [
                'name' => 'manage-faqs',
                'display_name' => 'Quản lý FAQ',
                'description' => 'Quyền quản lý câu hỏi thường gặp',
                'category' => 'content',
                'module' => 'faqs',
                'action' => 'manage',
            ],
            [
                'name' => 'view-faqs',
                'display_name' => 'Xem FAQ',
                'description' => 'Quyền xem danh sách FAQ',
                'category' => 'content',
                'module' => 'faqs',
                'action' => 'view',
            ],

            // Knowledge Base
            [
                'name' => 'manage-knowledge-base',
                'display_name' => 'Quản lý Knowledge Base',
                'description' => 'Quyền quản lý cơ sở tri thức',
                'category' => 'content',
                'module' => 'knowledge',
                'action' => 'manage',
            ],

            // CAD Files
            [
                'name' => 'manage-cad-library',
                'display_name' => 'Quản lý thư viện CAD',
                'description' => 'Quyền quản lý thư viện tệp CAD',
                'category' => 'business',
                'module' => 'cad',
                'action' => 'manage',
            ],

            // Showcase
            [
                'name' => 'manage-showcases',
                'display_name' => 'Quản lý Showcase',
                'description' => 'Quyền quản lý showcase sản phẩm',
                'category' => 'content',
                'module' => 'showcases',
                'action' => 'manage',
            ],

            // Roles & Permissions
            [
                'name' => 'manage-roles-permissions',
                'display_name' => 'Quản lý Roles & Permissions',
                'description' => 'Quyền quản lý vai trò và phân quyền hệ thống',
                'category' => 'system',
                'module' => 'roles',
                'action' => 'manage',
            ],
        ];

        foreach ($specialPermissions as $permission) {
            Permission::create(array_merge($permission, [
                'is_system' => true,
                'is_active' => true,
                'created_by' => 1,
            ]));
        }
    }
}
