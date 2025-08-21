<?php

namespace App\Http\Controllers;

use App\Models\CADFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CADLibraryController extends Controller
{
    /**
     * Display CAD library index
     */
    public function index(Request $request)
    {
        $query = CADFile::with(['user', 'category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('tags', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by file type
        if ($request->filled('file_type')) {
            $query->where('file_type', $request->get('file_type'));
        }

        // Filter by software
        if ($request->filled('software')) {
            $query->where('software_used', $request->get('software'));
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        $allowedSorts = ['title', 'created_at', 'download_count', 'file_size', 'rating'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $cadFiles = $query->paginate(12);

        // Get filter options
        $categories = Cache::remember('cad_categories', 3600, function() {
            return \App\Models\ProductCategory::orderBy('name')->get(['id', 'name']);
        });

        $fileTypes = ['dwg', 'step', 'iges', 'stl', 'obj', 'solidworks', 'inventor', 'fusion360'];
        $softwareOptions = ['AutoCAD', 'SolidWorks', 'Inventor', 'Fusion 360', 'CATIA', 'NX', 'Creo'];

        return view('technical.cad.library.index', compact(
            'cadFiles',
            'categories',
            'fileTypes',
            'softwareOptions'
        ));
    }

    /**
     * Display CAD file details
     */
    public function show(CADFile $cadFile)
    {
        $cadFile->load(['user', 'category', 'comments.user']);

        // Increment view count
        $cadFile->increment('view_count');

        // Get related CAD files
        $relatedFiles = CADFile::where('id', '!=', $cadFile->id)
                              ->where(function($q) use ($cadFile) {
                                  $q->where('category_id', $cadFile->category_id)
                                    ->orWhere('software_used', $cadFile->software_used);
                              })
                              ->limit(6)
                              ->get();

        return view('technical.cad.library.show', compact('cadFile', 'relatedFiles'));
    }

    /**
     * Upload new CAD file
     */
    public function create()
    {
        $this->authorize('upload-cad-files');

        $categories = \App\Models\ProductCategory::orderBy('name')->get(['id', 'name']);
        $softwareOptions = ['AutoCAD', 'SolidWorks', 'Inventor', 'Fusion 360', 'CATIA', 'NX', 'Creo'];

        return view('technical.cad.library.create', compact('categories', 'softwareOptions'));
    }

    /**
     * Store new CAD file
     */
    public function store(Request $request)
    {
        $this->authorize('upload-cad-files');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'category_id' => 'required|exists:product_categories,id',
            'software_used' => 'required|string',
            'file_type' => 'required|string',
            'tags' => 'nullable|string',
            'is_public' => 'boolean',
            'license_type' => 'required|in:free,commercial,educational',
            'cad_file' => 'required|file|mimes:dwg,step,iges,stl,obj,sldprt,ipt,f3d|max:51200', // 50MB
            'preview_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'technical_specs' => 'nullable|array'
        ]);

        // Store CAD file
        $cadFilePath = $request->file('cad_file')->store('cad-files', 'public');

        // Store preview image if provided
        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $previewPath = $request->file('preview_image')->store('cad-previews', 'public');
        }

        $cadFile = CADFile::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'user_id' => Auth::id(),
            'file_path' => $cadFilePath,
            'preview_image' => $previewPath,
            'file_type' => $validated['file_type'],
            'file_size' => $request->file('cad_file')->getSize(),
            'software_used' => $validated['software_used'],
            'tags' => $validated['tags'],
            'is_public' => $validated['is_public'] ?? true,
            'license_type' => $validated['license_type'],
            'technical_specs' => $validated['technical_specs'] ?? [],
            'status' => 'pending_review'
        ]);

        return redirect()->route('cad.library.show', $cadFile)
                        ->with('success', 'CAD file uploaded successfully! It will be reviewed before being published.');
    }

    /**
     * Download CAD file
     */
    public function download(CADFile $cadFile)
    {
        $this->authorize('download', $cadFile);

        if (!Storage::disk('public')->exists($cadFile->file_path)) {
            abort(404, 'File not found');
        }

        // Increment download count
        $cadFile->increment('download_count');

        // Log download activity
        \App\Models\UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => 'cad_download',
            'description' => "Downloaded CAD file: {$cadFile->title}",
            'metadata' => [
                'cad_file_id' => $cadFile->id,
                'file_type' => $cadFile->file_type,
                'file_size' => $cadFile->file_size
            ]
        ]);

        return Storage::disk('public')->download(
            $cadFile->file_path,
            $cadFile->title . '.' . $cadFile->file_type
        );
    }

    /**
     * Rate CAD file
     */
    public function rate(Request $request, CADFile $cadFile)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        // Check if user already rated this file
        $existingRating = $cadFile->ratings()
                                 ->where('user_id', Auth::id())
                                 ->first();

        if ($existingRating) {
            $existingRating->update($validated);
        } else {
            $cadFile->ratings()->create([
                'user_id' => Auth::id(),
                'rating' => $validated['rating'],
                'review' => $validated['review']
            ]);
        }

        // Update average rating
        $avgRating = $cadFile->ratings()->avg('rating');
        $cadFile->update(['average_rating' => $avgRating]);

        return back()->with('success', 'Rating submitted successfully!');
    }

    /**
     * Add comment to CAD file
     */
    public function comment(Request $request, CADFile $cadFile)
    {
        $validated = $request->validate([
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $cadFile->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $validated['comment']
        ]);

        return back()->with('success', 'Comment added successfully!');
    }

    /**
     * Get CAD file statistics
     */
    public function getStats()
    {
        $stats = [
            'total_files' => CADFile::count(),
            'total_downloads' => CADFile::sum('download_count'),
            'popular_software' => CADFile::select('software_used')
                                        ->selectRaw('COUNT(*) as count')
                                        ->groupBy('software_used')
                                        ->orderByDesc('count')
                                        ->limit(5)
                                        ->get(),
            'recent_uploads' => CADFile::latest()->limit(5)->get(['id', 'title', 'created_at'])
        ];

        return response()->json($stats);
    }

    /**
     * Search CAD files API
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $files = CADFile::where(function($q) use ($query) {
                           $q->where('title', 'LIKE', "%{$query}%")
                             ->orWhere('tags', 'LIKE', "%{$query}%");
                       })
                       ->limit(10)
                       ->get(['id', 'title', 'file_type', 'software_used']);

        return response()->json($files);
    }

    /**
     * Export CAD library data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');

        $files = CADFile::with(['user', 'category'])->get();

        if ($format === 'json') {
            return response()->json($files);
        }

        // CSV export
        $filename = 'cad_library_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($files) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Title', 'Category', 'File Type', 'Software', 'File Size (KB)',
                'Downloads', 'Rating', 'Uploaded By', 'Upload Date'
            ]);

            foreach ($files as $cadFile) {
                fputcsv($file, [
                    $cadFile->title,
                    $cadFile->category->name ?? 'N/A',
                    $cadFile->file_type,
                    $cadFile->software_used,
                    round($cadFile->file_size / 1024, 2),
                    $cadFile->download_count,
                    $cadFile->average_rating ?? 'N/A',
                    $cadFile->user->name ?? 'N/A',
                    $cadFile->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * My uploaded files
     */
    public function myFiles(Request $request)
    {
        $query = CADFile::where('created_by', Auth::id())
                       ->with(['category', 'ratings']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        $cadFiles = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calculate stats
        $allFiles = CADFile::where('created_by', Auth::id());
        $stats = [
            'total' => $allFiles->count(),
            'approved' => $allFiles->where('status', 'approved')->count(),
            'pending' => $allFiles->where('status', 'pending')->count(),
            'rejected' => $allFiles->where('status', 'rejected')->count(),
            'total_downloads' => $allFiles->sum('download_count'),
        ];

        return view('technical.cad.library.my-files', compact('cadFiles', 'stats'));
    }
}
