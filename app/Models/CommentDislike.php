<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $comment_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentDislike whereUserId($value)
 * @mixin \Eloquent
 */
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
