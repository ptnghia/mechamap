<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $featured_image
 * @property string|null $meta_description
 * @property array<array-key, mixed>|null $search_keywords
 * @property int|null $read_time
 * @property string|null $status
 * @property int $user_id
 * @property int $category_id
 * @property bool $is_sticky
 * @property bool $is_locked
 * @property bool $is_featured
 * @property bool $is_solved
 * @property int|null $solution_comment_id
 * @property \Illuminate\Support\Carbon|null $solved_at
 * @property int|null $solved_by
 * @property numeric $quality_score
 * @property numeric $average_rating
 * @property-read int|null $ratings_count
 * @property string $thread_type
 * @property string|null $technical_difficulty Cấp độ kỹ thuật của chủ đề (beginner=sinh viên, expert=kỹ sư senior)
 * @property string|null $project_type Loại dự án/vấn đề: thiết kế, sản xuất, phân tích, xử lý sự cố
 * @property array<array-key, mixed>|null $software_used Phần mềm sử dụng: ["AutoCAD","SolidWorks","ANSYS","CATIA","Fusion360"]
 * @property string|null $industry_sector Lĩnh vực công nghiệp: ô tô, hàng không, sản xuất, năng lượng
 * @property array<array-key, mixed>|null $technical_specs Thông số kỹ thuật: {"material":"Steel","tolerance":"±0.01","pressure":"10MPa"}
 * @property int $requires_calculations Thread yêu cầu tính toán kỹ thuật (FEA, stress analysis, thermal)
 * @property int $has_drawings Thread có kèm bản vẽ kỹ thuật (DWG, PDF, STEP)
 * @property string $urgency_level Mức độ khẩn cấp: low=học tập, critical=sự cố sản xuất
 * @property string|null $standards_compliance Tiêu chuẩn áp dụng: ["ASME","ISO","ASTM","JIS","DIN"]
 * @property int $requires_pe_review Yêu cầu review từ Professional Engineer (PE license)
 * @property int $has_cad_files Thread có file CAD đính kèm
 * @property int $attachment_count Số lượng file đính kèm
 * @property int $views
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadLike> $likes
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadBookmark> $bookmarks
 * @property int $shares
 * @property int $replies
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property string|null $bumped_at
 * @property int $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $attachments
 * @property-read int|null $attachments_count
 * @property-read int|null $bookmarks_count
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\User|null $flagger
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadFollow> $follows
 * @property-read int|null $follows_count
 * @property-read int $participant_count
 * @property-read \App\Models\User|null $lastCommenter
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $participants
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Poll> $poll
 * @property-read int|null $poll_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadRating> $ratings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadSave> $saves
 * @property-read int|null $saves_count
 * @property-read \App\Models\Showcase|null $showcase
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Showcase> $showcases
 * @property-read int|null $showcases_count
 * @property-read \App\Models\Comment|null $solutionComment
 * @property-read \App\Models\User|null $solver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Thread archived()
 * @method static Builder<static>|Thread bookmarkedBy($userId)
 * @method static Builder<static>|Thread byPopularity()
 * @method static Builder<static>|Thread byRecentActivity()
 * @method static Builder<static>|Thread fromPeriod(string $period)
 * @method static Builder<static>|Thread hidden()
 * @method static Builder<static>|Thread highQuality(float $minScore = '4')
 * @method static Builder<static>|Thread minRating(float $minRating)
 * @method static Builder<static>|Thread moderationStatus(string $status)
 * @method static Builder<static>|Thread newModelQuery()
 * @method static Builder<static>|Thread newQuery()
 * @method static Builder<static>|Thread notArchived()
 * @method static Builder<static>|Thread notFlagged()
 * @method static Builder<static>|Thread notSpam()
 * @method static Builder<static>|Thread ofType(string $type)
 * @method static Builder<static>|Thread onlyTrashed()
 * @method static Builder<static>|Thread popularBookmarks(int $minBookmarks = 5)
 * @method static Builder<static>|Thread publicVisible()
 * @method static Builder<static>|Thread query()
 * @method static Builder<static>|Thread ratedBy($userId)
 * @method static Builder<static>|Thread search(string $searchTerm)
 * @method static Builder<static>|Thread solved()
 * @method static Builder<static>|Thread trending()
 * @method static Builder<static>|Thread unsolved()
 * @method static Builder<static>|Thread visible()
 * @method static Builder<static>|Thread whereAttachmentCount($value)
 * @method static Builder<static>|Thread whereAverageRating($value)
 * @method static Builder<static>|Thread whereBookmarks($value)
 * @method static Builder<static>|Thread whereBumpedAt($value)
 * @method static Builder<static>|Thread whereCategoryId($value)
 * @method static Builder<static>|Thread whereContent($value)
 * @method static Builder<static>|Thread whereCreatedAt($value)
 * @method static Builder<static>|Thread whereFeaturedImage($value)
 * @method static Builder<static>|Thread whereHasCadFiles($value)
 * @method static Builder<static>|Thread whereHasDrawings($value)
 * @method static Builder<static>|Thread whereId($value)
 * @method static Builder<static>|Thread whereIndustrySector($value)
 * @method static Builder<static>|Thread whereIsFeatured($value)
 * @method static Builder<static>|Thread whereIsLocked($value)
 * @method static Builder<static>|Thread whereIsSolved($value)
 * @method static Builder<static>|Thread whereIsSticky($value)
 * @method static Builder<static>|Thread whereLastActivityAt($value)
 * @method static Builder<static>|Thread whereLikes($value)
 * @method static Builder<static>|Thread whereMetaDescription($value)
 * @method static Builder<static>|Thread wherePriority($value)
 * @method static Builder<static>|Thread whereProjectType($value)
 * @method static Builder<static>|Thread whereQualityScore($value)
 * @method static Builder<static>|Thread whereRatingsCount($value)
 * @method static Builder<static>|Thread whereReadTime($value)
 * @method static Builder<static>|Thread whereReplies($value)
 * @method static Builder<static>|Thread whereRequiresCalculations($value)
 * @method static Builder<static>|Thread whereRequiresPeReview($value)
 * @method static Builder<static>|Thread whereSearchKeywords($value)
 * @method static Builder<static>|Thread whereShares($value)
 * @method static Builder<static>|Thread whereSlug($value)
 * @method static Builder<static>|Thread whereSoftwareUsed($value)
 * @method static Builder<static>|Thread whereSolutionCommentId($value)
 * @method static Builder<static>|Thread whereSolvedAt($value)
 * @method static Builder<static>|Thread whereSolvedBy($value)
 * @method static Builder<static>|Thread whereStandardsCompliance($value)
 * @method static Builder<static>|Thread whereStatus($value)
 * @method static Builder<static>|Thread whereTechnicalDifficulty($value)
 * @method static Builder<static>|Thread whereTechnicalSpecs($value)
 * @method static Builder<static>|Thread whereThreadType($value)
 * @method static Builder<static>|Thread whereTitle($value)
 * @method static Builder<static>|Thread whereUpdatedAt($value)
 * @method static Builder<static>|Thread whereUrgencyLevel($value)
 * @method static Builder<static>|Thread whereUserId($value)
 * @method static Builder<static>|Thread whereViews($value)
 * @method static Builder<static>|Thread withTrashed()
 * @method static Builder<static>|Thread withoutTrashed()
 * @mixin \Eloquent
 */
