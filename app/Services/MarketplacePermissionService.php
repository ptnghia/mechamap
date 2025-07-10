<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\User;

class MarketplacePermissionService
{
    /**
     * Check if user can buy a specific product type
     */
    public static function canBuy(User $user, string $productType): bool
    {
        $permissions = self::getPermissionMatrix();
        $role = $user->role ?? 'guest';

        if (!isset($permissions[$role]['buy'])) {
            return false;
        }

        $buyPermissions = $permissions[$role]['buy'];
        return is_array($buyPermissions) && in_array($productType, $buyPermissions);
    }

    /**
     * Check if user can sell a specific product type
     */
    public static function canSell(User $user, string $productType): bool
    {
        $permissions = self::getPermissionMatrix();
        $role = $user->role ?? 'guest';

        if (!isset($permissions[$role]['sell'])) {
            return false;
        }

        $sellPermissions = $permissions[$role]['sell'];
        return is_array($sellPermissions) && in_array($productType, $sellPermissions);
    }

    /**
     * Get allowed product types for buying by user role
     */
    public static function getAllowedBuyTypes(string $role): array
    {
        $permissions = self::getPermissionMatrix();
        return $permissions[$role]['buy'] ?? [];
    }

    /**
     * Get allowed product types for selling by user role
     */
    public static function getAllowedSellTypes(string $role): array
    {
        $permissions = self::getPermissionMatrix();
        return $permissions[$role]['sell'] ?? [];
    }

    /**
     * Get permission matrix based on requirements
     */
    private static function getPermissionMatrix(): array
    {
        return [
            // Admin roles - full permissions
            'super_admin' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'system_admin' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'admin' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'content_admin' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'marketplace_moderator' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'moderator' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],

            // Cá nhân (Guest/Member) - chỉ được mua/bán sản phẩm kỹ thuật số
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

            // Nhà cung cấp (Supplier) - mua digital, bán digital + new_product
            'supplier' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            ],

            // Nhà sản xuất (Manufacturer) - mua digital + new_product, bán digital
            'manufacturer' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],

            // Thương hiệu (Brand) - chỉ xem, không mua/bán
            'brand' => [
                'buy' => [],
                'sell' => [],
            ],
        ];
    }

    /**
     * Get permission description for UI
     */
    public static function getPermissionDescription(string $role): array
    {
        $descriptions = [
            'guest' => [
                'buy' => 'Có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Có thể bán sản phẩm kỹ thuật số',
            ],
            'member' => [
                'buy' => 'Có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Có thể bán sản phẩm kỹ thuật số',
            ],
            'senior_member' => [
                'buy' => 'Có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Có thể bán sản phẩm kỹ thuật số',
            ],
            'supplier' => [
                'buy' => 'Có thể mua sản phẩm kỹ thuật số',
                'sell' => 'Có thể bán sản phẩm kỹ thuật số và sản phẩm mới',
            ],
            'manufacturer' => [
                'buy' => 'Có thể mua sản phẩm kỹ thuật số và sản phẩm mới',
                'sell' => 'Có thể bán sản phẩm kỹ thuật số',
            ],
            'brand' => [
                'buy' => 'Chỉ được xem sản phẩm',
                'sell' => 'Chỉ được xem sản phẩm',
            ],
        ];

        return $descriptions[$role] ?? [
            'buy' => 'Không có quyền mua',
            'sell' => 'Không có quyền bán',
        ];
    }

    /**
     * Validate product creation permissions
     */
    public static function validateProductCreation(User $user, array $productData): array
    {
        $errors = [];
        $productType = $productData['product_type'] ?? null;

        if (!$productType) {
            $errors[] = 'Loại sản phẩm là bắt buộc';
            return $errors;
        }

        if (!self::canSell($user, $productType)) {
            $allowedTypes = self::getAllowedSellTypes($user->role);
            $typeNames = array_map(function($type) {
                return MarketplaceProduct::getProductTypes()[$type] ?? $type;
            }, $allowedTypes);

            $errors[] = sprintf(
                'Bạn không có quyền bán loại sản phẩm này. Các loại được phép: %s',
                implode(', ', $typeNames)
            );
        }

        return $errors;
    }
}
