<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'notification_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'notification_id',
        'type',
        'channel',
        'notifiable_type',
        'notifiable_id',
        'status',
        'data',
        'error_message',
        'sent_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the notification that owns the log
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * Get the notifiable entity (usually User)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for specific status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for specific channel
     */
    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for specific type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for sent logs
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending logs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Log notification delivery
     */
    public static function logDelivery(
        ?int $notificationId,
        string $type,
        string $channel,
        string $notifiableType,
        int $notifiableId,
        string $status,
        array $data = [],
        ?string $errorMessage = null
    ): self {
        return static::create([
            'notification_id' => $notificationId,
            'type' => $type,
            'channel' => $channel,
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
            'status' => $status,
            'data' => $data,
            'error_message' => $errorMessage,
            'sent_at' => $status === 'sent' ? now() : null,
        ]);
    }

    /**
     * Get delivery statistics
     */
    public static function getDeliveryStats(array $filters = []): array
    {
        $query = static::query();

        // Apply filters
        if (isset($filters['type'])) {
            $query->forType($filters['type']);
        }
        if (isset($filters['channel'])) {
            $query->forChannel($filters['channel']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $stats = $query->selectRaw('
            status,
            channel,
            COUNT(*) as count,
            COUNT(CASE WHEN status = "sent" THEN 1 END) as sent_count,
            COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_count,
            COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count
        ')
        ->groupBy('status', 'channel')
        ->get();

        return $stats->toArray();
    }

    /**
     * Get success rate for a channel/type
     */
    public static function getSuccessRate(string $channel = null, string $type = null): float
    {
        $query = static::query();

        if ($channel) {
            $query->forChannel($channel);
        }
        if ($type) {
            $query->forType($type);
        }

        $total = $query->count();
        if ($total === 0) {
            return 0.0;
        }

        $sent = $query->sent()->count();
        return round(($sent / $total) * 100, 2);
    }

    /**
     * Get failed deliveries for retry
     */
    public static function getFailedForRetry(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return static::failed()
            ->where('created_at', '>', now()->subHours(24)) // Only retry within 24 hours
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark as retried
     */
    public function markAsRetried(): void
    {
        $this->update([
            'status' => 'retried',
            'updated_at' => now(),
        ]);
    }

    /**
     * Get recent activity
     */
    public static function getRecentActivity(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['notification'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
