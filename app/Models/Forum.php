<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Region;
use App\Models\Country;

/**
 *
 *
 * @property-read mixed $representative_image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read Forum|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Forum> $subForums
 * @property-read int|null $sub_forums_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $threads
 * @property-read int|null $threads_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum query()
 * @mixin \Eloquent
 */
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
        'category_id',
        'parent_id',
        'order',
        'is_private',
        'thread_count',
        'post_count',
        'last_activity_at',
        'last_thread_id',
        'last_post_user_id',
        // Geographic support
        'region_id',
        'allowed_countries',
        'primary_languages',
        'scope',
        'regional_standards',
        'local_regulations',
        // Avatar and media support
        'avatar_url',
        'avatar_media_id',
        'banner_url',
        'banner_media_id',
        'gallery_media_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_private' => 'boolean',
        'requires_approval' => 'boolean',
        'allowed_thread_types' => 'array',
        'last_activity_at' => 'datetime',
        'allowed_countries' => 'array',
        'primary_languages' => 'array',
        'regional_standards' => 'array',
        'local_regulations' => 'array',
        'gallery_media_ids' => 'array',
    ];

    /**
     * Get the category this forum belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

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

    // ====================================================================
    // GEOGRAPHIC RELATIONSHIPS - Regions & Countries
    // ====================================================================

    /**
     * Get the region this forum belongs to
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Check if forum allows users from specific country
     */
    public function allowsCountry(string $countryCode): bool
    {
        // Global forums allow all countries
        if ($this->scope === 'global') {
            return true;
        }

        // Check allowed_countries array
        if (!empty($this->allowed_countries)) {
            return in_array($countryCode, $this->allowed_countries);
        }

        // Regional/local forums allow region's country
        if ($this->region && $this->region->country) {
            return $this->region->country->code === $countryCode;
        }

        return false;
    }

    /**
     * Get countries allowed in this forum
     */
    public function getAllowedCountries(): array
    {
        if ($this->scope === 'global') {
            return Country::where('is_active', true)->pluck('code')->toArray();
        }

        if (!empty($this->allowed_countries)) {
            return $this->allowed_countries;
        }

        if ($this->region && $this->region->country) {
            return [$this->region->country->code];
        }

        return [];
    }

    /**
     * Get relevant technical standards for this forum
     */
    public function getRelevantStandards(): array
    {
        $standards = [];

        // Add regional standards
        if (!empty($this->regional_standards)) {
            $standards = array_merge($standards, $this->regional_standards);
        }

        // Add region's local standards
        if ($this->region && !empty($this->region->local_standards)) {
            $standards = array_merge($standards, $this->region->local_standards);
        }

        // Add country standards
        if ($this->region && $this->region->country && !empty($this->region->country->standard_organizations)) {
            $standards = array_merge($standards, $this->region->country->standard_organizations);
        }

        return array_unique($standards);
    }

    /**
     * Check if forum is accessible by user based on location
     */
    public function isAccessibleByUser(User $user): bool
    {
        // Global forums are accessible to all
        if ($this->scope === 'global') {
            return true;
        }

        // Check if user's country is allowed
        if ($user->country) {
            return $this->allowsCountry($user->country->code);
        }

        // Default allow if no geographic restrictions
        return empty($this->allowed_countries);
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
     * Get the avatar media for this forum.
     */
    public function avatarMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'avatar_media_id');
    }

    /**
     * Get the banner media for this forum.
     */
    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    /**
     * Get gallery media for this forum.
     */
    public function galleryMedia()
    {
        if (empty($this->gallery_media_ids)) {
            return collect();
        }

        return Media::whereIn('id', $this->gallery_media_ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $this->gallery_media_ids) . ')')
            ->get();
    }

    /**
     * Get the representative image for this forum.
     */
    public function getRepresentativeImageAttribute()
    {
        return $this->avatarMedia ?: $this->media()->first();
    }

    /**
     * Get the avatar URL (prioritize media over direct URL).
     */
    public function getAvatarUrlAttribute($value)
    {
        if ($this->avatarMedia) {
            return asset('storage/' . $this->avatarMedia->file_path);
        }
        return $value;
    }

    /**
     * Get the banner URL (prioritize media over direct URL).
     */
    public function getBannerUrlAttribute($value)
    {
        if ($this->bannerMedia) {
            return asset('storage/' . $this->bannerMedia->file_path);
        }
        return $value;
    }

    /**
     * Get the latest thread in this forum.
     */
    public function latestThread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'last_thread_id');
    }

    /**
     * Get the user who made the last post.
     */
    public function lastPostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }
}
