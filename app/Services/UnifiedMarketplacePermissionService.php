<?php

namespace App\Services;

use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\BusinessVerificationApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Unified Marketplace Permission Service
 *
 * Single source of truth for all marketplace permissions
 * Integrates with business verification system from Phase 2
 */
class UnifiedMarketplacePermissionService
{
    // Cache duration for permissions (1 hour)
    const CACHE_DURATION = 3600;

    // Permission cache key prefix
    const CACHE_PREFIX = 'marketplace_permissions_';

    /**
     * Check if user can buy a specific product type
     */
    public static function canBuy(?User $user, string $productType): bool
    {
        if (!$user) {
            return false; // Guest cannot buy anything
        }

        $permissions = self::getUserPermissions($user);
        return in_array($productType, $permissions['buy'] ?? []);
    }

    /**
     * Check if user can sell a specific product type
     */
    public static function canSell(?User $user, string $productType): bool
    {
        if (!$user) {
            return false; // Guest cannot sell anything
        }

        $permissions = self::getUserPermissions($user);
        return in_array($productType, $permissions['sell'] ?? []);
    }

    /**
     * Get all product types user can buy
     */
    public static function getAllowedBuyTypes(?User $user): array
    {
        if (!$user) {
            return []; // Guest cannot buy anything
        }

        $permissions = self::getUserPermissions($user);
        return $permissions['buy'] ?? [];
    }

    /**
     * Get all product types user can sell
     */
    public static function getAllowedSellTypes(?User $user): array
    {
        if (!$user) {
            return []; // Guest cannot sell anything
        }

        $permissions = self::getUserPermissions($user);
        return $permissions['sell'] ?? [];
    }

    /**
     * Check if user can access marketplace at all
     */
    public static function canAccessMarketplace(?User $user): bool
    {
        if (!$user) {
            return true; // Guest can view marketplace (read-only)
        }

        $permissions = self::getUserPermissions($user);
        return !empty($permissions['buy']) || !empty($permissions['sell']) || self::isAdminRole($user->role);
    }

    /**
     * Check if user can view shopping cart
     */
    public static function canViewCart(?User $user): bool
    {
        return $user && !empty(self::getAllowedBuyTypes($user));
    }

    /**
     * Check if user can checkout
     */
    public static function canCheckout(?User $user): bool
    {
        return $user && !empty(self::getAllowedBuyTypes($user));
    }

    /**
     * Check if user can manage seller account
     */
    public static function canManageSellerAccount(?User $user): bool
    {
        return $user && !empty(self::getAllowedSellTypes($user));
    }

    /**
     * Get commission rate for user's role
     */
    public static function getCommissionRate(?User $user): float
    {
        if (!$user) {
            return 0.0;
        }

        $role = self::getEffectiveRole($user);
        $commissionRates = self::getCommissionRates();

        return $commissionRates[$role] ?? 0.0;
    }

    /**
     * Get user's marketplace features
     */
    public static function getMarketplaceFeatures(?User $user): array
    {
        if (!$user) {
            return [
                'can_create_products' => false,
                'can_view_cart' => false,
                'can_checkout' => false,
                'can_view_orders' => false,
                'can_manage_seller_account' => false,
                'can_access_analytics' => false,
                'can_manage_business_profile' => false,
                'priority_support' => false,
            ];
        }

        $role = self::getEffectiveRole($user);
        $permissions = self::getUserPermissions($user);
        $features = config('mechamap_permissions.marketplace_features.' . $role, []);

        return [
            'can_create_products' => !empty($permissions['sell']),
            'can_view_cart' => !empty($permissions['buy']),
            'can_checkout' => !empty($permissions['buy']),
            'can_view_orders' => !empty($permissions['buy']) || !empty($permissions['sell']),
            'can_manage_seller_account' => !empty($permissions['sell']),
            'can_access_analytics' => !empty($permissions['sell']),
            'can_manage_business_profile' => self::isBusinessRole($role),
            'priority_support' => $features['priority_support'] ?? false,
            'commission_rate' => self::getCommissionRate($user),
        ];
    }

