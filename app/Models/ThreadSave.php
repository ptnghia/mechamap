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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadSave whereUserId($value)
 * @mixin \Eloquent
 */
class ThreadSave extends Model
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
     * Get the thread that is saved.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the user that saved the thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
