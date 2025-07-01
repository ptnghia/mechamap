<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent()
    {
        return $this->belongsTo(KnowledgeCategory::class, 'parent_id');
    }

    /**
     * Get the child categories
     */
    public function children()
    {
        return $this->hasMany(KnowledgeCategory::class, 'parent_id');
    }

    /**
     * Get all articles in this category
     */
    public function articles()
    {
        return $this->hasMany(KnowledgeArticle::class, 'category_id');
    }

    /**
     * Get all videos in this category
     */
    public function videos()
    {
        return $this->hasMany(KnowledgeVideo::class, 'category_id');
    }

    /**
     * Get all documents in this category
     */
    public function documents()
    {
        return $this->hasMany(KnowledgeDocument::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
