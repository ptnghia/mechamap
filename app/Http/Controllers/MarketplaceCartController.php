<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceProduct;
use App\Models\MarketplaceShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MarketplaceCartController extends Controller
{
    /**
     * Display the shopping cart
     */
    public function index(): View
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.seller.user', 'items.product.category']);

        return view('marketplace.cart.index', compact('cart'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:marketplace_products,id',
            'quantity' => 'integer|min:1|max:100',
            'options' => 'array'
        ]);

        try {
            $product = MarketplaceProduct::findOrFail($request->product_id);

            // Check if product is available
            if (!$product->is_active || $product->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Product is not available'
                ], 400);
            }

            // Check stock
            if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $product->stock_quantity . ' items available.'
                ], 400);
            }

            $cart = $this->getOrCreateCart();
            $quantity = $request->quantity ?? 1;
            $options = $request->options ?? [];

            $cartItem = $cart->addProduct($product, $quantity, $options);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart' => [
                    'total_items' => $cart->total_items,
                    'subtotal' => number_format($cart->subtotal, 2),
                    'total_amount' => number_format($cart->total_amount, 2),
                ],
                'item' => [
                    'id' => $cartItem->id,
                    'product_name' => $cartItem->product_name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => number_format($cartItem->unit_price, 2),
                    'total_price' => number_format($cartItem->total_price, 2),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:100'
        ]);

        try {
            $cart = $this->getOrCreateCart();
            $item = $cart->items()->findOrFail($itemId);

            if ($request->quantity == 0) {
                $item->delete();
                $message = 'Item removed from cart';
            } else {
                // Check stock
                if ($item->product->manage_stock && $item->product->stock_quantity < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock. Only ' . $item->product->stock_quantity . ' items available.'
                    ], 400);
                }

                $item->setQuantity($request->quantity);
                $message = 'Cart updated successfully';
            }

            $cart->calculateTotals();

            return response()->json([
                'success' => true,
                'message' => $message,
                'cart' => [
                    'total_items' => $cart->total_items,
                    'subtotal' => number_format($cart->subtotal, 2),
                    'total_amount' => number_format($cart->total_amount, 2),
                ],
                'item' => $request->quantity > 0 ? [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'total_price' => number_format($item->total_price, 2),
                ] : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(int $itemId): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart();
            $item = $cart->items()->findOrFail($itemId);

            $productName = $item->product_name;
            $item->delete();

            $cart->calculateTotals();

            return response()->json([
                'success' => true,
                'message' => $productName . ' removed from cart',
                'cart' => [
                    'total_items' => $cart->total_items,
                    'subtotal' => number_format($cart->subtotal, 2),
                    'total_amount' => number_format($cart->total_amount, 2),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->clear();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart' => [
                    'total_items' => 0,
                    'subtotal' => '0.00',
                    'total_amount' => '0.00',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart data for AJAX requests
     */
    public function data(): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->load(['items.product']);

            return response()->json([
                'success' => true,
                'cart' => [
                    'id' => $cart->id,
                    'total_items' => $cart->total_items,
                    'subtotal' => number_format($cart->subtotal, 2),
                    'tax_amount' => number_format($cart->tax_amount, 2),
                    'shipping_amount' => number_format($cart->shipping_amount, 2),
                    'total_amount' => number_format($cart->total_amount, 2),
                    'items' => $cart->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'product_image' => $item->product_image,
                            'product_slug' => $item->product->slug ?? null,
                            'quantity' => $item->quantity,
                            'unit_price' => number_format($item->unit_price, 2),
                            'sale_price' => $item->sale_price ? number_format($item->sale_price, 2) : null,
                            'total_price' => number_format($item->total_price, 2),
                            'is_on_sale' => $item->is_on_sale,
                            'savings' => number_format($item->savings, 2),
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cart data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count for header display
     */
    public function count(): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart();

            return response()->json([
                'success' => true,
                'count' => $cart->total_items
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Apply coupon code
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        // TODO: Implement coupon system
        return response()->json([
            'success' => false,
            'message' => 'Coupon system not implemented yet'
        ], 501);
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
     * Validate cart items (check availability, prices, etc.)
     */
    public function validateCartItems(): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart();
            $cart->load(['items.product']);

            $issues = [];
            $hasChanges = false;

            foreach ($cart->items as $item) {
                $availability = $item->getAvailabilityStatus();

                if (!$availability['available']) {
                    $issues[] = [
                        'item_id' => $item->id,
                        'product_name' => $item->product_name,
                        'issue' => $availability['message'],
                        'type' => $availability['type']
                    ];

                    if ($availability['type'] === 'error') {
                        // Remove unavailable items
                        $item->delete();
                        $hasChanges = true;
                    } elseif (isset($availability['max_quantity'])) {
                        // Adjust quantity to available stock
                        $item->setQuantity($availability['max_quantity']);
                        $hasChanges = true;
                    }
                }

                // Check for price changes
                $currentPrice = $item->product->is_on_sale && $item->product->sale_price
                    ? $item->product->sale_price
                    : $item->product->price;

                if ($item->unit_price != $item->product->price ||
                    ($item->sale_price ?? 0) != ($item->product->sale_price ?? 0)) {

                    $issues[] = [
                        'item_id' => $item->id,
                        'product_name' => $item->product_name,
                        'issue' => 'Price has changed',
                        'type' => 'info',
                        'old_price' => number_format($item->effective_price, 2),
                        'new_price' => number_format($currentPrice, 2)
                    ];

                    // Update to current prices
                    $item->syncWithProduct();
                    $hasChanges = true;
                }
            }

            if ($hasChanges) {
                $cart->calculateTotals();
            }

            return response()->json([
                'success' => true,
                'has_issues' => count($issues) > 0,
                'has_changes' => $hasChanges,
                'issues' => $issues,
                'cart' => [
                    'total_items' => $cart->total_items,
                    'subtotal' => number_format($cart->subtotal, 2),
                    'total_amount' => number_format($cart->total_amount, 2),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate cart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redirect to checkout
     */
    public function checkout(): RedirectResponse
    {
        $cart = $this->getOrCreateCart();

        if ($cart->isEmpty()) {
            return redirect()->route('marketplace.cart.index')
                ->with('error', 'Your cart is empty');
        }

        return redirect()->route('marketplace.checkout.index');
    }
}
