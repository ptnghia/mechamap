<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use App\Notifications\ProductApprovalNotification;
use App\Services\UnifiedMarketplacePermissionService;
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
            'product_type' => 'required|in:digital,new_product,used_product',
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'industry_category' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'technical_specs' => 'nullable|array',
            'file_formats' => 'nullable|string',
            'software_compatibility' => 'nullable|string',
            'digital_files' => 'nullable|array',
            'digital_files.*' => 'file|mimes:dwg,dxf,step,stp,iges,igs,stl,pdf,doc,docx,zip,rar|max:51200', // 50MB
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,pending,approved,rejected',
        ]);

        // Handle digital files upload for digital products
        $digitalFilesData = [];
        if ($request->hasFile('digital_files') && ($validated['product_type'] === 'digital' || $validated['seller_type'] === 'manufacturer')) {
            foreach ($request->file('digital_files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('marketplace/digital-files', $fileName, 'private');

                $digitalFilesData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

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

        // Process file formats and software compatibility
        if (!empty($validated['file_formats'])) {
            $validated['file_formats'] = array_map('trim', explode(',', $validated['file_formats']));
        }

        if (!empty($validated['software_compatibility'])) {
            $validated['software_compatibility'] = array_map('trim', explode(',', $validated['software_compatibility']));
        }

        // Add digital files data
        if (!empty($digitalFilesData)) {
            $validated['digital_files'] = $digitalFilesData;

            // Calculate total file size
            $totalSize = array_sum(array_column($digitalFilesData, 'size'));
            $validated['file_size_mb'] = round($totalSize / (1024 * 1024), 2);
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
            'product_type' => 'required|in:digital,new_product,used_product',
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
     * Display pending products for approval
     */
    public function pending(Request $request)
    {
        $query = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'pending');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhereHas('seller.user', function ($sq) use ($request) {
                      $sq->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('seller', function ($sq) use ($request) {
                      $sq->where('business_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('created_filter')) {
            switch ($request->created_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }

        $products = $query->orderBy('created_at', 'asc')->paginate(20);

        // Statistics
        $pendingCount = MarketplaceProduct::where('status', 'pending')->count();
        $approvedToday = MarketplaceProduct::where('status', 'approved')
            ->whereDate('approved_at', today())->count();
        $rejectedToday = MarketplaceProduct::where('status', 'rejected')
            ->whereDate('updated_at', today())->count();

        // Calculate average approval time
        $avgApprovalTime = MarketplaceProduct::where('status', 'approved')
            ->whereNotNull('approved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->value('avg_hours');
        $avgApprovalTime = round($avgApprovalTime ?? 0, 1);

        return view('admin.marketplace.products.pending', compact(
            'products', 'pendingCount', 'approvedToday', 'rejectedToday', 'avgApprovalTime'
        ));
    }

    /**
     * Approve a product
     */
    public function approve(MarketplaceProduct $product)
    {
        if ($product->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không ở trạng thái chờ duyệt'
            ], 400);
        }

        $product->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => Auth::guard('admin')->id(),
            'rejection_reason' => null,
        ]);

        // Update seller stats
        $product->seller->increment('active_products');

        // Send notification to seller
        $product->seller->user->notify(new ProductApprovalNotification($product, 'approved'));

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được duyệt thành công'
        ]);
    }

    /**
     * Reject a product
     */
    public function reject(Request $request, MarketplaceProduct $product)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($product->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không ở trạng thái chờ duyệt'
            ], 400);
        }

        $product->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        // Send notification to seller
        $product->seller->user->notify(new ProductApprovalNotification($product, 'rejected', $request->rejection_reason));

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được từ chối'
        ]);
    }

    /**
     * Bulk approve products
     */
    public function bulkApprove(Request $request)
    {
        $productIds = $request->input('product_ids', []);

        $approvedCount = MarketplaceProduct::whereIn('id', $productIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => Auth::guard('admin')->id(),
                'rejection_reason' => null,
            ]);

        // Update seller stats and send notifications
        $products = MarketplaceProduct::with('seller.user')->whereIn('id', $productIds)->get();
        foreach ($products->groupBy('seller_id') as $sellerId => $sellerProducts) {
            MarketplaceSeller::find($sellerId)->increment('active_products', $sellerProducts->count());

            // Send notification to each seller
            foreach ($sellerProducts as $product) {
                $product->seller->user->notify(new ProductApprovalNotification($product, 'approved'));
            }
        }

        return response()->json([
            'success' => true,
            'approved_count' => $approvedCount,
            'message' => "Đã duyệt {$approvedCount} sản phẩm thành công!"
        ]);
    }

    /**
     * Bulk reject products
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $productIds = $request->input('product_ids', []);
        $reason = $request->input('rejection_reason');

        // Get products before updating
        $products = MarketplaceProduct::with('seller.user')
            ->whereIn('id', $productIds)
            ->where('status', 'pending')
            ->get();

        $rejectedCount = MarketplaceProduct::whereIn('id', $productIds)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'is_active' => false,
                'rejection_reason' => $reason,
                'approved_at' => null,
                'approved_by' => null,
            ]);

        // Send notifications
        foreach ($products as $product) {
            $product->seller->user->notify(new ProductApprovalNotification($product, 'rejected', $reason));
        }

        return response()->json([
            'success' => true,
            'rejected_count' => $rejectedCount,
            'message' => "Đã từ chối {$rejectedCount} sản phẩm!"
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
