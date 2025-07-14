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
 * 🎯 MechaMap Verified Partner Analytics Controller
 * 
 * Controller cho verified_partner role (L11) - Phân tích kinh doanh
 * Cung cấp báo cáo chi tiết về hiệu suất bán hàng
 */
class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:verified_partner']);
    }

    /**
     * Display analytics dashboard for verified partner
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        // Get date range from request or default to last 30 days
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Revenue analytics
        $revenueData = $this->getRevenueAnalytics($seller, $dateFrom, $dateTo);
        
        // Product performance
        $productPerformance = $this->getProductPerformance($seller, $dateFrom, $dateTo);
        
        // Order analytics
        $orderAnalytics = $this->getOrderAnalytics($seller, $dateFrom, $dateTo);
        
        // Customer analytics
        $customerAnalytics = $this->getCustomerAnalytics($seller, $dateFrom, $dateTo);
        
        // Trend data for charts
        $trendData = $this->getTrendData($seller, $dateFrom, $dateTo);

        return view('verified-partner.analytics.index', compact(
            'seller',
            'revenueData',
            'productPerformance',
            'orderAnalytics',
            'customerAnalytics',
            'trendData',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Get revenue analytics data
     */
    private function getRevenueAnalytics(MarketplaceSeller $seller, string $dateFrom, string $dateTo): array
    {
        $baseQuery = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->whereBetween('created_at', [$dateFrom, $dateTo]);

        $totalRevenue = $baseQuery->sum('total_price');
        $totalOrders = $baseQuery->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Previous period comparison
        $previousPeriodStart = Carbon::parse($dateFrom)->subDays(
            Carbon::parse($dateTo)->diffInDays(Carbon::parse($dateFrom))
        );
        $previousPeriodEnd = Carbon::parse($dateFrom)->subDay();

        $previousRevenue = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
          ->sum('total_price');

        $revenueGrowth = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'revenue_growth' => $revenueGrowth,
            'commission_rate' => 2.0, // Verified partners have 2% commission
            'net_revenue' => $totalRevenue * 0.98, // After 2% commission
        ];
    }

    /**
     * Get product performance data
     */
    private function getProductPerformance(MarketplaceSeller $seller, string $dateFrom, string $dateTo): array
    {
        $topProducts = MarketplaceOrderItem::select([
            'marketplace_products.id',
            'marketplace_products.name',
            'marketplace_products.product_type',
            DB::raw('COUNT(marketplace_order_items.id) as total_orders'),
            DB::raw('SUM(marketplace_order_items.quantity) as total_quantity'),
            DB::raw('SUM(marketplace_order_items.total_price) as total_revenue')
        ])
        ->join('marketplace_products', 'marketplace_order_items.product_id', '=', 'marketplace_products.id')
        ->where('marketplace_products.seller_id', $seller->id)
        ->whereBetween('marketplace_order_items.created_at', [$dateFrom, $dateTo])
        ->groupBy('marketplace_products.id', 'marketplace_products.name', 'marketplace_products.product_type')
        ->orderBy('total_revenue', 'desc')
        ->limit(10)
        ->get();

        $productTypeBreakdown = MarketplaceOrderItem::select([
            'marketplace_products.product_type',
            DB::raw('COUNT(marketplace_order_items.id) as total_orders'),
            DB::raw('SUM(marketplace_order_items.total_price) as total_revenue')
        ])
        ->join('marketplace_products', 'marketplace_order_items.product_id', '=', 'marketplace_products.id')
        ->where('marketplace_products.seller_id', $seller->id)
        ->whereBetween('marketplace_order_items.created_at', [$dateFrom, $dateTo])
        ->groupBy('marketplace_products.product_type')
        ->get();

        return [
            'top_products' => $topProducts,
            'product_type_breakdown' => $productTypeBreakdown,
            'total_active_products' => MarketplaceProduct::where('seller_id', $seller->id)
                ->where('status', 'active')
                ->count(),
        ];
    }

    /**
     * Get order analytics data
     */
    private function getOrderAnalytics(MarketplaceSeller $seller, string $dateFrom, string $dateTo): array
    {
        $orderStatusBreakdown = MarketplaceOrderItem::select([
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_price) as revenue')
        ])
        ->whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->groupBy('status')
        ->get();

        $fulfillmentRate = $orderStatusBreakdown->where('status', 'delivered')->sum('count') / 
                          max($orderStatusBreakdown->sum('count'), 1) * 100;

        return [
            'status_breakdown' => $orderStatusBreakdown,
            'fulfillment_rate' => $fulfillmentRate,
            'average_processing_time' => $this->getAverageProcessingTime($seller, $dateFrom, $dateTo),
        ];
    }

    /**
     * Get customer analytics data
     */
    private function getCustomerAnalytics(MarketplaceSeller $seller, string $dateFrom, string $dateTo): array
    {
        $uniqueCustomers = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
        ->distinct('marketplace_orders.user_id')
        ->count('marketplace_orders.user_id');

        $repeatCustomers = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
        ->select('marketplace_orders.user_id')
        ->groupBy('marketplace_orders.user_id')
        ->havingRaw('COUNT(*) > 1')
        ->count();

        $customerRetentionRate = $uniqueCustomers > 0 ? ($repeatCustomers / $uniqueCustomers) * 100 : 0;

        return [
            'unique_customers' => $uniqueCustomers,
            'repeat_customers' => $repeatCustomers,
            'customer_retention_rate' => $customerRetentionRate,
        ];
    }

    /**
     * Get trend data for charts
     */
    private function getTrendData(MarketplaceSeller $seller, string $dateFrom, string $dateTo): array
    {
        $dailyRevenue = MarketplaceOrderItem::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as revenue'),
            DB::raw('COUNT(*) as orders')
        ])
        ->whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get();

        return [
            'daily_revenue' => $dailyRevenue,
            'labels' => $dailyRevenue->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            }),
            'revenue_data' => $dailyRevenue->pluck('revenue'),
            'orders_data' => $dailyRevenue->pluck('orders'),
        ];
    }

    /**
     * Get average processing time
     */
    private function getAverageProcessingTime(MarketplaceSeller $seller, string $dateFrom, string $dateTo): float
    {
        $processedOrders = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->whereIn('status', ['shipped', 'delivered'])
        ->get();

        if ($processedOrders->isEmpty()) {
            return 0;
        }

        $totalProcessingTime = 0;
        foreach ($processedOrders as $order) {
            // Assuming we track when status changes (would need additional tracking)
            // For now, use a simplified calculation
            $totalProcessingTime += $order->created_at->diffInHours($order->updated_at);
        }

        return $totalProcessingTime / $processedOrders->count();
    }
}
