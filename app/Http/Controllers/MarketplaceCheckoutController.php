<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceShoppingCart;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\PermissionService;

class MarketplaceCheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index(): View|RedirectResponse
    {
        // Kiểm tra quyền mua hàng
        if (!PermissionService::canBuy(auth()->user())) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Bạn không có quyền mua hàng. Vui lòng liên hệ admin để được hỗ trợ.');
        }

        $cart = $this->getCart();

        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('marketplace.cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Validate cart items before checkout
        $this->validateCartItems($cart);

        return view('marketplace.checkout.index', compact('cart'));
    }

    /**
     * Process shipping information step
     */
    public function shipping(Request $request)
    {
        // Convert checkbox value to boolean
        $billingSameAsShipping = $request->has('billing_same_as_shipping') ? true : false;
        $request->merge(['billing_same_as_shipping' => $billingSameAsShipping]);

        // Validate input
        $request->validate([
            'shipping_address.first_name' => 'required|string|max:255',
            'shipping_address.last_name' => 'required|string|max:255',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.phone' => 'nullable|string|max:20',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'required|string|max:255',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:2',
            'billing_same_as_shipping' => 'boolean',
            'billing_address' => 'required_if:billing_same_as_shipping,false|array',
        ]);

        try {
            $cart = $this->getOrCreateCart();

            // Store shipping information in session
            Session::put('checkout.shipping_address', $request->shipping_address);
            Session::put('checkout.billing_same_as_shipping', $request->billing_same_as_shipping);

            if (!$request->billing_same_as_shipping) {
                Session::put('checkout.billing_address', $request->billing_address);
            } else {
                Session::put('checkout.billing_address', $request->shipping_address);
            }

            // Calculate shipping costs
            $shippingCost = $this->calculateShipping($cart, $request->shipping_address);
            Session::put('checkout.shipping_cost', $shippingCost);

            // Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Shipping information saved',
                    'shipping_cost' => number_format($shippingCost, 2),
                    'next_step' => 'payment'
                ]);
            }

            // Handle regular form submission - redirect to payment step
            return redirect()->route('marketplace.checkout.index')
                ->with('success', 'Shipping information saved successfully')
                ->with('step', 'payment');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save shipping information: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to save shipping information: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Process payment information step
     */
    public function payment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,sepay',
            'payment_details' => 'required_unless:payment_method,sepay|array',
        ]);

        try {
            // Store payment information in session
            Session::put('checkout.payment_method', $request->payment_method);
            Session::put('checkout.payment_details', $request->payment_details ?? []);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment information saved',
                    'next_step' => 'review'
                ]);
            }

            // Handle regular form submission - redirect to review step
            return redirect()->route('marketplace.checkout.index')
                ->with('success', 'Payment information saved')
                ->with('step', 'review');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save payment information: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to save payment information: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display order review step
     */
    public function review(Request $request)
    {
        if ($request->ajax()) {
            try {
                $cart = $this->getCart();

                if (!$cart) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cart not found'
                    ], 404);
                }

                $shippingAddress = Session::get('checkout.shipping_address');
                $billingAddress = Session::get('checkout.billing_address');
                $paymentMethod = Session::get('checkout.payment_method');
                $shippingCost = Session::get('checkout.shipping_cost', 0);

                if (!$shippingAddress || !$paymentMethod) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Missing checkout information'
                    ], 400);
                }

            // Calculate final totals
            $subtotal = $cart->subtotal;
            $taxAmount = $cart->tax_amount;
            $shippingAmount = $shippingCost;
            $discountAmount = $cart->discount_amount;
            $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

            return response()->json([
                'success' => true,
                'order_summary' => [
                    'subtotal' => number_format($subtotal, 2),
                    'tax_amount' => number_format($taxAmount, 2),
                    'shipping_amount' => number_format($shippingAmount, 2),
                    'discount_amount' => number_format($discountAmount, 2),
                    'total_amount' => number_format($totalAmount, 2),
                ],
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'payment_method' => $paymentMethod,
                'items' => $cart->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'unit_price' => number_format($item->unit_price, 2),
                        'total_price' => number_format($item->total_price, 2),
                    ];
                })
            ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load order review: ' . $e->getMessage()
                ], 500);
            }
        }

        return redirect()->back()->with('error', 'Invalid request method');
    }

    /**
     * Place the order
     */
    public function placeOrder(Request $request)
    {
        // Kiểm tra quyền mua hàng
        if (!PermissionService::canBuy(auth()->user())) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền mua hàng.'
                ], 403);
            }
            return redirect()->route('marketplace.index')
                ->with('error', 'Bạn không có quyền mua hàng. Vui lòng liên hệ admin để được hỗ trợ.');
        }

        try {
            DB::beginTransaction();

            $cart = $this->getCart();

            if (!$cart || $cart->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Get checkout data from session
            $shippingAddress = Session::get('checkout.shipping_address');
            $billingAddress = Session::get('checkout.billing_address');
            $paymentMethod = Session::get('checkout.payment_method');
            $paymentDetails = Session::get('checkout.payment_details');
            $shippingCost = Session::get('checkout.shipping_cost', 0);

            if (!$shippingAddress || !$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing checkout information'
                ], 400);
            }

            // Create order
            $order = $this->createOrder($cart, $shippingAddress, $billingAddress, $paymentMethod, $paymentDetails, $shippingCost);

            // Create order items
            $this->createOrderItems($order, $cart);

            // Process payment
            $paymentResult = $this->processPayment($order, $paymentMethod, $paymentDetails);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['message']
                ], 400);
            }

            // Update order status
            $order->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'confirmed_at' => now(),
                'payment_gateway_id' => $paymentResult['transaction_id'] ?? null,
            ]);

            // Clear cart
            $cart->convertToOrder();

            // Clear checkout session
            Session::forget('checkout');

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('marketplace.checkout.success', $order->uuid)
                ]);
            }

            // Handle regular form submission
            return redirect()->route('marketplace.checkout.success', $order->uuid)
                ->with('success', 'Đơn hàng đã được đặt thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to place order: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Không thể đặt hàng: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display order success page
     */
    public function success(string $uuid): View|RedirectResponse
    {
        $order = MarketplaceOrder::where('uuid', $uuid)->first();

        if (!$order) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Order not found');
        }

        // Only allow access to order owner
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Access denied');
        }

        return view('marketplace.checkout.success', compact('order'));
    }

    /**
     * Get current cart
     */
    protected function getCart(): ?MarketplaceShoppingCart
    {
        $userId = auth()->id();
        $sessionId = Session::getId();

        if ($userId) {
            return MarketplaceShoppingCart::forUser($userId)->active()->first();
        } else {
            return MarketplaceShoppingCart::forSession($sessionId)->active()->first();
        }
    }

    /**
     * Get or create cart for current user/session
     */
    protected function getOrCreateCart(): MarketplaceShoppingCart
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        // If user is logged in, merge guest cart if exists
        if ($userId && session()->has('guest_cart_merged') === false) {
            $cart = MarketplaceShoppingCart::mergeGuestCart($sessionId, $userId);
            session()->put('guest_cart_merged', true);
            return $cart;
        }

        return MarketplaceShoppingCart::getOrCreateForUser($userId, $sessionId);
    }

    /**
     * Validate cart items before checkout
     */
    protected function validateCartItems(MarketplaceShoppingCart $cart): void
    {
        foreach ($cart->items as $item) {
            if (!$item->product || !$item->product->is_active || $item->product->status !== 'approved') {
                throw new \Exception("Product '{$item->product_name}' is no longer available");
            }

            if ($item->product->manage_stock && $item->product->stock_quantity < $item->quantity) {
                throw new \Exception("Insufficient stock for '{$item->product_name}'. Only {$item->product->stock_quantity} available");
            }
        }
    }

    /**
     * Calculate shipping cost
     */
    protected function calculateShipping(MarketplaceShoppingCart $cart, array $shippingAddress): float
    {
        // Check if cart contains only digital products
        $hasPhysicalProducts = $cart->items()->whereHas('product', function($query) {
            $query->where('product_type', '!=', 'digital');
        })->exists();

        // No shipping for digital-only carts
        if (!$hasPhysicalProducts) {
            return 0.00;
        }

        // Basic shipping calculation for physical products
        $baseShipping = 10.00;
        $freeShippingThreshold = 100.00;

        if ($cart->subtotal >= $freeShippingThreshold) {
            return 0.00;
        }

        // International shipping
        if ($shippingAddress['country'] !== 'VN') {
            $baseShipping += 15.00;
        }

        // Express shipping (if selected)
        if (Session::get('checkout.shipping_method') === 'express') {
            $baseShipping += 10.00;
        }

        return $baseShipping;
    }

    /**
     * Create order from cart
     */
    protected function createOrder(
        MarketplaceShoppingCart $cart,
        array $shippingAddress,
        array $billingAddress,
        string $paymentMethod,
        array $paymentDetails,
        float $shippingCost
    ): MarketplaceOrder {
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // Ensure user is authenticated
        $customerId = auth()->id();
        if (!$customerId) {
            throw new \Exception('User not authenticated');
        }

        return MarketplaceOrder::create([
            'uuid' => Str::uuid(),
            'order_number' => $orderNumber,
            'customer_id' => $customerId,
            'customer_email' => $shippingAddress['email'],
            'customer_phone' => $shippingAddress['phone'] ?? null,
            'order_type' => 'product_purchase',
            'subtotal' => $cart->subtotal,
            'tax_amount' => $cart->tax_amount,
            'shipping_amount' => $shippingCost,
            'discount_amount' => $cart->discount_amount,
            'total_amount' => $cart->subtotal + $cart->tax_amount + $shippingCost - $cart->discount_amount,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_status' => 'pending',
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'shipping_method' => Session::get('checkout.shipping_method', 'standard'),
            'payment_method' => $paymentMethod,
            'payment_details' => $paymentDetails,
            'customer_notes' => Session::get('checkout.customer_notes'),
        ]);
    }

    /**
     * Create order items from cart
     */
    protected function createOrderItems(MarketplaceOrder $order, MarketplaceShoppingCart $cart): void
    {
        foreach ($cart->items as $cartItem) {
            $itemSubtotal = $cartItem->total_price;
            $itemTax = 0; // Calculate if needed
            $itemTotal = $itemSubtotal + $itemTax;

            // Calculate commission (15% platform fee)
            $commissionRate = 15.00;
            $commissionAmount = $itemSubtotal * ($commissionRate / 100);
            $sellerEarnings = $itemSubtotal - $commissionAmount;

            MarketplaceOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'seller_id' => $cartItem->product->seller_id,
                'product_name' => $cartItem->product_name,
                'product_sku' => $cartItem->product_sku,
                'product_description' => $cartItem->product->description ?? null,
                'product_specifications' => $cartItem->product->technical_specs ?? null,
                'unit_price' => $cartItem->unit_price,
                'sale_price' => $cartItem->sale_price,
                'quantity' => $cartItem->quantity,
                'subtotal' => $itemSubtotal,
                'tax_amount' => $itemTax,
                'total_amount' => $itemTotal,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'seller_earnings' => $sellerEarnings,
                'fulfillment_status' => 'pending',
            ]);

            // Update product stock if managed
            if ($cartItem->product->manage_stock) {
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }
        }
    }

    /**
     * Process payment
     */
    protected function processPayment(MarketplaceOrder $order, string $paymentMethod, array $paymentDetails): array
    {
        // Process payment with real payment gateways
        switch ($paymentMethod) {
            case 'stripe':
                return $this->processStripePayment($order, $paymentDetails);
            case 'sepay':
                return $this->processSePayPayment($order, $paymentDetails);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // For marketplace checkout, we'll redirect to API payment flow
        // This is a simplified implementation
        return [
            'success' => true,
            'redirect_to_api' => true,
            'message' => 'Redirect to Stripe payment',
            'api_endpoint' => '/api/v1/payment/stripe/create-intent',
            'order_id' => $order->id
        ];
    }

    /**
     * Process SePay payment
     */
    protected function processSePayPayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // For marketplace checkout, we'll redirect to API payment flow
        // This is a simplified implementation
        return [
            'success' => true,
            'redirect_to_api' => true,
            'message' => 'Redirect to SePay payment',
            'api_endpoint' => '/api/v1/payment/sepay/create-payment',
            'order_id' => $order->id
        ];
    }
}
