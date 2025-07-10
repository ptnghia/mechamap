<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AchievementService
{
    /**
     * Check and unlock achievements for user
     */
    public static function checkAchievements(User $user): array
    {
        try {
            $unlockedAchievements = [];
            
            // Get all active achievements that user hasn't unlocked yet
            $availableAchievements = Achievement::active()
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('achievement_id')
                        ->from('user_achievements')
                        ->where('user_id', $user->id);
                })
                ->get();

            foreach ($availableAchievements as $achievement) {
                if ($achievement->checkCriteria($user)) {
                    $userAchievement = static::unlockAchievement($user, $achievement);
                    if ($userAchievement) {
                        $unlockedAchievements[] = $userAchievement;
                    }
                }
            }

            if (!empty($unlockedAchievements)) {
                Log::info("Achievements unlocked for user", [
                    'user_id' => $user->id,
                    'achievements_count' => count($unlockedAchievements),
                    'achievement_ids' => collect($unlockedAchievements)->pluck('achievement_id')->toArray(),
                ]);
            }

            return $unlockedAchievements;

        } catch (\Exception $e) {
            Log::error("Failed to check achievements", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            
            return [];
        }
    }

    /**
     * Unlock specific achievement for user
     */
    public static function unlockAchievement(User $user, Achievement $achievement): ?UserAchievement
    {
        try {
            // Check if user already has this achievement
            $existingAchievement = UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();

            if ($existingAchievement) {
                return null; // Already unlocked
            }

            // Get progress data
            $progressData = $achievement->getProgressForUser($user);
            
            // Create user achievement
            $userAchievement = UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'unlocked_at' => now(),
                'progress_data' => $progressData,
                'current_progress' => $progressData['criteria'][0]['current'] ?? 1,
                'target_progress' => $progressData['criteria'][0]['target'] ?? 1,
                'is_notified' => false,
            ]);

            // Send achievement notification
            static::sendAchievementNotification($user, $achievement);

            // Clear user achievements cache
            static::clearUserAchievementsCache($user->id);

            Log::info("Achievement unlocked", [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'achievement_key' => $achievement->key,
                'points' => $achievement->points,
            ]);

            return $userAchievement;

        } catch (\Exception $e) {
            Log::error("Failed to unlock achievement", [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Send achievement notification
     */
    private static function sendAchievementNotification(User $user, Achievement $achievement): void
    {
        try {
            $notificationData = [
                'user_id' => $user->id,
                'type' => 'achievement_unlocked',
                'title' => 'Thành tựu mới!',
                'message' => "Bạn đã mở khóa thành tựu: {$achievement->name}",
                'data' => [
                    'achievement_id' => $achievement->id,
                    'achievement_key' => $achievement->key,
                    'achievement_name' => $achievement->name,
                    'achievement_description' => $achievement->description,
                    'achievement_icon' => $achievement->icon,
                    'achievement_color' => $achievement->color,
                    'achievement_rarity' => $achievement->rarity,
                    'achievement_points' => $achievement->points,
                    'action_url' => "/profile/achievements",
                    'action_text' => 'Xem thành tựu',
                ],
                'priority' => 'normal',
            ];

            Notification::create($notificationData);

        } catch (\Exception $e) {
            Log::error("Failed to send achievement notification", [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user achievements
     */
    public static function getUserAchievements(int $userId, bool $includeProgress = false): array
    {
        $cacheKey = "user_achievements_{$userId}_" . ($includeProgress ? 'with_progress' : 'basic');
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($userId, $includeProgress) {
            $query = UserAchievement::where('user_id', $userId)
                ->with(['achievement'])
                ->orderBy('unlocked_at', 'desc');

            $userAchievements = $query->get();

            return $userAchievements->map(function ($userAchievement) use ($includeProgress) {
                $achievement = $userAchievement->achievement;
                
                $data = [
                    'id' => $userAchievement->id,
                    'achievement_id' => $achievement->id,
                    'key' => $achievement->key,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'category' => $achievement->category,
                    'type' => $achievement->type,
                    'icon' => $achievement->icon,
                    'color' => $achievement->color,
                    'rarity' => $achievement->rarity,
                    'rarity_color' => $achievement->rarity_color,
                    'points' => $achievement->points,
                    'unlocked_at' => $userAchievement->unlocked_at,
                    'is_notified' => $userAchievement->is_notified,
                ];

                if ($includeProgress) {
                    $data['progress'] = [
                        'current' => $userAchievement->current_progress,
                        'target' => $userAchievement->target_progress,
                        'percentage' => $userAchievement->progress_percentage,
                        'is_completed' => $userAchievement->is_completed,
                        'data' => $userAchievement->progress_data,
                    ];
                }

                return $data;
            })->toArray();
        });
    }

    /**
     * Get available achievements for user
     */
    public static function getAvailableAchievements(int $userId): array
    {
        $cacheKey = "available_achievements_{$userId}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($userId) {
            $user = User::findOrFail($userId);
            
            $availableAchievements = Achievement::active()
                ->visible()
                ->whereNotIn('id', function ($query) use ($userId) {
                    $query->select('achievement_id')
                        ->from('user_achievements')
                        ->where('user_id', $userId);
                })
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get();

            return $availableAchievements->map(function ($achievement) use ($user) {
                $progress = $achievement->getProgressForUser($user);
                
                return [
                    'id' => $achievement->id,
                    'key' => $achievement->key,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'category' => $achievement->category,
                    'type' => $achievement->type,
                    'icon' => $achievement->icon,
                    'color' => $achievement->color,
                    'rarity' => $achievement->rarity,
                    'rarity_color' => $achievement->rarity_color,
                    'points' => $achievement->points,
                    'progress' => $progress,
                ];
            })->toArray();
        });
    }

    /**
     * Get achievement statistics
     */
    public static function getAchievementStatistics(): array
    {
        $cacheKey = 'achievement_statistics';
        
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            return [
                'total_achievements' => Achievement::count(),
                'active_achievements' => Achievement::active()->count(),
                'total_unlocks' => UserAchievement::count(),
                'unique_achievers' => UserAchievement::distinct('user_id')->count(),
                'average_achievements_per_user' => UserAchievement::count() / max(1, User::count()),
                'most_popular_achievement' => static::getMostPopularAchievement(),
                'rarest_achievement' => static::getRarestAchievement(),
                'top_achiever' => static::getTopAchiever(),
                'achievements_by_category' => static::getAchievementsByCategory(),
                'achievements_by_rarity' => static::getAchievementsByRarity(),
            ];
        });
    }

    /**
     * Get most popular achievement
     */
    private static function getMostPopularAchievement(): ?array
    {
        $mostPopular = UserAchievement::select('achievement_id', \DB::raw('COUNT(*) as unlock_count'))
            ->groupBy('achievement_id')
            ->orderBy('unlock_count', 'desc')
            ->first();

        if (!$mostPopular) {
            return null;
        }

        $achievement = Achievement::find($mostPopular->achievement_id);
        
        return $achievement ? [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'unlock_count' => $mostPopular->unlock_count,
        ] : null;
    }

    /**
     * Get rarest achievement
     */
    private static function getRarestAchievement(): ?array
    {
        $rarest = UserAchievement::select('achievement_id', \DB::raw('COUNT(*) as unlock_count'))
            ->groupBy('achievement_id')
            ->orderBy('unlock_count', 'asc')
            ->first();

        if (!$rarest) {
            return null;
        }

        $achievement = Achievement::find($rarest->achievement_id);
        
        return $achievement ? [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'unlock_count' => $rarest->unlock_count,
        ] : null;
    }

    /**
     * Get top achiever
     */
    private static function getTopAchiever(): ?array
    {
        $topAchiever = UserAchievement::select('user_id', \DB::raw('COUNT(*) as achievements_count'))
            ->groupBy('user_id')
            ->orderBy('achievements_count', 'desc')
            ->first();

        if (!$topAchiever) {
            return null;
        }

        $user = User::find($topAchiever->user_id);
        
        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'achievements_count' => $topAchiever->achievements_count,
        ] : null;
    }

    /**
     * Get achievements by category
     */
    private static function getAchievementsByCategory(): array
    {
        return Achievement::select('category', \DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Get achievements by rarity
     */
    private static function getAchievementsByRarity(): array
    {
        return Achievement::select('rarity', \DB::raw('COUNT(*) as count'))
            ->groupBy('rarity')
            ->pluck('count', 'rarity')
            ->toArray();
    }

    /**
     * Clear user achievements cache
     */
    private static function clearUserAchievementsCache(int $userId): void
    {
        $patterns = [
            "user_achievements_{$userId}_basic",
            "user_achievements_{$userId}_with_progress",
            "available_achievements_{$userId}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Seed default achievements
     */
    public static function seedDefaultAchievements(): int
    {
        $achievements = [
            // Social achievements
            [
                'key' => 'first_follower',
                'name' => 'Người theo dõi đầu tiên',
                'description' => 'Có người theo dõi đầu tiên',
                'category' => Achievement::CATEGORY_SOCIAL,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'followers_count', 'operator' => '>=', 'value' => 1]],
                'icon' => 'fas fa-user-plus',
                'color' => '#10B981',
                'points' => 10,
                'rarity' => Achievement::RARITY_COMMON,
                'sort_order' => 1,
            ],
            [
                'key' => 'popular_user',
                'name' => 'Người dùng phổ biến',
                'description' => 'Có 10 người theo dõi',
                'category' => Achievement::CATEGORY_SOCIAL,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'followers_count', 'operator' => '>=', 'value' => 10]],
                'icon' => 'fas fa-star',
                'color' => '#3B82F6',
                'points' => 50,
                'rarity' => Achievement::RARITY_UNCOMMON,
                'sort_order' => 2,
            ],
            [
                'key' => 'social_butterfly',
                'name' => 'Bướm xã hội',
                'description' => 'Theo dõi 20 người dùng',
                'category' => Achievement::CATEGORY_SOCIAL,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'following_count', 'operator' => '>=', 'value' => 20]],
                'icon' => 'fas fa-users',
                'color' => '#8B5CF6',
                'points' => 30,
                'rarity' => Achievement::RARITY_COMMON,
                'sort_order' => 3,
            ],
            
            // Content achievements
            [
                'key' => 'first_post',
                'name' => 'Bài viết đầu tiên',
                'description' => 'Tạo bài viết đầu tiên',
                'category' => Achievement::CATEGORY_CONTENT,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'posts_count', 'operator' => '>=', 'value' => 1]],
                'icon' => 'fas fa-edit',
                'color' => '#F59E0B',
                'points' => 5,
                'rarity' => Achievement::RARITY_COMMON,
                'sort_order' => 1,
            ],
            [
                'key' => 'prolific_writer',
                'name' => 'Tác giả nhiều tác phẩm',
                'description' => 'Tạo 50 bài viết',
                'category' => Achievement::CATEGORY_CONTENT,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'posts_count', 'operator' => '>=', 'value' => 50]],
                'icon' => 'fas fa-feather-alt',
                'color' => '#EF4444',
                'points' => 100,
                'rarity' => Achievement::RARITY_RARE,
                'sort_order' => 2,
            ],
            
            // Community achievements
            [
                'key' => 'helpful_member',
                'name' => 'Thành viên hữu ích',
                'description' => 'Viết 100 bình luận',
                'category' => Achievement::CATEGORY_COMMUNITY,
                'type' => Achievement::TYPE_MILESTONE,
                'criteria' => [['type' => 'comments_count', 'operator' => '>=', 'value' => 100]],
                'icon' => 'fas fa-hands-helping',
                'color' => '#06B6D4',
                'points' => 75,
                'rarity' => Achievement::RARITY_UNCOMMON,
                'sort_order' => 1,
            ],
            
            // Special achievements
            [
                'key' => 'veteran_member',
                'name' => 'Thành viên kỳ cựu',
                'description' => 'Thành viên trong 365 ngày',
                'category' => Achievement::CATEGORY_SPECIAL,
                'type' => Achievement::TYPE_SPECIAL,
                'criteria' => [['type' => 'days_since_registration', 'operator' => '>=', 'value' => 365]],
                'icon' => 'fas fa-medal',
                'color' => '#F59E0B',
                'points' => 200,
                'rarity' => Achievement::RARITY_EPIC,
                'sort_order' => 1,
            ],
        ];

        $created = 0;
        foreach ($achievements as $achievementData) {
            $existing = Achievement::where('key', $achievementData['key'])->first();
            if (!$existing) {
                Achievement::create($achievementData);
                $created++;
            }
        }

        return $created;
    }
}
