<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ForumCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Cache global forum stats for all forum pages
        $globalForumStats = Cache::remember('forum.global.stats', 3600, function () {
            return [
                'total_forums' => \App\Models\Forum::count(),
                'total_threads' => \App\Models\Thread::count(),
                'total_posts' => \App\Models\Post::count(),
                'total_users' => \App\Models\User::count(),
                'online_users' => $this->getOnlineUsersCount(),
                'newest_member' => \App\Models\User::latest()->first(),
            ];
        });

        // Share with all views
        View::share('globalForumStats', $globalForumStats);

        return $next($request);
    }

    /**
     * Get count of online users (active in last 15 minutes)
     */
    private function getOnlineUsersCount(): int
    {
        return Cache::remember('forum.online.users.count', 300, function () {
            return \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(15))->count();
        });
    }
}
