<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DownloadToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'user_id',
        'product_purchase_id',
        'protected_file_id',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_used',
        'used_at',
        'download_attempts'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
        'download_attempts' => 'integer'
    ];

    /**
     * Get the user that owns the download token
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product purchase associated with the token
     */
    public function productPurchase(): BelongsTo
    {
        return $this->belongsTo(ProductPurchase::class);
    }

    /**
     * Get the protected file associated with the token
     */
    public function protectedFile(): BelongsTo
    {
        return $this->belongsTo(ProtectedFile::class);
    }

    /**
     * Check if the token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the token is valid for use
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => Carbon::now()
        ]);
    }

    /**
     * Increment download attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('download_attempts');
    }

    /**
     * Scope for valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope for expired tokens
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', Carbon::now());
    }
}
