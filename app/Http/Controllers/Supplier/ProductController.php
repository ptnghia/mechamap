<?php

namespace App\Http\Controllers\Supplier;

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
        $this->middleware(['auth', 'role:supplier']);
    }

    /**
     * Display supplier's products
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
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

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = ProductCategory::all();

        return view('supplier.products.index', compact('products', 'categories', 'seller'));
    }

    /**
     * Show form to create new product
     */
    public function create(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $categories = ProductCategory::all();

        return view('supplier.products.create', compact('categories', 'seller'));
    }

    /**
     * Store new product
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà cung cấp trước.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'technical_specs' => 'nullable|array',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
        ]);

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
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
            'product_type' => 'physical',
            'seller_type' => 'supplier',
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => $validated['stock_quantity'],
            'manage_stock' => $validated['manage_stock'] ?? true,
            'in_stock' => $validated['stock_quantity'] > 0,
            'technical_specs' => $validated['technical_specs'] ?? [],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $images,
            'featured_image' => $images[0] ?? null,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'status' => 'pending',
            'is_active' => false,
        ]);

        // Update seller product count
        $seller->increment('total_products');

        return redirect()->route('supplier.products.index')
            ->with('success', 'Sản phẩm đã được tạo và đang chờ phê duyệt.');
    }

    /**
     * Show product details
     */
    public function show(MarketplaceProduct $product): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền xem sản phẩm này.');
        }

        $product->load(['category', 'orderItems.order']);

        return view('supplier.products.show', compact('product', 'seller'));
    }

    /**
     * Show form to edit product
     */
    public function edit(MarketplaceProduct $product): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm này.');
        }

        $categories = ProductCategory::all();

        return view('supplier.products.edit', compact('product', 'categories', 'seller'));
    }

    /**
     * Update product
     */
    public function update(Request $request, MarketplaceProduct $product): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm này.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'manage_stock' => 'boolean',
            'technical_specs' => 'nullable|array',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
        ]);

        // Handle new image uploads
        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
        }

        // Update product
        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'product_category_id' => $validated['product_category_id'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => $validated['stock_quantity'],
            'manage_stock' => $validated['manage_stock'] ?? true,
            'in_stock' => $validated['stock_quantity'] > 0,
            'technical_specs' => $validated['technical_specs'] ?? [],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'images' => $images,
            'featured_image' => $images[0] ?? null,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
        ]);

        return redirect()->route('supplier.products.show', $product)
            ->with('success', 'Sản phẩm đã được cập nhật.');
    }

    /**
     * Delete product
     */
    public function destroy(MarketplaceProduct $product): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $product->seller_id !== $seller->id) {
            abort(403, 'Bạn không có quyền xóa sản phẩm này.');
        }

        // Delete product images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        // Update seller product count
        $seller->decrement('total_products');

        return redirect()->route('supplier.products.index')
            ->with('success', 'Sản phẩm đã được xóa.');
    }
}
