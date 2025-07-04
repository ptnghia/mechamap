<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MarketplaceDownloadHistory extends Model
{
    use HasFactory;

    protected $table = 'marketplace_download_history';

    protected $fillable = [
        'uuid',
        'user_id',
        'order_id',
        'order_item_id',
        'product_id',
        'file_name',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'downloaded_at',
        'ip_address',
        'user_agent',
        'download_method',
        'download_token',
        'is_valid_download',
        'validation_status',
        'validation_notes',
        'metadata',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'is_valid_download' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($downloadHistory) {
            if (empty($downloadHistory->uuid)) {
                $downloadHistory->uuid = Str::uuid();
            }
            if (empty($downloadHistory->downloaded_at)) {
                $downloadHistory->downloaded_at = now();
            }
        });
    }

    /**
     * Get the user who downloaded the file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this download
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'order_id');
    }

    /**
     * Get the order item associated with this download
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrderItem::class, 'order_item_id');
    }

    /**
     * Get the product associated with this download
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MarketplaceProduct::class, 'product_id');
    }

    /**
     * Scope for valid downloads
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid_download', true);
    }

    /**
     * Scope for downloads by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for downloads by product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
