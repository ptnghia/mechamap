<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type',
        'reason',
        'description',
        'status',
    ];

    /**
     * Get the reportable model.
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who reported.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
