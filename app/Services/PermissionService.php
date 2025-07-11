<?php

namespace App\Services;

use App\Models\User;

/**
 * Service quản lý phân quyền chi tiết cho hệ thống MechaMap
 * 🎯 Updated for MechaMap User Management Restructure - Phase 1
 * Hỗ trợ 4 nhóm với 14 roles và 64 permissions
 */
class PermissionService
{
    /**
     * Kiểm tra user có permission không (sử dụng Spatie Permission)
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        // Super Admin có tất cả quyền
        if ($user->role === 'super_admin') {
            return true;
        }

        // Sử dụng Spatie Permission
        return $user->hasPermissionTo($permission);
    }

    /**
     * Kiểm tra user có thể truy cập admin panel không
     *
     * @param User $user
     * @return bool
     */
    public static function canAccessAdmin(User $user): bool
    {
        return $user->canAccessAdmin();
    }

    /**
     * Kiểm tra user có thể truy cập marketplace không
     *
     * @param User $user
     * @return bool
     */
    public static function canAccessMarketplace(User $user): bool
    {
        return in_array($user->role_group, ['business_partners']) ||
               $user->hasPermissionTo('manage-marketplace') ||
               $user->hasPermissionTo('view-content');
    }

    /**
     * Kiểm tra user có thể bán hàng không
     *
     * @param User $user
     * @return bool
     */
    public static function canSell(User $user): bool
    {
        return $user->hasPermissionTo('sell-products');
    }

    /**
     * Kiểm tra user có thể mua hàng không
     *
     * @param User $user
     * @return bool
     */
    public static function canBuy(User $user): bool
    {
        // Guest không thể mua hàng - cần đăng ký
        return !in_array($user->role, ['guest']) &&
               auth()->check() &&
               $user->hasPermissionTo('view-content');
    }

    /**
     * Lấy role hierarchy level
     *
     * @param User $user
     * @return int
     */
    public static function getRoleLevel(User $user): int
    {
        $hierarchy = config('mechamap_permissions.role_hierarchy');
        return $hierarchy[$user->role] ?? 999;
    }

    /**
     * Kiểm tra user có level cao hơn user khác không
     *
     * @param User $user1
     * @param User $user2
     * @return bool
     */
    public static function hasHigherLevel(User $user1, User $user2): bool
    {
        return self::getRoleLevel($user1) < self::getRoleLevel($user2);
    }

    /**
     * Lấy marketplace features cho role
     *
     * @param User $user
     * @return array
     */
    public static function getMarketplaceFeatures(User $user): array
    {
        $features = config('mechamap_permissions.marketplace_features');
        return $features[$user->role] ?? [];
    }

    /**
     * Kiểm tra user có thể moderate user khác không
     *
     * @param User $moderator
     * @param User $target
     * @return bool
     */
    public static function canModerate(User $moderator, User $target): bool
    {
        // Super admin có thể moderate tất cả
        if ($moderator->role === 'super_admin') {
            return true;
        }

        // Không thể moderate user cùng level hoặc cao hơn
        return self::hasHigherLevel($moderator, $target);
    }

    /**
     * Lấy tất cả permissions của user
     *
     * @param User $user
     * @return array
     */
    public static function getUserPermissions(User $user): array
    {
        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Kiểm tra user có thể truy cập route không
     *
     * @param User $user
     * @param string $routeName
     * @return bool
     */
    public static function canAccessRoute(User $user, string $routeName): bool
    {
        // Super admin có thể truy cập tất cả
        if ($user->role === 'super_admin') {
            return true;
        }

        $adminRoutes = config('mechamap_permissions.admin_routes');

        // Kiểm tra theo role group
        foreach ($adminRoutes as $group => $routes) {
            if ($user->role_group === $group) {
                foreach ($routes as $pattern) {
                    if (str_contains($pattern, '*')) {
                        $regex = str_replace('*', '.*', $pattern);
                        if (preg_match('/^' . $regex . '$/', $routeName)) {
                            return true;
                        }
                    } elseif ($pattern === $routeName) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
