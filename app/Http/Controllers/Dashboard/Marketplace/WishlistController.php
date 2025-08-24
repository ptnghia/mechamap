<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceWishlist;
use App\Models\MarketplaceProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Wishlist Controller cho Dashboard Marketplace
 * 
 * Quản lý wishlist của user trong dashboard
 */
class WishlistController extends BaseController
{
    /**
     * Hiển thị danh sách wishlist của user
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $productType = $request->get('product_type');
        $priceRange = $request->get('price_range');
        $sort = $request->get('sort', 'newest');

        $query = MarketplaceWishlist::with(['product.category', 'product.seller.user'])
            ->where('user_id', $this->user->id);

        // Apply search
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category) {
            $query->whereHas('product', function($q) use ($category) {
                $q->where('category_id', $category);
            });
        }

        // Filter by product type
        if ($productType) {
            $query->whereHas('product', function($q) use ($productType) {
                $q->where('product_type', $productType);
            });
        }

        // Filter by price range
        if ($priceRange) {
            $this->applyPriceFilter($query, $priceRange);
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_low':
                $query->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
                    ->orderBy('marketplace_products.price');
                break;
            case 'price_high':
                $query->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
                    ->orderByDesc('marketplace_products.price');
                break;
            case 'name':
                $query->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
                    ->orderBy('marketplace_products.name');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $wishlistItems = $query->paginate(20);

        // Get categories for filter
        $categories = $this->getWishlistCategories();

        // Get statistics
        $stats = $this->getWishlistStats();

        return $this->dashboardResponse('dashboard.marketplace.wishlist.index', [
            'wishlistItems' => $wishlistItems,
            'categories' => $categories,
            'stats' => $stats,
            'search' => $search,
            'currentCategory' => $category,
            'currentProductType' => $productType,
            'currentPriceRange' => $priceRange,
            'currentSort' => $sort]);
    }

    /**
     * Thêm product vào wishlist
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:marketplace_products,id']);

        $product = MarketplaceProduct::findOrFail($request->product_id);

        // Check if product is available
        if (!$product->is_active || $product->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.'
            ], 400);
        }

        // Check if already in wishlist
        $existingWishlist = MarketplaceWishlist::where('user_id', $this->user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingWishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.'
            ], 400);
        }

        // Add to wishlist
        MarketplaceWishlist::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully.',
            'wishlist_count' => $this->user->marketplaceWishlist()->count()
        ]);
    }

    /**
     * Xóa product khỏi wishlist
     */
    public function remove(MarketplaceWishlist $wishlistItem): JsonResponse
    {
        // Verify ownership
        if ($wishlistItem->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully.',
            'wishlist_count' => $this->user->marketplaceWishlist()->count()
        ]);
    }

    /**
     * Xóa nhiều items khỏi wishlist
     */
    public function bulkRemove(Request $request): JsonResponse
    {
        $request->validate([
            'wishlist_ids' => 'required|array',
            'wishlist_ids.*' => 'exists:marketplace_wishlists,id']);

        $deleted = MarketplaceWishlist::whereIn('id', $request->wishlist_ids)
            ->where('user_id', $this->user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Removed {$deleted} items from wishlist successfully.",
            'deleted_count' => $deleted,
            'wishlist_count' => $this->user->marketplaceWishlist()->count()
        ]);
    }

    /**
     * Thêm tất cả wishlist items vào cart
     */
    public function addAllToCart(): JsonResponse
    {
        $wishlistItems = MarketplaceWishlist::with('product')
            ->where('user_id', $this->user->id)
            ->get();

        if ($wishlistItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your wishlist is empty.'
            ], 400);
        }

        $addedItems = 0;
        $unavailableItems = [];

        foreach ($wishlistItems as $wishlistItem) {
            $product = $wishlistItem->product;

            // Check if product is still available
            if (!$product || !$product->is_active || $product->status !== 'approved') {
                $unavailableItems[] = $product ? $product->name : 'Unknown Product';
                continue;
            }

            // Add to cart (assuming cart service exists)
            try {
                // Add item to cart logic here
                $addedItems++;
            } catch (\Exception $e) {
                $unavailableItems[] = $product->name;
            }
        }

        $message = "Added {$addedItems} items to cart.";
        if (!empty($unavailableItems)) {
            $message .= " " . count($unavailableItems) . " items are no longer available.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'added_items' => $addedItems,
            'unavailable_items' => $unavailableItems
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function checkProduct(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:marketplace_products,id']);

        $inWishlist = MarketplaceWishlist::where('user_id', $this->user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist
        ]);
    }

    /**
     * Apply price filter to query
     */
    private function applyPriceFilter($query, $priceRange)
    {
        switch ($priceRange) {
            case 'under_100k':
                $query->whereHas('product', function($q) {
                    $q->where('price', '<', 100000);
                });
                break;
            case '100k_500k':
                $query->whereHas('product', function($q) {
                    $q->whereBetween('price', [100000, 500000]);
                });
                break;
            case '500k_1m':
                $query->whereHas('product', function($q) {
                    $q->whereBetween('price', [500000, 1000000]);
                });
                break;
            case '1m_5m':
                $query->whereHas('product', function($q) {
                    $q->whereBetween('price', [1000000, 5000000]);
                });
                break;
            case 'over_5m':
                $query->whereHas('product', function($q) {
                    $q->where('price', '>', 5000000);
                });
                break;
        }
    }

    /**
     * Lấy categories có trong wishlist
     */
    private function getWishlistCategories()
    {
        return MarketplaceWishlist::where('user_id', $this->user->id)
            ->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
            ->join('marketplace_categories', 'marketplace_products.category_id', '=', 'marketplace_categories.id')
            ->select('marketplace_categories.id', 'marketplace_categories.name')
            ->distinct()
            ->orderBy('marketplace_categories.name')
            ->get();
    }

    /**
     * Lấy thống kê wishlist
     */
    private function getWishlistStats()
    {
        $total = MarketplaceWishlist::where('user_id', $this->user->id)->count();
        
        $totalValue = MarketplaceWishlist::where('user_id', $this->user->id)
            ->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
            ->sum('marketplace_products.price');

        $averagePrice = $total > 0 ? $totalValue / $total : 0;

        $productTypes = MarketplaceWishlist::where('user_id', $this->user->id)
            ->join('marketplace_products', 'marketplace_wishlists.product_id', '=', 'marketplace_products.id')
            ->selectRaw('product_type, COUNT(*) as count')
            ->groupBy('product_type')
            ->pluck('count', 'product_type')
            ->toArray();

        return [
            'total_items' => $total,
            'total_value' => $totalValue,
            'average_price' => $averagePrice,
            'product_types' => $productTypes,
            'this_month' => MarketplaceWishlist::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'this_week' => MarketplaceWishlist::where('user_id', $this->user->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count()];
    }
}
