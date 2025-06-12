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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentLike whereUserId($value)
 * @mixin \Eloquent
 */
class CommentLike extends Model
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
     * Get the comment that owns the like.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user that owns the like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
