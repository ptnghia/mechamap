<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TechnicalProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of products for marketplace
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = TechnicalProduct::with(['seller', 'category'])
                ->where('status', 'approved');

            // Filter by seller type
            if ($request->has('seller_type')) {
                $sellerType = $request->seller_type;
                $query->whereHas('seller', function($q) use ($sellerType) {
                    $q->where('role', $sellerType);
                });
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search by title/description
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('keywords', 'like', "%{$search}%");
                });
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by featured
            if ($request->has('featured') && $request->featured) {
                $query->where('is_featured', true);
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            switch ($sortBy) {
                case 'price':
                    $query->orderBy('price', $sortOrder);
                    break;
                case 'rating':
                    $query->orderBy('rating_average', $sortOrder);
                    break;
                case 'popularity':
                    $query->orderBy('view_count', $sortOrder);
                    break;
                case 'sales':
                    $query->orderBy('sales_count', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            $perPage = min($request->get('per_page', 12), 50);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products,
                'filters' => [
                    'seller_types' => ['supplier', 'manufacturer', 'brand'],
                    'sort_options' => ['created_at', 'price', 'rating', 'popularity', 'sales']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product (for business users)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Check if user can create products
            if (!in_array($user->role, ['supplier', 'manufacturer', 'brand'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only business users can create products'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:product_categories,id',
                'file_formats' => 'array',
                'software_compatibility' => 'string|nullable',
                'complexity_level' => 'in:beginner,intermediate,advanced',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = TechnicalProduct::create([
                'title' => $request->title,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'seller_id' => $user->id,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'discount_percentage' => $request->discount_percentage ?? 0,
                'currency' => 'VND',
                'file_formats' => $request->file_formats,
                'software_compatibility' => $request->software_compatibility,
                'complexity_level' => $request->complexity_level ?? 'intermediate',
                'keywords' => $request->keywords,
                'status' => $user->role === 'brand' ? 'approved' : 'pending', // Brands auto-approved
                'is_featured' => false,
                'published_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load(['seller', 'category'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = TechnicalProduct::with(['seller', 'category'])
                ->where('status', 'approved')
                ->findOrFail($id);

            // Increment view count
            $product->increment('view_count');

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified product (only by seller)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $product = TechnicalProduct::findOrFail($id);

            // Check ownership
            if ($product->seller_id !== $user->id && !in_array($user->role, ['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own products'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'description' => 'string',
                'price' => 'numeric|min:0',
                'category_id' => 'exists:product_categories,id',
                'file_formats' => 'array',
                'software_compatibility' => 'string|nullable',
                'complexity_level' => 'in:beginner,intermediate,advanced',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product->update($request->only([
                'title', 'description', 'short_description', 'price', 'discount_percentage',
                'category_id', 'file_formats', 'software_compatibility', 'complexity_level', 'keywords'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load(['seller', 'category'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product (soft delete)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $product = TechnicalProduct::findOrFail($id);

            // Check ownership
            if ($product->seller_id !== $user->id && !in_array($user->role, ['admin', 'moderator'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own products'
                ], 403);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products by seller type for marketplace sections
     */
    public function bySellerType(string $sellerType): JsonResponse
    {
        try {
            if (!in_array($sellerType, ['supplier', 'manufacturer', 'brand'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid seller type'
                ], 400);
            }

            $products = TechnicalProduct::with(['seller', 'category'])
                ->where('status', 'approved')
                ->whereHas('seller', function($q) use ($sellerType) {
                    $q->where('role', $sellerType);
                })
                ->orderBy('is_featured', 'desc')
                ->orderBy('rating_average', 'desc')
                ->paginate(12);

            return response()->json([
                'success' => true,
                'message' => "Products from {$sellerType}s retrieved successfully",
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
