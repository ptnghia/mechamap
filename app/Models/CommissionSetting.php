<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 💰 Commission Setting Model
 * 
 * Quản lý cấu hình hoa hồng theo seller role và product type
 * Hỗ trợ flexible commission rules cho MechaMap marketplace
 */
class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_role',
        'product_type',
        'commission_rate',
        'fixed_fee',
        'min_commission',
        'max_commission',
        'min_order_value',
        'special_conditions',
        'is_active',
        'description',
        'effective_from',
        'effective_until',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'min_commission' => 'decimal:2',
        'max_commission' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'special_conditions' => 'array',
        'is_active' => 'boolean',
        'effective_from' => 'datetime',
        'effective_until' => 'datetime',
    ];

    /**
     * Relationship: User who created this setting
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User who last updated this setting
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Active commission settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('effective_from', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>=', now());
                    });
    }

    /**
     * Scope: Filter by seller role
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('seller_role', $role);
    }

    /**
     * Scope: Filter by product type
     */
    public function scopeForProductType($query, string $productType)
    {
        return $query->where('product_type', $productType);
    }

    /**
     * Get commission setting for specific role and product type
     */
    public static function getCommissionRate(string $sellerRole, ?string $productType = null, float $orderValue = 0): array
    {
        $query = static::active()
                      ->forRole($sellerRole)
                      ->where('min_order_value', '<=', $orderValue)
                      ->orderBy('effective_from', 'desc');

        if ($productType) {
            $query->where(function ($q) use ($productType) {
                $q->forProductType($productType)
                  ->orWhereNull('product_type');
            });
        } else {
            $query->whereNull('product_type');
        }

        $setting = $query->first();

        if (!$setting) {
            // Fallback to default rates from config
            $defaultRates = config('mechamap_permissions.marketplace_features');
            $rate = $defaultRates[$sellerRole]['commission_rate'] ?? 5.0;
            
            return [
                'commission_rate' => $rate,
                'fixed_fee' => 0,
                'min_commission' => 0,
                'max_commission' => null,
                'source' => 'default'
            ];
        }

        return [
            'commission_rate' => $setting->commission_rate,
            'fixed_fee' => $setting->fixed_fee,
            'min_commission' => $setting->min_commission,
            'max_commission' => $setting->max_commission,
            'source' => 'database',
            'setting_id' => $setting->id
        ];
    }

    /**
     * Calculate commission amount for given order value
     */
    public function calculateCommission(float $orderValue): array
    {
        // Calculate percentage commission
        $percentageCommission = ($orderValue * $this->commission_rate) / 100;
        
        // Add fixed fee
        $totalCommission = $percentageCommission + $this->fixed_fee;
        
        // Apply minimum commission
        if ($this->min_commission > 0 && $totalCommission < $this->min_commission) {
            $totalCommission = $this->min_commission;
        }
        
        // Apply maximum commission
        if ($this->max_commission > 0 && $totalCommission > $this->max_commission) {
            $totalCommission = $this->max_commission;
        }
        
        return [
            'order_value' => $orderValue,
            'commission_rate' => $this->commission_rate,
            'percentage_commission' => $percentageCommission,
            'fixed_fee' => $this->fixed_fee,
            'total_commission' => $totalCommission,
            'seller_earnings' => $orderValue - $totalCommission,
            'applied_min' => $totalCommission == $this->min_commission,
            'applied_max' => $totalCommission == $this->max_commission,
        ];
    }

    /**
     * Get available seller roles
     */
    public static function getSellerRoles(): array
    {
        return [
            'manufacturer' => 'Manufacturer',
            'supplier' => 'Supplier', 
            'brand' => 'Brand',
            'verified_partner' => 'Verified Partner'
        ];
    }

    /**
     * Get available product types
     */
    public static function getProductTypes(): array
    {
        return [
            'digital' => 'Digital Products',
            'new_product' => 'New Products',
            'used_product' => 'Used Products',
            'service' => 'Services'
        ];
    }

    /**
     * Validation rules
     */
    public static function validationRules(): array
    {
        return [
            'seller_role' => 'required|in:manufacturer,supplier,brand,verified_partner',
            'product_type' => 'nullable|in:digital,new_product,used_product,service',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'fixed_fee' => 'nullable|numeric|min:0',
            'min_commission' => 'nullable|numeric|min:0',
            'max_commission' => 'nullable|numeric|min:0|gte:min_commission',
            'min_order_value' => 'nullable|numeric|min:0',
            'special_conditions' => 'nullable|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string|max:500',
            'effective_from' => 'required|date',
            'effective_until' => 'nullable|date|after:effective_from',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
