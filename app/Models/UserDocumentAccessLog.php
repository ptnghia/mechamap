<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 📊 User Document Access Log Model
 * 
 * Tracks access to verification documents for security and audit purposes
 */
class UserDocumentAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'accessed_by',
        'access_type',
        'ip_address',
        'user_agent',
        'access_metadata',
        'accessed_at',
    ];

    protected $casts = [
        'access_metadata' => 'array',
        'accessed_at' => 'datetime',
    ];

    /**
     * Access types
     */
    public const ACCESS_TYPES = [
        'view' => 'Xem',
        'download' => 'Tải xuống',
        'verify' => 'Xác minh',
        'reject' => 'Từ chối',
    ];

    /**
     * Get the document that was accessed
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(UserVerificationDocument::class, 'document_id');
    }

    /**
     * Get the user who accessed the document
     */
    public function accessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accessed_by');
    }

    /**
     * Scope: Get logs by access type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('access_type', $type);
    }

    /**
     * Scope: Get recent access logs
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('accessed_at', '>=', now()->subDays($days));
    }

    /**
     * Get access type label
     */
    public function getAccessTypeLabel(): string
    {
        return self::ACCESS_TYPES[$this->access_type] ?? $this->access_type;
    }

    /**
     * Get browser information from user agent
     */
    public function getBrowserInfo(): array
    {
        if (!$this->user_agent) {
            return ['browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        // Simple user agent parsing
        $browser = 'Unknown';
        $platform = 'Unknown';

        if (strpos($this->user_agent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($this->user_agent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($this->user_agent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($this->user_agent, 'Edge') !== false) {
            $browser = 'Edge';
        }

        if (strpos($this->user_agent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (strpos($this->user_agent, 'Mac') !== false) {
            $platform = 'macOS';
        } elseif (strpos($this->user_agent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($this->user_agent, 'Android') !== false) {
            $platform = 'Android';
        } elseif (strpos($this->user_agent, 'iOS') !== false) {
            $platform = 'iOS';
        }

        return ['browser' => $browser, 'platform' => $platform];
    }

    /**
     * Log document access
     */
    public static function logAccess(
        UserVerificationDocument $document,
        User $user,
        string $accessType,
        array $metadata = []
    ): self {
        return self::create([
            'document_id' => $document->id,
            'accessed_by' => $user->id,
            'access_type' => $accessType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'access_metadata' => $metadata,
            'accessed_at' => now(),
        ]);
    }
}
