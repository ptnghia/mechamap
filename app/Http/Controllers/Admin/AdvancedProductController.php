<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use App\Services\AdminMenuService;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Advanced Product Management Controller - Phase 3
 * Quản lý sản phẩm nâng cao với role-based features
 */
class AdvancedProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-marketplace|approve-products']);
    }

    /**
     * Hiển thị danh sách sản phẩm với filters nâng cao
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Build query với permissions
        $query = Product::with(['seller', 'category', 'reviews']);
        
        // Filter theo role
        if (!$user->hasPermissionTo('manage-marketplace')) {
            // Chỉ xem sản phẩm của seller mình quản lý
            $query->whereHas('seller', function($q) use ($user) {
                if ($user->hasPermissionTo('manage-seller-accounts')) {
                    // Marketplace moderator có thể xem tất cả
                    return $q;
                } else {
                    // Chỉ xem sản phẩm của chính mình
                    return $q->where('id', $user->id);
                }
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('seller_type')) {
            $query->whereHas('seller', function($q) use ($request) {
                $q->where('role', $request->seller_type);
            });
        }

        if ($request->filled('price_range')) {
            [$min, $max] = explode('-', $request->price_range);
            $query->whereBetween('price', [$min, $max]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'created_at', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = $this->getProductStats($user);
        
        // Filter options
        $categories = ProductCategory::active()->get();
        $sellerTypes = $this->getSellerTypes();

        return view('admin.products.advanced-index', compact(
            'products',
            'stats', 
            'categories',
            'sellerTypes',
            'user'
        ));
    }

    /**
     * Hiển thị sản phẩm chờ duyệt
     */
    public function pending(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('approve-products')) {
            abort(403, 'Không có quyền duyệt sản phẩm');
        }

        $query = Product::with(['seller', 'category'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc');

        // Priority sorting
        if ($request->get('priority') === 'verified_sellers') {
            $query->whereHas('seller', function($q) {
                $q->where('is_verified_business', true);
            });
        }

        $pendingProducts = $query->paginate(15);
        
        $stats = [
            'total_pending' => Product::where('status', 'pending')->count(),
            'urgent_pending' => Product::where('status', 'pending')
                ->where('created_at', '<', now()->subDays(3))->count(),
            'verified_seller_pending' => Product::where('status', 'pending')
                ->whereHas('seller', fn($q) => $q->where('is_verified_business', true))->count(),
        ];

        return view('admin.products.pending', compact('pendingProducts', 'stats'));
    }

    /**
     * Bulk approve products
     */
    public function bulkApprove(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('approve-products')) {
            return response()->json(['error' => 'Không có quyền duyệt sản phẩm'], 403);
        }

        $productIds = $request->input('product_ids', []);
        
        if (empty($productIds)) {
            return response()->json(['error' => 'Chưa chọn sản phẩm nào'], 400);
        }

        DB::beginTransaction();
        try {
            $updated = Product::whereIn('id', $productIds)
                ->where('status', 'pending')
                ->update([
                    'status' => 'active',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

            // Log activity
            foreach ($productIds as $productId) {
                activity()
                    ->performedOn(Product::find($productId))
                    ->causedBy($user)
                    ->log('Product approved via bulk action');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã duyệt {$updated} sản phẩm thành công",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk reject products
     */
    public function bulkReject(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('approve-products')) {
            return response()->json(['error' => 'Không có quyền từ chối sản phẩm'], 403);
        }

        $request->validate([
            'product_ids' => 'required|array',
            'reason' => 'required|string|max:500'
        ]);

        $productIds = $request->input('product_ids');
        $reason = $request->input('reason');

        DB::beginTransaction();
        try {
            $updated = Product::whereIn('id', $productIds)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'rejected_by' => $user->id,
                    'rejected_at' => now(),
                ]);

            // Send notifications to sellers
            $products = Product::whereIn('id', $productIds)->with('seller')->get();
            foreach ($products as $product) {
                // TODO: Send notification to seller
                activity()
                    ->performedOn($product)
                    ->causedBy($user)
                    ->withProperties(['reason' => $reason])
                    ->log('Product rejected via bulk action');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã từ chối {$updated} sản phẩm",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xem chi tiết sản phẩm với thông tin seller
     */
    public function show(Product $product)
    {
        $user = auth()->user();
        
        // Check permission to view this product
        if (!$user->hasPermissionTo('manage-marketplace') && 
            !$user->hasPermissionTo('approve-products') &&
            $product->seller_id !== $user->id) {
            abort(403, 'Không có quyền xem sản phẩm này');
        }

        $product->load([
            'seller.country',
            'category',
            'reviews.user',
            'orders.buyer',
            'media'
        ]);

        // Analytics data
        $analytics = [
            'total_views' => $product->view_count ?? 0,
            'total_orders' => $product->orders->count(),
            'total_revenue' => $product->orders->where('status', 'completed')->sum('total_amount'),
            'average_rating' => $product->reviews->avg('rating'),
            'conversion_rate' => $product->view_count > 0 ? 
                round(($product->orders->count() / $product->view_count) * 100, 2) : 0,
        ];

        // Commission calculation
        $commission = $this->calculateCommission($product);

        return view('admin.products.show', compact('product', 'analytics', 'commission'));
    }

    /**
     * Get product statistics
     */
    private function getProductStats(User $user): array
    {
        $query = Product::query();
        
        // Filter theo permission
        if (!$user->hasPermissionTo('manage-marketplace')) {
            $query->where('seller_id', $user->id);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'rejected' => $query->where('status', 'rejected')->count(),
            'total_revenue' => $query->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completed')
                ->sum('order_items.total_price'),
        ];
    }

    /**
     * Get seller types for filter
     */
    private function getSellerTypes(): array
    {
        return [
            'manufacturer' => 'Nhà sản xuất',
            'supplier' => 'Nhà cung cấp', 
            'verified_partner' => 'Đối tác xác thực',
            'brand' => 'Nhãn hàng'
        ];
    }

    /**
     * Calculate commission for product
     */
    private function calculateCommission(Product $product): array
    {
        $seller = $product->seller;
        $features = PermissionService::getMarketplaceFeatures($seller);
        $commissionRate = $features['commission_rate'] ?? 5.0;
        
        $totalRevenue = $product->orders->where('status', 'completed')->sum('total_amount');
        $commission = $totalRevenue * ($commissionRate / 100);
        
        return [
            'rate' => $commissionRate,
            'total_revenue' => $totalRevenue,
            'commission_amount' => $commission,
            'seller_earnings' => $totalRevenue - $commission,
        ];
    }

    /**
     * Export products data
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasPermissionTo('export-data')) {
            abort(403, 'Không có quyền xuất dữ liệu');
        }

        // TODO: Implement export functionality
        return response()->json(['message' => 'Export feature coming soon']);
    }
}
