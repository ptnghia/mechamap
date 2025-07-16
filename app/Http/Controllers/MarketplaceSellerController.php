<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;

class MarketplaceSellerController extends Controller
{
    /**
     * Show seller dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user can sell any product
        if (!$user->canSellAnyProduct()) {
            return redirect()->route('marketplace.seller.setup')
                ->with('error', __('marketplace.seller.setup_required'));
        }

        // Get seller profile
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();
        
        if (!$seller) {
            return redirect()->route('marketplace.seller.setup')
                ->with('error', __('marketplace.seller.profile_not_found'));
        }

        // Get seller statistics
        $stats = $this->getSellerStats($seller);
        
        // Get recent products
        $recentProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent orders
        $recentOrders = MarketplaceOrder::whereHas('items', function($query) use ($seller) {
                $query->whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                });
            })
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('marketplace.seller.dashboard', compact(
            'seller', 
            'stats', 
            'recentProducts', 
            'recentOrders'
        ));
    }

    /**
     * Get seller statistics
     */
    private function getSellerStats($seller)
    {
        $totalProducts = MarketplaceProduct::where('seller_id', $seller->id)->count();
        $activeProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->count();
        
        $pendingProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('status', 'pending')
            ->count();

        // Get total sales
        $totalSales = MarketplaceOrderItem::whereHas('product', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->whereHas('order', function($query) {
                $query->where('status', 'completed');
            })
            ->sum('total_amount');

        // Get total orders
        $totalOrders = MarketplaceOrder::whereHas('items', function($query) use ($seller) {
                $query->whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                });
            })
            ->count();

        // Get pending orders
        $pendingOrders = MarketplaceOrder::whereHas('items', function($query) use ($seller) {
                $query->whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                });
            })
            ->where('status', 'pending')
            ->count();

        // Get this month's sales
        $thisMonthSales = MarketplaceOrderItem::whereHas('product', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->whereHas('order', function($query) {
                $query->where('status', 'completed')
                      ->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
            })
            ->sum('total_amount');

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'pending_products' => $pendingProducts,
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'this_month_sales' => $thisMonthSales,
        ];
    }

    /**
     * Show seller products
     */
    public function products()
    {
        $user = Auth::user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();
        
        if (!$seller) {
            return redirect()->route('marketplace.seller.setup');
        }

        $products = MarketplaceProduct::where('seller_id', $seller->id)
            ->with(['category', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketplace.seller.products', compact('products', 'seller'));
    }

    /**
     * Show seller orders
     */
    public function orders()
    {
        $user = Auth::user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();
        
        if (!$seller) {
            return redirect()->route('marketplace.seller.setup');
        }

        $orders = MarketplaceOrder::whereHas('items', function($query) use ($seller) {
                $query->whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                });
            })
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketplace.seller.orders', compact('orders', 'seller'));
    }

    /**
     * Show seller analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();
        
        if (!$seller) {
            return redirect()->route('marketplace.seller.setup');
        }

        // Get monthly sales data for chart
        $monthlySales = MarketplaceOrderItem::whereHas('product', function($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->whereHas('order', function($query) {
                $query->where('status', 'completed')
                      ->where('created_at', '>=', now()->subMonths(12));
            })
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Get top selling products
        $topProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->withCount(['orderItems as total_sold' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return view('marketplace.seller.analytics', compact(
            'seller', 
            'monthlySales', 
            'topProducts'
        ));
    }
}
