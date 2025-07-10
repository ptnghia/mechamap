<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:brand']);
    }

    /**
     * Display brand's showcase products
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('brand.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản thương hiệu trước.');
        }

        $query = MarketplaceProduct::where('seller_id', $seller->id)
            ->with(['category']);

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

        return view('brand.products.index', compact('products', 'categories', 'seller'));
    }

    /**
     * Show form to create new showcase product
     */
    public function create(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('brand.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản thương hiệu trước.');
        }

        $categories = ProductCategory::all();

        return view('brand.products.create', compact('categories', 'seller'));
    }

    /**
     * Store new showcase product
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('brand.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản thương hiệu trước.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'product_type' => 'required|in:physical,digital,service',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'technical_specs' => 'nullable|array',
            'manufacturing_process' => 'nullable|string',
            'material' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
        ]);

        // Handle file uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace/products/images', 'public');
                $images[] = '/storage/' . $path;
            }
        }

        // Create showcase product (auto-approved for brands)
        $product = MarketplaceProduct::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'seller_id' => $seller->id,
            'product_category_id' => $validated['product_category_id'],
            'product_type' => $validated['product_type'],
            'seller_type' => 'brand',
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => 0, // Brands don't sell, just showcase
            'manage_stock' => false,
            'in_stock' => false,
            'technical_specs' => $validated['technical_specs'] ?? [],
            'manufacturing_process' => $validated['manufacturing_process'],
            'material' => $validated['material'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $images,
            'featured_image' => $images[0] ?? null,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'status' => 'approved', // Auto-approve brand showcase products
            'is_active' => true,
        ]);

        // Update seller product count
        $seller->increment('total_products');

        return redirect()->route('brand.products.index')
            ->with('success', 'Sản phẩm showcase đã được tạo thành công.');
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

        return view('brand.products.edit', compact('product', 'categories', 'seller'));
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
            'product_type' => 'required|in:physical,digital,service',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
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
            'technical_specs' => $validated['technical_specs'] ?? [],
            'manufacturing_process' => $validated['manufacturing_process'],
            'material' => $validated['material'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $images,
            'featured_image' => $images[0] ?? $product->featured_image,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
        ]);

        return redirect()->route('brand.products.index')
            ->with('success', 'Sản phẩm showcase đã được cập nhật thành công.');
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

        return redirect()->route('brand.products.index')
            ->with('success', 'Sản phẩm showcase đã được xóa thành công.');
    }
}
