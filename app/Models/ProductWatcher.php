<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWatcher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'target_price',
        'notify_any_drop',
        'notify_stock_changes',
        'is_active',
        'last_notified_at',
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
        'notify_any_drop' => 'boolean',
        'notify_stock_changes' => 'boolean',
        'is_active' => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    /**
     * Get the user who is watching the product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product being watched
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
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
     * Check if should notify for any price drop
     */
    public function shouldNotifyForPriceDrop(float $newPrice, float $oldPrice): bool
    {
        if (!$this->notify_any_drop || !$this->is_active) {
            return false;
        }

        // Only notify if price actually dropped
        return $newPrice < $oldPrice;
    }

    /**
     * Check if should notify for target price
     */
    public function shouldNotifyForTargetPrice(float $newPrice): bool
    {
        if (!$this->target_price || !$this->is_active) {
            return false;
        }

        return $newPrice <= $this->target_price;
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
     * Scope for active watchers only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for watchers with target price
     */
    public function scopeWithTargetPrice($query)
    {
        return $query->whereNotNull('target_price');
    }

    /**
     * Scope for watchers that notify on any drop
     */
    public function scopeNotifyAnyDrop($query)
    {
        return $query->where('notify_any_drop', true);
    }

    /**
     * Get watchers for a specific product
     */
    public static function getProductWatchers(int $productId)
    {
        return static::where('product_id', $productId)
            ->active()
            ->with('user')
            ->get();
    }

    /**
     * Get watchers that should be notified for price drop
     */
    public static function getWatchersForPriceDrop(int $productId, float $newPrice, float $oldPrice)
    {
        return static::where('product_id', $productId)
            ->active()
            ->where(function ($query) use ($newPrice, $oldPrice) {
                // Watchers who want any price drop notification
                $query->where('notify_any_drop', true)
                      ->where(function ($q) use ($newPrice, $oldPrice) {
                          // Only if price actually dropped
                          return $newPrice < $oldPrice;
                      });
                
                // OR watchers whose target price is reached
                $query->orWhere(function ($q) use ($newPrice) {
                    $q->whereNotNull('target_price')
                      ->where('target_price', '>=', $newPrice);
                });
            })
            ->with('user')
            ->get();
    }

    /**
     * Create or update watcher
     */
    public static function createOrUpdate(
        int $userId,
        int $productId,
        ?float $targetPrice = null,
        bool $notifyAnyDrop = true,
        bool $notifyStockChanges = false
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
            ],
            [
                'target_price' => $targetPrice,
                'notify_any_drop' => $notifyAnyDrop,
                'notify_stock_changes' => $notifyStockChanges,
                'is_active' => true,
            ]
        );
    }

    /**
     * Remove watcher
     */
    public static function removeWatcher(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Toggle watcher active status
     */
    public function toggleActive(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Get user's watching products count
     */
    public static function getUserWatchingCount(int $userId): int
    {
        return static::where('user_id', $userId)
            ->active()
            ->count();
    }

    /**
     * Get product's watchers count
     */
    public static function getProductWatchersCount(int $productId): int
    {
        return static::where('product_id', $productId)
            ->active()
            ->count();
    }

    /**
     * Clean up inactive watchers (older than 6 months)
     */
    public static function cleanupInactiveWatchers(): int
    {
        return static::where('is_active', false)
            ->where('updated_at', '<', now()->subMonths(6))
            ->delete();
    }

    /**
     * Get statistics
     */
    public static function getStats(): array
    {
        return [
            'total_watchers' => static::count(),
            'active_watchers' => static::active()->count(),
            'with_target_price' => static::active()->withTargetPrice()->count(),
            'notify_any_drop' => static::active()->notifyAnyDrop()->count(),
            'most_watched_products' => static::select('product_id')
                ->selectRaw('COUNT(*) as watchers_count')
                ->active()
                ->groupBy('product_id')
                ->orderByDesc('watchers_count')
                ->limit(10)
                ->with('product:id,name')
                ->get(),
        ];
    }
}
