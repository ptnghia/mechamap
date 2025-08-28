<?php

namespace App\Rules;

use App\Services\UnifiedMarketplacePermissionService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProductType implements ValidationRule
{
    /**
     * The user to validate permissions for
     */
    private $user;

    /**
     * Create a new rule instance.
     */
    public function __construct($user = null)
    {
        $this->user = $user ?? auth()->user();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->user) {
            $fail('Người dùng chưa đăng nhập.');
            return;
        }

        // Check if the product type is valid
        $validTypes = ['digital', 'new_product', 'used_product'];
        if (!in_array($value, $validTypes)) {
            $fail('Loại sản phẩm không hợp lệ.');
            return;
        }

        // Check if user has permission to sell this product type
        if (!UnifiedMarketplacePermissionService::canSell($this->user, $value)) {
            $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($this->user->role);
            
            if (empty($allowedTypes)) {
                $fail('Bạn không có quyền bán bất kỳ loại sản phẩm nào.');
                return;
            }

            // Get human-readable names for allowed types
            $typeNames = [];
            foreach ($allowedTypes as $type) {
                $typeNames[] = $this->getProductTypeName($type);
            }

            $allowedTypesText = implode(', ', $typeNames);
            $fail("Bạn không có quyền bán loại sản phẩm này. Các loại được phép: {$allowedTypesText}");
            return;
        }

        // Additional business logic validation
        $this->validateBusinessRules($value, $fail);
    }

    /**
     * Validate additional business rules
     */
    private function validateBusinessRules(string $productType, Closure $fail): void
    {
        $userRole = $this->user->role;

        // Specific rules for different user roles
        switch ($userRole) {
            case 'manufacturer':
                // Manufacturers can sell digital and new products
                if (!in_array($productType, ['digital', 'new_product'])) {
                    $fail('Nhà sản xuất chỉ có thể bán sản phẩm digital hoặc sản phẩm mới.');
                }
                break;

            case 'supplier':
                // Suppliers can sell new and used products
                if (!in_array($productType, ['new_product', 'used_product'])) {
                    $fail('Nhà cung cấp chỉ có thể bán sản phẩm mới hoặc sản phẩm cũ.');
                }
                break;

            case 'verified_partner':
                // Verified partners can sell all types
                // No additional restrictions
                break;

            case 'brand':
                // Brands typically sell new products and digital content
                if (!in_array($productType, ['digital', 'new_product'])) {
                    $fail('Thương hiệu chỉ có thể bán sản phẩm digital hoặc sản phẩm mới.');
                }
                break;

            default:
                // Regular users might have limited permissions
                if ($productType === 'digital') {
                    $fail('Bạn cần quyền đặc biệt để bán sản phẩm digital.');
                }
                break;
        }

        // Check user verification status for certain product types
        if ($productType === 'digital' && !$this->isUserVerifiedForDigitalSales()) {
            $fail('Bạn cần xác thực tài khoản để bán sản phẩm digital.');
            return;
        }

        // Check business verification for high-value product types
        if (in_array($productType, ['new_product']) && !$this->isBusinessVerified()) {
            $fail('Bạn cần xác thực doanh nghiệp để bán sản phẩm mới.');
            return;
        }
    }

    /**
     * Check if user is verified for digital sales
     */
    private function isUserVerifiedForDigitalSales(): bool
    {
        // Check if user has required verification
        return $this->user->email_verified_at !== null && 
               $this->user->phone_verified_at !== null;
    }

    /**
     * Check if user has business verification
     */
    private function isBusinessVerified(): bool
    {
        // Check if user has business verification
        if (!$this->user->marketplaceSeller) {
            return false;
        }

        return $this->user->marketplaceSeller->verification_status === 'verified';
    }

    /**
     * Get human-readable product type name
     */
    private function getProductTypeName(string $type): string
    {
        $names = [
            'digital' => 'Sản phẩm Digital',
            'new_product' => 'Sản phẩm Mới',
            'used_product' => 'Sản phẩm Cũ',
        ];

        return $names[$type] ?? $type;
    }

    /**
     * Get all valid product types
     */
    public static function getValidTypes(): array
    {
        return ['digital', 'new_product', 'used_product'];
    }

    /**
     * Get product type names mapping
     */
    public static function getTypeNames(): array
    {
        return [
            'digital' => 'Sản phẩm Digital',
            'new_product' => 'Sản phẩm Mới',
            'used_product' => 'Sản phẩm Cũ',
        ];
    }

    /**
     * Check if a product type requires special verification
     */
    public static function requiresSpecialVerification(string $type): bool
    {
        return in_array($type, ['digital', 'new_product']);
    }

    /**
     * Get required permissions for a product type
     */
    public static function getRequiredPermissions(string $type): array
    {
        $permissions = [
            'digital' => [
                'email_verified',
                'phone_verified',
                'can_sell_digital'
            ],
            'new_product' => [
                'business_verified',
                'can_sell_physical'
            ],
            'used_product' => [
                'can_sell_physical'
            ],
        ];

        return $permissions[$type] ?? [];
    }
}
