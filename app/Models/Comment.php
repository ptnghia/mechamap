<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * 
 *
 * @property int $id
 * @property int $thread_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $content
 * @property bool $has_media
 * @property bool $has_code_snippet Comment có chứa code/formula/technical calculation không
 * @property bool $has_formula Comment có chứa công thức toán học/kỹ thuật không
 * @property string|null $formula_content Nội dung công thức (LaTeX format cho MathJax rendering)
 * @property int $like_count
 * @property-read int|null $dislikes_count
 * @property int $helpful_count Số lượt đánh giá "hữu ích" cho câu trả lời kỹ thuật
 * @property int $expert_endorsements Số lượng expert ủng hộ câu trả lời này
 * @property numeric $quality_score
 * @property numeric $technical_accuracy_score Điểm độ chính xác kỹ thuật (0.00 - 5.00) do expert đánh giá
 * @property string $verification_status Trạng thái xác minh: chưa xác minh, chờ xác minh, đã xác minh, có tranh cãi
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $verified_at Thời gian được verify
 * @property array<array-key, mixed>|null $technical_tags Tags kỹ thuật: ["calculation","design","material","manufacturing"]
 * @property string|null $answer_type Loại câu trả lời: tổng quát, tính toán, tham khảo, kinh nghiệm, hướng dẫn
 * @property bool $is_flagged
 * @property bool $is_spam
 * @property bool $is_solution
 * @property-read int|null $reports_count
 * @property \Illuminate\Support\Carbon|null $edited_at
 * @property int $edit_count
 * @property int|null $edited_by
 * @property string|null $edit_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentDislike> $dislikes
 * @property-read \App\Models\User|null $editor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $likes
 * @property-read int|null $likes_count
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read \App\Models\Thread $thread
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Comment byPopularity()
 * @method static Builder<static>|Comment edited()
 * @method static Builder<static>|Comment highQuality(float $minScore = '4')
 * @method static Builder<static>|Comment newModelQuery()
 * @method static Builder<static>|Comment newQuery()
 * @method static Builder<static>|Comment notFlagged()
 * @method static Builder<static>|Comment notSpam()
 * @method static Builder<static>|Comment onlyTrashed()
 * @method static Builder<static>|Comment query()
 * @method static Builder<static>|Comment solution()
 * @method static Builder<static>|Comment whereAnswerType($value)
 * @method static Builder<static>|Comment whereContent($value)
 * @method static Builder<static>|Comment whereCreatedAt($value)
 * @method static Builder<static>|Comment whereDeletedAt($value)
 * @method static Builder<static>|Comment whereDislikesCount($value)
 * @method static Builder<static>|Comment whereEditCount($value)
 * @method static Builder<static>|Comment whereEditReason($value)
 * @method static Builder<static>|Comment whereEditedAt($value)
 * @method static Builder<static>|Comment whereEditedBy($value)
 * @method static Builder<static>|Comment whereExpertEndorsements($value)
 * @method static Builder<static>|Comment whereFormulaContent($value)
 * @method static Builder<static>|Comment whereHasCodeSnippet($value)
 * @method static Builder<static>|Comment whereHasFormula($value)
 * @method static Builder<static>|Comment whereHasMedia($value)
 * @method static Builder<static>|Comment whereHelpfulCount($value)
 * @method static Builder<static>|Comment whereId($value)
 * @method static Builder<static>|Comment whereIsFlagged($value)
 * @method static Builder<static>|Comment whereIsSolution($value)
 * @method static Builder<static>|Comment whereIsSpam($value)
 * @method static Builder<static>|Comment whereLikeCount($value)
 * @method static Builder<static>|Comment whereParentId($value)
 * @method static Builder<static>|Comment whereQualityScore($value)
 * @method static Builder<static>|Comment whereReportsCount($value)
 * @method static Builder<static>|Comment whereTechnicalAccuracyScore($value)
 * @method static Builder<static>|Comment whereTechnicalTags($value)
 * @method static Builder<static>|Comment whereThreadId($value)
 * @method static Builder<static>|Comment whereUpdatedAt($value)
 * @method static Builder<static>|Comment whereUserId($value)
 * @method static Builder<static>|Comment whereVerificationStatus($value)
 * @method static Builder<static>|Comment whereVerifiedAt($value)
 * @method static Builder<static>|Comment whereVerifiedBy($value)
 * @method static Builder<static>|Comment withTrashed()
 * @method static Builder<static>|Comment withoutTrashed()
 * @mixin \Eloquent
 */
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'content',
        'like_count',
        'has_media',
        // Edit tracking
        'edited_by',
        'edit_reason',
        'edit_count',
        // Moderation states
        'is_flagged',
        'is_spam',
        'is_solution',
        'reports_count',
        // Enhanced interactions
        'dislikes_count',
        'quality_score',
        // MechaMap technical discussion fields
        'has_code_snippet',
        'has_formula',
        'formula_content',
        'technical_accuracy_score',
        'verification_status',
        'verified_by',
        'verified_at',
        'technical_tags',
        'answer_type',
        'helpful_count',
        'expert_endorsements',
        // Missing timestamp field
        'edited_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_flagged' => 'boolean',
        'is_spam' => 'boolean',
        'is_solution' => 'boolean',
        'reports_count' => 'integer',
        'like_count' => 'integer',
        'dislikes_count' => 'integer',
        'quality_score' => 'decimal:2',
        // MechaMap technical discussion casts
        'has_code_snippet' => 'boolean',
        'has_formula' => 'boolean',
        'technical_accuracy_score' => 'decimal:2',
        'verified_at' => 'datetime',
        'technical_tags' => 'array',
        'helpful_count' => 'integer',
        'expert_endorsements' => 'integer',
        'is_spam' => 'boolean',
        'is_solution' => 'boolean',
        'has_media' => 'boolean',
        'like_count' => 'integer',
        'dislikes_count' => 'integer',
        'reports_count' => 'integer',
        'edit_count' => 'integer',
        'quality_score' => 'decimal:2',
    ];

    /**
     * Get the thread that owns the comment.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the likes for the comment.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Check if the comment is liked by the given user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get all media attached to this comment (polymorphic).
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the reports for the comment.
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // =================
    // NEW RELATIONSHIPS FOR ENHANCED STATES
    // =================

    /**
     * Get the user who edited this comment.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get the dislikes for the comment.
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(CommentDislike::class);
    }

    // =================
    // ENHANCED SCOPES
    // =================

    /**
     * Scope để lọc comments là solution.
     */
    public function scopeSolution(Builder $query): Builder
    {
        return $query->where('is_solution', true);
    }

    /**
     * Scope để lọc comments không bị flagged.
     */
    public function scopeNotFlagged(Builder $query): Builder
    {
        return $query->where('is_flagged', false);
    }

    /**
     * Scope để lọc comments không phải spam.
     */
    public function scopeNotSpam(Builder $query): Builder
    {
        return $query->where('is_spam', false);
    }

    /**
     * Scope để lọc comments đã được chỉnh sửa.
     */
    public function scopeEdited(Builder $query): Builder
    {
        return $query->whereNotNull('edited_at');
    }

    /**
     * Scope để lọc comments có quality score cao.
     */
    public function scopeHighQuality(Builder $query, float $minScore = 4.0): Builder
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * Scope để sắp xếp theo số like cao nhất.
     */
    public function scopeByPopularity(Builder $query): Builder
    {
        return $query->orderBy('like_count', 'desc');
    }

    // =================
    // ENHANCED METHODS
    // =================

    /**
     * Đánh dấu comment là solution.
     */
    public function markAsSolution(): bool
    {
        // Unmark other solutions in the same thread
        static::where('thread_id', $this->thread_id)
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        // Mark this comment as solution
        $result = $this->update(['is_solution' => true]);

        // Update thread as solved
        if ($result) {
            $this->thread->markAsSolved($this, $this->user);
        }

        return $result;
    }

    /**
     * Bỏ đánh dấu comment là solution.
     */
    public function unmarkSolution(): bool
    {
        $result = $this->update(['is_solution' => false]);

        // Update thread as unsolved if this was the solution
        if ($result && $this->thread->solution_comment_id === $this->id) {
            $this->thread->unmarkSolved();
        }

        return $result;
    }

    /**
     * Flag comment.
     */
    public function flag(): bool
    {
        return $this->update([
            'is_flagged' => true,
            'reports_count' => $this->reports_count + 1,
        ]);
    }

    /**
     * Unflag comment.
     */
    public function unflag(): bool
    {
        return $this->update([
            'is_flagged' => false,
        ]);
    }

    /**
     * Đánh dấu comment là spam.
     */
    public function markAsSpam(): bool
    {
        return $this->update(['is_spam' => true]);
    }

    /**
     * Bỏ đánh dấu comment là spam.
     */
    public function unmarkSpam(): bool
    {
        return $this->update(['is_spam' => false]);
    }

    /**
     * Ghi nhận việc chỉnh sửa comment.
     */
    public function recordEdit(User $editor, string $reason = null): bool
    {
        return $this->update([
            'edited_at' => now(),
            'edited_by' => $editor->id,
            'edit_reason' => $reason,
            'edit_count' => $this->edit_count + 1,
        ]);
    }

    /**
     * Kiểm tra comment có bị flagged không.
     */
    public function isFlagged(): bool
    {
        return $this->is_flagged;
    }

    /**
     * Kiểm tra comment có phải spam không.
     */
    public function isSpam(): bool
    {
        return $this->is_spam;
    }

    /**
     * Kiểm tra comment có phải solution không.
     */
    public function isSolution(): bool
    {
        return $this->is_solution;
    }

    /**
     * Kiểm tra comment đã được chỉnh sửa chưa.
     */
    public function isEdited(): bool
    {
        return !is_null($this->edited_at);
    }

    /**
     * Kiểm tra comment có dislike bởi user không.
     */
    public function isDislikedBy(User $user): bool
    {
        return $this->dislikes()->where('user_id', $user->id)->exists();
    }

    /**
     * Lấy net score (likes - dislikes).
     */
    public function getNetScore(): int
    {
        return $this->like_count - $this->dislikes_count;
    }

    /**
     * Cập nhật thread activity khi có comment mới.
     */
    protected static function booted()
    {
        static::created(function ($comment) {
            // Cập nhật last activity của thread
            $comment->thread->update([
                'last_activity_at' => now(),
                'last_comment_at' => now(),
                'last_comment_by' => $comment->user_id,
            ]);

            // Cập nhật cached counters
            $comment->thread->updateCachedCounters();
        });

        static::deleted(function ($comment) {
            // Cập nhật cached counters khi xóa comment
            $comment->thread->updateCachedCounters();
        });
    }
}
