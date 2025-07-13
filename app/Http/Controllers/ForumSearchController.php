<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Models\Thread;
use App\Models\Post;
use App\Models\Category;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Forum Search Controller
 * Specialized search functionality for forums, threads, and posts
 */
class ForumSearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Forum advanced search interface
     */
    public function advanced(Request $request): View
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');
        $forumId = $request->get('forum_id');
        $author = $request->get('author');
        $sortBy = $request->get('sort_by', 'relevance');
        $sortDir = $request->get('sort_dir', 'desc');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $threads = collect();
        $totalResults = 0;
        $searchPerformed = false;

        // Perform search if query or filters provided
        if ($query || $categoryId || $forumId || $author || $dateFrom || $dateTo) {
            $searchPerformed = true;
            
            try {
                if ($this->searchService->isAvailable()) {
                    // Use Elasticsearch for advanced search
                    $filters = $this->buildSearchFilters($request);
                    $options = [
                        'page' => $request->get('page', 1),
                        'per_page' => 20,
                        'sort' => $sortBy,
                        'sort_direction' => $sortDir,
                        'include_highlights' => true,
                        'include_facets' => true
                    ];

                    $searchResults = $this->searchService->search($query, $filters, $options);
                    $threads = collect($searchResults['results']['threads'] ?? []);
                    $totalResults = $searchResults['meta']['total'] ?? 0;
                } else {
                    // Fallback to database search
                    $results = $this->databaseSearch($request);
                    $threads = $results['threads'];
                    $totalResults = $results['total'];
                }
            } catch (\Exception $e) {
                Log::error('Forum search error: ' . $e->getMessage());
                $results = $this->databaseSearch($request);
                $threads = $results['threads'];
                $totalResults = $results['total'];
            }
        }

        // Get data for form dropdowns
        $categories = Category::with('forums')->orderBy('name')->get();
        $forums = Forum::orderBy('name')->get();

        return view('forums.search-advanced', compact(
            'query',
            'categoryId',
            'forumId',
            'author',
            'sortBy',
            'sortDir',
            'dateFrom',
            'dateTo',
            'threads',
            'totalResults',
            'searchPerformed',
            'categories',
            'forums'
        ));
    }

    /**
     * Search by category
     */
    public function searchByCategory(Request $request): View
    {
        $categoryId = $request->get('category_id');
        $query = $request->get('q', '');
        $sortBy = $request->get('sort_by', 'latest');

        $category = null;
        $threads = collect();
        $forums = collect();

        if ($categoryId) {
            $category = Category::with('forums')->findOrFail($categoryId);
            $forums = $category->forums;

            // Search threads in this category
            $threadsQuery = Thread::whereHas('forum', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });

            if ($query) {
                $threadsQuery->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                });
            }

            // Apply sorting
            switch ($sortBy) {
                case 'latest':
                    $threadsQuery->latest();
                    break;
                case 'oldest':
                    $threadsQuery->oldest();
                    break;
                case 'most_replies':
                    $threadsQuery->orderBy('replies_count', 'desc');
                    break;
                case 'most_views':
                    $threadsQuery->orderBy('views_count', 'desc');
                    break;
                case 'alphabetical':
                    $threadsQuery->orderBy('title');
                    break;
            }

            $threads = $threadsQuery->with(['user', 'forum'])
                ->paginate(20)
                ->appends($request->query());
        }

        $categories = Category::orderBy('name')->get();

        return view('forums.search-by-category', compact(
            'category',
            'categoryId',
            'query',
            'sortBy',
            'threads',
            'forums',
            'categories'
        ));
    }

    /**
     * Basic forum search
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'threads');

        $threads = collect();
        $posts = collect();
        $totalResults = 0;

        if ($query) {
            try {
                if ($this->searchService->isAvailable()) {
                    // Use Elasticsearch
                    $filters = ['content_type' => $type];
                    $searchResults = $this->searchService->search($query, $filters, [
                        'per_page' => 20,
                        'include_highlights' => true
                    ]);

                    if ($type === 'threads' || $type === 'all') {
                        $threads = collect($searchResults['results']['threads'] ?? []);
                    }
                    if ($type === 'posts' || $type === 'all') {
                        $posts = collect($searchResults['results']['posts'] ?? []);
                    }
                    $totalResults = $searchResults['meta']['total'] ?? 0;
                } else {
                    // Database fallback
                    $results = $this->basicDatabaseSearch($query, $type);
                    $threads = $results['threads'];
                    $posts = $results['posts'];
                    $totalResults = $results['total'];
                }
            } catch (\Exception $e) {
                Log::error('Forum basic search error: ' . $e->getMessage());
                $results = $this->basicDatabaseSearch($query, $type);
                $threads = $results['threads'];
                $posts = $results['posts'];
                $totalResults = $results['total'];
            }
        }

        return view('forums.search', compact(
            'query',
            'type',
            'threads',
            'posts',
            'totalResults'
        ));
    }

    /**
     * Build search filters from request
     */
    private function buildSearchFilters(Request $request): array
    {
        $filters = ['content_type' => 'threads'];

        if ($request->get('category_id')) {
            $filters['category_id'] = $request->get('category_id');
        }

        if ($request->get('forum_id')) {
            $filters['forum_id'] = $request->get('forum_id');
        }

        if ($request->get('author')) {
            $filters['user_name'] = $request->get('author');
        }

        if ($request->get('date_from')) {
            $filters['date_from'] = $request->get('date_from');
        }

        if ($request->get('date_to')) {
            $filters['date_to'] = $request->get('date_to');
        }

        return $filters;
    }

    /**
     * Database search fallback
     */
    private function databaseSearch(Request $request): array
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');
        $forumId = $request->get('forum_id');
        $author = $request->get('author');
        $sortBy = $request->get('sort_by', 'relevance');
        $sortDir = $request->get('sort_dir', 'desc');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $threadsQuery = Thread::with(['user', 'forum', 'category']);

        // Apply filters
        if ($query) {
            $threadsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            });
        }

        if ($categoryId) {
            $threadsQuery->whereHas('forum', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($forumId) {
            $threadsQuery->where('forum_id', $forumId);
        }

        if ($author) {
            $threadsQuery->whereHas('user', function ($q) use ($author) {
                $q->where('name', 'like', "%{$author}%")
                    ->orWhere('username', 'like', "%{$author}%");
            });
        }

        if ($dateFrom) {
            $threadsQuery->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $threadsQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        // Apply sorting
        switch ($sortBy) {
            case 'date':
                $threadsQuery->orderBy('created_at', $sortDir);
                break;
            case 'replies':
                $threadsQuery->orderBy('replies_count', $sortDir);
                break;
            case 'views':
                $threadsQuery->orderBy('views_count', $sortDir);
                break;
            case 'title':
                $threadsQuery->orderBy('title', $sortDir);
                break;
            default: // relevance
                if ($query) {
                    // Simple relevance: title matches first, then content
                    $threadsQuery->orderByRaw("
                        CASE 
                            WHEN title LIKE ? THEN 1
                            WHEN content LIKE ? THEN 2
                            ELSE 3
                        END, created_at DESC
                    ", ["%{$query}%", "%{$query}%"]);
                } else {
                    $threadsQuery->latest();
                }
                break;
        }

        $threads = $threadsQuery->paginate(20)->appends($request->query());

        return [
            'threads' => $threads,
            'total' => $threads->total()
        ];
    }

    /**
     * Basic database search
     */
    private function basicDatabaseSearch(string $query, string $type): array
    {
        $threads = collect();
        $posts = collect();

        if ($type === 'threads' || $type === 'all') {
            $threads = Thread::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with(['user', 'forum'])
                ->latest()
                ->take(20)
                ->get();
        }

        if ($type === 'posts' || $type === 'all') {
            $posts = Post::where('content', 'like', "%{$query}%")
                ->with(['user', 'thread'])
                ->latest()
                ->take(20)
                ->get();
        }

        return [
            'threads' => $threads,
            'posts' => $posts,
            'total' => $threads->count() + $posts->count()
        ];
    }
}
