<?php

namespace App\Http\Controllers;

use App\Models\TechnicalProduct;
use App\Models\MarketplaceWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class MarketplaceWishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's wishlist
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = MarketplaceWishlist::where('user_id', $user->id)
            ->with(['product.seller.user', 'product.category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $wishlistItems = $query->paginate(12)->withQueryString();

        return view('marketplace.wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:technical_products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        // Check if already in wishlist
        $existingItem = MarketplaceWishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist'
            ]);
        }

        // Add to wishlist
        MarketplaceWishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully'
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:technical_products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $deleted = MarketplaceWishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist'
        ]);
    }

    /**
     * Toggle product in wishlist
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:technical_products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $existingItem = MarketplaceWishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            $existingItem->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Product removed from wishlist'
            ]);
        } else {
            MarketplaceWishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId
            ]);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Product added to wishlist'
            ]);
        }
    }

    /**
     * Clear entire wishlist
     */
    public function clear(): JsonResponse
    {
        $user = Auth::user();

        $deleted = MarketplaceWishlist::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Removed {$deleted} items from wishlist"
        ]);
    }

    /**
     * Get wishlist count for user
     */
    public function count(): JsonResponse
    {
        $user = Auth::user();
        $count = MarketplaceWishlist::where('user_id', $user->id)->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Move wishlist items to cart
     */
    public function moveToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:technical_products,id'
        ]);

        $user = Auth::user();
        $productIds = $request->product_ids;
        $cartController = new MarketplaceCartController();

        $addedItems = 0;
        $errors = [];

        foreach ($productIds as $productId) {
            try {
                // Get product details
                $product = TechnicalProduct::find($productId);
                if (!$product || $product->status !== 'active') {
                    $productName = $product ? $product->name : 'Unknown';
                    $errors[] = "Product {$productName} is not available";
                    continue;
                }

                // Add to cart
                $addRequest = new Request([
                    'product_id' => $productId,
                    'quantity' => 1,
                    'seller_id' => $product->seller_id
                ]);

                $result = $cartController->add($addRequest);
                if ($result->getStatusCode() === 200) {
                    $addedItems++;

                    // Remove from wishlist
                    MarketplaceWishlist::where('user_id', $user->id)
                        ->where('product_id', $productId)
                        ->delete();
                }
            } catch (\Exception $e) {
                $errors[] = "Error adding product to cart: " . $e->getMessage();
            }
        }

        $message = "Added {$addedItems} items to cart";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', $errors);
        }

        return response()->json([
            'success' => $addedItems > 0,
            'message' => $message,
            'added_items' => $addedItems,
            'errors' => $errors
        ]);
    }
}
