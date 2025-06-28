<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceSeller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketplaceSellerSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show seller setup form
     */
    public function show(): View
    {
        $user = auth()->user();

        // Check if user is eligible to be a seller
        if (!in_array($user->role, ['supplier', 'manufacturer', 'brand'])) {
            abort(403, 'Chỉ nhà cung cấp, nhà sản xuất và thương hiệu mới có thể thiết lập cửa hàng.');
        }

        // Check if seller already exists
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if ($seller) {
            return redirect()->route($this->getDashboardRoute($user->role))
                ->with('info', 'Tài khoản bán hàng đã được thiết lập.');
        }

        return view('marketplace.seller.setup', compact('user'));
    }

    /**
     * Process seller setup
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Check if user is eligible
        if (!in_array($user->role, ['supplier', 'manufacturer', 'brand'])) {
            abort(403, 'Chỉ nhà cung cấp, nhà sản xuất và thương hiệu mới có thể thiết lập cửa hàng.');
        }

        // Check if seller already exists
        $existingSeller = MarketplaceSeller::where('user_id', $user->id)->first();
        if ($existingSeller) {
            return redirect()->route($this->getDashboardRoute($user->role))
                ->with('info', 'Tài khoản bán hàng đã được thiết lập.');
        }

        $validated = $request->validate([
            // Business Information
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:individual,company,partnership',
            'tax_id' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',

            // Store Information
            'store_name' => 'required|string|max:255',
            'store_slug' => 'required|string|max:255|unique:marketplace_sellers,store_slug',
            'store_description' => 'nullable|string',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Categories
            'categories' => 'nullable|array',
            'categories.*' => 'string',

            // Terms
            'agree_terms' => 'required|accepted',
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('store_logo')) {
            $logoPath = $request->file('store_logo')->store('sellers/logos', 'public');
        }

        // Create seller account
        $seller = MarketplaceSeller::create([
            'user_id' => $user->id,
            'seller_type' => $user->role,
            'business_type' => $validated['business_type'],
            'business_name' => $validated['business_name'],
            'tax_identification_number' => $validated['tax_id'],
            'contact_person_name' => $user->name,
            'contact_email' => $user->email,
            'contact_phone' => $validated['phone'],
            'business_address' => [
                'full_address' => $validated['address'],
            ],
            'store_name' => $validated['store_name'],
            'store_slug' => $validated['store_slug'],
            'store_description' => $validated['store_description'],
            'store_logo' => $logoPath,
            'industry_categories' => $validated['categories'] ?? [],
            'verification_status' => 'pending',
            'status' => 'active',
            'commission_rate' => $this->getDefaultCommissionRate($user->role),
        ]);

        // Send notification to admin for verification
        $this->notifyAdminForVerification($seller);

        return redirect()->route($this->getDashboardRoute($user->role))
            ->with('success', 'Tài khoản bán hàng đã được tạo thành công! Chúng tôi sẽ xem xét và xác minh trong vòng 24-48 giờ.');
    }

    /**
     * Check store slug availability
     */
    public function checkSlug(Request $request)
    {
        $slug = $request->get('slug');
        $exists = MarketplaceSeller::where('store_slug', $slug)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'URL này đã được sử dụng' : 'URL có thể sử dụng'
        ]);
    }

    /**
     * Get dashboard route based on user role
     */
    private function getDashboardRoute(string $role): string
    {
        return match($role) {
            'supplier' => 'supplier.dashboard',
            'manufacturer' => 'manufacturer.dashboard',
            'brand' => 'brand.dashboard',
            default => 'home'
        };
    }

    /**
     * Get default commission rate based on seller type
     */
    private function getDefaultCommissionRate(string $role): float
    {
        return match($role) {
            'supplier' => 5.0,      // 5% for suppliers
            'manufacturer' => 3.0,   // 3% for manufacturers (digital products)
            'brand' => 0.0,         // 0% for brands (view-only)
            default => 5.0
        };
    }

    /**
     * Notify admin for seller verification
     */
    private function notifyAdminForVerification(MarketplaceSeller $seller): void
    {
        // Get admin users
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            // Send notification (implement your notification system)
            // For now, we'll just log it
            \Log::info("New seller verification required", [
                'seller_id' => $seller->id,
                'business_name' => $seller->business_name,
                'seller_type' => $seller->seller_type,
                'admin_notified' => $admin->email
            ]);
        }
    }

    /**
     * Show verification status
     */
    public function verificationStatus(): View
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản bán hàng trước.');
        }

        return view('marketplace.seller.verification-status', compact('seller'));
    }

    /**
     * Resend verification documents
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $seller = MarketplaceSeller::where('user_id', $user->id)->first();

        if (!$seller) {
            return redirect()->route('marketplace.seller.setup')
                ->with('error', 'Vui lòng hoàn thành thiết lập tài khoản bán hàng trước.');
        }

        if ($seller->verification_status === 'verified') {
            return redirect()->route($this->getDashboardRoute($user->role))
                ->with('info', 'Tài khoản đã được xác minh.');
        }

        $validated = $request->validate([
            'verification_documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'additional_notes' => 'nullable|string',
        ]);

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('verification_documents')) {
            foreach ($request->file('verification_documents') as $file) {
                $path = $file->store('sellers/verification', 'public');
                $documents[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        // Update seller with new documents
        $seller->update([
            'verification_documents' => array_merge($seller->verification_documents ?? [], $documents),
            'verification_status' => 'pending',
            'verification_notes' => $validated['additional_notes'],
        ]);

        // Notify admin
        $this->notifyAdminForVerification($seller);

        return redirect()->route('marketplace.seller.verification-status')
            ->with('success', 'Tài liệu xác minh đã được gửi lại. Chúng tôi sẽ xem xét trong vòng 24-48 giờ.');
    }
}
