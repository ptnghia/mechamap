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

class MarketplaceCheckoutController extends Controller
{
    /**
     * Display checkout page
     */
    public function index(): View|RedirectResponse
    {
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
    public function shipping(Request $request): JsonResponse
    {
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
            $cart = $this->getCart();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Shipping information saved',
                'shipping_cost' => number_format($shippingCost, 2),
                'next_step' => 'payment'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save shipping information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment information step
     */
    public function payment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,cod',
            'payment_details' => 'required|array',
        ]);

        try {
            // Store payment information in session
            Session::put('checkout.payment_method', $request->payment_method);
            Session::put('checkout.payment_details', $request->payment_details);

            return response()->json([
                'success' => true,
                'message' => 'Payment information saved',
                'next_step' => 'review'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save payment information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display order review step
     */
    public function review(): JsonResponse
    {
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

    /**
     * Place the order
     */
    public function placeOrder(Request $request): JsonResponse
    {
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

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('marketplace.checkout.success', $order->uuid)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
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
        // Basic shipping calculation
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

        return MarketplaceOrder::create([
            'uuid' => Str::uuid(),
            'order_number' => $orderNumber,
            'customer_id' => auth()->id(),
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
                'subtotal' => $cartItem->total_price,
                'tax_amount' => 0, // Calculate if needed
                'total_amount' => $cartItem->total_price,
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
        // Mock payment processing
        switch ($paymentMethod) {
            case 'credit_card':
                return $this->processCreditCardPayment($order, $paymentDetails);
            case 'paypal':
                return $this->processPayPalPayment($order, $paymentDetails);
            case 'bank_transfer':
                return $this->processBankTransferPayment($order, $paymentDetails);
            case 'cod':
                return $this->processCODPayment($order, $paymentDetails);
            default:
                return ['success' => false, 'message' => 'Invalid payment method'];
        }
    }

    /**
     * Process credit card payment
     */
    protected function processCreditCardPayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // Mock credit card processing
        // In real implementation, integrate with payment gateway like Stripe, PayPal, etc.

        // Simulate payment success/failure
        $success = rand(1, 10) > 1; // 90% success rate for demo

        if ($success) {
            return [
                'success' => true,
                'transaction_id' => 'cc_' . strtoupper(Str::random(12)),
                'message' => 'Payment processed successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Credit card payment failed'
            ];
        }
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // Mock PayPal processing
        return [
            'success' => true,
            'transaction_id' => 'pp_' . strtoupper(Str::random(12)),
            'message' => 'PayPal payment processed successfully'
        ];
    }

    /**
     * Process bank transfer payment
     */
    protected function processBankTransferPayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // Bank transfer requires manual verification
        return [
            'success' => true,
            'transaction_id' => 'bt_' . strtoupper(Str::random(12)),
            'message' => 'Bank transfer initiated. Please complete the transfer and provide proof.'
        ];
    }

    /**
     * Process cash on delivery payment
     */
    protected function processCODPayment(MarketplaceOrder $order, array $paymentDetails): array
    {
        // COD doesn't require immediate payment
        return [
            'success' => true,
            'transaction_id' => 'cod_' . strtoupper(Str::random(12)),
            'message' => 'Cash on delivery order confirmed'
        ];
    }
}
