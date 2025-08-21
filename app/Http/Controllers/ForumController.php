<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class ForumController extends Controller
{
    /**
     * Display a listing of the forums.
     */
    public function index(): View
    {
        // Cache forum data for better performance
        $categories = cache()->remember('forums.categories', 3600, function () {
            return Forum::where('parent_id', null)
                ->with([
                    'media' => function ($query) {
                        $query->where('mime_type', 'like', 'image/%');
                    },
                    'subForums' => function ($query) {
                        $query->withCount(['threads', 'posts'])
                            ->with(['media' => function ($mediaQuery) {
                                $mediaQuery->where('mime_type', 'like', 'image/%');
                            }]);
                    }
                ])
                ->get();
        });

        // Cache stats for better performance (refresh every 30 minutes)
        $stats = cache()->remember('forums.stats', 1800, function () {
            return [
                'forums' => Forum::count(),
                'threads' => Thread::count(),
                'posts' => \App\Models\Comment::count(), // Use Comment instead of Post
                'users' => \App\Models\User::count(),
                'newest_member' => \App\Models\User::latest()->first()
            ];
        });

        return view('forums.index', compact('categories', 'stats'));
    }

    /**
     * Display the specified forum.
     */
    public function show(Forum $forum, Request $request): View
    {
        // Load media relationship for forum images
        $forum->load('media');

        $query = $forum->threads()->with(['user', 'media', 'category', 'forum'])->withCount('allComments as comments_count');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by activity
        if ($request->get('filter') === 'recent') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($request->get('filter') === 'popular') {
            $query->where('comments_count', '>=', 5);
        } elseif ($request->get('filter') === 'unanswered') {
            $query->having('comments_count', '=', 0);
        }

        // Sort options - ALWAYS put sticky threads first
        $sortBy = $request->get('sort', 'latest');

        // Primary sort: sticky threads first
        $query->orderBy('is_sticky', 'desc');

        // Secondary sort: based on user selection
        switch ($sortBy) {
            case 'oldest':
                $query->oldest();
                break;
            case 'popular':
                $query->orderBy('comments_count', 'desc');
                break;
            case 'views':
                $query->orderBy('view_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $threads = $query->paginate(20)->appends($request->query());

        // Get forum statistics
        $forumStats = [
            'total_threads' => $forum->threads()->count(),
            'total_posts' => \App\Models\Comment::whereHas('thread', function($q) use ($forum) {
                $q->where('forum_id', $forum->id);
            })->count(),
            'recent_threads' => $forum->threads()->where('created_at', '>=', now()->subWeek())->count(),
            'active_users' => $forum->threads()->with('user')->get()->pluck('user')->unique('id')->count(),
        ];

        return view('forums.show', compact('forum', 'threads', 'forumStats'));
    }

    // DEPRECATED: Search functionality moved to ThreadController

    // DEPRECATED: Advanced search functionality moved to ThreadController

    /**
     * Search by category
     */
    public function searchByCategory(Request $request): View
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'q' => 'nullable|string|max:255',
        ]);

        $categoryId = $request->get('category_id');
        $query = $request->get('q');

        // Get all categories for navigation
        $categories = \App\Models\Category::with('forums')->get();

        // If no category selected, show category selection form
        if (!$categoryId) {
            return view('forums.search-by-category', compact('categories'));
        }

        $category = \App\Models\Category::with('forums')->findOrFail($categoryId);

        // Build threads query for this category
        $threadsQuery = Thread::query()
            ->whereHas('forum', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->with(['forum', 'user'])
            ->withCount('allComments as comments_count');

        // Apply search if provided
        if ($query) {
            $threadsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            });
        }

        $threads = $threadsQuery->orderBy('created_at', 'desc')->paginate(20);

        return view('forums.search-by-category', compact(
            'threads', 'category', 'categories', 'query'
        ));
    }
}
