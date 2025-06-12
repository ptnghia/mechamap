<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $visitable_id
 * @property string $visitable_type
 * @property \Illuminate\Support\Carbon $last_visit_at
 * @property-read \App\Models\User $user
 * @property-read Model|\Eloquent $visitable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit whereLastVisitAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit whereVisitableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserVisit whereVisitableType($value)
 * @mixin \Eloquent
 */
class UserVisit extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'visitable_id',
        'visitable_type',
        'last_visit_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_visit_at' => 'datetime',
    ];

    /**
     * Get the user that visited.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent visitable model.
     */
    public function visitable(): MorphTo
    {
        return $this->morphTo();
    }
}
