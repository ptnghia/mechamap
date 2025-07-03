<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $showcase_id
 * @property int $user_id
 * @property int $technical_quality
 * @property int $innovation
 * @property int $usefulness
 * @property int $documentation
 * @property float $overall_rating
 * @property string|null $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Showcase $showcase
 * @property-read \App\Models\User $user
 */
class ShowcaseRating extends Model
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
        'technical_quality',
        'innovation',
        'usefulness',
        'documentation',
        'overall_rating',
        'review',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'technical_quality' => 'integer',
        'innovation' => 'integer',
        'usefulness' => 'integer',
        'documentation' => 'integer',
        'overall_rating' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate overall rating before saving
        static::saving(function ($rating) {
            $rating->overall_rating = ($rating->technical_quality + 
                                    $rating->innovation + 
                                    $rating->usefulness + 
                                    $rating->documentation) / 4;
        });
    }

    /**
     * Get the showcase that owns the rating.
     */
    public function showcase(): BelongsTo
    {
        return $this->belongsTo(Showcase::class);
    }

    /**
     * Get the user that created the rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get rating categories as array.
     */
    public function getCategoriesAttribute(): array
    {
        return [
            'technical_quality' => $this->technical_quality,
            'innovation' => $this->innovation,
            'usefulness' => $this->usefulness,
            'documentation' => $this->documentation,
        ];
    }

    /**
     * Get category names in Vietnamese.
     */
    public static function getCategoryNames(): array
    {
        return [
            'technical_quality' => 'Chất lượng kỹ thuật',
            'innovation' => 'Tính sáng tạo',
            'usefulness' => 'Tính hữu ích',
            'documentation' => 'Chất lượng tài liệu',
        ];
    }
}
