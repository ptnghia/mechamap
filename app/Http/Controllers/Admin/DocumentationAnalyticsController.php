<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documentation;
use App\Models\DocumentationCategory;
use App\Models\DocumentationView;
use App\Models\DocumentationDownload;
use App\Models\DocumentationComment;
use App\Models\DocumentationRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentationAnalyticsController extends Controller
{
    /**
     * Display documentation analytics dashboard
     */
    public function index(Request $request)
    {
        $timeRange = $request->get('range', '30'); // days
        $startDate = now()->subDays($timeRange);

        // Overview Statistics
        $overviewStats = [
            'total_documents' => Documentation::count(),
            'published_documents' => Documentation::where('status', 'published')->count(),
            'draft_documents' => Documentation::where('status', 'draft')->count(),
            'total_views' => DocumentationView::count(),
            'total_downloads' => DocumentationDownload::count(),
            'total_comments' => DocumentationComment::count(),
            'total_ratings' => DocumentationRating::count(),
            'average_rating' => DocumentationRating::avg('rating'),
        ];

        // Views Analytics
        $viewsData = $this->getViewsAnalytics($startDate);

        // Popular Documents
        $popularDocs = Documentation::with(['category', 'author'])
            ->withCount(['views' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        // Category Performance
        $categoryStats = DocumentationCategory::withCount([
            'documentations',
            'documentations as published_count' => function ($query) {
                $query->where('status', 'published');
            }
        ])
        ->with(['documentations' => function ($query) use ($startDate) {
            $query->withCount(['views' => function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            }]);
        }])
        ->get()
        ->map(function ($category) {
            $totalViews = $category->documentations->sum('views_count');
            return [
                'name' => $category->name,
                'documents_count' => $category->documentations_count,
                'published_count' => $category->published_count,
                'total_views' => $totalViews,
                'avg_views_per_doc' => $category->documentations_count > 0 ? round($totalViews / $category->documentations_count, 2) : 0,
            ];
        });

        // Content Type Performance
        $contentTypeStats = Documentation::select('content_type', DB::raw('COUNT(*) as doc_count'))
            ->groupBy('content_type')
            ->get()
            ->mapWithKeys(function ($item) use ($startDate) {
                $viewsCount = DocumentationView::whereHas('documentation', function ($query) use ($item) {
                    $query->where('content_type', $item->content_type);
                })
                ->where('created_at', '>=', $startDate)
                ->count();

                return [$item->content_type => $viewsCount];
            });

        // User Engagement
        $engagementStats = [
            'avg_time_spent' => DocumentationView::where('created_at', '>=', $startDate)->avg('time_spent'),
            'avg_scroll_percentage' => DocumentationView::where('created_at', '>=', $startDate)->avg('scroll_percentage'),
            'bounce_rate' => $this->calculateBounceRate($startDate),
            'return_visitor_rate' => $this->calculateReturnVisitorRate($startDate),
        ];

        // Recent Activity
        $recentActivity = [
            'recent_views' => DocumentationView::with(['documentation', 'user'])
                ->latest()
                ->limit(10)
                ->get(),
            'recent_comments' => DocumentationComment::with(['documentation', 'user'])
                ->latest()
                ->limit(10)
                ->get(),
            'recent_downloads' => DocumentationDownload::with(['documentation', 'user'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        // Search Analytics
        $searchStats = $this->getSearchAnalytics($startDate);

        return view('admin.documentation.analytics.index', compact(
            'overviewStats',
            'viewsData',
            'popularDocs',
            'categoryStats',
            'contentTypeStats',
            'engagementStats',
            'recentActivity',
            'searchStats',
            'timeRange'
        ));
    }

    /**
     * Get views analytics data
     */
    private function getViewsAnalytics($startDate)
    {
        $viewsByDay = DocumentationView::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('views', 'date');

        $uniqueViewsByDay = DocumentationView::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(DISTINCT user_id) as unique_views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('unique_views', 'date');

        return [
            'daily_views' => $viewsByDay,
            'daily_unique_views' => $uniqueViewsByDay,
            'total_views_period' => $viewsByDay->sum(),
            'total_unique_views_period' => DocumentationView::where('created_at', '>=', $startDate)
                ->distinct('user_id')
                ->count(),
        ];
    }

    /**
     * Calculate bounce rate
     */
    private function calculateBounceRate($startDate)
    {
        $totalSessions = DocumentationView::where('created_at', '>=', $startDate)->count();
        $bounceSessions = DocumentationView::where('created_at', '>=', $startDate)
            ->where('time_spent', '<', 30) // Less than 30 seconds
            ->count();

        return $totalSessions > 0 ? round(($bounceSessions / $totalSessions) * 100, 2) : 0;
    }

    /**
     * Calculate return visitor rate
     */
    private function calculateReturnVisitorRate($startDate)
    {
        $totalUsers = DocumentationView::where('created_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();

        $returnUsers = DocumentationView::where('created_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return $totalUsers > 0 ? round(($returnUsers / $totalUsers) * 100, 2) : 0;
    }

    /**
     * Get search analytics
     */
    private function getSearchAnalytics($startDate)
    {
        // This would require a search_logs table to track searches
        // For now, return placeholder data
        return [
            'total_searches' => 0,
            'top_search_terms' => [],
            'search_success_rate' => 0,
            'avg_results_per_search' => 0,
        ];
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $timeRange = $request->get('range', '30');
        $format = $request->get('format', 'csv');
        $startDate = now()->subDays($timeRange);

        $data = Documentation::with(['category', 'author'])
            ->withCount([
                'views' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                },
                'downloads' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                },
                'comments' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            ])
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($data, $timeRange);
        }

        return $this->exportToJson($data, $timeRange);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $timeRange)
    {
        $filename = "documentation_analytics_{$timeRange}days_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Category', 'Author', 'Status', 'Content Type',
                'Views', 'Downloads', 'Comments', 'Created At', 'Updated At'
            ]);

            // CSV data
            foreach ($data as $doc) {
                fputcsv($file, [
                    $doc->id,
                    $doc->title,
                    $doc->category->name ?? 'N/A',
                    $doc->author->name ?? 'N/A',
                    $doc->status,
                    $doc->content_type,
                    $doc->views_count,
                    $doc->downloads_count,
                    $doc->comments_count,
                    $doc->created_at->format('Y-m-d H:i:s'),
                    $doc->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to JSON
     */
    private function exportToJson($data, $timeRange)
    {
        $filename = "documentation_analytics_{$timeRange}days_" . now()->format('Y-m-d') . ".json";

        $exportData = [
            'export_date' => now()->toISOString(),
            'time_range_days' => $timeRange,
            'total_documents' => $data->count(),
            'documents' => $data->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'category' => $doc->category->name ?? null,
                    'author' => $doc->author->name ?? null,
                    'status' => $doc->status,
                    'content_type' => $doc->content_type,
                    'views' => $doc->views_count,
                    'downloads' => $doc->downloads_count,
                    'comments' => $doc->comments_count,
                    'created_at' => $doc->created_at->toISOString(),
                    'updated_at' => $doc->updated_at->toISOString(),
                ];
            })
        ];

        return response()->json($exportData)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Get real-time analytics data for AJAX requests
     */
    public function realtime(Request $request)
    {
        $data = [
            'current_online_users' => $this->getCurrentOnlineUsers(),
            'views_last_hour' => DocumentationView::where('created_at', '>=', now()->subHour())->count(),
            'popular_docs_now' => Documentation::withCount([
                'views' => function ($query) {
                    $query->where('created_at', '>=', now()->subHour());
                }
            ])
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'views_count']),
        ];

        return response()->json($data);
    }

    /**
     * Get current online users count
     */
    private function getCurrentOnlineUsers()
    {
        // Users who viewed documentation in the last 5 minutes
        return DocumentationView::where('created_at', '>=', now()->subMinutes(5))
            ->distinct('user_id')
            ->count();
    }
}
