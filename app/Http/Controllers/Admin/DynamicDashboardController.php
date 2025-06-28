<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Thread;
use App\Models\Product;
use App\Models\Order;
use App\Models\Report;
use App\Services\AdminMenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Dynamic Dashboard Controller - Phase 2
 * Dashboard động theo role và permissions của user
 */
class DynamicDashboardController extends Controller
{
    public function __construct()
    {
        // Middleware auth và role đã được áp dụng ở route level
        // Không cần áp dụng lại ở controller level
    }

    /**
     * Hiển thị dashboard chính
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Lấy data theo role group
        $dashboardData = $this->getDashboardDataByRole($user);

        // Lấy menu và breadcrumb
        $adminMenu = AdminMenuService::getAdminMenu($user);
        $breadcrumb = AdminMenuService::getBreadcrumb('admin.dashboard', $user);

        // Enhanced stats for Phase 3
        $stats = $this->getEnhancedStats();
        $latestUsers = User::latest()->take(5)->get();
        $latestThreads = Thread::with(['user', 'forum'])->latest()->take(5)->get();

        // Debug: Check if stats are being passed
        // dd($stats);

        // Debug: Force specific view
        return view('admin.dashboard', compact(
            'dashboardData',
            'adminMenu',
            'breadcrumb',
            'user',
            'stats',
            'latestUsers',
            'latestThreads'
        ));
    }

    /**
     * Lấy data dashboard theo role
     */
    private function getDashboardDataByRole(User $user): array
    {
        $data = [
            'role_info' => [
                'role' => $user->getRoleDisplayName(),
                'role_group' => $user->getRoleGroupDisplayName(),
                'permissions_count' => $user->getAllPermissions()->count(),
                'level' => \App\Services\PermissionService::getRoleLevel($user),
            ]
        ];

        // System Management Dashboard
        if ($user->isSystemManagement()) {
            $data['system'] = $this->getSystemManagementData($user);
        }

        // Community Management Dashboard
        if ($user->isCommunityManagement()) {
            $data['community'] = $this->getCommunityManagementData($user);
        }

        // Marketplace Data (cho marketplace moderator hoặc system admin)
        if ($user->hasPermissionTo('manage-marketplace') || $user->hasPermissionTo('view-marketplace-analytics')) {
            $data['marketplace'] = $this->getMarketplaceData($user);
        }

        // Content Data (cho content moderator hoặc admin)
        if ($user->hasPermissionTo('moderate-content') || $user->hasPermissionTo('manage-content')) {
            $data['content'] = $this->getContentData($user);
        }

        // Analytics Data
        if ($user->hasPermissionTo('view-analytics')) {
            $data['analytics'] = $this->getAnalyticsData($user);
        }

        return $data;
    }

    /**
     * Data cho System Management
     */
    private function getSystemManagementData(User $user): array
    {
        $data = [];

        if ($user->hasPermissionTo('view-users')) {
            $data['users'] = [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
                'new_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'by_role_group' => User::select('role_group', DB::raw('count(*) as count'))
                    ->groupBy('role_group')
                    ->pluck('count', 'role_group')
                    ->toArray(),
            ];
        }

        if ($user->hasPermissionTo('view-system-logs')) {
            $data['system'] = [
                'server_status' => 'online',
                'database_status' => 'healthy',
                'storage_usage' => $this->getStorageUsage(),
                'recent_errors' => $this->getRecentErrors(),
            ];
        }

        return $data;
    }

    /**
     * Data cho Community Management
     */
    private function getCommunityManagementData(User $user): array
    {
        $data = [];

        if ($user->hasPermissionTo('moderate-content')) {
            $data['moderation'] = [
                'pending_reports' => Report::where('status', 'pending')->count(),
                'urgent_reports' => Report::where('priority', 'high')->where('status', 'pending')->count(),
                'resolved_today' => Report::where('status', 'resolved')->whereDate('updated_at', today())->count(),
                'recent_reports' => Report::with(['user', 'reportable'])
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
            ];
        }

        if ($user->hasPermissionTo('manage-community')) {
            $data['community'] = [
                'active_discussions' => Thread::where('is_active', true)->count(),
                'new_threads_today' => Thread::whereDate('created_at', today())->count(),
                'popular_threads' => Thread::withCount('comments')
                    ->orderBy('comments_count', 'desc')
                    ->take(5)
                    ->get(),
            ];
        }

        return $data;
    }

