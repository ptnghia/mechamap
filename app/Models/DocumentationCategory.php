<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DocumentationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color_code',
        'parent_id',
        'sort_order',
        'is_active',
        'is_public',
        'allowed_roles',
        'document_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'allowed_roles' => 'array',
        'sort_order' => 'integer',
        'document_count' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_public' => false,
        'sort_order' => 0,
        'document_count' => 0,
        'color_code' => '#007bff',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            $category->updateDocumentCount();
        });
    }

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentationCategory::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(DocumentationCategory::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    /**
     * Get all documentations in this category
     */
    public function documentations(): HasMany
    {
        return $this->hasMany(Documentation::class, 'category_id');
    }

    /**
     * Get published documentations
     */
    public function publishedDocumentations(): HasMany
    {
        return $this->documentations()
                    ->where('status', 'published')
                    ->orderBy('sort_order');
    }

    /**
     * Get public documentations
     */
    public function publicDocumentations(): HasMany
    {
        return $this->publishedDocumentations()
                    ->where('is_public', true);
    }

    /**
     * Update document count
     */
    public function updateDocumentCount(): void
    {
        $count = $this->documentations()->where('status', 'published')->count();
        $this->update(['document_count' => $count]);
    }

    /**
     * Check if user can access this category
     */
    public function canAccess(?User $user = null): bool
    {
        // Public categories are accessible to everyone
        if ($this->is_public) {
            return true;
        }

        // If no user, can't access private categories
        if (!$user) {
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
     * Get breadcrumb path
     */
    public function getBreadcrumbPath(): array
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
                'url' => route('admin.documentation.categories.show', $current->id)
            ]);
            $current = $current->parent;
        }

        return $path;
    }

    /**
     * Get category tree (recursive)
     */
    public static function getTree(?int $parentId = null): array
    {
        $categories = static::where('parent_id', $parentId)
                           ->where('is_active', true)
                           ->orderBy('sort_order')
                           ->get();

        $tree = [];
        foreach ($categories as $category) {
            $item = $category->toArray();
            $item['children'] = static::getTree($category->id);
            $tree[] = $item;
        }

        return $tree;
    }

    /**
     * Get all descendant categories
     */
    public function getDescendants(): array
    {
        $descendants = [];
        
        foreach ($this->children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public categories
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for categories accessible by user
     */
    public function scopeAccessibleBy($query, ?User $user = null)
    {
        if (!$user) {
            return $query->where('is_public', true);
        }

        return $query->where(function ($q) use ($user) {
            $q->where('is_public', true)
              ->orWhereNull('allowed_roles')
              ->orWhereJsonContains('allowed_roles', $user->role);
        });
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_documents' => $this->documentations()->count(),
            'published_documents' => $this->documentations()->where('status', 'published')->count(),
            'draft_documents' => $this->documentations()->where('status', 'draft')->count(),
            'total_views' => $this->documentations()->sum('view_count'),
            'average_rating' => $this->documentations()->avg('rating_average'),
            'total_downloads' => $this->documentations()->sum('download_count'),
        ];
    }

    /**
     * Get URL for frontend
     */
    public function getUrlAttribute(): string
    {
        return route('docs.category', $this->slug);
    }

    /**
     * Get admin URL
     */
    public function getAdminUrlAttribute(): string
    {
        return route('admin.documentation.categories.show', $this->id);
    }
}
