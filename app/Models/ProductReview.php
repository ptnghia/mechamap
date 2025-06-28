<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'content',
        'is_verified_purchase',
        'helpful_count',
        'not_helpful_count',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the product this review belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for featured reviews
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for verified purchase reviews
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }
}
