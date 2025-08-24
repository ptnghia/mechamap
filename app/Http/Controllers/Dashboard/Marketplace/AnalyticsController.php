<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * Analytics Controller cho Dashboard Marketplace
 * 
 * Hiển thị analytics và báo cáo cho seller
 */
class AnalyticsController extends BaseController
{
    /**
     * Hiển thị analytics dashboard
     */
    public function index(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('info', 'Please complete your seller setup first.');
        }

        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        // Get comprehensive analytics
        $analytics = [
            'overview' => $this->getOverviewAnalytics($seller, $dateRange),
            'sales' => $this->getSalesAnalytics($seller, $dateRange),
            'products' => $this->getProductAnalytics($seller, $dateRange),
            'customers' => $this->getCustomerAnalytics($seller, $dateRange),
            'trends' => $this->getTrendAnalytics($seller, $dateRange)];

        return $this->dashboardResponse('dashboard.marketplace.analytics.index', [
            'seller' => $seller,
            'analytics' => $analytics,
            'currentPeriod' => $period,
            'dateRange' => $dateRange]);
    }

    /**
     * Lấy sales analytics data (AJAX)
     */
    public function getSalesData(Request $request): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller not found'], 404);
        }

        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        $salesData = $this->getSalesAnalytics($seller, $dateRange);

        return response()->json([
            'success' => true,
            'data' => $salesData
        ]);
    }

    /**
     * Lấy product performance data (AJAX)
     */
    public function getProductPerformance(Request $request): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller not found'], 404);
        }

        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        $productData = $this->getProductAnalytics($seller, $dateRange);

        return response()->json([
            'success' => true,
            'data' => $productData
        ]);
    }

    /**
     * Lấy overview analytics
     */
    private function getOverviewAnalytics($seller, $dateRange)
    {
        $totalRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', $dateRange)
            ->sum('total_price');

        $totalOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->count();

        $totalProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->count();

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Previous period comparison
        $previousRange = $this->getPreviousPeriodRange($dateRange);
        $previousRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', $previousRange)
            ->sum('total_price');

        $revenueGrowth = $previousRevenue > 0 ? 
            (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'average_order_value' => $averageOrderValue,
            'revenue_growth' => $revenueGrowth];
    }

    /**
     * Lấy sales analytics
     */
    private function getSalesAnalytics($seller, $dateRange)
    {
        // Daily sales data
        $dailySales = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top selling products
        $topProducts = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total_price) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Sales by product type
        $salesByType = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->join('marketplace_products', 'marketplace_order_items.product_id', '=', 'marketplace_products.id')
            ->selectRaw('marketplace_products.product_type, SUM(marketplace_order_items.total_price) as revenue, COUNT(*) as orders')
            ->groupBy('marketplace_products.product_type')
            ->get();

        return [
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
            'sales_by_type' => $salesByType];
    }

    /**
     * Lấy product analytics
     */
    private function getProductAnalytics($seller, $dateRange)
    {
        // Product views
        $productViews = MarketplaceProduct::where('seller_id', $seller->id)
            ->selectRaw('name, view_count, sales_count')
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();

        // Conversion rates
        $conversionData = MarketplaceProduct::where('seller_id', $seller->id)
            ->selectRaw('name, view_count, sales_count, 
                CASE WHEN view_count > 0 THEN (sales_count / view_count) * 100 ELSE 0 END as conversion_rate')
            ->orderByDesc('conversion_rate')
            ->limit(10)
            ->get();

        // Product performance
        $productPerformance = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->with('product')
            ->selectRaw('product_id, SUM(quantity) as units_sold, SUM(total_price) as revenue, AVG(unit_price) as avg_price')
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'product_views' => $productViews,
            'conversion_data' => $conversionData,
            'product_performance' => $productPerformance];
    }

    /**
     * Lấy customer analytics
     */
    private function getCustomerAnalytics($seller, $dateRange)
    {
        // Top customers
        $topCustomers = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->join('users', 'marketplace_orders.customer_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, users.email, SUM(marketplace_order_items.total_price) as total_spent, COUNT(DISTINCT marketplace_orders.id) as order_count')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Customer acquisition
        $newCustomers = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->selectRaw('DATE(marketplace_order_items.created_at) as date, COUNT(DISTINCT marketplace_orders.customer_id) as new_customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'top_customers' => $topCustomers,
            'new_customers' => $newCustomers];
    }

    /**
     * Lấy trend analytics
     */
    private function getTrendAnalytics($seller, $dateRange)
    {
        // Revenue trend
        $revenueTrend = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Order trend
        $orderTrend = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'revenue_trend' => $revenueTrend,
            'order_trend' => $orderTrend];
    }

    /**
     * Lấy date range theo period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case '7days':
                return [now()->subDays(7), now()];
            case '30days':
                return [now()->subDays(30), now()];
            case '90days':
                return [now()->subDays(90), now()];
            case '1year':
                return [now()->subYear(), now()];
            case 'this_month':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'last_month':
                return [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];
            default:
                return [now()->subDays(30), now()];
        }
    }

    /**
     * Lấy previous period range để so sánh
     */
    private function getPreviousPeriodRange($currentRange)
    {
        $start = Carbon::parse($currentRange[0]);
        $end = Carbon::parse($currentRange[1]);
        $duration = $start->diffInDays($end);

        return [
            $start->copy()->subDays($duration + 1),
            $start->copy()->subDay()
        ];
    }
}