    /**
     * Data cho Marketplace
     */
    private function getMarketplaceData(User $user): array
    {
        $data = [];

        if ($user->hasPermissionTo('manage-marketplace')) {
            $data['products'] = [
                'total' => Product::count(),
                'pending' => Product::where('status', 'pending')->count(),
                'active' => Product::where('status', 'active')->count(),
                'revenue_today' => Order::whereDate('created_at', today())->sum('total_amount'),
                'orders_today' => Order::whereDate('created_at', today())->count(),
            ];
        }

        if ($user->hasPermissionTo('manage-seller-accounts')) {
            $data['sellers'] = [
                'total' => User::where('role_group', 'business_partners')->count(),
                'verified' => User::where('role_group', 'business_partners')
                    ->where('is_verified_business', true)->count(),
                'pending_verification' => User::where('role_group', 'business_partners')
                    ->where('is_verified_business', false)->count(),
            ];
        }

        return $data;
    }

    /**
     * Data cho Content Management
     */
    private function getContentData(User $user): array
    {
        $data = [];

        if ($user->hasPermissionTo('moderate-content')) {
            $data['threads'] = [
                'total' => Thread::count(),
                'published' => Thread::where('status', 'published')->count(),
                'pending' => Thread::where('status', 'pending')->count(),
                'flagged' => Thread::whereNotNull('flagged_at')->count(),
            ];
        }

        return $data;
    }

    /**
     * Data cho Analytics
     */
    private function getAnalyticsData(User $user): array
    {
        $data = [];

        if ($user->hasPermissionTo('view-analytics')) {
            // User growth chart data
            $userGrowth = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

            $data['charts'] = [
                'user_growth' => [
                    'labels' => $userGrowth->pluck('date')->map(function($date) {
                        return Carbon::parse($date)->format('M d');
                    })->toArray(),
                    'data' => $userGrowth->pluck('count')->toArray(),
                ],
            ];

            // Top statistics
            $data['stats'] = [
                'total_revenue' => Order::sum('total_amount'),
                'total_orders' => Order::count(),
                'active_users' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
                'conversion_rate' => $this->calculateConversionRate(),
            ];
        }

        return $data;
    }

    /**
     * Helper methods
     */
    private function getStorageUsage(): array
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    private function getRecentErrors(): array
    {
        // Placeholder - implement based on your logging system
        return [
            ['message' => 'No recent errors', 'time' => now(), 'level' => 'info']
        ];
    }

    private function calculateConversionRate(): float
    {
        $visitors = User::where('role', 'guest')->count();
        $members = User::where('role', 'member')->count();

        return $visitors > 0 ? round(($members / $visitors) * 100, 2) : 0;
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * API endpoint cho real-time updates
     */
    public function getRealtimeData(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'notifications' => [
                'reports' => Report::where('status', 'pending')->count(),
                'messages' => $user->unreadMessages()->count(),
                'products' => Product::where('status', 'pending')->count(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get enhanced stats for Phase 3
     */
    private function getEnhancedStats(): array
    {
        return [
            // Community Stats
            'users' => User::count(),
            'threads' => Thread::count(),
            'comments' => \Schema::hasTable('comments') ? \DB::table('comments')->count() : 0,
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_threads_today' => Thread::whereDate('created_at', today())->count(),
            'new_comments_today' => \Schema::hasTable('comments') ?
                \DB::table('comments')->whereDate('created_at', today())->count() : 0,

            // Weekly Activity
            'weekly_activity' => $this->getWeeklyActivity(),

            // Marketplace Stats (mock data for now)
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'pending_orders' => $this->getPendingOrders(),
            'pending_products' => $this->getPendingProducts(),
            'unpaid_commission' => $this->getUnpaidCommission(),
        ];
    }

    /**
     * Get weekly activity count
     */
    private function getWeeklyActivity(): int
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $threadCount = Thread::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $commentCount = \Schema::hasTable('comments') ?
            \DB::table('comments')->whereBetween('created_at', [$weekStart, $weekEnd])->count() : 0;
        $userCount = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        return $threadCount + $commentCount + $userCount;
    }

    /**
     * Get monthly revenue (mock data for now)
     */
    private function getMonthlyRevenue(): int
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $activeUsers = User::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        // Mock calculation: 2M VND per active user
        return $activeUsers * 2000000;
    }

    /**
     * Get pending orders count (mock data for now)
     */
    private function getPendingOrders(): int
    {
        $recentThreads = Thread::where('created_at', '>=', now()->subDays(7))->count();

        // Mock: assume 20% of recent threads result in orders
        return max(1, intval($recentThreads * 0.2));
    }

    /**
     * Get pending products count (mock data for now)
     */
    private function getPendingProducts(): int
    {
        $businessUsers = User::whereIn('role', ['supplier', 'manufacturer', 'brand'])->count();

        // Mock: assume each business user has 0.5 pending products on average
        return max(1, intval($businessUsers * 0.5));
    }

    /**
     * Get unpaid commission amount (mock data for now)
     */
    private function getUnpaidCommission(): int
    {
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Mock: assume 12% commission rate
        return intval($monthlyRevenue * 0.12);
    }
}
