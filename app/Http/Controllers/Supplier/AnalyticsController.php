<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    /**
     * Display supplier analytics dashboard
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $period = $request->get('period', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($period);

        // Get analytics data
        $analytics = [
            'revenue' => $this->getRevenueAnalytics($seller, $startDate),
            'orders' => $this->getOrderAnalytics($seller, $startDate),
            'products' => $this->getProductAnalytics($seller, $startDate),
            'customers' => $this->getCustomerAnalytics($seller, $startDate),
            'trends' => $this->getTrendAnalytics($seller, $startDate),
        ];

        return view('supplier.analytics.index', compact('analytics', 'seller', 'period'));
    }

    /**
     * Get revenue analytics
     */
    private function getRevenueAnalytics(MarketplaceSeller $seller, Carbon $startDate): array
    {
        $currentPeriodRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->sum('total_price');

        $previousPeriodRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereBetween('created_at', [
                $startDate->copy()->subDays($startDate->diffInDays(Carbon::now())),
                $startDate
            ])
            ->sum('total_price');

        $growthRate = $previousPeriodRevenue > 0
            ? (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : 0;

        // Daily revenue for chart
        $dailyRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'current_period' => $currentPeriodRevenue,
            'previous_period' => $previousPeriodRevenue,
            'growth_rate' => round($growthRate, 2),
            'daily_revenue' => $dailyRevenue,
            'average_order_value' => MarketplaceOrderItem::where('seller_id', $seller->id)
                ->where('created_at', '>=', $startDate)
                ->avg('total_price') ?? 0,
        ];
    }

    /**
     * Get order analytics
     */
    private function getOrderAnalytics(MarketplaceSeller $seller, Carbon $startDate): array
    {
        $totalOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->count();

        $ordersByStatus = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->select('fulfillment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('fulfillment_status')
            ->get();

        // Daily orders for chart
        $dailyOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_orders' => $totalOrders,
            'orders_by_status' => $ordersByStatus,
            'daily_orders' => $dailyOrders,
            'completion_rate' => $totalOrders > 0
                ? ($ordersByStatus->where('fulfillment_status', 'delivered')->first()->count ?? 0) / $totalOrders * 100
                : 0,
        ];
    }

    /**
     * Get product analytics
     */
    private function getProductAnalytics(MarketplaceSeller $seller, Carbon $startDate): array
    {
        $topProducts = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        $productViews = MarketplaceProduct::where('seller_id', $seller->id)
            ->select('name', 'view_count', 'purchase_count')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        $conversionRates = $productViews->map(function ($product) {
            return [
                'name' => $product->name,
                'views' => $product->view_count,
                'purchases' => $product->purchase_count,
                'conversion_rate' => $product->view_count > 0
                    ? ($product->purchase_count / $product->view_count) * 100
                    : 0
            ];
        });

        return [
            'top_products' => $topProducts,
            'product_views' => $productViews,
            'conversion_rates' => $conversionRates,
            'total_products' => MarketplaceProduct::where('seller_id', $seller->id)->count(),
            'active_products' => MarketplaceProduct::where('seller_id', $seller->id)->where('is_active', true)->count(),
        ];
    }

    /**
     * Get customer analytics
     */
    private function getCustomerAnalytics(MarketplaceSeller $seller, Carbon $startDate): array
    {
        $uniqueCustomers = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->distinct('marketplace_orders.user_id')
            ->count();

        $repeatCustomers = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->select('marketplace_orders.user_id', DB::raw('COUNT(*) as order_count'))
            ->groupBy('marketplace_orders.user_id')
            ->having('order_count', '>', 1)
            ->count();

        $topCustomers = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->join('users', 'marketplace_orders.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(marketplace_order_items.total_price) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return [
            'unique_customers' => $uniqueCustomers,
            'repeat_customers' => $repeatCustomers,
            'repeat_rate' => $uniqueCustomers > 0 ? ($repeatCustomers / $uniqueCustomers) * 100 : 0,
            'top_customers' => $topCustomers,
        ];
    }

    /**
     * Get trend analytics
     */
    private function getTrendAnalytics(MarketplaceSeller $seller, Carbon $startDate): array
    {
        // Weekly trends
        $weeklyTrends = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        // Category performance
        $categoryPerformance = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('created_at', '>=', $startDate)
            ->join('marketplace_products', 'marketplace_order_items.product_id', '=', 'marketplace_products.id')
            ->join('product_categories', 'marketplace_products.product_category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.name as category_name',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(marketplace_order_items.total_price) as revenue')
            )
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return [
            'weekly_trends' => $weeklyTrends,
            'category_performance' => $categoryPerformance,
        ];
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        $analytics = [
            'revenue' => $this->getRevenueAnalytics($seller, $startDate),
            'orders' => $this->getOrderAnalytics($seller, $startDate),
            'products' => $this->getProductAnalytics($seller, $startDate),
            'customers' => $this->getCustomerAnalytics($seller, $startDate),
        ];

        $filename = 'supplier_analytics_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($analytics) {
            $file = fopen('php://output', 'w');

            // Revenue summary
            fputcsv($file, ['BÁO CÁO PHÂN TÍCH DOANH THU']);
            fputcsv($file, ['Doanh thu kỳ hiện tại', number_format($analytics['revenue']['current_period'], 2)]);
            fputcsv($file, ['Doanh thu kỳ trước', number_format($analytics['revenue']['previous_period'], 2)]);
            fputcsv($file, ['Tỷ lệ tăng trưởng (%)', $analytics['revenue']['growth_rate']]);
            fputcsv($file, ['Giá trị đơn hàng trung bình', number_format($analytics['revenue']['average_order_value'], 2)]);
            fputcsv($file, []);

            // Top products
            fputcsv($file, ['SẢN PHẨM BÁN CHẠY']);
            fputcsv($file, ['Tên sản phẩm', 'Số lượng bán', 'Doanh thu']);
            foreach ($analytics['products']['top_products'] as $product) {
                fputcsv($file, [$product->product_name, $product->total_sold, number_format($product->total_revenue, 2)]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
