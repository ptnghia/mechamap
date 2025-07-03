<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EducationalResource extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'category',
        'type',
        'difficulty_level',
        'file_path',
        'file_size',
        'file_type',
        'thumbnail_path',
        'duration_minutes',
        'language',
        'tags',
        'is_published',
        'is_featured',
        'view_count',
        'download_count',
        'rating_average',
        'rating_count',
        'user_id',
        'university_id',
        'course_code',
        'academic_year',
        'semester',
        'prerequisites',
        'learning_objectives',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'prerequisites' => 'array',
        'learning_objectives' => 'array',
        'metadata' => 'array',
        'rating_average' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Resource categories.
     */
    const CATEGORIES = [
        'textbook' => 'Textbook',
        'video' => 'Video Tutorial',
        'research_paper' => 'Research Paper',
        'software_tool' => 'Software Tool',
        'lecture_note' => 'Lecture Notes',
        'assignment' => 'Assignment',
        'exam' => 'Exam/Quiz',
        'project' => 'Project Template',
        'reference' => 'Reference Material',
        'simulation' => 'Simulation',
    ];

    /**
     * Resource types.
     */
    const TYPES = [
        'pdf' => 'PDF Document',
        'video' => 'Video File',
        'audio' => 'Audio File',
        'image' => 'Image',
        'document' => 'Document',
        'presentation' => 'Presentation',
        'spreadsheet' => 'Spreadsheet',
        'archive' => 'Archive',
        'software' => 'Software',
        'link' => 'External Link',
    ];

    /**
     * Difficulty levels.
     */
    const DIFFICULTY_LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert',
    ];

    /**
     * Get the user who uploaded this resource.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the university this resource belongs to.
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    /**
     * Get the downloads for this resource.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(ResourceDownload::class);
    }

    /**
     * Get the reviews for this resource.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ResourceReview::class);
    }

    /**
     * Get the bookmarks for this resource.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(ResourceBookmark::class);
    }

    /**
     * Get users who bookmarked this resource.
     */
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_bookmarks')
            ->withTimestamps();
    }

    /**
     * Get the learning paths that include this resource.
     */
    public function learningPaths(): BelongsToMany
    {
        return $this->belongsToMany(LearningPath::class, 'learning_path_resources')
            ->withPivot(['order', 'is_required'])
            ->withTimestamps();
    }

    /**
     * Scope for published resources.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured resources.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for specific category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific difficulty level.
     */
    public function scopeDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope for search by title or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        if (filter_var($this->file_path, FILTER_VALIDATE_URL)) {
            return $this->file_path;
        }

        $cleanPath = ltrim($this->file_path, '/');
        return asset('storage/' . $cleanPath);
    }

    /**
     * Get the thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return $this->getDefaultThumbnail();
        }

        if (filter_var($this->thumbnail_path, FILTER_VALIDATE_URL)) {
            return $this->thumbnail_path;
        }

        $cleanPath = ltrim($this->thumbnail_path, '/');
        return asset('storage/' . $cleanPath);
    }

    /**
     * Get default thumbnail based on category.
     */
    private function getDefaultThumbnail(): string
    {
        $thumbnails = [
            'textbook' => 'images/defaults/textbook.svg',
            'video' => 'images/defaults/video.svg',
            'research_paper' => 'images/defaults/paper.svg',
            'software_tool' => 'images/defaults/software.svg',
            'lecture_note' => 'images/defaults/notes.svg',
            'assignment' => 'images/defaults/assignment.svg',
            'exam' => 'images/defaults/exam.svg',
            'project' => 'images/defaults/project.svg',
            'reference' => 'images/defaults/reference.svg',
            'simulation' => 'images/defaults/simulation.svg',
        ];

        return asset($thumbnails[$this->category] ?? 'images/defaults/resource.svg');
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration_minutes) {
            return null;
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    /**
     * Get category display name.
     */
    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get difficulty display name.
     */
    public function getDifficultyNameAttribute(): string
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level);
    }

    /**
     * Check if user has bookmarked this resource.
     */
    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user has downloaded this resource.
     */
    public function isDownloadedBy(User $user): bool
    {
        return $this->downloads()->where('user_id', $user->id)->exists();
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment download count.
     */
    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    /**
     * Update rating average.
     */
    public function updateRating(): void
    {
        $average = $this->reviews()->avg('rating');
        $count = $this->reviews()->count();

        $this->update([
            'rating_average' => $average,
            'rating_count' => $count,
        ]);
    }
}
