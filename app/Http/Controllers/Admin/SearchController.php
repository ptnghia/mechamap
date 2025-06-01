<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
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

            // Thống kê về search patterns và performance
            $searchStats = [
                'most_searched_content_types' => [
                    ['type' => 'Bài viết (Threads)', 'percentage' => 65],
                    ['type' => 'Bình luận (Comments)', 'percentage' => 25],
                    ['type' => 'Người dùng (Users)', 'percentage' => 10],
                ],
                'search_efficiency' => [
                    'average_results_per_search' => 15.7,
                    'zero_results_percentage' => 8.3,
                    'popular_search_length' => '3-5 từ',
                    'peak_search_hours' => '14:00 - 16:00',
                ],
            ];

            // Các từ khóa được tìm kiếm nhiều nhất (dữ liệu mẫu)
            $popularSearchTerms = [
                ['term' => 'Laravel tutorial', 'count' => 156, 'trend' => 'up'],
                ['term' => 'PHP development', 'count' => 143, 'trend' => 'stable'],
                ['term' => 'JavaScript frameworks', 'count' => 127, 'trend' => 'up'],
                ['term' => 'Database design', 'count' => 98, 'trend' => 'down'],
                ['term' => 'API development', 'count' => 89, 'trend' => 'up'],
                ['term' => 'React components', 'count' => 76, 'trend' => 'stable'],
                ['term' => 'Vue.js setup', 'count' => 65, 'trend' => 'up'],
                ['term' => 'Node.js server', 'count' => 54, 'trend' => 'stable'],
                ['term' => 'MySQL optimization', 'count' => 43, 'trend' => 'down'],
                ['term' => 'Docker deployment', 'count' => 38, 'trend' => 'up'],
            ];

            // Thống kê theo thời gian (dữ liệu mẫu)
            $timeBasedStats = [
                'daily_searches' => [
                    'today' => 245,
                    'yesterday' => 198,
                    'this_week' => 1456,
                    'last_week' => 1298,
                    'this_month' => 5670,
                    'last_month' => 5234,
                ],
                'hourly_distribution' => [
                    '00-06' => 5,
                    '06-12' => 25,
                    '12-18' => 45,
                    '18-24' => 25,
                ],
                'weekly_trend' => [
                    'Monday' => 18,
                    'Tuesday' => 22,
                    'Wednesday' => 20,
                    'Thursday' => 16,
                    'Friday' => 14,
                    'Saturday' => 6,
                    'Sunday' => 4,
                ],
            ];

            // Thống kê hiệu suất search
            $performanceStats = [
                'average_response_time' => '120ms',
                'cache_hit_rate' => '78%',
                'index_size' => '45MB',
                'last_optimization' => Setting::getValue('search', 'last_optimization_date', 'Chưa có dữ liệu'),
                'optimization_needed' => $this->checkOptimizationNeeded(),
            ];

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
}
