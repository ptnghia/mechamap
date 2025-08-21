<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * 👑 MechaMap Role Seeder
 *
 * Tạo 14 roles theo cấu trúc 4 nhóm của MechaMap
 * Gán permissions tương ứng cho từng role
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing roles
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_has_permissions')->truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('👑 Creating MechaMap Roles...');

        // Lấy role hierarchy từ config
        $roleHierarchy = config('mechamap_permissions.role_hierarchy');
        $roleGroups = config('mechamap_permissions.role_groups');

        // Tạo roles theo từng nhóm
        foreach ($roleGroups as $groupKey => $group) {
            $this->command->info("📁 Creating {$group['name']} roles...");

            foreach ($group['roles'] as $roleName) {
                $this->createRole($roleName, $groupKey, $roleHierarchy[$roleName], $group);
            }
        }

        // Gán permissions cho roles
        $this->assignPermissionsToRoles();

        $this->command->info('✅ All roles created and permissions assigned successfully!');
    }

    /**
     * Tạo một role
     */
    private function createRole(string $name, string $group, int $hierarchyLevel, array $groupConfig): void
    {
        $roleData = $this->getRoleData($name, $group, $hierarchyLevel, $groupConfig);

        Role::create($roleData);
        $this->command->info("  ✓ Created role: {$roleData['display_name']}");
    }

    /**
     * Lấy data cho role
     */
    private function getRoleData(string $name, string $group, int $hierarchyLevel, array $groupConfig): array
    {
        $roleDefinitions = [
            // System Management Group
            'super_admin' => [
                'display_name' => 'Super Admin',
                'description' => 'Quyền cao nhất trong hệ thống, có thể thực hiện mọi hành động',
                'color' => 'danger',
                'icon' => 'fas fa-crown',
                'max_users' => 2,
                'default_permissions' => ['*'], // All permissions
            ],
            'system_admin' => [
                'display_name' => 'System Admin',
                'description' => 'Quản trị hệ thống và infrastructure, quản lý người dùng',
                'color' => 'warning',
                'icon' => 'fas fa-cogs',
                'max_users' => 5,
            ],
            'content_admin' => [
                'display_name' => 'Content Admin',
                'description' => 'Quản trị nội dung, trang và tri thức',
                'color' => 'info',
                'icon' => 'fas fa-edit',
                'max_users' => 10,
            ],

            // Community Management Group
            'content_moderator' => [
                'display_name' => 'Content Moderator',
                'description' => 'Kiểm duyệt nội dung, quản lý diễn đàn và bài viết',
                'color' => 'primary',
                'icon' => 'fas fa-shield-alt',
                'max_users' => 20,
            ],
            'marketplace_moderator' => [
                'display_name' => 'Marketplace Moderator',
                'description' => 'Kiểm duyệt marketplace, quản lý sản phẩm và giao dịch',
                'color' => 'success',
                'icon' => 'fas fa-store',
                'max_users' => 15,
            ],
            'community_moderator' => [
                'display_name' => 'Community Moderator',
                'description' => 'Quản lý cộng đồng, sự kiện và hoạt động người dùng',
                'color' => 'dark',
                'icon' => 'fas fa-users-cog',
                'max_users' => 25,
            ],

            // Community Members Group
            'senior_member' => [
                'display_name' => 'Thành viên cấp cao',
                'description' => 'Thành viên có kinh nghiệm, có thêm quyền trong cộng đồng',
                'color' => 'info',
                'icon' => 'fas fa-star',
                'max_users' => null,
            ],
            'member' => [
                'display_name' => 'Thành viên',
                'description' => 'Thành viên thường của cộng đồng',
                'color' => 'primary',
                'icon' => 'fas fa-user',
                'max_users' => null,
            ],

            'guest' => [
                'display_name' => 'Khách',
                'description' => 'Khách tham quan, quyền hạn hạn chế',
                'color' => 'secondary',
                'icon' => 'fas fa-eye',
                'max_users' => null,
            ],

            // Business Partners Group
            'verified_partner' => [
                'display_name' => 'Đối tác xác thực',
                'description' => 'Đối tác kinh doanh đã được xác thực, có quyền ưu tiên',
                'color' => 'warning',
                'icon' => 'fas fa-certificate',
                'max_users' => null,
            ],
            'manufacturer' => [
                'display_name' => 'Nhà sản xuất',
                'description' => 'Nhà sản xuất có thể bán sản phẩm và tệp kỹ thuật',
                'color' => 'dark',
                'icon' => 'fas fa-industry',
                'max_users' => null,
            ],
            'supplier' => [
                'display_name' => 'Nhà cung cấp',
                'description' => 'Nhà cung cấp có thể mua bán sản phẩm',
                'color' => 'success',
                'icon' => 'fas fa-truck',
                'max_users' => null,
            ],
            'brand' => [
                'display_name' => 'Nhãn hàng',
                'description' => 'Nhãn hàng có quyền xem và quảng cáo',
                'color' => 'purple',
                'icon' => 'fas fa-tags',
                'max_users' => null,
            ],
        ];

        $roleData = $roleDefinitions[$name] ?? [
            'display_name' => ucwords(str_replace('_', ' ', $name)),
            'description' => "Role {$name}",
            'color' => $groupConfig['color'],
            'icon' => $groupConfig['icon'],
        ];

        return array_merge($roleData, [
            'name' => $name,
            'role_group' => $group,
            'hierarchy_level' => $hierarchyLevel,
            'is_system' => true,
            'is_active' => true,
            'can_be_assigned' => true,
            'is_visible' => true,
            'created_by' => 1,
        ]);
    }

    /**
     * Gán permissions cho roles
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info("🔗 Assigning permissions to roles...");

        // Super Admin gets all permissions
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->permissions()->attach($allPermissions->pluck('id')->toArray(), [
                'is_granted' => true,
                'granted_by' => 1,
                'granted_at' => now(),
                'grant_reason' => 'Super Admin has all permissions by default',
            ]);
            $this->command->info("  ✓ Super Admin: ALL permissions ({$allPermissions->count()})");
        }

        // System Admin permissions
        $this->assignRolePermissions('system_admin', [
            'system', 'users', 'admin_access'
        ]);

        // Content Admin permissions
        $this->assignRolePermissions('content_admin', [
            'content', 'admin_access'
        ]);

        // Content Moderator permissions
        $this->assignRolePermissions('content_moderator', [
            'content', 'community', 'analytics'
        ]);

        // Marketplace Moderator permissions
        $this->assignRolePermissions('marketplace_moderator', [
            'marketplace', 'analytics'
        ]);

        // Community Moderator permissions
        $this->assignRolePermissions('community_moderator', [
            'community', 'analytics'
        ]);

        // Member permissions
        $this->assignRolePermissions('senior_member', [
            'basic', 'business'
        ]);

        $this->assignRolePermissions('member', [
            'basic'
        ]);

        $this->assignRolePermissions('student', [
            'basic'
        ]);

        $this->assignRolePermissions('guest', [
            'basic'
        ], ['view-content']); // Only view content

        // Business partner permissions
        $this->assignRolePermissions('verified_partner', [
            'basic', 'business'
        ]);

        $this->assignRolePermissions('manufacturer', [
            'basic', 'business'
        ]);

        $this->assignRolePermissions('supplier', [
            'basic', 'business'
        ]);

        $this->assignRolePermissions('brand', [
            'basic'
        ], ['view-content', 'manage-business-profile']);
    }

    /**
     * Gán permissions cho một role
     */
    private function assignRolePermissions(string $roleName, array $permissionGroups, array $specificPermissions = []): void
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return;
        }

        $permissionIds = [];

        // Lấy permissions từ groups
        $allPermissionGroups = config('mechamap_permissions.permission_groups');
        foreach ($permissionGroups as $groupKey) {
            if (isset($allPermissionGroups[$groupKey])) {
                $groupPermissions = Permission::whereIn('name', $allPermissionGroups[$groupKey]['permissions'])->pluck('id')->toArray();
                $permissionIds = array_merge($permissionIds, $groupPermissions);
            }
        }

        // Thêm specific permissions
        if (!empty($specificPermissions)) {
            $specificIds = Permission::whereIn('name', $specificPermissions)->pluck('id')->toArray();
            $permissionIds = array_merge($permissionIds, $specificIds);
        }

        // Remove duplicates
        $permissionIds = array_unique($permissionIds);

        // Attach permissions
        $attachData = [];
        foreach ($permissionIds as $permissionId) {
            $attachData[$permissionId] = [
                'is_granted' => true,
                'granted_by' => 1,
                'granted_at' => now(),
                'grant_reason' => "Default permissions for {$role->display_name}",
            ];
        }

        $role->permissions()->attach($attachData);
        $this->command->info("  ✓ {$role->display_name}: " . count($permissionIds) . " permissions");
    }
}
