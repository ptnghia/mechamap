<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RFQ;
use App\Models\MarketplaceSeller;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RFQController extends Controller
{
    /**
     * Display RFQ index page
     */
    public function index(Request $request)
    {
        $query = RFQ::with(['user', 'responses.seller']);
        
        // Filter by status for authenticated users
        if (Auth::check()) {
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
            
            // Show user's own RFQs
            if ($request->get('my_rfqs') === '1') {
                $query->where('user_id', Auth::id());
            }
        } else {
            // Only show public RFQs for guests
            $query->where('is_public', true)->where('status', 'open');
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }
        
        $rfqs = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('marketplace.rfq.index', compact('rfqs'));
    }
    
    /**
     * Show RFQ creation form
     */
    public function create()
    {
        $this->authorize('create', RFQ::class);
        
        $categories = [
            'mechanical_parts' => 'Mechanical Parts',
            'raw_materials' => 'Raw Materials', 
            'manufacturing' => 'Manufacturing Services',
            'tooling' => 'Tooling & Equipment',
            'consulting' => 'Engineering Consulting',
            'testing' => 'Testing & Inspection',
            'other' => 'Other'
        ];
        
        $materials = Material::where('is_active', true)
                           ->orderBy('name')
                           ->get(['id', 'name', 'code']);
        
        $suppliers = MarketplaceSeller::where('verification_status', 'verified')
                                   ->with('user')
                                   ->orderBy('company_name')
                                   ->get();
        
        return view('marketplace.rfq.create', compact('categories', 'materials', 'suppliers'));
    }
    
    /**
     * Store new RFQ
     */
    public function store(Request $request)
    {
        $this->authorize('create', RFQ::class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'delivery_location' => 'required|string',
            'delivery_deadline' => 'required|date|after:today',
            'technical_requirements' => 'nullable|string',
            'quality_standards' => 'nullable|array',
            'preferred_materials' => 'nullable|array',
            'preferred_suppliers' => 'nullable|array',
            'is_public' => 'boolean',
            'attachments.*' => 'file|mimes:pdf,doc,docx,dwg,step,iges|max:10240'
        ]);
        
        $rfq = new RFQ($validated);
        $rfq->user_id = Auth::id();
        $rfq->rfq_number = 'RFQ-' . strtoupper(uniqid());
        $rfq->status = 'open';
        $rfq->expires_at = now()->addDays(30); // Default 30 days
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('rfq-attachments', 'public');
                $attachments[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
            $rfq->attachments = $attachments;
        }
        
        $rfq->save();
        
        // Notify relevant suppliers
        $this->notifySuppliers($rfq);
        
        return redirect()->route('marketplace.rfq.show', $rfq)
                        ->with('success', 'RFQ created successfully! Suppliers will be notified.');
    }
    
    /**
     * Display specific RFQ
     */
    public function show(RFQ $rfq)
    {
        $this->authorize('view', $rfq);
        
        $rfq->load(['user', 'responses.seller.user']);
        
        // Increment view count
        $rfq->increment('view_count');
        
        // Get similar RFQs
        $similarRfqs = RFQ::where('id', '!=', $rfq->id)
                         ->where('category', $rfq->category)
                         ->where('status', 'open')
                         ->where('is_public', true)
                         ->limit(5)
                         ->get();
        
        return view('marketplace.rfq.show', compact('rfq', 'similarRfqs'));
    }
    
    /**
     * Submit quote response to RFQ
     */
    public function submitQuote(Request $request, RFQ $rfq)
    {
        $this->authorize('respond', $rfq);
        
        $validated = $request->validate([
            'quoted_price' => 'required|numeric|min:0',
            'delivery_time' => 'required|integer|min:1',
            'delivery_time_unit' => 'required|in:days,weeks,months',
            'message' => 'required|string|min:50',
            'terms_conditions' => 'nullable|string',
            'attachments.*' => 'file|mimes:pdf,doc,docx,dwg,step,iges|max:10240'
        ]);
        
        // Check if seller already responded
        $existingResponse = $rfq->responses()
                                ->where('seller_id', Auth::user()->marketplaceSeller->id)
                                ->first();
        
        if ($existingResponse) {
            return back()->with('error', 'You have already submitted a quote for this RFQ.');
        }
        
        $response = $rfq->responses()->create([
            'seller_id' => Auth::user()->marketplaceSeller->id,
            'quoted_price' => $validated['quoted_price'],
            'delivery_time' => $validated['delivery_time'],
            'delivery_time_unit' => $validated['delivery_time_unit'],
            'message' => $validated['message'],
            'terms_conditions' => $validated['terms_conditions'],
            'status' => 'submitted'
        ]);
        
        // Handle attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('rfq-responses', 'public');
                $attachments[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
            }
            $response->attachments = $attachments;
            $response->save();
        }
        
        // Notify RFQ owner
        $this->notifyRFQOwner($rfq, $response);
        
        return back()->with('success', 'Your quote has been submitted successfully!');
    }
    
    /**
     * Accept a quote response
     */
    public function acceptQuote(Request $request, RFQ $rfq, $responseId)
    {
        $this->authorize('manage', $rfq);
        
        $response = $rfq->responses()->findOrFail($responseId);
        
        // Update RFQ status
        $rfq->update(['status' => 'awarded']);
        
        // Update response status
        $response->update(['status' => 'accepted']);
        
        // Reject other responses
        $rfq->responses()
            ->where('id', '!=', $responseId)
            ->update(['status' => 'rejected']);
        
        return back()->with('success', 'Quote accepted! You can now proceed with the supplier.');
    }
    
    /**
     * Notify suppliers about new RFQ
     */
    private function notifySuppliers(RFQ $rfq)
    {
        // Get relevant suppliers based on category and location
        $suppliers = MarketplaceSeller::where('verification_status', 'verified')
                                    ->whereJsonContains('business_categories', $rfq->category)
                                    ->get();
        
        foreach ($suppliers as $supplier) {
            // Send notification (implement your notification system)
            // Mail::to($supplier->user->email)->send(new NewRFQNotification($rfq));
        }
    }
    
    /**
     * Notify RFQ owner about new quote
     */
    private function notifyRFQOwner(RFQ $rfq, $response)
    {
        // Send notification to RFQ owner
        // Mail::to($rfq->user->email)->send(new NewQuoteNotification($rfq, $response));
    }
    
    /**
     * Get RFQ statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_rfqs' => RFQ::count(),
            'open_rfqs' => RFQ::where('status', 'open')->count(),
            'awarded_rfqs' => RFQ::where('status', 'awarded')->count(),
            'avg_response_time' => RFQ::whereHas('responses')->avg('response_time_hours') ?? 0
        ];
        
        return response()->json($stats);
    }
}
