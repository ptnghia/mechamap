<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'old_sale_price',
        'new_sale_price',
        'price_change',
        'price_change_percentage',
        'change_type',
        'reason',
        'changed_by',
        'effective_date',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'old_sale_price' => 'decimal:2',
        'new_sale_price' => 'decimal:2',
        'price_change' => 'decimal:2',
        'price_change_percentage' => 'decimal:2',
        'effective_date' => 'datetime',
    ];

    /**
     * Get the product that this price history belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
    }

    /**
     * Get the user who changed the price
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Check if this is a price decrease
     */
    public function isPriceDecrease(): bool
    {
        return $this->change_type === 'decrease';
    }

    /**
     * Check if this is a price increase
     */
    public function isPriceIncrease(): bool
    {
        return $this->change_type === 'increase';
    }

    /**
     * Get the effective price (considering sale price)
     */
    public function getOldEffectivePriceAttribute(): float
    {
        return $this->old_sale_price ?? $this->old_price;
    }

    /**
     * Get the new effective price (considering sale price)
     */
    public function getNewEffectivePriceAttribute(): float
    {
        return $this->new_sale_price ?? $this->new_price;
    }

    /**
     * Get formatted price change
     */
    public function getFormattedPriceChangeAttribute(): string
    {
        $sign = $this->price_change >= 0 ? '+' : '';
        return $sign . number_format($this->price_change, 0, ',', '.') . '₫';
    }

    /**
     * Get formatted percentage change
     */
    public function getFormattedPercentageChangeAttribute(): string
    {
        $sign = $this->price_change_percentage >= 0 ? '+' : '';
        return $sign . number_format($this->price_change_percentage, 1) . '%';
    }

    /**
     * Scope for price decreases only
     */
    public function scopePriceDecreases($query)
    {
        return $query->where('change_type', 'decrease');
    }

    /**
     * Scope for price increases only
     */
    public function scopePriceIncreases($query)
    {
        return $query->where('change_type', 'increase');
    }

    /**
     * Scope for significant price changes (more than X%)
     */
    public function scopeSignificantChanges($query, float $threshold = 5.0)
    {
        return $query->where(function ($q) use ($threshold) {
            $q->where('price_change_percentage', '>=', $threshold)
              ->orWhere('price_change_percentage', '<=', -$threshold);
        });
    }

    /**
     * Get recent price history for a product
     */
    public static function getRecentHistory(int $productId, int $days = 30)
    {
        return static::where('product_id', $productId)
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get price trend for a product
     */
    public static function getPriceTrend(int $productId, int $days = 30): array
    {
        $history = static::getRecentHistory($productId, $days);
        
        if ($history->isEmpty()) {
            return [
                'trend' => 'stable',
                'total_change' => 0,
                'total_change_percentage' => 0,
                'changes_count' => 0
            ];
        }

        $totalChange = $history->sum('price_change');
        $changesCount = $history->count();
        $decreases = $history->where('change_type', 'decrease')->count();
        $increases = $history->where('change_type', 'increase')->count();

        // Determine trend
        $trend = 'stable';
        if ($decreases > $increases) {
            $trend = 'decreasing';
        } elseif ($increases > $decreases) {
            $trend = 'increasing';
        }

        // Calculate total percentage change
        $firstPrice = $history->last()->old_price ?? 0;
        $lastPrice = $history->first()->new_price ?? 0;
        $totalChangePercentage = $firstPrice > 0 ? (($lastPrice - $firstPrice) / $firstPrice) * 100 : 0;

        return [
            'trend' => $trend,
            'total_change' => $totalChange,
            'total_change_percentage' => round($totalChangePercentage, 2),
            'changes_count' => $changesCount,
            'decreases_count' => $decreases,
            'increases_count' => $increases,
        ];
    }

    /**
     * Create price history record
     */
    public static function createRecord(
        MarketplaceProduct $product,
        float $oldPrice,
        float $newPrice,
        ?float $oldSalePrice = null,
        ?float $newSalePrice = null,
        ?string $reason = null,
        ?int $changedBy = null
    ): self {
        $priceChange = $newPrice - $oldPrice;
        $priceChangePercentage = $oldPrice > 0 ? (($newPrice - $oldPrice) / $oldPrice) * 100 : 0;
        
        $changeType = 'no_change';
        if ($priceChange > 0) {
            $changeType = 'increase';
        } elseif ($priceChange < 0) {
            $changeType = 'decrease';
        }

        return static::create([
            'product_id' => $product->id,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'old_sale_price' => $oldSalePrice,
            'new_sale_price' => $newSalePrice,
            'price_change' => $priceChange,
            'price_change_percentage' => round($priceChangePercentage, 2),
            'change_type' => $changeType,
            'reason' => $reason,
            'changed_by' => $changedBy,
            'effective_date' => now(),
        ]);
    }
}
