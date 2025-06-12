<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SecureDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'protected_file_id',
        'user_id',
        'download_token',
        'download_url',
        'expires_at',
        'downloaded_at',
        'download_ip',
        'user_agent',
        'download_size',
        'download_duration_seconds',
        'is_completed',
        'is_verified',
        'failure_reason',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'download_size' => 'integer',
        'download_duration_seconds' => 'integer',
        'is_completed' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected $attributes = [
        'is_completed' => false,
        'is_verified' => false,
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($download) {
            if (empty($download->download_token)) {
                $download->download_token = Str::random(128);
            }

            if (empty($download->expires_at)) {
                $download->expires_at = now()->addHours(24); // Default 24-hour expiration
            }
        });
    }

    /**
     * Get the purchase this download belongs to
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(ProductPurchase::class, 'purchase_id');
    }

    /**
     * Get the protected file being downloaded
     */
    public function protectedFile(): BelongsTo
    {
        return $this->belongsTo(ProtectedFile::class, 'protected_file_id');
    }

    /**
     * Get the user downloading the file
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for active downloads (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('is_completed', false);
    }

    /**
     * Scope for completed downloads
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for expired downloads
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope for failed downloads
     */
    public function scopeFailed($query)
    {
        return $query->whereNotNull('failure_reason');
    }

    /**
     * Check if download is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if download is valid and can be used
     */
    public function isValid(): bool
    {
        return !$this->isExpired()
               && !$this->is_completed
               && is_null($this->failure_reason);
    }

    /**
     * Mark download as started
     */
    public function markAsStarted(string $ipAddress = null, string $userAgent = null): void
    {
        $this->update([
            'download_ip' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Mark download as completed
     */
    public function markAsCompleted(int $downloadSize = null, int $duration = null): void
    {
        $this->update([
            'downloaded_at' => now(),
            'download_size' => $downloadSize,
            'download_duration_seconds' => $duration,
            'is_completed' => true,
            'is_verified' => true,
        ]);
    }

    /**
     * Mark download as failed
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'failure_reason' => $reason,
            'is_completed' => false,
        ]);
    }

    /**
     * Get time remaining until expiration
     */
    public function getTimeRemainingAttribute(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        $diff = now()->diff($this->expires_at);

        if ($diff->h > 0) {
            return $diff->h . ' hours ' . $diff->i . ' minutes';
        }

        if ($diff->i > 0) {
            return $diff->i . ' minutes';
        }

        return $diff->s . ' seconds';
    }

    /**
     * Get formatted download size
     */
    public function getFormattedSizeAttribute(): ?string
    {
        if (!$this->download_size) {
            return null;
        }

        $bytes = $this->download_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get formatted download duration
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->download_duration_seconds) {
            return null;
        }

        $seconds = $this->download_duration_seconds;

        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 60) {
            return $minutes . ' minutes ' . $remainingSeconds . ' seconds';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours . ' hours ' . $remainingMinutes . ' minutes';
    }

    /**
     * Calculate download speed in MB/s
     */
    public function getDownloadSpeedAttribute(): ?float
    {
        if (!$this->download_size || !$this->download_duration_seconds || $this->download_duration_seconds === 0) {
            return null;
        }

        $mbSize = $this->download_size / (1024 * 1024);
        return round($mbSize / $this->download_duration_seconds, 2);
    }
}
