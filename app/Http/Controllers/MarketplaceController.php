<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceProduct;
use App\Models\ProductCategory;
use App\Models\MarketplaceSeller;
use App\Services\MarketplacePermissionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Display marketplace homepage with featured products
     */
    public function index(Request $request): View
    {
        // Get featured products
        $featuredProducts = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('featured_at', 'desc')
            ->limit(8)
            ->get();

        // Get latest products
        $latestProducts = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Get categories with product counts
        $categories = ProductCategory::withCount(['marketplaceProducts' => function($query) {
            $query->where('status', 'approved')->where('is_active', true);
        }])
        ->having('marketplace_products_count', '>', 0)
        ->orderBy('marketplace_products_count', 'desc')
        ->limit(8)
        ->get();

        // Get top sellers
        $topSellers = MarketplaceSeller::with('user')
            ->where('status', 'active')
            ->where('verification_status', 'verified')
            ->orderBy('total_sales', 'desc')
            ->limit(6)
            ->get();

        // Marketplace stats
        $stats = [
            'total_products' => MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count(),
            'total_sellers' => MarketplaceSeller::where('status', 'active')->count(),
            'total_categories' => ProductCategory::whereHas('marketplaceProducts', function($query) {
                $query->where('status', 'approved')->where('is_active', true);
            })->count(),
        ];

        return view('marketplace.index', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'topSellers',
            'stats'
        ));
    }

    /**
     * Display products listing with filters and search
     */
    public function products(Request $request): View
    {
        $query = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true);

        // Advanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Check for exact phrases (quoted text)
                if (preg_match('/"([^"]+)"/', $search, $matches)) {
                    $exactPhrase = $matches[1];
                    $q->where('name', 'like', "%{$exactPhrase}%")
                      ->orWhere('description', 'like', "%{$exactPhrase}%")
                      ->orWhere('short_description', 'like', "%{$exactPhrase}%");
                } else {
                    // Split search terms and search each
                    $terms = explode(' ', $search);
                    foreach ($terms as $term) {
                        $term = trim($term);
                        if (!empty($term)) {
                            if (str_starts_with($term, '+')) {
                                // Required term
                                $requiredTerm = substr($term, 1);
                                $q->where(function($subQ) use ($requiredTerm) {
                                    $subQ->where('name', 'like', "%{$requiredTerm}%")
                                         ->orWhere('description', 'like', "%{$requiredTerm}%")
                                         ->orWhere('short_description', 'like', "%{$requiredTerm}%")
                                         ->orWhere('technical_specs', 'like', "%{$requiredTerm}%");
                                });
                            } elseif (str_starts_with($term, '-')) {
                                // Excluded term
                                $excludedTerm = substr($term, 1);
                                $q->where(function($subQ) use ($excludedTerm) {
                                    $subQ->where('name', 'not like', "%{$excludedTerm}%")
                                         ->where('description', 'not like', "%{$excludedTerm}%")
                                         ->where('short_description', 'not like', "%{$excludedTerm}%");
                                });
                            } else {
                                // Regular term
                                $q->orWhere('name', 'like', "%{$term}%")
                                  ->orWhere('description', 'like', "%{$term}%")
                                  ->orWhere('short_description', 'like', "%{$term}%")
                                  ->orWhere('technical_specs', 'like', "%{$term}%")
                                  ->orWhere('sku', 'like', "%{$term}%");
                            }
                        }
                    }
                }
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by seller type
        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        // Filter by product type
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $query->where('rating_average', '>=', $request->min_rating);
        }

        // Filter by material
        if ($request->filled('material')) {
            $query->where('material', $request->material);
        }

        // Filter by file format
        if ($request->filled('file_format')) {
            $query->where('file_formats', 'like', '%"' . $request->file_format . '"%');
        }

        // Filter by availability
        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        // Filter by featured products
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        // Filter by products on sale
        if ($request->filled('on_sale')) {
            $query->where('is_on_sale', true);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 50);
        $products = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $categories = ProductCategory::whereHas('marketplaceProducts', function($query) {
            $query->where('status', 'approved')->where('is_active', true);
        })->orderBy('name')->get();

        $priceRanges = [
            ['label' => 'Under $25', 'min' => 0, 'max' => 25],
            ['label' => '$25 - $100', 'min' => 25, 'max' => 100],
            ['label' => '$100 - $500', 'min' => 100, 'max' => 500],
            ['label' => '$500 - $1000', 'min' => 500, 'max' => 1000],
            ['label' => 'Over $1000', 'min' => 1000, 'max' => null],
        ];

        return view('marketplace.products.index', compact(
            'products',
            'categories',
            'priceRanges'
        ));
    }

    /**
     * Display single product details
     */
    public function show(string $slug): View
    {
        $product = MarketplaceProduct::with([
            'seller.user',
            'category',
            'orderItems.order'
        ])
        ->where('slug', $slug)
        ->where('status', 'approved')
        ->where('is_active', true)
        ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        // Get related products
        $relatedProducts = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                $query->where('product_category_id', $product->product_category_id)
                      ->orWhere('seller_id', $product->seller_id);
            })
            ->orderBy('rating_average', 'desc')
            ->limit(6)
            ->get();

        // Check if user has purchased this product
        $hasPurchased = false;
        if (auth()->check()) {
            $hasPurchased = $product->orderItems()
                ->whereHas('order', function($query) {
                    $query->where('customer_id', auth()->id())
                          ->where('status', 'completed');
                })
                ->exists();
        }

        return view('marketplace.products.show', compact(
            'product',
            'relatedProducts',
            'hasPurchased'
        ));
    }

    /**
     * Display seller profile and products
     */
    public function seller(string $slug): View
    {
        $seller = MarketplaceSeller::with('user')
            ->where('store_slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $products = MarketplaceProduct::with('category')
            ->where('seller_id', $seller->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketplace.sellers.show', compact('seller', 'products'));
    }

    /**
     * Display all categories
     */
    public function categories(): View
    {
        $categories = ProductCategory::withCount(['marketplaceProducts' => function($query) {
            $query->where('status', 'approved')->where('is_active', true);
        }])
        ->with(['children', 'marketplaceProducts' => function($query) {
            $query->where('status', 'approved')->where('is_active', true)->take(3);
        }])
        ->where('is_active', true)
        ->orderBy('name')
        ->paginate(12);

        // Featured categories (categories with high product count)
        $featuredCategories = ProductCategory::withCount(['marketplaceProducts' => function($query) {
            $query->where('status', 'approved')->where('is_active', true);
        }])
        ->where('is_active', true)
        ->whereHas('marketplaceProducts', function($query) {
            $query->where('status', 'approved')->where('is_active', true);
        }, '>=', 3)
        ->orderBy('marketplace_products_count', 'desc')
        ->take(6)
        ->get();

        // Calculate stats
        $totalCategories = ProductCategory::where('is_active', true)->count();
        $totalProducts = MarketplaceProduct::where('status', 'approved')->where('is_active', true)->count();
        $totalSellers = MarketplaceSeller::where('status', 'active')->count();
        $activeCategories = ProductCategory::where('is_active', true)
            ->whereHas('marketplaceProducts', function($query) {
                $query->where('status', 'approved')->where('is_active', true);
            })->count();
        $activeSellers = MarketplaceSeller::where('status', 'active')->where('verification_status', 'verified')->count();

        // New products this week
        $newThisWeek = MarketplaceProduct::where('status', 'approved')
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        return view('marketplace.categories.index', compact(
            'categories',
            'featuredCategories',
            'totalCategories',
            'totalProducts',
            'totalSellers',
            'activeCategories',
            'activeSellers',
            'newThisWeek'
        ));
    }

    /**
     * Display products by category
     */
    public function category(string $slug): View
    {
        $category = ProductCategory::with(['children' => function($query) {
            $query->where('is_active', true)
                  ->withCount(['marketplaceProducts' => function($subQuery) {
                      $subQuery->where('status', 'approved')->where('is_active', true);
                  }]);
        }])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        $products = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('product_category_id', $category->id)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketplace.categories.show', compact('category', 'products'));
    }
}
