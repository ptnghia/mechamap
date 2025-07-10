<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFollow;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UserFollowService
{
    /**
     * Follow a user
     */
    public static function followUser(User $follower, User $following): array
    {
        try {
            // Validate follow request
            $validation = static::validateFollowRequest($follower, $following);
            if (!$validation['success']) {
                return $validation;
            }

            // Create follow relationship
            $userFollow = UserFollow::create([
                'follower_id' => $follower->id,
                'following_id' => $following->id,
                'followed_at' => now(),
            ]);

            // Send notification to the followed user
            static::sendFollowNotification($follower, $following);

            // Clear cache
            static::clearFollowCache($follower->id, $following->id);

            Log::info("User followed successfully", [
                'follower_id' => $follower->id,
                'following_id' => $following->id,
                'follow_id' => $userFollow->id,
            ]);

            return [
                'success' => true,
                'message' => 'Đã theo dõi người dùng thành công',
                'follow_id' => $userFollow->id,
                'followers_count' => static::getFollowersCount($following->id),
                'following_count' => static::getFollowingCount($follower->id),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to follow user", [
                'follower_id' => $follower->id,
                'following_id' => $following->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Không thể theo dõi người dùng',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Unfollow a user
     */
    public static function unfollowUser(User $follower, User $following): array
    {
        try {
            $deleted = UserFollow::where('follower_id', $follower->id)
                ->where('following_id', $following->id)
                ->delete();

            if ($deleted === 0) {
                return [
                    'success' => false,
                    'message' => 'Bạn chưa theo dõi người dùng này',
                ];
            }

            // Clear cache
            static::clearFollowCache($follower->id, $following->id);

            Log::info("User unfollowed successfully", [
                'follower_id' => $follower->id,
                'following_id' => $following->id,
            ]);

            return [
                'success' => true,
                'message' => 'Đã hủy theo dõi người dùng thành công',
                'followers_count' => static::getFollowersCount($following->id),
                'following_count' => static::getFollowingCount($follower->id),
            ];

        } catch (\Exception $e) {
            Log::error("Failed to unfollow user", [
                'follower_id' => $follower->id,
                'following_id' => $following->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Không thể hủy theo dõi người dùng',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate follow request
     */
    private static function validateFollowRequest(User $follower, User $following): array
    {
        // Can't follow yourself
        if ($follower->id === $following->id) {
            return [
                'success' => false,
                'message' => 'Bạn không thể theo dõi chính mình',
            ];
        }

        // Check if already following
        if (UserFollow::isFollowing($follower->id, $following->id)) {
            return [
                'success' => false,
                'message' => 'Bạn đã theo dõi người dùng này rồi',
            ];
        }

        // Check if following user is active
        if (!$following->is_active) {
            return [
                'success' => false,
                'message' => 'Không thể theo dõi người dùng không hoạt động',
            ];
        }

        return ['success' => true];
    }

    /**
     * Send follow notification
     */
    private static function sendFollowNotification(User $follower, User $following): void
    {
        try {
            $notificationData = [
                'user_id' => $following->id,
                'type' => 'user_followed',
                'title' => 'Có người theo dõi bạn',
                'message' => "{$follower->name} đã bắt đầu theo dõi bạn",
                'data' => [
                    'follower_id' => $follower->id,
                    'follower_name' => $follower->name,
                    'follower_avatar' => $follower->avatar_url,
                    'action_url' => "/users/{$follower->id}",
                    'action_text' => 'Xem hồ sơ',
                ],
                'priority' => 'normal',
            ];

            Notification::create($notificationData);

        } catch (\Exception $e) {
            Log::error("Failed to send follow notification", [
                'follower_id' => $follower->id,
                'following_id' => $following->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get followers for user
     */
    public static function getFollowers(int $userId, int $limit = 20, int $offset = 0): array
    {
        $cacheKey = "user_followers_{$userId}_{$limit}_{$offset}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($userId, $limit, $offset) {
            $followers = UserFollow::where('following_id', $userId)
                ->with(['follower:id,name,email,avatar,created_at'])
                ->orderBy('followed_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($follow) {
                    return [
                        'id' => $follow->follower->id,
                        'name' => $follow->follower->name,
                        'email' => $follow->follower->email,
                        'avatar' => $follow->follower->avatar_url,
                        'followed_at' => $follow->followed_at,
                        'member_since' => $follow->follower->created_at,
                    ];
                });

            return $followers->toArray();
        });
    }

    /**
     * Get following for user
     */
    public static function getFollowing(int $userId, int $limit = 20, int $offset = 0): array
    {
        $cacheKey = "user_following_{$userId}_{$limit}_{$offset}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($userId, $limit, $offset) {
            $following = UserFollow::where('follower_id', $userId)
                ->with(['following:id,name,email,avatar,created_at'])
                ->orderBy('followed_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get()
                ->map(function ($follow) {
                    return [
                        'id' => $follow->following->id,
                        'name' => $follow->following->name,
                        'email' => $follow->following->email,
                        'avatar' => $follow->following->avatar_url,
                        'followed_at' => $follow->followed_at,
                        'member_since' => $follow->following->created_at,
                    ];
                });

            return $following->toArray();
        });
    }

    /**
     * Get followers count
     */
    public static function getFollowersCount(int $userId): int
    {
        $cacheKey = "user_followers_count_{$userId}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($userId) {
            return UserFollow::getFollowersCount($userId);
        });
    }

    /**
     * Get following count
     */
    public static function getFollowingCount(int $userId): int
    {
        $cacheKey = "user_following_count_{$userId}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($userId) {
            return UserFollow::getFollowingCount($userId);
        });
    }

    /**
     * Check if user is following another user
     */
    public static function isFollowing(int $followerId, int $followingId): bool
    {
        $cacheKey = "is_following_{$followerId}_{$followingId}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($followerId, $followingId) {
            return UserFollow::isFollowing($followerId, $followingId);
        });
    }

    /**
     * Get mutual followers
     */
    public static function getMutualFollowers(int $userId1, int $userId2): array
    {
        $followers1 = UserFollow::where('following_id', $userId1)->pluck('follower_id');
        $followers2 = UserFollow::where('following_id', $userId2)->pluck('follower_id');

        $mutualFollowerIds = $followers1->intersect($followers2);

        if ($mutualFollowerIds->isEmpty()) {
            return [];
        }

        $mutualFollowers = User::whereIn('id', $mutualFollowerIds)
            ->select('id', 'name', 'email', 'avatar')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                ];
            });

        return $mutualFollowers->toArray();
    }

    /**
     * Get follow suggestions for user
     */
    public static function getFollowSuggestions(int $userId, int $limit = 10): array
    {
        $cacheKey = "follow_suggestions_{$userId}_{$limit}";

        return Cache::remember($cacheKey, now()->addHours(2), function () use ($userId, $limit) {
            // Get users that current user's following are also following
            $followingIds = UserFollow::where('follower_id', $userId)->pluck('following_id');

            if ($followingIds->isEmpty()) {
                // If user is not following anyone, suggest popular users
                return static::getPopularUsers($userId, $limit);
            }

            $suggestions = UserFollow::whereIn('follower_id', $followingIds)
                ->where('following_id', '!=', $userId)
                ->whereNotIn('following_id', function ($query) use ($userId) {
                    $query->select('following_id')
                        ->from('user_follows')
                        ->where('follower_id', $userId);
                })
                ->select('following_id', \DB::raw('COUNT(*) as mutual_count'))
                ->groupBy('following_id')
                ->orderBy('mutual_count', 'desc')
                ->limit($limit)
                ->get();

            $userIds = $suggestions->pluck('following_id');

            $users = User::whereIn('id', $userIds)
                ->select('id', 'name', 'email', 'avatar', 'created_at')
                ->get()
                ->map(function ($user) use ($suggestions) {
                    $suggestion = $suggestions->firstWhere('following_id', $user->id);
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar_url,
                        'mutual_followers' => $suggestion->mutual_count,
                        'member_since' => $user->created_at,
                    ];
                });

            return $users->toArray();
        });
    }

    /**
     * Get popular users
     */
    private static function getPopularUsers(int $excludeUserId, int $limit): array
    {
        $popularUsers = UserFollow::select('following_id', \DB::raw('COUNT(*) as followers_count'))
            ->where('following_id', '!=', $excludeUserId)
            ->groupBy('following_id')
            ->orderBy('followers_count', 'desc')
            ->limit($limit)
            ->get();

        $userIds = $popularUsers->pluck('following_id');

        $users = User::whereIn('id', $userIds)
            ->select('id', 'name', 'email', 'avatar', 'created_at')
            ->get()
            ->map(function ($user) use ($popularUsers) {
                $popular = $popularUsers->firstWhere('following_id', $user->id);
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                    'followers_count' => $popular->followers_count,
                    'member_since' => $user->created_at,
                ];
            });

        return $users->toArray();
    }

    /**
     * Clear follow cache
     */
    private static function clearFollowCache(int $followerId, int $followingId): void
    {
        $patterns = [
            "user_followers_{$followingId}_*",
            "user_following_{$followerId}_*",
            "user_followers_count_{$followingId}",
            "user_following_count_{$followerId}",
            "is_following_{$followerId}_{$followingId}",
            "follow_suggestions_{$followerId}_*",
            "follow_suggestions_{$followingId}_*",
        ];

        foreach ($patterns as $pattern) {
            // This would depend on your cache driver
            // For now, just forget specific keys
            Cache::forget(str_replace('*', '20_0', $pattern));
        }
    }

    /**
     * Get follow statistics
     */
    public static function getFollowStatistics(): array
    {
        $cacheKey = 'follow_statistics';

        return Cache::remember($cacheKey, now()->addHours(1), function () {
            return [
                'total_follows' => UserFollow::count(),
                'total_users_with_followers' => UserFollow::distinct('following_id')->count(),
                'total_users_following' => UserFollow::distinct('follower_id')->count(),
                'average_followers_per_user' => UserFollow::count() / max(1, User::count()),
                'most_followed_user' => static::getMostFollowedUser(),
                'most_active_follower' => static::getMostActiveFollower(),
            ];
        });
    }

    /**
     * Get most followed user
     */
    private static function getMostFollowedUser(): ?array
    {
        $mostFollowed = UserFollow::select('following_id', \DB::raw('COUNT(*) as followers_count'))
            ->groupBy('following_id')
            ->orderBy('followers_count', 'desc')
            ->first();

        if (!$mostFollowed) {
            return null;
        }

        $user = User::find($mostFollowed->following_id);

        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'followers_count' => $mostFollowed->followers_count,
        ] : null;
    }

    /**
     * Get most active follower
     */
    private static function getMostActiveFollower(): ?array
    {
        $mostActive = UserFollow::select('follower_id', \DB::raw('COUNT(*) as following_count'))
            ->groupBy('follower_id')
            ->orderBy('following_count', 'desc')
            ->first();

        if (!$mostActive) {
            return null;
        }

        $user = User::find($mostActive->follower_id);

        return $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'following_count' => $mostActive->following_count,
        ] : null;
    }
}
