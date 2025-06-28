<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:manufacturer']);
    }

    /**
     * Display manufacturer's designs/digital products
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
            ->where('product_type', 'digital')
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

        $designs = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = ProductCategory::all();

        return view('manufacturer.designs.index', compact('designs', 'categories', 'seller'));
    }

    /**
     * Show form to create new design
     */
    public function create(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('manufacturer.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà sản xuất trước.');
        }

        $categories = ProductCategory::all();

        return view('manufacturer.designs.create', compact('categories', 'seller'));
    }

    /**
     * Store new design
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('manufacturer.dashboard')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản nhà sản xuất trước.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'technical_specs' => 'nullable|array',
            'mechanical_properties' => 'nullable|array',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'file_formats' => 'required|array',
            'software_compatibility' => 'nullable|array',
            'design_files.*' => 'required|file|mimes:dwg,step,stp,iges,igs,stl,obj,3mf,pdf|max:51200', // 50MB max
            'preview_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'documentation' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'download_limit' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
        ]);

        // Handle design file uploads
        $designFiles = [];
        $totalFileSize = 0;

        if ($request->hasFile('design_files')) {
            foreach ($request->file('design_files') as $file) {
                $path = $file->store('designs/files', 'public');
                $fileSize = $file->getSize() / (1024 * 1024); // Convert to MB
                $totalFileSize += $fileSize;

                $designFiles[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size_mb' => round($fileSize, 2),
                    'format' => $file->getClientOriginalExtension(),
                ];
            }
        }

        // Handle preview images
        $previewImages = [];
        if ($request->hasFile('preview_images')) {
            foreach ($request->file('preview_images') as $image) {
                $path = $image->store('designs/previews', 'public');
                $previewImages[] = $path;
            }
        }

        // Handle documentation
        $documentationPath = null;
        if ($request->hasFile('documentation')) {
            $documentationPath = $request->file('documentation')->store('designs/docs', 'public');
        }

        // Create design product
        $design = MarketplaceProduct::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'seller_id' => $seller->id,
            'product_category_id' => $validated['product_category_id'],
            'product_type' => 'digital',
            'seller_type' => 'manufacturer',
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'stock_quantity' => 999999, // Digital products have unlimited stock
            'manage_stock' => false,
            'in_stock' => true,
            'technical_specs' => $validated['technical_specs'] ?? [],
            'mechanical_properties' => $validated['mechanical_properties'] ?? [],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'file_formats' => $validated['file_formats'],
            'software_compatibility' => $validated['software_compatibility'] ?? [],
            'file_size_mb' => round($totalFileSize, 2),
            'download_limit' => $validated['download_limit'],
            'images' => $previewImages,
            'featured_image' => $previewImages[0] ?? null,
            'attachments' => array_merge($designFiles, $documentationPath ? [['path' => $documentationPath, 'type' => 'documentation']] : []),
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'status' => 'pending',
            'is_active' => false,
        ]);

        // Update seller product count
        $seller->increment('total_products');

        return redirect()->route('manufacturer.designs.index')
            ->with('success', 'Thiết kế đã được tạo và đang chờ phê duyệt.');
    }

    /**
     * Show design details
     */
    public function show(MarketplaceProduct $design): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $design->seller_id !== $seller->id || $design->product_type !== 'digital') {
            abort(403, 'Bạn không có quyền xem thiết kế này.');
        }

        $design->load(['category', 'orderItems.order']);

        // Get download statistics
        $downloadStats = [
            'total_downloads' => $design->orderItems->sum('download_count'),
            'unique_customers' => $design->orderItems->count(),
            'revenue' => $design->orderItems->sum('total_price'),
        ];

        return view('manufacturer.designs.show', compact('design', 'seller', 'downloadStats'));
    }

    /**
     * Show form to edit design
     */
    public function edit(MarketplaceProduct $design): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $design->seller_id !== $seller->id || $design->product_type !== 'digital') {
            abort(403, 'Bạn không có quyền chỉnh sửa thiết kế này.');
        }

        $categories = ProductCategory::all();

        return view('manufacturer.designs.edit', compact('design', 'categories', 'seller'));
    }

    /**
     * Update design
     */
    public function update(Request $request, MarketplaceProduct $design): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $design->seller_id !== $seller->id || $design->product_type !== 'digital') {
            abort(403, 'Bạn không có quyền chỉnh sửa thiết kế này.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'product_category_id' => 'required|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'technical_specs' => 'nullable|array',
            'mechanical_properties' => 'nullable|array',
            'material' => 'nullable|string',
            'manufacturing_process' => 'nullable|string',
            'standards_compliance' => 'nullable|array',
            'file_formats' => 'required|array',
            'software_compatibility' => 'nullable|array',
            'design_files.*' => 'nullable|file|mimes:dwg,step,stp,iges,igs,stl,obj,3mf,pdf|max:51200',
            'preview_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'documentation' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'download_limit' => 'nullable|integer|min:0',
            'tags' => 'nullable|string',
        ]);

        // Handle new design file uploads
        $existingFiles = $design->attachments ?? [];
        $totalFileSize = collect($existingFiles)->sum('size_mb');

        if ($request->hasFile('design_files')) {
            foreach ($request->file('design_files') as $file) {
                $path = $file->store('designs/files', 'public');
                $fileSize = $file->getSize() / (1024 * 1024);
                $totalFileSize += $fileSize;

                $existingFiles[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size_mb' => round($fileSize, 2),
                    'format' => $file->getClientOriginalExtension(),
                ];
            }
        }

        // Handle new preview images
        $existingImages = $design->images ?? [];
        if ($request->hasFile('preview_images')) {
            foreach ($request->file('preview_images') as $image) {
                $path = $image->store('designs/previews', 'public');
                $existingImages[] = $path;
            }
        }

        // Update design
        $design->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'product_category_id' => $validated['product_category_id'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'],
            'technical_specs' => $validated['technical_specs'] ?? [],
            'mechanical_properties' => $validated['mechanical_properties'] ?? [],
            'material' => $validated['material'],
            'manufacturing_process' => $validated['manufacturing_process'],
            'standards_compliance' => $validated['standards_compliance'] ?? [],
            'file_formats' => $validated['file_formats'],
            'software_compatibility' => $validated['software_compatibility'] ?? [],
            'file_size_mb' => round($totalFileSize, 2),
            'download_limit' => $validated['download_limit'],
            'images' => $existingImages,
            'featured_image' => $existingImages[0] ?? null,
            'attachments' => $existingFiles,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
        ]);

        return redirect()->route('manufacturer.designs.show', $design)
            ->with('success', 'Thiết kế đã được cập nhật.');
    }

    /**
     * Delete design
     */
    public function destroy(MarketplaceProduct $design): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $design->seller_id !== $seller->id || $design->product_type !== 'digital') {
            abort(403, 'Bạn không có quyền xóa thiết kế này.');
        }

        // Delete design files
        if ($design->attachments) {
            foreach ($design->attachments as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        // Delete preview images
        if ($design->images) {
            foreach ($design->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $design->delete();

        // Update seller product count
        $seller->decrement('total_products');

        return redirect()->route('manufacturer.designs.index')
            ->with('success', 'Thiết kế đã được xóa.');
    }

    /**
     * Generate download link for customer
     */
    public function generateDownloadLink(MarketplaceProduct $design, Request $request)
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller || $design->seller_id !== $seller->id || $design->product_type !== 'digital') {
            abort(403, 'Bạn không có quyền tạo link download cho thiết kế này.');
        }

        $validated = $request->validate([
            'customer_email' => 'required|email',
            'expires_hours' => 'required|integer|min:1|max:168', // Max 7 days
        ]);

        // Generate secure download token
        $token = Str::random(64);
        $expiresAt = now()->addHours($validated['expires_hours']);

        // Store download link in cache or database
        cache()->put(
            "download_link_{$token}",
            [
                'design_id' => $design->id,
                'customer_email' => $validated['customer_email'],
                'seller_id' => $seller->id,
                'expires_at' => $expiresAt,
            ],
            $expiresAt
        );

        $downloadUrl = route('marketplace.download', ['token' => $token]);

        return response()->json([
            'success' => true,
            'download_url' => $downloadUrl,
            'expires_at' => $expiresAt->format('d/m/Y H:i'),
        ]);
    }
}
