<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int $profile_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfilePost whereUserId($value)
 * @mixin \Eloquent
 */
class ProfilePost extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'profile_id',
    ];

    /**
     * Get the user that created the profile post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user whose profile the post is on.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profile_id');
    }

    /**
     * Get the reactions for the profile post.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }
}
