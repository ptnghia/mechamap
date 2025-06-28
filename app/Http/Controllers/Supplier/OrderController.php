<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceSeller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supplier']);
    }

    /**
     * Display supplier's orders
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $query = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('fulfillment_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%');
            })->orWhere('product_name', 'like', '%' . $request->search . '%');
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get order statistics
        $stats = $this->getOrderStats($seller);

        return view('supplier.orders.index', compact('orderItems', 'stats', 'seller'));
    }

    /**
     * Show order details
     */
    public function show(MarketplaceOrderItem $orderItem): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $orderItem->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $orderItem->load(['order.customer', 'product']);

        return view('supplier.orders.show', compact('orderItem', 'seller'));
    }

    /**
     * Update order fulfillment status
     */
    public function updateStatus(Request $request, MarketplaceOrderItem $orderItem): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $orderItem->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền cập nhật đơn hàng này.');
        }

        $validated = $request->validate([
            'fulfillment_status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $orderItem->update([
            'fulfillment_status' => $validated['fulfillment_status'],
            'tracking_number' => $validated['tracking_number'],
            'carrier' => $validated['carrier'],
        ]);

        // Update timestamps based on status
        switch ($validated['fulfillment_status']) {
            case 'shipped':
                $orderItem->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $orderItem->update(['delivered_at' => now()]);
                break;
        }

        // Update main order status if needed
        $this->updateMainOrderStatus($orderItem->order);

        return redirect()->route('supplier.orders.show', $orderItem)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    /**
     * Bulk update order statuses
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'order_items' => 'required|array',
            'order_items.*' => 'exists:marketplace_order_items,id',
            'action' => 'required|in:mark_processing,mark_shipped,mark_delivered,cancel',
            'tracking_number' => 'nullable|string|max:255',
            'carrier' => 'nullable|string|max:255',
        ]);

        $orderItems = MarketplaceOrderItem::whereIn('id', $validated['order_items'])
            ->where('seller_id', $seller->id)
            ->get();

        $statusMap = [
            'mark_processing' => 'processing',
            'mark_shipped' => 'shipped',
            'mark_delivered' => 'delivered',
            'cancel' => 'cancelled',
        ];

        $status = $statusMap[$validated['action']];
        $updateData = ['fulfillment_status' => $status];

        if ($status === 'shipped') {
            $updateData['shipped_at'] = now();
            if ($validated['tracking_number']) {
                $updateData['tracking_number'] = $validated['tracking_number'];
            }
            if ($validated['carrier']) {
                $updateData['carrier'] = $validated['carrier'];
            }
        } elseif ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        foreach ($orderItems as $orderItem) {
            $orderItem->update($updateData);
            $this->updateMainOrderStatus($orderItem->order);
        }

        return redirect()->route('supplier.orders.index')
            ->with('success', 'Đã cập nhật trạng thái cho ' . count($orderItems) . ' đơn hàng.');
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $query = MarketplaceOrderItem::where('seller_id', $seller->id)
            ->with(['order.customer', 'product']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('fulfillment_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orderItems = $query->orderBy('created_at', 'desc')->get();

        $filename = 'supplier_orders_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orderItems) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Mã đơn hàng',
                'Khách hàng',
                'Email khách hàng',
                'Sản phẩm',
                'SKU',
                'Số lượng',
                'Đơn giá',
                'Tổng tiền',
                'Trạng thái',
                'Ngày đặt',
                'Ngày giao',
                'Mã vận đơn',
                'Đơn vị vận chuyển'
            ]);

            foreach ($orderItems as $item) {
                fputcsv($file, [
                    $item->order->order_number,
                    $item->order->customer->name,
                    $item->order->customer->email,
                    $item->product_name,
                    $item->product_sku,
                    $item->quantity,
                    $item->unit_price,
                    $item->total_price,
                    $item->fulfillment_status,
                    $item->created_at->format('d/m/Y H:i'),
                    $item->delivered_at ? $item->delivered_at->format('d/m/Y H:i') : '',
                    $item->tracking_number ?? '',
                    $item->carrier ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get order statistics for supplier
     */
    private function getOrderStats(MarketplaceSeller $seller): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->count(),
            'pending_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'pending')->count(),
            'processing_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'processing')->count(),
            'shipped_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'shipped')->count(),
            'delivered_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'delivered')->count(),
            'cancelled_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('fulfillment_status', 'cancelled')->count(),
            'today_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->whereDate('created_at', $today)->count(),
            'week_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisWeek)->count(),
            'month_orders' => MarketplaceOrderItem::where('seller_id', $seller->id)->where('created_at', '>=', $thisMonth)->count(),
        ];
    }

    /**
     * Update main order status based on order items
     */
    private function updateMainOrderStatus(MarketplaceOrder $order): void
    {
        $orderItems = $order->items;
        $totalItems = $orderItems->count();

        if ($totalItems === 0) return;

        $statusCounts = $orderItems->groupBy('fulfillment_status')->map->count();

        // Determine main order status
        if ($statusCounts->get('delivered', 0) === $totalItems) {
            $order->update(['fulfillment_status' => 'delivered']);
        } elseif ($statusCounts->get('cancelled', 0) === $totalItems) {
            $order->update(['fulfillment_status' => 'cancelled']);
        } elseif ($statusCounts->get('shipped', 0) > 0) {
            $order->update(['fulfillment_status' => 'shipped']);
        } elseif ($statusCounts->get('processing', 0) > 0) {
            $order->update(['fulfillment_status' => 'processing']);
        } else {
            $order->update(['fulfillment_status' => 'pending']);
        }
    }
}
