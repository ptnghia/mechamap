<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $poll_id
 * @property int $poll_option_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PollOption $option
 * @property-read \App\Models\Poll $poll
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote wherePollOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereUserId($value)
 * @mixin \Eloquent
 */
class PollVote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
    ];

    /**
     * Get the poll that owns the vote.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the option that owns the vote.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    /**
     * Get the user that owns the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
