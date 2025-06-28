<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaterialController extends Controller
{
    /**
     * Display materials database index
     */
    public function index(Request $request)
    {
        $query = Material::where('is_active', true);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        
        // Filter by material type
        if ($request->filled('type')) {
            $query->where('material_type', $request->get('type'));
        }
        
        // Sort options
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        $allowedSorts = ['name', 'code', 'density', 'tensile_strength', 'yield_strength', 'cost_per_kg', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $materials = $query->paginate(12);
        
        // Get filter options
        $categories = Cache::remember('material_categories', 3600, function() {
            return Material::where('is_active', true)
                          ->distinct()
                          ->pluck('category')
                          ->filter()
                          ->sort()
                          ->values();
        });
        
        $types = Cache::remember('material_types', 3600, function() {
            return Material::where('is_active', true)
                          ->distinct()
                          ->pluck('material_type')
                          ->filter()
                          ->sort()
                          ->values();
        });
        
        return view('technical.materials.index', compact('materials', 'categories', 'types'));
    }
    
    /**
     * Display material details
     */
    public function show(Material $material)
    {
        if (!$material->is_active) {
            abort(404);
        }
        
        // Increment view count
        $material->increment('view_count');
        
        // Get related materials
        $relatedMaterials = Material::where('is_active', true)
                                  ->where('id', '!=', $material->id)
                                  ->where(function($q) use ($material) {
                                      $q->where('category', $material->category)
                                        ->orWhere('material_type', $material->material_type);
                                  })
                                  ->limit(6)
                                  ->get();
        
        return view('technical.materials.show', compact('material', 'relatedMaterials'));
    }
    
    /**
     * Material comparison tool
     */
    public function compare(Request $request)
    {
        $materialIds = $request->get('materials', []);
        
        if (empty($materialIds)) {
            return redirect()->route('materials.index')
                           ->with('warning', 'Please select materials to compare');
        }
        
        $materials = Material::whereIn('id', $materialIds)
                           ->where('is_active', true)
                           ->get();
        
        if ($materials->count() < 2) {
            return redirect()->route('materials.index')
                           ->with('warning', 'Please select at least 2 materials to compare');
        }
        
        return view('technical.materials.compare', compact('materials'));
    }
    
    /**
     * Material cost calculator
     */
    public function calculator(Request $request)
    {
        $material = null;
        $calculation = null;
        
        if ($request->filled('material_id')) {
            $material = Material::where('is_active', true)
                              ->findOrFail($request->get('material_id'));
            
            if ($request->filled('quantity') && $request->filled('unit')) {
                $quantity = (float) $request->get('quantity');
                $unit = $request->get('unit');
                
                // Convert to kg if needed
                $quantityInKg = $this->convertToKg($quantity, $unit, $material->density);
                
                $calculation = [
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'quantity_kg' => $quantityInKg,
                    'cost_per_kg' => $material->cost_per_kg,
                    'total_cost' => $quantityInKg * $material->cost_per_kg,
                    'material' => $material
                ];
            }
        }
        
        $materials = Material::where('is_active', true)
                           ->orderBy('name')
                           ->get(['id', 'name', 'code']);
        
        return view('technical.materials.calculator', compact('materials', 'material', 'calculation'));
    }
    
    /**
     * Convert quantity to kg based on unit and density
     */
    private function convertToKg($quantity, $unit, $density)
    {
        switch ($unit) {
            case 'kg':
                return $quantity;
            case 'g':
                return $quantity / 1000;
            case 'ton':
                return $quantity * 1000;
            case 'm3':
                return $quantity * $density * 1000; // density in g/cm³
            case 'cm3':
                return $quantity * $density / 1000;
            case 'mm3':
                return $quantity * $density / 1000000;
            default:
                return $quantity; // assume kg
        }
    }
    
    /**
     * API endpoint for material search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $materials = Material::where('is_active', true)
                           ->where(function($q) use ($query) {
                               $q->where('name', 'LIKE', "%{$query}%")
                                 ->orWhere('code', 'LIKE', "%{$query}%");
                           })
                           ->limit(10)
                           ->get(['id', 'name', 'code', 'category']);
        
        return response()->json($materials);
    }
    
    /**
     * Export materials data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $materials = Material::where('is_active', true)->get();
        
        if ($format === 'json') {
            return response()->json($materials);
        }
        
        // CSV export
        $filename = 'materials_database_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($materials) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Name', 'Code', 'Category', 'Type', 'Density (g/cm³)', 
                'Tensile Strength (MPa)', 'Yield Strength (MPa)', 
                'Cost per kg (USD)', 'Availability'
            ]);
            
            foreach ($materials as $material) {
                fputcsv($file, [
                    $material->name,
                    $material->code,
                    $material->category,
                    $material->material_type,
                    $material->density,
                    $material->tensile_strength,
                    $material->yield_strength,
                    $material->cost_per_kg,
                    $material->availability
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
