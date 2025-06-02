<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;

/**
 * Service chuyên xử lý tối ưu hiệu suất cho API
 * Cung cấp các phương thức caching, query optimization và performance monitoring
 */
class ApiPerformanceService
{
    /**
     * Cache duration trong giây
     */
    const CACHE_DURATION_SHORT = 300;  // 5 phút
    const CACHE_DURATION_MEDIUM = 1800; // 30 phút
    const CACHE_DURATION_LONG = 3600;   // 1 giờ
    const CACHE_DURATION_DAILY = 86400; // 24 giờ

    /**
     * Lấy dữ liệu threads với cache và eager loading
     */
    public function getCachedThreads($page = 1, $perPage = 20, $forumId = null)
    {
        $cacheKey = "threads_page_{$page}_per_{$perPage}_forum_{$forumId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION_SHORT, function () use ($page, $perPage, $forumId) {
            $query = Thread::with([
                'user:id,name,avatar',
                'forum:id,name,slug',
                'lastPost.user:id,name,avatar'
            ])
                ->select(['id', 'title', 'slug', 'user_id', 'forum_id', 'views', 'replies', 'created_at', 'last_post_id'])
                ->orderBy('created_at', 'desc');

            if ($forumId) {
                $query->where('forum_id', $forumId);
            }

            return $query->paginate($perPage, ['*'], 'page', $page);
        });
    }

    /**
     * Lấy thống kê tổng quan với cache
     */
    public function getCachedStats()
    {
        return Cache::remember('api_stats_overview', self::CACHE_DURATION_MEDIUM, function () {
            return [
                'total_threads' => Thread::count(),
                'total_users' => User::count(),
                'total_forums' => Forum::count(),
                'active_users_today' => User::whereDate('last_seen_at', today())->count(),
                'new_threads_today' => Thread::whereDate('created_at', today())->count(),
                'top_forums' => Forum::withCount('threads')
                    ->orderBy('threads_count', 'desc')
                    ->take(5)
                    ->get(['id', 'name', 'threads_count']),
            ];
        });
    }

    /**
     * Tìm kiếm với cache và full-text search optimization
     */
    public function getCachedSearch($query, $type = 'all', $page = 1)
    {
        $cacheKey = "search_" . md5($query . $type . $page);

        return Cache::remember($cacheKey, self::CACHE_DURATION_SHORT, function () use ($query, $type, $page) {
            $results = [];

            if ($type === 'all' || $type === 'threads') {
                $results['threads'] = Thread::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->with(['user:id,name,avatar', 'forum:id,name'])
                    ->select(['id', 'title', 'slug', 'user_id', 'forum_id', 'views', 'created_at'])
                    ->take(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'users') {
                $results['users'] = User::where('name', 'LIKE', "%{$query}%")
                    ->select(['id', 'name', 'avatar', 'created_at'])
                    ->take(10)
                    ->get();
            }

            if ($type === 'all' || $type === 'forums') {
                $results['forums'] = Forum::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->withCount('threads')
                    ->select(['id', 'name', 'slug', 'description'])
                    ->take(10)
                    ->get();
            }

            return $results;
        });
    }

    /**
     * Lấy user profile với eager loading
     */
    public function getCachedUserProfile($userId)
    {
        $cacheKey = "user_profile_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION_MEDIUM, function () use ($userId) {
            return User::with([
                'threads' => function ($query) {
                    $query->select(['id', 'title', 'slug', 'user_id', 'views', 'replies', 'created_at'])
                        ->orderBy('created_at', 'desc')
                        ->take(5);
                },
                'posts' => function ($query) {
                    $query->with('thread:id,title,slug')
                        ->select(['id', 'content', 'user_id', 'thread_id', 'created_at'])
                        ->orderBy('created_at', 'desc')
                        ->take(5);
                }
            ])
                ->findOrFail($userId);
        });
    }

    /**
     * Lấy hot threads (trending) với cache
     */
    public function getCachedHotThreads($limit = 10)
    {
        return Cache::remember('hot_threads', self::CACHE_DURATION_MEDIUM, function () use ($limit) {
            return Thread::with(['user:id,name,avatar', 'forum:id,name'])
                ->select(['id', 'title', 'slug', 'user_id', 'forum_id', 'views', 'replies', 'created_at'])
                ->where('created_at', '>=', now()->subDays(7))
                ->orderByRaw('(views + replies * 2) DESC')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Clear cache theo pattern
     */
    public function clearCache($pattern = null)
    {
        if ($pattern) {
            // Xóa cache theo pattern cụ thể - sử dụng Laravel cache tags hoặc manual
            try {
                // Nếu cache driver hỗ trợ tags
                if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                    Cache::tags($pattern)->flush();
                } else {
                    // Fallback: flush all cache khi không hỗ trợ pattern
                    Cache::flush();
                }
            } catch (\Exception $e) {
                // Fallback: flush all cache
                Cache::flush();
            }
        } else {
            // Xóa tất cả cache API
            Cache::flush();
        }

        Log::info('API Cache cleared', ['pattern' => $pattern]);
    }

    /**
     * Monitor API performance
     */
    public function trackApiPerformance(Request $request, $startTime, $response)
    {
        $duration = microtime(true) - $startTime;
        $endpoint = $request->path();
        $method = $request->method();
        $statusCode = $response->getStatusCode();

        // Log slow requests (>1 giây)
        if ($duration > 1.0) {
            Log::warning('Slow API Request', [
                'endpoint' => "{$method} {$endpoint}",
                'duration' => round($duration, 3),
                'status_code' => $statusCode,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Lưu metrics vào cache để monitoring
        $metricsKey = "api_metrics_" . date('Y-m-d-H');
        $metrics = Cache::get($metricsKey, []);

        $endpointKey = "{$method}_{$endpoint}";
        if (!isset($metrics[$endpointKey])) {
            $metrics[$endpointKey] = [
                'count' => 0,
                'total_duration' => 0,
                'avg_duration' => 0,
                'max_duration' => 0,
                'errors' => 0,
            ];
        }

        $metrics[$endpointKey]['count']++;
        $metrics[$endpointKey]['total_duration'] += $duration;
        $metrics[$endpointKey]['avg_duration'] = $metrics[$endpointKey]['total_duration'] / $metrics[$endpointKey]['count'];
        $metrics[$endpointKey]['max_duration'] = max($metrics[$endpointKey]['max_duration'], $duration);

        if ($statusCode >= 400) {
            $metrics[$endpointKey]['errors']++;
        }

        Cache::put($metricsKey, $metrics, self::CACHE_DURATION_DAILY);

        return $duration;
    }

    /**
     * Lấy API metrics cho monitoring
     */
    public function getApiMetrics($hours = 24)
    {
        $metrics = [];

        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $hourMetrics = Cache::get("api_metrics_{$hour}", []);

            foreach ($hourMetrics as $endpoint => $data) {
                if (!isset($metrics[$endpoint])) {
                    $metrics[$endpoint] = [
                        'total_requests' => 0,
                        'total_duration' => 0,
                        'avg_duration' => 0,
                        'max_duration' => 0,
                        'total_errors' => 0,
                        'error_rate' => 0,
                    ];
                }

                $metrics[$endpoint]['total_requests'] += $data['count'];
                $metrics[$endpoint]['total_duration'] += $data['total_duration'];
                $metrics[$endpoint]['max_duration'] = max($metrics[$endpoint]['max_duration'], $data['max_duration']);
                $metrics[$endpoint]['total_errors'] += $data['errors'];
            }
        }

        // Tính toán avg duration và error rate
        foreach ($metrics as $endpoint => &$data) {
            if ($data['total_requests'] > 0) {
                $data['avg_duration'] = round($data['total_duration'] / $data['total_requests'], 3);
                $data['error_rate'] = round(($data['total_errors'] / $data['total_requests']) * 100, 2);
            }
        }

        return $metrics;
    }

    /**
     * Database query optimization helper
     */
    public function optimizeQuery($query, $relations = [], $selects = ['*'])
    {
        if (!empty($relations)) {
            $query->with($relations);
        }

        if ($selects !== ['*']) {
            $query->select($selects);
        }

        return $query;
    }

    /**
     * Warm up cache cho các endpoints quan trọng
     */
    public function warmUpCache()
    {
        Log::info('Starting API cache warm up');

        // Warm up stats
        $this->getCachedStats();

        // Warm up hot threads
        $this->getCachedHotThreads();

        // Warm up first few pages of threads
        for ($page = 1; $page <= 3; $page++) {
            $this->getCachedThreads($page);
        }

        Log::info('API cache warm up completed');
    }
}
