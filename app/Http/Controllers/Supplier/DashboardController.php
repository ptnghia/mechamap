<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    /**
     * Display supplier dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return view('supplier.setup-required');
        }

        // Get supplier statistics
        $stats = $this->getSupplierStats($seller);

        // Get recent orders
        $recentOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top products
        $topProducts = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        return view('supplier.dashboard', compact('seller', 'stats', 'recentOrders', 'topProducts'));
    }

    /**
     * Get supplier statistics
     */
    private function getSupplierStats(MarketplaceSeller $seller): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_products' => MarketplaceProduct::where('seller_id', $seller->id)->count(),
            'active_products' => MarketplaceProduct::where('seller_id', $seller->id)->where('is_active', true)->count(),
            'total_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'pending')->count(),
            'processing_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'processing')->count(),
            'completed_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'completed')->count(),
            'today_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->whereDate('created_at', $today)->count(),
            'week_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisWeek)->count(),
            'month_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)->sum('total_amount'),
            'month_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'average_order_value' => MarketplaceOrderItem::where('seller_id', $seller->id)->avg('total_amount'),
        ];
    }
}
