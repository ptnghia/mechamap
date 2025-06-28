<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketplaceProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketplaceProduct::with(['seller.user', 'category', 'approvedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by seller type
        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        // Filter by product type
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('product_category_id', $request->category_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20)->withQueryString();

        // Get filter options
        $categories = ProductCategory::all();
        $sellers = MarketplaceSeller::with('user')->get();

        return view('admin.marketplace.products.index', compact(
            'products',
            'categories',
            'sellers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::all();
        $sellers = MarketplaceSeller::with('user')->where('status', 'active')->get();

        return view('admin.marketplace.products.create', compact('categories', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'seller_id' => 'required|exists:marketplace_sellers,id',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'product_type' => 'required|in:physical,digital,service',
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'industry_category' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'technical_specs' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,pending,approved,rejected',
        ]);

        // Handle image uploads
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('marketplace/products', 'public');
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('marketplace/products', 'public');
            }
            $validated['images'] = $images;
        }

        // Auto-approve if admin is creating
        if (Auth::guard('admin')->check()) {
            $validated['status'] = 'approved';
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::guard('admin')->id();
        }

        $product = MarketplaceProduct::create($validated);

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketplaceProduct $product)
    {
        $product->load(['seller.user', 'category', 'approvedBy', 'orderItems.order']);

        return view('admin.marketplace.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketplaceProduct $product)
    {
        $categories = ProductCategory::all();
        $sellers = MarketplaceSeller::with('user')->where('status', 'active')->get();

        return view('admin.marketplace.products.edit', compact('product', 'categories', 'sellers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketplaceProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'seller_id' => 'required|exists:marketplace_sellers,id',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'product_type' => 'required|in:physical,digital,service',
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'industry_category' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'technical_specs' => 'nullable|array',
            'status' => 'required|in:draft,pending,approved,rejected,suspended',
            'rejection_reason' => 'nullable|string',
        ]);

        // Handle status changes
        if ($validated['status'] === 'approved' && $product->status !== 'approved') {
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::guard('admin')->id();
        }

        $product->update($validated);

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketplaceProduct $product)
    {
        // Delete associated images
        if ($product->featured_image) {
            Storage::disk('public')->delete($product->featured_image);
        }

        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()
            ->route('admin.marketplace.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    /**
     * Bulk approve products
     */
    public function bulkApprove(Request $request)
    {
        $productIds = $request->input('product_ids', []);

        MarketplaceProduct::whereIn('id', $productIds)
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::guard('admin')->id(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã phê duyệt ' . count($productIds) . ' sản phẩm thành công!'
        ]);
    }

    /**
     * Bulk reject products
     */
    public function bulkReject(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        $reason = $request->input('reason', 'Không đáp ứng tiêu chuẩn chất lượng');

        MarketplaceProduct::whereIn('id', $productIds)
            ->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối ' . count($productIds) . ' sản phẩm!'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(MarketplaceProduct $product)
    {
        $product->update([
            'is_featured' => !$product->is_featured,
            'featured_at' => $product->is_featured ? null : now(),
        ]);

        $status = $product->is_featured ? 'đã được đánh dấu nổi bật' : 'đã bỏ đánh dấu nổi bật';

        return response()->json([
            'success' => true,
            'message' => "Sản phẩm {$status}!",
            'is_featured' => $product->is_featured
        ]);
    }
}
