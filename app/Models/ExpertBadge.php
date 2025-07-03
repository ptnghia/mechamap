<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpertBadge extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'type',
        'icon',
        'color',
        'requirements',
        'points_required',
        'is_active',
        'is_featured',
        'rarity',
        'verification_required',
        'auto_award',
        'display_order',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'verification_required' => 'boolean',
        'auto_award' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Badge categories.
     */
    const CATEGORIES = [
        'expertise' => 'Technical Expertise',
        'contribution' => 'Community Contribution',
        'leadership' => 'Leadership',
        'achievement' => 'Special Achievement',
        'skill' => 'Skill Mastery',
        'recognition' => 'Recognition',
        'milestone' => 'Milestone',
        'special' => 'Special Event',
    ];

    /**
     * Badge types.
     */
    const TYPES = [
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'platinum' => 'Platinum',
        'diamond' => 'Diamond',
        'legendary' => 'Legendary',
    ];

    /**
     * Badge rarity levels.
     */
    const RARITY = [
        'common' => 'Common',
        'uncommon' => 'Uncommon',
        'rare' => 'Rare',
        'epic' => 'Epic',
        'legendary' => 'Legendary',
        'mythic' => 'Mythic',
    ];

    /**
     * Get users who have this badge.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_expert_badges')
            ->withPivot(['awarded_at', 'verified_at', 'verified_by', 'notes'])
            ->withTimestamps();
    }

    /**
     * Get badge verifications.
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(BadgeVerification::class);
    }

    /**
     * Scope for active badges.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured badges.
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
     * Scope for specific type.
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for auto-award badges.
     */
    public function scopeAutoAward($query)
    {
        return $query->where('auto_award', true);
    }

    /**
     * Get the icon URL.
     */
    public function getIconUrlAttribute(): string
    {
        if (!$this->icon) {
            return $this->getDefaultIcon();
        }

        if (filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return $this->icon;
        }

        $cleanPath = ltrim($this->icon, '/');
        return asset('storage/' . $cleanPath);
    }

    /**
     * Get default icon based on category and type.
     */
    private function getDefaultIcon(): string
    {
        $icons = [
            'expertise' => [
                'bronze' => 'images/badges/expertise-bronze.svg',
                'silver' => 'images/badges/expertise-silver.svg',
                'gold' => 'images/badges/expertise-gold.svg',
                'platinum' => 'images/badges/expertise-platinum.svg',
                'diamond' => 'images/badges/expertise-diamond.svg',
                'legendary' => 'images/badges/expertise-legendary.svg',
            ],
            'contribution' => [
                'bronze' => 'images/badges/contribution-bronze.svg',
                'silver' => 'images/badges/contribution-silver.svg',
                'gold' => 'images/badges/contribution-gold.svg',
                'platinum' => 'images/badges/contribution-platinum.svg',
                'diamond' => 'images/badges/contribution-diamond.svg',
                'legendary' => 'images/badges/contribution-legendary.svg',
            ],
            'leadership' => [
                'bronze' => 'images/badges/leadership-bronze.svg',
                'silver' => 'images/badges/leadership-silver.svg',
                'gold' => 'images/badges/leadership-gold.svg',
                'platinum' => 'images/badges/leadership-platinum.svg',
                'diamond' => 'images/badges/leadership-diamond.svg',
                'legendary' => 'images/badges/leadership-legendary.svg',
            ],
        ];

        return asset($icons[$this->category][$this->type] ?? 'images/badges/default.svg');
    }

    /**
     * Get category display name.
     */
    public function getCategoryNameAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get type display name.
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get rarity display name.
     */
    public function getRarityNameAttribute(): string
    {
        return self::RARITY[$this->rarity] ?? ucfirst($this->rarity);
    }

    /**
     * Get rarity color.
     */
    public function getRarityColorAttribute(): string
    {
        $colors = [
            'common' => '#6c757d',
            'uncommon' => '#28a745',
            'rare' => '#007bff',
            'epic' => '#6f42c1',
            'legendary' => '#fd7e14',
            'mythic' => '#dc3545',
        ];

        return $colors[$this->rarity] ?? '#6c757d';
    }

    /**
     * Check if user meets requirements for this badge.
     */
    public function userMeetsRequirements(User $user): bool
    {
        if (!$this->requirements) {
            return true;
        }

        foreach ($this->requirements as $requirement) {
            if (!$this->checkRequirement($user, $requirement)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check individual requirement.
     */
    private function checkRequirement(User $user, array $requirement): bool
    {
        $type = $requirement['type'] ?? '';
        $value = $requirement['value'] ?? 0;
        $operator = $requirement['operator'] ?? '>=';

        switch ($type) {
            case 'posts_count':
                $userValue = $user->threads()->count() + $user->posts()->count();
                break;
            case 'reputation_score':
                $userValue = $user->reputation_score ?? 0;
                break;
            case 'years_experience':
                $userValue = $user->years_experience ?? 0;
                break;
            case 'helpful_answers':
                $userValue = $user->posts()->where('is_helpful', true)->count();
                break;
            case 'forum_moderations':
                $userValue = $user->moderationActions()->count();
                break;
            case 'mentoring_sessions':
                $userValue = $user->mentoringSessions()->count();
                break;
            default:
                return false;
        }

        return $this->compareValues($userValue, $operator, $value);
    }

    /**
     * Compare values with operator.
     */
    private function compareValues($userValue, string $operator, $requiredValue): bool
    {
        switch ($operator) {
            case '>=':
                return $userValue >= $requiredValue;
            case '>':
                return $userValue > $requiredValue;
            case '<=':
                return $userValue <= $requiredValue;
            case '<':
                return $userValue < $requiredValue;
            case '==':
                return $userValue == $requiredValue;
            case '!=':
                return $userValue != $requiredValue;
            default:
                return false;
        }
    }

    /**
     * Award this badge to a user.
     */
    public function awardToUser(User $user, ?User $awardedBy = null, ?string $notes = null): bool
    {
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return false; // Already has this badge
        }

        $this->users()->attach($user->id, [
            'awarded_at' => now(),
            'verified_at' => $this->verification_required ? null : now(),
            'verified_by' => $awardedBy?->id,
            'notes' => $notes,
        ]);

        // Fire event
        event(new \App\Events\BadgeAwarded($user, $this));

        return true;
    }

    /**
     * Verify badge for a user.
     */
    public function verifyForUser(User $user, User $verifiedBy, ?string $notes = null): bool
    {
        $pivot = $this->users()->where('user_id', $user->id)->first();
        
        if (!$pivot) {
            return false; // User doesn't have this badge
        }

        $this->users()->updateExistingPivot($user->id, [
            'verified_at' => now(),
            'verified_by' => $verifiedBy->id,
            'notes' => $notes,
        ]);

        // Fire event
        event(new \App\Events\BadgeVerified($user, $this, $verifiedBy));

        return true;
    }

    /**
     * Get badge progress for user.
     */
    public function getProgressForUser(User $user): array
    {
        if (!$this->requirements) {
            return ['progress' => 100, 'completed' => true];
        }

        $totalRequirements = count($this->requirements);
        $completedRequirements = 0;
        $details = [];

        foreach ($this->requirements as $requirement) {
            $completed = $this->checkRequirement($user, $requirement);
            if ($completed) {
                $completedRequirements++;
            }

            $details[] = [
                'type' => $requirement['type'],
                'required' => $requirement['value'],
                'current' => $this->getCurrentValueForUser($user, $requirement['type']),
                'completed' => $completed,
            ];
        }

        $progress = ($completedRequirements / $totalRequirements) * 100;

        return [
            'progress' => round($progress, 2),
            'completed' => $progress >= 100,
            'details' => $details,
        ];
    }

    /**
     * Get current value for user for specific requirement type.
     */
    private function getCurrentValueForUser(User $user, string $type)
    {
        switch ($type) {
            case 'posts_count':
                return $user->threads()->count() + $user->posts()->count();
            case 'reputation_score':
                return $user->reputation_score ?? 0;
            case 'years_experience':
                return $user->years_experience ?? 0;
            case 'helpful_answers':
                return $user->posts()->where('is_helpful', true)->count();
            case 'forum_moderations':
                return $user->moderationActions()->count();
            case 'mentoring_sessions':
                return $user->mentoringSessions()->count();
            default:
                return 0;
        }
    }
}
