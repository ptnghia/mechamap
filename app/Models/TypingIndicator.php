<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TypingIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'context_type',
        'context_id',
        'typing_type',
        'started_at',
        'last_activity_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Typing types
    const TYPE_COMMENT = 'comment';
    const TYPE_REPLY = 'reply';
    const TYPE_MESSAGE = 'message';
    const TYPE_THREAD = 'thread';

    // Context types
    const CONTEXT_THREAD = 'thread';
    const CONTEXT_COMMENT = 'comment';
    const CONTEXT_MESSAGE = 'message';
    const CONTEXT_SHOWCASE = 'showcase';

    // Default expiration time (in seconds)
    const DEFAULT_EXPIRATION = 30;

    /**
     * Get the user who is typing
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active indicators (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope for specific context
     */
    public function scopeForContext($query, string $contextType, int $contextId)
    {
        return $query->where('context_type', $contextType)
                    ->where('context_id', $contextId);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific typing type
     */
    public function scopeForTypingType($query, string $typingType)
    {
        return $query->where('typing_type', $typingType);
    }

    /**
     * Check if indicator is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Check if indicator is still active
     */
    public function isActive(): bool
    {
        return !$this->isExpired();
    }

    /**
     * Update last activity and extend expiration
     */
    public function updateActivity(int $extensionSeconds = null): bool
    {
        $extensionSeconds = $extensionSeconds ?? self::DEFAULT_EXPIRATION;

        return $this->update([
            'last_activity_at' => now(),
            'expires_at' => now()->addSeconds($extensionSeconds),
        ]);
    }

    /**
     * Get time remaining until expiration
     */
    public function getTimeRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) $this->expires_at->diffInSeconds(now());
    }

    /**
     * Get typing duration
     */
    public function getTypingDurationAttribute(): int
    {
        return $this->started_at->diffInSeconds($this->last_activity_at);
    }

    /**
     * Create or update typing indicator
     */
    public static function startTyping(
        int $userId,
        string $contextType,
        int $contextId,
        string $typingType = self::TYPE_COMMENT,
        array $metadata = []
    ): self {
        $expiresAt = now()->addSeconds(self::DEFAULT_EXPIRATION);

        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'typing_type' => $typingType,
            ],
            [
                'started_at' => now(),
                'last_activity_at' => now(),
                'expires_at' => $expiresAt,
                'metadata' => $metadata,
            ]
        );
    }

    /**
     * Stop typing indicator
     */
    public static function stopTyping(
        int $userId,
        string $contextType,
        int $contextId,
        string $typingType = self::TYPE_COMMENT
    ): bool {
        return static::where('user_id', $userId)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->where('typing_type', $typingType)
            ->delete() > 0;
    }

    /**
     * Get active typing indicators for context
     */
    public static function getActiveForContext(
        string $contextType,
        int $contextId,
        string $typingType = null
    ): \Illuminate\Database\Eloquent\Collection {
        $query = static::active()
            ->forContext($contextType, $contextId)
            ->with(['user:id,name,avatar']);

        if ($typingType) {
            $query->forTypingType($typingType);
        }

        return $query->orderBy('started_at', 'desc')->get();
    }

    /**
     * Clean up expired indicators
     */
    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<=', now())->delete();
    }

    /**
     * Get typing statistics
     */
    public static function getTypingStatistics(): array
    {
        return [
            'total_active' => static::active()->count(),
            'by_context_type' => static::active()
                ->selectRaw('context_type, COUNT(*) as count')
                ->groupBy('context_type')
                ->pluck('count', 'context_type')
                ->toArray(),
            'by_typing_type' => static::active()
                ->selectRaw('typing_type, COUNT(*) as count')
                ->groupBy('typing_type')
                ->pluck('count', 'typing_type')
                ->toArray(),
            'average_duration' => static::active()
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, last_activity_at)) as avg_duration')
                ->value('avg_duration') ?? 0,
        ];
    }

    /**
     * Get user's current typing contexts
     */
    public static function getUserTypingContexts(int $userId): array
    {
        return static::active()
            ->forUser($userId)
            ->select('context_type', 'context_id', 'typing_type', 'started_at', 'expires_at')
            ->get()
            ->map(function ($indicator) {
                return [
                    'context_type' => $indicator->context_type,
                    'context_id' => $indicator->context_id,
                    'typing_type' => $indicator->typing_type,
                    'started_at' => $indicator->started_at,
                    'expires_at' => $indicator->expires_at,
                    'time_remaining' => $indicator->time_remaining,
                ];
            })
            ->toArray();
    }
}
