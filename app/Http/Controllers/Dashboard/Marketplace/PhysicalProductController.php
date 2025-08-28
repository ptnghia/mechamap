<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Http\Requests\Dashboard\Marketplace\StorePhysicalProductRequest;
use App\Http\Requests\Dashboard\Marketplace\UpdatePhysicalProductRequest;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use App\Services\UnifiedMarketplacePermissionService;
use App\Services\ProductImageValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Physical Product Controller cho Dashboard Marketplace
 *
 * Chuyên xử lý sản phẩm vật lý: sản phẩm mới và đã qua sử dụng
 */
class PhysicalProductController extends BaseController
{
    protected ProductImageValidationService $imageService;

    public function __construct(ProductImageValidationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Show form to create new physical product
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản seller trước.');
        }

        // Check if user can sell physical products (new_product or used_product)
        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);
        $canSellPhysical = array_intersect(['new_product', 'used_product'], $allowedTypes);

        if (empty($canSellPhysical)) {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Bạn không có quyền bán sản phẩm vật lý.');
        }

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.marketplace.products.physical.create', compact('categories', 'seller', 'canSellPhysical'));
    }

    /**
     * Store new physical product
     */
    public function store(StorePhysicalProductRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản seller trước.');
        }

        // Get validated data from Form Request
        $validated = $request->validated();

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

        // Create physical product
        $product = MarketplaceProduct::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'sku' => 'PHY-' . strtoupper(Str::random(8)),
            'seller_id' => $seller->id,
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'seller_type' => $seller->seller_type,
            'industry_category' => $validated['industry_category'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => !empty($validated['sale_price']),
            'stock_quantity' => $validated['stock_quantity'],
            'manage_stock' => $validated['manage_stock'] ?? true,
            'in_stock' => $validated['stock_quantity'] > 0,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'technical_specs' => $validated['technical_specs'] ?? [],
            'mechanical_properties' => $validated['mechanical_properties'] ?? [],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $imagesData,
            'featured_image' => $featuredImagePath,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => 'pending', // Require approval
            'is_active' => false,
        ]);

        Log::info('Physical product created', [
            'product_id' => $product->id,
            'seller_id' => $user->id,
            'product_type' => $product->product_type,
            'stock_quantity' => $product->stock_quantity
        ]);

        // Update seller product count
        $seller->increment('total_products');

        return redirect()->route('dashboard.marketplace.seller.products.index')
            ->with('success', 'Sản phẩm vật lý đã được tạo thành công! Sản phẩm sẽ được xem xét và phê duyệt.');
    }

    /**
     * Show form to edit physical product
     */
    public function edit(MarketplaceProduct $product): View|RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Không tìm thấy sản phẩm hoặc bạn không có quyền chỉnh sửa.');
        }

        if (!in_array($product->product_type, ['new_product', 'used_product'])) {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Sản phẩm này không phải là sản phẩm vật lý.');
        }

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Check allowed types for editing
        $allowedTypes = UnifiedMarketplacePermissionService::getAllowedSellTypes($user);
        $canSellPhysical = array_intersect(['new_product', 'used_product'], $allowedTypes);

        return view('dashboard.marketplace.products.physical.edit', compact('product', 'categories', 'seller', 'canSellPhysical'));
    }

    /**
     * Update physical product
     */
    public function update(UpdatePhysicalProductRequest $request, MarketplaceProduct $product): RedirectResponse
    {
        // Get validated data from Form Request (authorization already handled)
        $validated = $request->validated();

        // Check permissions for product type change
        if ($validated['product_type'] !== $product->product_type) {
            if (!UnifiedMarketplacePermissionService::canSell($user, $validated['product_type'])) {
                return back()->withErrors(['product_type' => 'Bạn không có quyền bán loại sản phẩm này.'])->withInput();
            }
        }

        // Update basic fields
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'industry_category' => $validated['industry_category'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => !empty($validated['sale_price']),
            'stock_quantity' => $validated['stock_quantity'],
            'manage_stock' => $validated['manage_stock'] ?? true,
            'in_stock' => $validated['stock_quantity'] > 0,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'technical_specs' => $validated['technical_specs'] ?? [],
            'mechanical_properties' => $validated['mechanical_properties'] ?? [],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => 'pending', // Require re-approval after edit
        ]);

        // Handle new images if uploaded
        if ($request->hasFile('images')) {
            $imagesData = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                try {
                    $imagePath = $this->imageService->processUploadedImage($image, 'products');
                    $imagesData[] = $imagePath;
                } catch (\Exception $e) {
                    Log::error('Error processing image: ' . $e->getMessage());
                }
            }
            $product->update(['images' => $imagesData]);
        }

        // Handle new featured image if uploaded
        if ($request->hasFile('featured_image')) {
            try {
                $featuredImagePath = $this->imageService->processUploadedImage($request->file('featured_image'), 'products');
                $product->update(['featured_image' => $featuredImagePath]);
            } catch (\Exception $e) {
                Log::error('Error processing featured image: ' . $e->getMessage());
            }
        }

        Log::info('Physical product updated', [
            'product_id' => $product->id,
            'seller_id' => $user->id,
            'product_type' => $product->product_type,
        ]);

        return redirect()->route('dashboard.marketplace.seller.products.index')
            ->with('success', 'Sản phẩm vật lý đã được cập nhật thành công!');
    }
}
