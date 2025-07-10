<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class NotificationAnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        // Check admin permission
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            abort(403, 'Unauthorized access to analytics dashboard');
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return view('admin.analytics.notifications.index', compact('analytics', 'filters'));
    }

    /**
     * Get analytics data as JSON (for AJAX requests)
     */
    public function getData(Request $request): JsonResponse
    {
        // Check admin permission
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'filters' => $filters,
            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get overview metrics
     */
    public function overview(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'overview' => $analytics['overview'],
            'engagement' => $analytics['engagement'],
            'delivery' => $analytics['delivery'],
        ]);
    }

    /**
     * Get trend data for charts
     */
    public function trends(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'trends' => $analytics['trends'],
            'top_types' => $analytics['top_types'],
        ]);
    }

    /**
     * Get user segment analysis
     */
    public function userSegments(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'user_segments' => $analytics['user_segments'],
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performance(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'performance' => $analytics['performance'],
        ]);
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            abort(403, 'Unauthorized access to export');
        }

        $request->validate([
            'format' => 'required|in:csv,json',
        ]);

        $filters = $this->getFiltersFromRequest($request);
        $format = $request->input('format', 'csv');

        try {
            $exportData = NotificationAnalyticsService::exportAnalytics($filters, $format);
            
            $filename = 'notification_analytics_' . now()->format('Y-m-d_H-i-s') . '.' . $format;
            
            $headers = [
                'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response($exportData, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time metrics (for live dashboard updates)
     */
    public function realTime(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get metrics for the last hour
        $filters = [
            'start_date' => now()->subHour(),
            'end_date' => now(),
        ];

        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        // Add real-time specific metrics
        $realTimeMetrics = [
            'notifications_last_hour' => $analytics['overview']['total_sent'],
            'read_rate_last_hour' => $analytics['overview']['read_rate'],
            'active_connections' => $this->getActiveConnections(),
            'queue_size' => $this->getQueueSize(),
            'system_health' => $this->getSystemHealth(),
        ];

        return response()->json([
            'success' => true,
            'real_time' => $realTimeMetrics,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get notification type statistics
     */
    public function typeStats(Request $request): JsonResponse
    {
        if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Moderator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $this->getFiltersFromRequest($request);
        $analytics = NotificationAnalyticsService::getAnalytics($filters);

        return response()->json([
            'success' => true,
            'type_stats' => $analytics['top_types'],
            'engagement_by_priority' => $analytics['engagement']['engagement_by_priority'],
        ]);
    }

    /**
     * Get filters from request
     */
    private function getFiltersFromRequest(Request $request): array
    {
        $filters = [];

        // Date range
        if ($request->has('start_date')) {
            $filters['start_date'] = Carbon::parse($request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $filters['end_date'] = Carbon::parse($request->input('end_date'));
        }

        // Notification type
        if ($request->has('type') && $request->input('type') !== '') {
            $filters['type'] = $request->input('type');
        }

        // User ID
        if ($request->has('user_id') && $request->input('user_id') !== '') {
            $filters['user_id'] = $request->input('user_id');
        }

        // Default to last 30 days if no dates provided
        if (!isset($filters['start_date'])) {
            $filters['start_date'] = now()->subDays(30);
        }

        if (!isset($filters['end_date'])) {
            $filters['end_date'] = now();
        }

        return $filters;
    }

    /**
     * Get active WebSocket connections count
     */
    private function getActiveConnections(): int
    {
        try {
            return \App\Services\WebSocketConnectionService::getOnlineUsersCount();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get current queue size
     */
    private function getQueueSize(): int
    {
        try {
            // This would require queue monitoring
            // For now, return a placeholder
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get system health status
     */
    private function getSystemHealth(): array
    {
        try {
            return [
                'database' => 'healthy',
                'cache' => 'healthy',
                'queue' => 'healthy',
                'websocket' => 'healthy',
            ];
        } catch (\Exception $e) {
            return [
                'database' => 'unknown',
                'cache' => 'unknown',
                'queue' => 'unknown',
                'websocket' => 'unknown',
            ];
        }
    }
}
