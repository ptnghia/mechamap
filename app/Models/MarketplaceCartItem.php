<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MarketplaceCartItem extends Model
{
    use HasFactory;

    protected $table = 'marketplace_cart_items';

    protected $fillable = [
        'uuid',
        'shopping_cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'sale_price',
        'total_price',
        'product_name',
        'product_sku',
        'product_image',
        'product_options',
        'metadata',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'product_options' => 'array',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->uuid)) {
                $item->uuid = Str::uuid();
            }
        });
    }

    /**
     * Relationship with Shopping Cart
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(MarketplaceShoppingCart::class, 'shopping_cart_id');
    }

    /**
     * Relationship with Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
    }

    /**
     * Get the effective price (sale price if available, otherwise unit price)
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->sale_price ?? $this->unit_price;
    }

    /**
     * Check if item is on sale
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->unit_price;
    }

    /**
     * Get savings amount if on sale
     */
    public function getSavingsAttribute(): float
    {
        if ($this->is_on_sale) {
            return ($this->unit_price - $this->sale_price) * $this->quantity;
        }

        return 0;
    }

    /**
     * Update total price based on quantity and unit price
     */
    public function updateTotal(): void
    {
        $this->total_price = $this->quantity * $this->effective_price;
        $this->save();
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity(int $amount = 1): void
    {
        $this->quantity += $amount;
        $this->updateTotal();
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity(int $amount = 1): bool
    {
        if ($this->quantity <= $amount) {
            $this->delete();
            return false; // Item was removed
        }

        $this->quantity -= $amount;
        $this->updateTotal();
        return true; // Item still exists
    }

    /**
     * Set quantity
     */
    public function setQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            $this->delete();
            return false; // Item was removed
        }

        $this->quantity = $quantity;
        $this->updateTotal();
        return true; // Item updated
    }

    /**
     * Check if product is still available
     */
    public function isProductAvailable(): bool
    {
        return $this->product &&
               $this->product->is_active &&
               $this->product->status === 'approved' &&
               ($this->product->stock_quantity === null || $this->product->stock_quantity >= $this->quantity);
    }
}
