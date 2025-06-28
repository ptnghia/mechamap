<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketplaceSeller extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'seller_type',
        'business_type',
        'business_name',
        'business_registration_number',
        'tax_identification_number',
        'business_description',
        'contact_person_name',
        'contact_email',
        'contact_phone',
        'business_address',
        'website_url',
        'industry_categories',
        'specializations',
        'certifications',
        'capabilities',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_documents',
        'verification_notes',
        'rating_average',
        'rating_count',
        'total_sales',
        'total_revenue',
        'total_products',
        'active_products',
        'commission_rate',
        'pending_earnings',
        'available_earnings',
        'total_earnings',
        'payment_methods',
        'auto_approve_orders',
        'processing_time_days',
        'shipping_methods',
        'return_policy',
        'terms_conditions',
        'status',
        'is_featured',
        'last_active_at',
        'suspended_at',
        'suspension_reason',
        'store_name',
        'store_slug',
        'store_description',
        'store_logo',
        'store_banner',
        'store_settings',
    ];

    protected $casts = [
        'business_address' => 'array',
        'industry_categories' => 'array',
        'specializations' => 'array',
        'certifications' => 'array',
        'capabilities' => 'array',
        'verification_documents' => 'array',
        'payment_methods' => 'array',
        'shipping_methods' => 'array',
        'return_policy' => 'array',
        'terms_conditions' => 'array',
        'store_settings' => 'array',
        'auto_approve_orders' => 'boolean',
        'is_featured' => 'boolean',
        'rating_average' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'available_earnings' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->store_slug) && $model->store_name) {
                $model->store_slug = Str::slug($model->store_name);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(MarketplaceProduct::class, 'seller_id');
    }

    public function orderItems()
    {
        return $this->hasMany(MarketplaceOrderItem::class, 'seller_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeBySellerType($query, $type)
    {
        return $query->where('seller_type', $type);
    }

    // Accessors
    public function getVerificationStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Chờ xác minh',
            'verified' => 'Đã xác minh',
            'rejected' => 'Bị từ chối',
        ];

        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
            'suspended' => 'Bị đình chỉ',
            'banned' => 'Bị cấm',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getSellerTypeLabelAttribute()
    {
        $labels = [
            'supplier' => 'Nhà Cung Cấp',
            'manufacturer' => 'Nhà Sản Xuất',
            'brand' => 'Thương Hiệu',
        ];

        return $labels[$this->seller_type] ?? $this->seller_type;
    }

    // Methods
    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function canSell()
    {
        return $this->isActive() && $this->isVerified();
    }

    public function updateEarnings($amount, $type = 'add')
    {
        if ($type === 'add') {
            $this->increment('pending_earnings', $amount);
            $this->increment('total_earnings', $amount);
        } else {
            $this->decrement('pending_earnings', $amount);
        }
    }
}
