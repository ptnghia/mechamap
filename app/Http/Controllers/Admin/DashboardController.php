<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Forum;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard admin
     */
    public function index(): View
    {
        // Lấy thống kê cơ bản
        $stats = [
            // Community Stats
            'users' => User::count(),
            'threads' => Thread::count(),
            'posts' => Post::count(),
            'comments' => Schema::hasTable('comments') ? Comment::count() : 0,
            'forums' => Schema::hasTable('forums') ? Forum::count() : 0,
            'categories' => Schema::hasTable('categories') ? Category::count() : 0,
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_threads_today' => Thread::whereDate('created_at', today())->count(),
            'new_posts_today' => Post::whereDate('created_at', today())->count(),
            'new_comments_today' => Schema::hasTable('comments') ? Comment::whereDate('created_at', today())->count() : 0,
            'online_users' => Schema::hasColumn('users', 'last_seen_at') ? User::where('last_seen_at', '>=', now()->subMinutes(5))->count() : 0,

            // Weekly Activity
            'weekly_activity' => $this->getWeeklyActivity(),

            // Marketplace Stats
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'pending_orders' => $this->getPendingOrders(),
            'pending_products' => $this->getPendingProducts(),
            'unpaid_commission' => $this->getUnpaidCommission(),
        ];

        // Lấy danh sách người dùng mới nhất
        $latestUsers = User::latest()->take(5)->get();

        // Lấy danh sách bài viết mới nhất
        $latestThreads = Thread::with(['user', 'forum'])->latest()->take(5)->get();

        // Thống kê người dùng theo vai trò
        $roleStats = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get()
            ->pluck('total', 'role')
            ->toArray();

        // Thống kê bài đăng theo trạng thái
        $statusStats = Thread::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Thống kê người dùng mới theo tháng (12 tháng gần nhất)
        $userMonthlyStats = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Thống kê nội dung theo tháng (12 tháng gần nhất)
        $threadMonthlyStats = Thread::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $commentMonthlyStats = [];
        if (Schema::hasTable('comments')) {
            $commentMonthlyStats = Comment::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        // Thống kê tương tác
        $interactionStats = [
            'comments' => Schema::hasTable('comments') ? Comment::count() : 0,
            'likes' => $this->countTableIfExists('thread_likes') + $this->countTableIfExists('comment_likes'),
            'saves' => $this->countTableIfExists('thread_saves'),
            'reports' => $this->countTableIfExists('reports'),
        ];

        // Breadcrumbs
        $breadcrumbs = [];

        return view('admin.dashboard', compact(
            'stats',
            'latestUsers',
            'latestThreads',
            'roleStats',
            'statusStats',
            'userMonthlyStats',
            'threadMonthlyStats',
            'commentMonthlyStats',
            'interactionStats',
            'breadcrumbs'
        ));
    }
    /**
     * Đếm số bản ghi trong bảng nếu bảng tồn tại
     *
     * @param string $table Tên bảng
     * @return int Số bản ghi hoặc 0 nếu bảng không tồn tại
     */
    private function countTableIfExists(string $table): int
    {
        if (Schema::hasTable($table)) {
            return DB::table($table)->count();
        }

        return 0;
    }

    /**
     * Get weekly activity count
     */
    private function getWeeklyActivity(): int
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $threadCount = Thread::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $commentCount = Schema::hasTable('comments') ?
            Comment::whereBetween('created_at', [$weekStart, $weekEnd])->count() : 0;
        $userCount = User::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        return $threadCount + $commentCount + $userCount;
    }

    /**
     * Get monthly revenue (mock data for now)
     */
    private function getMonthlyRevenue(): int
    {
        // TODO: Implement real revenue calculation when Order/Payment system is ready
        // For now, return mock data based on user activity
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
        // TODO: Implement real order system
        // For now, return mock data based on recent activity
        $recentThreads = Thread::where('created_at', '>=', now()->subDays(7))->count();

        // Mock: assume 20% of recent threads result in orders
        return max(1, intval($recentThreads * 0.2));
    }

    /**
     * Get pending products count (mock data for now)
     */
    private function getPendingProducts(): int
    {
        // TODO: Implement real product approval system
        // For now, return mock data
        $businessUsers = User::whereIn('role', ['supplier', 'manufacturer', 'brand'])->count();

        // Mock: assume each business user has 0.5 pending products on average
        return max(1, intval($businessUsers * 0.5));
    }

    /**
     * Get unpaid commission amount (mock data for now)
     */
    private function getUnpaidCommission(): int
    {
        // TODO: Implement real commission system
        // For now, return mock data based on monthly revenue
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Mock: assume 12% commission rate
        return intval($monthlyRevenue * 0.12);
    }
}
