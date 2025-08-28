<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MarketplaceProduct;
use App\Services\UnifiedMarketplacePermissionService;

class MarketplacePermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Check marketplace buy/sell permissions based on user role and product type
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'view'): Response
    {
        $user = auth()->user();

        // Guest users can only view
        if (!$user && $action !== 'view') {
            return response()->json([
                'error' => 'Authentication required',
                'message' => 'Bạn cần đăng nhập để thực hiện hành động này'
            ], 401);
        }

        // For view action, allow everyone (including guests)
        if ($action === 'view') {
            return $next($request);
        }

        // For authenticated users, check permissions using unified service
        if ($user) {
            $productType = $this->getProductTypeFromRequest($request);

            // Check permission using unified service
            $hasPermission = false;
            switch ($action) {
                case 'buy':
                    $hasPermission = UnifiedMarketplacePermissionService::canBuy($user, $productType);
                    break;
                case 'sell':
                    // For sell action, if no specific product type, check if user can sell any type
                    if ($productType) {
                        $hasPermission = UnifiedMarketplacePermissionService::canSell($user, $productType);
                    } else {
                        // Check if user can sell any product type
                        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);
                        $hasPermission = !empty($allowedTypes);
                    }
                    break;
                case 'checkout':
                    $hasPermission = UnifiedMarketplacePermissionService::canCheckout($user);
                    break;
                case 'cart':
                    $hasPermission = UnifiedMarketplacePermissionService::canViewCart($user);
                    break;
                default:
                    $hasPermission = UnifiedMarketplacePermissionService::canAccessMarketplace($user);
            }

            if (!$hasPermission) {
                return response()->json([
                    'error' => 'Permission denied',
                    'message' => $this->getPermissionMessage($user, $action, $productType)
                ], 403);
            }
        }

        return $next($request);
    }

    /**
     * Check if user role is admin role with full marketplace permissions
     */
    private function isAdminRole(string $role): bool
    {
        return in_array($role, [
            'super_admin',
            'system_admin',
            'admin',
            'content_admin',
            'marketplace_moderator',
            'moderator'
        ]);
    }

    /**
     * Extract product type from request
     */
    private function getProductTypeFromRequest(Request $request): ?string
    {
        // From route parameter
        if ($request->route('product')) {
            $product = $request->route('product');
            return is_object($product) ? $product->product_type : null;
        }

        // From request data - direct product_type
        if ($request->has('product_type')) {
            return $request->input('product_type');
        }

        // From product_id - lookup product in database
        if ($request->has('product_id')) {
            try {
                $product = MarketplaceProduct::find($request->input('product_id'));
                return $product ? $product->product_type : null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Check if user has permission for specific action and product type
     */
    private function hasPermission(string $role, string $action, ?string $productType): bool
    {
        $permissions = $this->getPermissionMatrix();

        if (!isset($permissions[$role])) {
            return false;
        }

        $rolePermissions = $permissions[$role];

        // Check action permission
        if (!isset($rolePermissions[$action])) {
            return false;
        }

        $actionPermissions = $rolePermissions[$action];

        // If no product type specified (e.g., checkout), check if user has any permission for this action
        if (!$productType) {
            if (is_bool($actionPermissions)) {
                return $actionPermissions;
            }
            if (is_array($actionPermissions)) {
                return !empty($actionPermissions); // Has permission if array is not empty
            }
            return false;
        }

        // Check specific product type permission
        if (is_bool($actionPermissions)) {
            return $actionPermissions;
        }

        if (is_array($actionPermissions)) {
            return in_array($productType, $actionPermissions);
        }

        return false;
    }

    /**
     * Get permission matrix based on requirements
     * @deprecated Use UnifiedMarketplacePermissionService instead
     */
    private function getPermissionMatrix(): array
    {
        // This method is deprecated and should not be used
        // All permission logic is now handled by UnifiedMarketplacePermissionService
        return [];
    }

    /**
     * Get permission error message using unified service
     */
    private function getPermissionMessage($user, string $action, ?string $productType): string
    {
        if (!$user) {
            return 'Bạn cần đăng nhập để thực hiện hành động này';
        }

        $role = $user->role ?? 'guest';

        // Check if business verification is required
        $isBusinessRole = in_array($role, ['manufacturer', 'supplier', 'brand', 'verified_partner']);
        $isVerified = $isBusinessRole ? UnifiedMarketplacePermissionService::isBusinessVerified($user) : true;

        // Get allowed types for better error messages
        $allowedBuyTypes = UnifiedMarketplacePermissionService::getAllowedBuyTypes($user);
        $allowedSellTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

        switch ($action) {
            case 'buy':
                if (empty($allowedBuyTypes)) {
                    if ($isBusinessRole && !$isVerified) {
                        return 'Bạn cần xác thực doanh nghiệp để có thể mua sản phẩm. Vui lòng hoàn tất quy trình xác thực.';
                    }
                    return 'Vai trò của bạn không có quyền mua sản phẩm';
                }
                return "Bạn chỉ có thể mua: " . implode(', ', $allowedBuyTypes);

            case 'sell':
                if (empty($allowedSellTypes)) {
                    if ($isBusinessRole && !$isVerified) {
                        return 'Bạn cần xác thực doanh nghiệp để có thể bán sản phẩm. Vui lòng hoàn tất quy trình xác thực.';
                    }
                    return 'Vai trò của bạn không có quyền bán sản phẩm';
                }
                return "Bạn chỉ có thể bán: " . implode(', ', $allowedSellTypes);

            case 'checkout':
                return 'Bạn không có quyền thanh toán. Vui lòng kiểm tra quyền mua hàng của bạn.';

            case 'cart':
                return 'Bạn không có quyền truy cập giỏ hàng. Vui lòng kiểm tra quyền mua hàng của bạn.';

            default:
                return 'Bạn không có quyền thực hiện hành động này';
        }
    }
}
