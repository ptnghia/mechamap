<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $thread_id
 * @property string $question
 * @property int $max_options
 * @property bool $allow_change_vote
 * @property bool $show_votes_publicly
 * @property bool $allow_view_without_vote
 * @property \Illuminate\Support\Carbon|null $close_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $total_votes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollOption> $options
 * @property-read int|null $options_count
 * @property-read \App\Models\Thread $thread
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollVote> $votes
 * @property-read int|null $votes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAllowChangeVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAllowViewWithoutVote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereCloseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereMaxOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereShowVotesPublicly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Poll extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'thread_id',
        'question',
        'max_options',
        'allow_change_vote',
        'show_votes_publicly',
        'allow_view_without_vote',
        'close_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allow_change_vote' => 'boolean',
        'show_votes_publicly' => 'boolean',
        'allow_view_without_vote' => 'boolean',
        'close_at' => 'datetime',
    ];

    /**
     * Get the thread that owns the poll.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the options for the poll.
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Get the votes for the poll.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Check if the poll is closed.
     */
    public function isClosed(): bool
    {
        return $this->close_at !== null && now()->greaterThan($this->close_at);
    }

    /**
     * Check if the user has voted in this poll.
     */
    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the total number of votes.
     */
    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }
}
