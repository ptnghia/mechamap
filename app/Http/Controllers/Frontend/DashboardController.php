<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\UserDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * 🎯 MechaMap Frontend Dashboard Controller
 * 
 * Controller xử lý dashboard cho các nhóm thành viên frontend:
 * - Community Members (senior_member L7, member L8, guest L9)
 * - Business Partners (verified_partner L10, manufacturer L11, supplier L12, brand L13)
 */
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị dashboard chính theo role của user
     */
    public function index(): View
    {
        $user = Auth::user();
        $dashboardData = UserDashboardService::getDashboardData($user);
        
        // Chọn view template theo role group
        $viewTemplate = $this->getViewTemplate($dashboardData['role_group']);
        
        return view($viewTemplate, $dashboardData);
    }

    /**
     * Lấy template view theo role group
     */
    private function getViewTemplate(string $roleGroup): string
    {
        return match($roleGroup) {
            'community_members' => 'frontend.dashboard.community-members',
            'business_partners' => 'frontend.dashboard.business-partners',
            default => 'frontend.dashboard.default',
        };
    }

    /**
     * API: Lấy dữ liệu dashboard cho AJAX refresh
     */
    public function dashboardData(Request $request)
    {
        $user = Auth::user();
        $dashboardData = UserDashboardService::getDashboardData($user);
        
        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    /**
     * Trang profile management
     */
    public function profile(): View
    {
        $user = Auth::user();
        
        return view('frontend.dashboard.profile', compact('user'));
    }

    /**
     * Trang activity feed
     */
    public function activity(): View
    {
        $user = Auth::user();
        
        // Lấy activities của user
        $activities = $user->activities()
            ->with(['thread', 'comment.thread'])
            ->latest()
            ->paginate(20);
        
        return view('frontend.dashboard.activity', compact('user', 'activities'));
    }

    /**
     * Trang notifications
     */
    public function notifications(): View
    {
        $user = Auth::user();
        
        // Lấy notifications của user
        $notifications = $user->userNotifications()
            ->latest()
            ->paginate(20);
        
        return view('frontend.dashboard.notifications', compact('user', 'notifications'));
    }

    /**
     * Trang my threads (chỉ cho community members)
     */
    public function myThreads(): View
    {
        $user = Auth::user();
        
        // Kiểm tra permission
        if (!in_array($user->role, ['member', 'senior_member'])) {
            abort(403, 'Unauthorized access');
        }
        
        $threads = $user->threads()
            ->with(['forum', 'user'])
            ->withCount(['allComments as comments_count', 'bookmarks', 'ratings'])
            ->latest()
            ->paginate(15);
        
        return view('frontend.dashboard.my-threads', compact('user', 'threads'));
    }

    /**
     * Trang bookmarks (chỉ cho community members)
     */
    public function bookmarks(): View
    {
        $user = Auth::user();
        
        // Kiểm tra permission
        if (!in_array($user->role, ['member', 'senior_member'])) {
            abort(403, 'Unauthorized access');
        }
        
        $bookmarks = $user->threadBookmarks()
            ->with(['thread.user', 'thread.forum'])
            ->latest()
            ->paginate(15);
        
        return view('frontend.dashboard.bookmarks', compact('user', 'bookmarks'));
    }

    /**
     * Trang following (cho guest và community members)
     */
    public function following(): View
    {
        $user = Auth::user();
        
        $followingUsers = $user->following()
            ->withCount(['threads', 'posts'])
            ->paginate(20);
        
        return view('frontend.dashboard.following', compact('user', 'followingUsers'));
    }

    /**
     * Business dashboard (chỉ cho business partners)
     */
    public function businessDashboard(): View
    {
        $user = Auth::user();
        
        // Kiểm tra permission
        if (!in_array($user->role, ['manufacturer', 'supplier', 'brand', 'verified_partner'])) {
            abort(403, 'Unauthorized access');
        }
        
        $businessStats = $this->getBusinessStats($user);
        
        return view('frontend.dashboard.business', compact('user', 'businessStats'));
    }

    /**
     * Brand insights (chỉ cho brand)
     */
    public function brandInsights(): View
    {
        $user = Auth::user();
        
        // Kiểm tra permission
        if ($user->role !== 'brand') {
            abort(403, 'Unauthorized access');
        }
        
        $insights = $this->getBrandInsights($user);
        
        return view('frontend.dashboard.brand-insights', compact('user', 'insights'));
    }

    /**
     * Brand promotions (chỉ cho brand)
     */
    public function brandPromotions(): View
    {
        $user = Auth::user();
        
        // Kiểm tra permission
        if ($user->role !== 'brand') {
            abort(403, 'Unauthorized access');
        }
        
        $promotions = $this->getBrandPromotions($user);
        
        return view('frontend.dashboard.brand-promotions', compact('user', 'promotions'));
    }

    /**
     * Lấy business statistics
     */
    private function getBusinessStats($user): array
    {
        return [
            'total_products' => $user->marketplaceProducts()->count(),
            'active_products' => $user->marketplaceProducts()->where('status', 'active')->count(),
            'total_orders' => $user->sellerOrders()->count(),
            'pending_orders' => $user->sellerOrders()->where('status', 'pending')->count(),
            'completed_orders' => $user->sellerOrders()->where('status', 'completed')->count(),
            'total_revenue' => $user->sellerOrders()->where('status', 'completed')->sum('total_amount'),
            'monthly_revenue' => $user->sellerOrders()
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
        ];
    }

    /**
     * Lấy brand insights
     */
    private function getBrandInsights($user): array
    {
        return [
            'market_trends' => [
                'trending_keywords' => ['CAD', 'Automation', 'IoT', 'Sustainability'],
                'growth_sectors' => ['Renewable Energy', 'Smart Manufacturing', 'Robotics'],
                'market_size' => 2500000,
                'growth_rate' => 12.5,
            ],
            'competitor_analysis' => [
                'top_competitors' => ['Brand A', 'Brand B', 'Brand C'],
                'market_share' => 15.2,
                'positioning' => 'Premium',
            ],
            'audience_insights' => [
                'primary_audience' => 'Mechanical Engineers',
                'age_group' => '25-45',
                'interests' => ['Innovation', 'Technology', 'Sustainability'],
            ],
        ];
    }

    /**
     * Lấy brand promotions
     */
    private function getBrandPromotions($user): array
    {
        return [
            'active_campaigns' => [
                [
                    'name' => 'Summer Innovation Campaign',
                    'status' => 'active',
                    'reach' => 15000,
                    'engagement' => 8.5,
                    'budget' => 50000,
                    'spent' => 32000,
                ],
            ],
            'opportunities' => [
                [
                    'type' => 'Forum Sponsorship',
                    'category' => 'CAD Design',
                    'estimated_reach' => 5000,
                    'cost' => 15000,
                ],
                [
                    'type' => 'Showcase Feature',
                    'category' => 'Innovation',
                    'estimated_reach' => 8000,
                    'cost' => 25000,
                ],
            ],
            'performance_metrics' => [
                'total_impressions' => 125000,
                'click_through_rate' => 3.2,
                'conversion_rate' => 1.8,
                'roi' => 245,
            ],
        ];
    }
}
