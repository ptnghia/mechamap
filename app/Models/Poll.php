<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
