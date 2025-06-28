<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display orders listing
     */
    public function index(Request $request): View
    {
        $query = MarketplaceOrder::with(['customer', 'items.product', 'items.seller'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20);

        // Get statistics for dashboard
        $stats = $this->getOrderStatistics();

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show(MarketplaceOrder $order): View
    {
        $order->load([
            'customer',
            'items.product',
            'items.seller.user',
            'sellers'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Update order status
            $order->update([
                'status' => $newStatus,
                'admin_notes' => $request->notes ?
                    ($order->admin_notes ? $order->admin_notes . "\n" . $request->notes : $request->notes) :
                    $order->admin_notes,
            ]);

            // Update timestamps based on status
            $this->updateOrderTimestamps($order, $newStatus);

            // Update order items status if needed
            $this->updateOrderItemsStatus($order, $newStatus);

            // Log status change
            $this->logStatusChange($order, $oldStatus, $newStatus, $request->notes);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order status updated to {$newStatus}",
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, MarketplaceOrder $order): JsonResponse
    {
        $request->validate([
            'payment_status' => 'required|in:pending,processing,paid,failed,refunded,partially_refunded',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $order->update([
                'payment_status' => $request->payment_status,
                'payment_gateway_id' => $request->transaction_id ?: $order->payment_gateway_id,
                'admin_notes' => $request->notes ?
                    ($order->admin_notes ? $order->admin_notes . "\n" . $request->notes : $request->notes) :
                    $order->admin_notes,
                'paid_at' => $request->payment_status === 'paid' ? now() : $order->paid_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Payment status updated to {$request->payment_status}",
                'order' => $order->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update orders
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:marketplace_orders,id',
            'action' => 'required|in:update_status,update_payment_status,delete',
            'status' => 'required_if:action,update_status|in:pending,confirmed,processing,shipped,delivered,completed,cancelled,refunded',
            'payment_status' => 'required_if:action,update_payment_status|in:pending,processing,paid,failed,refunded,partially_refunded',
        ]);

        try {
            DB::beginTransaction();

            $orders = MarketplaceOrder::whereIn('id', $request->order_ids)->get();
            $updatedCount = 0;

            foreach ($orders as $order) {
                switch ($request->action) {
                    case 'update_status':
                        $order->update(['status' => $request->status]);
                        $this->updateOrderTimestamps($order, $request->status);
                        $updatedCount++;
                        break;

                    case 'update_payment_status':
                        $order->update([
                            'payment_status' => $request->payment_status,
                            'paid_at' => $request->payment_status === 'paid' ? now() : $order->paid_at,
                        ]);
                        $updatedCount++;
                        break;

                    case 'delete':
                        if ($order->status === 'pending' || $order->status === 'cancelled') {
                            $order->delete();
                            $updatedCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} orders",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order analytics data
     */
    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', '30'); // days
        $startDate = Carbon::now()->subDays($period);

        $analytics = [
            'total_orders' => MarketplaceOrder::where('created_at', '>=', $startDate)->count(),
            'total_revenue' => MarketplaceOrder::where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'average_order_value' => MarketplaceOrder::where('created_at', '>=', $startDate)
                ->where('payment_status', 'paid')
                ->avg('total_amount'),
            'orders_by_status' => MarketplaceOrder::where('created_at', '>=', $startDate)
                ->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status'),
            'orders_by_day' => MarketplaceOrder::where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->selectRaw('DATE(created_at) as date, count(*) as count, sum(total_amount) as revenue')
                ->orderBy('date')
                ->get(),
            'top_products' => MarketplaceOrderItem::whereHas('order', function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })
                ->groupBy('product_id', 'product_name')
                ->selectRaw('product_id, product_name, sum(quantity) as total_quantity, sum(total_amount) as total_revenue')
                ->orderBy('total_revenue', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = MarketplaceOrder::with(['customer', 'items']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Status',
                'Payment Status',
                'Total Amount',
                'Items Count',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer->name ?? 'Guest',
                    $order->customer_email,
                    $order->status,
                    $order->payment_status,
                    $order->total_amount,
                    $order->items->count(),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get order statistics for dashboard
     */
    protected function getOrderStatistics(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_orders' => MarketplaceOrder::count(),
            'pending_orders' => MarketplaceOrder::where('status', 'pending')->count(),
            'processing_orders' => MarketplaceOrder::where('status', 'processing')->count(),
            'completed_orders' => MarketplaceOrder::where('status', 'completed')->count(),
            'today_orders' => MarketplaceOrder::whereDate('created_at', $today)->count(),
            'week_orders' => MarketplaceOrder::where('created_at', '>=', $thisWeek)->count(),
            'month_orders' => MarketplaceOrder::where('created_at', '>=', $thisMonth)->count(),
            'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount'),
            'month_revenue' => MarketplaceOrder::where('payment_status', 'paid')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'average_order_value' => MarketplaceOrder::where('payment_status', 'paid')->avg('total_amount'),
        ];
    }

    /**
     * Update order timestamps based on status
     */
    protected function updateOrderTimestamps(MarketplaceOrder $order, string $status): void
    {
        $updates = [];

        switch ($status) {
            case 'confirmed':
                if (!$order->confirmed_at) {
                    $updates['confirmed_at'] = now();
                }
                break;
            case 'processing':
                if (!$order->processing_at) {
                    $updates['processing_at'] = now();
                }
                break;
            case 'shipped':
                if (!$order->shipped_at) {
                    $updates['shipped_at'] = now();
                }
                break;
            case 'delivered':
                if (!$order->delivered_at) {
                    $updates['delivered_at'] = now();
                }
                break;
            case 'completed':
                if (!$order->completed_at) {
                    $updates['completed_at'] = now();
                }
                break;
            case 'cancelled':
                if (!$order->cancelled_at) {
                    $updates['cancelled_at'] = now();
                }
                break;
        }

        if (!empty($updates)) {
            $order->update($updates);
        }
    }

    /**
     * Update order items status based on order status
     */
    protected function updateOrderItemsStatus(MarketplaceOrder $order, string $orderStatus): void
    {
        $itemStatus = match($orderStatus) {
            'confirmed', 'processing' => 'processing',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            'refunded' => 'refunded',
            default => 'pending'
        };

        $order->items()->update(['fulfillment_status' => $itemStatus]);
    }

    /**
     * Log status change for audit trail
     */
    protected function logStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus, ?string $notes): void
    {
        // This could be expanded to use a dedicated audit log table
        $logEntry = [
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'System',
            'action' => 'status_change',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
        ];

        $metadata = $order->metadata ?? [];
        $metadata['status_history'][] = $logEntry;

        $order->update(['metadata' => $metadata]);
    }
}
