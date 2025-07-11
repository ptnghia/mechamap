<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display pending products for approval
     */
    public function index(Request $request): View
    {
        $query = MarketplaceProduct::with(['seller.user', 'category'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $pendingProducts = $query->paginate(15);

        // Get statistics
        $stats = [
            'total_pending' => MarketplaceProduct::where('status', 'pending')->count(),
            'pending_by_type' => MarketplaceProduct::where('status', 'pending')
                ->selectRaw('product_type, COUNT(*) as count')
                ->groupBy('product_type')
                ->pluck('count', 'product_type')
                ->toArray(),
            'pending_by_seller' => MarketplaceProduct::where('status', 'pending')
                ->selectRaw('seller_type, COUNT(*) as count')
                ->groupBy('seller_type')
                ->pluck('count', 'seller_type')
                ->toArray(),
        ];

        return view('admin.marketplace.product-approval.index', compact('pendingProducts', 'stats'));
    }

    /**
     * Show product details for approval
     */
    public function show(MarketplaceProduct $product): View
    {
        $product->load(['seller.user', 'category']);

        // Get seller's other products for context
        $sellerProducts = MarketplaceProduct::where('seller_id', $product->seller_id)
            ->where('id', '!=', $product->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.marketplace.product-approval.show', compact('product', 'sellerProducts'));
    }

    /**
     * Approve a product
     */
    public function approve(Request $request, MarketplaceProduct $product): RedirectResponse
    {
        if ($product->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể phê duyệt sản phẩm đang chờ xử lý.');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $product->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => Auth::guard('admin')->id(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        // Log approval action
        Log::info('Product approved by admin', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'seller_id' => $product->seller_id,
            'admin_id' => Auth::guard('admin')->id(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        // TODO: Send notification to seller
        // $this->notifySeller($product, 'approved');

        return redirect()
            ->route('admin.marketplace.product-approval.index')
            ->with('success', "Sản phẩm '{$product->name}' đã được phê duyệt thành công!");
    }

    /**
     * Reject a product
     */
    public function reject(Request $request, MarketplaceProduct $product): RedirectResponse
    {
        if ($product->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối sản phẩm đang chờ xử lý.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $product->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejected_at' => now(),
            'rejected_by' => Auth::guard('admin')->id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Log rejection action
        Log::info('Product rejected by admin', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'seller_id' => $product->seller_id,
            'admin_id' => Auth::guard('admin')->id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // TODO: Send notification to seller
        // $this->notifySeller($product, 'rejected');

        return redirect()
            ->route('admin.marketplace.product-approval.index')
            ->with('success', "Sản phẩm '{$product->name}' đã bị từ chối.");
    }

    /**
     * Bulk approve products
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:marketplace_products,id',
            'bulk_approval_notes' => 'nullable|string|max:1000',
        ]);

        $products = MarketplaceProduct::whereIn('id', $validated['product_ids'])
            ->where('status', 'pending')
            ->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Không có sản phẩm nào để phê duyệt.');
        }

        $approvedCount = 0;
        foreach ($products as $product) {
            $product->update([
                'status' => 'approved',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => Auth::guard('admin')->id(),
                'approval_notes' => $validated['bulk_approval_notes'] ?? null,
            ]);
            $approvedCount++;

            // Log each approval
            Log::info('Product bulk approved by admin', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'admin_id' => Auth::guard('admin')->id(),
            ]);
        }

        return redirect()
            ->route('admin.marketplace.product-approval.index')
            ->with('success', "Đã phê duyệt thành công {$approvedCount} sản phẩm!");
    }

    /**
     * Bulk reject products
     */
    public function bulkReject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:marketplace_products,id',
            'bulk_rejection_reason' => 'required|string|max:1000',
        ]);

        $products = MarketplaceProduct::whereIn('id', $validated['product_ids'])
            ->where('status', 'pending')
            ->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Không có sản phẩm nào để từ chối.');
        }

        $rejectedCount = 0;
        foreach ($products as $product) {
            $product->update([
                'status' => 'rejected',
                'is_active' => false,
                'rejected_at' => now(),
                'rejected_by' => Auth::guard('admin')->id(),
                'rejection_reason' => $validated['bulk_rejection_reason'],
            ]);
            $rejectedCount++;

            // Log each rejection
            Log::info('Product bulk rejected by admin', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'admin_id' => Auth::guard('admin')->id(),
            ]);
        }

        return redirect()
            ->route('admin.marketplace.product-approval.index')
            ->with('success', "Đã từ chối {$rejectedCount} sản phẩm!");
    }

    /**
     * Get approval statistics
     */
    public function statistics(): View
    {
        $stats = [
            'total_products' => MarketplaceProduct::count(),
            'pending_products' => MarketplaceProduct::where('status', 'pending')->count(),
            'approved_products' => MarketplaceProduct::where('status', 'approved')->count(),
            'rejected_products' => MarketplaceProduct::where('status', 'rejected')->count(),
            'by_type' => MarketplaceProduct::selectRaw('product_type, status, COUNT(*) as count')
                ->groupBy('product_type', 'status')
                ->get()
                ->groupBy('product_type'),
            'by_seller_type' => MarketplaceProduct::selectRaw('seller_type, status, COUNT(*) as count')
                ->groupBy('seller_type', 'status')
                ->get()
                ->groupBy('seller_type'),
            'recent_approvals' => MarketplaceProduct::where('status', 'approved')
                ->with(['seller.user'])
                ->orderBy('approved_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.marketplace.product-approval.statistics', compact('stats'));
    }

    /**
     * Send notification to seller (placeholder for future implementation)
     */
    private function notifySeller(MarketplaceProduct $product, string $action): void
    {
        // TODO: Implement notification system
        // This could send email, in-app notification, etc.
        Log::info("Notification should be sent to seller", [
            'product_id' => $product->id,
            'seller_id' => $product->seller_id,
            'action' => $action,
        ]);
    }
}
