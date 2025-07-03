<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketplaceSeller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CompanyController extends Controller
{
    /**
     * Display company directory
     */
    public function index(Request $request)
    {
        $query = MarketplaceSeller::with(['user', 'products'])
                                ->where('verification_status', 'verified');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
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
        $sortBy = $request->get('sort', 'company_name');
        $sortOrder = $request->get('order', 'asc');

        $allowedSorts = ['company_name', 'created_at', 'total_sales', 'rating'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'rating') {
                $query->orderByDesc('average_rating');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $companies = $query->paginate(12);

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
                                  ->selectRaw('CONCAT(city, ", ", state, ", ", country) as location')
                                  ->distinct()
                                  ->pluck('location')
                                  ->filter()
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
            'industries'
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
                $query->where('status', 'active')->latest()->take(6);
            }
        ]);

        // Get company statistics
        $stats = [
            'total_products' => $company->products()->where('status', 'active')->count(),
            'years_in_business' => $company->established_year ? now()->year - $company->established_year : 0,
            'total_orders' => $company->orders()->count(),
            'response_rate' => $this->calculateResponseRate($company),
            'on_time_delivery' => $this->calculateOnTimeDelivery($company)
        ];

        // Get recent reviews/testimonials
        $reviews = $company->reviews()
                          ->with('user')
                          ->latest()
                          ->take(5)
                          ->get();

        // Get related companies
        $relatedCompanies = MarketplaceSeller::where('verification_status', 'verified')
                                           ->where('id', '!=', $company->id)
                                           ->where(function($q) use ($company) {
                                               $q->where('business_type', $company->business_type)
                                                 ->orWhere('city', $company->city);
                                           })
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

        $query = $company->products()->where('status', 'active');

        // Search within company products
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
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
     * Send message to company
     */
    public function sendMessage(Request $request, MarketplaceSeller $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:20',
            'inquiry_type' => 'required|in:general,quote,partnership,support'
        ]);

        // Store message in database
        $company->messages()->create([
            'sender_name' => $validated['name'],
            'sender_email' => $validated['email'],
            'sender_phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'inquiry_type' => $validated['inquiry_type'],
            'status' => 'unread'
        ]);

        // Send email notification to company
        // Mail::to($company->contact_email)->send(new CompanyInquiry($validated, $company));

        return back()->with('success', 'Your message has been sent successfully! The company will contact you soon.');
    }

    /**
     * Get company statistics for API
     */
    public function getStats(MarketplaceSeller $company)
    {
        $stats = [
            'total_products' => $company->products()->where('status', 'active')->count(),
            'total_orders' => $company->orders()->count(),
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
        $totalInquiries = $company->messages()->count();
        $respondedInquiries = $company->messages()->where('status', 'responded')->count();

        return $totalInquiries > 0 ? round(($respondedInquiries / $totalInquiries) * 100) : 0;
    }

    /**
     * Calculate on-time delivery rate
     */
    private function calculateOnTimeDelivery(MarketplaceSeller $company)
    {
        $totalOrders = $company->orders()->where('status', 'completed')->count();
        $onTimeOrders = $company->orders()
                              ->where('status', 'completed')
                              ->whereRaw('delivered_at <= expected_delivery_date')
                              ->count();

        return $totalOrders > 0 ? round(($onTimeOrders / $totalOrders) * 100) : 0;
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
                    $company->company_name,
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
}
