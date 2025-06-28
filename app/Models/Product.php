<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'seller_id',
        'product_category_id',
        'product_type',
        'seller_type',
        'price',
        'sale_price',
        'is_on_sale',
        'sale_starts_at',
        'sale_ends_at',
        'stock_quantity',
        'manage_stock',
        'in_stock',
        'low_stock_threshold',
        'technical_specs',
        'mechanical_properties',
        'material',
        'manufacturing_process',
        'standards_compliance',
        'file_formats',
        'software_compatibility',
        'file_size_mb',
        'download_limit',
        'images',
        'featured_image',
        'attachments',
        'video_url',
        'meta_title',
        'meta_description',
        'keywords',
        'status',
        'is_featured',
        'is_digital_download',
        'requires_shipping',
        'average_rating',
        'review_count',
        'sales_count',
        'view_count',
        'wishlist_count',
        'weight',
        'dimensions',
        'shipping_class',
        'requires_approval',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'technical_specs' => 'array',
        'mechanical_properties' => 'array',
        'standards_compliance' => 'array',
        'file_formats' => 'array',
        'images' => 'array',
        'attachments' => 'array',
        'keywords' => 'array',
        'dimensions' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'file_size_mb' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_on_sale' => 'boolean',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital_download' => 'boolean',
        'requires_shipping' => 'boolean',
        'requires_approval' => 'boolean',
        'is_approved' => 'boolean',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->sku)) {
                $product->sku = 'MECHA-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the seller (user) who owns this product
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
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    /**
     * Get the user who approved this product
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for published products
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for products by seller type
     */
    public function scopeBySellerType($query, $sellerType)
    {
        return $query->where('seller_type', $sellerType);
    }

    /**
     * Scope for products by type
     */
    public function scopeByType($query, $productType)
    {
        return $query->where('product_type', $productType);
    }

    /**
     * Scope for in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
    }

    /**
     * Get the current price (sale price if on sale, otherwise regular price)
     */
    public function getCurrentPrice()
    {
        if ($this->is_on_sale && $this->sale_price) {
            return $this->sale_price;
        }

        return $this->price;
    }

    /**
     * Check if product is on sale
     */
    public function isOnSale(): bool
    {
        if (!$this->is_on_sale || !$this->sale_price) {
            return false;
        }

        $now = now();

        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return false;
        }

        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if product is low in stock
     */
    public function isLowStock(): bool
    {
        if (!$this->manage_stock) {
            return false;
        }

        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->isOnSale()) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Check if user can purchase this product
     */
    public function canBePurchasedBy(User $user): bool
    {
        // Basic checks
        if ($this->status !== 'published' || !$this->is_approved) {
            return false;
        }

        // Stock check for physical products
        if ($this->product_type === 'physical' && $this->manage_stock && !$this->in_stock) {
            return false;
        }

        // Seller cannot buy their own product
        if ($this->seller_id === $user->id) {
            return false;
        }

        return true;
    }
}
