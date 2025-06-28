<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSeller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketplaceSellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MarketplaceSeller::with(['user', 'verifiedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhere('business_registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by seller type
        if ($request->filled('seller_type')) {
            $query->where('seller_type', $request->seller_type);
        }

        // Filter by verification status
        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $sellers = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_sellers' => MarketplaceSeller::count(),
            'verified_sellers' => MarketplaceSeller::where('verification_status', 'verified')->count(),
            'pending_verification' => MarketplaceSeller::where('verification_status', 'pending')->count(),
            'active_sellers' => MarketplaceSeller::where('status', 'active')->count(),
            'total_revenue' => MarketplaceSeller::sum('total_revenue'),
            'pending_earnings' => MarketplaceSeller::sum('pending_earnings'),
        ];

        return view('admin.marketplace.sellers.index', compact('sellers', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('marketplaceSeller')->get();

        return view('admin.marketplace.sellers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:marketplace_sellers,user_id',
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'business_type' => 'required|in:individual,company,corporation',
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'business_description' => 'nullable|string',
            'contact_person_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'business_address' => 'nullable|array',
            'website_url' => 'nullable|url',
            'industry_categories' => 'nullable|array',
            'specializations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'verification_status' => 'required|in:pending,verified,rejected',
            'status' => 'required|in:active,inactive,suspended,banned',
        ]);

        // Auto-verify if admin is creating
        if (Auth::guard('admin')->check() && $validated['verification_status'] === 'verified') {
            $validated['verified_at'] = now();
            $validated['verified_by'] = Auth::guard('admin')->id();
        }

        $seller = MarketplaceSeller::create($validated);

        return redirect()
            ->route('admin.marketplace.sellers.index')
            ->with('success', 'Nhà bán hàng đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketplaceSeller $seller)
    {
        $seller->load([
            'user',
            'products' => function($query) {
                $query->latest()->limit(10);
            },
            'orderItems.order',
            'verifiedBy'
        ]);

        // Get seller statistics
        $stats = [
            'total_products' => $seller->products()->count(),
            'active_products' => $seller->products()->where('status', 'approved')->where('is_active', true)->count(),
            'total_orders' => $seller->orderItems()->count(),
            'completed_orders' => $seller->orderItems()->where('fulfillment_status', 'completed')->count(),
            'total_revenue' => $seller->orderItems()->sum('seller_earnings'),
            'average_rating' => $seller->rating_average,
            'total_reviews' => $seller->rating_count,
        ];

        return view('admin.marketplace.sellers.show', compact('seller', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketplaceSeller $seller)
    {
        return view('admin.marketplace.sellers.edit', compact('seller'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketplaceSeller $seller)
    {
        $validated = $request->validate([
            'seller_type' => 'required|in:supplier,manufacturer,brand',
            'business_type' => 'required|in:individual,company,corporation',
            'business_name' => 'required|string|max:255',
            'business_registration_number' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
            'business_description' => 'nullable|string',
            'contact_person_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'business_address' => 'nullable|array',
            'website_url' => 'nullable|url',
            'industry_categories' => 'nullable|array',
            'specializations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'verification_status' => 'required|in:pending,verified,rejected',
            'verification_notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended,banned',
            'suspension_reason' => 'nullable|string',
        ]);

        // Handle verification status changes
        if ($validated['verification_status'] === 'verified' && $seller->verification_status !== 'verified') {
            $validated['verified_at'] = now();
            $validated['verified_by'] = Auth::guard('admin')->id();
        } elseif ($validated['verification_status'] !== 'verified') {
            $validated['verified_at'] = null;
            $validated['verified_by'] = null;
        }

        // Handle suspension
        if ($validated['status'] === 'suspended' && $seller->status !== 'suspended') {
            $validated['suspended_at'] = now();
        } elseif ($validated['status'] !== 'suspended') {
            $validated['suspended_at'] = null;
            $validated['suspension_reason'] = null;
        }

        $seller->update($validated);

        return redirect()
            ->route('admin.marketplace.sellers.index')
            ->with('success', 'Thông tin nhà bán hàng đã được cập nhật!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketplaceSeller $seller)
    {
        // Check if seller has active orders
        $activeOrders = $seller->orderItems()
            ->whereIn('fulfillment_status', ['pending', 'processing', 'shipped'])
            ->count();

        if ($activeOrders > 0) {
            return redirect()
                ->route('admin.marketplace.sellers.index')
                ->with('error', 'Không thể xóa nhà bán hàng có đơn hàng đang xử lý!');
        }

        $seller->delete();

        return redirect()
            ->route('admin.marketplace.sellers.index')
            ->with('success', 'Nhà bán hàng đã được xóa thành công!');
    }

    /**
     * Verify seller
     */
    public function verify(Request $request, MarketplaceSeller $seller)
    {
        $validated = $request->validate([
            'verification_notes' => 'nullable|string',
        ]);

        $seller->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::guard('admin')->id(),
            'verification_notes' => $validated['verification_notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nhà bán hàng đã được xác minh thành công!'
        ]);
    }

    /**
     * Reject seller verification
     */
    public function reject(Request $request, MarketplaceSeller $seller)
    {
        $validated = $request->validate([
            'verification_notes' => 'required|string',
        ]);

        $seller->update([
            'verification_status' => 'rejected',
            'verified_at' => null,
            'verified_by' => null,
            'verification_notes' => $validated['verification_notes'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối xác minh nhà bán hàng!'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(MarketplaceSeller $seller)
    {
        $seller->update([
            'is_featured' => !$seller->is_featured,
        ]);

        $status = $seller->is_featured ? 'đã được đánh dấu nổi bật' : 'đã bỏ đánh dấu nổi bật';

        return response()->json([
            'success' => true,
            'message' => "Nhà bán hàng {$status}!",
            'is_featured' => $seller->is_featured
        ]);
    }

    /**
     * Suspend seller
     */
    public function suspend(Request $request, MarketplaceSeller $seller)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string',
        ]);

        $seller->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspension_reason' => $validated['suspension_reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nhà bán hàng đã bị đình chỉ!'
        ]);
    }

    /**
     * Reactivate seller
     */
    public function reactivate(MarketplaceSeller $seller)
    {
        $seller->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nhà bán hàng đã được kích hoạt lại!'
        ]);
    }

    /**
     * Get seller earnings report
     */
    public function earningsReport(MarketplaceSeller $seller, Request $request)
    {
        $period = $request->get('period', '30'); // days

        $earnings = $seller->orderItems()
            ->where('created_at', '>=', now()->subDays($period))
            ->where('fulfillment_status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(seller_earnings) as daily_earnings'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'earnings' => $earnings,
            'total_earnings' => $earnings->sum('daily_earnings'),
            'total_orders' => $earnings->sum('orders_count'),
        ]);
    }
}
