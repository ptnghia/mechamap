<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Forum extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'is_private',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * Get the parent forum.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Forum::class, 'parent_id');
    }

    /**
     * Get the sub-forums.
     */
    public function subForums(): HasMany
    {
        return $this->hasMany(Forum::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the threads in this forum.
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get all posts in this forum through threads.
     */
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, Thread::class);
    }

    /**
     * Get all media attached to this forum (polymorphic).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the representative image for this forum.
     */
    public function getRepresentativeImageAttribute()
    {
        return $this->media()->first();
    }
}
