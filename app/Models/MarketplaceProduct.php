<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketplaceProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'seller_id',
        'product_category_id',
        'product_type',
        'seller_type',
        'industry_category',
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
        'meta_title',
        'meta_description',
        'tags',
        'status',
        'is_featured',
        'is_active',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'view_count',
        'like_count',
        'download_count',
        'purchase_count',
        'rating_average',
        'rating_count',
        'display_order',
        'featured_at',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'mechanical_properties' => 'array',
        'standards_compliance' => 'array',
        'file_formats' => 'array',
        'software_compatibility' => 'array',
        'images' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
        'is_on_sale' => 'boolean',
        'manage_stock' => 'boolean',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'file_size_mb' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'approved_at' => 'datetime',
        'featured_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->sku)) {
                $model->sku = 'MP-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function seller()
    {
        return $this->belongsTo(MarketplaceSeller::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(MarketplaceOrderItem::class, 'product_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBySellerType($query, $type)
    {
        return $query->where('seller_type', $type);
    }

    public function scopeByProductType($query, $type)
    {
        return $query->where('product_type', $type);
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        if ($this->is_on_sale && $this->sale_price) {
            $now = now();
            if ((!$this->sale_starts_at || $now >= $this->sale_starts_at) &&
                (!$this->sale_ends_at || $now <= $this->sale_ends_at)) {
                return $this->sale_price;
            }
        }
        return $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->is_on_sale && $this->sale_price && $this->price > 0) {
            return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
        }
        return 0;
    }

    public function getIsInStockAttribute()
    {
        if (!$this->manage_stock) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute()
    {
        if (!$this->manage_stock) {
            return false;
        }
        return $this->stock_quantity <= $this->low_stock_threshold;
    }
}
