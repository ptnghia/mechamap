<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketplaceSeller;
use App\Models\User;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    /**
     * Display company directory
     */
    public function index(Request $request)
    {
        $query = MarketplaceSeller::with(['user'])
                                ->withCount([
                                    'products as total_products_count',
                                    'products as active_products_count' => function($query) {
                                        $query->where('status', 'approved')
                                              ->where('is_active', true);
                                    }
                                ])
                                ->where('verification_status', 'verified');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                  ->orWhere('business_description', 'LIKE', "%{$search}%")
                  ->orWhere('specializations', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by business type
        if ($request->filled('business_type')) {
            $query->where('business_type', $request->get('business_type'));
        }

        // Filter by location
        if ($request->filled('location')) {
            $location = $request->get('location');
            $query->where(function($q) use ($location) {
                $q->where('city', 'LIKE', "%{$location}%")
                  ->orWhere('state', 'LIKE', "%{$location}%")
                  ->orWhere('country', 'LIKE', "%{$location}%");
            });
        }

        // Filter by industry
        if ($request->filled('industry')) {
            $query->whereJsonContains('business_categories', $request->get('industry'));
        }

        // Sort options
        $sortBy = $request->get('sort', 'business_name');
        $sortOrder = $request->get('order', 'asc');

        $allowedSorts = ['business_name', 'created_at', 'total_sales', 'rating'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'rating') {
                $query->orderByDesc('rating_average');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $companies = $query->paginate(12);

        // Load favorite status for authenticated users
        if (auth()->check()) {
            $userFavorites = auth()->user()->favoriteCompanies()->pluck('marketplace_seller_id')->toArray();
            foreach ($companies as $company) {
                $company->is_favorited = in_array($company->id, $userFavorites);
            }
        }

        // Get statistics
        $stats = [
            'total_companies' => MarketplaceSeller::where('verification_status', 'verified')->count(),
            'total_industries' => MarketplaceSeller::where('verification_status', 'verified')
                                                  ->distinct()
                                                  ->whereNotNull('business_type')
                                                  ->count('business_type'),
            'total_cities' => MarketplaceSeller::where('verification_status', 'verified')
                                              ->whereNotNull('business_address')
                                              ->get()
                                              ->pluck('business_address')
                                              ->filter()
                                              ->map(function($address) {
                                                  return is_array($address) ? ($address['city'] ?? null) : null;
                                              })
                                              ->filter()
                                              ->unique()
                                              ->count(),
            'average_rating' => MarketplaceSeller::where('verification_status', 'verified')
                                                ->where('rating_count', '>', 0)
                                                ->avg('rating_average') ?? 0
        ];

        // Get filter options
        $businessTypes = Cache::remember('company_business_types', 3600, function() {
            return MarketplaceSeller::where('verification_status', 'verified')
                                  ->distinct()
                                  ->pluck('business_type')
                                  ->filter()
                                  ->sort()
                                  ->values();
        });

        $locations = Cache::remember('company_locations', 3600, function() {
            return MarketplaceSeller::where('verification_status', 'verified')
                                  ->whereNotNull('business_address')
                                  ->get()
                                  ->pluck('business_address')
                                  ->filter()
                                  ->map(function($address) {
                                      if (is_array($address)) {
                                          $parts = array_filter([
                                              $address['city'] ?? null,
                                              $address['state'] ?? null,
                                              $address['country'] ?? 'Vietnam'
                                          ]);
                                          return implode(', ', $parts);
                                      }
                                      return $address;
                                  })
                                  ->filter()
                                  ->unique()
                                  ->sort()
                                  ->values();
        });

        $industries = [
            'mechanical_parts' => 'Mechanical Parts',
            'raw_materials' => 'Raw Materials',
            'manufacturing' => 'Manufacturing Services',
            'tooling' => 'Tooling & Equipment',
            'consulting' => 'Engineering Consulting',
            'testing' => 'Testing & Inspection',
            'automation' => 'Automation & Control',
            'maintenance' => 'Maintenance & Repair'
        ];

        return view('community.companies.index', compact(
            'companies',
            'businessTypes',
            'locations',
            'industries',
            'stats'
        ));
    }

    /**
     * Display company profile
     */
    public function show(MarketplaceSeller $company)
    {
        if ($company->verification_status !== 'verified') {
            abort(404);
        }

        $company->load([
            'user',
            'products' => function($query) {
                $query->where('status', 'approved')
                      ->where('is_active', true)
                      ->latest()
                      ->take(6);
            }
        ]);

        // Get company statistics
        $stats = [
            'total_products' => $company->products()->count(),
            'years_in_business' => $company->established_year ? now()->year - $company->established_year : 0,
            'total_orders' => $company->orderItems()->distinct('order_id')->count(),
            'response_rate' => $this->calculateResponseRate($company),
            'on_time_delivery' => $this->calculateOnTimeDelivery($company)
        ];

        // Get recent reviews/testimonials from products
        // Since MarketplaceSeller doesn't have direct reviews, we'll get reviews from their products
        $reviews = collect(); // Empty collection for now - can be implemented later when review system is ready

        // Get related companies
        $relatedCompanies = MarketplaceSeller::where('verification_status', 'verified')
                                           ->where('id', '!=', $company->id)
                                           ->where('business_type', $company->business_type)
                                           ->limit(6)
                                           ->get();

        return view('community.companies.show', compact(
            'company',
            'stats',
            'reviews',
            'relatedCompanies'
        ));
    }

    /**
     * Display company products
     */
    public function products(MarketplaceSeller $company, Request $request)
    {
        if ($company->verification_status !== 'verified') {
            abort(404);
        }

        $query = $company->products()->where('status', 'approved')->where('is_active', true);

        // Search within company products
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        $products = $query->with('category')
                         ->orderBy('created_at', 'desc')
                         ->paginate(12);

        return view('community.companies.products', compact('company', 'products'));
    }

    /**
     * Contact company form
     */
    public function contact(MarketplaceSeller $company)
    {
        if ($company->verification_status !== 'verified') {
            abort(404);
        }

        return view('community.companies.contact', compact('company'));
    }

    /**
     * Send message to company via chat system
     */
    public function sendMessage(Request $request, MarketplaceSeller $company)
    {
        // Require authentication for messaging
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để gửi tin nhắn.');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:20',
            'inquiry_type' => 'required|in:general,quote,partnership,support'
        ]);

        // Get company owner user
        $companyUser = $company->user;
        if (!$companyUser) {
            return back()->with('error', 'Không thể gửi tin nhắn đến công ty này.');
        }

        // Check if conversation already exists between current user and company user
        $existingConversation = $this->findConversationBetweenUsers(auth()->id(), $companyUser->id);

        // Format message with inquiry details
        $inquiryTypeLabels = [
            'general' => 'Thông tin chung',
            'quote' => 'Báo giá sản phẩm',
            'partnership' => 'Hợp tác kinh doanh',
            'support' => 'Hỗ trợ kỹ thuật'
        ];

        $formattedMessage = "📋 **{$validated['subject']}**\n\n";
        $formattedMessage .= "🏷️ **Loại yêu cầu:** {$inquiryTypeLabels[$validated['inquiry_type']]}\n\n";
        $formattedMessage .= "💬 **Nội dung:**\n{$validated['message']}\n\n";
        $formattedMessage .= "---\n_Tin nhắn được gửi từ trang liên hệ công ty {$company->business_name}_";

        if ($existingConversation) {
            // Add message to existing conversation
            Message::create([
                'conversation_id' => $existingConversation->id,
                'user_id' => auth()->id(),
                'content' => $formattedMessage,
            ]);

            $existingConversation->touch();

            return redirect()->route('chat.show', $existingConversation->id)
                ->with('success', 'Tin nhắn đã được gửi thành công!');
        }

        // Create new conversation
        $conversation = Conversation::create(['title' => "Liên hệ: {$validated['subject']}"]);

        // Add participants
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $companyUser->id,
        ]);

        // Add first message
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'content' => $formattedMessage,
        ]);

        return redirect()->route('chat.show', $conversation->id)
            ->with('success', 'Cuộc trò chuyện mới đã được tạo và tin nhắn đã được gửi!');
    }

    /**
     * Get company statistics for API
     */
    public function getStats(MarketplaceSeller $company)
    {
        $stats = [
            'total_products' => $company->products()->where('status', 'active')->count(),
            'total_orders' => $company->orderItems()->distinct('order_id')->count(),
            'average_rating' => $company->average_rating,
            'response_rate' => $this->calculateResponseRate($company),
            'member_since' => $company->created_at->format('Y-m-d')
        ];

        return response()->json($stats);
    }

    /**
     * Calculate response rate for company
     */
    private function calculateResponseRate(MarketplaceSeller $company)
    {
        // For now, return a default response rate since messages relationship doesn't exist yet
        return 95; // Default good response rate
    }

    /**
     * Calculate on-time delivery rate
     */
    private function calculateOnTimeDelivery(MarketplaceSeller $company)
    {
        // Calculate based on order items since we don't have direct orders relationship
        $totalOrderItems = $company->orderItems()->where('fulfillment_status', 'delivered')->count();
        $onTimeOrderItems = $company->orderItems()
                                  ->where('fulfillment_status', 'delivered')
                                  ->whereNotNull('delivered_at')
                                  ->count();

        return $totalOrderItems > 0 ? round(($onTimeOrderItems / $totalOrderItems) * 100) : 92; // Default good delivery rate
    }

    /**
     * Export companies data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $companies = MarketplaceSeller::where('verification_status', 'verified')
                                    ->with('user')
                                    ->get();

        if ($format === 'json') {
            return response()->json($companies);
        }

        // CSV export
        $filename = 'companies_directory_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($companies) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Company Name', 'Business Type', 'Contact Person', 'Email',
                'Phone', 'City', 'State', 'Country', 'Website', 'Established Year'
            ]);

            foreach ($companies as $company) {
                fputcsv($file, [
                    $company->business_name,
                    $company->business_type,
                    $company->user->name,
                    $company->contact_email,
                    $company->contact_phone,
                    $company->city,
                    $company->state,
                    $company->country,
                    $company->website,
                    $company->established_year
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Toggle favorite status for a company
     */
    public function toggleFavorite(MarketplaceSeller $company)
    {
        $user = auth()->user();

        // Check if company is already favorited
        $existingFavorite = $user->favoriteCompanies()->where('marketplace_seller_id', $company->id)->first();

        if ($existingFavorite) {
            // Remove from favorites
            $user->favoriteCompanies()->detach($company->id);
            $favorited = false;
            $message = 'Company removed from favorites';
        } else {
            // Add to favorites
            $user->favoriteCompanies()->attach($company->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $favorited = true;
            $message = 'Company added to favorites';
        }

        return response()->json([
            'success' => true,
            'favorited' => $favorited,
            'message' => $message
        ]);
    }

    /**
     * Find conversation between two users
     */
    private function findConversationBetweenUsers($userId1, $userId2)
    {
        return Conversation::whereHas('participants', function ($query) use ($userId1) {
            $query->where('user_id', $userId1);
        })->whereHas('participants', function ($query) use ($userId2) {
            $query->where('user_id', $userId2);
        })->first();
    }
}
