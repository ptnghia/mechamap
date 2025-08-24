<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\Media;
use App\Services\ShowcaseImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WhatsNewController extends Controller
{
    /**
     * Cache duration for expensive queries (in minutes)
     */
    private const CACHE_DURATION = 5;

    /**
     * Optimize thread statistics with eager loading to prevent N+1 queries
     */
    private function optimizeThreadStatistics($threads)
    {
        // Get all thread IDs
        $threadIds = $threads->pluck('id')->toArray();

        if (empty($threadIds)) {
            return $threads;
        }

        // Eager load latest comments for all threads in one query
        $latestComments = Comment::whereIn('thread_id', $threadIds)
            ->select('thread_id', 'created_at', 'user_id')
            ->with('user:id,name,avatar')
            ->whereIn('id', function($query) use ($threadIds) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('comments')
                    ->whereIn('thread_id', $threadIds)
                    ->groupBy('thread_id');
            })
            ->get()
            ->keyBy('thread_id');

        // Apply statistics to threads
        foreach ($threads as $thread) {
            // Comment count is already loaded via withCount
            $thread->page_count = ceil($thread->comment_count / 20);

            // Set latest comment info from eager loaded data
            $latestComment = $latestComments->get($thread->id);
            if ($latestComment) {
                $thread->latest_comment_at = $latestComment->created_at;
                $thread->latest_comment_user = $latestComment->user;
            } else {
                $thread->latest_comment_at = $thread->created_at;
                $thread->latest_comment_user = $thread->user;
            }
        }

        return $threads;
    }

    /**
     * Generate pagination data
     */
    private function generatePaginationData($paginatedData, $routeName, $page, $additionalParams = [])
    {
        $totalPages = ceil($paginatedData->total() / $paginatedData->perPage());

        $prevPageUrl = $page > 1
            ? route($routeName, array_merge(['page' => $page - 1], $additionalParams))
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route($routeName, array_merge(['page' => $page + 1], $additionalParams))
            : '#';

        return [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'prevPageUrl' => $prevPageUrl,
            'nextPageUrl' => $nextPageUrl
        ];
    }
    /**
     * Display the "What's New" page with recent posts
     */
    public function index(Request $request)
    {
        try {
            // Simplified approach - get threads directly like CategoryController
            $threads = Thread::with(['user', 'category', 'forum', 'media'])
                ->publicVisible()
                ->whereNull('deleted_at')
                ->where('is_locked', false)
                ->where(function ($query) {
                    $query->where('status', '!=', 'cancelled')
                        ->where('status', '!=', 'rejected')
                        ->where(function ($q) {
                            $q->whereNull('status')
                                ->orWhere('status', '!=', 'deleted');
                        });
                })
                ->withCount('allComments as comments_count')
                ->latest()
                ->paginate(20); // Use Laravel pagination like CategoryController

            return view('whats-new.index', compact('threads'));

        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('WhatsNew index error: ' . $e->getMessage());

            // Return empty result to avoid 500 error
            $threads = Thread::whereRaw('1 = 0')->paginate(20); // Empty paginated collection

            return view('whats-new.index', compact('threads'));
        }
    }

    /**
     * Display popular threads
     */
    public function popular(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;
        $timeframe = $request->input('timeframe', 'week'); // day, week, month, year, all
        $sortType = $request->input('sort', 'trending'); // trending, most_viewed

        // Determine date range based on timeframe
        $startDate = now();
        switch ($timeframe) {
            case 'day':
                $startDate = $startDate->subDay();
                break;
            case 'week':
                $startDate = $startDate->subWeek();
                break;
            case 'month':
                $startDate = $startDate->subMonth();
                break;
            case 'year':
                $startDate = $startDate->subYear();
                break;
            case 'all':
                $startDate = null;
                break;
        }

        // Get threads based on sort type
        $query = Thread::with(['user', 'category', 'forum'])
            ->withCount('allComments as comment_count')
            ->where('is_locked', false);

        if ($startDate) {
            $query->where(function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)
                    ->orWhereHas('comments', function ($q2) use ($startDate) {
                        $q2->where('created_at', '>=', $startDate);
                    });
            });
        }

        // Apply sorting logic based on sort type
        if ($sortType === 'most_viewed') {
            // Most Viewed: Simple view count sorting
            $threads = $query->orderBy('view_count', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            // Trending: Complex trending score calculation
            $query->selectRaw('threads.*, (
                (threads.view_count * 0.3) +
                (threads.cached_comments_count * 2) +
                (CASE
                    WHEN threads.created_at > NOW() - INTERVAL 1 DAY THEN 10
                    WHEN threads.created_at > NOW() - INTERVAL 3 DAY THEN 5
                    WHEN threads.created_at > NOW() - INTERVAL 7 DAY THEN 2
                    ELSE 1
                END)
            ) as trending_score');

            $threads = $query->orderBy('trending_score', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.popular', ['page' => $page - 1, 'timeframe' => $timeframe, 'sort' => $sortType])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.popular', ['page' => $page + 1, 'timeframe' => $timeframe, 'sort' => $sortType])
            : '#';

        // Optimize thread statistics
        $this->optimizeThreadStatistics($threads);

        return view('whats-new.popular', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl',
            'timeframe',
            'sortType'
        ));
    }

    /**
     * Display new threads
     */
    public function threads(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Cache key for new threads
        $cacheKey = "whats_new_threads_{$page}";

        $threads = Cache::remember($cacheKey, self::CACHE_DURATION, function() use ($page, $perPage) {
            // Get recent threads with optimized eager loading
            return Thread::with(['user:id,name,avatar', 'category:id,name', 'forum:id,name'])
                ->withCount('allComments as comment_count')
                ->where('is_locked', false)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        });

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.threads', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.threads', ['page' => $page + 1])
            : '#';

        // Optimize thread statistics
        $this->optimizeThreadStatistics($threads);

        return view('whats-new.threads', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display new media
     */
    public function media(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = 20;

            // Cache key for new media
            $cacheKey = "whats_new_media_{$page}";

            $mediaItems = Cache::remember($cacheKey, self::CACHE_DURATION, function() use ($page, $perPage) {
                // Get recent media with optimized eager loading using polymorphic relationship
                return Media::with([
                    'user:id,name,avatar',
                    'mediable' // This will load the related model (Thread, Comment, etc.)
                ])
                    ->where('mediable_type', 'App\\Models\\Thread') // Only media attached to threads
                    ->whereHasMorph('mediable', ['App\\Models\\Thread'], function ($query) {
                        $query->where('is_locked', false)
                            ->whereNull('deleted_at');
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);
            });

            // Calculate total pages
            $totalPages = ceil($mediaItems->total() / $perPage);

            // Generate pagination URLs
            $prevPageUrl = $page > 1
                ? route('whats-new.media', ['page' => $page - 1])
                : '#';

            $nextPageUrl = $page < $totalPages
                ? route('whats-new.media', ['page' => $page + 1])
                : '#';

            return view('whats-new.media', compact(
                'mediaItems',
                'page',
                'totalPages',
                'prevPageUrl',
                'nextPageUrl'
            ));

        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('WhatsNew media error: ' . $e->getMessage());

            // Return empty result to avoid 500 error
            $mediaItems = collect();
            $page = 1;
            $totalPages = 1;
            $prevPageUrl = '#';
            $nextPageUrl = '#';

            return view('whats-new.media', compact(
                'mediaItems',
                'page',
                'totalPages',
                'prevPageUrl',
                'nextPageUrl'
            ));
        }
    }

    /**
     * Display threads looking for replies
     */
    public function replies(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Cache key for threads needing replies
        $cacheKey = "whats_new_replies_{$page}";

        $threads = Cache::remember($cacheKey, self::CACHE_DURATION, function() use ($page, $perPage) {
            // Get threads with few replies, optimized query
            return Thread::with(['user:id,name,avatar', 'category:id,name', 'forum:id,name'])
                ->withCount('allComments as comment_count')
                ->where('is_locked', false)
                ->whereDoesntHave('comments')
                ->orWhereHas('comments', function ($query) {
                    $query->havingRaw('COUNT(*) < 5');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
        });

        // Calculate total pages
        $totalPages = ceil($threads->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.replies', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.replies', ['page' => $page + 1])
            : '#';

        // Optimize thread statistics
        $this->optimizeThreadStatistics($threads);

        return view('whats-new.replies', compact(
            'threads',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display new showcases
     */
    public function showcases(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Get recent showcases
        $showcases = \App\Models\Showcase::with(['user', 'showcaseable', 'media'])
            ->whereHas('showcaseable') // Ensure showcaseable exists
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate total pages
        $totalPages = ceil($showcases->total() / $perPage);

        // Generate pagination URLs
        $prevPageUrl = $page > 1
            ? route('whats-new.showcases', ['page' => $page - 1])
            : '#';

        $nextPageUrl = $page < $totalPages
            ? route('whats-new.showcases', ['page' => $page + 1])
            : '#';

        // Process featured images using unified service
        ShowcaseImageService::processFeaturedImages($showcases->getCollection());

        // Add additional data for each showcase
        foreach ($showcases as $showcase) {
            // Get showcase type and title
            if ($showcase->showcaseable_type === 'App\Models\Thread') {
                $showcase->showcase_type = 'Thread';
                $showcase->showcase_title = $showcase->showcaseable->title ?? 'Unknown Thread';
                $showcase->showcase_url = route('threads.show', $showcase->showcaseable);
            } elseif ($showcase->showcaseable_type === 'App\Models\Post') {
                $showcase->showcase_type = 'Post';
                $showcase->showcase_title = 'Reply in: ' . ($showcase->showcaseable->thread->title ?? 'Unknown Thread');
                $showcase->showcase_url = route('threads.show', $showcase->showcaseable->thread) . '#post-' . $showcase->showcaseable->id;
            } else {
                $showcase->showcase_type = 'Content';
                $showcase->showcase_title = $showcase->title ?? 'Showcase Item';
                $showcase->showcase_url = route('showcase.show', $showcase);
            }

            // Content preview
            if ($showcase->showcaseable) {
                $content = $showcase->showcaseable->content ?? $showcase->showcaseable->description ?? '';
                $showcase->content_preview = \Illuminate\Support\Str::limit(strip_tags($content), 150);
            } else {
                $showcase->content_preview = $showcase->description ?? '';
            }
        }

        return view('whats-new.showcases', compact(
            'showcases',
            'page',
            'totalPages',
            'prevPageUrl',
            'nextPageUrl'
        ));
    }

    /**
     * Display trending content this week - Redirect to popular with trending sort
     */
    public function trending(Request $request)
    {
        // Redirect to popular page with trending sort to maintain backward compatibility
        $params = [
            'sort' => 'trending',
            'timeframe' => $request->input('timeframe', 'week'),
            'page' => $request->input('page', 1)
        ];

        return redirect()->route('whats-new.popular', $params);
    }

    /**
     * Display most viewed content - Redirect to popular with most_viewed sort
     */
    public function mostViewed(Request $request)
    {
        // Redirect to popular page with most_viewed sort to maintain backward compatibility
        $params = [
            'sort' => 'most_viewed',
            'timeframe' => $request->input('timeframe', 'week'),
            'page' => $request->input('page', 1)
        ];

        return redirect()->route('whats-new.popular', $params);
    }

    /**
     * Display hot topics (high engagement recently)
     */
    public function hotTopics(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20;

        // Cache key for hot topics
        $cacheKey = "whats_new_hot_topics_page_{$page}";

        $result = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($page, $perPage) {
            // Hot topics are threads with high recent activity
            $threads = Thread::select([
                'threads.*',
                DB::raw('(
                    SELECT COUNT(*)
                    FROM comments
                    WHERE comments.thread_id = threads.id
                    AND comments.created_at > NOW() - INTERVAL 24 HOUR
                ) as recent_comments'),
                DB::raw('(
                    (threads.view_count * 0.1) +
                    (threads.cached_comments_count * 1.5) +
                    ((SELECT COUNT(*) FROM comments WHERE comments.thread_id = threads.id AND comments.created_at > NOW() - INTERVAL 24 HOUR) * 5)
                ) as hot_score')
            ])
            ->with(['user:id,name,username,avatar', 'forum:id,name,slug', 'category:id,name,slug'])
            ->withCount('allComments as comment_count')
            ->having('hot_score', '>', 0)
            ->orderBy('hot_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

            return $threads;
        });

        // Optimize thread statistics
        $threads = $this->optimizeThreadStatistics($result);

        // Generate pagination info
        $pagination = $this->generatePaginationData($result, 'whats-new.hot-topics', $page);

        return view('whats-new.hot-topics', compact('threads', 'pagination'));
    }

    /**
     * Clear all What's New caches
     * Call this method when new threads/comments are created
     */
    public static function clearCache()
    {
        $patterns = [
            'whats_new_index_*',
            'whats_new_popular_*',
            'whats_new_threads_*',
            'whats_new_media_*',
            'whats_new_replies_*',
            'whats_new_trending_*',
            'whats_new_most_viewed_*',
            'whats_new_hot_topics_*'
        ];

        foreach ($patterns as $pattern) {
            // Clear cache with pattern (if using Redis)
            if (config('cache.default') === 'redis') {
                $keys = Cache::getRedis()->keys($pattern);
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            } else {
                // For other cache drivers, clear specific keys
                for ($page = 1; $page <= 10; $page++) {
                    $key = str_replace('*', $page, $pattern);
                    Cache::forget($key);
                }
            }
        }
    }
}
