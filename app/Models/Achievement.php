<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'category',
        'type',
        'criteria',
        'icon',
        'color',
        'points',
        'rarity',
        'is_active',
        'is_hidden',
        'sort_order',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
        'is_hidden' => 'boolean',
        'points' => 'integer',
        'sort_order' => 'integer',
    ];

    // Achievement types
    const TYPE_MILESTONE = 'milestone';
    const TYPE_BADGE = 'badge';
    const TYPE_STREAK = 'streak';
    const TYPE_SPECIAL = 'special';

    // Achievement rarities
    const RARITY_COMMON = 'common';
    const RARITY_UNCOMMON = 'uncommon';
    const RARITY_RARE = 'rare';
    const RARITY_EPIC = 'epic';
    const RARITY_LEGENDARY = 'legendary';

    // Achievement categories
    const CATEGORY_SOCIAL = 'social';
    const CATEGORY_CONTENT = 'content';
    const CATEGORY_MARKETPLACE = 'marketplace';
    const CATEGORY_COMMUNITY = 'community';
    const CATEGORY_SPECIAL = 'special';

    /**
     * Get user achievements
     */
    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Get users who have this achievement
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'progress_data', 'current_progress', 'target_progress'])
            ->withTimestamps();
    }

    /**
     * Scope for active achievements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for visible achievements
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific rarity
     */
    public function scopeRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Get rarity color
     */
    public function getRarityColorAttribute(): string
    {
        return match($this->rarity) {
            self::RARITY_COMMON => '#6B7280',
            self::RARITY_UNCOMMON => '#10B981',
            self::RARITY_RARE => '#3B82F6',
            self::RARITY_EPIC => '#8B5CF6',
            self::RARITY_LEGENDARY => '#F59E0B',
            default => '#6B7280',
        };
    }

    /**
     * Get rarity weight for sorting
     */
    public function getRarityWeightAttribute(): int
    {
        return match($this->rarity) {
            self::RARITY_COMMON => 1,
            self::RARITY_UNCOMMON => 2,
            self::RARITY_RARE => 3,
            self::RARITY_EPIC => 4,
            self::RARITY_LEGENDARY => 5,
            default => 1,
        };
    }

    /**
     * Check if achievement criteria is met
     */
    public function checkCriteria(User $user): bool
    {
        $criteria = $this->criteria;

        if (empty($criteria)) {
            return false;
        }

        // Check each criterion
        foreach ($criteria as $criterion) {
            if (!$this->evaluateCriterion($criterion, $user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluate single criterion
     */
    private function evaluateCriterion(array $criterion, User $user): bool
    {
        $type = $criterion['type'] ?? '';
        $value = $criterion['value'] ?? 0;
        $operator = $criterion['operator'] ?? '>=';

        switch ($type) {
            case 'posts_count':
                $userValue = $user->posts()->count();
                break;
            case 'comments_count':
                $userValue = $user->comments()->count();
                break;
            case 'followers_count':
                $userValue = UserFollow::getFollowersCount($user->id);
                break;
            case 'following_count':
                $userValue = UserFollow::getFollowingCount($user->id);
                break;
            case 'days_since_registration':
                $userValue = $user->created_at->diffInDays(now());
                break;
            case 'login_streak':
                $userValue = $this->getUserLoginStreak($user);
                break;
            case 'achievements_count':
                $userValue = $user->achievements()->count();
                break;
            default:
                return false;
        }

        return $this->compareValues($userValue, $operator, $value);
    }

    /**
     * Compare values with operator
     */
    private function compareValues($userValue, string $operator, $targetValue): bool
    {
        return match($operator) {
            '>=' => $userValue >= $targetValue,
            '>' => $userValue > $targetValue,
            '<=' => $userValue <= $targetValue,
            '<' => $userValue < $targetValue,
            '==' => $userValue == $targetValue,
            '!=' => $userValue != $targetValue,
            default => false,
        };
    }

    /**
     * Get user login streak (placeholder)
     */
    private function getUserLoginStreak(User $user): int
    {
        // This would require login tracking
        // For now, return a placeholder
        return 1;
    }

    /**
     * Get achievement progress for user
     */
    public function getProgressForUser(User $user): array
    {
        $criteria = $this->criteria;
        $progress = [];
        $totalProgress = 0;
        $totalTarget = 0;

        foreach ($criteria as $criterion) {
            $type = $criterion['type'] ?? '';
            $target = $criterion['value'] ?? 1;
            $current = $this->getCurrentProgressValue($type, $user);

            $progress[] = [
                'type' => $type,
                'current' => $current,
                'target' => $target,
                'percentage' => $target > 0 ? min(100, ($current / $target) * 100) : 0,
                'completed' => $current >= $target,
            ];

            $totalProgress += min($current, $target);
            $totalTarget += $target;
        }

        return [
            'criteria' => $progress,
            'overall_percentage' => $totalTarget > 0 ? round(($totalProgress / $totalTarget) * 100, 2) : 0,
            'is_completed' => $totalProgress >= $totalTarget,
        ];
    }

    /**
     * Get current progress value for criterion type
     */
    private function getCurrentProgressValue(string $type, User $user): int
    {
        switch ($type) {
            case 'posts_count':
                return $user->posts()->count();
            case 'comments_count':
                return $user->comments()->count();
            case 'followers_count':
                return UserFollow::getFollowersCount($user->id);
            case 'following_count':
                return UserFollow::getFollowingCount($user->id);
            case 'days_since_registration':
                return $user->created_at->diffInDays(now());
            case 'login_streak':
                return $this->getUserLoginStreak($user);
            case 'achievements_count':
                return $user->achievements()->count();
            default:
                return 0;
        }
    }
}
