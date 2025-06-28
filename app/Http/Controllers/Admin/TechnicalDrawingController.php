<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicalDrawing;
use App\Models\MarketplaceSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TechnicalDrawingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TechnicalDrawing::with(['creator', 'company', 'approvedBy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('drawing_number', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by drawing type
        if ($request->filled('drawing_type')) {
            $query->where('drawing_type', $request->drawing_type);
        }

        // Filter by visibility
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by industry category
        if ($request->filled('industry_category')) {
            $query->where('industry_category', $request->industry_category);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $drawings = $query->paginate(20)->withQueryString();

        // Get filter options
        $companies = MarketplaceSeller::all();

        // Statistics
        $stats = [
            'total_drawings' => TechnicalDrawing::count(),
            'pending_approval' => TechnicalDrawing::where('status', 'pending')->count(),
            'approved_drawings' => TechnicalDrawing::where('status', 'approved')->count(),
            'public_drawings' => TechnicalDrawing::where('visibility', 'public')->count(),
            'total_downloads' => TechnicalDrawing::sum('download_count'),
            'featured_drawings' => TechnicalDrawing::where('is_featured', true)->count(),
        ];

        return view('admin.technical.drawings.index', compact(
            'drawings',
            'companies',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = MarketplaceSeller::where('status', 'active')->get();

        return view('admin.technical.drawings.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'drawing_type' => 'required|in:Assembly,Detail,Schematic,Layout,Wiring',
            'scale' => 'nullable|string',
            'units' => 'required|in:mm,inch,m',
            'project_name' => 'nullable|string|max:255',
            'part_number' => 'nullable|string|max:255',
            'material_specification' => 'nullable|string',
            'industry_category' => 'nullable|string',
            'application_area' => 'nullable|string',
            'visibility' => 'required|in:public,private,company_only',
            'license_type' => 'required|in:free,commercial,educational',
            'price' => 'nullable|numeric|min:0',
            'company_id' => 'nullable|exists:marketplace_sellers,id',
            'file' => 'required|file|mimes:pdf,dwg,dxf|max:50000', // 50MB max
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('technical/drawings', $filename, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        // Process tags and keywords
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if ($validated['keywords']) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }

        // Set creator
        $validated['created_by'] = Auth::guard('admin')->id();

        // Auto-approve if admin is creating
        $validated['status'] = 'approved';
        $validated['approved_at'] = now();
        $validated['approved_by'] = Auth::guard('admin')->id();

        $drawing = TechnicalDrawing::create($validated);

        return redirect()
            ->route('admin.technical.drawings.index')
            ->with('success', 'Bản vẽ kỹ thuật đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TechnicalDrawing $drawing)
    {
        $drawing->load(['creator', 'company', 'approvedBy', 'cadFiles', 'childDrawings']);

        // Increment view count
        $drawing->incrementViewCount();

        return view('admin.technical.drawings.show', compact('drawing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TechnicalDrawing $drawing)
    {
        $companies = MarketplaceSeller::where('status', 'active')->get();

        return view('admin.technical.drawings.edit', compact('drawing', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TechnicalDrawing $drawing)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'drawing_type' => 'required|in:Assembly,Detail,Schematic,Layout,Wiring',
            'scale' => 'nullable|string',
            'units' => 'required|in:mm,inch,m',
            'project_name' => 'nullable|string|max:255',
            'part_number' => 'nullable|string|max:255',
            'material_specification' => 'nullable|string',
            'industry_category' => 'nullable|string',
            'application_area' => 'nullable|string',
            'visibility' => 'required|in:public,private,company_only',
            'license_type' => 'required|in:free,commercial,educational',
            'price' => 'nullable|numeric|min:0',
            'company_id' => 'nullable|exists:marketplace_sellers,id',
            'status' => 'required|in:draft,pending,approved,rejected,archived',
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($drawing->file_path) {
                Storage::disk('public')->delete($drawing->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . Str::slug($validated['title']) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('technical/drawings', $filename, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        // Process tags and keywords
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if (isset($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }

        // Handle status changes
        if ($validated['status'] === 'approved' && $drawing->status !== 'approved') {
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::guard('admin')->id();
        }

        $drawing->update($validated);

        return redirect()
            ->route('admin.technical.drawings.index')
            ->with('success', 'Bản vẽ kỹ thuật đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TechnicalDrawing $drawing)
    {
        // Delete associated file
        if ($drawing->file_path) {
            Storage::disk('public')->delete($drawing->file_path);
        }

        $drawing->delete();

        return redirect()
            ->route('admin.technical.drawings.index')
            ->with('success', 'Bản vẽ kỹ thuật đã được xóa thành công!');
    }

    /**
     * Bulk approve drawings
     */
    public function bulkApprove(Request $request)
    {
        $drawingIds = $request->input('drawing_ids', []);

        TechnicalDrawing::whereIn('id', $drawingIds)
            ->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::guard('admin')->id(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã phê duyệt ' . count($drawingIds) . ' bản vẽ thành công!'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(TechnicalDrawing $drawing)
    {
        $drawing->update([
            'is_featured' => !$drawing->is_featured,
        ]);

        $status = $drawing->is_featured ? 'đã được đánh dấu nổi bật' : 'đã bỏ đánh dấu nổi bật';

        return response()->json([
            'success' => true,
            'message' => "Bản vẽ {$status}!",
            'is_featured' => $drawing->is_featured
        ]);
    }

    /**
     * Download drawing file
     */
    public function download(TechnicalDrawing $drawing)
    {
        if (!$drawing->canBeDownloaded()) {
            abort(403, 'Bản vẽ này không thể tải xuống');
        }

        $drawing->incrementDownloadCount();

        return Storage::disk('public')->download($drawing->file_path, $drawing->file_name);
    }
}
