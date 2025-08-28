<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSalePrice implements ValidationRule
{
    /**
     * The regular price to compare against
     */
    private $regularPrice;

    /**
     * Minimum discount percentage required
     */
    private $minDiscountPercent;

    /**
     * Maximum discount percentage allowed
     */
    private $maxDiscountPercent;

    /**
     * Create a new rule instance.
     */
    public function __construct($regularPrice, $minDiscountPercent = 1, $maxDiscountPercent = 90)
    {
        $this->regularPrice = $regularPrice;
        $this->minDiscountPercent = $minDiscountPercent;
        $this->maxDiscountPercent = $maxDiscountPercent;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If sale price is null or empty, it's valid (no sale)
        if (is_null($value) || $value === '' || $value === 0) {
            return;
        }

        // Convert to numeric values
        $salePrice = (float) $value;
        $regularPrice = (float) $this->regularPrice;

        // Basic validation: sale price must be positive
        if ($salePrice < 0) {
            $fail('Giá khuyến mãi không được âm.');
            return;
        }

        // Sale price must be less than regular price
        if ($salePrice >= $regularPrice) {
            $fail('Giá khuyến mãi phải nhỏ hơn giá gốc.');
            return;
        }

        // Calculate discount percentage
        $discountPercent = (($regularPrice - $salePrice) / $regularPrice) * 100;

        // Check minimum discount
        if ($discountPercent < $this->minDiscountPercent) {
            $fail("Giá khuyến mãi phải giảm ít nhất {$this->minDiscountPercent}% so với giá gốc.");
            return;
        }

        // Check maximum discount
        if ($discountPercent > $this->maxDiscountPercent) {
            $fail("Giá khuyến mãi không được giảm quá {$this->maxDiscountPercent}% so với giá gốc.");
            return;
        }

        // Additional business logic validation
        $this->validateBusinessRules($salePrice, $regularPrice, $discountPercent, $fail);
    }

    /**
     * Validate additional business rules
     */
    private function validateBusinessRules(float $salePrice, float $regularPrice, float $discountPercent, Closure $fail): void
    {
        // Prevent suspiciously low prices that might indicate errors
        if ($salePrice < 1000 && $regularPrice > 100000) { // Less than 1k VND when regular price > 100k VND
            $fail('Giá khuyến mãi quá thấp so với giá gốc. Vui lòng kiểm tra lại.');
            return;
        }

        // Prevent round number pricing that might look fake
        if ($this->isSuspiciousRoundNumber($salePrice, $regularPrice)) {
            $fail('Giá khuyến mãi có vẻ không tự nhiên. Vui lòng đặt giá hợp lý hơn.');
            return;
        }

        // Check for common discount percentages that might be too aggressive
        $commonAggressiveDiscounts = [99, 95, 90, 85, 80, 75, 70];
        foreach ($commonAggressiveDiscounts as $discount) {
            if (abs($discountPercent - $discount) < 0.1) {
                if ($regularPrice > 1000000) { // For products over 1M VND
                    $fail("Giảm giá {$discount}% cho sản phẩm giá cao cần được xem xét kỹ.");
                    return;
                }
            }
        }

        // Validate pricing tiers
        $this->validatePricingTiers($salePrice, $regularPrice, $fail);
    }

    /**
     * Check if the pricing looks suspiciously round
     */
    private function isSuspiciousRoundNumber(float $salePrice, float $regularPrice): bool
    {
        // Check if both prices end in too many zeros
        $salePriceStr = (string) $salePrice;
        $regularPriceStr = (string) $regularPrice;

        $saleZeros = strlen($salePriceStr) - strlen(rtrim($salePriceStr, '0'));
        $regularZeros = strlen($regularPriceStr) - strlen(rtrim($regularPriceStr, '0'));

        // If both prices have 3+ trailing zeros, it might be suspicious
        return $saleZeros >= 3 && $regularZeros >= 3;
    }

    /**
     * Validate pricing based on different tiers
     */
    private function validatePricingTiers(float $salePrice, float $regularPrice, Closure $fail): void
    {
        // Define pricing tiers and their rules
        $tiers = [
            'budget' => ['min' => 0, 'max' => 100000, 'max_discount' => 50],
            'mid_range' => ['min' => 100000, 'max' => 1000000, 'max_discount' => 70],
            'premium' => ['min' => 1000000, 'max' => 10000000, 'max_discount' => 60],
            'luxury' => ['min' => 10000000, 'max' => PHP_FLOAT_MAX, 'max_discount' => 40],
        ];

        foreach ($tiers as $tierName => $tier) {
            if ($regularPrice >= $tier['min'] && $regularPrice < $tier['max']) {
                $discountPercent = (($regularPrice - $salePrice) / $regularPrice) * 100;
                
                if ($discountPercent > $tier['max_discount']) {
                    $tierLabel = $this->getTierLabel($tierName);
                    $fail("Sản phẩm {$tierLabel} không được giảm giá quá {$tier['max_discount']}%.");
                    return;
                }
                break;
            }
        }
    }

    /**
     * Get human-readable tier label
     */
    private function getTierLabel(string $tierName): string
    {
        $labels = [
            'budget' => 'phân khúc bình dân',
            'mid_range' => 'phân khúc trung cấp',
            'premium' => 'phân khúc cao cấp',
            'luxury' => 'phân khúc sang trọng',
        ];

        return $labels[$tierName] ?? $tierName;
    }

    /**
     * Calculate discount percentage
     */
    public static function calculateDiscountPercent(float $regularPrice, float $salePrice): float
    {
        if ($regularPrice <= 0) {
            return 0;
        }

        return (($regularPrice - $salePrice) / $regularPrice) * 100;
    }

    /**
     * Calculate sale price from discount percentage
     */
    public static function calculateSalePrice(float $regularPrice, float $discountPercent): float
    {
        return $regularPrice * (1 - ($discountPercent / 100));
    }

    /**
     * Validate if a discount percentage is reasonable
     */
    public static function isReasonableDiscount(float $discountPercent, float $regularPrice): bool
    {
        // Very high discounts on expensive items are suspicious
        if ($regularPrice > 10000000 && $discountPercent > 40) {
            return false;
        }

        if ($regularPrice > 1000000 && $discountPercent > 60) {
            return false;
        }

        if ($discountPercent > 90) {
            return false;
        }

        return true;
    }

    /**
     * Get recommended discount range for a price
     */
    public static function getRecommendedDiscountRange(float $regularPrice): array
    {
        if ($regularPrice < 100000) {
            return ['min' => 5, 'max' => 50];
        } elseif ($regularPrice < 1000000) {
            return ['min' => 5, 'max' => 40];
        } elseif ($regularPrice < 10000000) {
            return ['min' => 5, 'max' => 30];
        } else {
            return ['min' => 5, 'max' => 20];
        }
    }
}
