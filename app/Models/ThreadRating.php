<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadRating extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'rating',
        'review',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Validation rules for rating.
     */
    public static function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get the thread this rating belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the user who created this rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method để tự động cập nhật average rating của thread.
     */
    protected static function booted()
    {
        static::created(function ($rating) {
            $rating->thread->recalculateRatings();
        });

        static::updated(function ($rating) {
            $rating->thread->recalculateRatings();
        });

        static::deleted(function ($rating) {
            $rating->thread->recalculateRatings();
        });
    }

    /**
     * Scope để lọc theo rating level.
     */
    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope để lọc rating có review.
     */
    public function scopeWithReview($query)
    {
        return $query->whereNotNull('review');
    }

    /**
     * Get rating as stars display.
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Check if this is a positive rating (4-5 stars).
     */
    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    /**
     * Check if this is a negative rating (1-2 stars).
     */
    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }
}
