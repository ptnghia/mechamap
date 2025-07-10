<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserFollowService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserFollowController extends Controller
{
    /**
     * Follow a user
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $request->validate([
            // No additional validation needed, user is resolved by route model binding
        ]);

        $follower = auth()->user();
        
        $result = UserFollowService::followUser($follower, $user);
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Unfollow a user
     */
    public function unfollow(Request $request, User $user): JsonResponse
    {
        $follower = auth()->user();
        
        $result = UserFollowService::unfollowUser($follower, $user);
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get user's followers
     */
    public function followers(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $followers = UserFollowService::getFollowers($user->id, $limit, $offset);
        $followersCount = UserFollowService::getFollowersCount($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'followers' => $followers,
                'total_count' => $followersCount,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => count($followers) === $limit,
            ],
        ]);
    }

    /**
     * Get user's following
     */
    public function following(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        $following = UserFollowService::getFollowing($user->id, $limit, $offset);
        $followingCount = UserFollowService::getFollowingCount($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'following' => $following,
                'total_count' => $followingCount,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => count($following) === $limit,
            ],
        ]);
    }

    /**
     * Check if current user is following another user
     */
    public function isFollowing(Request $request, User $user): JsonResponse
    {
        $currentUser = auth()->user();
        
        if (!$currentUser) {
            return response()->json([
                'success' => true,
                'is_following' => false,
            ]);
        }

        $isFollowing = UserFollowService::isFollowing($currentUser->id, $user->id);

        return response()->json([
            'success' => true,
            'is_following' => $isFollowing,
            'followers_count' => UserFollowService::getFollowersCount($user->id),
            'following_count' => UserFollowService::getFollowingCount($user->id),
        ]);
    }

    /**
     * Get mutual followers between current user and another user
     */
    public function mutualFollowers(Request $request, User $user): JsonResponse
    {
        $currentUser = auth()->user();
        
        $mutualFollowers = UserFollowService::getMutualFollowers($currentUser->id, $user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'mutual_followers' => $mutualFollowers,
                'count' => count($mutualFollowers),
            ],
        ]);
    }

    /**
     * Get follow suggestions for current user
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
        ]);

        $currentUser = auth()->user();
        $limit = $request->input('limit', 10);

        $suggestions = UserFollowService::getFollowSuggestions($currentUser->id, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'suggestions' => $suggestions,
                'count' => count($suggestions),
            ],
        ]);
    }

    /**
     * Get follow statistics (admin only)
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

        $statistics = UserFollowService::getFollowStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Bulk follow/unfollow operations
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:follow,unfollow',
            'user_ids' => 'required|array|min:1|max:50',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $currentUser = auth()->user();
        $action = $request->input('action');
        $userIds = $request->input('user_ids');

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($userIds as $userId) {
            try {
                $targetUser = User::findOrFail($userId);
                
                if ($action === 'follow') {
                    $result = UserFollowService::followUser($currentUser, $targetUser);
                } else {
                    $result = UserFollowService::unfollowUser($currentUser, $targetUser);
                }

                $results[] = [
                    'user_id' => $userId,
                    'success' => $result['success'],
                    'message' => $result['message'],
                ];

                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $userId,
                    'success' => false,
                    'message' => 'User not found or error occurred',
                ];
                $errorCount++;
            }
        }

        return response()->json([
            'success' => $successCount > 0,
            'data' => [
                'results' => $results,
                'summary' => [
                    'total' => count($userIds),
                    'success' => $successCount,
                    'errors' => $errorCount,
                ],
            ],
        ]);
    }
}
