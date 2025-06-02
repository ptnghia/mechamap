<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Showcase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'showcaseable_id',
        'showcaseable_type',
        'description',
        'order',
    ];

    /**
     * Get the user that owns the showcase item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent showcaseable model.
     */
    public function showcaseable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Lấy tất cả comments của showcase này.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ShowcaseComment::class);
    }

    /**
     * Lấy tất cả media liên quan đến showcase này.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Lấy featured image của showcase.
     */
    public function getFeaturedImageAttribute(): ?string
    {
        $featuredMedia = $this->media()
            ->where('file_name', 'like', '%[Featured]%')
            ->first();

        return $featuredMedia ? $featuredMedia->url : null;
    }

    /**
     * Lấy tất cả likes của showcase này.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ShowcaseLike::class);
    }

    /**
     * Lấy tất cả follows của user sở hữu showcase này.
     */
    public function follows(): HasMany
    {
        return $this->hasMany(ShowcaseFollow::class, 'following_id', 'user_id');
    }

    /**
     * Kiểm tra user hiện tại đã like showcase này chưa.
     */
    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Kiểm tra user hiện tại đã follow showcase owner này chưa.
     */
    public function isFollowedBy($userId): bool
    {
        return ShowcaseFollow::where('follower_id', $userId)
            ->where('following_id', $this->user_id)
            ->exists();
    }

    /**
     * Đếm số lượng likes.
     */
    public function likesCount(): int
    {
        return $this->likes()->count();
    }

    /**
     * Đếm số lượng người theo dõi showcase owner.
     */
    public function followsCount(): int
    {
        return ShowcaseFollow::where('following_id', $this->user_id)->count();
    }

    /**
     * Đếm số lượng comments.
     */
    public function commentsCount(): int
    {
        return $this->comments()->count();
    }
}
