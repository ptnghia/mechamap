<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\Forum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CommunityStatsController extends Controller
{
    /**
     * Get quick stats for mega menu
     */
    public function getQuickStats(): JsonResponse
    {
        $stats = Cache::remember('community_quick_stats', 300, function () {
            return [
                'online_users' => $this->getOnlineUsersCount(),
                'today_posts' => $this->getTodayPostsCount(),
                'trending_topics' => $this->getTrendingTopicsCount(),
                'featured_discussions' => $this->getFeaturedDiscussionsCount(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get online users count
     */
    public function getOnlineUsersCount(): JsonResponse
    {
        $count = Cache::remember('online_users_count', 60, function () {
            // Users active in last 15 minutes
            return User::where('last_activity', '>=', now()->subMinutes(15))->count();
        });

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(): JsonResponse
    {
        $activities = Cache::remember('recent_activity', 300, function () {
            $recentThreads = Thread::with(['user', 'forum'])
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($thread) {
                    return [
                        'type' => 'thread',
                        'title' => $thread->title,
                        'user' => $thread->user->name,
                        'forum' => $thread->forum->name ?? 'General',
                        'time' => $thread->created_at->diffForHumans(),
                        'url' => route('threads.show', $thread->id)
                    ];
                });

            $recentComments = Comment::with(['user', 'thread'])
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($comment) {
                    return [
                        'type' => 'comment',
                        'title' => 'Replied to: ' . $comment->thread->title,
                        'user' => $comment->user->name,
                        'forum' => $comment->thread->forum->name ?? 'General',
                        'time' => $comment->created_at->diffForHumans(),
                        'url' => route('threads.show', $comment->thread->id)
                    ];
                });

            return $recentThreads->concat($recentComments)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();
        });

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Get popular forums
     */
    public function getPopularForums(): JsonResponse
    {
        $forums = Cache::remember('popular_forums', 3600, function () {
            return Forum::withCount(['threads', 'comments'])
                ->orderBy('threads_count', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($forum) {
                    return [
                        'id' => $forum->id,
                        'name' => $forum->name,
                        'description' => $forum->description,
                        'threads_count' => $forum->threads_count,
                        'comments_count' => $forum->comments_count,
                        'url' => route('forums.show', $forum->id),
                        'icon' => $forum->icon ?? 'fa-solid fa-comments'
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $forums
        ]);
    }

    /**
     * Get trending topics
     */
    public function getTrendingTopics(): JsonResponse
    {
        $topics = Cache::remember('trending_topics', 1800, function () {
            return Thread::with(['user', 'forum'])
                ->withCount('comments')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('comments_count', 'desc')
                ->orderBy('views', 'desc')
                ->limit(8)
                ->get()
                ->map(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'user' => $thread->user->name,
                        'forum' => $thread->forum->name ?? 'General',
                        'comments_count' => $thread->comments_count,
                        'views' => $thread->views ?? 0,
                        'created_at' => $thread->created_at->diffForHumans(),
                        'url' => route('threads.show', $thread->id)
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    /**
     * Private helper methods
     */
    private function getTodayPostsCount(): int
    {
        return Thread::whereDate('created_at', today())->count() +
               Comment::whereDate('created_at', today())->count();
    }

    private function getTrendingTopicsCount(): int
    {
        return Thread::where('created_at', '>=', now()->subDays(7))
            ->withCount('comments')
            ->having('comments_count', '>=', 3)
            ->count();
    }

    private function getFeaturedDiscussionsCount(): int
    {
        return Thread::where('is_featured', true)
            ->orWhere('is_pinned', true)
            ->count();
    }

    /**
     * Get community overview stats
     */
    public function getOverviewStats(): JsonResponse
    {
        $stats = Cache::remember('community_overview_stats', 3600, function () {
            return [
                'total_users' => User::count(),
                'total_threads' => Thread::count(),
                'total_comments' => Comment::count(),
                'total_forums' => Forum::count(),
                'active_users_today' => User::whereDate('last_activity', today())->count(),
                'new_threads_today' => Thread::whereDate('created_at', today())->count(),
                'new_comments_today' => Comment::whereDate('created_at', today())->count(),
                'top_contributors' => User::withCount(['threads', 'comments'])
                    ->orderByRaw('threads_count + comments_count DESC')
                    ->limit(5)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'name' => $user->name,
                            'avatar' => $user->avatar_url,
                            'total_posts' => $user->threads_count + $user->comments_count,
                            'profile_url' => route('profile.show', $user->username)
                        ];
                    })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Clear stats cache (for admin use)
     */
    public function clearStatsCache(): JsonResponse
    {
        $keys = [
            'community_quick_stats',
            'online_users_count',
            'recent_activity',
            'popular_forums',
            'trending_topics',
            'community_overview_stats'
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stats cache cleared successfully'
        ]);
    }
}
