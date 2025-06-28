<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class MarketplaceOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of user's orders
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = MarketplaceOrder::where('customer_id', $user->id)
            ->with(['items.product', 'items.seller.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $orders = $query->paginate(10)->withQueryString();

        // Statistics for user
        $stats = [
            'total_orders' => MarketplaceOrder::where('customer_id', $user->id)->count(),
            'pending_orders' => MarketplaceOrder::where('customer_id', $user->id)->where('status', 'pending')->count(),
            'completed_orders' => MarketplaceOrder::where('customer_id', $user->id)->where('status', 'completed')->count(),
            'total_spent' => MarketplaceOrder::where('customer_id', $user->id)->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('marketplace.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show(MarketplaceOrder $order): View
    {
        // Ensure user can only view their own orders
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        $order->load([
            'items.product',
            'items.seller.user',
            'customer'
        ]);

        return view('marketplace.orders.show', compact('order'));
    }

    /**
     * Download order invoice
     */
    public function invoice(MarketplaceOrder $order)
    {
        // Ensure user can only download their own invoices
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice');
        }

        // Generate and return PDF invoice
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('marketplace.orders.invoice', compact('order'));
        
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * Track order status
     */
    public function tracking(MarketplaceOrder $order): View
    {
        // Ensure user can only track their own orders
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order tracking');
        }

        $order->load(['items.product', 'items.seller.user']);

        return view('marketplace.orders.tracking', compact('order'));
    }

    /**
     * Reorder items from a previous order
     */
    public function reorder(Request $request, MarketplaceOrder $order): JsonResponse
    {
        // Ensure user can only reorder their own orders
        if ($order->customer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $cartController = new MarketplaceCartController();
            $addedItems = 0;
            $unavailableItems = [];

            foreach ($order->items as $item) {
                // Check if product is still available
                if ($item->product && $item->product->status === 'active') {
                    $addRequest = new Request([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'seller_id' => $item->seller_id
                    ]);
                    
                    $result = $cartController->add($addRequest);
                    if ($result->getStatusCode() === 200) {
                        $addedItems++;
                    }
                } else {
                    $unavailableItems[] = $item->product->name ?? 'Unknown Product';
                }
            }

            $message = "Added {$addedItems} items to cart";
            if (!empty($unavailableItems)) {
                $message .= ". Some items are no longer available: " . implode(', ', $unavailableItems);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'added_items' => $addedItems,
                'unavailable_items' => $unavailableItems
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding items to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        // Ensure user can only cancel their own orders
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now()
        ]);

        // TODO: Handle refund if payment was already processed

        return redirect()->route('marketplace.orders.index')
            ->with('success', 'Order cancelled successfully');
    }

    /**
     * Request return/refund for an order
     */
    public function returnRequest(Request $request, MarketplaceOrder $order): RedirectResponse
    {
        // Ensure user can only request return for their own orders
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Check if order is eligible for return
        if ($order->status !== 'completed') {
            return redirect()->back()->with('error', 'Only completed orders can be returned');
        }

        $request->validate([
            'return_reason' => 'required|string|max:500',
            'return_type' => 'required|in:refund,exchange'
        ]);

        $order->update([
            'return_requested' => true,
            'return_reason' => $request->return_reason,
            'return_type' => $request->return_type,
            'return_requested_at' => now()
        ]);

        return redirect()->route('marketplace.orders.show', $order)
            ->with('success', 'Return request submitted successfully');
    }
}
