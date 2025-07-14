<?php

namespace App\Http\Controllers\VerifiedPartner;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 🎯 MechaMap Verified Partner Order Controller
 * 
 * Controller cho verified_partner role (L11) - Quản lý đơn hàng
 * Verified Partner có quyền cao nhất trong business partners
 */
class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:verified_partner']);
    }

    /**
     * Display verified partner's orders
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        $query = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with(['order.user', 'product']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereHas('order.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $stats = $this->calculateOrderStats($seller);

        return view('verified-partner.orders.index', compact('orderItems', 'seller', 'stats'));
    }

    /**
     * Display the specified order item
     */
    public function show(MarketplaceOrderItem $orderItem): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $orderItem->product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to order.');
        }

        $orderItem->load(['order.user', 'product', 'order.shippingAddress']);

        return view('verified-partner.orders.show', compact('orderItem', 'seller'));
    }

    /**
     * Update order item status
     */
    public function updateStatus(Request $request, MarketplaceOrderItem $orderItem): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $orderItem->product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to order.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $orderItem->update([
            'status' => $request->status,
            'tracking_number' => $request->tracking_number,
            'seller_notes' => $request->notes,
        ]);

        // Update main order status if all items are completed
        $this->updateMainOrderStatus($orderItem->order);

        return redirect()->route('partner.orders.show', $orderItem)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        $query = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with(['order.user', 'product']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orderItems = $query->orderBy('created_at', 'desc')->get();

        $filename = 'verified-partner-orders-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orderItems) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Mã đơn hàng',
                'Sản phẩm',
                'Khách hàng',
                'Email',
                'Số lượng',
                'Giá',
                'Tổng tiền',
                'Trạng thái',
                'Ngày đặt',
                'Mã vận chuyển',
            ]);

            // CSV data
            foreach ($orderItems as $item) {
                fputcsv($file, [
                    $item->order->order_number,
                    $item->product->name,
                    $item->order->user->name,
                    $item->order->user->email,
                    $item->quantity,
                    number_format($item->price, 0, ',', '.') . ' VND',
                    number_format($item->total_price, 0, ',', '.') . ' VND',
                    $this->getStatusLabel($item->status),
                    $item->created_at->format('d/m/Y H:i'),
                    $item->tracking_number ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate order statistics for verified partner
     */
    private function calculateOrderStats(MarketplaceSeller $seller): array
    {
        $baseQuery = MarketplaceOrderItem::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        });

        return [
            'total_orders' => $baseQuery->count(),
            'pending_orders' => $baseQuery->where('status', 'pending')->count(),
            'processing_orders' => $baseQuery->where('status', 'processing')->count(),
            'shipped_orders' => $baseQuery->where('status', 'shipped')->count(),
            'delivered_orders' => $baseQuery->where('status', 'delivered')->count(),
            'cancelled_orders' => $baseQuery->where('status', 'cancelled')->count(),
            'total_revenue' => $baseQuery->sum('total_price'),
            'this_month_revenue' => $baseQuery->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price'),
            'this_month_orders' => $baseQuery->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Update main order status based on all items
     */
    private function updateMainOrderStatus(MarketplaceOrder $order): void
    {
        $allItems = $order->items;
        $statuses = $allItems->pluck('status')->unique();

        if ($statuses->count() === 1) {
            // All items have same status
            $order->update(['status' => $statuses->first()]);
        } elseif ($statuses->contains('cancelled') && $statuses->count() === 2 && $statuses->contains('delivered')) {
            // Mixed cancelled and delivered
            $order->update(['status' => 'partially_delivered']);
        } elseif ($statuses->contains('delivered')) {
            // Some delivered, some not
            $order->update(['status' => 'partially_delivered']);
        } elseif ($statuses->contains('shipped')) {
            // Some shipped
            $order->update(['status' => 'partially_shipped']);
        } else {
            // Default to processing
            $order->update(['status' => 'processing']);
        }
    }

    /**
     * Get status label in Vietnamese
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định',
        };
    }
}
