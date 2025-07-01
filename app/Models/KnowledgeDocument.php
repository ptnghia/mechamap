<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KnowledgeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'original_filename',
        'category_id',
        'author_id',
        'tags',
        'download_count',
        'rating_average',
        'rating_count',
        'status',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the author of the document
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the category of the document
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    /**
     * Scope for published documents
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured documents
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get file icon based on file type
     */
    public function getFileIconAttribute()
    {
        return match (strtolower($this->file_type)) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc', 'docx' => 'fas fa-file-word text-primary',
            'xls', 'xlsx' => 'fas fa-file-excel text-success',
            'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
            'zip', 'rar' => 'fas fa-file-archive text-secondary',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
            default => 'fas fa-file text-muted'
        };
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}
