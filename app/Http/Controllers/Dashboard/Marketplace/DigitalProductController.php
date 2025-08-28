<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Http\Requests\Dashboard\Marketplace\StoreDigitalProductRequest;
use App\Http\Requests\Dashboard\Marketplace\UpdateDigitalProductRequest;
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
use Illuminate\Support\Facades\Storage;

/**
 * Digital Product Controller cho Dashboard Marketplace
 *
 * Chuyên xử lý sản phẩm số: files kỹ thuật, tài liệu, phần mềm
 */
class DigitalProductController extends BaseController
{
    protected ProductImageValidationService $imageService;

    public function __construct(ProductImageValidationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Show form to create new digital product
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản seller trước.');
        }

        // Check if user can sell digital products
        if (!UnifiedMarketplacePermissionService::canSell($user, 'digital')) {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Bạn không có quyền bán sản phẩm số.');
        }

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.marketplace.products.digital.create', compact('categories', 'seller'));
    }

    /**
     * Store new digital product
     */
    public function store(StoreDigitalProductRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('dashboard.marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản seller trước.');
        }

        // Get validated data from Form Request
        $validated = $request->validated();

        // Process digital files
        $digitalFilesData = [];
        $totalSize = 0;

        if ($request->hasFile('digital_files')) {
            foreach ($request->file('digital_files') as $file) {
                try {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('digital-products', $filename, 'private');

                    $digitalFilesData[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'filename' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];

                    $totalSize += $file->getSize();
                } catch (\Exception $e) {
                    Log::error('Error uploading digital file: ' . $e->getMessage());
                    return back()->withErrors(['digital_files' => 'Lỗi upload file: ' . $file->getClientOriginalName()])->withInput();
                }
            }
        }

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

        // Create digital product
        $product = MarketplaceProduct::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'sku' => 'DIG-' . strtoupper(Str::random(8)),
            'seller_id' => $seller->id,
            'product_category_id' => $validated['product_category_id'],
            'product_type' => 'digital',
            'seller_type' => $seller->seller_type,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => !empty($validated['sale_price']),
            'stock_quantity' => 0, // Digital products don't have stock
            'manage_stock' => false,
            'in_stock' => true, // Always in stock for digital
            'download_limit' => $validated['download_limit'],
            'file_formats' => $validated['file_formats'] ?? [],
            'software_compatibility' => $validated['software_compatibility'] ?? [],
            'file_size_mb' => round($totalSize / (1024 * 1024), 2),
            'digital_files' => $digitalFilesData,
            'images' => $imagesData,
            'featured_image' => $featuredImagePath,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => 'pending', // Require approval
            'is_active' => false,
        ]);

        Log::info('Digital product created', [
            'product_id' => $product->id,
            'seller_id' => $user->id,
            'file_count' => count($digitalFilesData),
            'total_size_mb' => $product->file_size_mb
        ]);

        // Update seller product count
        $seller->increment('total_products');

        return redirect()->route('dashboard.marketplace.seller.products.index')
            ->with('success', 'Sản phẩm số đã được tạo thành công! Sản phẩm sẽ được xem xét và phê duyệt.');
    }

    /**
     * Show form to edit digital product
     */
    public function edit(MarketplaceProduct $product): View|RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Không tìm thấy sản phẩm hoặc bạn không có quyền chỉnh sửa.');
        }

        if ($product->product_type !== 'digital') {
            return redirect()->route('dashboard.marketplace.seller.products.index')
                ->with('error', 'Sản phẩm này không phải là sản phẩm số.');
        }

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.marketplace.products.digital.edit', compact('product', 'categories', 'seller'));
    }

    /**
     * Update digital product
     */
    public function update(UpdateDigitalProductRequest $request, MarketplaceProduct $product): RedirectResponse
    {
        // Get validated data from Form Request (authorization already handled)
        $validated = $request->validated();

        // Update basic fields
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'product_category_id' => $validated['product_category_id'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'is_on_sale' => !empty($validated['sale_price']),
            'download_limit' => $validated['download_limit'],
            'file_formats' => $validated['file_formats'] ?? [],
            'software_compatibility' => $validated['software_compatibility'] ?? [],
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'status' => 'pending', // Require re-approval after edit
        ]);

        // Handle new digital files if uploaded
        if ($request->hasFile('digital_files')) {
            $digitalFilesData = $product->digital_files ?? [];
            $totalSize = array_sum(array_column($digitalFilesData, 'size'));

            foreach ($request->file('digital_files') as $file) {
                try {
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('digital-products', $filename, 'private');

                    $digitalFilesData[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'filename' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension(),
                    ];

                    $totalSize += $file->getSize();
                } catch (\Exception $e) {
                    Log::error('Error uploading digital file: ' . $e->getMessage());
                }
            }

            $product->update([
                'digital_files' => $digitalFilesData,
                'file_size_mb' => round($totalSize / (1024 * 1024), 2),
            ]);
        }

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

        Log::info('Digital product updated', [
            'product_id' => $product->id,
            'seller_id' => $user->id,
        ]);

        return redirect()->route('dashboard.marketplace.seller.products.index')
            ->with('success', 'Sản phẩm số đã được cập nhật thành công!');
    }
}
