<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $showcase_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Showcase $showcase
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike whereShowcaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ShowcaseLike whereUserId($value)
 * @mixin \Eloquent
 */
class ShowcaseLike extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'showcase_id',
        'user_id',
    ];

    /**
     * Lấy showcase được like.
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * Lấy user thực hiện like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
