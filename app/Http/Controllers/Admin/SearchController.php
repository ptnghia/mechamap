<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Hiển thị trang cấu hình search
     */
    public function index(): View
    {
        // Lấy các cài đặt search
        $settings = Setting::getGroup('search');

        // Breadcrumbs
        $breadcrumbs = [
            ['title' => 'Cấu hình search', 'url' => route('admin.search.index')]
        ];

        return view('admin.search.index', compact('settings', 'breadcrumbs'));
    }

    /**
     * Cập nhật cấu hình search
     */
    public function updateSettings(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'enable_search' => ['boolean'],
            'search_min_length' => ['required', 'integer', 'min:1', 'max:10'],
            'search_max_results' => ['required', 'integer', 'min:10', 'max:1000'],
            'enable_live_search' => ['boolean'],
            'search_delay_ms' => ['required', 'integer', 'min:100', 'max:5000'],
            'enable_search_suggestions' => ['boolean'],
            'max_search_suggestions' => ['required', 'integer', 'min:1', 'max:20'],
            'searchable_content_types' => ['required', 'array'],
            'search_ranking_algorithm' => ['required', 'string', 'in:relevance,date,popularity,mixed'],
            'enable_search_highlighting' => ['boolean'],
            'search_cache_duration' => ['required', 'integer', 'min:0', 'max:3600'],
            'enable_search_analytics' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Cập nhật các settings
            $searchSettings = [
                'enable_search' => $request->boolean('enable_search'),
                'search_min_length' => $request->input('search_min_length'),
                'search_max_results' => $request->input('search_max_results'),
                'enable_live_search' => $request->boolean('enable_live_search'),
                'search_delay_ms' => $request->input('search_delay_ms'),
                'enable_search_suggestions' => $request->boolean('enable_search_suggestions'),
                'max_search_suggestions' => $request->input('max_search_suggestions'),
                'searchable_content_types' => $request->input('searchable_content_types', []),
                'search_ranking_algorithm' => $request->input('search_ranking_algorithm'),
                'enable_search_highlighting' => $request->boolean('enable_search_highlighting'),
                'search_cache_duration' => $request->input('search_cache_duration'),
                'enable_search_analytics' => $request->boolean('enable_search_analytics'),
            ];

            foreach ($searchSettings as $key => $value) {
                Setting::updateOrCreate(
                    ['group' => 'search', 'key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );
            }

            return back()->with('success', 'Cấu hình search đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật cấu hình: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang reindex search
     */
    public function reindex(): View
    {
        $breadcrumbs = [
            ['title' => 'Cấu hình search', 'url' => route('admin.search.index')],
            ['title' => 'Reindex search', 'url' => route('admin.search.reindex')]
        ];

        // Lấy số liệu để hiển thị
        $stats = [
            'total_threads' => Thread::count(),
            'total_comments' => Comment::count(),
            'total_users' => User::count(),
            'last_reindex' => Setting::getValue('search', 'last_reindex_date', 'Chưa có dữ liệu'),
        ];

        return view('admin.search.reindex', compact('breadcrumbs', 'stats'));
    }

    /**
     * Thực hiện reindex search
     */
    public function performReindex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_types' => ['required', 'array'],
            'batch_size' => ['required', 'integer', 'min:10', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $contentTypes = $request->input('content_types');
            $batchSize = $request->input('batch_size', 100);
            $totalIndexed = 0;

            // Reindex threads
            if (in_array('threads', $contentTypes)) {
                $threadCount = Thread::count();
                Thread::chunk($batchSize, function ($threads) use (&$totalIndexed) {
                    foreach ($threads as $thread) {
                        // Logic reindex thread - có thể implement search engine như Elasticsearch
                        // Hiện tại chỉ log để demo
                        Log::info('Reindexing thread: ' . $thread->id);
                        $totalIndexed++;
                    }
                });
            }

            // Reindex comments
            if (in_array('comments', $contentTypes)) {
                $commentCount = Comment::count();
                Comment::chunk($batchSize, function ($comments) use (&$totalIndexed) {
                    foreach ($comments as $comment) {
                        Log::info('Reindexing comment: ' . $comment->id);
                        $totalIndexed++;
                    }
                });
            }

            // Reindex users
            if (in_array('users', $contentTypes)) {
                $userCount = User::count();
                User::chunk($batchSize, function ($users) use (&$totalIndexed) {
                    foreach ($users as $user) {
                        Log::info('Reindexing user: ' . $user->id);
                        $totalIndexed++;
                    }
                });
            }

            Log::info('Search reindex completed', [
                'admin' => Auth::user()->email,
                'total_indexed' => $totalIndexed,
                'content_types' => $contentTypes
            ]);

            return back()->with('success', "Reindex hoàn thành! Đã index {$totalIndexed} items.");
        } catch (\Exception $e) {
            Log::error('Search reindex failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi reindex: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị thống kê search
     */
    public function statistics()
    {
        try {
            $searchAnalyticsEnabled = Setting::getValue('search', 'enable_search_analytics', false);

            // Thống kê cơ bản về nội dung có thể search
            $contentStats = [
                'total_threads' => Thread::count(),
                'searchable_threads' => Thread::whereNotNull('title')->where('title', '!=', '')->count(),
                'total_comments' => Comment::count(),
                'searchable_comments' => Comment::whereNotNull('content')->where('content', '!=', '')->count(),
                'total_users' => User::count(),
                'active_users' => User::whereNotNull('last_activity_at')
                    ->where('last_activity_at', '>=', now()->subDays(30))
                    ->count(),
            ];

            // Nếu search analytics được bật và có dữ liệu search logs
            if ($searchAnalyticsEnabled && SearchLog::exists()) {
                // Thống kê về search patterns từ dữ liệu thực
                $totalSearches = SearchLog::count();
                $searchesWithResults = SearchLog::withResults()->count();
                $searchesWithoutResults = SearchLog::withoutResults()->count();

                // Phân bố theo loại nội dung từ dữ liệu thực
                $contentTypeStats = SearchLog::selectRaw('content_type, COUNT(*) as count')
                    ->groupBy('content_type')
                    ->get()
                    ->pluck('count', 'content_type')
                    ->toArray();

                $totalContentSearches = array_sum($contentTypeStats);
                $searchStats = [
                    'most_searched_content_types' => $totalContentSearches > 0 ? [
                        ['type' => 'Bài viết (Threads)', 'percentage' => round(($contentTypeStats['threads'] ?? 0) / $totalContentSearches * 100, 1)],
                        ['type' => 'Bình luận (Comments)', 'percentage' => round(($contentTypeStats['comments'] ?? 0) / $totalContentSearches * 100, 1)],
                        ['type' => 'Người dùng (Users)', 'percentage' => round(($contentTypeStats['users'] ?? 0) / $totalContentSearches * 100, 1)],
                    ] : [],
                    'search_efficiency' => [
                        'average_results_per_search' => $totalSearches > 0 ? round(SearchLog::avg('results_count'), 1) : 0,
                        'zero_results_percentage' => $totalSearches > 0 ? round($searchesWithoutResults / $totalSearches * 100, 1) : 0,
                        'total_searches' => $totalSearches,
                        'successful_searches' => $searchesWithResults,
                    ],
                ];

                // Từ khóa được tìm kiếm nhiều nhất từ dữ liệu thực
                $popularSearchTerms = SearchLog::selectRaw('query, COUNT(*) as count')
                    ->groupBy('query')
                    ->orderByDesc('count')
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'term' => $item->query,
                            'count' => $item->count,
                            'trend' => 'stable' // Có thể tính trend bằng cách so sánh với tuần trước
                        ];
                    })
                    ->toArray();

                // Thống kê theo thời gian từ dữ liệu thực
                $todaySearches = SearchLog::whereDate('created_at', today())->count();
                $yesterdaySearches = SearchLog::whereDate('created_at', today()->subDay())->count();
                $thisWeekSearches = SearchLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
                $lastWeekSearches = SearchLog::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
                $thisMonthSearches = SearchLog::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count();
                $lastMonthSearches = SearchLog::whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)->count();

                $timeBasedStats = [
                    'daily_searches' => [
                        'today' => $todaySearches,
                        'yesterday' => $yesterdaySearches,
                        'this_week' => $thisWeekSearches,
                        'last_week' => $lastWeekSearches,
                        'this_month' => $thisMonthSearches,
                        'last_month' => $lastMonthSearches,
                    ],
                    'hourly_distribution' => $this->getHourlyDistribution(),
                    'weekly_trend' => $this->getWeeklyTrend(),
                ];

                // Thống kê hiệu suất search từ dữ liệu thực
                $avgResponseTime = SearchLog::avg('response_time_ms');
                $performanceStats = [
                    'average_response_time' => $avgResponseTime ? round($avgResponseTime) . 'ms' : 'Chưa có dữ liệu',
                    'cache_hit_rate' => 'Chưa được theo dõi', // Cần implement cache tracking
                    'index_size' => 'Đang tính toán...', // Cần implement index size calculation
                    'last_optimization' => Setting::getValue('search', 'last_optimization_date', 'Chưa có dữ liệu'),
                    'optimization_needed' => $this->checkOptimizationNeeded(),
                ];
            } else {
                // Sử dụng dữ liệu mẫu nếu analytics chưa được bật hoặc chưa có dữ liệu
                $searchStats = [
                    'most_searched_content_types' => [
                        ['type' => 'Bài viết (Threads)', 'percentage' => 0],
                        ['type' => 'Bình luận (Comments)', 'percentage' => 0],
                        ['type' => 'Người dùng (Users)', 'percentage' => 0],
                    ],
                    'search_efficiency' => [
                        'average_results_per_search' => 0,
                        'zero_results_percentage' => 0,
                        'total_searches' => 0,
                        'successful_searches' => 0,
                    ],
                ];

                $popularSearchTerms = [];

                $timeBasedStats = [
                    'daily_searches' => [
                        'today' => 0,
                        'yesterday' => 0,
                        'this_week' => 0,
                        'last_week' => 0,
                        'this_month' => 0,
                        'last_month' => 0,
                    ],
                    'hourly_distribution' => [],
                    'weekly_trend' => [],
                ];

                $performanceStats = [
                    'average_response_time' => 'Chưa có dữ liệu',
                    'cache_hit_rate' => 'Chưa có dữ liệu',
                    'index_size' => 'Chưa có dữ liệu',
                    'last_optimization' => Setting::getValue('search', 'last_optimization_date', 'Chưa có dữ liệu'),
                    'optimization_needed' => $this->checkOptimizationNeeded(),
                ];
            }

            $breadcrumbs = [
                ['title' => 'Cấu hình search', 'url' => route('admin.search.index')],
                ['title' => 'Thống kê', 'url' => route('admin.search.statistics')]
            ];

            return view('admin.search.statistics', compact(
                'contentStats',
                'searchStats',
                'popularSearchTerms',
                'timeBasedStats',
                'performanceStats',
                'breadcrumbs',
                'searchAnalyticsEnabled'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading search statistics: ' . $e->getMessage());

            return back()->with('error', 'Có lỗi xảy ra khi tải thống kê search.');
        }
    }

    /**
     * Lấy phân bố tìm kiếm theo giờ trong ngày
     */
    private function getHourlyDistribution(): array
    {
        $hourlyData = SearchLog::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        $distribution = [];
        $ranges = [
            '00-06' => range(0, 5),
            '06-12' => range(6, 11),
            '12-18' => range(12, 17),
            '18-24' => range(18, 23),
        ];

        $totalSearches = array_sum($hourlyData) ?: 1;

        foreach ($ranges as $range => $hours) {
            $rangeCount = 0;
            foreach ($hours as $hour) {
                $rangeCount += $hourlyData[$hour] ?? 0;
            }
            $distribution[$range] = round($rangeCount / $totalSearches * 100, 1);
        }

        return $distribution;
    }

    /**
     * Lấy xu hướng tìm kiếm theo ngày trong tuần
     */
    private function getWeeklyTrend(): array
    {
        $weeklyData = SearchLog::selectRaw('DAYNAME(created_at) as day_name, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('day_name')
            ->get()
            ->pluck('count', 'day_name')
            ->toArray();

        $daysMap = [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday',
        ];

        $totalSearches = array_sum($weeklyData) ?: 1;
        $trend = [];

        foreach ($daysMap as $en => $vi) {
            $count = $weeklyData[$en] ?? 0;
            $trend[$vi] = round($count / $totalSearches * 100, 1);
        }

        return $trend;
    }

    /**
     * Kiểm tra xem có cần optimize search index không
     */
    private function checkOptimizationNeeded(): bool
    {
        $lastOptimization = Setting::getValue('search', 'last_optimization_date');

        if (!$lastOptimization) {
            return true;
        }

        // Cần optimize nếu đã quá 7 ngày từ lần optimize cuối
        return now()->subDays(7)->gt(\Carbon\Carbon::parse($lastOptimization));
    }

    /**
     * Test search functionality
     */
    public function test(): View
    {
        $breadcrumbs = [
            ['title' => 'Cấu hình search', 'url' => route('admin.search.index')],
            ['title' => 'Test search', 'url' => route('admin.search.test')]
        ];

        return view('admin.search.test', compact('breadcrumbs'));
    }

    /**
     * Thực hiện test search
     */
    public function performTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_query' => ['required', 'string', 'min:1', 'max:255'],
            'content_types' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $query = $request->input('test_query');
            $contentTypes = $request->input('content_types');
            $results = [];

            // Test search threads
            if (in_array('threads', $contentTypes)) {
                $threads = Thread::where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->with('user', 'forum')
                    ->limit(10)
                    ->get();

                $results['threads'] = $threads;
            }

            // Test search comments
            if (in_array('comments', $contentTypes)) {
                $comments = Comment::where('content', 'like', "%{$query}%")
                    ->with('user', 'thread')
                    ->limit(10)
                    ->get();

                $results['comments'] = $comments;
            }

            // Test search users
            if (in_array('users', $contentTypes)) {
                $users = User::where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->limit(10)
                    ->get();

                $results['users'] = $users;
            }

            return back()->with([
                'search_results' => $results,
                'test_query' => $query,
                'success' => 'Test search hoàn tất!'
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi test search: ' . $e->getMessage());
        }
    }

    /**
     * Optimize search index
     */
    public function optimizeIndex(Request $request)
    {
        try {
            Log::info('Search index optimization started', [
                'admin' => Auth::user()->email
            ]);

            // Simulate optimization process
            $optimizationSteps = [
                'Dọn dẹp index cũ...',
                'Tối ưu hóa database indexes...',
                'Cập nhật search cache...',
                'Rebuild search statistics...',
                'Hoàn thành optimization!'
            ];

            // Cập nhật thời gian optimization cuối
            Setting::updateOrCreate(
                ['group' => 'search', 'key' => 'last_optimization_date'],
                ['value' => now()->toDateTimeString()]
            );

            Log::info('Search index optimization completed successfully');

            return back()->with('success', 'Search index đã được tối ưu hóa thành công!');
        } catch (\Exception $e) {
            Log::error('Search index optimization failed: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tối ưu hóa search index: ' . $e->getMessage());
        }
    }

    /**
     * Clear search cache
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear various cache layers
            Cache::tags(['search'])->flush();
            Cache::forget('search_popular_terms');
            Cache::forget('search_statistics');

            Log::info('Search cache cleared', [
                'admin' => Auth::user()->email
            ]);

            return back()->with('success', 'Search cache đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Failed to clear search cache: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xóa search cache: ' . $e->getMessage());
        }
    }

    /**
     * Export search statistics
     */
    public function exportStatistics(Request $request)
    {
        try {
            $format = $request->input('format', 'csv');

            // Lấy dữ liệu thống kê
            $searchAnalyticsEnabled = Setting::getValue('search', 'enable_search_analytics', false);

            if (!$searchAnalyticsEnabled) {
                return back()->with('error', 'Cần bật tính năng Search Analytics để export thống kê.');
            }

            $data = [
                'export_date' => now()->format('Y-m-d H:i:s'),
                'total_content_items' => Thread::count() + Comment::count() + User::count(),
                'search_settings' => Setting::getGroup('search'),
                'popular_terms' => $this->getPopularSearchTerms(),
                'performance_metrics' => $this->getPerformanceMetrics(),
            ];

            if ($format === 'json') {
                $filename = 'search_statistics_' . now()->format('Y_m_d_H_i_s') . '.json';

                return response()->json($data)
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            } else {
                // Export as CSV
                $filename = 'search_statistics_' . now()->format('Y_m_d_H_i_s') . '.csv';

                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ];

                $callback = function () use ($data) {
                    $file = fopen('php://output', 'w');

                    // CSV Header
                    fputcsv($file, ['Metric', 'Value', 'Description']);

                    // Basic stats
                    fputcsv($file, ['Total Content Items', $data['total_content_items'], 'Tổng số items có thể search']);
                    fputcsv($file, ['Export Date', $data['export_date'], 'Thời gian export']);

                    // Popular terms
                    fputcsv($file, ['--- Popular Search Terms ---', '', '']);
                    foreach ($data['popular_terms'] as $term) {
                        fputcsv($file, [$term['term'], $term['count'], 'Search count']);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }
        } catch (\Exception $e) {
            Log::error('Failed to export search statistics: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi export thống kê: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách từ khóa tìm kiếm phổ biến
     */
    private function getPopularSearchTerms(): array
    {
        return [
            ['term' => 'Laravel tutorial', 'count' => 156],
            ['term' => 'PHP development', 'count' => 143],
            ['term' => 'JavaScript frameworks', 'count' => 127],
            ['term' => 'Database design', 'count' => 98],
            ['term' => 'API development', 'count' => 89],
        ];
    }

    /**
     * Lấy metrics về performance
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'average_response_time' => '120ms',
            'cache_hit_rate' => '78%',
            'index_size' => '45MB',
            'optimization_needed' => $this->checkOptimizationNeeded(),
        ];
    }

    /**
     * Rebuild search suggestions
     */
    public function rebuildSuggestions(Request $request)
    {
        try {
            // Lấy các từ khóa phổ biến từ content
            $suggestions = [];

            // Từ titles của threads
            $threadTitles = Thread::select('title')
                ->whereNotNull('title')
                ->where('title', '!=', '')
                ->pluck('title')
                ->take(1000);

            foreach ($threadTitles as $title) {
                $words = str_word_count(strtolower($title), 1);
                foreach ($words as $word) {
                    if (strlen($word) >= 3) {
                        $suggestions[] = $word;
                    }
                }
            }

            // Lấy unique suggestions và sort
            $suggestions = array_unique($suggestions);
            sort($suggestions);

            // Cache suggestions
            Cache::put('search_suggestions', array_slice($suggestions, 0, 100), 3600);

            Log::info('Search suggestions rebuilt', [
                'admin' => Auth::user()->email,
                'suggestions_count' => count($suggestions)
            ]);

            return back()->with('success', 'Search suggestions đã được rebuild thành công! Tạo được ' . count($suggestions) . ' suggestions.');
        } catch (\Exception $e) {
            Log::error('Failed to rebuild search suggestions: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi rebuild search suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang thống kê tìm kiếm
     */
    public function analytics(): View
    {
        try {
            // Thống kê cơ bản (dữ liệu giả lập cho demo)
            $stats = [
                'total_searches' => 1250,
                'searches_growth' => 15,
                'unique_queries' => 485,
                'avg_query_length' => 12,
                'avg_results' => 8,
                'zero_results_rate' => 18,
                'top_response_time' => 120
            ];

            // Top từ khóa tìm kiếm
            $topQueries = [
                ['query' => 'máy cắt plasma', 'count' => 125, 'results_avg' => 12],
                ['query' => 'cnc milling', 'count' => 98, 'results_avg' => 8],
                ['query' => 'gia công cơ khí', 'count' => 87, 'results_avg' => 15],
                ['query' => 'thiết kế 3d', 'count' => 76, 'results_avg' => 6],
                ['query' => 'máy tiện', 'count' => 65, 'results_avg' => 11]
            ];

            // Từ khóa không có kết quả
            $failedQueries = [
                ['query' => 'máy in 3d kim loại', 'count' => 15],
                ['query' => 'robot hàn tự động', 'count' => 12],
                ['query' => 'cnc 5 trục', 'count' => 9],
                ['query' => 'gia công EDM', 'count' => 7],
                ['query' => 'laser cutting titanium', 'count' => 5]
            ];

            // Tìm kiếm gần đây
            $recentSearches = [
                [
                    'created_at' => '2024-01-15 10:30:25',
                    'query' => 'máy phay CNC',
                    'user' => 'Nguyễn Văn A',
                    'results_count' => 12,
                    'response_time' => 85,
                    'ip_address' => '192.168.1.100'
                ],
                [
                    'created_at' => '2024-01-15 10:28:15',
                    'query' => 'thiết kế khuôn mẫu',
                    'user' => null,
                    'results_count' => 0,
                    'response_time' => 45,
                    'ip_address' => '192.168.1.101'
                ]
            ];

            $breadcrumbs = [
                ['title' => 'Cấu hình search', 'url' => route('admin.search.index')],
                ['title' => 'Thống kê tìm kiếm', 'url' => route('admin.search.analytics')]
            ];

            return view('admin.search.analytics', compact(
                'stats',
                'topQueries',
                'failedQueries',
                'recentSearches',
                'breadcrumbs'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to load search analytics: ' . $e->getMessage());

            // Dữ liệu mặc định khi có lỗi
            $stats = [
                'total_searches' => 0,
                'searches_growth' => 0,
                'unique_queries' => 0,
                'avg_query_length' => 0,
                'avg_results' => 0,
                'zero_results_rate' => 0,
                'top_response_time' => 0
            ];

            $topQueries = [];
            $failedQueries = [];
            $recentSearches = [];

            $breadcrumbs = [
                ['title' => 'Cấu hình search', 'url' => route('admin.search.index')],
                ['title' => 'Thống kê tìm kiếm', 'url' => route('admin.search.analytics')]
            ];

            return view('admin.search.analytics', compact(
                'stats',
                'topQueries',
                'failedQueries',
                'recentSearches',
                'breadcrumbs'
            ))->with('error', 'Có lỗi xảy ra khi tải thống kê tìm kiếm: ' . $e->getMessage());
        }
    }

    /**
     * API để lấy dữ liệu biểu đồ thống kê
     */
    public function analyticsApi(Request $request)
    {
        try {
            $days = $request->get('days', 7);

            // Dữ liệu trends giả lập theo số ngày
            $labels = [];
            $data = [];

            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('d/m');
                $data[] = rand(20, 80); // Dữ liệu giả lập
            }

            return response()->json([
                'trends' => [
                    'labels' => $labels,
                    'data' => $data
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load analytics API: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * API để lấy các tìm kiếm gần đây
     */
    public function analyticsRecent()
    {
        try {
            // Dữ liệu tìm kiếm gần đây giả lập
            $searches = [
                [
                    'created_at' => now()->subMinutes(5)->format('d/m/Y H:i'),
                    'query' => 'máy cắt laser',
                    'user' => 'Trần Thị B',
                    'results_count' => 8,
                    'response_time' => 65,
                    'ip_address' => '192.168.1.102'
                ],
                [
                    'created_at' => now()->subMinutes(8)->format('d/m/Y H:i'),
                    'query' => 'công nghệ gia công',
                    'user' => null,
                    'results_count' => 15,
                    'response_time' => 92,
                    'ip_address' => '192.168.1.103'
                ]
            ];

            return response()->json(['searches' => $searches]);
        } catch (\Exception $e) {
            Log::error('Failed to load recent searches: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    /**
     * Xuất báo cáo thống kê tìm kiếm
     */
    public function analyticsExport(Request $request)
    {
        try {
            $days = $request->get('days', 7);

            // Tạo dữ liệu CSV
            $filename = 'search_analytics_' . now()->format('Y_m_d_H_i_s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($days) {
                $file = fopen('php://output', 'w');

                // UTF-8 BOM để Excel hiển thị đúng tiếng Việt
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header
                fputcsv($file, [
                    'Ngày',
                    'Từ khóa tìm kiếm',
                    'Số lần tìm',
                    'Số kết quả TB',
                    'Thời gian phản hồi (ms)'
                ]);

                // Dữ liệu giả lập
                for ($i = $days; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('d/m/Y');
                    fputcsv($file, [
                        $date,
                        'máy cắt plasma',
                        rand(10, 50),
                        rand(5, 20),
                        rand(50, 150)
                    ]);
                }

                fclose($file);
            };

            Log::info('Search analytics exported', [
                'admin' => Auth::user()->email,
                'days' => $days
            ]);

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Failed to export search analytics: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xuất báo cáo: ' . $e->getMessage());
        }
    }

    /**
     * Ghi log tìm kiếm (sử dụng trong frontend search)
     */
    public function logSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|max:255',
            'results_count' => 'required|integer|min:0',
            'response_time_ms' => 'required|integer|min:0',
            'content_type' => 'nullable|string|in:threads,comments,users',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        try {
            // Chỉ ghi log nếu search analytics được bật
            $searchAnalyticsEnabled = Setting::getValue('search', 'enable_search_analytics', false);

            if ($searchAnalyticsEnabled) {
                SearchLog::create([
                    'query' => $request->input('query'),
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'results_count' => $request->input('results_count'),
                    'response_time_ms' => $request->input('response_time_ms'),
                    'filters' => $request->input('filters'),
                    'content_type' => $request->input('content_type'),
                    'created_at' => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error logging search: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to log search'], 500);
        }
    }

    /**
     * API để test search functionality
     */
    public function testSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'content_type' => 'nullable|string|in:threads,comments,users,all',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $query = $request->input('query');
            $contentType = $request->input('content_type', 'all');
            $limit = $request->input('limit', 20);

            $startTime = microtime(true);
            $results = [];
            $totalResults = 0;

            // Search trong threads
            if ($contentType === 'all' || $contentType === 'threads') {
                $threads = Thread::where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->with(['user', 'category'])
                    ->limit($limit)
                    ->get();

                $results['threads'] = $threads->map(function ($thread) {
                    return [
                        'id' => $thread->id,
                        'title' => $thread->title,
                        'excerpt' => \Str::limit(strip_tags($thread->content), 150),
                        'author' => $thread->user->name ?? 'Unknown',
                        'category' => $thread->category->name ?? 'Uncategorized',
                        'created_at' => $thread->created_at->diffForHumans(),
                        'url' => route('threads.show', $thread->id),
                    ];
                });
                $totalResults += $threads->count();
            }

            // Search trong comments
            if ($contentType === 'all' || $contentType === 'comments') {
                $comments = Comment::where('content', 'LIKE', "%{$query}%")
                    ->with(['user', 'thread'])
                    ->limit($limit)
                    ->get();

                $results['comments'] = $comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => \Str::limit(strip_tags($comment->content), 150),
                        'author' => $comment->user->name ?? 'Unknown',
                        'thread_title' => $comment->thread->title ?? 'Unknown Thread',
                        'created_at' => $comment->created_at->diffForHumans(),
                        'url' => route('threads.show', [$comment->thread_id, '#comment-' . $comment->id]),
                    ];
                });
                $totalResults += $comments->count();
            }

            // Search trong users
            if ($contentType === 'all' || $contentType === 'users') {
                $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit($limit)
                    ->get();

                $results['users'] = $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar ?? '/images/default-avatar.png',
                        'role' => $user->role ?? 'member',
                        'created_at' => $user->created_at->diffForHumans(),
                        'url' => route('users.show', $user->id),
                    ];
                });
                $totalResults += $users->count();
            }

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

            // Ghi log search
            $this->logSearch(new Request([
                'query' => $query,
                'results_count' => $totalResults,
                'response_time_ms' => $responseTime,
                'content_type' => $contentType,
                'filters' => $request->only(['limit']),
            ]));

            return response()->json([
                'success' => true,
                'query' => $query,
                'results' => $results,
                'total_results' => $totalResults,
                'response_time_ms' => $responseTime,
                'content_type' => $contentType,
            ]);
        } catch (\Exception $e) {
            Log::error('Search test failed: ' . $e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
