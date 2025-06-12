<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $follower_id
 * @property int $following_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $follower
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseFollow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShowcaseFollow extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'showcase_follows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * Lấy user thực hiện follow (người theo dõi).
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Lấy user được follow (người được theo dõi).
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
