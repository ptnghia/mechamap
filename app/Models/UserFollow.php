<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'follower_id',
        'following_id',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    /**
     * Get the follower user
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Get the following user
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    /**
     * Scope for specific follower
     */
    public function scopeForFollower($query, $userId)
    {
        return $query->where('follower_id', $userId);
    }

    /**
     * Scope for specific following
     */
    public function scopeForFollowing($query, $userId)
    {
        return $query->where('following_id', $userId);
    }

    /**
     * Check if user is following another user
     */
    public static function isFollowing(int $followerId, int $followingId): bool
    {
        return static::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->exists();
    }

    /**
     * Get followers count for user
     */
    public static function getFollowersCount(int $userId): int
    {
        return static::where('following_id', $userId)->count();
    }

    /**
     * Get following count for user
     */
    public static function getFollowingCount(int $userId): int
    {
        return static::where('follower_id', $userId)->count();
    }
}
