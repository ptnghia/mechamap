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
 * @property int $poll_id
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $vote_count
 * @property-read float $vote_percentage
 * @property-read \App\Models\Poll $poll
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollVote> $votes
 * @property-read int|null $votes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PollOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'text',
    ];

    /**
     * Get the poll that owns the option.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the votes for the option.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * Get the vote count for this option.
     */
    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get the vote percentage for this option.
     */
    public function getVotePercentageAttribute(): float
    {
        $totalVotes = $this->poll->total_votes;
        if ($totalVotes === 0) {
            return 0;
        }
        
        return round(($this->vote_count / $totalVotes) * 100, 1);
    }
}
