<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceSeller;
use App\Models\TechnicalDrawing;
use App\Models\Material;
use App\Models\Thread;
use Carbon\Carbon;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const ANALYTICS_CACHE_TTL = 1800; // 30 minutes
    const REALTIME_CACHE_TTL = 60; // 1 minute

    /**
     * Get or cache dashboard statistics
     */
    public function getDashboardStats()
    {
        return Cache::remember('dashboard_stats', self::CACHE_TTL, function () {
            return [
                'total_users' => User::count(),
                'total_products' => MarketplaceProduct::count(),
                'total_orders' => MarketplaceOrder::count(),
                'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount'),
                'active_sellers' => MarketplaceSeller::where('status', 'active')->count(),
                'pending_approvals' => MarketplaceProduct::where('status', 'pending')->count() +
                                     TechnicalDrawing::where('status', 'pending')->count(),
                'total_drawings' => TechnicalDrawing::count(),
                'total_materials' => Material::count(),
                'total_threads' => Thread::count(),
            ];
        });
    }

    /**
     * Get or cache analytics data
     */
    public function getAnalyticsData($period = 30)
    {
        $cacheKey = "analytics_data_{$period}";
        
        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($period) {
            $startDate = now()->subDays($period);
            
            return [
                'revenue_trend' => $this->getRevenueTrend($startDate),
                'user_growth' => $this->getUserGrowth($startDate),
                'order_stats' => $this->getOrderStats($startDate),
                'product_performance' => $this->getProductPerformance($startDate),
                'seller_performance' => $this->getSellerPerformance($startDate),
            ];
        });
    }

    /**
     * Get or cache real-time metrics
     */
    public function getRealtimeMetrics()
    {
        return Cache::remember('realtime_metrics', self::REALTIME_CACHE_TTL, function () {
            return [
                'online_users' => User::where('last_login_at', '>=', now()->subMinutes(5))->count(),
                'today_orders' => MarketplaceOrder::whereDate('created_at', today())->count(),
                'today_revenue' => MarketplaceOrder::whereDate('created_at', today())
                    ->where('payment_status', 'paid')->sum('total_amount'),
                'pending_approvals' => MarketplaceProduct::where('status', 'pending')->count() +
                                     TechnicalDrawing::where('status', 'pending')->count(),
                'active_sessions' => $this->getActiveSessions(),
            ];
        });
    }

    /**
     * Get or cache marketplace statistics
     */
    public function getMarketplaceStats($period = 30)
    {
        $cacheKey = "marketplace_stats_{$period}";
        
        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($period) {
            $startDate = now()->subDays($period);
            
            return [
                'total_sellers' => MarketplaceSeller::count(),
                'active_sellers' => MarketplaceSeller::where('status', 'active')->count(),
                'verified_sellers' => MarketplaceSeller::where('verification_status', 'verified')->count(),
                'total_products' => MarketplaceProduct::count(),
                'active_products' => MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count(),
                'conversion_rate' => $this->calculateConversionRate($startDate),
                'avg_order_value' => MarketplaceOrder::where('created_at', '>=', $startDate)->avg('total_amount'),
                'top_categories' => $this->getTopCategories($startDate),
                'revenue_by_seller_type' => $this->getRevenueBySellerType($startDate),
            ];
        });
    }

    /**
     * Get or cache technical statistics
     */
    public function getTechnicalStats($period = 30)
    {
        $cacheKey = "technical_stats_{$period}";
        
        return Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($period) {
            $startDate = now()->subDays($period);
            
            return [
                'total_drawings' => TechnicalDrawing::count(),
                'public_drawings' => TechnicalDrawing::where('visibility', 'public')->count(),
                'total_downloads' => TechnicalDrawing::sum('download_count'),
                'period_downloads' => TechnicalDrawing::where('created_at', '>=', $startDate)->sum('download_count'),
                'total_materials' => Material::count(),
                'approved_materials' => Material::where('status', 'approved')->count(),
                'popular_drawings' => $this->getPopularDrawings(),
                'material_usage' => $this->getMaterialUsage(),
                'drawing_types' => $this->getDrawingTypes(),
            ];
        });
    }

    /**
     * Clear specific cache
     */
    public function clearCache($key)
    {
        Cache::forget($key);
    }

    /**
     * Clear all analytics cache
     */
    public function clearAnalyticsCache()
    {
        $patterns = [
            'dashboard_stats',
            'analytics_data_*',
            'realtime_metrics',
            'marketplace_stats_*',
            'technical_stats_*',
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // For wildcard patterns, we'd need to implement cache tag clearing
                // For now, clear common periods
                $periods = [7, 30, 90, 365];
                foreach ($periods as $period) {
                    $key = str_replace('*', $period, $pattern);
                    Cache::forget($key);
                }
            } else {
                Cache::forget($pattern);
            }
        }
    }

    /**
     * Warm up cache with fresh data
     */
    public function warmUpCache()
    {
        // Warm up dashboard stats
        $this->getDashboardStats();
        
        // Warm up analytics for common periods
        $periods = [7, 30, 90];
        foreach ($periods as $period) {
            $this->getAnalyticsData($period);
            $this->getMarketplaceStats($period);
            $this->getTechnicalStats($period);
        }
        
        // Warm up real-time metrics
        $this->getRealtimeMetrics();
    }

    /**
     * Private helper methods
     */
    private function getRevenueTrend($startDate)
    {
        return MarketplaceOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getUserGrowth($startDate)
    {
        return User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as registrations')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getOrderStats($startDate)
    {
        return [
            'total_orders' => MarketplaceOrder::where('created_at', '>=', $startDate)->count(),
            'completed_orders' => MarketplaceOrder::where('created_at', '>=', $startDate)
                ->where('status', 'completed')->count(),
            'avg_order_value' => MarketplaceOrder::where('created_at', '>=', $startDate)->avg('total_amount'),
        ];
    }

    private function getProductPerformance($startDate)
    {
        return MarketplaceProduct::select('name', 'view_count', 'download_count')
            ->where('created_at', '>=', $startDate)
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getSellerPerformance($startDate)
    {
        return MarketplaceSeller::select(
                'business_name',
                'total_revenue',
                'total_sales'
            )
            ->where('created_at', '>=', $startDate)
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
    }

    private function calculateConversionRate($startDate)
    {
        $visitors = User::where('created_at', '>=', $startDate)->count();
        $orders = MarketplaceOrder::where('created_at', '>=', $startDate)->count();
        
        return $visitors > 0 ? round(($orders / $visitors) * 100, 2) : 0;
    }

    private function getTopCategories($startDate)
    {
        return DB::table('marketplace_products')
            ->join('product_categories', 'marketplace_products.product_category_id', '=', 'product_categories.id')
            ->select('product_categories.name', DB::raw('COUNT(*) as count'))
            ->where('marketplace_products.created_at', '>=', $startDate)
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRevenueBySellerType($startDate)
    {
        return DB::table('marketplace_orders')
            ->join('marketplace_order_items', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->join('marketplace_sellers', 'marketplace_order_items.seller_id', '=', 'marketplace_sellers.id')
            ->select('marketplace_sellers.seller_type', DB::raw('SUM(marketplace_order_items.total_amount) as revenue'))
            ->where('marketplace_orders.payment_status', 'paid')
            ->where('marketplace_orders.created_at', '>=', $startDate)
            ->groupBy('marketplace_sellers.seller_type')
            ->get();
    }

    private function getPopularDrawings()
    {
        return TechnicalDrawing::select('title', 'download_count', 'view_count')
            ->orderBy('download_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getMaterialUsage()
    {
        return Material::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();
    }

    private function getDrawingTypes()
    {
        return TechnicalDrawing::select('drawing_type', DB::raw('COUNT(*) as count'))
            ->groupBy('drawing_type')
            ->get();
    }

    private function getActiveSessions()
    {
        // This would require session tracking implementation
        return User::where('last_login_at', '>=', now()->subMinutes(15))->count();
    }
}
