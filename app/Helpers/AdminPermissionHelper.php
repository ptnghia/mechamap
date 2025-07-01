<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AdminPermissionHelper
{
    /**
     * Kiểm tra user hiện tại có permission không
     *
     * @param string $permission
     * @return bool
     */
    public static function can(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission($permission);
    }

    /**
     * Kiểm tra user hiện tại có bất kỳ permission nào không
     *
     * @param array $permissions
     * @return bool
     */
    public static function canAny(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyPermission($permissions);
    }

    /**
     * Kiểm tra user hiện tại có tất cả permissions không
     *
     * @param array $permissions
     * @return bool
     */
    public static function canAll(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAllPermissions($permissions);
    }

    /**
     * Kiểm tra user hiện tại có thể truy cập route không
     *
     * @param string $routeName
     * @return bool
     */
    public static function canAccessRoute(string $routeName): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->canAccessRoute($routeName);
    }

    /**
     * Lấy tất cả permissions của user hiện tại
     *
     * @return array
     */
    public static function getUserPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getUserPermissions();
    }

    /**
     * Kiểm tra user có phải admin không
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $role = Auth::user()->role;
        return in_array($role, ['admin', 'super_admin']);
    }

    /**
     * Kiểm tra user có phải moderator không
     *
     * @return bool
     */
    public static function isModerator(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->role === 'moderator';
    }

    /**
     * Kiểm tra user có thể quản lý users không
     *
     * @return bool
     */
    public static function canManageUsers(): bool
    {
        return self::canAny(['view_users', 'edit_users', 'ban_users']);
    }

    /**
     * Kiểm tra user có thể quản lý content không
     *
     * @return bool
     */
    public static function canManageContent(): bool
    {
        return self::canAny(['view_threads', 'edit_threads', 'view_comments', 'edit_comments']);
    }

    /**
     * Kiểm tra user có thể quản lý marketplace không
     *
     * @return bool
     */
    public static function canManageMarketplace(): bool
    {
        return self::canAny(['view_products', 'view_orders', 'manage_sellers']);
    }

    /**
     * Kiểm tra user có thể xem analytics không
     *
     * @return bool
     */
    public static function canViewAnalytics(): bool
    {
        return self::canAny(['view_statistics', 'view_analytics']);
    }

    /**
     * Kiểm tra user có thể quản lý system không
     *
     * @return bool
     */
    public static function canManageSystem(): bool
    {
        return self::canAny(['view_settings', 'manage_settings', 'manage_seo']);
    }

    /**
     * Lấy menu items mà user có thể truy cập
     *
     * @return array
     */
    public static function getAccessibleMenuItems(): array
    {
        $menuItems = [];

        // Dashboard - tất cả admin/moderator đều có thể truy cập
        $menuItems['dashboard'] = true;

        // Quản lý nội dung
        if (self::canManageContent()) {
            $menuItems['content'] = [
                'forum_management' => self::canAny(['manage_categories', 'manage_forums']),
                'threads' => self::can('view_threads'),
                'comments' => self::can('view_comments'),
                'showcases' => self::can('view_showcases'),
            ];
        }

        // Quản lý người dùng
        if (self::canManageUsers()) {
            $menuItems['users'] = [
                'view_users' => self::can('view_users'),
                'manage_admins' => self::can('manage_admins'),
                'manage_roles' => self::can('manage_roles'),
            ];
        }

        // Thị trường
        if (self::canManageMarketplace()) {
            $menuItems['marketplace'] = [
                'products' => self::can('view_products'),
                'orders' => self::can('view_orders'),
                'payments' => self::can('view_payments'),
                'sellers' => self::can('manage_sellers'),
            ];
        }

        // Quản lý kỹ thuật
        if (self::canAny(['view_cad_files', 'view_materials', 'view_standards'])) {
            $menuItems['technical'] = [
                'cad_files' => self::can('view_cad_files'),
                'materials' => self::can('view_materials'),
                'standards' => self::can('view_standards'),
            ];
        }

        // Thống kê & phân tích
        if (self::canViewAnalytics()) {
            $menuItems['analytics'] = [
                'statistics' => self::can('view_statistics'),
                'analytics' => self::can('view_analytics'),
            ];
        }

        // Giao tiếp
        if (self::canAny(['view_messages', 'send_messages'])) {
            $menuItems['communication'] = [
                'messages' => self::can('view_messages'),
                'chat' => self::can('view_messages'),
            ];
        }

        // Hệ thống
        if (self::canManageSystem()) {
            $menuItems['system'] = [
                'settings' => self::can('view_settings'),
                'seo' => self::can('manage_seo'),
                'performance' => self::can('manage_performance'),
                'locations' => self::can('manage_locations'),
            ];
        }

        // Profile - tất cả admin/moderator đều có thể truy cập
        $menuItems['profile'] = true;

        return $menuItems;
    }
}
