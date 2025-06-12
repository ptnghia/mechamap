<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property string $faq_type
 * @property string|null $related_topics
 * @property string|null $applicable_standards
 * @property string|null $code_example
 * @property string $difficulty_level
 * @property int $category_id
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $created_by
 * @property int|null $reviewed_by
 * @property int $helpful_votes
 * @property int $view_count
 * @property string|null $last_updated
 * @property-read \App\Models\FaqCategory $category
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereApplicableStandards($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCodeExample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereDifficultyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereFaqType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereHelpfulVotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereRelatedTopics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Faq whereViewCount($value)
 * @mixin \Eloquent
 */
class Faq extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'answer',
        'category_id',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the category of the FAQ.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