    /**
     * Get user permissions with caching
     */
    public static function getUserPermissions(User $user): array
    {
        $cacheKey = self::CACHE_PREFIX . $user->id;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($user) {
            return self::calculateUserPermissions($user);
        });
    }

    /**
     * Calculate user permissions based on role and verification status
     */
    private static function calculateUserPermissions(User $user): array
    {
        $role = $user->role ?? 'guest';
        $effectiveRole = self::getEffectiveRole($user);

        $matrix = self::getPermissionMatrix();

        if (!isset($matrix[$effectiveRole])) {
            Log::warning("Unknown role in permission matrix: {$effectiveRole}", [
                'user_id' => $user->id,
                'original_role' => $role,
                'effective_role' => $effectiveRole,
            ]);
            return ['buy' => [], 'sell' => []];
        }

        return $matrix[$effectiveRole];
    }

    /**
     * Get effective role considering verification status
     */
    private static function getEffectiveRole(User $user): string
    {
        $role = $user->role ?? 'guest';

        // For business roles, check verification status
        if (self::isBusinessRole($role)) {
            $isVerified = self::isBusinessVerified($user);

            if ($isVerified) {
                // Return verified version of role
                return $role . '_verified';
            } else {
                // Return unverified version
                return $role . '_unverified';
            }
        }

        return $role;
    }

    /**
     * Check if user's business is verified (public method)
     */
    public static function isBusinessVerified(User $user): bool
    {
        if (!self::isBusinessRole($user->role)) {
            return false;
        }

        // Check if user has approved business verification application
        return BusinessVerificationApplication::where('user_id', $user->id)
            ->where('status', BusinessVerificationApplication::STATUS_APPROVED)
            ->exists();
    }

    /**
     * Check if role is a business role
     */
    private static function isBusinessRole(string $role): bool
    {
        $businessRoles = ['manufacturer', 'supplier', 'brand', 'verified_partner'];
        return in_array($role, $businessRoles);
    }

    /**
     * Check if role is admin role
     */
    private static function isAdminRole(string $role): bool
    {
        $adminRoles = [
            'super_admin',
            'system_admin',
            'content_admin',
            'content_moderator',
            'marketplace_moderator',
            'community_moderator'
        ];

        return in_array($role, $adminRoles);
    }

    /**
     * Get unified permission matrix
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
            'content_admin' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
            'content_moderator' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL],
            ],
            'marketplace_moderator' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            ],
            'community_moderator' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [],
            ],

            // Community Members - Basic marketplace access
            'senior_member' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [], // Community members don't sell
            ],
            'member' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [], // Community members don't sell
            ],
            'guest' => [
                'buy' => [], // Must register to buy
                'sell' => [], // Must register to sell
            ],

            // Business Partners - Unverified (Limited Access)
            'manufacturer_unverified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [], // No selling until verified
            ],
            'supplier_unverified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [], // No selling until verified
            ],
            'brand_unverified' => [
                'buy' => [],
                'sell' => [], // No selling until verified
            ],
            'verified_partner_unverified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [], // No selling until verified
            ],

            // Business Partners - Verified (Full Access)
            'manufacturer_verified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            ],
            'supplier_verified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            ],
            'brand_verified' => [
                'buy' => [], // Brands typically don't buy
                'sell' => [], // Brands use platform for promotion only
            ],
            'verified_partner_verified' => [
                'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
                'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT, MarketplaceProduct::TYPE_USED_PRODUCT],
            ],
        ];
    }

    /**
     * Get commission rates by role
     */
    private static function getCommissionRates(): array
    {
        return [
            // Admin roles - no commission
            'super_admin' => 0.0,
            'system_admin' => 0.0,
            'content_admin' => 0.0,
            'content_moderator' => 0.0,
            'marketplace_moderator' => 0.0,
            'community_moderator' => 0.0,

            // Community members - no commission (they don't sell)
            'senior_member' => 0.0,
            'member' => 0.0,
            'guest' => 0.0,

            // Business partners - unverified (higher rates)
            'manufacturer_unverified' => 10.0,
            'supplier_unverified' => 10.0,
            'brand_unverified' => 10.0,
            'verified_partner_unverified' => 10.0,

            // Business partners - verified (lower rates)
            'manufacturer_verified' => 5.0,
            'supplier_verified' => 3.0,
            'brand_verified' => 0.0, // Brands don't sell
            'verified_partner_verified' => 2.0,
        ];
    }

    /**
     * Clear user permission cache
     */
    public static function clearUserPermissionCache(User $user): void
    {
        $cacheKey = self::CACHE_PREFIX . $user->id;
        Cache::forget($cacheKey);

        Log::info('Cleared marketplace permission cache for user', [
            'user_id' => $user->id,
            'role' => $user->role,
        ]);
    }

    /**
     * Clear all permission caches
     */
    public static function clearAllPermissionCaches(): void
    {
        // This would require a more sophisticated cache tagging system
        // For now, we'll implement it when needed
        Log::info('Request to clear all marketplace permission caches');
    }

    /**
     * Validate product creation permissions
     */
    public static function validateProductCreation(User $user, array $productData): array
    {
        $errors = [];
        $productType = $productData['product_type'] ?? null;

        if (!$productType) {
            $errors[] = 'Product type is required';
            return $errors;
        }

        if (!self::canSell($user, $productType)) {
            $allowedTypes = self::getAllowedSellTypes($user);
            $errors[] = "You don't have permission to sell {$productType} products. Allowed types: " . implode(', ', $allowedTypes);
        }

        // Additional business logic validation
        if (self::isBusinessRole($user->role) && !self::isBusinessVerified($user)) {
            $errors[] = 'Business verification required before selling products. Please complete your business verification.';
        }

        return $errors;
    }

    /**
     * Get permission summary for user
     */
    public static function getPermissionSummary(User $user): array
    {
        $permissions = self::getUserPermissions($user);
        $features = self::getMarketplaceFeatures($user);
        $role = self::getEffectiveRole($user);

        return [
            'user_id' => $user->id,
            'role' => $user->role,
            'effective_role' => $role,
            'is_verified' => self::isBusinessVerified($user),
            'permissions' => $permissions,
            'features' => $features,
            'commission_rate' => self::getCommissionRate($user),
            'can_access_marketplace' => self::canAccessMarketplace($user),
        ];
    }
}
