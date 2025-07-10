<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'achievement_id',
        'unlocked_at',
        'progress_data',
        'current_progress',
        'target_progress',
        'is_notified',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'progress_data' => 'array',
        'current_progress' => 'integer',
        'target_progress' => 'integer',
        'is_notified' => 'boolean',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the achievement
     */
    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific achievement
     */
    public function scopeForAchievement($query, int $achievementId)
    {
        return $query->where('achievement_id', $achievementId);
    }

    /**
     * Scope for notified achievements
     */
    public function scopeNotified($query)
    {
        return $query->where('is_notified', true);
    }

    /**
     * Scope for unnotified achievements
     */
    public function scopeUnnotified($query)
    {
        return $query->where('is_notified', false);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_progress <= 0) {
            return 100;
        }

        return min(100, ($this->current_progress / $this->target_progress) * 100);
    }

    /**
     * Check if achievement is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->current_progress >= $this->target_progress;
    }

    /**
     * Mark as notified
     */
    public function markAsNotified(): bool
    {
        return $this->update(['is_notified' => true]);
    }

    /**
     * Update progress
     */
    public function updateProgress(int $currentProgress, array $progressData = []): bool
    {
        return $this->update([
            'current_progress' => $currentProgress,
            'progress_data' => array_merge($this->progress_data ?? [], $progressData),
        ]);
    }
}
