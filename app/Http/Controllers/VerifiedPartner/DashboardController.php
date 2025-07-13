<?php

namespace App\Http\Controllers\VerifiedPartner;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 🎯 MechaMap Verified Partner Dashboard Controller
 * 
 * Controller cho verified_partner role (L10) - Đối tác đã xác minh
 * Có quyền cao nhất trong business partners với:
 * - Bán tất cả loại sản phẩm (digital, new_product, used_product)
 * - Commission rate thấp nhất (2.0%)
 * - Priority support
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:verified_partner']);
    }

    /**
     * Display verified partner dashboard
     * Premium dashboard with comprehensive business analytics
     */
    public function index(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return view('verified-partner.setup-required');
        }

        // Get comprehensive statistics
        $stats = $this->getVerifiedPartnerStats($seller);

        // Get recent orders across all product types
        $recentOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top performing products by category
        $topProducts = $this->getTopProductsByCategory($seller);

        // Get revenue analytics
        $revenueAnalytics = $this->getRevenueAnalytics($seller);

        // Get market insights
        $marketInsights = $this->getMarketInsights($seller);

        return view('verified-partner.dashboard', compact(
            'seller', 
            'stats', 
            'recentOrders', 
            'topProducts',
            'revenueAnalytics',
            'marketInsights'
        ));
    }

    /**
     * Get comprehensive verified partner statistics
     */
    private function getVerifiedPartnerStats($seller): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            // Product statistics
            'total_products' => MarketplaceProduct::where('seller_id', $seller->id)->count(),
            'active_products' => MarketplaceProduct::where('seller_id', $seller->id)->where('is_active', true)->count(),
            'digital_products' => MarketplaceProduct::where('seller_id', $seller->id)->where('product_type', 'digital')->count(),
            'physical_products' => MarketplaceProduct::where('seller_id', $seller->id)->whereIn('product_type', ['new_product', 'used_product'])->count(),

            // Order statistics
            'total_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'pending')->count(),
            'processing_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'processing')->count(),
            'completed_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'completed')->count(),

            // Revenue statistics
            'total_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)->sum('total_amount'),
            'monthly_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereDate('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'weekly_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereDate('created_at', '>=', $thisWeek)
                ->sum('total_amount'),
            'daily_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereDate('created_at', $today)
                ->sum('total_amount'),

            // Performance metrics
            'conversion_rate' => $this->calculateConversionRate($seller),
            'average_order_value' => MarketplaceOrderItem::where('seller_id', $seller->id)->avg('total_amount'),
            'customer_satisfaction' => $this->getCustomerSatisfactionScore($seller),
            'commission_rate' => 2.0, // Verified partners get lowest commission
        ];

        // Calculate growth rates
        $lastMonthRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereDate('created_at', '>=', $lastMonth)
            ->whereDate('created_at', '<', $thisMonth)
            ->sum('total_amount');

        $stats['revenue_growth'] = $lastMonthRevenue > 0 
            ? (($stats['monthly_revenue'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return $stats;
    }

    /**
     * Get top products by category
     */
    private function getTopProductsByCategory($seller): array
    {
        return [
            'digital' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereHas('product', function ($query) {
                    $query->where('product_type', 'digital');
                })
                ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_amount) as total_revenue'))
                ->groupBy('product_id', 'product_name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get(),

            'physical' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereHas('product', function ($query) {
                    $query->whereIn('product_type', ['new_product', 'used_product']);
                })
                ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_amount) as total_revenue'))
                ->groupBy('product_id', 'product_name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get revenue analytics data
     */
    private function getRevenueAnalytics($seller): array
    {
        $last12Months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            
            $last12Months[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return [
            'monthly_trend' => $last12Months,
            'product_type_breakdown' => $this->getRevenueByProductType($seller),
            'top_revenue_days' => $this->getTopRevenueDays($seller),
        ];
    }

    /**
     * Get revenue breakdown by product type
     */
    private function getRevenueByProductType($seller): array
    {
        return [
            'digital' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereHas('product', function ($query) {
                    $query->where('product_type', 'digital');
                })
                ->sum('total_amount'),
            
            'new_product' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereHas('product', function ($query) {
                    $query->where('product_type', 'new_product');
                })
                ->sum('total_amount'),
            
            'used_product' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->whereHas('product', function ($query) {
                    $query->where('product_type', 'used_product');
                })
                ->sum('total_amount'),
        ];
    }

    /**
     * Get top revenue days
     */
    private function getTopRevenueDays($seller): array
    {
        return MarketplaceOrderItem::where('seller_id', $seller->id)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as daily_revenue'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('daily_revenue', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get market insights for verified partners
     */
    private function getMarketInsights($seller): array
    {
        return [
            'market_position' => $this->getMarketPosition($seller),
            'trending_categories' => $this->getTrendingCategories(),
            'competitor_analysis' => $this->getCompetitorAnalysis($seller),
            'growth_opportunities' => $this->getGrowthOpportunities($seller),
        ];
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate($seller): float
    {
        // Simplified calculation - would need product view tracking for accurate rate
        $totalProducts = MarketplaceProduct::where('seller_id', $seller->id)->count();
        $totalOrders = MarketplaceOrderItem::where('seller_id', $seller->id)->count();
        
        return $totalProducts > 0 ? ($totalOrders / $totalProducts) * 100 : 0;
    }

    /**
     * Get customer satisfaction score
     */
    private function getCustomerSatisfactionScore($seller): float
    {
        // Placeholder - would integrate with review system
        return 4.5; // Out of 5
    }

    /**
     * Get market position
     */
    private function getMarketPosition($seller): array
    {
        return [
            'rank' => 'Top 10%',
            'category_leader' => 'Digital Products',
            'market_share' => 3.2,
        ];
    }

    /**
     * Get trending categories
     */
    private function getTrendingCategories(): array
    {
        return [
            'CAD Files' => 25.5,
            'Technical Drawings' => 18.3,
            'Automation Equipment' => 15.7,
            'IoT Devices' => 12.1,
        ];
    }

    /**
     * Get competitor analysis
     */
    private function getCompetitorAnalysis($seller): array
    {
        return [
            'direct_competitors' => 5,
            'market_advantage' => 'Premium Quality',
            'price_position' => 'Competitive',
        ];
    }

    /**
     * Get growth opportunities
     */
    private function getGrowthOpportunities($seller): array
    {
        return [
            'untapped_categories' => ['Renewable Energy', 'Smart Manufacturing'],
            'seasonal_trends' => 'Q4 typically shows 30% increase',
            'expansion_potential' => 'International markets',
        ];
    }
}
