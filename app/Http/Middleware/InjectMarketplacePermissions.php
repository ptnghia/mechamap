<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\UnifiedMarketplacePermissionService;
use Illuminate\Support\Facades\View;

class InjectMarketplacePermissions
{
    /**
     * Handle an incoming request.
     * Inject marketplace permissions into all views
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current user
        $user = auth()->user();

        // Default permissions for guests
        $marketplacePermissions = [
            'can_access_marketplace' => false,
            'can_buy' => [],
            'can_sell' => [],
            'allowed_buy_types' => [],
            'allowed_sell_types' => [],
            'role' => 'guest',
            'role_display' => 'Khách',
            'marketplace_features' => [
                'can_create_products' => false,
                'can_view_cart' => false,
                'can_checkout' => false,
                'can_view_orders' => false,
                'can_manage_seller_account' => false,
            ]
        ];

        if ($user) {
            $role = $user->role ?? 'guest';

            // Get permission arrays using unified service
            $allowedBuyTypes = UnifiedMarketplacePermissionService::getAllowedBuyTypes($user);
            $allowedSellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

            // Check if user has any marketplace access
            $hasMarketplaceAccess = UnifiedMarketplacePermissionService::canAccessMarketplace($user);

            // Get comprehensive marketplace features from unified service
            $marketplaceFeatures = UnifiedMarketplacePermissionService::getMarketplaceFeatures($user);

            $marketplacePermissions = [
                'can_access_marketplace' => $hasMarketplaceAccess,
                'can_buy' => $allowedBuyTypes,
                'can_sell' => $allowedSellTypes,
                'allowed_buy_types' => $allowedBuyTypes,
                'allowed_sell_types' => $allowedSellTypes,
                'role' => $role,
                'role_display' => $this->getRoleDisplayName($role),
                'marketplace_features' => $marketplaceFeatures,
                'commission_rate' => UnifiedMarketplacePermissionService::getCommissionRate($user),
                'is_business_verified' => UnifiedMarketplacePermissionService::isBusinessVerified($user),
            ];

            // Add specific product type permissions
            foreach (['digital', 'new_product', 'used_product'] as $productType) {
                $marketplacePermissions['can_buy'][$productType] = in_array($productType, $allowedBuyTypes);
                $marketplacePermissions['can_sell'][$productType] = in_array($productType, $allowedSellTypes);
            }
        }

        // Share permissions with all views
        View::share('marketplacePermissions', $marketplacePermissions);

        return $next($request);
    }

    /**
     * Get display name for role
     */
    private function getRoleDisplayName(string $role): string
    {
        $roleNames = [
            'guest' => 'Khách',
            'member' => 'Thành viên',
            'senior_member' => 'Thành viên cao cấp',
            'supplier' => 'Nhà cung cấp',
            'manufacturer' => 'Nhà sản xuất',
            'brand' => 'Thương hiệu',
            'admin' => 'Quản trị viên',
            'super_admin' => 'Quản trị viên cấp cao',
            'system_admin' => 'Quản trị hệ thống',
            'content_admin' => 'Quản trị nội dung',
            'marketplace_moderator' => 'Điều hành marketplace',
            'moderator' => 'Điều hành viên',
        ];

        return $roleNames[$role] ?? 'Không xác định';
    }
}
