<?php

namespace App\Services;

use App\Models\User;

/**
 * Service quản lý menu admin động theo permissions
 * Phase 2 - Dynamic Admin Panel
 */
class AdminMenuService
{
    /**
     * Lấy menu admin theo role của user
     *
     * @param User $user
     * @return array
     */
    public static function getAdminMenu(User $user): array
    {
        $menu = [];

        // Dashboard - Tất cả admin users đều có
        if ($user->canAccessAdmin()) {
            $menu[] = [
                'title' => 'Dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'route' => 'admin.dashboard',
                'permission' => 'access-admin-panel',
                'badge' => null,
            ];
        }

        // System Management Menu
        if ($user->isSystemManagement()) {
            $menu[] = [
                'title' => 'Quản lý hệ thống',
                'icon' => 'fas fa-cogs',
                'permission' => 'manage-system',
                'children' => [
                    [
                        'title' => 'Người dùng',
                        'icon' => 'fas fa-users',
                        'route' => 'admin.users.index',
                        'permission' => 'view-users',
                    ],
                    [
                        'title' => 'Phân quyền',
                        'icon' => 'fas fa-user-shield',
                        'route' => 'admin.roles.index',
                        'permission' => 'manage-user-roles',
                    ],
                    [
                        'title' => 'Cài đặt hệ thống',
                        'icon' => 'fas fa-sliders-h',
                        'route' => 'admin.settings.index',
                        'permission' => 'manage-system-settings',
                    ],
                    [
                        'title' => 'Logs hệ thống',
                        'icon' => 'fas fa-file-alt',
                        'route' => 'admin.logs.index',
                        'permission' => 'view-system-logs',
                    ],
                ]
            ];
        }

        // Content Management Menu
        if ($user->hasPermissionTo('manage-content') || $user->hasPermissionTo('moderate-content')) {
            $menu[] = [
                'title' => 'Quản lý nội dung',
                'icon' => 'fas fa-edit',
                'permission' => 'manage-content',
                'children' => [
                    [
                        'title' => 'Danh mục',
                        'icon' => 'fas fa-folder',
                        'route' => 'admin.categories.index',
                        'permission' => 'manage-categories',
                    ],
                    [
                        'title' => 'Diễn đàn',
                        'icon' => 'fas fa-comments',
                        'route' => 'admin.forums.index',
                        'permission' => 'manage-forums',
                    ],
                    [
                        'title' => 'Bài viết',
                        'icon' => 'fas fa-newspaper',
                        'route' => 'admin.threads.index',
                        'permission' => 'moderate-content',
                    ],
                    [
                        'title' => 'Báo cáo vi phạm',
                        'icon' => 'fas fa-flag',
                        'route' => 'admin.reports.index',
                        'permission' => 'view-reports',
                        'badge' => 'danger',
                    ],
                ]
            ];
        }

        // Marketplace Management Menu
        if ($user->hasPermissionTo('manage-marketplace') || $user->hasPermissionTo('approve-products')) {
            $menu[] = [
                'title' => 'Quản lý Marketplace',
                'icon' => 'fas fa-store',
                'permission' => 'manage-marketplace',
                'children' => [
                    [
                        'title' => 'Sản phẩm',
                        'icon' => 'fas fa-box',
                        'route' => 'admin.products.index',
                        'permission' => 'approve-products',
                    ],
                    [
                        'title' => 'Đơn hàng',
                        'icon' => 'fas fa-shopping-cart',
                        'route' => 'admin.orders.index',
                        'permission' => 'manage-orders',
                    ],
                    [
                        'title' => 'Nhà bán hàng',
                        'icon' => 'fas fa-handshake',
                        'route' => 'admin.sellers.index',
                        'permission' => 'manage-seller-accounts',
                    ],
                    [
                        'title' => 'Thanh toán',
                        'icon' => 'fas fa-credit-card',
                        'route' => 'admin.payments.index',
                        'permission' => 'manage-payments',
                    ],
                    [
                        'title' => 'Hoa hồng',
                        'icon' => 'fas fa-percentage',
                        'route' => 'admin.commissions.index',
                        'permission' => 'manage-commissions',
                    ],
                ]
            ];
        }

        // Community Management Menu
        if ($user->hasPermissionTo('manage-community') || $user->isCommunityManagement()) {
            $menu[] = [
                'title' => 'Quản lý cộng đồng',
                'icon' => 'fas fa-users-cog',
                'permission' => 'manage-community',
                'children' => [
                    [
                        'title' => 'Thành viên',
                        'icon' => 'fas fa-user-friends',
                        'route' => 'admin.members.index',
                        'permission' => 'view-users',
                    ],
                    [
                        'title' => 'Sự kiện',
                        'icon' => 'fas fa-calendar-alt',
                        'route' => 'admin.events.index',
                        'permission' => 'manage-events',
                    ],
                    [
                        'title' => 'Thông báo',
                        'icon' => 'fas fa-bullhorn',
                        'route' => 'admin.announcements.index',
                        'permission' => 'send-announcements',
                    ],
                    [
                        'title' => 'Nhóm người dùng',
                        'icon' => 'fas fa-layer-group',
                        'route' => 'admin.user-groups.index',
                        'permission' => 'manage-user-groups',
                    ],
                ]
            ];
        }

        // Analytics & Reports Menu
        if ($user->hasPermissionTo('view-analytics') || $user->hasPermissionTo('view-reports')) {
            $menu[] = [
                'title' => 'Báo cáo & Phân tích',
                'icon' => 'fas fa-chart-bar',
                'permission' => 'view-analytics',
                'children' => [
                    [
                        'title' => 'Dashboard Analytics',
                        'icon' => 'fas fa-chart-line',
                        'route' => 'admin.analytics.dashboard',
                        'permission' => 'view-analytics',
                    ],
                    [
                        'title' => 'Báo cáo người dùng',
                        'icon' => 'fas fa-users',
                        'route' => 'admin.analytics.users',
                        'permission' => 'view-reports',
                    ],
                    [
                        'title' => 'Báo cáo Marketplace',
                        'icon' => 'fas fa-store',
                        'route' => 'admin.analytics.marketplace',
                        'permission' => 'view-marketplace-analytics',
                    ],
                    [
                        'title' => 'Xuất dữ liệu',
                        'icon' => 'fas fa-download',
                        'route' => 'admin.analytics.export',
                        'permission' => 'export-data',
                    ],
                ]
            ];
        }

        // Chat & Messages (cho tất cả admin)
        if ($user->canAccessAdmin()) {
            $menu[] = [
                'title' => 'Tin nhắn',
                'icon' => 'fas fa-envelope',
                'route' => 'admin.messages.index',
                'permission' => 'send-messages',
                'badge' => 'info',
            ];
        }

        return self::filterMenuByPermissions($menu, $user);
    }

