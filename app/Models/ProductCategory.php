<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'sort_order',
        'is_active',
        'commission_rate',
        'engineering_discipline',
        'required_software',
        'product_count',
        'total_sales',
    ];

    protected $casts = [
        'required_software' => 'array',
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'sort_order' => 'integer',
        'product_count' => 'integer',
        'total_sales' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'commission_rate' => 10.00,
        'sort_order' => 0,
        'product_count' => 0,
        'total_sales' => 0,
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    /**
     * Get all products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(TechnicalProduct::class, 'category_id');
    }

    /**
     * Get active products in this category
     */
    public function activeProducts(): HasMany
    {
        return $this->products()->where('status', 'approved');
    }

    /**
     * Get marketplace products in this category
     */
    public function marketplaceProducts(): HasMany
    {
        return $this->hasMany(\App\Models\MarketplaceProduct::class, 'product_category_id');
    }

    /**
     * Get active marketplace products in this category
     */
    public function activeMarketplaceProducts(): HasMany
    {
        return $this->marketplaceProducts()->where('status', 'approved')->where('is_active', true);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for engineering discipline
     */
    public function scopeByDiscipline($query, string $discipline)
    {
        return $query->where('engineering_discipline', $discipline);
    }

    /**
     * Get category path for breadcrumbs
     */
    public function getPathAttribute(): array
    {
        $path = [];
        $category = $this;

        while ($category) {
            array_unshift($path, [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]);
            $category = $category->parent;
        }

        return $path;
    }

    /**
     * Get full category name with parent path
     */
    public function getFullNameAttribute(): string
    {
        $path = collect($this->path)->pluck('name')->implode(' > ');
        return $path;
    }

    /**
     * Check if category has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Update product statistics
     */
    public function updateStats(): void
    {
        $this->update([
            'product_count' => $this->activeProducts()->count(),
            'total_sales' => $this->activeProducts()->sum('sales_count'),
        ]);
    }
}
