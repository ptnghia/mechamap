<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AchievementController extends Controller
{
    /**
     * Get user's achievements
     */
    public function userAchievements(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'include_progress' => 'boolean',
        ]);

        $includeProgress = $request->boolean('include_progress', false);
        $achievements = AchievementService::getUserAchievements($user->id, $includeProgress);

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $achievements,
                'total_count' => count($achievements),
                'total_points' => collect($achievements)->sum('points'),
                'achievements_by_rarity' => $user->getAchievementsByRarity(),
            ],
        ]);
    }

    /**
     * Get current user's achievements
     */
    public function myAchievements(Request $request): JsonResponse
    {
        $user = auth()->user();
        $request->validate([
            'include_progress' => 'boolean',
        ]);

        $includeProgress = $request->boolean('include_progress', false);
        $achievements = AchievementService::getUserAchievements($user->id, $includeProgress);

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $achievements,
                'total_count' => count($achievements),
                'total_points' => collect($achievements)->sum('points'),
                'achievements_by_rarity' => $user->getAchievementsByRarity(),
            ],
        ]);
    }

    /**
     * Get available achievements for user
     */
    public function availableAchievements(Request $request, User $user): JsonResponse
    {
        $availableAchievements = AchievementService::getAvailableAchievements($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $availableAchievements,
                'total_count' => count($availableAchievements),
            ],
        ]);
    }

    /**
     * Get available achievements for current user
     */
    public function myAvailableAchievements(Request $request): JsonResponse
    {
        $user = auth()->user();
        $availableAchievements = AchievementService::getAvailableAchievements($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $availableAchievements,
                'total_count' => count($availableAchievements),
            ],
        ]);
    }

    /**
     * Check achievements for current user
     */
    public function checkAchievements(Request $request): JsonResponse
    {
        $user = auth()->user();
        $unlockedAchievements = AchievementService::checkAchievements($user);

        return response()->json([
            'success' => true,
            'data' => [
                'unlocked_achievements' => $unlockedAchievements,
                'count' => count($unlockedAchievements),
                'message' => count($unlockedAchievements) > 0 ? 
                    'Chúc mừng! Bạn đã mở khóa ' . count($unlockedAchievements) . ' thành tựu mới!' :
                    'Không có thành tựu mới được mở khóa.',
            ],
        ]);
    }

    /**
     * Get all achievements (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'string|in:social,content,marketplace,community,special',
            'type' => 'string|in:milestone,badge,streak,special',
            'rarity' => 'string|in:common,uncommon,rare,epic,legendary',
            'is_active' => 'boolean',
            'is_hidden' => 'boolean',
        ]);

        $query = Achievement::query();

        if ($request->has('category')) {
            $query->category($request->category);
        }

        if ($request->has('type')) {
            $query->type($request->type);
        }

        if ($request->has('rarity')) {
            $query->rarity($request->rarity);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_hidden')) {
            $query->where('is_hidden', $request->boolean('is_hidden'));
        }

        $achievements = $query->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->map(function ($achievement) {
                return [
                    'id' => $achievement->id,
                    'key' => $achievement->key,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'category' => $achievement->category,
                    'type' => $achievement->type,
                    'criteria' => $achievement->criteria,
                    'icon' => $achievement->icon,
                    'color' => $achievement->color,
                    'rarity' => $achievement->rarity,
                    'rarity_color' => $achievement->rarity_color,
                    'points' => $achievement->points,
                    'is_active' => $achievement->is_active,
                    'is_hidden' => $achievement->is_hidden,
                    'sort_order' => $achievement->sort_order,
                    'unlock_count' => $achievement->userAchievements()->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'achievements' => $achievements,
                'total_count' => $achievements->count(),
            ],
        ]);
    }

    /**
     * Get achievement statistics (admin)
     */
    public function statistics(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('view_admin_statistics')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $statistics = AchievementService::getAchievementStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get achievement leaderboard
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'category' => 'string|in:social,content,marketplace,community,special',
        ]);

        $limit = $request->input('limit', 20);
        $category = $request->input('category');

        $query = \DB::table('user_achievements')
            ->join('users', 'user_achievements.user_id', '=', 'users.id')
            ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
            ->select(
                'users.id',
                'users.name',
                'users.avatar',
                \DB::raw('COUNT(user_achievements.id) as achievements_count'),
                \DB::raw('SUM(achievements.points) as total_points')
            )
            ->groupBy('users.id', 'users.name', 'users.avatar');

        if ($category) {
            $query->where('achievements.category', $category);
        }

        $leaderboard = $query->orderBy('total_points', 'desc')
            ->orderBy('achievements_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'achievements_count' => $user->achievements_count,
                    'total_points' => $user->total_points,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'leaderboard' => $leaderboard,
                'category' => $category,
                'limit' => $limit,
            ],
        ]);
    }

    /**
     * Seed default achievements (admin)
     */
    public function seedAchievements(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('manage_achievements')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $created = AchievementService::seedDefaultAchievements();

        return response()->json([
            'success' => true,
            'data' => [
                'created_count' => $created,
                'message' => "Đã tạo {$created} thành tựu mặc định",
            ],
        ]);
    }
}
