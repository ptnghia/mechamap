<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MarketplaceSearchController extends Controller
{
    /**
     * Get search suggestions for marketplace
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => []
            ]);
        }

        // Search products
        $products = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => number_format($product->price, 2),
                    'product_type' => ucfirst($product->product_type),
                    'featured_image' => $product->featured_image,
                    'seller_name' => $product->seller->business_name ?? $product->seller->user->name,
                ];
            });

        // Search categories
        $categories = ProductCategory::whereHas('marketplaceProducts', function($q) {
                $q->where('status', 'approved')->where('is_active', true);
            })
            ->where('name', 'like', "%{$query}%")
            ->withCount(['marketplaceProducts' => function($q) {
                $q->where('status', 'approved')->where('is_active', true);
            }])
            ->orderBy('marketplace_products_count', 'desc')
            ->limit(3)
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->marketplace_products_count,
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * Get popular search terms
     */
    public function popular(): JsonResponse
    {
        // This could be enhanced with actual search analytics
        $popularTerms = [
            'ball bearing',
            'gear assembly', 
            'CAD files',
            'aluminum parts',
            'hydraulic cylinder',
            'pneumatic valve',
            'stainless steel',
            'precision tools'
        ];

        return response()->json([
            'terms' => $popularTerms
        ]);
    }

    /**
     * Advanced search with filters
     */
    public function search(Request $request): JsonResponse
    {
        $query = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'approved')
            ->where('is_active', true);

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('technical_specs', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('material')) {
            $query->where('material', $request->material);
        }

        if ($request->filled('in_stock')) {
            $query->where('in_stock', true);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('on_sale')) {
            $query->where('is_on_sale', true);
        }

        // Sorting
        $sortBy = $request->get('sort', 'relevance');
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
            case 'relevance':
            default:
                // For relevance, we could implement a scoring system
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('rating_average', 'desc')
                      ->orderBy('view_count', 'desc');
                break;
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 50);
        $products = $query->paginate($perPage);

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    /**
     * Get search filters data
     */
    public function filters(): JsonResponse
    {
        $categories = ProductCategory::whereHas('marketplaceProducts', function($q) {
                $q->where('status', 'approved')->where('is_active', true);
            })
            ->withCount(['marketplaceProducts' => function($q) {
                $q->where('status', 'approved')->where('is_active', true);
            }])
            ->orderBy('name')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => $category->marketplace_products_count,
                ];
            });

        $materials = MarketplaceProduct::where('status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('material')
            ->distinct()
            ->pluck('material')
            ->filter()
            ->sort()
            ->values();

        $priceRanges = [
            ['label' => 'Under $25', 'min' => 0, 'max' => 25],
            ['label' => '$25 - $100', 'min' => 25, 'max' => 100],
            ['label' => '$100 - $500', 'min' => 100, 'max' => 500],
            ['label' => '$500 - $1000', 'min' => 500, 'max' => 1000],
            ['label' => 'Over $1000', 'min' => 1000, 'max' => null],
        ];

        return response()->json([
            'categories' => $categories,
            'materials' => $materials,
            'price_ranges' => $priceRanges,
            'product_types' => ['physical', 'digital', 'service'],
            'seller_types' => ['supplier', 'manufacturer', 'brand'],
        ]);
    }
}
