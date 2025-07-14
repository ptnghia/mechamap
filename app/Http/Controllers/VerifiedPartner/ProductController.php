<?php

namespace App\Http\Controllers\VerifiedPartner;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use App\Services\UnifiedMarketplacePermissionService;
use App\Services\ProductImageValidationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private ProductImageValidationService $imageService;

    public function __construct(ProductImageValidationService $imageService)
    {
        $this->middleware(['auth', 'role:verified_partner']);
        $this->imageService = $imageService;
    }

    /**
     * Display verified partner's products
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        $query = MarketplaceProduct::where('seller_id', $seller->id)
            ->with(['category', 'orderItems']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('product_category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = ProductCategory::all();

        return view('verified-partner.products.index', compact('products', 'categories', 'seller'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        $categories = ProductCategory::all();
        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

        return view('verified-partner.products.create', compact('categories', 'seller', 'allowedTypes'));
    }

    /**
     * Store new product
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản đối tác trước.');
        }

        // Validate permissions first
        $permissionErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $request->all());
        if (!empty($permissionErrors)) {
            return back()->withErrors(['permission' => $permissionErrors])->withInput();
        }

        // Get allowed types for validation
        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:999999',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type' => 'required|in:' . implode(',', $allowedTypes),
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'digital_files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'stock_quantity' => 'required_if:product_type,new_product,used_product|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:marketplace_products,sku',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'condition' => 'required_if:product_type,used_product|in:excellent,good,fair,poor',
            'warranty_info' => 'nullable|string|max:500',
            'shipping_info' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:500',
        ]);

        try {
            // Handle featured image upload
            $featuredImagePath = null;
            if ($request->hasFile('featured_image')) {
                $featuredImagePath = $this->imageService->processUploadedImage($request->file('featured_image'));
            }

            // Handle additional images
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $this->imageService->processUploadedImage($image);
                }
            }

            // Handle digital files
            $digitalFilePaths = [];
            if ($request->hasFile('digital_files')) {
                foreach ($request->file('digital_files') as $file) {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/digital-products', $filename);
                    $digitalFilePaths[] = [
                        'filename' => $file->getClientOriginalName(),
                        'path' => Storage::url($path),
                        'size' => $file->getSize(),
                    ];
                }
            }

            // Create product
            $product = MarketplaceProduct::create([
                'seller_id' => $seller->id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'product_category_id' => $request->product_category_id,
                'product_type' => $request->product_type,
                'featured_image' => $featuredImagePath,
                'images' => !empty($imagePaths) ? $imagePaths : null,
                'digital_files' => !empty($digitalFilePaths) ? $digitalFilePaths : null,
                'stock_quantity' => $request->stock_quantity ?? 0,
                'sku' => $request->sku,
                'weight' => $request->weight,
                'dimensions' => $request->dimensions,
                'condition' => $request->condition,
                'warranty_info' => $request->warranty_info,
                'shipping_info' => $request->shipping_info,
                'tags' => $request->tags,
                'status' => 'active',
            ]);

            // Update seller product count
            $seller->increment('total_products');

            Log::info('Verified Partner product created', [
                'product_id' => $product->id,
                'seller_id' => $seller->id,
                'user_id' => $user->id,
                'product_type' => $product->product_type,
            ]);

            return redirect()->route('partner.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công.');

        } catch (\Exception $e) {
            Log::error('Failed to create verified partner product', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'seller_id' => $seller->id,
            ]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi tạo sản phẩm. Vui lòng thử lại.'])->withInput();
        }
    }

    /**
     * Display the specified product.
     */
    public function show(MarketplaceProduct $product): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to product.');
        }

        $product->load(['category', 'orderItems.order']);

        return view('verified-partner.products.show', compact('product', 'seller'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(MarketplaceProduct $product): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to product.');
        }

        $categories = ProductCategory::all();

        return view('verified-partner.products.edit', compact('product', 'categories', 'seller'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, MarketplaceProduct $product): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to product.');
        }

        // Validate permissions
        $permissionErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $request->all());
        if (!empty($permissionErrors)) {
            return back()->withErrors(['permission' => $permissionErrors])->withInput();
        }

        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:999999',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type' => 'required|in:' . implode(',', $allowedTypes),
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'digital_files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'stock_quantity' => 'required_if:product_type,new_product,used_product|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:marketplace_products,sku,' . $product->id,
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'condition' => 'required_if:product_type,used_product|in:excellent,good,fair,poor',
            'warranty_info' => 'nullable|string|max:500',
            'shipping_info' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive,draft',
        ]);

        try {
            $updateData = $request->only([
                'name', 'description', 'price', 'product_category_id', 'product_type',
                'stock_quantity', 'sku', 'weight', 'dimensions', 'condition',
                'warranty_info', 'shipping_info', 'tags', 'status'
            ]);

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                // Delete old featured image
                if ($product->featured_image && Storage::exists(str_replace('/storage/', 'public/', $product->featured_image))) {
                    Storage::delete(str_replace('/storage/', 'public/', $product->featured_image));
                }
                $updateData['featured_image'] = $this->imageService->processUploadedImage($request->file('featured_image'));
            }

            // Handle additional images
            if ($request->hasFile('images')) {
                // Delete old images
                if ($product->images) {
                    foreach ($product->images as $image) {
                        if (Storage::exists(str_replace('/storage/', 'public/', $image))) {
                            Storage::delete(str_replace('/storage/', 'public/', $image));
                        }
                    }
                }

                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $this->imageService->processUploadedImage($image);
                }
                $updateData['images'] = $imagePaths;
            }

            $product->update($updateData);

            return redirect()->route('partner.products.index')
                ->with('success', 'Sản phẩm đã được cập nhật thành công.');

        } catch (\Exception $e) {
            Log::error('Failed to update verified partner product', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'user_id' => $user->id,
            ]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật sản phẩm.'])->withInput();
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(MarketplaceProduct $product): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Unauthorized access to product.');
        }

        // Delete associated files
        if ($product->images) {
            foreach ($product->images as $image) {
                if (Storage::exists(str_replace('/storage/', 'public/', $image))) {
                    Storage::delete(str_replace('/storage/', 'public/', $image));
                }
            }
        }

        $product->delete();

        // Update seller product count
        $seller->decrement('total_products');

        return redirect()->route('partner.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
