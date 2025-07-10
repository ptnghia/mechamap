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
     * Extract product type from request
     */
    private function getProductTypeFromRequest(Request $request): ?string
    {
        // From route parameter
        if ($request->route('product')) {
            $product = $request->route('product');
            return is_object($product) ? $product->product_type : null;
        }

        // From request data
        return $request->input('product_type');
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

        // If no product type specified, check general permission
        if (!$productType) {
            return $rolePermissions[$action] === true;
        }

        // Check specific product type permission
        $actionPermissions = $rolePermissions[$action];

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
