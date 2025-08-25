<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Product Controller cho Dashboard Marketplace
 *
 * Quản lý products của seller trong dashboard
 */
class ProductController extends BaseController
{
    /**
     * Hiển thị danh sách products của seller
     */
    public function index(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('info', 'Please complete your seller setup first.');
        }

        $search = $request->get('search');
        $status = $request->get('status');
        $category = $request->get('category');
        $productType = $request->get('product_type');
        $sort = $request->get('sort', 'newest');

        $query = MarketplaceProduct::where('seller_id', $seller->id)
            ->with(['category']);

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status) {
            if ($status === 'active') {
                $query->where('is_active', true)->where('status', 'approved');
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by category
        if ($category) {
            $query->where('category_id', $category);
        }

        // Filter by product type
        if ($productType) {
            $query->where('product_type', $productType);
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'sales':
                $query->orderByDesc('sales_count');
                break;
            case 'views':
                $query->orderByDesc('view_count');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(20);

        // Get categories for filter
        $categories = ProductCategory::orderBy('name')->get();

        // Get statistics
        $stats = $this->getProductStats($seller);

        return $this->dashboardResponse('dashboard.marketplace.products.index', [
            'seller' => $seller,
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'search' => $search,
            'currentStatus' => $status,
            'currentCategory' => $category,
            'currentProductType' => $productType,
            'currentSort' => $sort]);
    }

    /**
     * Hiển thị form tạo product mới
     */
    public function create()
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup');
        }

        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();

        return $this->dashboardResponse('dashboard.marketplace.products.create', [
            'seller' => $seller,
            'categories' => $categories]);
    }

    /**
     * Hiển thị form tạo product chuyên nghiệp
     */
    public function createAdvanced()
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('info', 'Vui lòng hoàn thành thiết lập seller trước.');
        }

        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();

        return $this->dashboardResponse('dashboard.marketplace.products.create-advanced', [
            'seller' => $seller,
            'categories' => $categories]);
    }

    /**
     * Lưu product mới
     */
    public function store(Request $request)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập seller trước.');
        }

        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|max:100|unique:marketplace_products,sku',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type' => 'required|in:digital,new_product,used_product',
            'seller_type' => 'nullable|in:supplier,manufacturer,brand',
            'industry_category' => 'nullable|string',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_on_sale' => 'boolean',
            'sale_starts_at' => 'nullable|date',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
            'file_size_mb' => 'nullable|numeric|min:0',
            'download_limit' => 'nullable|integer|min:0',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'digital_files.*' => 'file|mimes:dwg,dxf,step,stp,iges,igs,stl,pdf,doc,docx,zip,rar|max:51200', // 50MB
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480', // 20MB
            'video_url' => 'nullable|url',
            'demo_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:draft,pending',
        ];

        $validated = $request->validate($rules);

        // Process technical specifications
        $technicalSpecs = [];
        if ($request->has('technical_specs')) {
            foreach ($request->technical_specs as $spec) {
                if (!empty($spec['name']) && !empty($spec['value'])) {
                    $technicalSpecs[] = [
                        'name' => $spec['name'],
                        'value' => $spec['value'],
                        'unit' => $spec['unit'] ?? ''
                    ];
                }
            }
        }

        // Process mechanical properties
        $mechanicalProperties = [];
        if ($request->has('mechanical_properties')) {
            foreach ($request->mechanical_properties as $prop) {
                if (!empty($prop['property']) && !empty($prop['value'])) {
                    $mechanicalProperties[] = [
                        'property' => $prop['property'],
                        'value' => $prop['value'],
                        'unit' => $prop['unit'] ?? ''
                    ];
                }
            }
        }

        // Process standards compliance
        $standardsCompliance = [];
        if ($request->has('standards_compliance')) {
            foreach ($request->standards_compliance as $standard) {
                if (!empty($standard['standard'])) {
                    $standardsCompliance[] = [
                        'standard' => $standard['standard'],
                        'certification' => $standard['certification'] ?? ''
                    ];
                }
            }
        }

        // Handle file uploads
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('marketplace/products', 'public');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('marketplace/products', 'public');
            }
        }

        $digitalFilesData = [];
        if ($request->hasFile('digital_files') && $validated['product_type'] === 'digital') {
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

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $fileName = time() . '_' . $attachment->getClientOriginalName();
                $filePath = $attachment->storeAs('marketplace/attachments', $fileName, 'public');

                $attachmentPaths[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $attachment->getSize(),
                    'mime_type' => $attachment->getMimeType(),
                ];
            }
        }

        // Process file formats and software compatibility
        $fileFormats = [];
        if (!empty($validated['file_formats'])) {
            $fileFormats = array_map('trim', explode(',', $validated['file_formats']));
        }

        $softwareCompatibility = [];
        if (!empty($validated['software_compatibility'])) {
            $softwareCompatibility = array_map('trim', explode(',', $validated['software_compatibility']));
        }

        // Process tags
        $tags = [];
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Calculate total file size for digital products
        $totalFileSize = 0;
        if (!empty($digitalFilesData)) {
            $totalFileSize = array_sum(array_column($digitalFilesData, 'size'));
            $validated['file_size_mb'] = round($totalFileSize / (1024 * 1024), 2);
        }

        // Create product
        $productData = [
            'seller_id' => $seller->id,
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'sku' => $validated['sku'] ?: 'MP-' . strtoupper(\Str::random(8)),
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'seller_type' => $validated['seller_type'] ?: 'supplier',
            'industry_category' => $validated['industry_category'],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => $validated['is_on_sale'] ?? false,
            'sale_starts_at' => $validated['sale_starts_at'],
            'sale_ends_at' => $validated['sale_ends_at'],
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
            'manage_stock' => $validated['manage_stock'] ?? true,
            'in_stock' => $validated['in_stock'] ?? true,
            'technical_specs' => $technicalSpecs,
            'mechanical_properties' => $mechanicalProperties,
            'standards_compliance' => $standardsCompliance,
            'file_formats' => $fileFormats,
            'software_compatibility' => $softwareCompatibility,
            'file_size_mb' => $validated['file_size_mb'],
            'download_limit' => $validated['download_limit'],
            'digital_files' => $digitalFilesData,
            'featured_image' => $featuredImagePath,
            'images' => $imagePaths,
            'attachments' => $attachmentPaths,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'tags' => $tags,
            'status' => $validated['status'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ];

        $product = MarketplaceProduct::create($productData);

        $message = $validated['status'] === 'pending'
            ? 'Sản phẩm đã được tạo và gửi duyệt thành công!'
            : 'Sản phẩm đã được lưu nháp thành công!';

        return redirect()
            ->route('dashboard.marketplace.seller.products.index')
            ->with('success', $message);
    }

    /**
     * Hiển thị chi tiết product
     */
    public function show(MarketplaceProduct $product)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'You can only view your own products.');
        }

        $product->load(['category', 'images', 'digitalFiles']);

        return $this->dashboardResponse('dashboard.marketplace.products.show', [
            'seller' => $seller,
            'product' => $product]);
    }

    /**
     * Hiển thị form chỉnh sửa product
     */
    public function edit(MarketplaceProduct $product)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'You can only edit your own products.');
        }

        $categories = ProductCategory::orderBy('name')->get();

        return $this->dashboardResponse('dashboard.marketplace.products.edit', [
            'seller' => $seller,
            'product' => $product,
            'categories' => $categories]);
    }

    /**
     * Cập nhật product
     */
    public function update(Request $request, MarketplaceProduct $product)
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'You can only edit your own products.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type' => 'required|in:digital,new_product,used_product',
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'industry_category' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'technical_specs' => 'nullable|array',
            'mechanical_properties' => 'nullable|array',
            'material' => 'nullable|string|max:255',
            'manufacturing_process' => 'nullable|string|max:255',
            'standards_compliance' => 'nullable|array',
            'file_formats' => 'nullable|array',
            'software_compatibility' => 'nullable|array',
            'file_size_mb' => 'nullable|numeric|min:0',
            'download_limit' => 'nullable|integer|min:0',
            'digital_files' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        $updateData = $request->except(['featured_image', 'images', 'attachments']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old featured image if exists
            if ($product->featured_image && \Storage::exists('public/' . $product->featured_image)) {
                \Storage::delete('public/' . $product->featured_image);
            }

            $featuredImage = $request->file('featured_image');
            $featuredImagePath = $featuredImage->store('marketplace/products', 'public');
            $updateData['featured_image'] = $featuredImagePath;
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            // Delete old images if exists
            if ($product->images) {
                foreach ($product->images as $image) {
                    if (\Storage::exists('public/' . $image)) {
                        \Storage::delete('public/' . $image);
                    }
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('marketplace/products', 'public');
                $imagePaths[] = $imagePath;
            }
            $updateData['images'] = $imagePaths;
        }

        // Handle attachments upload
        if ($request->hasFile('attachments')) {
            // Delete old attachments if exists
            if ($product->attachments) {
                foreach ($product->attachments as $attachment) {
                    if (\Storage::exists('public/' . $attachment['path'])) {
                        \Storage::delete('public/' . $attachment['path']);
                    }
                }
            }

            $attachmentPaths = [];
            foreach ($request->file('attachments') as $attachment) {
                $attachmentPath = $attachment->store('marketplace/attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $attachmentPath,
                    'size' => $attachment->getSize(),
                    'type' => $attachment->getClientMimeType(),
                ];
            }
            $updateData['attachments'] = $attachmentPaths;
        }

        // Update product
        $product->update($updateData);

        return redirect()
            ->route('dashboard.marketplace.seller.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * Cập nhật product status
     */
    public function updateStatus(Request $request, MarketplaceProduct $product): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_active' => 'required|boolean']);

        $product->update([
            'is_active' => $request->is_active]);

        $message = $request->is_active ? 'Product activated successfully.' : 'Product deactivated successfully.';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Duplicate product
     */
    public function duplicate(MarketplaceProduct $product): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->sku = $product->sku . '-copy-' . time();
        $newProduct->status = 'draft';
        $newProduct->is_active = false;
        $newProduct->save();

        return response()->json([
            'success' => true,
            'message' => 'Product duplicated successfully.',
            'product_id' => $newProduct->id,
            'edit_url' => route('dashboard.marketplace.products.edit', $newProduct)
        ]);
    }

    /**
     * Bulk actions cho products
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $seller = MarketplaceSeller::where('user_id', $this->user->id)->first();

        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Seller not found'], 404);
        }

        $request->validate([
            'action' => 'required|string|in:activate,deactivate,delete,draft',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:marketplace_products,id']);

        $productIds = $request->product_ids;
        $action = $request->action;

        // Verify ownership
        $products = MarketplaceProduct::whereIn('id', $productIds)
            ->where('seller_id', $seller->id)
            ->get();

        if ($products->count() !== count($productIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some products do not belong to you.'
            ], 403);
        }

        $updated = 0;

        switch ($action) {
            case 'activate':
                $updated = MarketplaceProduct::whereIn('id', $productIds)
                    ->where('seller_id', $seller->id)
                    ->update(['is_active' => true]);
                break;

            case 'deactivate':
                $updated = MarketplaceProduct::whereIn('id', $productIds)
                    ->where('seller_id', $seller->id)
                    ->update(['is_active' => false]);
                break;

            case 'draft':
                $updated = MarketplaceProduct::whereIn('id', $productIds)
                    ->where('seller_id', $seller->id)
                    ->update(['status' => 'draft', 'is_active' => false]);
                break;

            case 'delete':
                $updated = MarketplaceProduct::whereIn('id', $productIds)
                    ->where('seller_id', $seller->id)
                    ->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully {$action}d {$updated} products.",
            'updated_count' => $updated
        ]);
    }

    /**
     * Lấy thống kê products
     */
    private function getProductStats($seller)
    {
        $total = MarketplaceProduct::where('seller_id', $seller->id)->count();
        $active = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->where('status', 'approved')
            ->count();
        $draft = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('status', 'draft')->count();
        $pending = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('status', 'under_review')->count();
        $rejected = MarketplaceProduct::where('seller_id', $seller->id)
            ->where('status', 'rejected')->count();

        $totalViews = MarketplaceProduct::where('seller_id', $seller->id)->sum('view_count');
        $totalSales = MarketplaceProduct::where('seller_id', $seller->id)->sum('purchase_count');

        $productTypes = MarketplaceProduct::where('seller_id', $seller->id)
            ->selectRaw('product_type, COUNT(*) as count')
            ->groupBy('product_type')
            ->pluck('count', 'product_type')
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'draft' => $draft,
            'pending' => $pending,
            'rejected' => $rejected,
            'total_views' => $totalViews,
            'total_sales' => $totalSales,
            'product_types' => $productTypes,
            'this_month' => MarketplaceProduct::where('seller_id', $seller->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count()];
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy(MarketplaceProduct $product)
    {
        try {
            // Kiểm tra quyền sở hữu
            $seller = auth()->user()->marketplaceSeller;
            if (!$seller || $product->seller_id !== $seller->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xóa sản phẩm này.'
                ], 403);
            }

            // Kiểm tra xem sản phẩm có đơn hàng nào không
            if ($product->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sản phẩm đã có đơn hàng.'
                ], 400);
            }

            // Xóa files nếu có
            if ($product->featured_image) {
                Storage::disk('public')->delete($product->featured_image);
            }

            if ($product->images && is_array($product->images)) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            if ($product->digital_files && is_array($product->digital_files)) {
                foreach ($product->digital_files as $file) {
                    if (isset($file['path'])) {
                        Storage::disk('public')->delete($file['path']);
                    }
                }
            }

            // Xóa sản phẩm
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa thành công.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'
            ], 500);
        }
    }
}
