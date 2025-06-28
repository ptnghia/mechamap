<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCart;
use App\Models\TechnicalProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{
    /**
     * Get user's shopping cart
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();

            $cartItems = ShoppingCart::with(['technicalProduct.seller', 'technicalProduct.category'])
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->get();

            $total = $cartItems->sum('total_price');
            $itemCount = $cartItems->count();

            return response()->json([
                'success' => true,
                'message' => 'Shopping cart retrieved successfully',
                'data' => [
                    'items' => $cartItems,
                    'summary' => [
                        'item_count' => $itemCount,
                        'subtotal' => $total,
                        'tax' => $total * 0.1, // 10% VAT
                        'total' => $total * 1.1
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to cart
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'technical_product_id' => 'required|exists:technical_products,id',
                'license_type' => 'required|in:standard,commercial,extended',
                'quantity' => 'integer|min:1|max:1' // Digital products usually quantity 1
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = TechnicalProduct::where('status', 'approved')
                ->findOrFail($request->technical_product_id);

            // Check if product already in cart
            $existingItem = ShoppingCart::where('user_id', $user->id)
                ->where('technical_product_id', $product->id)
                ->where('license_type', $request->license_type)
                ->where('status', 'active')
                ->first();

            if ($existingItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in cart'
                ], 409);
            }

            // Check if user is trying to buy their own product
            if ($product->seller_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot purchase your own product'
                ], 403);
            }

            $quantity = $request->get('quantity', 1);
            $unitPrice = $product->sale_price ?? $product->price;
            $totalPrice = $unitPrice * $quantity;

            $cartItem = ShoppingCart::create([
                'user_id' => $user->id,
                'technical_product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'license_type' => $request->license_type,
                'product_snapshot' => [
                    'title' => $product->title,
                    'description' => $product->description,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                ],
                'status' => 'active',
                'expires_at' => now()->addDays(7), // Cart expires in 7 days
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => $cartItem->load(['technicalProduct.seller'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:1' // Digital products usually quantity 1
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cartItem = ShoppingCart::where('user_id', $user->id)
                ->where('status', 'active')
                ->findOrFail($id);

            $quantity = $request->quantity;
            $cartItem->update([
                'quantity' => $quantity,
                'total_price' => $cartItem->unit_price * $quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'data' => $cartItem->load(['technicalProduct.seller'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $cartItem = ShoppingCart::where('user_id', $user->id)
                ->where('status', 'active')
                ->findOrFail($id);

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        try {
            $user = Auth::user();

            ShoppingCart::where('user_id', $user->id)
                ->where('status', 'active')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart item count (for header badge)
     */
    public function count(): JsonResponse
    {
        try {
            $user = Auth::user();

            $count = ShoppingCart::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting cart count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
