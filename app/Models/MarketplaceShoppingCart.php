<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MarketplaceShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'marketplace_shopping_carts';

    protected $fillable = [
        'uuid',
        'user_id',
        'session_id',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'metadata',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            if (empty($cart->uuid)) {
                $cart->uuid = Str::uuid();
            }
            $cart->last_activity_at = now();
        });

        static::updating(function ($cart) {
            $cart->last_activity_at = now();
        });
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Cart Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(MarketplaceCartItem::class, 'shopping_cart_id');
    }

    /**
     * Scope: Active carts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope: For specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: For specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Check if cart is valid
     */
    public function isValid(): bool
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at > now());
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Calculate and update cart totals
     */
    public function calculateTotals(): void
    {
        $items = $this->items()->get();

        $this->subtotal = $items->sum('total_price');
        $this->tax_amount = $this->calculateTax();
        $this->shipping_amount = $this->calculateShipping();
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;

        $this->save();
    }

    /**
     * Calculate tax amount
     */
    protected function calculateTax(): float
    {
        // Basic tax calculation - can be enhanced based on location
        $taxRate = 0.10; // 10% tax rate
        return round($this->subtotal * $taxRate, 2);
    }

    /**
     * Calculate shipping amount
     */
    protected function calculateShipping(): float
    {
        // Basic shipping calculation
        if ($this->subtotal >= 100) {
            return 0; // Free shipping over $100
        }

        return 10.00; // Flat rate shipping
    }

    /**
     * Add product to cart
     */
    public function addProduct(MarketplaceProduct $product, int $quantity = 1, array $options = []): MarketplaceCartItem
    {
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->total_price = $existingItem->quantity * $existingItem->unit_price;
            $existingItem->save();

            $this->calculateTotals();
            return $existingItem;
        }

        $unitPrice = $product->is_on_sale && $product->sale_price
            ? $product->sale_price
            : $product->price;

        $cartItem = $this->items()->create([
            'uuid' => Str::uuid(),
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'sale_price' => $product->is_on_sale ? $product->sale_price : null,
            'total_price' => $quantity * $unitPrice,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'product_image' => $product->featured_image,
            'product_options' => $options,
        ]);

        $this->calculateTotals();
        return $cartItem;
    }

    /**
     * Remove product from cart
     */
    public function removeProduct(int $productId): bool
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->delete();
            $this->calculateTotals();
            return true;
        }

        return false;
    }

    /**
     * Update product quantity
     */
    public function updateQuantity(int $productId, int $quantity): bool
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if ($item) {
            if ($quantity <= 0) {
                return $this->removeProduct($productId);
            }

            $item->quantity = $quantity;
            $item->total_price = $item->quantity * $item->unit_price;
            $item->save();

            $this->calculateTotals();
            return true;
        }

        return false;
    }

    /**
     * Clear all items from cart
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->subtotal = 0;
        $this->tax_amount = 0;
        $this->shipping_amount = 0;
        $this->total_amount = 0;
        $this->save();
    }

    /**
     * Convert cart to order
     */
    public function convertToOrder(): void
    {
        $this->status = 'converted';
        $this->save();
    }

    /**
     * Mark cart as abandoned
     */
    public function markAsAbandoned(): void
    {
        $this->status = 'abandoned';
        $this->save();
    }

    /**
     * Get or create cart for user
     */
    public static function getOrCreateForUser(?int $userId, ?string $sessionId = null): self
    {
        $query = static::active();

        if ($userId) {
            $cart = $query->forUser($userId)->first();
        } elseif ($sessionId) {
            $cart = $query->forSession($sessionId)->first();
        } else {
            return static::create([
                'session_id' => session()->getId(),
                'expires_at' => now()->addDays(7),
            ]);
        }

        if (!$cart) {
            $cart = static::create([
                'user_id' => $userId,
                'session_id' => $sessionId ?: session()->getId(),
                'expires_at' => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    /**
     * Merge guest cart with user cart
     */
    public static function mergeGuestCart(string $sessionId, int $userId): self
    {
        $guestCart = static::forSession($sessionId)->active()->first();
        $userCart = static::getOrCreateForUser($userId);

        if ($guestCart && !$guestCart->isEmpty()) {
            foreach ($guestCart->items as $item) {
                $userCart->addProduct(
                    $item->product,
                    $item->quantity,
                    $item->product_options ?? []
                );
            }

            $guestCart->clear();
            $guestCart->delete();
        }

        return $userCart;
    }
}
