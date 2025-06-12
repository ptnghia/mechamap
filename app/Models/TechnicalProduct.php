<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TechnicalProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'showcase_id',
        'seller_id',
        'title',
        'slug',
        'description',
        'short_description',
        'price',
        'currency',
        'discount_percentage',
        'category_id',
        'tags',
        'software_compatibility',
        'file_formats',
        'complexity_level',
        'industry_applications',
        'preview_images',
        'sample_files',
        'protected_files',
        'documentation_files',
        'view_count',
        'download_count',
        'sales_count',
        'total_revenue',
        'rating_average',
        'rating_count',
        'status',
        'is_featured',
        'is_bestseller',
        'featured_until',
        'meta_title',
        'meta_description',
        'keywords',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tags' => 'array',
        'software_compatibility' => 'array',
        'file_formats' => 'array',
        'industry_applications' => 'array',
        'preview_images' => 'array',
        'sample_files' => 'array',
        'protected_files' => 'array',
        'documentation_files' => 'array',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'sales_count' => 'integer',
        'total_revenue' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'featured_until' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'discount_percentage' => 0,
        'complexity_level' => 'intermediate',
        'view_count' => 0,
        'download_count' => 0,
        'sales_count' => 0,
        'total_revenue' => 0,
        'rating_average' => 0,
        'rating_count' => 0,
        'status' => 'draft',
        'is_featured' => false,
        'is_bestseller' => false,
    ];

    protected $appends = ['sale_price'];

    /**
     * Get the seller (user who owns this product)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the product category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the original showcase (if converted from showcase)
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class, 'showcase_id');
    }

    /**
     * Get all purchases of this product
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(ProductPurchase::class, 'product_id');
    }

    /**
     * Get active purchases of this product
     */
    public function activePurchases(): HasMany
    {
        return $this->purchases()->where('status', 'active');
    }

    /**
     * Get protected files for this product
     */
    public function protectedFiles(): HasMany
    {
        return $this->hasMany(ProtectedFile::class, 'product_id');
    }

    /**
     * Get active protected files
     */
    public function activeFiles(): HasMany
    {
        return $this->protectedFiles()->where('is_active', true);
    }

    /**
     * Calculate sale price with discount
     */
    public function getSalePriceAttribute(): float
    {
        return $this->price * (1 - ($this->discount_percentage / 100));
    }

    /**
     * Auto-generate slug from title
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Scope for approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where(function($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    /**
     * Scope for bestsellers
     */
    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    /**
     * Scope by complexity level
     */
    public function scopeByComplexity($query, string $level)
    {
        return $query->where('complexity_level', $level);
    }

    /**
     * Scope by price range
     */
    public function scopeByPriceRange($query, float $minPrice = null, float $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    /**
     * Scope by software compatibility
     */
    public function scopeBySoftware($query, array $software)
    {
        foreach ($software as $soft) {
            $query->whereJsonContains('software_compatibility', $soft);
        }

        return $query;
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, string $search)
    {
        return $query->whereFullText(['title', 'description', 'keywords'], $search)
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Check if product is purchased by user
     */
    public function isPurchasedBy(User $user): bool
    {
        return $this->purchases()
                    ->where('buyer_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * Get average rating
     */
    public function updateRating(): void
    {
        // This would be implemented when review system is added
        // For now, we'll just use the existing rating fields
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment download count
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * Increment sales count and revenue
     */
    public function incrementSales(float $revenue): void
    {
        $this->increment('sales_count');
        $this->increment('total_revenue', $revenue);

        // Update bestseller status if sales > 100
        if ($this->sales_count >= 100) {
            $this->update(['is_bestseller' => true]);
        }
    }

    /**
     * Check if product is free
     */
    public function isFree(): bool
    {
        return $this->sale_price <= 0;
    }

    /**
     * Check if product has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_percentage > 0;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }

        return $this->currency . ' ' . number_format($this->sale_price, 2);
    }
}
