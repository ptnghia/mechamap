<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\Forum;
use App\Models\SearchLog;
use App\Models\Showcase;
use App\Models\MarketplaceProduct;
use App\Models\TechnicalDrawing;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display the search results.
     */
    public function index(Request $request): View
    {
        $startTime = microtime(true);
        $query = $request->input('q') ?: $request->input('query');
        $type = $request->input('type', 'all');

        $threads = collect();
        $posts = collect();
        $users = collect();
        $showcases = collect();
        $products = collect();
        $materials = collect();
        $cadFiles = collect();
        $documentation = collect();
        $totalResults = 0;

        if ($query) {
            // Search threads
            if ($type == 'all' || $type == 'threads') {
                $threads = $this->searchThreads($query, 10);
            }

            // Search posts
            if ($type == 'all' || $type == 'posts') {
                $posts = Post::where('content', 'like', "%{$query}%")
                    ->with(['user', 'thread'])
                    ->latest()
                    ->take(10)
                    ->get();
            }

            // Search users
            if ($type == 'all' || $type == 'users') {
                $users = $this->searchUsers($query, 10);
            }

            // Search showcases
            if ($type == 'all' || $type == 'showcases') {
                $showcases = $this->searchShowcases($query, 10);
            }

            // Search products
            if ($type == 'all' || $type == 'products') {
                $products = $this->searchProducts($query, 10);
            }

            // Search materials
            if ($type == 'all' || $type == 'materials') {
                $materials = $this->searchMaterials($query, 10);
            }

            // Search CAD files
            if ($type == 'all' || $type == 'cad_files') {
                $cadFiles = $this->searchCadFiles($query, 10);
            }

            $totalResults = $threads->count() + $posts->count() + $users->count() +
                          $showcases->count() + $products->count() + $materials->count() + $cadFiles->count() + $documentation->count();
        } else {
            $totalResults = 0;

            // Log search activity for analytics
            $this->logSearch($query, $request, $totalResults, $startTime, [
                'search_type' => $type,
                'content_type' => 'general'
            ]);
        }

        // Debug: Log to confirm this controller is being used
        \Log::info('SearchController::index called', ['query' => $query, 'type' => $type, 'view' => 'search.basic']);

        // Determine first active tab when type is 'all'
        $firstActiveTab = 'threads'; // default
        if ($type === 'all' && $query) {
            $tabOrder = ['threads', 'posts', 'users', 'products', 'showcases', 'documentation', 'materials', 'cad_files'];
            $collections = [
                'threads' => $threads,
                'posts' => $posts,
                'users' => $users,
                'products' => $products,
                'showcases' => $showcases,
                'documentation' => $documentation,
                'materials' => $materials,
                'cad_files' => $cadFiles
            ];

            foreach ($tabOrder as $tab) {
                if ($collections[$tab]->count() > 0) {
                    $firstActiveTab = $tab;
                    break;
                }
            }
        }

        return view('search.basic', compact('query', 'type', 'threads', 'posts', 'users', 'showcases', 'products', 'documentation', 'materials', 'cadFiles', 'totalResults', 'firstActiveTab'));
    }

    /**
     * Display the advanced search form.
     */
    public function advanced(): View
    {
        $forums = Forum::all();

        return view('search.advanced', compact('forums'));
    }

    /**
     * Process the advanced search.
     */
    public function advancedSearch(Request $request): View
    {
        $startTime = microtime(true);

        $request->validate([
            'keywords' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'forum_id' => 'nullable|exists:forums,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort_by' => 'nullable|in:relevance,date,replies',
            'sort_dir' => 'nullable|in:asc,desc',
        ]);

        $keywords = $request->input('keywords');
        $author = $request->input('author');
        $forumId = $request->input('forum_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortBy = $request->input('sort_by', 'date');
        $sortDir = $request->input('sort_dir', 'desc');

        // Start with a base query
        $threadsQuery = Thread::query();
        $postsQuery = Post::query();

        // Apply keyword filter
        if ($keywords) {
            $threadsQuery->where(function ($query) use ($keywords) {
                $query->where('title', 'like', "%{$keywords}%")
                    ->orWhere('content', 'like', "%{$keywords}%");
            });

            $postsQuery->where('content', 'like', "%{$keywords}%");
        }

        // Apply author filter
        if ($author) {
            $user = User::where('username', $author)->first();

            if ($user) {
                $threadsQuery->where('user_id', $user->id);
                $postsQuery->where('user_id', $user->id);
            } else {
                // No user found, return empty results
                $threadsQuery->where('user_id', 0);
                $postsQuery->where('user_id', 0);
            }
        }

        // Apply forum filter
        if ($forumId) {
            $threadsQuery->where('forum_id', $forumId);
            $postsQuery->whereHas('thread', function ($query) use ($forumId) {
                $query->where('forum_id', $forumId);
            });
        }

        // Apply date filters
        if ($dateFrom) {
            $threadsQuery->whereDate('created_at', '>=', $dateFrom);
            $postsQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $threadsQuery->whereDate('created_at', '<=', $dateTo);
            $postsQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'relevance':
                // For relevance, we would need a more complex scoring system
                // For simplicity, we'll just sort by date
                $threadsQuery->orderBy('created_at', $sortDir);
                $postsQuery->orderBy('created_at', $sortDir);
                break;

            case 'date':
                $threadsQuery->orderBy('created_at', $sortDir);
                $postsQuery->orderBy('created_at', $sortDir);
                break;

            case 'replies':
                $threadsQuery->withCount('posts')
                    ->orderBy('posts_count', $sortDir);
                // Posts don't have replies, so we'll just sort by date
                $postsQuery->orderBy('created_at', $sortDir);
                break;
        }

        // Get the results
        $threads = $threadsQuery->with(['user', 'forum'])->paginate(10);
        $posts = $postsQuery->with(['user', 'thread'])->paginate(10);

        $totalResults = $threads->total() + $posts->total();

        // Log advanced search activity for analytics
        if ($keywords) {
            $this->logSearch($keywords, $request, $totalResults, $startTime, [
                'search_type' => 'advanced',
                'content_type' => 'advanced',
                'author' => $author,
                'forum_id' => $forumId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir
            ]);
        }

        $forums = Forum::all();

        return view('search.advanced-results', compact(
            'threads',
            'posts',
            'keywords',
            'author',
            'forumId',
            'dateFrom',
            'dateTo',
            'sortBy',
            'sortDir',
            'forums'
        ));
    }

    /**
     * Handle AJAX search requests.
     */
    public function ajaxSearch(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $query = $request->input('query');
        $scope = $request->input('scope', 'site'); // 'thread', 'forum', 'site'
        $threadId = $request->input('thread_id');
        $forumId = $request->input('forum_id');

        $results = [];
        $totalResults = 0;

        if (!$query || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // Search based on scope
        if ($scope === 'thread' && $threadId) {
            // Search within a specific thread
            $thread = Thread::find($threadId);
            if ($thread) {
                $results['thread'] = [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'url' => route('threads.show', $thread)
                ];

                // Search posts within this thread
                $posts = Post::where('thread_id', $threadId)
                    ->where('content', 'like', "%{$query}%")
                    ->with('user')
                    ->take(5)
                    ->get()
                    ->map(function ($post) {
                        return [
                            'id' => $post->id,
                            'content' => Str::limit(strip_tags($post->content), 100),
                            'user' => [
                                'name' => $post->user->name,
                                'username' => $post->user->username
                            ],
                            'url' => route('threads.show', $post->thread_id) . '#post-' . $post->id
                        ];
                    });

                $results['posts'] = $posts;
                $totalResults = $posts->count();
            }
        } elseif ($scope === 'forum' && $forumId) {
            // Search within a specific forum
            $forum = Forum::find($forumId);
            if ($forum) {
                $results['forum'] = [
                    'id' => $forum->id,
                    'name' => $forum->name,
                    'url' => route('forums.show', $forum)
                ];

                // Search threads within this forum
                $threads = Thread::where('forum_id', $forumId)
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                            ->orWhere('content', 'like', "%{$query}%");
                    })
                    ->with('user')
                    ->take(5)
                    ->get()
                    ->map(function ($thread) {
                        return [
                            'id' => $thread->id,
                            'title' => $thread->title,
                            'content' => Str::limit(strip_tags($thread->content), 100),
                            'user' => [
                                'name' => $thread->user->name,
                                'username' => $thread->user->username
                            ],
                            'url' => route('threads.show', $thread)
                        ];
                    });

                $results['threads'] = $threads;
                $totalResults = $threads->count();
            }
        } else {
            // Search across the entire site
            // Search threads
            $threads = Thread::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with(['user', 'forum'])
                ->take(5)
                ->get()
                ->map(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'content' => Str::limit(strip_tags($thread->content), 100),
                        'user' => [
                            'name' => $thread->user->name,
                            'username' => $thread->user->username
                        ],
                        'forum' => [
                            'name' => $thread->forum->name,
                            'url' => route('forums.show', $thread->forum)
                        ],
                        'url' => route('threads.show', $thread)
                    ];
                });

            // Search posts
            $posts = Post::where('content', 'like', "%{$query}%")
                ->with(['user', 'thread'])
                ->take(5)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'content' => Str::limit(strip_tags($post->content), 100),
                        'user' => [
                            'name' => $post->user->name,
                            'username' => $post->user->username
                        ],
                        'thread' => [
                            'title' => $post->thread->title,
                            'url' => route('threads.show', $post->thread)
                        ],
                        'url' => route('threads.show', $post->thread_id) . '#post-' . $post->id
                    ];
                });

            $results['threads'] = $threads;
            $results['posts'] = $posts;
            $totalResults = $threads->count() + $posts->count();
        }

        // Log AJAX search activity for analytics
        $this->logSearch($query, $request, $totalResults, $startTime, [
            'search_type' => 'ajax',
            'content_type' => 'ajax',
            'scope' => $scope,
            'thread_id' => $threadId,
            'forum_id' => $forumId
        ]);

        return response()->json([
            'results' => $results,
            'advanced_search_url' => route('search.advanced')
        ]);
    }

    /**
     * Log search activity for analytics.
     */
    private function logSearch(
        ?string $query,
        Request $request,
        int $resultsCount,
        float $startTime,
        array $filters = []
    ): void {
        try {
            $responseTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds

            SearchLog::create([
                'query' => $query,
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'results_count' => $resultsCount,
                'response_time_ms' => $responseTime,
                'filters' => $filters,
                'content_type' => $filters['content_type'] ?? 'general',
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error nhưng không ảnh hưởng đến quá trình tìm kiếm chính
            Log::error('Failed to log search activity: ' . $e->getMessage(), [
                'query' => $query,
                'user_id' => Auth::check() ? Auth::id() : null,
                'results_count' => $resultsCount
            ]);
        }
    }

    /**
     * Search threads with relevance scoring
     */
    private function searchThreads(string $query, int $limit): \Illuminate\Support\Collection
    {
        return Thread::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->where('moderation_status', 'approved')
            ->where('is_spam', false)
            ->whereNull('hidden_at')
            ->with(['user', 'forum', 'category', 'media'])
            ->withCount('allComments as comments_count')
            ->orderByRaw("
                CASE
                    WHEN title LIKE ? THEN 1
                    WHEN title LIKE ? THEN 2
                    WHEN content LIKE ? THEN 3
                    ELSE 4
                END, created_at DESC
            ", ["{$query}%", "%{$query}%", "%{$query}%"])
            ->limit($limit)
            ->get();
    }

    /**
     * Search showcases
     */
    private function searchShowcases(string $query, int $limit): \Illuminate\Support\Collection
    {
        try {
            return Showcase::where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('project_type', 'like', "%{$query}%")
                      ->orWhere('materials', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('is_public', true)
                ->with(['user'])
                ->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN project_type LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END, featured_at DESC, created_at DESC
                ", ["{$query}%", "%{$query}%", "%{$query}%"])
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Showcase search error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Search products (marketplace + technical)
     */
    private function searchProducts(string $query, int $limit): \Illuminate\Support\Collection
    {
        try {
            // Debug: Check total products
            $totalProducts = MarketplaceProduct::count();
            $activeProducts = MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count();
            \Log::info("Products debug", ['total' => $totalProducts, 'active' => $activeProducts, 'query' => $query]);

            // Search marketplace products
            $marketplaceProducts = MarketplaceProduct::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('is_active', true)
                ->with(['seller'])
                ->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN sku LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END, featured_at DESC, created_at DESC
                ", ["{$query}%", "%{$query}%", "%{$query}%"])
                ->limit($limit)
                ->get();

            return $marketplaceProducts;
        } catch (\Exception $e) {
            Log::error('Product search error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Search users
     */
    private function searchUsers(string $query, int $limit): \Illuminate\Support\Collection
    {
        $cleanQuery = trim($query);

        return User::where(function ($q) use ($cleanQuery) {
                $q->where('name', 'like', "%{$cleanQuery}%")
                  ->orWhere('username', 'like', "%{$cleanQuery}%")
                  ->orWhere('company_name', 'like', "%{$cleanQuery}%");
            })
            ->where('status', 'active')
            ->whereNull('banned_at')
            ->orderByRaw("
                CASE
                    WHEN username LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN company_name LIKE ? THEN 3
                    ELSE 4
                END
            ", ["{$cleanQuery}%", "{$cleanQuery}%", "{$cleanQuery}%"])
            ->limit($limit)
            ->get();
    }

    /**
     * Search materials
     */
    private function searchMaterials(string $query, int $limit): \Illuminate\Support\Collection
    {
        try {
            return Material::where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('category', 'like', "%{$query}%")
                      ->orWhere('code', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('is_active', true)
                ->orderByRaw("
                    CASE
                        WHEN name LIKE ? THEN 1
                        WHEN category LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END, created_at DESC
                ", ["{$query}%", "%{$query}%", "%{$query}%"])
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Material search error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Search CAD files
     */
    private function searchCadFiles(string $query, int $limit): \Illuminate\Support\Collection
    {
        try {
            return TechnicalDrawing::where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('drawing_number', 'like', "%{$query}%")
                      ->orWhere('part_number', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->where('visibility', 'public')
                ->where('is_active', true)
                ->with(['creator'])
                ->orderByRaw("
                    CASE
                        WHEN title LIKE ? THEN 1
                        WHEN drawing_number LIKE ? THEN 2
                        WHEN description LIKE ? THEN 3
                        ELSE 4
                    END, created_at DESC
                ", ["{$query}%", "%{$query}%", "%{$query}%"])
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('CAD file search error: ' . $e->getMessage());
            return collect();
        }
    }
}
