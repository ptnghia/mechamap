<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentDislike extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment_id',
        'user_id',
    ];

    /**
     * Get the comment that owns the dislike.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user that owns the dislike.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::created(function ($dislike) {
            // Tăng dislikes_count của comment
            $dislike->comment->increment('dislikes_count');
        });

        static::deleted(function ($dislike) {
            // Giảm dislikes_count của comment
            $dislike->comment->decrement('dislikes_count');
        });
    }
}
