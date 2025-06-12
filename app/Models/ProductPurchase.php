<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProductPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'buyer_id',
        'seller_id',
        'purchase_token',
        'amount_paid',
        'currency',
        'platform_fee',
        'seller_revenue',
        'payment_method',
        'payment_id',
        'payment_status',
        'payment_gateway',
        'license_type',
        'license_key',
        'download_limit',
        'download_count',
        'expires_at',
        'download_token',
        'last_download_at',
        'download_ip_addresses',
        'status',
        'refund_reason',
        'refunded_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_revenue' => 'decimal:2',
        'download_limit' => 'integer',
        'download_count' => 'integer',
        'expires_at' => 'datetime',
        'last_download_at' => 'datetime',
        'download_ip_addresses' => 'array',
        'refunded_at' => 'datetime',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'payment_status' => 'pending',
        'license_type' => 'single_use',
        'download_limit' => 5,
        'download_count' => 0,
        'status' => 'active',
    ];

    protected $hidden = [
        'download_token',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->purchase_token)) {
                $purchase->purchase_token = 'purchase_' . Str::random(32) . '_' . time();
            }

            if (empty($purchase->license_key)) {
                $purchase->license_key = 'lic_' . Str::upper(Str::random(4)) . '-' .
                                       Str::upper(Str::random(4)) . '-' .
                                       Str::upper(Str::random(4)) . '-' .
                                       Str::upper(Str::random(4));
            }

            if (empty($purchase->download_token)) {
                $purchase->download_token = Str::random(64);
            }
        });
    }

    /**
     * Get the product that was purchased
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(TechnicalProduct::class, 'product_id');
    }

    /**
     * Get the buyer (user who made the purchase)
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the seller (user who sold the product)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get secure downloads for this purchase
     */
    public function secureDownloads(): HasMany
    {
        return $this->hasMany(SecureDownload::class, 'purchase_id');
    }

    /**
     * Get completed downloads
     */
    public function completedDownloads(): HasMany
    {
        return $this->secureDownloads()->where('is_completed', true);
    }

    /**
     * Scope for active purchases
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed payments
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope by license type
     */
    public function scopeByLicenseType($query, string $licenseType)
    {
        return $query->where('license_type', $licenseType);
    }

    /**
     * Scope for expired purchases
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Check if purchase is active and valid
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->payment_status !== 'completed') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if purchase has downloads remaining
     */
    public function hasDownloadsRemaining(): bool
    {
        if ($this->license_type === 'unlimited') {
            return true;
        }

        return $this->download_count < $this->download_limit;
    }

    /**
     * Check if purchase is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Calculate days until expiration
     */
    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }

        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Get download usage percentage
     */
    public function getDownloadUsagePercentageAttribute(): float
    {
        if ($this->license_type === 'unlimited') {
            return 0;
        }

        if ($this->download_limit === 0) {
            return 100;
        }

        return ($this->download_count / $this->download_limit) * 100;
    }

    /**
     * Increment download count
     */
    public function incrementDownloads(string $ipAddress = null): void
    {
        $this->increment('download_count');
        $this->update([
            'last_download_at' => now(),
        ]);

        if ($ipAddress) {
            $ips = $this->download_ip_addresses ?? [];
            if (!in_array($ipAddress, $ips)) {
                $ips[] = $ipAddress;
                $this->update(['download_ip_addresses' => $ips]);
            }
        }

        // Auto-expire if download limit reached
        if ($this->download_count >= $this->download_limit && $this->license_type !== 'unlimited') {
            $this->update(['status' => 'expired']);
        }
    }

    /**
     * Generate new download token
     */
    public function regenerateDownloadToken(): string
    {
        $newToken = Str::random(64);
        $this->update(['download_token' => $newToken]);
        return $newToken;
    }

    /**
     * Process refund
     */
    public function processRefund(string $reason): void
    {
        $this->update([
            'status' => 'refunded',
            'payment_status' => 'refunded',
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }

    /**
     * Get formatted license type
     */
    public function getFormattedLicenseTypeAttribute(): string
    {
        return match($this->license_type) {
            'single_use' => 'Single Use',
            'commercial' => 'Commercial License',
            'educational' => 'Educational License',
            'unlimited' => 'Unlimited License',
            default => 'Unknown License'
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->amount_paid, 2);
    }
}
