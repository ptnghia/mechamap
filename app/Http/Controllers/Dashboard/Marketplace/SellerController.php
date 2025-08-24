<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Seller Controller cho Dashboard Marketplace
 *
 * Quản lý seller dashboard và activities
 */
class SellerController extends BaseController
{
    /**
     * Hiển thị seller dashboard
     */
    public function dashboard(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('info', 'Please complete your seller setup first.');
        }

        // Get comprehensive statistics
        $stats = $this->getSellerStats($seller);

        // Get recent orders
        $recentOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get top products
        $topProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get();

        // Get recent reviews
        $recentReviews = collect(); // Placeholder for reviews

        return $this->dashboardResponse('dashboard.marketplace.seller.dashboard', [
            'seller' => $seller,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'recentReviews' => $recentReviews
        ]);
    }

    /**
     * Hiển thị form setup seller
     */
    public function setup()
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        return $this->dashboardResponse('dashboard.marketplace.seller.setup', [
            'seller' => $seller
        ]);
    }

    /**
     * Lưu hoặc cập nhật seller setup
     */
    public function storeSetup(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_slug' => 'required|string|max:255|unique:marketplace_sellers,store_slug,' .
                ($this->user->marketplaceSeller->id ?? 'NULL'),
            'store_description' => 'required|string|max:1000',
            'business_type' => 'required|string|in:individual,company',
            'business_name' => 'nullable|string|max:255',
            'business_registration' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255']);

        $seller = MarketplaceSeller::updateOrCreate(
            ['user_id' => $this->user->id],
            $request->only([
                'store_name', 'store_slug', 'store_description', 'business_type',
                'business_name', 'business_registration', 'tax_id', 'phone',
                'address', 'city', 'state', 'postal_code', 'country',
                'bank_name', 'bank_account_number', 'bank_account_name'
            ])
        );

        return redirect()->route('dashboard.marketplace.seller.dashboard')
            ->with('success', 'Seller setup completed successfully.');
    }

    /**
     * Hiển thị seller orders
     */
    public function orders(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup');
        }

        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $query = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product']);

        // Apply filters
        if ($status) {
            $query->where('fulfillment_status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->whereHas('order.customer', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(20);

        return $this->dashboardResponse('dashboard.marketplace.seller.orders', [
            'seller' => $seller,
            'orderItems' => $orderItems,
            'currentStatus' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'search' => $search
        ]);
    }

    /**
     * Cập nhật fulfillment status của order
     */
    public function updateOrderStatus(Request $request, MarketplaceOrderItem $orderItem): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $orderItem->seller_id !== $seller->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500']);

        $orderItem->update([
            'fulfillment_status' => $request->status,
            'tracking_number' => $request->tracking_number,
            'fulfillment_notes' => $request->notes]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.'
        ]);
    }

    /**
     * Lấy thống kê seller
     */
    private function getSellerStats($seller)
    {
        $totalProducts = MarketplaceProduct::where('seller_id', $seller->id)->count();
        $activeProducts = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->count();

        $totalOrders = MarketplaceOrderItem::where('seller_id', $seller->id)->count();
        $pendingOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('fulfillment_status', 'pending')->count();
        $completedOrders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->where('fulfillment_status', 'delivered')->count();

        $totalRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->sum('total_price');

        $thisMonthRevenue = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->whereHas('order', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_price');

        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'pending_products' => $totalProducts - $activeProducts,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'average_order_value' => $averageOrderValue,
            'conversion_rate' => 0, // Placeholder
            'total_views' => MarketplaceProduct::where('seller_id', $seller->id)->sum('view_count')];
    }

    /**
     * Export seller data
     */
    public function exportData(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller not found'], 404);
        }

        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'orders'); // orders, products, analytics

        switch ($type) {
            case 'orders':
                return $this->exportOrders($seller, $format);
            case 'products':
                return $this->exportProducts($seller, $format);
            case 'analytics':
                return $this->exportAnalytics($seller, $format);
            default:
                return response()->json(['success' => false, 'message' => 'Invalid export type'], 400);
        }
    }

    /**
     * Export orders data
     */
    private function exportOrders($seller, $format)
    {
        $orders = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product'])
            ->get()
            ->map(function ($orderItem) {
                return [
                    'order_number' => $orderItem->order->order_number,
                    'customer_name' => $orderItem->order->customer->name,
                    'customer_email' => $orderItem->order->customer->email,
                    'product_name' => $orderItem->product->name,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->unit_price,
                    'total_price' => $orderItem->total_price,
                    'status' => $orderItem->fulfillment_status,
                    'created_at' => $orderItem->created_at->toISOString()];
            });

        $filename = 'seller_orders_' . $seller->store_slug . '_' . now()->format('Y-m-d');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\""];

            $callback = function () use ($orders) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Order Number', 'Customer Name', 'Customer Email', 'Product Name', 'Quantity', 'Unit Price', 'Total Price', 'Status', 'Created At']);

                foreach ($orders as $order) {
                    fputcsv($file, array_values($order));
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($orders)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }

    /**
     * Export products data
     */
    private function exportProducts($seller, $format)
    {
        // Implementation for product export
        return response()->json(['message' => 'Product export not implemented yet']);
    }

    /**
     * Export analytics data
     */
    private function exportAnalytics($seller, $format)
    {
        // Implementation for analytics export
        return response()->json(['message' => 'Analytics export not implemented yet']);
    }
}
