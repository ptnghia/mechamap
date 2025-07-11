<?php

namespace App\Http\Controllers\Manufacturer;

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
        $this->middleware(['auth', 'role:manufacturer']);
        $this->imageService = $imageService;
    }

    /**
     * Display manufacturer's products
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('manufacturer.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà sản xuất trước.');
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
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = ProductCategory::all();

        return view('manufacturer.products.index', compact('products', 'categories', 'seller'));
    }

    /**
     * Show form to create new product
     */
    public function create(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('manufacturer.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà sản xuất trước.');
        }

        // Get allowed product types for manufacturer
        $allowedTypes = MarketplacePermissionService::getAllowedSellTypes($user->role);
        if (empty($allowedTypes)) {
            return redirect()->route('manufacturer.products.index')
                ->with('error', 'Bạn không có quyền bán sản phẩm nào.');
        }

        $categories = ProductCategory::all();

        return view('manufacturer.products.create', compact('categories', 'seller', 'allowedTypes'));
    }

    /**
     * Store new product
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('manufacturer.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà sản xuất trước.');
        }

        // Validate permissions first
        $permissionErrors = UnifiedMarketplacePermissionService::validateProductCreation($user, $request->all());
        if (!empty($permissionErrors)) {
            return back()->withErrors(['permission' => $permissionErrors])->withInput();
        }

        // Get allowed types for validation
        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_type' => 'required|in:' . implode(',', $allowedTypes),
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required_if:product_type,new_product|integer|min:0',
            'manage_stock' => 'boolean',
            'technical_specs' => 'nullable|array',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'file_formats' => 'nullable|array',
            'software_compatibility' => 'nullable|array',
            'digital_files' => 'nullable|array',
            'digital_files.*' => 'file|mimes:dwg,dxf,step,stp,iges,igs,stl,pdf,doc,docx,zip,rar|max:51200',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
        ]);

        // Process images
        $imagesData = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    $imagePath = $this->imageService->processUploadedImage($image, 'products');
                    $imagesData[] = $imagePath;
                } catch (\Exception $e) {
                    Log::error('Error processing image: ' . $e->getMessage());
                }
            }
        }

        // Process featured image
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            try {
                $featuredImagePath = $this->imageService->processUploadedImage($request->file('featured_image'), 'products');
            } catch (\Exception $e) {
                Log::error('Error processing featured image: ' . $e->getMessage());
            }
        }

        // Process digital files for digital products
        $digitalFilesData = [];
        if ($validated['product_type'] === 'digital' && $request->hasFile('digital_files')) {
            foreach ($request->file('digital_files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('digital_files', $filename, 'public');

                $digitalFilesData[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Create product
        $product = MarketplaceProduct::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'seller_id' => $seller->id,
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'seller_type' => 'manufacturer',
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => !empty($validated['sale_price']),
            'stock_quantity' => $validated['product_type'] === 'digital' ? 0 : ($validated['stock_quantity'] ?? 0),
            'manage_stock' => $validated['product_type'] === 'digital' ? false : ($validated['manage_stock'] ?? true),
            'in_stock' => $validated['product_type'] === 'digital' ? true : (($validated['stock_quantity'] ?? 0) > 0),
            'technical_specs' => $validated['technical_specs'] ?? [],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'file_formats' => $validated['file_formats'] ?? [],
            'software_compatibility' => $validated['software_compatibility'] ?? [],
            'digital_files' => $digitalFilesData,
            'file_size_mb' => !empty($digitalFilesData) ? round(array_sum(array_column($digitalFilesData, 'size')) / (1024 * 1024), 2) : 0,
            'images' => $imagesData,
            'featured_image' => $featuredImagePath ?: ($imagesData[0] ?? null),
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'status' => 'pending', // Manufacturer products require approval
            'is_active' => false,
        ]);

        Log::info('Manufacturer product created', [
            'product_id' => $product->id,
            'manufacturer_id' => $user->id,
            'product_type' => $product->product_type,
            'status' => $product->status
        ]);

        return redirect()->route('manufacturer.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công! Sản phẩm sẽ được xem xét và phê duyệt bởi admin.');
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

        return view('manufacturer.products.edit', compact('product', 'categories', 'seller'));
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'product_type' => 'required|in:digital,service',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'file_formats' => 'nullable|string',
            'software_compatibility' => 'nullable|string',
            'technical_specs' => 'nullable|array',
            'manufacturing_process' => 'nullable|string',
            'material' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'tags' => 'nullable|string',
        ]);

        // Handle file uploads if provided
        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($images as $image) {
                if (Storage::exists(str_replace('/storage/', 'public/', $image))) {
                    Storage::delete(str_replace('/storage/', 'public/', $image));
                }
            }

            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace/products/images', 'public');
                $images[] = '/storage/' . $path;
            }
        }

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'file_formats' => $validated['file_formats'],
            'software_compatibility' => $validated['software_compatibility'],
            'technical_specs' => $validated['technical_specs'] ?? [],
            'manufacturing_process' => $validated['manufacturing_process'],
            'material' => $validated['material'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $images,
            'featured_image' => $images[0] ?? $product->featured_image,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'status' => 'pending', // Reset to pending after edit
        ]);

        return redirect()->route('manufacturer.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật và đang chờ phê duyệt lại.');
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

        if ($product->digital_files) {
            foreach ($product->digital_files as $file) {
                if (isset($file['path']) && Storage::exists(str_replace('/storage/', 'public/', $file['path']))) {
                    Storage::delete(str_replace('/storage/', 'public/', $file['path']));
                }
            }
        }

        $product->delete();

        // Update seller product count
        $seller->decrement('total_products');

        return redirect()->route('manufacturer.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