class Thread extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'user_id',
        'category_id',
        'is_sticky',
        'is_locked',
        'is_featured',
        'view_count',
        'status',
        // Lifecycle states
        'archived_reason',
        'hidden_reason',
        // Moderation states
        'is_flagged',
        'is_spam',
        'moderation_status',
        'reports_count',
        'flagged_by',
        'moderation_notes',
        // Quality states
        'is_solved',
        'solution_comment_id',
        'solved_by',
        'quality_score',
        'average_rating',
        'ratings_count',
        'thread_type',
        // Activity tracking
        'last_comment_by',
        'bump_count',
        'dislikes_count',
        'bookmark_count',
        'follow_count',
        'share_count',
        'cached_comments_count',
        'cached_participants_count',
        'meta_description',
        'search_keywords',
        'read_time',
        // MechaMap mechanical engineering fields
        'technical_difficulty',
        'project_type',
        'software_used',
        'industry_sector',
        'technical_specs',
        'attachment_types',
        'has_calculations',
        'has_3d_models',
        'expert_verified',
        'verified_by',
        'verified_at',
        'technical_accuracy_score',
        'technical_keywords',
        'related_standards',
        // Missing timestamp fields
        'solved_at',
        'flagged_at',
        'last_activity_at',
        'last_comment_at',
        'last_bump_at',
        'archived_at',
        'hidden_at',
        // Additional moderation fields
        'moderated_by',
        'moderated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_sticky' => 'boolean',
        'is_locked' => 'boolean',
        'is_featured' => 'boolean',
        // Lifecycle states
        'archived_at' => 'datetime',
        'hidden_at' => 'datetime',
        'deleted_at' => 'datetime',
        // Moderation states
        'is_flagged' => 'boolean',
        'is_spam' => 'boolean',
        'flagged_at' => 'datetime',
        'reports_count' => 'integer',
        // Quality states
        'is_solved' => 'boolean',
        'solved_at' => 'datetime',
        'quality_score' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'ratings_count' => 'integer',
        // Activity tracking
        'last_activity_at' => 'datetime',
        'last_comment_at' => 'datetime',
        'last_bump_at' => 'datetime',
        'moderated_at' => 'datetime',
        // MechaMap mechanical engineering casts
        'software_used' => 'array',
        'technical_specs' => 'array',
        'attachment_types' => 'array',
        'technical_keywords' => 'array',
        'related_standards' => 'array',
        'has_calculations' => 'boolean',
        'has_3d_models' => 'boolean',
        'expert_verified' => 'boolean',
        'verified_at' => 'datetime',
        'technical_accuracy_score' => 'decimal:2',
        'bump_count' => 'integer',
        'view_count' => 'integer',
        'dislikes_count' => 'integer',
        'bookmark_count' => 'integer',
        'share_count' => 'integer',
        'cached_comments_count' => 'integer',
        'cached_participants_count' => 'integer',
        'read_time' => 'integer',
        'search_keywords' => 'array',
    ];

    /**
     * Get the user that created the thread.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the forum of the thread.
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Get the category of the thread.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the posts in the thread.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the reactions for the thread.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Get the comments for the thread.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    /**
     * Get all comments for the thread including replies.
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the likes for the thread.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ThreadLike::class);
    }

    /**
     * Get the saves for the thread.
     */
    public function saves(): HasMany
    {
        return $this->hasMany(ThreadSave::class);
    }

    /**
     * Get the participants of the thread.
     */
    public function participants()
    {
        return $this->hasMany(Comment::class)
            ->select('user_id')
            ->distinct()
            ->with('user');
    }

    /**
     * Check if the thread is liked by the given user.
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the thread is saved by the given user.
     */
    public function isSavedBy(User $user): bool
    {
        return $this->saves()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the thread is bookmarked by the given user.
     */
    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the participant count attribute.
     */
    public function getParticipantCountAttribute(): int
    {
        return $this->allComments()
            ->select('user_id')
            ->distinct()
            ->count('user_id') + 1; // +1 for the thread author
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Get all media attached to this thread (polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get all media attached to this thread (polymorphic) - alias for consistency.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Lấy featured image của thread.
     */
    public function getFeaturedImageAttribute(): ?string
    {
        // Tìm media đầu tiên từ polymorphic relationship
        $featuredMedia = $this->media()
            ->where('mime_type', 'like', 'image/%')
            ->first();

        if ($featuredMedia) {
            $filePath = $featuredMedia->file_path;

            // Nếu là URL đầy đủ (http/https), return trực tiếp
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                return $filePath;
            }

            // Nếu là relative path, tạo asset URL
            // Loại bỏ slash đầu để tránh double slash
            $cleanPath = ltrim($filePath, '/');
            return asset('storage/' . $cleanPath);
        }

        return null;
    }

    /**
     * Get the poll associated with the thread.
     */
    public function poll(): HasMany
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Check if the thread has a poll.
     */
    public function hasPoll(): bool
    {
        return $this->poll()->exists();
    }

    /**
     * Get the follows for the thread.
     */
    public function follows(): HasMany
    {
        return $this->hasMany(ThreadFollow::class);
    }

    /**
     * Get the users following this thread.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'thread_follows')
            ->withTimestamps();
    }

    /**
     * Check if the thread is followed by the given user.
     */
    public function isFollowedBy(User $user): bool
    {
        return $this->follows()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the showcase for this thread (polymorphic relationship).
     */
    public function showcase(): MorphOne
    {
        return $this->morphOne(Showcase::class, 'showcaseable');
    }

    /**
     * Get the showcases attached to this thread (many-to-many relationship).
     */
    public function showcases(): BelongsToMany
    {
        return $this->belongsToMany(Showcase::class, 'thread_showcase')
            ->withTimestamps();
    }

    /**
     * Get the tags for the thread.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'thread_tag')
            ->withTimestamps();
    }

    /**
     * Get the reports for the thread.
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // =================
    // NEW RELATIONSHIPS FOR ENHANCED STATES
    // =================

    /**
     * Get the user who flagged this thread.
     */
    public function flagger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    /**
     * Get the user who solved this thread.
     */
    public function solver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solved_by');
    }

    /**
     * Get the solution comment for this thread.
     */
    public function solutionComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'solution_comment_id');
    }

    /**
     * Get the user who moderated this thread.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Get the ratings for this thread.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(ThreadRating::class);
    }

    /**
     * Get the bookmarks for this thread.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(ThreadBookmark::class);
    }

    // =================
    // ENHANCED SCOPES
    // =================

    /**
     * Scope để lọc threads đã được giải quyết.
     */
    public function scopeSolved(Builder $query): Builder
    {
        return $query->where('is_solved', true);
    }

    /**
     * Scope để lọc threads chưa được giải quyết.
     */
    public function scopeUnsolved(Builder $query): Builder
    {
        return $query->where('is_solved', false);
    }

    /**
     * Scope để lọc threads theo loại.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('thread_type', $type);
    }

    /**
     * Scope để lọc threads không bị flagged.
     */
    public function scopeNotFlagged(Builder $query): Builder
    {
        return $query->where('is_flagged', false);
    }

    /**
     * Scope để lọc threads không phải spam.
     */
    public function scopeNotSpam(Builder $query): Builder
    {
        return $query->where('is_spam', false);
    }

    /**
     * Scope để lọc threads đã được archived.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope để lọc threads chưa được archived.
     */
    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope để lọc threads bị ẩn.
     */
    public function scopeHidden(Builder $query): Builder
    {
        return $query->whereNotNull('hidden_at');
    }

    /**
     * Scope để lọc threads không bị ẩn.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNull('hidden_at');
    }

    /**
     * Scope để lọc threads theo trạng thái moderation.
     */
    public function scopeModerationStatus(Builder $query, string $status): Builder
    {
        return $query->where('moderation_status', $status);
    }

    /**
     * Scope để lọc threads có quality score cao.
     */
    public function scopeHighQuality(Builder $query, float $minScore = 4.0): Builder
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * Scope để sắp xếp theo activity gần đây nhất.
     */
    public function scopeByRecentActivity(Builder $query): Builder
    {
        return $query->orderBy('last_activity_at', 'desc');
    }

    /**
     * Scope để sắp xếp theo số lượt xem cao nhất.
     */
    public function scopeByPopularity(Builder $query): Builder
    {
        return $query->orderBy('view_count', 'desc');
    }

    /**
     * Scope để lọc threads phù hợp hiển thị cho public (không spam, không hidden).
     */
    public function scopePublicVisible(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved')
            ->where('is_spam', false)
            ->whereNull('hidden_at')
            ->whereNull('archived_at');
    }

    /**
     * Scope để lọc threads theo rating tối thiểu.
     */
    public function scopeMinRating(Builder $query, float $minRating): Builder
    {
        return $query->where('average_rating', '>=', $minRating)
            ->where('ratings_count', '>=', 3); // Ít nhất 3 ratings để đáng tin cậy
    }

    /**
     * Scope để lọc threads có bookmark nhiều.
     */
    public function scopePopularBookmarks(Builder $query, int $minBookmarks = 5): Builder
    {
        return $query->where('bookmark_count', '>=', $minBookmarks);
    }

    /**
     * Scope để lọc theo khoảng thời gian.
     */
    public function scopeFromPeriod(Builder $query, string $period): Builder
    {
        $date = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->subDays(7)
        };

        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope để sắp xếp theo trending (kết hợp view, rating, bookmark).
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->select('*')
            ->selectRaw('(
                (view_count * 0.3) +
                (average_rating * ratings_count * 0.4) +
                (bookmark_count * 0.3)
            ) as trending_score')
            ->orderByDesc('trending_score');
    }

    /**
     * Scope để tìm kiếm threads.
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
                ->orWhere('content', 'like', "%{$searchTerm}%")
                ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                    $tagQuery->where('name', 'like', "%{$searchTerm}%");
                });
        });
    }

    /**
     * Scope để lọc threads của user đã bookmark.
     */
    public function scopeBookmarkedBy(Builder $query, $userId): Builder
    {
        return $query->whereHas('bookmarks', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Scope để lọc threads của user đã rate.
     */
    public function scopeRatedBy(Builder $query, $userId): Builder
    {
        return $query->whereHas('ratings', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // =================
    // ENHANCED METHODS
    // =================

    /**
     * Đánh dấu thread là đã được giải quyết.
     */
    public function markAsSolved(Comment $solutionComment, User $solver): bool
    {
        return $this->update([
            'is_solved' => true,
            'solution_comment_id' => $solutionComment->id,
            'solved_by' => $solver->id,
            'solved_at' => now(),
        ]);
    }

    /**
     * Bỏ đánh dấu thread đã được giải quyết.
     */
    public function unmarkSolved(): bool
    {
        return $this->update([
            'is_solved' => false,
            'solution_comment_id' => null,
            'solved_by' => null,
            'solved_at' => null,
        ]);
    }

    /**
     * Archive thread với lý do.
     */
    public function archive(string $reason = null): bool
    {
        return $this->update([
            'archived_at' => now(),
            'archived_reason' => $reason,
        ]);
    }

    /**
     * Unarchive thread.
     */
    public function unarchive(): bool
    {
        return $this->update([
            'archived_at' => null,
            'archived_reason' => null,
        ]);
    }

    /**
     * Ẩn thread với lý do.
     */
    public function hide(string $reason = null): bool
    {
        return $this->update([
            'hidden_at' => now(),
            'hidden_reason' => $reason,
        ]);
    }

    /**
     * Hiện thread.
     */
    public function unhide(): bool
    {
        return $this->update([
            'hidden_at' => null,
            'hidden_reason' => null,
        ]);
    }

    /**
     * Flag thread với user và lý do.
     */
    public function flag(User $flagger, string $notes = null): bool
    {
        return $this->update([
            'is_flagged' => true,
            'flagged_by' => $flagger->id,
            'flagged_at' => now(),
            'moderation_notes' => $notes,
        ]);
    }

    /**
     * Unflag thread.
     */
    public function unflag(): bool
    {
        return $this->update([
            'is_flagged' => false,
            'flagged_by' => null,
            'flagged_at' => null,
            'moderation_notes' => null,
        ]);
    }

    /**
     * Cập nhật activity timestamp.
     */
    public function updateLastActivity(): bool
    {
        return $this->update([
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Bump thread lên top.
     */
    public function bump(): bool
    {
        return $this->update([
            'last_bump_at' => now(),
            'bump_count' => $this->bump_count + 1,
        ]);
    }

    /**
     * Cập nhật cached counters.
     */
    public function updateCachedCounters(): bool
    {
        $commentsCount = $this->allComments()->count();
        $participantsCount = $this->allComments()
            ->select('user_id')
            ->distinct()
            ->count() + 1; // +1 for thread author

        return $this->update([
            'cached_comments_count' => $commentsCount,
            'cached_participants_count' => $participantsCount,
        ]);
    }

    /**
     * Kiểm tra thread có đang bị archived không.
     */
    public function isArchived(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Kiểm tra thread có đang bị ẩn không.
     */
    public function isHidden(): bool
    {
        return !is_null($this->hidden_at);
    }

    /**
     * Kiểm tra thread có đang bị flagged không.
     */
    public function isFlagged(): bool
    {
        return $this->is_flagged;
    }

    /**
     * Kiểm tra thread có phải spam không.
     */
    public function isSpam(): bool
    {
        return $this->is_spam;
    }

    /**
     * Kiểm tra thread đã được giải quyết chưa.
     */
    public function isSolved(): bool
    {
        return $this->is_solved;
    }

    /**
     * Lấy trạng thái hiển thị của thread.
     */
    public function getVisibilityStatus(): string
    {
        if ($this->trashed()) {
            return 'deleted';
        }

        if ($this->isHidden()) {
            return 'hidden';
        }

        if ($this->isArchived()) {
            return 'archived';
        }

        if ($this->isSpam()) {
            return 'spam';
        }

        if ($this->isFlagged()) {
            return 'flagged';
        }

        return 'visible';
    }

    /**
     * Tính toán lại average rating và ratings count.
     */
    public function recalculateRatings(): bool
    {
        $ratings = $this->ratings();
        $ratingsCount = $ratings->count();

        if ($ratingsCount === 0) {
            return $this->update([
                'average_rating' => 0,
                'ratings_count' => 0,
            ]);
        }

        $averageRating = $ratings->avg('rating');

        return $this->update([
            'average_rating' => round($averageRating, 2),
            'ratings_count' => $ratingsCount,
        ]);
    }

    /**
     * Get the rating from a specific user.
     */
    public function getRatingByUser(User $user): ?ThreadRating
    {
        return $this->ratings()->where('user_id', $user->id)->first();
    }

    /**
     * Check if thread is rated by a specific user.
     */
    public function isRatedBy(User $user): bool
    {
        return $this->ratings()->where('user_id', $user->id)->exists();
    }

    /**
     * Get rating distribution (1-5 stars count).
     */
    public function getRatingDistribution(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get percentage of positive ratings (4-5 stars).
     */
    public function getPositiveRatingPercentage(): float
    {
        if ($this->ratings_count === 0) {
            return 0;
        }

        $positiveCount = $this->ratings()
            ->whereIn('rating', [4, 5])
            ->count();

        return round(($positiveCount / $this->ratings_count) * 100, 1);
    }
}
