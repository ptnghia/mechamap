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
 * @property-read string $folder_display
 * @property-read \App\Models\Thread $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark inFolder(?string $folder)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ThreadBookmark withNotes()
 * @mixin \Eloquent
 */
class ThreadBookmark extends Model
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
        'folder',
        'notes',
    ];

    /**
     * Get the thread this bookmark belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the user who created this bookmark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method để tự động cập nhật bookmark count của thread.
     */
    protected static function booted()
    {
        static::created(function ($bookmark) {
            $bookmark->thread->increment('bookmark_count');
        });

        static::deleted(function ($bookmark) {
            $bookmark->thread->decrement('bookmark_count');
        });
    }

    /**
     * Scope để lọc theo folder.
     */
    public function scopeInFolder($query, ?string $folder)
    {
        if ($folder === null) {
            return $query->whereNull('folder');
        }

        return $query->where('folder', $folder);
    }

    /**
     * Scope để lọc bookmarks có notes.
     */
    public function scopeWithNotes($query)
    {
        return $query->whereNotNull('notes');
    }

    /**
     * Get available bookmark folders for a user.
     */
    public static function getUserFolders(User $user): array
    {
        return static::where('user_id', $user->id)
            ->whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->toArray();
    }

    /**
     * Check if bookmark has notes.
     */
    public function hasNotes(): bool
    {
        return !empty($this->notes);
    }

    /**
     * Get formatted folder name.
     */
    public function getFolderDisplayAttribute(): string
    {
        return $this->folder ?? 'Uncategorized';
    }
}
