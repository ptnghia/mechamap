<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\UserFollow;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WeeklyDigestService
{
    /**
     * Generate and send weekly digest for all users
     */
    public static function sendWeeklyDigests(): array
    {
        try {
            $startTime = now();
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            
            Log::info("Starting weekly digest generation", [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
            ]);

            // Get users who want weekly digest
            $users = static::getUsersForDigest();
            
            $results = [
                'total_users' => $users->count(),
                'sent_count' => 0,
                'skipped_count' => 0,
                'error_count' => 0,
                'errors' => [],
            ];

            foreach ($users as $user) {
                try {
                    $digestData = static::generateDigestData($user, $weekStart, $weekEnd);
                    
                    if (static::shouldSendDigest($digestData)) {
                        static::sendDigestToUser($user, $digestData);
                        $results['sent_count']++;
                    } else {
                        $results['skipped_count']++;
                    }

                } catch (\Exception $e) {
                    $results['error_count']++;
                    $results['errors'][] = [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ];
                    
                    Log::error("Failed to send weekly digest to user", [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $duration = now()->diffInSeconds($startTime);
            
            Log::info("Weekly digest generation completed", [
                'duration_seconds' => $duration,
                'results' => $results,
            ]);

            return $results;

        } catch (\Exception $e) {
            Log::error("Failed to send weekly digests", [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'total_users' => 0,
                'sent_count' => 0,
                'skipped_count' => 0,
                'error_count' => 1,
                'errors' => [['error' => $e->getMessage()]],
            ];
        }
    }

    /**
     * Get users who should receive weekly digest
     */
    private static function getUsersForDigest()
    {
        return User::where('is_active', true)
            ->where('email_notifications_enabled', true)
            ->whereJsonContains('notification_preferences->weekly_digest', true)
            ->orWhereNull('notification_preferences') // Default to enabled for users without preferences
            ->get();
    }

    /**
     * Generate digest data for user
     */
    private static function generateDigestData(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $cacheKey = "weekly_digest_{$user->id}_{$weekStart->format('Y-m-d')}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $weekStart, $weekEnd) {
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'period' => [
                    'start' => $weekStart->format('d/m/Y'),
                    'end' => $weekEnd->format('d/m/Y'),
                    'week_number' => $weekStart->weekOfYear,
                ],
                'activity_summary' => static::getActivitySummary($user, $weekStart, $weekEnd),
                'new_threads' => static::getNewThreads($user, $weekStart, $weekEnd),
                'popular_threads' => static::getPopularThreads($user, $weekStart, $weekEnd),
                'new_followers' => static::getNewFollowers($user, $weekStart, $weekEnd),
                'achievements' => static::getNewAchievements($user, $weekStart, $weekEnd),
                'trending_topics' => static::getTrendingTopics($weekStart, $weekEnd),
                'community_stats' => static::getCommunityStats($weekStart, $weekEnd),
                'personalized_content' => static::getPersonalizedContent($user, $weekStart, $weekEnd),
            ];
        });
    }

    /**
     * Get activity summary for user
     */
    private static function getActivitySummary(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        return [
            'threads_created' => Thread::where('user_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count(),
            'comments_posted' => Comment::where('user_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count(),
            'new_followers' => UserFollow::where('following_id', $user->id)
                ->whereBetween('followed_at', [$weekStart, $weekEnd])
                ->count(),
            'achievements_unlocked' => UserAchievement::where('user_id', $user->id)
                ->whereBetween('unlocked_at', [$weekStart, $weekEnd])
                ->count(),
            'notifications_received' => Notification::where('user_id', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count(),
        ];
    }

    /**
     * Get new threads from followed users
     */
    private static function getNewThreads(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $followingIds = UserFollow::where('follower_id', $user->id)->pluck('following_id');
        
        if ($followingIds->isEmpty()) {
            return [];
        }

        $threads = Thread::whereIn('user_id', $followingIds)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->with(['user:id,name,avatar', 'forum:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return $threads->map(function ($thread) {
            return [
                'id' => $thread->id,
                'title' => $thread->title,
                'excerpt' => \Str::limit(strip_tags($thread->content), 150),
                'author' => [
                    'name' => $thread->user->name,
                    'avatar' => $thread->user->avatar_url,
                ],
                'forum' => $thread->forum->name,
                'created_at' => $thread->created_at->format('d/m/Y H:i'),
                'url' => "/threads/{$thread->id}",
            ];
        })->toArray();
    }

    /**
     * Get popular threads this week
     */
    private static function getPopularThreads(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $threads = Thread::whereBetween('created_at', [$weekStart, $weekEnd])
            ->with(['user:id,name,avatar', 'forum:id,name'])
            ->withCount(['comments', 'likes'])
            ->orderByDesc('comments_count')
            ->orderByDesc('likes_count')
            ->limit(5)
            ->get();

        return $threads->map(function ($thread) {
            return [
                'id' => $thread->id,
                'title' => $thread->title,
                'excerpt' => \Str::limit(strip_tags($thread->content), 150),
                'author' => [
                    'name' => $thread->user->name,
                    'avatar' => $thread->user->avatar_url,
                ],
                'forum' => $thread->forum->name,
                'stats' => [
                    'comments' => $thread->comments_count,
                    'likes' => $thread->likes_count,
                ],
                'created_at' => $thread->created_at->format('d/m/Y H:i'),
                'url' => "/threads/{$thread->id}",
            ];
        })->toArray();
    }

    /**
     * Get new followers this week
     */
    private static function getNewFollowers(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $newFollowers = UserFollow::where('following_id', $user->id)
            ->whereBetween('followed_at', [$weekStart, $weekEnd])
            ->with(['follower:id,name,avatar'])
            ->orderBy('followed_at', 'desc')
            ->limit(10)
            ->get();

        return $newFollowers->map(function ($follow) {
            return [
                'id' => $follow->follower->id,
                'name' => $follow->follower->name,
                'avatar' => $follow->follower->avatar_url,
                'followed_at' => $follow->followed_at->format('d/m/Y H:i'),
                'url' => "/users/{$follow->follower->id}",
            ];
        })->toArray();
    }

    /**
     * Get new achievements this week
     */
    private static function getNewAchievements(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        $newAchievements = UserAchievement::where('user_id', $user->id)
            ->whereBetween('unlocked_at', [$weekStart, $weekEnd])
            ->with(['achievement'])
            ->orderBy('unlocked_at', 'desc')
            ->get();

        return $newAchievements->map(function ($userAchievement) {
            $achievement = $userAchievement->achievement;
            return [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'icon' => $achievement->icon,
                'color' => $achievement->color,
                'rarity' => $achievement->rarity,
                'points' => $achievement->points,
                'unlocked_at' => $userAchievement->unlocked_at->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

    /**
     * Get trending topics this week
     */
    private static function getTrendingTopics(Carbon $weekStart, Carbon $weekEnd): array
    {
        // Get most active forums this week
        $trendingForums = \DB::table('threads')
            ->join('forums', 'threads.forum_id', '=', 'forums.id')
            ->whereBetween('threads.created_at', [$weekStart, $weekEnd])
            ->select('forums.id', 'forums.name', \DB::raw('COUNT(threads.id) as thread_count'))
            ->groupBy('forums.id', 'forums.name')
            ->orderBy('thread_count', 'desc')
            ->limit(5)
            ->get();

        return $trendingForums->map(function ($forum) {
            return [
                'id' => $forum->id,
                'name' => $forum->name,
                'thread_count' => $forum->thread_count,
                'url' => "/forums/{$forum->id}",
            ];
        })->toArray();
    }

    /**
     * Get community statistics
     */
    private static function getCommunityStats(Carbon $weekStart, Carbon $weekEnd): array
    {
        return [
            'new_threads' => Thread::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'new_comments' => Comment::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'new_users' => User::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            'new_follows' => UserFollow::whereBetween('followed_at', [$weekStart, $weekEnd])->count(),
            'achievements_unlocked' => UserAchievement::whereBetween('unlocked_at', [$weekStart, $weekEnd])->count(),
        ];
    }

    /**
     * Get personalized content recommendations
     */
    private static function getPersonalizedContent(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        // Get user's most active forums
        $activeForums = \DB::table('threads')
            ->join('forums', 'threads.forum_id', '=', 'forums.id')
            ->where('threads.user_id', $user->id)
            ->select('forums.id', 'forums.name', \DB::raw('COUNT(threads.id) as activity_count'))
            ->groupBy('forums.id', 'forums.name')
            ->orderBy('activity_count', 'desc')
            ->limit(3)
            ->get();

        $recommendations = [];
        
        foreach ($activeForums as $forum) {
            $recentThreads = Thread::where('forum_id', $forum->id)
                ->where('user_id', '!=', $user->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->with(['user:id,name'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            if ($recentThreads->isNotEmpty()) {
                $recommendations[] = [
                    'forum' => [
                        'id' => $forum->id,
                        'name' => $forum->name,
                    ],
                    'threads' => $recentThreads->map(function ($thread) {
                        return [
                            'id' => $thread->id,
                            'title' => $thread->title,
                            'author' => $thread->user->name,
                            'url' => "/threads/{$thread->id}",
                        ];
                    })->toArray(),
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Check if digest should be sent
     */
    private static function shouldSendDigest(array $digestData): bool
    {
        $activitySummary = $digestData['activity_summary'];
        $hasNewContent = !empty($digestData['new_threads']) || 
                        !empty($digestData['popular_threads']) ||
                        !empty($digestData['new_followers']) ||
                        !empty($digestData['achievements']);
        
        $hasActivity = $activitySummary['threads_created'] > 0 ||
                      $activitySummary['comments_posted'] > 0 ||
                      $activitySummary['new_followers'] > 0 ||
                      $activitySummary['achievements_unlocked'] > 0;

        // Send if user has activity or there's new content to show
        return $hasActivity || $hasNewContent;
    }

    /**
     * Send digest to user
     */
    private static function sendDigestToUser(User $user, array $digestData): void
    {
        // Create notification record
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'weekly_digest',
            'title' => 'Tóm tắt hoạt động tuần',
            'message' => "Tóm tắt hoạt động tuần {$digestData['period']['week_number']} của bạn",
            'data' => [
                'digest_data' => $digestData,
                'week_start' => $digestData['period']['start'],
                'week_end' => $digestData['period']['end'],
                'action_url' => '/notifications',
                'action_text' => 'Xem chi tiết',
            ],
            'priority' => 'low',
        ]);

        // Send email (placeholder - would implement actual email sending)
        // Mail::to($user->email)->send(new WeeklyDigestMail($digestData));

        Log::info("Weekly digest sent to user", [
            'user_id' => $user->id,
            'notification_id' => $notification->id,
            'digest_summary' => [
                'threads_created' => $digestData['activity_summary']['threads_created'],
                'comments_posted' => $digestData['activity_summary']['comments_posted'],
                'new_followers' => $digestData['activity_summary']['new_followers'],
                'achievements_unlocked' => $digestData['activity_summary']['achievements_unlocked'],
            ],
        ]);
    }

    /**
     * Get digest statistics
     */
    public static function getDigestStatistics(): array
    {
        $cacheKey = 'weekly_digest_statistics';
        
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $lastWeekStart = now()->subWeek()->startOfWeek();
            $lastWeekEnd = now()->subWeek()->endOfWeek();

            return [
                'total_users_eligible' => User::where('is_active', true)
                    ->where('email_notifications_enabled', true)
                    ->count(),
                'last_week_sent' => Notification::where('type', 'weekly_digest')
                    ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                    ->count(),
                'total_digests_sent' => Notification::where('type', 'weekly_digest')->count(),
                'average_weekly_sends' => Notification::where('type', 'weekly_digest')
                    ->where('created_at', '>=', now()->subWeeks(4))
                    ->count() / 4,
                'engagement_rate' => static::calculateEngagementRate(),
            ];
        });
    }

    /**
     * Calculate digest engagement rate
     */
    private static function calculateEngagementRate(): float
    {
        $totalSent = Notification::where('type', 'weekly_digest')
            ->where('created_at', '>=', now()->subWeeks(4))
            ->count();

        if ($totalSent === 0) {
            return 0;
        }

        $totalRead = Notification::where('type', 'weekly_digest')
            ->where('created_at', '>=', now()->subWeeks(4))
            ->where('is_read', true)
            ->count();

        return round(($totalRead / $totalSent) * 100, 2);
    }
}
