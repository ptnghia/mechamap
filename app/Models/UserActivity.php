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
 * @property string $activity_type
 * @property int|null $activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Comment|null $comment
 * @property-read \App\Models\Thread|null $thread
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereActivityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserActivity whereUserId($value)
 * @mixin \Eloquent
 */
class UserActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_id',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related thread if activity is thread-related.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'activity_id');
    }

    /**
     * Get the related comment if activity is comment-related.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'activity_id');
    }

    /**
     * Get the URL for this activity.
     */
    public function getUrl(): ?string
    {
        switch ($this->activity_type) {
            case 'thread_created':
            case 'thread_liked':
            case 'thread_saved':
            case 'thread_followed':
                $thread = $this->thread;
                return $thread ? route('threads.show', $thread) : null;

            case 'comment_created':
                $comment = $this->comment;
                if ($comment && $comment->thread) {
                    return route('threads.show', $comment->thread) . '#comment-' . $comment->id;
                }
                return null;

            case 'profile_updated':
                return route('profile.show', $this->user);

            default:
                return null;
        }
    }

    /**
     * Get the title for this activity.
     */
    public function getTitle(): string
    {
        switch ($this->activity_type) {
            case 'thread_created':
                $thread = $this->thread;
                return $thread ? $thread->title : 'Created a new thread';

            case 'comment_created':
                $comment = $this->comment;
                return $comment && $comment->thread ? 'Commented on: ' . $comment->thread->title : 'Commented on a thread';

            case 'thread_liked':
                $thread = $this->thread;
                return $thread ? 'Liked: ' . $thread->title : 'Liked a thread';

            case 'thread_saved':
                $thread = $this->thread;
                return $thread ? 'Saved: ' . $thread->title : 'Saved a thread';

            case 'thread_followed':
                $thread = $this->thread;
                return $thread ? 'Followed: ' . $thread->title : 'Followed a thread';

            case 'profile_updated':
                return 'Updated profile information';

            default:
                return $this->activity_type;
        }
    }
}
