<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\Forum;
use App\Models\SearchLog;
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
        $query = $request->input('query');
        $type = $request->input('type', 'all');

        $threads = collect();
        $posts = collect();
        $users = collect();
        $totalResults = 0;

        if ($query) {
            // Search threads
            if ($type == 'all' || $type == 'threads') {
                $threads = Thread::where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->with(['user', 'forum'])
                    ->latest()
                    ->take(10)
                    ->get();
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
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->latest()
                    ->take(10)
                    ->get();
            }

            $totalResults = $threads->count() + $posts->count() + $users->count();

            // Log search activity for analytics
            $this->logSearch($query, $request, $totalResults, $startTime, [
                'search_type' => $type,
                'content_type' => 'general'
            ]);
        }

        return view('search.index', compact('query', 'type', 'threads', 'posts', 'users'));
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
        string $query,
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
}
