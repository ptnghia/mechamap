<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStockQuantity implements ValidationRule
{
    /**
     * The product type
     */
    private $productType;

    /**
     * The user role
     */
    private $userRole;

    /**
     * Whether stock management is enabled
     */
    private $manageStock;

    /**
     * Create a new rule instance.
     */
    public function __construct($productType = null, $userRole = null, $manageStock = true)
    {
        $this->productType = $productType;
        $this->userRole = $userRole ?? auth()->user()?->role;
        $this->manageStock = $manageStock;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $stockQuantity = (int) $value;

        // Basic validation: stock quantity must be non-negative
        if ($stockQuantity < 0) {
            $fail('Số lượng tồn kho không được âm.');
            return;
        }

        // Digital products don't need stock management
        if ($this->productType === 'digital') {
            if ($stockQuantity > 0) {
                $fail('Sản phẩm digital không cần quản lý tồn kho.');
                return;
            }
            return; // Valid for digital products
        }

        // Physical products require stock management
        if (in_array($this->productType, ['new_product', 'used_product'])) {
            $this->validatePhysicalProductStock($stockQuantity, $fail);
        }

        // Role-based validation
        $this->validateByUserRole($stockQuantity, $fail);

        // Business logic validation
        $this->validateBusinessRules($stockQuantity, $fail);
    }

    /**
     * Validate stock for physical products
     */
    private function validatePhysicalProductStock(int $stockQuantity, Closure $fail): void
    {
        // Physical products should have stock if stock management is enabled
        if ($this->manageStock && $stockQuantity === 0) {
            $fail('Sản phẩm vật lý cần có số lượng tồn kho khi bật quản lý tồn kho.');
            return;
        }

        // Check maximum stock limits based on product type
        $maxLimits = [
            'new_product' => 999999,
            'used_product' => 1000, // Used products typically have lower quantities
        ];

        $maxLimit = $maxLimits[$this->productType] ?? 999999;
        
        if ($stockQuantity > $maxLimit) {
            $productTypeName = $this->getProductTypeName($this->productType);
            $fail("{$productTypeName} không được có số lượng tồn kho vượt quá " . number_format($maxLimit) . ".");
            return;
        }

        // Used products validation
        if ($this->productType === 'used_product') {
            $this->validateUsedProductStock($stockQuantity, $fail);
        }
    }

    /**
     * Validate stock for used products
     */
    private function validateUsedProductStock(int $stockQuantity, Closure $fail): void
    {
        // Used products typically have limited quantities
        if ($stockQuantity > 100) {
            $fail('Sản phẩm cũ thường có số lượng hạn chế. Số lượng quá 100 cần được xem xét.');
            return;
        }

        // Very high quantities for used products are suspicious
        if ($stockQuantity > 50) {
            $fail('Số lượng tồn kho cao cho sản phẩm cũ. Vui lòng xác nhận thông tin.');
            return;
        }
    }

    /**
     * Validate based on user role
     */
    private function validateByUserRole(int $stockQuantity, Closure $fail): void
    {
        $roleLimits = [
            'manufacturer' => 999999,
            'supplier' => 999999,
            'verified_partner' => 999999,
            'brand' => 999999,
            'member' => 100,
            'student' => 10,
        ];

        $maxAllowed = $roleLimits[$this->userRole] ?? 50;

        if ($stockQuantity > $maxAllowed) {
            $roleName = $this->getRoleName($this->userRole);
            $fail("{$roleName} không được có số lượng tồn kho vượt quá " . number_format($maxAllowed) . ".");
            return;
        }

        // Special validation for non-business users
        if (in_array($this->userRole, ['member', 'student']) && $stockQuantity > 10) {
            $fail('Người dùng cá nhân nên có số lượng tồn kho hạn chế.');
            return;
        }
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(int $stockQuantity, Closure $fail): void
    {
        // Check for suspicious patterns
        if ($this->isSuspiciousQuantity($stockQuantity)) {
            $fail('Số lượng tồn kho có vẻ không hợp lý. Vui lòng kiểm tra lại.');
            return;
        }

        // Validate quantity ranges
        $this->validateQuantityRanges($stockQuantity, $fail);
    }

    /**
     * Check if quantity looks suspicious
     */
    private function isSuspiciousQuantity(int $stockQuantity): bool
    {
        // Very round numbers might be suspicious for certain ranges
        $suspiciousPatterns = [
            999999, 888888, 777777, 666666, 555555,
            100000, 200000, 300000, 400000, 500000
        ];

        return in_array($stockQuantity, $suspiciousPatterns);
    }

    /**
     * Validate quantity ranges
     */
    private function validateQuantityRanges(int $stockQuantity, Closure $fail): void
    {
        // Define reasonable ranges for different scenarios
        $ranges = [
            'small_business' => ['min' => 1, 'max' => 1000],
            'medium_business' => ['min' => 1, 'max' => 10000],
            'large_business' => ['min' => 1, 'max' => 100000],
        ];

        // Determine business size based on user role
        $businessSize = $this->determineBusinessSize();
        $range = $ranges[$businessSize] ?? $ranges['small_business'];

        if ($stockQuantity > $range['max']) {
            $fail("Số lượng tồn kho vượt quá giới hạn cho quy mô doanh nghiệp của bạn (" . number_format($range['max']) . ").");
            return;
        }
    }

    /**
     * Determine business size based on user role
     */
    private function determineBusinessSize(): string
    {
        $businessSizes = [
            'manufacturer' => 'large_business',
            'supplier' => 'large_business',
            'verified_partner' => 'medium_business',
            'brand' => 'medium_business',
        ];

        return $businessSizes[$this->userRole] ?? 'small_business';
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
     * Get human-readable role name
     */
    private function getRoleName(string $role): string
    {
        $names = [
            'manufacturer' => 'Nhà sản xuất',
            'supplier' => 'Nhà cung cấp',
            'verified_partner' => 'Đối tác xác thực',
            'brand' => 'Thương hiệu',
            'member' => 'Thành viên',
            'student' => 'Sinh viên',
        ];

        return $names[$role] ?? $role;
    }

    /**
     * Get maximum stock quantity for a user role
     */
    public static function getMaxStockForRole(string $role): int
    {
        $limits = [
            'manufacturer' => 999999,
            'supplier' => 999999,
            'verified_partner' => 999999,
            'brand' => 999999,
            'member' => 100,
            'student' => 10,
        ];

        return $limits[$role] ?? 50;
    }

    /**
     * Get recommended stock range for product type
     */
    public static function getRecommendedStockRange(string $productType): array
    {
        $ranges = [
            'digital' => ['min' => 0, 'max' => 0],
            'new_product' => ['min' => 1, 'max' => 1000],
            'used_product' => ['min' => 1, 'max' => 50],
        ];

        return $ranges[$productType] ?? ['min' => 1, 'max' => 100];
    }

    /**
     * Check if stock management is required for product type
     */
    public static function requiresStockManagement(string $productType): bool
    {
        return in_array($productType, ['new_product', 'used_product']);
    }
}
