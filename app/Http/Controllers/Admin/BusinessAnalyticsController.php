<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Commission;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Business Analytics Controller - Phase 3
 * Dashboard analytics cho business partners và marketplace
 */
class BusinessAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view-analytics|view-marketplace-analytics']);
    }

    /**
     * Dashboard analytics chính
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $timeRange = $request->get('range', '30'); // days
        
        // Get data based on user role
        $analytics = $this->getAnalyticsData($user, $timeRange);
        
        return view('admin.analytics.business-dashboard', compact('analytics', 'timeRange'));
    }

    /**
     * Marketplace overview analytics
     */
    public function marketplace(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('view-marketplace-analytics')) {
            abort(403, 'Không có quyền xem analytics marketplace');
        }

        $timeRange = $request->get('range', '30');
        $startDate = now()->subDays($timeRange);
        
        $data = [
            'overview' => $this->getMarketplaceOverview($startDate),
            'revenue' => $this->getRevenueAnalytics($startDate),
            'sellers' => $this->getSellerAnalytics($startDate),
            'products' => $this->getProductAnalytics($startDate),
            'commissions' => $this->getCommissionAnalytics($startDate),
            'trends' => $this->getTrendAnalytics($startDate),
        ];

        return view('admin.analytics.marketplace', compact('data', 'timeRange'));
    }

    /**
     * Seller performance analytics
     */
    public function sellers(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('manage-seller-accounts') && 
            !$user->hasPermissionTo('view-marketplace-analytics')) {
            abort(403, 'Không có quyền xem analytics seller');
        }

        $timeRange = $request->get('range', '30');
        $sellerType = $request->get('type', 'all');
        
        $query = User::where('role_group', 'business_partners')
            ->with(['products', 'orders']);
            
        if ($sellerType !== 'all') {
            $query->where('role', $sellerType);
        }

        $sellers = $query->get();
        
        $analytics = $sellers->map(function($seller) use ($timeRange) {
            return $this->getSellerPerformance($seller, $timeRange);
        })->sortByDesc('total_revenue');

        $summary = [
            'total_sellers' => $sellers->count(),
            'active_sellers' => $sellers->where('is_active', true)->count(),
            'verified_sellers' => $sellers->where('is_verified_business', true)->count(),
            'total_revenue' => $analytics->sum('total_revenue'),
            'total_commission' => $analytics->sum('commission_earned'),
        ];

        return view('admin.analytics.sellers', compact('analytics', 'summary', 'timeRange', 'sellerType'));
    }

    /**
     * Revenue analytics với breakdown
     */
    public function revenue(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('view-marketplace-analytics')) {
            abort(403, 'Không có quyền xem analytics doanh thu');
        }

        $timeRange = $request->get('range', '30');
        $groupBy = $request->get('group', 'day'); // day, week, month
        
        $data = [
            'timeline' => $this->getRevenueTimeline($timeRange, $groupBy),
            'by_category' => $this->getRevenueByCategory($timeRange),
            'by_seller_type' => $this->getRevenueBySeller($timeRange),
            'commission_breakdown' => $this->getCommissionBreakdown($timeRange),
            'top_products' => $this->getTopProducts($timeRange),
            'growth_metrics' => $this->getGrowthMetrics($timeRange),
        ];

        return view('admin.analytics.revenue', compact('data', 'timeRange', 'groupBy'));
    }

    /**
     * Commission analytics và reports
     */
    public function commissions(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('manage-commissions')) {
            abort(403, 'Không có quyền xem commission analytics');
        }

        $timeRange = $request->get('range', '30');
        $status = $request->get('status', 'all');
        
        $query = Commission::with(['seller', 'order'])
            ->whereBetween('created_at', [now()->subDays($timeRange), now()]);
            
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $commissions = $query->get();
        
        $analytics = [
            'total_commission' => $commissions->sum('amount'),
            'paid_commission' => $commissions->where('status', 'paid')->sum('amount'),
            'pending_commission' => $commissions->where('status', 'pending')->sum('amount'),
            'by_seller_type' => $this->getCommissionBySeller($commissions),
            'monthly_trend' => $this->getCommissionTrend($timeRange),
            'top_earners' => $this->getTopCommissionEarners($commissions),
        ];

        return view('admin.analytics.commissions', compact('analytics', 'commissions', 'timeRange', 'status'));
    }

    /**
     * Get analytics data based on user role
     */
    private function getAnalyticsData(User $user, int $timeRange): array
    {
        $startDate = now()->subDays($timeRange);
        
        $data = [
            'user_role' => $user->role,
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ];

        // System Management Analytics
        if ($user->isSystemManagement()) {
            $data['system'] = [
                'total_users' => User::count(),
                'new_users' => User::where('created_at', '>=', $startDate)->count(),
                'active_users' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
                'user_growth' => $this->getUserGrowthData($timeRange),
            ];
        }

        // Marketplace Analytics
        if ($user->hasPermissionTo('view-marketplace-analytics')) {
            $data['marketplace'] = $this->getMarketplaceOverview($startDate);
        }

        // Business Partner Analytics (for own data)
        if ($user->role_group === 'business_partners') {
            $data['business'] = $this->getSellerPerformance($user, $timeRange);
        }

        return $data;
    }

    /**
     * Get marketplace overview data
     */
    private function getMarketplaceOverview(Carbon $startDate): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'active')->count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'completed_orders' => Order::where('status', 'completed')
                ->where('created_at', '>=', $startDate)->count(),
            'total_revenue' => Order::where('status', 'completed')
                ->where('created_at', '>=', $startDate)->sum('total_amount'),
            'average_order_value' => Order::where('status', 'completed')
                ->where('created_at', '>=', $startDate)->avg('total_amount'),
        ];
    }

    /**
     * Get seller performance data
     */
    private function getSellerPerformance(User $seller, int $timeRange): array
    {
        $startDate = now()->subDays($timeRange);
        
        $orders = Order::whereHas('items.product', function($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->where('created_at', '>=', $startDate)->get();

        $totalRevenue = $orders->where('status', 'completed')->sum('total_amount');
        $features = PermissionService::getMarketplaceFeatures($seller);
        $commissionRate = $features['commission_rate'] ?? 5.0;
        $commissionAmount = $totalRevenue * ($commissionRate / 100);

        return [
            'seller_id' => $seller->id,
            'seller_name' => $seller->name,
            'seller_role' => $seller->role,
            'total_products' => $seller->products()->count(),
            'active_products' => $seller->products()->where('status', 'active')->count(),
            'total_orders' => $orders->count(),
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'total_revenue' => $totalRevenue,
            'commission_rate' => $commissionRate,
            'commission_earned' => $commissionAmount,
            'seller_earnings' => $totalRevenue - $commissionAmount,
            'average_order_value' => $orders->where('status', 'completed')->avg('total_amount') ?? 0,
            'conversion_rate' => $this->calculateConversionRate($seller, $timeRange),
        ];
    }

    /**
     * Get revenue analytics
     */
    private function getRevenueAnalytics(Carbon $startDate): array
    {
        $orders = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total_revenue' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->avg('total_amount'),
            'daily_revenue' => $this->getDailyRevenue($startDate),
        ];
    }

    /**
     * Get commission analytics
     */
    private function getCommissionAnalytics(Carbon $startDate): array
    {
        // Calculate commissions from completed orders
        $totalCommission = 0;
        $commissionByType = [];

        $sellers = User::where('role_group', 'business_partners')->get();
        
        foreach ($sellers as $seller) {
            $revenue = Order::whereHas('items.product', function($q) use ($seller) {
                $q->where('seller_id', $seller->id);
            })->where('status', 'completed')
              ->where('created_at', '>=', $startDate)
              ->sum('total_amount');

            $features = PermissionService::getMarketplaceFeatures($seller);
            $rate = $features['commission_rate'] ?? 5.0;
            $commission = $revenue * ($rate / 100);
            
            $totalCommission += $commission;
            $commissionByType[$seller->role] = ($commissionByType[$seller->role] ?? 0) + $commission;
        }

        return [
            'total_commission' => $totalCommission,
            'by_seller_type' => $commissionByType,
        ];
    }

    /**
     * Helper methods
     */
    private function getUserGrowthData(int $days): array
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->whereBetween('created_at', [now()->subDays($days), now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->pluck('count', 'date')
        ->toArray();
    }

    private function getDailyRevenue(Carbon $startDate): array
    {
        return Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->where('status', 'completed')
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->pluck('revenue', 'date')
        ->toArray();
    }

    private function calculateConversionRate(User $seller, int $timeRange): float
    {
        // Placeholder - implement based on your tracking system
        return rand(1, 10) / 100; // 1-10%
    }

    /**
     * API endpoints for real-time data
     */
    public function getRealtimeMetrics(Request $request)
    {
        $user = auth()->user();
        
        $metrics = [
            'timestamp' => now()->toISOString(),
        ];

        if ($user->hasPermissionTo('view-marketplace-analytics')) {
            $metrics['marketplace'] = [
                'pending_products' => Product::where('status', 'pending')->count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
                'today_revenue' => Order::where('status', 'completed')
                    ->whereDate('created_at', today())->sum('total_amount'),
            ];
        }

        if ($user->role_group === 'business_partners') {
            $metrics['business'] = [
                'my_products' => $user->products()->count(),
                'my_orders' => Order::whereHas('items.product', function($q) use ($user) {
                    $q->where('seller_id', $user->id);
                })->whereDate('created_at', today())->count(),
            ];
        }

        return response()->json($metrics);
    }
}
