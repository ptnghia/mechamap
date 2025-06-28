<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Material::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('grade', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by material type
        if ($request->filled('material_type')) {
            $query->where('material_type', $request->material_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by hazardous
        if ($request->filled('hazardous')) {
            $query->where('hazardous', $request->boolean('hazardous'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $materials = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_materials' => Material::count(),
            'approved_materials' => Material::where('status', 'approved')->count(),
            'pending_materials' => Material::where('status', 'pending')->count(),
            'hazardous_materials' => Material::where('hazardous', true)->count(),
            'metal_materials' => Material::where('category', 'Metal')->count(),
            'polymer_materials' => Material::where('category', 'Polymer')->count(),
        ];

        return view('admin.technical.materials.index', compact('materials', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.technical.materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:materials,code',
            'description' => 'nullable|string',
            'category' => 'required|in:Metal,Polymer,Ceramic,Composite',
            'subcategory' => 'nullable|string|max:255',
            'material_type' => 'required|in:Structural,Tool,Special',
            'grade' => 'nullable|string|max:255',

            // Physical properties
            'density' => 'nullable|numeric|min:0',
            'melting_point' => 'nullable|numeric',
            'thermal_conductivity' => 'nullable|numeric|min:0',
            'thermal_expansion' => 'nullable|numeric',
            'specific_heat' => 'nullable|numeric|min:0',
            'electrical_resistivity' => 'nullable|numeric|min:0',

            // Mechanical properties
            'youngs_modulus' => 'nullable|numeric|min:0',
            'shear_modulus' => 'nullable|numeric|min:0',
            'bulk_modulus' => 'nullable|numeric|min:0',
            'poissons_ratio' => 'nullable|numeric|min:0|max:0.5',
            'yield_strength' => 'nullable|numeric|min:0',
            'tensile_strength' => 'nullable|numeric|min:0',
            'compressive_strength' => 'nullable|numeric|min:0',
            'fatigue_strength' => 'nullable|numeric|min:0',
            'hardness_hb' => 'nullable|numeric|min:0',
            'hardness_hrc' => 'nullable|numeric|min:0',
            'impact_energy' => 'nullable|numeric|min:0',
            'elongation' => 'nullable|numeric|min:0|max:100',

            // Other fields
            'cost_per_kg' => 'nullable|numeric|min:0',
            'availability' => 'nullable|in:Common,Special Order,Limited,Discontinued',
            'hazardous' => 'boolean',
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
            'datasheet' => 'nullable|file|mimes:pdf|max:10000', // 10MB max
        ]);

        // Handle datasheet upload
        if ($request->hasFile('datasheet')) {
            $file = $request->file('datasheet');
            $filename = time() . '_' . $validated['code'] . '_datasheet.pdf';
            $filePath = $file->storeAs('materials/datasheets', $filename, 'public');
            $validated['datasheet_path'] = $filePath;
        }

        // Process tags and keywords
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if ($validated['keywords']) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }

        // Set creator and auto-approve
        $validated['created_by_user'] = Auth::guard('admin')->user()->name;
        $validated['status'] = 'approved';
        $validated['verified_by'] = Auth::guard('admin')->user()->name;
        $validated['verified_at'] = now();

        $material = Material::create($validated);

        return redirect()
            ->route('admin.technical.materials.index')
            ->with('success', 'Vật liệu đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        // Increment view count
        $material->incrementViewCount();

        return view('admin.technical.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('admin.technical.materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:materials,code,' . $material->id,
            'description' => 'nullable|string',
            'category' => 'required|in:Metal,Polymer,Ceramic,Composite',
            'subcategory' => 'nullable|string|max:255',
            'material_type' => 'required|in:Structural,Tool,Special',
            'grade' => 'nullable|string|max:255',

            // Physical properties
            'density' => 'nullable|numeric|min:0',
            'melting_point' => 'nullable|numeric',
            'thermal_conductivity' => 'nullable|numeric|min:0',
            'thermal_expansion' => 'nullable|numeric',
            'specific_heat' => 'nullable|numeric|min:0',
            'electrical_resistivity' => 'nullable|numeric|min:0',

            // Mechanical properties
            'youngs_modulus' => 'nullable|numeric|min:0',
            'shear_modulus' => 'nullable|numeric|min:0',
            'bulk_modulus' => 'nullable|numeric|min:0',
            'poissons_ratio' => 'nullable|numeric|min:0|max:0.5',
            'yield_strength' => 'nullable|numeric|min:0',
            'tensile_strength' => 'nullable|numeric|min:0',
            'compressive_strength' => 'nullable|numeric|min:0',
            'fatigue_strength' => 'nullable|numeric|min:0',
            'hardness_hb' => 'nullable|numeric|min:0',
            'hardness_hrc' => 'nullable|numeric|min:0',
            'impact_energy' => 'nullable|numeric|min:0',
            'elongation' => 'nullable|numeric|min:0|max:100',

            // Other fields
            'cost_per_kg' => 'nullable|numeric|min:0',
            'availability' => 'nullable|in:Common,Special Order,Limited,Discontinued',
            'hazardous' => 'boolean',
            'status' => 'required|in:draft,pending,approved,deprecated',
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
        ]);

        // Handle datasheet upload if new file provided
        if ($request->hasFile('datasheet')) {
            // Delete old file
            if ($material->datasheet_path) {
                Storage::disk('public')->delete($material->datasheet_path);
            }

            $file = $request->file('datasheet');
            $filename = time() . '_' . $validated['code'] . '_datasheet.pdf';
            $filePath = $file->storeAs('materials/datasheets', $filename, 'public');
            $validated['datasheet_path'] = $filePath;
        }

        // Process tags and keywords
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if (isset($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }

        $material->update($validated);

        return redirect()
            ->route('admin.technical.materials.index')
            ->with('success', 'Vật liệu đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        // Delete associated datasheet
        if ($material->datasheet_path) {
            Storage::disk('public')->delete($material->datasheet_path);
        }

        $material->delete();

        return redirect()
            ->route('admin.technical.materials.index')
            ->with('success', 'Vật liệu đã được xóa thành công!');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Material $material)
    {
        $material->update([
            'is_featured' => !$material->is_featured,
        ]);

        $status = $material->is_featured ? 'đã được đánh dấu nổi bật' : 'đã bỏ đánh dấu nổi bật';

        return response()->json([
            'success' => true,
            'message' => "Vật liệu {$status}!",
            'is_featured' => $material->is_featured
        ]);
    }

    /**
     * Download datasheet
     */
    public function downloadDatasheet(Material $material)
    {
        if (!$material->datasheet_path) {
            abort(404, 'Không tìm thấy datasheet');
        }

        return Storage::disk('public')->download($material->datasheet_path, $material->code . '_datasheet.pdf');
    }

    /**
     * Get material comparison data
     */
    public function compare(Request $request)
    {
        $materialIds = $request->input('materials', []);

        if (count($materialIds) < 2 || count($materialIds) > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn từ 2 đến 5 vật liệu để so sánh'
            ], 400);
        }

        $materials = Material::whereIn('id', $materialIds)->get();

        return response()->json([
            'success' => true,
            'materials' => $materials,
            'comparison_data' => $this->generateComparisonData($materials)
        ]);
    }

    /**
     * Generate comparison data for materials
     */
    private function generateComparisonData($materials)
    {
        $properties = [
            'density', 'melting_point', 'thermal_conductivity',
            'youngs_modulus', 'yield_strength', 'tensile_strength',
            'hardness_hb', 'cost_per_kg'
        ];

        $comparison = [];

        foreach ($properties as $property) {
            $values = $materials->pluck($property)->filter()->values();
            if ($values->count() > 0) {
                $comparison[$property] = [
                    'min' => $values->min(),
                    'max' => $values->max(),
                    'avg' => round($values->avg(), 2),
                ];
            }
        }

        return $comparison;
    }
}
