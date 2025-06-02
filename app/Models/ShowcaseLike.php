<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
