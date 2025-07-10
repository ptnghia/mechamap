<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceWishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'notify_when_available',
        'notify_price_drops',
        'target_price',
        'last_notified_at',
    ];

    protected $casts = [
        'notify_when_available' => 'boolean',
        'notify_price_drops' => 'boolean',
        'target_price' => 'decimal:2',
        'last_notified_at' => 'datetime',
    ];

    /**
     * Get the user who owns this wishlist item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product in this wishlist item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
    }

    /**
     * Check if should notify when product becomes available
     */
    public function shouldNotifyWhenAvailable(): bool
    {
        return $this->notify_when_available && $this->product && !$this->product->in_stock;
    }

    /**
     * Check if should notify for price drops
     */
    public function shouldNotifyForPriceDrops(): bool
    {
        return $this->notify_price_drops;
    }

    /**
     * Check if target price is reached
     */
    public function isTargetPriceReached(): bool
    {
        if (!$this->target_price || !$this->product) {
            return false;
        }

        $currentPrice = $this->product->getCurrentPrice();
        return $currentPrice <= $this->target_price;
    }

    /**
     * Update last notified timestamp
     */
    public function markAsNotified(): void
    {
        $this->update(['last_notified_at' => now()]);
    }

    /**
     * Check if recently notified (within last 24 hours)
     */
    public function wasRecentlyNotified(): bool
    {
        if (!$this->last_notified_at) {
            return false;
        }

        return $this->last_notified_at->isAfter(now()->subHours(24));
    }

    /**
     * Scope for items that should notify when available
     */
    public function scopeNotifyWhenAvailable($query)
    {
        return $query->where('notify_when_available', true);
    }

    /**
     * Scope for items that should notify for price drops
     */
    public function scopeNotifyPriceDrops($query)
    {
        return $query->where('notify_price_drops', true);
    }

    /**
     * Get wishlist items for a specific product
     */
    public static function getProductWishlistUsers(int $productId)
    {
        return static::where('product_id', $productId)
            ->with('user')
            ->get();
    }

    /**
     * Get users who should be notified when product becomes available
     */
    public static function getUsersToNotifyWhenAvailable(int $productId)
    {
        return static::where('product_id', $productId)
            ->notifyWhenAvailable()
            ->with('user')
            ->get();
    }

    /**
     * Get users who should be notified for price drops
     */
    public static function getUsersToNotifyForPriceDrops(int $productId)
    {
        return static::where('product_id', $productId)
            ->notifyPriceDrops()
            ->with('user')
            ->get();
    }

    /**
     * Add product to wishlist
     */
    public static function addToWishlist(
        int $userId,
        int $productId,
        bool $notifyWhenAvailable = true,
        bool $notifyPriceDrops = true,
        ?float $targetPrice = null
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
            ],
            [
                'notify_when_available' => $notifyWhenAvailable,
                'notify_price_drops' => $notifyPriceDrops,
                'target_price' => $targetPrice,
            ]
        );
    }

    /**
     * Remove product from wishlist
     */
    public static function removeFromWishlist(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Check if product is in user's wishlist
     */
    public static function isInWishlist(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get user's wishlist count
     */
    public static function getUserWishlistCount(int $userId): int
    {
        return static::where('user_id', $userId)->count();
    }

    /**
     * Get product's wishlist count
     */
    public static function getProductWishlistCount(int $productId): int
    {
        return static::where('product_id', $productId)->count();
    }

    /**
     * Get user's wishlist with products
     */
    public static function getUserWishlist(int $userId, int $limit = 20)
    {
        return static::where('user_id', $userId)
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'slug', 'price', 'sale_price', 'featured_image', 'in_stock', 'stock_quantity');
            }])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clear user's entire wishlist
     */
    public static function clearUserWishlist(int $userId): int
    {
        return static::where('user_id', $userId)->delete();
    }

    /**
     * Get wishlist statistics
     */
    public static function getStats(): array
    {
        return [
            'total_items' => static::count(),
            'notify_when_available' => static::notifyWhenAvailable()->count(),
            'notify_price_drops' => static::notifyPriceDrops()->count(),
            'with_target_price' => static::whereNotNull('target_price')->count(),
            'most_wishlisted_products' => static::select('product_id')
                ->selectRaw('COUNT(*) as wishlist_count')
                ->groupBy('product_id')
                ->orderByDesc('wishlist_count')
                ->limit(10)
                ->with('product:id,name')
                ->get(),
        ];
    }

    /**
     * Clean up old wishlist items (older than 1 year with no activity)
     */
    public static function cleanupOldItems(): int
    {
        return static::where('created_at', '<', now()->subYear())
            ->whereNull('last_notified_at')
            ->delete();
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(
        bool $notifyWhenAvailable = null,
        bool $notifyPriceDrops = null,
        ?float $targetPrice = null
    ): bool {
        $updates = [];
        
        if ($notifyWhenAvailable !== null) {
            $updates['notify_when_available'] = $notifyWhenAvailable;
        }
        
        if ($notifyPriceDrops !== null) {
            $updates['notify_price_drops'] = $notifyPriceDrops;
        }
        
        if ($targetPrice !== null) {
            $updates['target_price'] = $targetPrice;
        }

        return $this->update($updates);
    }

    /**
     * Get formatted target price
     */
    public function getFormattedTargetPriceAttribute(): ?string
    {
        if (!$this->target_price) {
            return null;
        }

        return number_format($this->target_price, 0, ',', '.') . '₫';
    }

    /**
     * Check if product is currently available and in stock
     */
    public function isProductAvailable(): bool
    {
        return $this->product && $this->product->in_stock && $this->product->stock_quantity > 0;
    }

    /**
     * Get the current price of the product
     */
    public function getCurrentProductPrice(): ?float
    {
        return $this->product ? $this->product->getCurrentPrice() : null;
    }
}