    /**
     * Lọc menu theo permissions của user
     *
     * @param array $menu
     * @param User $user
     * @return array
     */
    private static function filterMenuByPermissions(array $menu, User $user): array
    {
        $filteredMenu = [];

        foreach ($menu as $item) {
            // Kiểm tra permission cho item chính
            if (isset($item['permission']) && !$user->hasPermissionTo($item['permission'])) {
                continue;
            }

            // Lọc children nếu có
            if (isset($item['children'])) {
                $filteredChildren = [];
                foreach ($item['children'] as $child) {
                    if (!isset($child['permission']) || $user->hasPermissionTo($child['permission'])) {
                        $filteredChildren[] = $child;
                    }
                }
                
                // Chỉ thêm parent nếu có children
                if (!empty($filteredChildren)) {
                    $item['children'] = $filteredChildren;
                    $filteredMenu[] = $item;
                }
            } else {
                $filteredMenu[] = $item;
            }
        }

        return $filteredMenu;
    }

    /**
     * Lấy breadcrumb cho route hiện tại
     *
     * @param string $currentRoute
     * @param User $user
     * @return array
     */
    public static function getBreadcrumb(string $currentRoute, User $user): array
    {
        $menu = self::getAdminMenu($user);
        return self::findBreadcrumbPath($menu, $currentRoute);
    }

    /**
     * Tìm đường dẫn breadcrumb
     *
     * @param array $menu
     * @param string $currentRoute
     * @param array $path
     * @return array
     */
    private static function findBreadcrumbPath(array $menu, string $currentRoute, array $path = []): array
    {
        foreach ($menu as $item) {
            $currentPath = array_merge($path, [$item['title']]);
            
            if (isset($item['route']) && $item['route'] === $currentRoute) {
                return $currentPath;
            }
            
            if (isset($item['children'])) {
                $result = self::findBreadcrumbPath($item['children'], $currentRoute, $currentPath);
                if (!empty($result)) {
                    return $result;
                }
            }
        }
        
        return [];
    }
}
