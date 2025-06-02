<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Thread extends Model
{
    use HasFactory;

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
        'user_id',
        'forum_id',
        'category_id',
        'is_sticky',
        'is_locked',
        'is_featured',
        'view_count',
        'status',
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
     * Get the media for the thread.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'thread_id');
    }

    /**
     * Get all media attached to this thread (polymorphic).
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
        // Prioritize: Tìm media đầu tiên từ polymorphic attachments
        $featuredMedia = $this->attachments()
            ->where('file_type', 'like', 'image/%')
            ->first();

        if (!$featuredMedia) {
            // Fallback: Tìm media đầu tiên từ direct relationship
            $featuredMedia = $this->media()
                ->where('file_type', 'like', 'image/%')
                ->first();
        }

        if ($featuredMedia) {
            $filePath = $featuredMedia->file_path;

            // Nếu là URL đầy đủ (http/https), return trực tiếp
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                return $filePath;
            }

            // Nếu là relative path, tạo asset URL
            return asset('storage/' . $filePath);
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
}
