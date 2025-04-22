<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get latest threads
        $latestThreads = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments')
            ->latest()
            ->take(10)
            ->get();

        // Get featured threads (sticky or with most views)
        $featuredThreads = Thread::with(['user'])
            ->where('is_featured', true)
            ->orWhere('is_sticky', true)
            ->orWhere(function ($query) {
                $query->where('view_count', '>', 100);
            })
            ->latest()
            ->take(4)
            ->get();

        // Get top forums with thread count
        $topForums = Forum::select('forums.*', DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.forum_id = forums.id) as threads_count'))
            ->orderBy('threads_count', 'desc')
            ->take(5)
            ->get();

        // Get categories with thread count
        $categories = Category::select('categories.*', DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.category_id = categories.id) as threads_count'))
            ->orderBy('threads_count', 'desc')
            ->take(10)
            ->get();

        // Get top contributors this month
        $topContributors = User::select(
            'users.*',
            DB::raw('(SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id AND threads.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as threads_count'),
            DB::raw('(SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) as comments_count'),
            DB::raw('((SELECT COUNT(*) FROM threads WHERE threads.user_id = users.id AND threads.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)) +
                         (SELECT COUNT(*) FROM comments WHERE comments.user_id = users.id AND comments.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH))) as contribution_count')
        )
            ->orderBy('contribution_count', 'desc')
            ->take(5)
            ->get();

        return view('home', compact(
            'latestThreads',
            'featuredThreads',
            'topForums',
            'categories',
            'topContributors'
        ));
    }

    /**
     * API endpoint to get more threads for infinite scrolling.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMoreThreads(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;

        $threads = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments as comments_count')
            ->latest()
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1) // Take one extra to check if there are more
            ->get();

        $hasMore = $threads->count() > $perPage;

        if ($hasMore) {
            $threads = $threads->take($perPage);
        }

        return response()->json([
            'threads' => $threads,
            'has_more' => $hasMore
        ]);
    }
}
