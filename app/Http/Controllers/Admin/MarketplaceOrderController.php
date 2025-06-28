<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketplaceOrder::with(['customer', 'items.product', 'items.seller.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by order type
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_orders' => MarketplaceOrder::count(),
            'pending_orders' => MarketplaceOrder::where('status', 'pending')->count(),
            'completed_orders' => MarketplaceOrder::where('status', 'completed')->count(),
            'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount'),
            'today_orders' => MarketplaceOrder::whereDate('created_at', today())->count(),
            'today_revenue' => MarketplaceOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.marketplace.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketplaceOrder $order)
    {
        $order->load([
            'customer',
            'items.product',
            'items.seller.user'
        ]);

        return view('admin.marketplace.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, MarketplaceOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded',
            'admin_notes' => 'nullable|string',
            'tracking_number' => 'nullable|string',
        ]);

        $oldStatus = $order->status;

        DB::transaction(function() use ($order, $validated, $oldStatus) {
            // Update order
            $order->update($validated);

            // Set timestamps based on status
            switch ($validated['status']) {
                case 'confirmed':
                    $order->update(['confirmed_at' => now()]);
                    break;
                case 'processing':
                    $order->update(['processing_at' => now()]);
                    break;
                case 'shipped':
                    $order->update(['shipped_at' => now()]);
                    break;
                case 'delivered':
                    $order->update(['delivered_at' => now()]);
                    break;
                case 'completed':
                    $order->update(['completed_at' => now()]);
                    break;
                case 'cancelled':
                    $order->update(['cancelled_at' => now()]);
                    break;
            }

            // Update order items status accordingly
            $itemStatus = $this->getItemStatusFromOrderStatus($validated['status']);
            if ($itemStatus) {
                $order->items()->update(['fulfillment_status' => $itemStatus]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Trạng thái đơn hàng đã được cập nhật từ '{$order->getStatusLabel($oldStatus)}' thành '{$order->status_label}'!"
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, MarketplaceOrder $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,processing,paid,failed,refunded,partially_refunded',
            'payment_notes' => 'nullable|string',
        ]);

        $order->update($validated);

        if ($validated['payment_status'] === 'paid') {
            $order->update(['paid_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Trạng thái thanh toán đã được cập nhật thành '{$order->payment_status_label}'!"
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, MarketplaceOrder $order)
    {
        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này không thể hủy!'
            ], 400);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);

        DB::transaction(function() use ($order, $validated) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_at' => now(),
            ]);

            // Cancel all order items
            $order->items()->update(['fulfillment_status' => 'cancelled']);

            // If payment was made, initiate refund process
            if ($order->payment_status === 'paid') {
                $order->update(['payment_status' => 'refunded']);
                // TODO: Implement actual refund logic with payment gateway
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được hủy thành công!'
        ]);
    }

    /**
     * Generate invoice
     */
    public function generateInvoice(MarketplaceOrder $order)
    {
        // TODO: Implement invoice generation
        return response()->json([
            'success' => true,
            'message' => 'Hóa đơn đang được tạo...',
            'download_url' => '#'
        ]);
    }

    /**
     * Export orders to Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export
        return response()->json([
            'success' => true,
            'message' => 'Đang xuất dữ liệu...',
            'download_url' => '#'
        ]);
    }

    /**
     * Get order statistics for dashboard
     */
    public function getStatistics(Request $request)
    {
        $period = $request->get('period', '30'); // days

        $stats = [
            'total_orders' => MarketplaceOrder::where('created_at', '>=', now()->subDays($period))->count(),
            'total_revenue' => MarketplaceOrder::where('created_at', '>=', now()->subDays($period))
                ->where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => MarketplaceOrder::where('created_at', '>=', now()->subDays($period))
                ->where('payment_status', 'paid')->avg('total_amount'),
            'completion_rate' => $this->getCompletionRate($period),
            'top_products' => $this->getTopProducts($period),
            'daily_orders' => $this->getDailyOrderStats($period),
        ];

        return response()->json($stats);
    }

    /**
     * Helper methods
     */
    private function getItemStatusFromOrderStatus($orderStatus)
    {
        $mapping = [
            'confirmed' => 'processing',
            'processing' => 'processing',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
        ];

        return $mapping[$orderStatus] ?? null;
    }

    private function getCompletionRate($days)
    {
        $total = MarketplaceOrder::where('created_at', '>=', now()->subDays($days))->count();
        $completed = MarketplaceOrder::where('created_at', '>=', now()->subDays($days))
            ->where('status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    private function getTopProducts($days)
    {
        return MarketplaceOrderItem::select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
    }

    private function getDailyOrderStats($days)
    {
        return MarketplaceOrder::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
