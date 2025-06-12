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
 * @property int $thread_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Thread $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadLike whereUserId($value)
 * @mixin \Eloquent
 */
class ThreadLike extends Model
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
    ];

    /**
     * Get the thread that owns the like.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the user that owns the like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
