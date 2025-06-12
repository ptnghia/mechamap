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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadFollow whereUserId($value)
 * @mixin \Eloquent
 */
class ThreadFollow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'thread_id',
    ];

    /**
     * Boot method để tự động cập nhật follow count của thread.
     */
    protected static function booted()
    {
        static::created(function ($follow) {
            $follow->thread->increment('follow_count');
        });

        static::deleted(function ($follow) {
            $follow->thread->decrement('follow_count');
        });
    }

    /**
     * Get the user that follows the thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the thread that is being followed.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
