<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionCheck
{
    /**
     * 🔐 MechaMap Admin Permission Middleware
     *
     * Hỗ trợ Multiple Roles System với permission checking toàn diện
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        // ✅ BƯỚC 1: Kiểm tra quyền admin cơ bản
        if (!$this->canAccessAdminPanel($user)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // ✅ BƯỚC 2: Nếu có permission cụ thể được chỉ định
        if ($permission) {
            if (!$this->hasPermission($user, $permission)) {
                abort(403, 'Bạn không có quyền thực hiện hành động này.');
            }
        }

        return $next($request);
    }

    /**
     * ✅ Kiểm tra user có thể truy cập admin panel không
     */
    private function canAccessAdminPanel($user): bool
    {
        // Super Admin & System Admin: Full access
        if (in_array($user->role, ['super_admin', 'system_admin', 'admin'])) {
            return true;
        }

        // Legacy moderator role
        if ($user->role === 'moderator') {
            return true;
        }

        // Multiple Roles: Kiểm tra có role admin/moderator nào không
        if ($this->hasAdminRoleViaMultipleRoles($user)) {
            return true;
        }

        // Specific moderator roles
        if (in_array($user->role, [
            'content_moderator',
            'community_moderator',
            'marketplace_moderator'
        ])) {
            return true;
        }

        return false;
    }

    /**
     * ✅ Kiểm tra user có admin role qua Multiple Roles system không
     */
    private function hasAdminRoleViaMultipleRoles($user): bool
    {
        // Load roles nếu chưa load
        if (!$user->relationLoaded('roles')) {
            $user->load('activeRoles');
        }

        // Kiểm tra có role admin/moderator nào trong multiple roles không
        $adminRoles = [
            'super_admin', 'system_admin', 'admin', 'moderator',
            'content_moderator', 'community_moderator', 'marketplace_moderator'
        ];

        return $user->activeRoles()
            ->whereIn('name', $adminRoles)
            ->exists();
    }

    /**
     * ✅ Kiểm tra permission với Multiple Roles system
     */
    private function hasPermission($user, string $permission): bool
    {
        // Super Admin & System Admin: Tất cả quyền
        if (in_array($user->role, ['super_admin', 'system_admin', 'admin'])) {
            return true;
        }

        // Multiple Roles: Kiểm tra permission qua roles
        if ($this->hasPermissionViaMultipleRoles($user, $permission)) {
            return true;
        }

        // Legacy: Kiểm tra permission theo role cũ
        if ($this->hasLegacyPermission($user, $permission)) {
            return true;
        }

        return false;
    }

    /**
     * ✅ Kiểm tra permission qua Multiple Roles system
     */
    private function hasPermissionViaMultipleRoles($user, string $permission): bool
    {
        // Sử dụng method có sẵn trong User model
        if (method_exists($user, 'hasPermissionViaRoles')) {
            return $user->hasPermissionViaRoles($permission);
        }

        // Fallback: Load roles và kiểm tra permissions
        if (!$user->relationLoaded('roles')) {
            $user->load(['activeRoles.permissions']);
        }

        return $user->activeRoles()
            ->whereHas('permissions', function($query) use ($permission) {
                $query->where('permissions.name', $permission)
                      ->where('role_has_permissions.is_granted', true);
            })->exists();
    }

    /**
     * ✅ Legacy permission checking cho backward compatibility
     */
    private function hasLegacyPermission($user, string $permission): bool
    {
        // Comprehensive permissions cho tất cả moderator roles
        $allModeratorPermissions = [
            // 👥 User Management
            'view_users', 'edit_users', 'create_users', 'delete_users',
            'manage_user_roles', 'manage_user_permissions',

            // 📝 Content Management
            'view_threads', 'edit_threads', 'delete_threads', 'create_threads',
            'view_comments', 'edit_comments', 'delete_comments',
            'view_posts', 'edit_posts', 'delete_posts',
            'manage_categories', 'manage_forums',

            // 🛡️ Moderation
            'view_reports', 'view-reports', 'manage_reports', 'manage-reports',
            'handle_reports', 'moderate-content', 'moderate_content',
            'ban_users', 'suspend_users',

            // 🏪 Marketplace
            'view_products', 'edit_products', 'delete_products',
            'manage_orders', 'view_orders', 'process_orders',
            'manage_marketplace', 'view_marketplace_stats',

            // 📄 Pages & Knowledge
            'view_pages', 'edit_pages', 'create_pages', 'delete_pages',
            'manage_knowledge_base', 'view_knowledge_base',

            // 🎨 Showcases
            'view_showcases', 'edit_showcases', 'delete_showcases',
            'manage_showcases',

            // ⚙️ Settings
            'view_settings_general', 'edit_settings_general',
            'view_settings_forum', 'edit_settings_forum',
            'view_settings_user', 'edit_settings_user',
            'view_settings_marketplace', 'edit_settings_marketplace',

            // 🔐 Roles & Permissions
            'manage_roles', 'view_roles', 'edit_roles',
            'manage_permissions', 'view_permissions',

            // 📊 Analytics & Reports
            'view_analytics', 'view_statistics', 'generate_reports',
            'view_dashboard_stats',

            // 💬 Communication
            'manage_notifications', 'send_announcements',
            'manage_messages', 'view_chat_logs',

            // 🔧 Technical
            'manage_cache', 'view_logs', 'manage_backups',
            'manage_seo', 'manage_search_index',
        ];

        // Kiểm tra role-specific permissions
        $rolePermissions = $this->getRoleSpecificPermissions($user->role);
        $allowedPermissions = array_merge($allModeratorPermissions, $rolePermissions);

        return in_array($permission, $allowedPermissions);
    }

    /**
     * ✅ Lấy permissions cụ thể theo role
     */
    private function getRoleSpecificPermissions(string $role): array
    {
        return match($role) {
            'content_moderator' => [
                'manage_content', 'moderate_forums', 'manage_threads',
                'manage_comments', 'handle_content_reports'
            ],
            'community_moderator' => [
                'manage_community', 'moderate_users', 'handle_user_reports',
                'manage_user_interactions', 'send_community_announcements'
            ],
            'marketplace_moderator' => [
                'manage_marketplace_full', 'moderate_products', 'handle_marketplace_reports',
                'manage_seller_accounts', 'process_marketplace_disputes'
            ],
            'moderator' => [
                'full_moderation_access', 'all_content_permissions',
                'all_user_permissions', 'all_marketplace_permissions'
            ],
            default => []
        };
    }
}
