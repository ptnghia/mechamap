<?php

namespace App\Http\Controllers\Manufacturer;

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
        $this->middleware(['auth', 'role:manufacturer']);
    }

    /**
     * Display manufacturer dashboard
     */
    public function index(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return view('manufacturer.setup-required');
        }

        // Get manufacturer statistics
        $stats = $this->getManufacturerStats($seller);

        // Get recent technical product orders
        $recentOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product'])
            ->whereHas('product', function ($query) {
                $query->where('product_type', 'digital'); // Technical documents/designs
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top technical products
        $topProducts = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('product', function ($query) {
                $query->where('product_type', 'digital');
            })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        return view('manufacturer.dashboard', compact('seller', 'stats', 'recentOrders', 'topProducts'));
    }

    /**
     * Get manufacturer statistics
     */
    private function getManufacturerStats(MarketplaceSeller $seller): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_designs' => MarketplaceProduct::where('seller_id', $seller->id)->where('product_type', 'digital')->count(),
            'active_designs' => MarketplaceProduct::where('seller_id', $seller->id)->where('product_type', 'digital')->where('is_active', true)->count(),
            'total_downloads' => MarketplaceOrderItem::where('seller_id', $seller->id)->whereHas('product', function ($q) {
                $q->where('product_type', 'digital');
            })->sum('download_count'),
            'total_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'pending')->count(),
            'completed_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'downloaded')->count(),
            'today_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->whereDate('created_at', $today)->count(),
            'week_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisWeek)->count(),
            'month_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)->sum('total_amount'),
            'month_revenue' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'average_order_value' => MarketplaceOrderItem::where('seller_id', $seller->id)->avg('total_amount'),
        ];
    }
}
