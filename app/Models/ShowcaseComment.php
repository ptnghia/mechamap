<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShowcaseComment extends Model
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
        'parent_id',
        'comment',
    ];

    /**
     * Lấy showcase sở hữu comment này.
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * Lấy user tạo comment này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lấy comment cha (parent comment).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ShowcaseComment::class, 'parent_id');
    }

    /**
     * Lấy các comment con (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ShowcaseComment::class, 'parent_id');
    }
}
