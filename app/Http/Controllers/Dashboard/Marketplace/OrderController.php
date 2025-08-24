<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Order Controller cho Dashboard Marketplace
 *
 * Quản lý orders của user trong dashboard
 */
class OrderController extends BaseController
{
    /**
     * Hiển thị danh sách orders của user
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sort = $request->get('sort', 'newest');

        $query = MarketplaceOrder::where('customer_id', $this->user->id)
            ->with(['items.product', 'items.seller.user']);

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'amount_high':
                $query->orderByDesc('total_amount');
                break;
            case 'amount_low':
                $query->orderBy('total_amount');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $orders = $query->paginate(20);

        // Get statistics
        $stats = $this->getOrderStats();

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Marketplace', 'route' => null],
            ['name' => 'My Orders', 'route' => 'dashboard.marketplace.orders']
        ]);

        return $this->dashboardResponse('dashboard.marketplace.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'search' => $search,
            'currentStatus' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentSort' => $sort,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Hiển thị chi tiết order
     */
    public function show(MarketplaceOrder $order)
    {
        // Ensure user can only view their own orders
        if ($order->customer_id !== $this->user->id) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load([
            'items.product.digitalFiles',
            'items.seller.user',
            'customer'
        ]);

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Marketplace', 'route' => null],
            ['name' => 'My Orders', 'route' => 'dashboard.marketplace.orders'],
            ['name' => 'Order #' . $order->order_number, 'route' => null]
        ]);

        return $this->dashboardResponse('dashboard.marketplace.orders.show', [
            'order' => $order,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Hiển thị trang tracking order
     */
    public function tracking(MarketplaceOrder $order)
    {
        // Ensure user can only track their own orders
        if ($order->customer_id !== $this->user->id) {
            abort(403, 'Unauthorized access to order tracking');
        }

        $order->load(['items.product', 'items.seller.user']);

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Marketplace', 'route' => null],
            ['name' => 'My Orders', 'route' => 'dashboard.marketplace.orders'],
            ['name' => 'Track Order', 'route' => null]
        ]);

        return $this->dashboardResponse('dashboard.marketplace.orders.tracking', [
            'order' => $order,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Reorder items từ order cũ
     */
    public function reorder(Request $request, MarketplaceOrder $order): JsonResponse
    {
        // Ensure user can only reorder their own orders
        if ($order->customer_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $addedItems = 0;
        $unavailableItems = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            // Check if product is still available
            if (!$product || !$product->is_active || $product->status !== 'approved') {
                $unavailableItems[] = $product ? $product->name : 'Unknown Product';
                continue;
            }

            // Add to cart (assuming cart service exists)
            try {
                // Add item to cart logic here
                $addedItems++;
            } catch (\Exception $e) {
                $unavailableItems[] = $product->name;
            }
        }

        $message = "Added {$addedItems} items to cart.";
        if (!empty($unavailableItems)) {
            $message .= " " . count($unavailableItems) . " items are no longer available.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added_items' => $addedItems,
            'unavailable_items' => $unavailableItems
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, MarketplaceOrder $order): JsonResponse
    {
        // Ensure user can only cancel their own orders
        if ($order->customer_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.'
            ], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }

    /**
     * Lấy thống kê orders
     */
    private function getOrderStats()
    {
        $total = MarketplaceOrder::where('customer_id', $this->user->id)->count();
        $pending = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'pending')->count();
        $confirmed = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'confirmed')->count();
        $processing = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'processing')->count();
        $shipped = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'shipped')->count();
        $delivered = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'delivered')->count();
        $completed = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'completed')->count();
        $cancelled = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('status', 'cancelled')->count();

        $totalSpent = MarketplaceOrder::where('customer_id', $this->user->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $averageOrderValue = $total > 0 ? $totalSpent / $total : 0;

        return [
            'total' => $total,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'shipped' => $shipped,
            'delivered' => $delivered,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'total_spent' => $totalSpent,
            'average_order_value' => $averageOrderValue,
            'this_month' => MarketplaceOrder::where('customer_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'this_year' => MarketplaceOrder::where('customer_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
                ->count(),
        ];
    }

    /**
     * Export orders
     */
    public function exportOrders(Request $request)
    {
        $format = $request->get('format', 'csv');

        $orders = MarketplaceOrder::where('customer_id', $this->user->id)
            ->with(['items.product'])
            ->get()
            ->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items->count(),
                    'created_at' => $order->created_at->toISOString(),
                ];
            });

        $filename = 'orders_' . $this->user->username . '_' . now()->format('Y-m-d');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ];

            $callback = function () use ($orders) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Order Number', 'Status', 'Payment Status', 'Total Amount', 'Items Count', 'Created At']);

                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order['order_number'],
                        $order['status'],
                        $order['payment_status'],
                        $order['total_amount'],
                        $order['items_count'],
                        $order['created_at'],
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to JSON
        return response()->json($orders)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }
}
