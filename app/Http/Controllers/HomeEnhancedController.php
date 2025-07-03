<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeEnhancedController extends Controller
{
    /**
     * Display the enhanced home page.
     */
    public function index(): View
    {
        // Get latest discussions
        $latestThreads = $this->getLatestThreads();

        // Get top contributors
        $topContributors = $this->getTopContributors();

        // Get community stats
        $communityStats = $this->getCommunityStats();

        return view('home-new', compact(
            'latestThreads',
            'topContributors',
            'communityStats'
        ));
    }

    /**
     * Get latest threads for featured content.
     */
    private function getLatestThreads()
    {
        return Cache::remember('home_latest_threads', 300, function() {
            return Thread::with(['user', 'forum'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'content' => $thread->content,
                        'user' => [
                            'name' => $thread->user->name,
                            'avatar_url' => $thread->user->getAvatarUrl(),
                        ],
                        'forum' => [
                            'name' => $thread->forum->name,
                        ],
                        'view_count' => $thread->view_count ?? 0,
                        'posts_count' => $thread->posts()->count(),
                        'created_at' => $thread->created_at,
                    ];
                });
        });
    }

    /**
     * Get top contributors for sidebar.
     */
    private function getTopContributors()
    {
        return Cache::remember('home_top_contributors', 600, function() {
            return User::select('users.*')
                ->selectRaw('
                    (
                        (SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id) * 5 +
                        (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id) * 2
                    ) as points
                ')
                ->having('points', '>', 0)
                ->orderBy('points', 'desc')
                ->limit(5)
                ->get()
                ->map(function($user) {
                    return [
                        'name' => $user->name,
                        'avatar_url' => $user->getAvatarUrl(),
                        'points' => $user->points ?? 0,
                    ];
                });
        });
    }

    /**
     * Get community statistics.
     */
    private function getCommunityStats()
    {
        return Cache::remember('home_community_stats', 1800, function() {
            return [
                'total_users' => User::count(),
                'total_threads' => Thread::count(),
                'total_posts' => Post::count(),
                'active_users_today' => User::whereDate('created_at', today())->count(),
                'new_threads_today' => Thread::whereDate('created_at', today())->count(),
                'total_forums' => Forum::count(),
            ];
        });
    }

    /**
     * Get live activity feed data via AJAX.
     */
    public function getLiveActivity(Request $request)
    {
        $activities = Cache::remember('live_activities', 60, function() {
            $activities = collect();

            // Recent threads
            $recentThreads = Thread::with('user')
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentThreads as $thread) {
                $activities->push([
                    'type' => 'new_thread',
                    'user' => $thread->user->name,
                    'action' => __('home.created_new_thread'),
                    'title' => $thread->title,
                    'time' => $thread->created_at->diffForHumans(),
                    'avatar' => $thread->user->getAvatarUrl(),
                ]);
            }

            // Recent posts
            $recentPosts = Post::with(['user', 'thread'])
                ->where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($recentPosts as $post) {
                $activities->push([
                    'type' => 'new_answer',
                    'user' => $post->user->name,
                    'action' => __('home.provided_helpful_answer'),
                    'title' => $post->thread->title,
                    'time' => $post->created_at->diffForHumans(),
                    'avatar' => $post->user->getAvatarUrl(),
                ]);
            }

            // New users
            $newUsers = User::where('created_at', '>=', now()->subHours(24))
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($newUsers as $user) {
                $activities->push([
                    'type' => 'new_member',
                    'user' => $user->name,
                    'action' => __('home.joined_community'),
                    'title' => '',
                    'time' => $user->created_at->diffForHumans(),
                    'avatar' => $user->getAvatarUrl(),
                ]);
            }

            return $activities->sortByDesc('time')->take(6)->values();
        });

        return response()->json($activities);
    }

    /**
     * Handle newsletter subscription.
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        // Here you would typically save to a newsletter service
        // For now, we'll just return success

        // Log the subscription
        \Log::info('Newsletter subscription', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('home.newsletter_success'),
        ]);
    }

    /**
     * Get trending topics for the week.
     */
    public function getTrendingTopics()
    {
        return Cache::remember('trending_topics', 3600, function() {
            return Thread::select('threads.*')
                ->selectRaw('
                    (threads.view_count * 0.1 +
                     (SELECT COUNT(*) FROM posts WHERE posts.thread_id = threads.id) * 2) as trend_score
                ')
                ->where('created_at', '>=', now()->subWeek())
                ->orderBy('trend_score', 'desc')
                ->limit(10)
                ->get();
        });
    }

    /**
     * Get featured engineers of the month.
     */
    public function getFeaturedEngineers()
    {
        return Cache::remember('featured_engineers', 86400, function() {
            return User::select('users.*')
                ->selectRaw('
                    (
                        (SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id AND threads.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) * 5 +
                        (SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id AND posts.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) * 2
                    ) as monthly_score
                ')
                ->having('monthly_score', '>', 0)
                ->orderBy('monthly_score', 'desc')
                ->limit(3)
                ->get();
        });
    }

    /**
     * Get quick stats for dashboard widgets.
     */
    public function getQuickStats()
    {
        return Cache::remember('quick_stats', 300, function() {
            return [
                'online_users' => User::where('created_at', '>=', now()->subMinutes(15))->count(),
                'active_discussions' => Thread::where('updated_at', '>=', now()->subHours(24))->count(),
                'new_members_today' => User::whereDate('created_at', today())->count(),
                'questions_answered_today' => Post::whereDate('created_at', today())->count(),
            ];
        });
    }

    /**
     * Search suggestions for quick search.
     */
    public function getSearchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Cache::remember("search_suggestions_{$query}", 300, function() use ($query) {
            $threads = Thread::where('title', 'like', "%{$query}%")
                ->orderBy('view_count', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'view_count']);

            $forums = Forum::where('name', 'like', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'description']);

            return [
                'threads' => $threads,
                'forums' => $forums,
            ];
        });

        return response()->json($suggestions);
    }
}
