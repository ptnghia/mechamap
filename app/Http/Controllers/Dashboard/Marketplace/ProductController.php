<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

    // NOTE: create() method removed - use DigitalProductController or PhysicalProductController instead

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

    // NOTE: edit() method removed - use DigitalProductController or PhysicalProductController instead

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
            ->get();

        return [
            'total_products' => $total,
            'active_products' => $active,
            'inactive_products' => $total - $active,
            'draft_products' => $draft,
            'pending_products' => $pending,
            'rejected_products' => $rejected,
            'total_views' => $totalViews,
            'total_sales' => $totalSales,
            'product_types' => $productTypes,
            'this_month_products' => MarketplaceProduct::where('seller_id', $seller->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count()
        ];
    }
}
