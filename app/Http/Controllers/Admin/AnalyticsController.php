<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceSeller;
use App\Models\TechnicalDrawing;
use App\Models\Material;
use App\Models\Thread;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Analytics Dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        // Key Performance Indicators
        $kpis = [
            'total_users' => User::count(),
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'active_users' => User::where('last_login_at', '>=', $startDate)->count(),
            'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount'),
            'period_revenue' => MarketplaceOrder::where('payment_status', 'paid')
                ->where('created_at', '>=', $startDate)->sum('total_amount'),
            'total_orders' => MarketplaceOrder::count(),
            'period_orders' => MarketplaceOrder::where('created_at', '>=', $startDate)->count(),
            'total_products' => MarketplaceProduct::count(),
            'active_products' => MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count(),
        ];

        // Growth rates
        $previousPeriod = now()->subDays($period * 2);
        $previousUsers = User::whereBetween('created_at', [$previousPeriod, $startDate])->count();
        $previousRevenue = MarketplaceOrder::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousPeriod, $startDate])->sum('total_amount');

        $growthRates = [
            'users' => $previousUsers > 0 ? round((($kpis['new_users'] - $previousUsers) / $previousUsers) * 100, 2) : 0,
            'revenue' => $previousRevenue > 0 ? round((($kpis['period_revenue'] - $previousRevenue) / $previousRevenue) * 100, 2) : 0,
        ];

        return view('admin.analytics.index', compact('kpis', 'growthRates', 'period'));
    }

    /**
     * Revenue Analytics
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        // Daily revenue data
        $dailyRevenue = MarketplaceOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by seller type
        $revenueBySellerType = MarketplaceOrder::join('marketplace_order_items', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->join('marketplace_sellers', 'marketplace_order_items.seller_id', '=', 'marketplace_sellers.id')
            ->select('marketplace_sellers.seller_type', DB::raw('SUM(marketplace_order_items.total_amount) as revenue'))
            ->where('marketplace_orders.payment_status', 'paid')
            ->where('marketplace_orders.created_at', '>=', $startDate)
            ->groupBy('marketplace_sellers.seller_type')
            ->get();

        // Top selling products
        $topProducts = MarketplaceOrder::join('marketplace_order_items', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->select(
                'marketplace_order_items.product_name',
                DB::raw('SUM(marketplace_order_items.quantity) as total_sold'),
                DB::raw('SUM(marketplace_order_items.total_amount) as revenue')
            )
            ->where('marketplace_orders.payment_status', 'paid')
            ->where('marketplace_orders.created_at', '>=', $startDate)
            ->groupBy('marketplace_order_items.product_id', 'marketplace_order_items.product_name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.revenue', compact('dailyRevenue', 'revenueBySellerType', 'topProducts', 'period'));
    }

    /**
     * User Analytics
     */
    public function users(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        // User registration trends
        $userRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as registrations')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User activity
        $userActivity = User::select(
                DB::raw('DATE(last_login_at) as date'),
                DB::raw('COUNT(*) as active_users')
            )
            ->where('last_login_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // User roles distribution
        $userRoles = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->get();

        // Geographic distribution (if location data available)
        $usersByLocation = User::select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.users', compact('userRegistrations', 'userActivity', 'userRoles', 'usersByLocation', 'period'));
    }

    /**
     * Content Analytics
     */
    public function content(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        // Content creation trends
        $contentStats = [
            'threads' => Thread::where('created_at', '>=', $startDate)->count(),
            'comments' => Comment::where('created_at', '>=', $startDate)->count(),
            'technical_drawings' => TechnicalDrawing::where('created_at', '>=', $startDate)->count(),
            'materials' => Material::where('created_at', '>=', $startDate)->count(),
            'products' => MarketplaceProduct::where('created_at', '>=', $startDate)->count(),
        ];

        // Most active forums
        $activeForums = Thread::join('forums', 'threads.forum_id', '=', 'forums.id')
            ->select('forums.name', DB::raw('COUNT(*) as thread_count'))
            ->where('threads.created_at', '>=', $startDate)
            ->groupBy('forums.id', 'forums.name')
            ->orderBy('thread_count', 'desc')
            ->limit(10)
            ->get();

        // Content engagement
        $contentEngagement = [
            'avg_comments_per_thread' => Thread::where('created_at', '>=', $startDate)->avg('comment_count'),
            'avg_views_per_thread' => Thread::where('created_at', '>=', $startDate)->avg('view_count'),
            'avg_downloads_per_drawing' => TechnicalDrawing::where('created_at', '>=', $startDate)->avg('download_count'),
        ];

        return view('admin.analytics.content', compact('contentStats', 'activeForums', 'contentEngagement', 'period'));
    }

    /**
     * Marketplace Analytics
     */
    public function marketplace(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        // Marketplace metrics
        $marketplaceStats = [
            'total_sellers' => MarketplaceSeller::count(),
            'active_sellers' => MarketplaceSeller::where('status', 'active')->count(),
            'verified_sellers' => MarketplaceSeller::where('verification_status', 'verified')->count(),
            'conversion_rate' => $this->calculateConversionRate($startDate),
            'avg_order_value' => MarketplaceOrder::where('created_at', '>=', $startDate)->avg('total_amount'),
        ];

        // Seller performance
        $topSellers = MarketplaceSeller::select(
                'marketplace_sellers.business_name',
                DB::raw('SUM(marketplace_order_items.seller_earnings) as earnings'),
                DB::raw('COUNT(DISTINCT marketplace_orders.id) as orders')
            )
            ->join('marketplace_order_items', 'marketplace_sellers.id', '=', 'marketplace_order_items.seller_id')
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->where('marketplace_orders.payment_status', 'paid')
            ->where('marketplace_orders.created_at', '>=', $startDate)
            ->groupBy('marketplace_sellers.id', 'marketplace_sellers.business_name')
            ->orderBy('earnings', 'desc')
            ->limit(10)
            ->get();

        // Product categories performance
        $categoryPerformance = MarketplaceProduct::join('product_categories', 'marketplace_products.product_category_id', '=', 'product_categories.id')
            ->join('marketplace_order_items', 'marketplace_products.id', '=', 'marketplace_order_items.product_id')
            ->join('marketplace_orders', 'marketplace_order_items.order_id', '=', 'marketplace_orders.id')
            ->select(
                'product_categories.name',
                DB::raw('SUM(marketplace_order_items.total_amount) as revenue'),
                DB::raw('SUM(marketplace_order_items.quantity) as units_sold')
            )
            ->where('marketplace_orders.payment_status', 'paid')
            ->where('marketplace_orders.created_at', '>=', $startDate)
            ->groupBy('product_categories.id', 'product_categories.name')
            ->orderBy('revenue', 'desc')
            ->get();

        return view('admin.analytics.marketplace', compact('marketplaceStats', 'topSellers', 'categoryPerformance', 'period'));
    }

    /**
     * Technical Analytics
     */
    public function technical(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);

        // Technical content stats
        $technicalStats = [
            'total_drawings' => TechnicalDrawing::count(),
            'public_drawings' => TechnicalDrawing::where('visibility', 'public')->count(),
            'total_downloads' => TechnicalDrawing::sum('download_count'),
            'period_downloads' => TechnicalDrawing::where('created_at', '>=', $startDate)->sum('download_count'),
            'total_materials' => Material::count(),
            'approved_materials' => Material::where('status', 'approved')->count(),
        ];

        // Most downloaded drawings
        $popularDrawings = TechnicalDrawing::select('title', 'download_count', 'view_count', 'drawing_type')
            ->orderBy('download_count', 'desc')
            ->limit(10)
            ->get();

        // Material usage statistics
        $materialUsage = Material::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        // Drawing types distribution
        $drawingTypes = TechnicalDrawing::select('drawing_type', DB::raw('COUNT(*) as count'))
            ->groupBy('drawing_type')
            ->get();

        return view('admin.analytics.technical', compact('technicalStats', 'popularDrawings', 'materialUsage', 'drawingTypes', 'period'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', '30');

        // TODO: Implement Excel export functionality
        return response()->json([
            'success' => true,
            'message' => 'Chức năng xuất dữ liệu sẽ được triển khai',
            'download_url' => '#'
        ]);
    }

    /**
     * Get real-time analytics data for AJAX
     */
    public function realtime(Request $request)
    {
        $data = [
            'online_users' => User::where('last_login_at', '>=', now()->subMinutes(5))->count(),
            'today_orders' => MarketplaceOrder::whereDate('created_at', today())->count(),
            'today_revenue' => MarketplaceOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')->sum('total_amount'),
            'pending_approvals' => MarketplaceProduct::where('status', 'pending')->count() +
                                 TechnicalDrawing::where('status', 'pending')->count(),
        ];

        return response()->json($data);
    }

    /**
     * Helper method to calculate conversion rate
     */
    private function calculateConversionRate($startDate)
    {
        $totalVisitors = User::where('created_at', '>=', $startDate)->count();
        $totalOrders = MarketplaceOrder::where('created_at', '>=', $startDate)->count();

        return $totalVisitors > 0 ? round(($totalOrders / $totalVisitors) * 100, 2) : 0;
    }
}
