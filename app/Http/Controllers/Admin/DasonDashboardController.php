<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\TechnicalProduct;
use App\Models\Order;
use App\Models\Thread;
use App\Models\Post;
use App\Models\SellerEarning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DasonDashboardController extends BaseAdminController
{
    /**
     * Display the admin dashboard with Dason template
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();

        // Get chart data
        $revenueChartData = $this->getRevenueChartData();
        $userActivityData = $this->getUserActivityData();
        $forumActivityData = $this->getForumActivityData();

        // Get recent data
        $recentOrders = $this->getRecentOrders();
        $topSellers = $this->getTopSellers();

        return view('admin.dashboard-dason', compact(
            'stats',
            'revenueChartData',
            'userActivityData',
            'forumActivityData',
            'recentOrders',
            'topSellers'
        ));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            // User Statistics
            'total_users' => User::count(),
            'new_users_this_month' => User::where('created_at', '>=', $currentMonth)->count(),
            'active_users' => User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count(),
            'new_registrations' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'returning_users' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))
                ->where('created_at', '<', Carbon::now()->subDays(7))->count(),

            // Product Statistics
            'total_products' => TechnicalProduct::count(),
            'approved_products' => TechnicalProduct::where('status', 'approved')->count(),
            'pending_products' => TechnicalProduct::where('status', 'pending')->count(),

            // Order Statistics
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),

            // Revenue Statistics
            'total_revenue' => Order::where('payment_status', 'completed')->sum('total_amount'),
            'monthly_revenue' => Order::where('payment_status', 'completed')
                ->where('created_at', '>=', $currentMonth)->sum('total_amount'),
            'last_month_revenue' => Order::where('payment_status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $currentMonth])->sum('total_amount'),

            // Forum Statistics
            'total_threads' => Thread::count(),
            'total_posts' => Post::count(),
            'active_threads' => Thread::where('updated_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    /**
     * Calculate revenue growth percentage
     */
    private function calculateRevenueGrowth($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Get revenue chart data for the last 12 months
     */
    private function getRevenueChartData(): array
    {
        $months = [];
        $revenues = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $months[] = $date->format('M Y');
            $revenues[] = Order::where('payment_status', 'completed')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_amount');
        }

        return [
            'revenue_chart_labels' => $months,
            'revenue_chart_data' => $revenues
        ];
    }

    /**
     * Get user activity data for donut chart
     */
    private function getUserActivityData(): array
    {
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count();
        $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $returningUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(7))
            ->where('created_at', '<', Carbon::now()->subDays(7))->count();

        return [$activeUsers, $newUsers, $returningUsers];
    }

    /**
     * Get forum activity data for the last 7 days
     */
    private function getForumActivityData(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $data[] = Post::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        }

        return $data;
    }

    /**
     * Get recent orders for the dashboard table
     */
    private function getRecentOrders()
    {
        return Order::with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top sellers based on earnings
     */
    private function getTopSellers()
    {
        return User::whereIn('role', ['supplier', 'manufacturer'])
            ->withCount(['sellerEarnings as total_sales'])
            ->withSum(['sellerEarnings as total_earnings'], 'net_amount')
            ->orderBy('total_earnings', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get marketplace analytics data
     */
    public function marketplaceAnalytics()
    {
        $analytics = [
            // Sales by category
            'sales_by_category' => TechnicalProduct::select('category_id')
                ->with('category')
                ->withSum('orderItems', 'total_price')
                ->groupBy('category_id')
                ->orderBy('order_items_sum_total_price', 'desc')
                ->limit(10)
                ->get(),

            // Top selling products
            'top_products' => TechnicalProduct::withSum('orderItems', 'quantity')
                ->orderBy('order_items_sum_quantity', 'desc')
                ->limit(10)
                ->get(),

            // Seller performance
            'seller_performance' => User::whereIn('role', ['supplier', 'manufacturer'])
                ->withCount('technicalProducts')
                ->withSum(['sellerEarnings as total_earnings'], 'net_amount')
                ->orderBy('total_earnings', 'desc')
                ->limit(10)
                ->get(),

            // Monthly trends
            'monthly_trends' => $this->getMonthlyTrends(),
        ];

        return view('admin.analytics.marketplace', compact('analytics'));
    }

    /**
     * Get monthly trends for various metrics
     */
    private function getMonthlyTrends(): array
    {
        $trends = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $trends[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'products' => TechnicalProduct::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'orders' => Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
                'revenue' => Order::where('payment_status', 'completed')
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->sum('total_amount'),
            ];
        }

        return $trends;
    }

    /**
     * Get user analytics data
     */
    public function userAnalytics()
    {
        $analytics = [
            // User registration trends
            'registration_trends' => $this->getUserRegistrationTrends(),

            // User roles distribution
            'roles_distribution' => User::select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->get(),

            // User activity levels
            'activity_levels' => [
                'very_active' => User::where('last_login_at', '>=', Carbon::now()->subDays(1))->count(),
                'active' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count(),
                'inactive' => User::where('last_login_at', '<', Carbon::now()->subDays(30))->count(),
                'dormant' => User::whereNull('last_login_at')
                    ->orWhere('last_login_at', '<', Carbon::now()->subDays(90))->count(),
            ],

            // Geographic distribution (if available)
            'geographic_distribution' => User::select('country', DB::raw('count(*) as count'))
                ->whereNotNull('country')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.analytics.users', compact('analytics'));
    }

    /**
     * Get user registration trends for the last 12 months
     */
    private function getUserRegistrationTrends(): array
    {
        $trends = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $trends[] = [
                'month' => $date->format('M Y'),
                'registrations' => User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            ];
        }

        return $trends;
    }

    /**
     * Export dashboard data to Excel/CSV
     */
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'excel');
        $data = $this->getDashboardStats();

        // Implementation for data export
        // This would typically use Laravel Excel or similar package

        return response()->json([
            'success' => true,
            'message' => 'Data export functionality will be implemented',
            'data' => $data
        ]);
    }
}
