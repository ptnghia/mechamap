<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketplaceProduct extends Model
{
    use HasFactory, SoftDeletes;

    // Product Type Constants
    const TYPE_DIGITAL = 'digital';
    const TYPE_NEW_PRODUCT = 'new_product';
    const TYPE_USED_PRODUCT = 'used_product';

    // Seller Type Constants
    const SELLER_SUPPLIER = 'supplier';
    const SELLER_MANUFACTURER = 'manufacturer';
    const SELLER_BRAND = 'brand';

    /**
     * Get all available product types
     */
    public static function getProductTypes(): array
    {
        return [
            self::TYPE_DIGITAL => 'Sản phẩm kỹ thuật số',
            self::TYPE_NEW_PRODUCT => 'Sản phẩm mới',
            self::TYPE_USED_PRODUCT => 'Sản phẩm cũ',
        ];
    }

    /**
     * Get all available seller types
     */
    public static function getSellerTypes(): array
    {
        return [
            self::SELLER_SUPPLIER => 'Nhà cung cấp',
            self::SELLER_MANUFACTURER => 'Nhà sản xuất',
            self::SELLER_BRAND => 'Thương hiệu',
        ];
    }

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
        'digital_files',
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
        'digital_files' => 'array',
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

        // Update seller stats when product is created
        static::created(function ($model) {
            static::updateSellerStats($model->seller_id);
        });

        // Update seller stats when product is updated (status or is_active might change)
        static::updated(function ($model) {
            if ($model->isDirty('status') || $model->isDirty('is_active') || $model->isDirty('seller_id')) {
                // If seller changed, update both old and new seller
                if ($model->isDirty('seller_id') && $model->getOriginal('seller_id')) {
                    static::updateSellerStats($model->getOriginal('seller_id'));
                }
                static::updateSellerStats($model->seller_id);
            }
        });

        // Update seller stats when product is deleted
        static::deleted(function ($model) {
            static::updateSellerStats($model->seller_id);
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Update seller statistics
     */
    protected static function updateSellerStats($sellerId)
    {
        if (!$sellerId) return;

        try {
            $seller = MarketplaceSeller::find($sellerId);
            if ($seller) {
                $totalProducts = static::where('seller_id', $sellerId)->count();
                $activeProducts = static::where('seller_id', $sellerId)
                                        ->where('status', 'approved')
                                        ->where('is_active', true)
                                        ->count();

                $seller->update([
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't throw exception to prevent transaction failure
            \Log::error("Failed to update seller stats for ID {$sellerId}: " . $e->getMessage());
        }
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

    public function getFirstImageUrl()
    {
        // Check if featured_image exists
        if ($this->featured_image) {
            return asset('images/products/' . $this->featured_image);
        }

        // Check if images array has any images
        if ($this->images && is_array($this->images) && count($this->images) > 0) {
            return asset('images/products/' . $this->images[0]);
        }

        // Return placeholder image
        return asset('images/placeholder-product.jpg');
    }

    /**
     * Get all media files for this product
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get digital files for this product
     */
    public function digitalFiles()
    {
        return $this->morphMany(Media::class, 'mediable')
            ->whereIn('file_category', ['cad_drawing', 'cad_model', 'technical_doc'])
            ->where('is_approved', true);
    }

    /**
     * Get download history for this product
     */
    public function downloadHistory()
    {
        return $this->hasMany(MarketplaceDownloadHistory::class, 'product_id');
    }

    /**
     * Check if this is a digital product
     */
    public function isDigitalProduct(): bool
    {
        return $this->product_type === 'digital' ||
               !empty($this->digital_files) ||
               $this->digitalFiles()->exists();
    }

    /**
     * Get effective price (sale price if on sale, otherwise regular price)
     */
    public function getEffectivePrice(): float
    {
        if ($this->is_on_sale && $this->sale_price && $this->sale_price < $this->price) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage(): float
    {
        if ($this->is_on_sale && $this->sale_price && $this->sale_price < $this->price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
        }
        return 0;
    }

    /**
     * Check if product is available for purchase
     */
    public function isAvailable(): bool
    {
        if (!$this->is_active || $this->status !== 'approved') {
            return false;
        }

        // Digital products are always available
        if ($this->isDigitalProduct()) {
            return true;
        }

        // Physical products depend on stock
        if ($this->manage_stock) {
            return $this->in_stock && $this->stock_quantity > 0;
        }

        return $this->in_stock;
    }

    /**
     * Normalize product data
     */
    public function normalize(): bool
    {
        $service = app(\App\Services\MarketplaceDataNormalizationService::class);
        $changes = $service->normalizeProduct($this);

        if (!empty($changes)) {
            return $this->update($changes);
        }

        return true;
    }

    /**
     * Validate data integrity
     */
    public function validateIntegrity(): array
    {
        $service = app(\App\Services\MarketplaceDataNormalizationService::class);
        return $service->validateProductIntegrity($this);
    }

    /**
     * Get all downloadable files for this product
     */
    public function getDownloadableFiles(): array
    {
        $files = [];

        // Files from digital_files JSON field
        if (!empty($this->digital_files)) {
            foreach ($this->digital_files as $file) {
                $files[] = [
                    'type' => 'json',
                    'name' => $file['name'] ?? 'Unknown',
                    'path' => $file['path'] ?? '',
                    'size' => $file['size'] ?? 0,
                    'mime_type' => $file['mime_type'] ?? 'application/octet-stream',
                    'description' => $file['description'] ?? '',
                ];
            }
        }

        // Files from Media relationship
        foreach ($this->digitalFiles as $media) {
            $files[] = [
                'type' => 'media',
                'id' => $media->id,
                'name' => $media->file_name,
                'path' => $media->file_path,
                'size' => $media->file_size,
                'mime_type' => $media->mime_type,
                'description' => $media->description ?? '',
                'category' => $media->file_category,
            ];
        }

        return $files;
    }

    /**
     * Get total file size for this product
     */
    public function getTotalFileSizeAttribute(): float
    {
        $totalSize = 0;

        // Size from digital_files JSON
        if (!empty($this->digital_files)) {
            foreach ($this->digital_files as $file) {
                $totalSize += $file['size'] ?? 0;
            }
        }

        // Size from Media files
        $totalSize += $this->digitalFiles()->sum('file_size');

        return $totalSize;
    }

    /**
     * Get formatted total file size
     */
    public function getFormattedTotalFileSizeAttribute(): string
    {
        $bytes = $this->total_file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if user has purchased this product
     */
    public function isPurchasedBy($userId): bool
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->where('payment_status', 'paid');
            })
            ->exists();
    }

    /**
     * Get purchase record for user
     */
    public function getPurchaseByUser($userId)
    {
        return $this->orderItems()
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->where('payment_status', 'paid');
            })
            ->with('order')
            ->first();
    }

    /**
     * Get download count for user
     */
    public function getDownloadCountByUser($userId): int
    {
        return $this->downloadHistory()
            ->where('user_id', $userId)
            ->where('is_valid_download', true)
            ->count();
    }

    /**
     * Check if user can download files
     */
    public function canUserDownload($userId): array
    {
        // Check if user has purchased the product
        if (!$this->isPurchasedBy($userId)) {
            return [
                'can_download' => false,
                'reason' => 'Bạn chưa mua sản phẩm này'
            ];
        }

        // For digital products, no download limit by default
        // (as per requirement: không giới hạn thời gian tải)
        return [
            'can_download' => true,
            'reason' => 'Có quyền tải xuống'
        ];
    }

    /**
     * Add digital file to product
     */
    public function addDigitalFile(array $fileData): void
    {
        $digitalFiles = $this->digital_files ?? [];
        $digitalFiles[] = $fileData;
        $this->update(['digital_files' => $digitalFiles]);
    }

    /**
     * Remove digital file from product
     */
    public function removeDigitalFile(int $index): void
    {
        $digitalFiles = $this->digital_files ?? [];
        if (isset($digitalFiles[$index])) {
            unset($digitalFiles[$index]);
            $this->update(['digital_files' => array_values($digitalFiles)]);
        }
    }

    /**
     * Update digital file
     */
    public function updateDigitalFile(int $index, array $fileData): void
    {
        $digitalFiles = $this->digital_files ?? [];
        if (isset($digitalFiles[$index])) {
            $digitalFiles[$index] = array_merge($digitalFiles[$index], $fileData);
            $this->update(['digital_files' => $digitalFiles]);
        }
    }
}
