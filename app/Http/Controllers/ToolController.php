<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\EngineeringStandard;
use App\Models\ManufacturingProcess;
use App\Models\CADFile;
use App\Models\Documentation;
use App\Models\DocumentationCategory;

class ToolController extends Controller
{
    /**
     * Hiển thị trang tổng quan tools
     */
    public function index()
    {
        return view('tools.index');
    }

    /**
     * CALCULATORS
     */

    /**
     * Material Calculator
     */
    public function materialCalculator()
    {
        return view('tools.calculators.material-calculator');
    }

    /**
     * Process Calculator
     */
    public function processCalculator()
    {
        $processes = ManufacturingProcess::where('is_active', true)->get();
        return view('tools.calculators.process-calculator', compact('processes'));
    }

    /**
     * DATABASES
     */

    /**
     * Materials Database
     */
    public function materials(Request $request)
    {
        $query = Material::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $materials = $query->paginate(12);
        $categories = Material::distinct('category')->pluck('category');

        return view('tools.databases.materials.index', compact('materials', 'categories'));
    }

    /**
     * Material Detail
     */
    public function materialShow(Material $material)
    {
        // Get related materials (same category, excluding current material)
        $relatedMaterials = Material::where('category', $material->category)
            ->where('id', '!=', $material->id)
            ->where('is_active', true)
            ->limit(6)
            ->get();

        return view('tools.databases.materials.show', compact('material', 'relatedMaterials'));
    }

    /**
     * Standards Database
     */
    public function standards(Request $request)
    {
        $query = EngineeringStandard::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%");
            });
        }

        if ($request->filled('organization')) {
            $query->where('organization', $request->get('organization'));
        }

        $standards = $query->paginate(12);
        $organizations = EngineeringStandard::distinct('organization')->pluck('organization');

        return view('tools.databases.standards.index', compact('standards', 'organizations'));
    }

    /**
     * Manufacturing Processes Database
     */
    public function processes(Request $request)
    {
        $query = ManufacturingProcess::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $processes = $query->paginate(12);
        $categories = ManufacturingProcess::distinct('category')->pluck('category');
        $skillLevels = ['beginner', 'intermediate', 'advanced', 'expert'];

        return view('tools.databases.processes.index', compact('processes', 'categories', 'skillLevels'));
    }

    /**
     * LIBRARIES
     */

    /**
     * CAD Library
     */
    public function cadLibrary(Request $request)
    {
        $query = CADFile::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('industry_category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('industry_category', $request->get('category'));
        }

        if ($request->filled('file_type')) {
            $query->where('file_extension', $request->get('file_type'));
        }

        $cadFiles = $query->paginate(12);
        $categories = CADFile::distinct('industry_category')->pluck('industry_category');
        $fileTypes = CADFile::distinct('file_extension')->pluck('file_extension');
        $softwareOptions = CADFile::distinct('cad_software')->pluck('cad_software');

        return view('tools.libraries.cad.index', compact('cadFiles', 'categories', 'fileTypes', 'softwareOptions'));
    }

    /**
     * CAD File Detail
     */
    public function cadFileShow(CADFile $cadFile)
    {
        // Get related files (same category or similar tags)
        $relatedFiles = CADFile::where('id', '!=', $cadFile->id)
            ->where('industry_category', $cadFile->industry_category)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->limit(5)
            ->get();

        return view('tools.libraries.cad.show', compact('cadFile', 'relatedFiles'));
    }

    /**
     * Technical Documentation
     */
    public function technicalDocs()
    {
        return view('tools.libraries.technical-docs.index');
    }

    /**
     * Documentation Library
     */
    public function documentation(Request $request)
    {
        // Get featured documentation
        $featuredDocs = Documentation::with(['category', 'author'])
                                    ->where('status', 'published')
                                    ->where('is_featured', true)
                                    ->where('is_public', true)
                                    ->orderBy('created_at', 'desc')
                                    ->limit(4)
                                    ->get();

        // Get recent documentation with filters
        $query = Documentation::with(['category', 'author'])
                              ->where('status', 'published')
                              ->where('is_public', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('content_type')) {
            $query->where('content_type', $request->get('content_type'));
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->get('difficulty_level'));
        }

        // Sort options
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'downloads':
                $query->orderBy('download_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_average', 'desc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $documentation = $query->paginate(12);

        // Get categories with document counts
        $categories = DocumentationCategory::where('is_active', true)
                                         ->where('is_public', true)
                                         ->withCount(['documentations' => function($query) {
                                             $query->where('status', 'published')
                                                   ->where('is_public', true);
                                         }])
                                         ->orderBy('sort_order')
                                         ->get();

        // Calculate statistics
        $stats = [
            'total_docs' => Documentation::where('status', 'published')->where('is_public', true)->count(),
            'total_categories' => $categories->count(),
            'total_views' => Documentation::where('status', 'published')->where('is_public', true)->sum('view_count'),
            'total_downloads' => Documentation::where('status', 'published')->where('is_public', true)->sum('download_count'),
        ];

        return view('tools.libraries.documentation.index', compact(
            'documentation',
            'featuredDocs',
            'categories',
            'stats'
        ));
    }

    /**
     * Documentation Detail
     */
    public function documentationShow(Documentation $documentation)
    {
        // Check access permission
        if (!$documentation->is_public && (!auth()->check() || !$documentation->canAccess(auth()->user()))) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        // Record view if user is authenticated
        if (auth()->check()) {
            $documentation->recordView(auth()->user());
        } else {
            // Increment view count for anonymous users
            $documentation->increment('view_count');
        }

        // Load relationships
        $documentation->load(['category', 'author']);

        // Get user rating if authenticated
        $userRating = null;
        if (auth()->check()) {
            $userRating = $documentation->ratings()
                                      ->where('user_id', auth()->id())
                                      ->first();
        }

        // Get comments with pagination
        $comments = $documentation->comments()
                                 ->with(['user', 'replies.user'])
                                 ->whereNull('parent_id')
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(10);

        // Get related documents (same category, excluding current)
        $relatedDocs = Documentation::where('status', 'published')
                                   ->where('is_public', true)
                                   ->where('id', '!=', $documentation->id)
                                   ->where('category_id', $documentation->category_id)
                                   ->orderBy('view_count', 'desc')
                                   ->limit(5)
                                   ->get();

        return view('tools.libraries.documentation.show', compact(
            'documentation',
            'userRating',
            'comments',
            'relatedDocs'
        ));
    }
}
