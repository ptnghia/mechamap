<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalProduct;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of technical products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TechnicalProduct::with(['seller:id,name,avatar', 'category:id,name,slug'])
                ->where('status', 'approved');

            // Apply filters
            $this->applyFilters($query, $request);

            // Apply sorting
            $this->applySorting($query, $request);

            // Paginate results
            $perPage = min($request->input('per_page', 20), 50); // Max 50 items per page
            $products = $query->paginate($perPage);

            // Get available filters for frontend
            $filters = $this->getAvailableFilters();

            return response()->json([
                'success' => true,
                'data' => $products,
                'filters' => $filters,
                'message' => 'Products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching marketplace products', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $product = TechnicalProduct::where('slug', $slug)
                ->where('status', 'approved')
                ->with([
                    'seller:id,name,avatar,created_at',
                    'category:id,name,slug,description',
                    'protectedFiles' => function($query) {
                        $query->where('is_active', true)
                              ->select('id', 'product_id', 'original_filename', 'file_size', 'file_type', 'access_level');
                    }
                ])
                ->firstOrFail();

            // Increment view count (async to avoid blocking)
            dispatch(function () use ($product) {
                $product->incrementViews();
            })->afterResponse();

            // Check if current user already purchased
            $hasPurchased = false;
            if (auth()->check()) {
                $hasPurchased = $product->isPurchasedBy(auth()->user());
            }

            // Get related products
            $relatedProducts = $this->getRelatedProducts($product);

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'has_purchased' => $hasPurchased,
                    'related_products' => $relatedProducts
                ],
                'message' => 'Product details retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error fetching product details', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching product details'
            ], 500);
        }
    }

    /**
     * Get all product categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Cache::remember('marketplace_categories', 3600, function () {
                return ProductCategory::active()
                    ->with(['children' => function($query) {
                        $query->active()->orderBy('sort_order');
                    }])
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching categories', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories'
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'per_page' => 'integer|min:1|max:50'
        ]);

        try {
            $searchTerm = $request->input('q');
            $perPage = $request->input('per_page', 20);

            $products = TechnicalProduct::search($searchTerm)
                ->where('status', 'approved')
                ->with(['seller:id,name,avatar', 'category:id,name,slug'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'search_term' => $searchTerm,
                'message' => "Found {$products->total()} products for '{$searchTerm}'"
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching products', [
                'search_term' => $request->input('q'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error searching products'
            ], 500);
        }
    }

    /**
     * Get featured products
     */
    public function featured(): JsonResponse
    {
        try {
            $products = TechnicalProduct::featured()
                ->approved()
                ->with(['seller:id,name,avatar', 'category:id,name,slug'])
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Featured products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching featured products', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching featured products'
            ], 500);
        }
    }

    /**
     * Get bestseller products
     */
    public function bestsellers(): JsonResponse
    {
        try {
            $products = TechnicalProduct::bestseller()
                ->approved()
                ->with(['seller:id,name,avatar', 'category:id,name,slug'])
                ->orderBy('sales_count', 'desc')
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Bestseller products retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching bestsellers', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching bestsellers'
            ], 500);
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request): void
    {
        // Category filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->has('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->has('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Software compatibility filter
        if ($request->has('software')) {
            $software = is_array($request->software) ? $request->software : [$request->software];
            foreach ($software as $soft) {
                $query->whereJsonContains('software_compatibility', $soft);
            }
        }

        // Complexity level filter
        if ($request->has('complexity')) {
            $query->where('complexity_level', $request->complexity);
        }

        // File format filter
        if ($request->has('format')) {
            $formats = is_array($request->format) ? $request->format : [$request->format];
            foreach ($formats as $format) {
                $query->whereJsonContains('file_formats', $format);
            }
        }

        // Free products filter
        if ($request->boolean('free_only')) {
            $query->where('price', '<=', 0);
        }

        // Rating filter
        if ($request->has('min_rating')) {
            $query->where('rating_average', '>=', $request->min_rating);
        }
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, Request $request): void
    {
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'popularity':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc')
                      ->orderBy('rating_count', 'desc');
                break;
            case 'views':
                $query->orderBy('view_count', 'desc');
                break;
            case 'name':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
    }

    /**
     * Get available filters for frontend
     */
    private function getAvailableFilters(): array
    {
        return Cache::remember('marketplace_filters', 1800, function () {
            return [
                'categories' => ProductCategory::active()
                    ->select('id', 'name', 'slug')
                    ->orderBy('name')
                    ->get(),
                'complexity_levels' => ['beginner', 'intermediate', 'advanced'],
                'software_options' => ['SolidWorks', 'AutoCAD', 'Fusion 360', 'Inventor', 'ANSYS'],
                'file_formats' => ['dwg', 'step', 'iges', 'stl', 'pdf', 'docx'],
                'price_ranges' => [
                    ['label' => 'Free', 'min' => 0, 'max' => 0],
                    ['label' => '$1 - $25', 'min' => 1, 'max' => 25],
                    ['label' => '$25 - $100', 'min' => 25, 'max' => 100],
                    ['label' => '$100 - $500', 'min' => 100, 'max' => 500],
                    ['label' => '$500+', 'min' => 500, 'max' => null],
                ]
            ];
        });
    }

    /**
     * Get related products
     */
    private function getRelatedProducts(TechnicalProduct $product): \Illuminate\Database\Eloquent\Collection
    {
        return TechnicalProduct::approved()
            ->where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhereJsonOverlaps('tags', $product->tags ?? [])
                      ->orWhere('complexity_level', $product->complexity_level);
            })
            ->with(['seller:id,name,avatar', 'category:id,name,slug'])
            ->orderBy('rating_average', 'desc')
            ->limit(6)
            ->get();
    }
}
