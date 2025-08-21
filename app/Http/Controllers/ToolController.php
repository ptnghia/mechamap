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
        // Get documentation with filters (only published)
        $query = Documentation::with('category')
                              ->where('status', 'published');

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

        $documentation = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get categories with document counts
        $categories = DocumentationCategory::where('is_active', true)
                                         ->withCount(['documentations' => function($query) {
                                             $query->where('status', 'published');
                                         }])
                                         ->orderBy('name')
                                         ->get();

        // Calculate statistics
        $totalDocs = Documentation::where('status', 'published')->count();
        $totalCategories = DocumentationCategory::where('is_active', true)->count();
        $totalViews = Documentation::where('status', 'published')->sum('view_count');
        $totalDownloads = Documentation::where('status', 'published')->sum('download_count');

        return view('tools.libraries.documentation.index', compact(
            'documentation',
            'categories',
            'totalDocs',
            'totalCategories',
            'totalViews',
            'totalDownloads'
        ));
    }

    /**
     * Documentation Detail
     */
    public function documentationShow(Documentation $documentation)
    {
        return view('tools.libraries.documentation.show', compact('documentation'));
    }
}
