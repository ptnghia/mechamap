<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageAnalytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Store analytics data
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'event' => 'required|string',
                'pageId' => 'nullable|string',
                'url' => 'nullable|url',
                'title' => 'nullable|string',
                'referrer' => 'nullable|string',
                'userAgent' => 'nullable|string',
                'sessionId' => 'nullable|string',
                'userId' => 'nullable|integer',
                'timestamp' => 'nullable|date',
                'viewport' => 'nullable|array',
                'screen' => 'nullable|array',
                'device' => 'nullable|array',
                'milestone' => 'nullable|string',
                'readingTime' => 'nullable|integer',
                'scrollDepth' => 'nullable|integer',
                'interaction' => 'nullable|array',
                'platform' => 'nullable|string',
                'metrics' => 'nullable|array',
                'timeOnPage' => 'nullable|integer',
                'maxScrollDepth' => 'nullable|integer',
                'interactions' => 'nullable|integer',
                'visible' => 'nullable|boolean'
            ]);

            // Store analytics data
            $this->storeAnalyticsData($data);

            // Handle specific events
            switch ($data['event']) {
                case 'page_view':
                    $this->handlePageView($data);
                    break;
                case 'scroll_milestone':
                    $this->handleScrollMilestone($data);
                    break;
                case 'reading_milestone':
                    $this->handleReadingMilestone($data);
                    break;
                case 'social_share':
                    $this->handleSocialShare($data);
                    break;
                case 'page_exit':
                    $this->handlePageExit($data);
                    break;
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Analytics error: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get page view count
     */
    public function getViewCount(string $pageId): JsonResponse
    {
        try {
            $page = Page::where('slug', $pageId)->first();
            
            if (!$page) {
                return response()->json(['success' => false, 'message' => 'Page not found'], 404);
            }

            return response()->json([
                'success' => true,
                'count' => $page->view_count
            ]);

        } catch (\Exception $e) {
            Log::error('View count error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Get analytics dashboard data
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7d'); // 1d, 7d, 30d, 90d
            
            $data = [
                'pageViews' => $this->getPageViews($period),
                'topPages' => $this->getTopPages($period),
                'deviceStats' => $this->getDeviceStats($period),
                'referrerStats' => $this->getReferrerStats($period),
                'readingTime' => $this->getReadingTimeStats($period),
                'scrollDepth' => $this->getScrollDepthStats($period),
                'socialShares' => $this->getSocialShareStats($period)
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Store analytics data in database
     */
    private function storeAnalyticsData(array $data): void
    {
        // Create analytics record if table exists
        if (class_exists(PageAnalytics::class)) {
            PageAnalytics::create([
                'event_type' => $data['event'],
                'page_id' => $this->getPageIdFromSlug($data['pageId'] ?? null),
                'session_id' => $data['sessionId'] ?? null,
                'user_id' => $data['userId'] ?? null,
                'url' => $data['url'] ?? null,
                'referrer' => $data['referrer'] ?? null,
                'user_agent' => $data['userAgent'] ?? null,
                'device_info' => $data['device'] ?? null,
                'viewport_info' => $data['viewport'] ?? null,
                'event_data' => $data,
                'created_at' => $data['timestamp'] ?? now()
            ]);
        }

        // Store in cache for real-time stats
        $cacheKey = "analytics:{$data['event']}:" . date('Y-m-d');
        $current = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $current + 1, now()->addDays(7));
    }

    /**
     * Handle page view event
     */
    private function handlePageView(array $data): void
    {
        if (isset($data['pageId'])) {
            $page = Page::where('slug', $data['pageId'])->first();
            if ($page) {
                $page->increment('view_count');
                
                // Update cache
                Cache::forget("page_views:{$page->id}");
            }
        }

        // Track daily page views
        $cacheKey = "daily_page_views:" . date('Y-m-d');
        Cache::increment($cacheKey, 1);
        Cache::expire($cacheKey, 86400 * 7); // 7 days
    }

    /**
     * Handle scroll milestone
     */
    private function handleScrollMilestone(array $data): void
    {
        $cacheKey = "scroll_milestone:{$data['milestone']}:" . date('Y-m-d');
        Cache::increment($cacheKey, 1);
    }

    /**
     * Handle reading milestone
     */
    private function handleReadingMilestone(array $data): void
    {
        $cacheKey = "reading_milestone:{$data['milestone']}:" . date('Y-m-d');
        Cache::increment($cacheKey, 1);
    }

    /**
     * Handle social share
     */
    private function handleSocialShare(array $data): void
    {
        $cacheKey = "social_share:{$data['platform']}:" . date('Y-m-d');
        Cache::increment($cacheKey, 1);
    }

    /**
     * Handle page exit
     */
    private function handlePageExit(array $data): void
    {
        // Store session data
        $sessionData = [
            'timeOnPage' => $data['timeOnPage'] ?? 0,
            'readingTime' => $data['readingTime'] ?? 0,
            'maxScrollDepth' => $data['maxScrollDepth'] ?? 0,
            'interactions' => $data['interactions'] ?? 0
        ];

        $cacheKey = "session_data:" . ($data['sessionId'] ?? 'unknown');
        Cache::put($cacheKey, $sessionData, now()->addHours(24));
    }

    /**
     * Get page views for period
     */
    private function getPageViews(string $period): array
    {
        $days = $this->getPeriodDays($period);
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $cacheKey = "daily_page_views:{$date}";
            $views = Cache::get($cacheKey, 0);
            
            $data[] = [
                'date' => $date,
                'views' => $views
            ];
        }

        return $data;
    }

    /**
     * Get top pages for period
     */
    private function getTopPages(string $period): array
    {
        return Page::orderBy('view_count', 'desc')
            ->limit(10)
            ->get(['title', 'slug', 'view_count'])
            ->toArray();
    }

    /**
     * Get device stats
     */
    private function getDeviceStats(string $period): array
    {
        // This would be implemented with proper analytics table
        return [
            'desktop' => 65,
            'mobile' => 30,
            'tablet' => 5
        ];
    }

    /**
     * Get referrer stats
     */
    private function getReferrerStats(string $period): array
    {
        return [
            'direct' => 40,
            'google' => 35,
            'social' => 15,
            'other' => 10
        ];
    }

    /**
     * Get reading time stats
     */
    private function getReadingTimeStats(string $period): array
    {
        return [
            'average' => 180, // seconds
            'median' => 120,
            'bounce_rate' => 25
        ];
    }

    /**
     * Get scroll depth stats
     */
    private function getScrollDepthStats(string $period): array
    {
        return [
            '25%' => 85,
            '50%' => 70,
            '75%' => 45,
            '100%' => 25
        ];
    }

    /**
     * Get social share stats
     */
    private function getSocialShareStats(string $period): array
    {
        $days = $this->getPeriodDays($period);
        $platforms = ['facebook', 'twitter', 'linkedin'];
        $data = [];

        foreach ($platforms as $platform) {
            $total = 0;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $cacheKey = "social_share:{$platform}:{$date}";
                $total += Cache::get($cacheKey, 0);
            }
            $data[$platform] = $total;
        }

        return $data;
    }

    /**
     * Helper methods
     */
    private function getPageIdFromSlug(?string $slug): ?int
    {
        if (!$slug) return null;
        
        $page = Page::where('slug', $slug)->first();
        return $page ? $page->id : null;
    }

    private function getPeriodDays(string $period): int
    {
        return match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 7
        };
    }
}
