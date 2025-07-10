<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_fingerprint',
        'device_name',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'platform_version',
        'user_agent',
        'ip_address',
        'country',
        'city',
        'is_trusted',
        'first_seen_at',
        'last_seen_at',
        'trusted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_trusted' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'trusted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if device is new (first time seen)
     */
    public function isNew(): bool
    {
        return $this->first_seen_at && $this->first_seen_at->diffInMinutes(now()) < 5;
    }

    /**
     * Mark device as trusted
     */
    public function markAsTrusted(): void
    {
        $this->update([
            'is_trusted' => true,
            'trusted_at' => now(),
        ]);
    }

    /**
     * Get device display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->device_name) {
            return $this->device_name;
        }

        $parts = array_filter([
            $this->browser,
            $this->platform,
            $this->device_type ? ucfirst($this->device_type) : null,
        ]);

        return implode(' on ', $parts) ?: 'Unknown Device';
    }

    /**
     * Get location string
     */
    public function getLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->country]);
        return implode(', ', $parts) ?: 'Unknown Location';
    }
}
