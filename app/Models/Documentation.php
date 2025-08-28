<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Documentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'category_id',
        'author_id',
        'reviewer_id',
        'status',
        'is_featured',
        'is_public',
        'allowed_roles',
        'content_type',
        'difficulty_level',
        'estimated_read_time',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'featured_image',
        'view_count',
        'rating_average',
        'rating_count',
        'download_count',
        'sort_order',
        'tags',
        'related_docs',
        'attachments',
        'downloadable_files',
        'published_at',
        'reviewed_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'allowed_roles' => 'array',
        'tags' => 'array',
        'related_docs' => 'array',
        'attachments' => 'array',
        'downloadable_files' => 'array',
        'view_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'download_count' => 'integer',
        'sort_order' => 'integer',
        'estimated_read_time' => 'integer',
        'published_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
        'is_featured' => false,
        'is_public' => false,
        'content_type' => 'guide',
        'difficulty_level' => 'beginner',
        'view_count' => 0,
        'rating_average' => 0,
        'rating_count' => 0,
        'download_count' => 0,
        'sort_order' => 0,
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($doc) {
            if (empty($doc->slug)) {
                $doc->slug = Str::slug($doc->title);
            }
            if (empty($doc->author_id)) {
                $doc->author_id = Auth::id();
            }
            if (empty($doc->excerpt)) {
                $doc->excerpt = Str::limit(strip_tags($doc->content), 200);
            }
            if (empty($doc->estimated_read_time)) {
                $doc->estimated_read_time = static::calculateReadTime($doc->content);
            }
        });

        static::updating(function ($doc) {
            if ($doc->isDirty('title') && empty($doc->slug)) {
                $doc->slug = Str::slug($doc->title);
            }
            if ($doc->isDirty('content')) {
                if (empty($doc->excerpt)) {
                    $doc->excerpt = Str::limit(strip_tags($doc->content), 200);
                }
                $doc->estimated_read_time = static::calculateReadTime($doc->content);
            }
        });

        static::saved(function ($doc) {
            $doc->category->updateDocumentCount();
        });

        static::deleted(function ($doc) {
            $doc->category->updateDocumentCount();
        });
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentationCategory::class, 'category_id');
    }

    /**
     * Get the author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the reviewer
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get versions
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentationVersion::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get views
     */
    public function views(): HasMany
    {
        return $this->hasMany(DocumentationView::class);
    }

    /**
     * Get ratings
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(DocumentationRating::class);
    }

    /**
     * Get comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(DocumentationComment::class)->whereNull('parent_id');
    }

    /**
     * Get downloads
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentationDownload::class);
    }

    /**
     * Calculate estimated read time
     */
    public static function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Average reading speed
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Check if user can access this document
     */
    public function canAccess(?User $user = null): bool
    {
        // Check if document is published
        if ($this->status !== 'published') {
            return $user && ($user->id === $this->author_id || $user->hasRole(['admin', 'moderator']));
        }

        // Public documents are accessible to everyone
        if ($this->is_public) {
            return true;
        }

        // If no user, can't access private documents
        if (!$user) {
            return false;
        }

        // Check category access
        if (!$this->category->canAccess($user)) {
            return false;
        }

        // If no role restrictions, authenticated users can access
        if (empty($this->allowed_roles)) {
            return true;
        }

        // Check if user's role is in allowed roles
        return in_array($user->role, $this->allowed_roles);
    }

    /**
     * Record a view
     */
    public function recordView(?User $user = null, array $data = []): void
    {
        $this->views()->create([
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->header('referer'),
            'time_spent' => $data['time_spent'] ?? null,
            'scroll_percentage' => $data['scroll_percentage'] ?? null,
        ]);

        $this->increment('view_count');
    }

    /**
     * Add rating
     */
    public function addRating(User $user, int $rating, ?string $comment = null): void
    {
        $this->ratings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'rating' => $rating,
                'comment' => $comment,
                'is_helpful' => $rating >= 4,
            ]
        );

        $this->updateRatingAverage();
    }

    /**
     * Update rating average
     */
    public function updateRatingAverage(): void
    {
        $ratings = $this->ratings();
        $this->update([
            'rating_average' => $ratings->avg('rating') ?? 0,
            'rating_count' => $ratings->count(),
        ]);
    }

    /**
     * Create new version
     */
    public function createVersion(User $user, string $changeSummary = null): DocumentationVersion
    {
        $lastVersion = $this->versions()->first();
        $versionNumber = $lastVersion ? $this->incrementVersion($lastVersion->version_number) : '1.0';

        return $this->versions()->create([
            'user_id' => $user->id,
            'version_number' => $versionNumber,
            'content' => $this->content,
            'change_summary' => $changeSummary,
            'metadata' => [
                'title' => $this->title,
                'status' => $this->status,
                'updated_at' => $this->updated_at,
            ],
            'is_major_version' => str_contains($versionNumber, '.0'),
        ]);
    }

    /**
     * Increment version number
     */
    private function incrementVersion(string $version): string
    {
        $parts = explode('.', $version);
        $parts[1] = (int)$parts[1] + 1;
        return implode('.', $parts);
    }

    /**
     * Get related documents
     */
    public function getRelatedDocuments()
    {
        if (empty($this->related_docs)) {
            return collect();
        }

        return static::whereIn('id', $this->related_docs)
                    ->where('status', 'published')
                    ->get();
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeByDifficulty($query, string $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeAccessibleBy($query, ?User $user = null)
    {
        if (!$user) {
            return $query->where('is_public', true)->where('status', 'published');
        }

        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
              ->orWhere('author_id', $user->id)
              ->orWhere(function ($subQ) use ($user) {
                  $subQ->whereNull('allowed_roles')
                       ->orWhereJsonContains('allowed_roles', $user->role);
              });
        })->where('status', 'published');
    }

    /**
     * Get URL for frontend
     */
    public function getUrlAttribute(): string
    {
        return route('docs.show', $this->slug);
    }

    /**
     * Get admin URL
     */
    public function getAdminUrlAttribute(): string
    {
        return route('admin.documentation.show', $this->id);
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumbPath(): array
    {
        $path = $this->category->getBreadcrumbPath();
        $path[] = [
            'id' => $this->id,
            'name' => $this->title,
            'slug' => $this->slug,
            'url' => $this->url
        ];
        return $path;
    }
}
