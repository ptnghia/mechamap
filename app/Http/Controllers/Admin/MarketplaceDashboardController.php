<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceDashboardController extends Controller
{
    /**
     * Display marketplace dashboard
     */
    public function index(): View
    {
        // Get product statistics
        $stats = $this->getProductStatistics();

        // Get recent products
        $recentProducts = MarketplaceProduct::with(['seller.user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.marketplace.dashboard', compact('stats', 'recentProducts'));
    }

    /**
     * Get product statistics
     */
    private function getProductStatistics(): array
    {
        $totalProducts = MarketplaceProduct::count();

        // Count by product type
        $digitalProducts = MarketplaceProduct::where('product_type', 'digital')->count();
        $newProducts = MarketplaceProduct::where('product_type', 'new_product')->count();
        $usedProducts = MarketplaceProduct::where('product_type', 'used_product')->count();

        // Count by seller type
        $supplierProducts = MarketplaceProduct::where('seller_type', 'supplier')->count();
        $manufacturerProducts = MarketplaceProduct::where('seller_type', 'manufacturer')->count();
        $brandProducts = MarketplaceProduct::where('seller_type', 'brand')->count();

        // Count by status
        $pendingProducts = MarketplaceProduct::where('status', 'pending')->count();
        $approvedProducts = MarketplaceProduct::where('status', 'approved')->count();
        $rejectedProducts = MarketplaceProduct::where('status', 'rejected')->count();

        // Order statistics
        $totalOrders = MarketplaceOrder::count();
        $paidOrders = MarketplaceOrder::where('payment_status', 'paid')->count();
        $pendingOrders = MarketplaceOrder::where('payment_status', 'pending')->count();

        // Revenue statistics
        $totalRevenue = MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount');
        $monthlyRevenue = MarketplaceOrder::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return [
            // Product counts
            'total_products' => $totalProducts,
            'digital_products' => $digitalProducts,
            'new_products' => $newProducts,
            'used_products' => $usedProducts,

            // Seller type counts
            'supplier_products' => $supplierProducts,
            'manufacturer_products' => $manufacturerProducts,
            'brand_products' => $brandProducts,

            // Status counts
            'pending_products' => $pendingProducts,
            'approved_products' => $approvedProducts,
            'rejected_products' => $rejectedProducts,

            // Order statistics
            'total_orders' => $totalOrders,
            'paid_orders' => $paidOrders,
            'pending_orders' => $pendingOrders,

            // Revenue
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,

            // Percentages
            'digital_percentage' => $totalProducts > 0 ? round(($digitalProducts / $totalProducts) * 100, 1) : 0,
            'new_percentage' => $totalProducts > 0 ? round(($newProducts / $totalProducts) * 100, 1) : 0,
            'used_percentage' => $totalProducts > 0 ? round(($usedProducts / $totalProducts) * 100, 1) : 0,
        ];
    }

    /**
     * Get permission matrix data for display
     */
    public function permissionMatrix(): array
    {
        return [
            'guest' => [
                'buy' => ['digital'],
                'sell' => ['digital'],
                'description' => '🏪 Đối tác cá nhân - Mua/bán digital (bán cần admin duyệt)'
            ],
            'member' => [
                'buy' => [],
                'sell' => [],
                'description' => '👥 Thành viên diễn đàn - Chỉ xem sản phẩm (không mua/bán)'
            ],
            'senior_member' => [
                'buy' => [],
                'sell' => [],
                'description' => '👥 Thành viên cao cấp - Chỉ xem sản phẩm (không mua/bán)'
            ],
            'supplier' => [
                'buy' => ['digital'],
                'sell' => ['digital', 'new_product'],
                'description' => 'Có thể bán thiết bị, linh kiện mới'
            ],
            'manufacturer' => [
                'buy' => ['digital', 'new_product'],
                'sell' => ['digital'],
                'description' => 'Mua nguyên liệu, bán file kỹ thuật'
            ],
            'brand' => [
                'buy' => [],
                'sell' => [],
                'description' => 'Chỉ xem và liên hệ'
            ],
        ];
    }

    /**
     * Get product approval statistics
     */
    public function approvalStats(): View
    {
        $pendingProducts = MarketplaceProduct::where('status', 'pending')
            ->with(['seller.user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $approvalStats = [
            'pending_count' => MarketplaceProduct::where('status', 'pending')->count(),
            'approved_today' => MarketplaceProduct::where('status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'rejected_today' => MarketplaceProduct::where('status', 'rejected')
                ->whereDate('updated_at', today())->count(),
            'avg_approval_time' => $this->getAverageApprovalTime(),
        ];

        return view('admin.marketplace.approval-stats', compact('pendingProducts', 'approvalStats'));
    }

    /**
     * Calculate average approval time
     */
    private function getAverageApprovalTime(): float
    {
        $approvedProducts = MarketplaceProduct::where('status', 'approved')
            ->whereNotNull('approved_at')
            ->whereDate('approved_at', '>=', now()->subDays(30))
            ->get();

        if ($approvedProducts->isEmpty()) {
            return 0;
        }

        $totalHours = 0;
        $count = 0;

        foreach ($approvedProducts as $product) {
            if ($product->approved_at && $product->created_at) {
                $hours = $product->created_at->diffInHours($product->approved_at);
                $totalHours += $hours;
                $count++;
            }
        }

        return $count > 0 ? round($totalHours / $count, 1) : 0;
    }

    /**
     * Export product data
     */
    public function exportProducts(Request $request)
    {
        $request->validate([
            'type' => 'in:all,digital,new_product,used_product',
            'status' => 'in:all,pending,approved,rejected',
            'format' => 'in:csv,xlsx'
        ]);

        $query = MarketplaceProduct::with(['seller.user', 'category']);

        if ($request->type && $request->type !== 'all') {
            $query->where('product_type', $request->type);
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $products = $query->get();

        // Export logic would go here
        // For now, return JSON for API testing
        return response()->json([
            'success' => true,
            'message' => 'Export functionality would be implemented here',
            'data' => [
                'total_products' => $products->count(),
                'filters' => [
                    'type' => $request->type ?? 'all',
                    'status' => $request->status ?? 'all',
                    'format' => $request->format ?? 'csv'
                ]
            ]
        ]);
    }
}
