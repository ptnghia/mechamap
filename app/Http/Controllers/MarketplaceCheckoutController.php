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
use App\Services\MarketplacePermissionService;

class MarketplaceCheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $cart = $this->getCart();

        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('marketplace.cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Validate cart items and permissions before checkout
        $this->validateCartItems($cart);
        $this->validateCartPermissions($cart, $user);

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
        $user = auth()->user();
        $cart = $this->getCart();

        if (!$cart || $cart->isEmpty()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }
            return redirect()->route('marketplace.cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Validate cart items and permissions
        try {
            $this->validateCartItems($cart);
            $this->validateCartPermissions($cart, $user);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 403);
            }
            return redirect()->route('marketplace.cart.index')
                ->with('error', $e->getMessage());
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

            // Clear cart after successful order creation
            $cart->convertToOrder();

            // Clear checkout session
            Session::forget('checkout');

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_required' => true,
                    'payment_method' => $paymentMethod,
                    'redirect_url' => route('marketplace.checkout.success', $order->uuid)
                ]);
            }

            // Handle regular form submission - redirect to payment
            return redirect()->route('marketplace.checkout.payment-gateway', $order->uuid)
                ->with('success', 'Đơn hàng đã được tạo thành công!');

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
     * Validate cart permissions before checkout
     */
    protected function validateCartPermissions(MarketplaceShoppingCart $cart, $user): void
    {
        if (!$user) {
            throw new \Exception('Bạn cần đăng nhập để thực hiện thanh toán');
        }

        foreach ($cart->items as $item) {
            if (!$item->product) {
                continue;
            }

            // Check if user has permission to buy this product type
            if (!MarketplacePermissionService::canBuy($user, $item->product->product_type)) {
                $allowedTypes = MarketplacePermissionService::getAllowedBuyTypes($user->role ?? 'guest');
                throw new \Exception("Bạn không có quyền mua sản phẩm '{$item->product_name}'. Bạn chỉ có thể mua: " . implode(', ', $allowedTypes));
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

    /**
     * Display payment gateway page
     */
    public function paymentGateway(Request $request, $uuid)
    {
        $order = MarketplaceOrder::where('uuid', $uuid)->firstOrFail();

        // Check if user owns this order
        if ($order->customer_id !== auth()->id()) {
            abort(403, 'Unauthorized access to order');
        }

        // Check if order is in correct status
        if ($order->status !== 'pending') {
            return redirect()->route('marketplace.orders.show', $order->uuid)
                ->with('info', 'Đơn hàng này đã được xử lý');
        }

        // Generate payment data based on payment method
        $paymentData = $this->generatePaymentData($order);

        return view('marketplace.checkout.payment-gateway', $paymentData);
    }

    /**
     * Check payment status via AJAX
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:marketplace_orders,id'
        ]);

        $order = MarketplaceOrder::where('id', $request->order_id)
            ->where('customer_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status
        ]);
    }

    /**
     * Generate payment data for the order
     */
    private function generatePaymentData(MarketplaceOrder $order)
    {
        // SePay configuration from config/services.php
        $bankInfo = [
            'bank_code' => config('services.sepay.bank_code', 'MBBank'),
            'bank_name' => config('services.sepay.bank_code', 'MBBank'),
            'account_number' => config('services.sepay.account_number'),
            'account_name' => config('services.sepay.account_name'),
        ];

        // Generate transfer content with order ID
        $transferContent = 'DH' . $order->id;

        // Generate QR URL
        $qrUrl = 'https://qr.sepay.vn/img?' . http_build_query([
            'bank' => $bankInfo['bank_code'],
            'acc' => $bankInfo['account_number'],
            'template' => 'compact',
            'amount' => intval($order->total_amount),
            'des' => $transferContent
        ]);

        return [
            'order' => $order,
            'bank_info' => $bankInfo,
            'transfer_content' => $transferContent,
            'qr_url' => $qrUrl
        ];
    }

    /**
     * Handle SePay webhook
     */
    public function sepayWebhook(Request $request)
    {
        try {
            // Get webhook data
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'No data'], 400);
            }

            // Log webhook for debugging
            \Log::info('SePay Webhook received', $data);

            // Extract order ID from transaction content using regex
            $transactionContent = $data['content'] ?? '';
            $regex = '/DH(\d+)/';

            if (!preg_match($regex, $transactionContent, $matches)) {
                \Log::warning('SePay Webhook: Order ID not found in content', ['content' => $transactionContent]);
                return response()->json(['success' => false, 'message' => 'Order ID not found'], 400);
            }

            $orderId = $matches[1];
            $transferAmount = $data['transferAmount'] ?? 0;
            $transferType = $data['transferType'] ?? '';

            // Only process incoming transfers
            if ($transferType !== 'in') {
                return response()->json(['success' => false, 'message' => 'Not an incoming transfer'], 400);
            }

            // Find the order
            $order = MarketplaceOrder::where('id', $orderId)
                ->where('total_amount', $transferAmount)
                ->where('payment_status', 'pending')
                ->first();

            if (!$order) {
                \Log::warning('SePay Webhook: Order not found or already processed', [
                    'order_id' => $orderId,
                    'amount' => $transferAmount
                ]);
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            // Update order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'payment_gateway_id' => $data['id'] ?? null,
            ]);

            // Log successful payment
            \Log::info('SePay Webhook: Payment processed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $transferAmount
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('SePay Webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }
}
