<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'video_url',
        'video_type',
        'thumbnail',
        'duration',
        'category_id',
        'author_id',
        'difficulty_level',
        'tags',
        'views_count',
        'rating_average',
        'rating_count',
        'status',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'duration' => 'integer',
        'views_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the author of the video
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the category of the video
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    /**
     * Scope for published videos
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured videos
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return null;

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Get video embed URL
     */
    public function getEmbedUrlAttribute()
    {
        if ($this->video_type === 'youtube') {
            $videoId = $this->extractYouTubeId($this->video_url);
            return "https://www.youtube.com/embed/{$videoId}";
        }

        if ($this->video_type === 'vimeo') {
            $videoId = $this->extractVimeoId($this->video_url);
            return "https://player.vimeo.com/video/{$videoId}";
        }

        return $this->video_url;
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function extractYouTubeId($url)
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        return $matches[1] ?? null;
    }
}
