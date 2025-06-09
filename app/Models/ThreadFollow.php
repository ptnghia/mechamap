<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
