<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MarketplaceProduct;

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

        // For authenticated users, check permissions
        if ($user && $action !== 'view') {
            // Admin roles have full marketplace permissions
            if ($this->isAdminRole($user->role)) {
                return $next($request);
            }

            $productType = $this->getProductTypeFromRequest($request);

            if (!$this->hasPermission($user->role, $action, $productType)) {
                return response()->json([
                    'error' => 'Permission denied',
                    'message' => $this->getPermissionMessage($user->role, $action, $productType)
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
     */
    private function getPermissionMatrix(): array
    {
        return [
            // Cá nhân (Guest/Member)
            'guest' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],
            'member' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],
            'senior_member' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],

            // Nhà cung cấp (Supplier)
            'supplier' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            ],

            // Nhà sản xuất (Manufacturer)
            'manufacturer' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],

            // Thương hiệu (Brand) - chỉ xem
            'brand' => [
                'buy' => [],
                'sell' => [],
                'view' => true,
                'contact' => true,
            ],
        ];
    }

    /**
     * Get permission error message
     */
    private function getPermissionMessage(string $role, string $action, ?string $productType): string
    {
        $messages = [
            'guest' => [
                'buy' => 'Khách vãng lai chỉ có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Khách vãng lai chỉ có thể bán sản phẩm kỹ thuật số',
            ],
            'member' => [
                'buy' => 'Thành viên chỉ có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Thành viên chỉ có thể bán sản phẩm kỹ thuật số',
            ],
            'supplier' => [
                'buy' => 'Nhà cung cấp chỉ có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Nhà cung cấp có thể bán sản phẩm kỹ thuật số và sản phẩm mới',
            ],
            'manufacturer' => [
                'buy' => 'Nhà sản xuất có thể mua sản phẩm kỹ thuật số và sản phẩm mới',
                'sell' => 'Nhà sản xuất chỉ có thể bán sản phẩm kỹ thuật số',
            ],
            'brand' => [
                'buy' => 'Thương hiệu không được phép mua sản phẩm',
                'sell' => 'Thương hiệu không được phép bán sản phẩm',
            ],
        ];

        return $messages[$role][$action] ?? 'Bạn không có quyền thực hiện hành động này';
    }
}
