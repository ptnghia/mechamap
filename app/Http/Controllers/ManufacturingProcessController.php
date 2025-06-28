<?php

namespace App\Http\Controllers;

use App\Models\ManufacturingProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ManufacturingProcessController extends Controller
{
    /**
     * Display manufacturing processes index
     */
    public function index(Request $request)
    {
        $query = ManufacturingProcess::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        
        // Filter by skill level
        if ($request->filled('skill_level')) {
            $query->where('skill_level_required', $request->get('skill_level'));
        }
        
        // Sort options
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        $allowedSorts = ['name', 'category', 'cost_per_hour', 'production_rate', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $processes = $query->paginate(12);
        
        // Get filter options
        $categories = Cache::remember('process_categories', 3600, function() {
            return ManufacturingProcess::distinct()
                                     ->pluck('category')
                                     ->filter()
                                     ->sort()
                                     ->values();
        });
        
        $skillLevels = ['basic', 'intermediate', 'advanced', 'expert'];
        
        return view('technical.manufacturing.processes.index', compact(
            'processes', 
            'categories', 
            'skillLevels'
        ));
    }
    
    /**
     * Display process details
     */
    public function show(ManufacturingProcess $process)
    {
        // Get related processes
        $relatedProcesses = ManufacturingProcess::where('id', '!=', $process->id)
                                              ->where(function($q) use ($process) {
                                                  $q->where('category', $process->category)
                                                    ->orWhereJsonContains('materials_compatible', $process->materials_compatible);
                                              })
                                              ->limit(6)
                                              ->get();
        
        return view('technical.manufacturing.processes.show', compact('process', 'relatedProcesses'));
    }
    
    /**
     * Process selector tool
     */
    public function selector(Request $request)
    {
        $selectedProcess = null;
        $recommendations = [];
        
        if ($request->filled('material') || $request->filled('quantity') || $request->filled('tolerance')) {
            $query = ManufacturingProcess::query();
            
            // Filter by material compatibility
            if ($request->filled('material')) {
                $material = $request->get('material');
                $query->whereJsonContains('materials_compatible', $material);
            }
            
            // Filter by production volume
            if ($request->filled('quantity')) {
                $quantity = (int) $request->get('quantity');
                if ($quantity < 100) {
                    $query->where('category', 'machining');
                } elseif ($quantity < 1000) {
                    $query->whereIn('category', ['machining', 'forming']);
                } else {
                    $query->whereIn('category', ['forming', 'casting', 'additive']);
                }
            }
            
            $recommendations = $query->orderBy('cost_per_hour')
                                   ->limit(5)
                                   ->get();
        }
        
        $materials = Cache::remember('process_materials', 3600, function() {
            return ManufacturingProcess::select('materials_compatible')
                                     ->get()
                                     ->pluck('materials_compatible')
                                     ->flatten()
                                     ->unique()
                                     ->sort()
                                     ->values();
        });
        
        return view('technical.manufacturing.processes.selector', compact(
            'recommendations', 
            'materials'
        ));
    }
    
    /**
     * Cost calculator
     */
    public function calculator(Request $request)
    {
        $process = null;
        $calculation = null;
        
        if ($request->filled('process_id')) {
            $process = ManufacturingProcess::findOrFail($request->get('process_id'));
            
            if ($request->filled('quantity') && $request->filled('setup_time')) {
                $quantity = (int) $request->get('quantity');
                $setupTime = (float) $request->get('setup_time', $process->setup_time);
                $cycleTime = (float) $request->get('cycle_time', $process->cycle_time);
                
                $totalTime = $setupTime + ($quantity * $cycleTime);
                $totalCost = $totalTime * $process->cost_per_hour;
                
                $calculation = [
                    'quantity' => $quantity,
                    'setup_time' => $setupTime,
                    'cycle_time' => $cycleTime,
                    'total_time' => $totalTime,
                    'cost_per_hour' => $process->cost_per_hour,
                    'total_cost' => $totalCost,
                    'cost_per_unit' => $totalCost / $quantity,
                    'process' => $process
                ];
            }
        }
        
        $processes = ManufacturingProcess::orderBy('name')->get(['id', 'name', 'category']);
        
        return view('technical.manufacturing.processes.calculator', compact(
            'processes', 
            'process', 
            'calculation'
        ));
    }
    
    /**
     * Compare processes
     */
    public function compare(Request $request)
    {
        $processIds = $request->get('processes', []);
        
        if (count($processIds) < 2) {
            return redirect()->route('manufacturing.processes.index')
                           ->with('warning', 'Please select at least 2 processes to compare');
        }
        
        $processes = ManufacturingProcess::whereIn('id', $processIds)->get();
        
        return view('technical.manufacturing.processes.compare', compact('processes'));
    }
    
    /**
     * API endpoint for process search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $processes = ManufacturingProcess::where(function($q) use ($query) {
                                           $q->where('name', 'LIKE', "%{$query}%")
                                             ->orWhere('category', 'LIKE', "%{$query}%");
                                       })
                                       ->limit(10)
                                       ->get(['id', 'name', 'category']);
        
        return response()->json($processes);
    }
    
    /**
     * Export processes data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $processes = ManufacturingProcess::all();
        
        if ($format === 'json') {
            return response()->json($processes);
        }
        
        // CSV export
        $filename = 'manufacturing_processes_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($processes) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Name', 'Category', 'Description', 'Setup Time', 'Cycle Time', 
                'Cost per Hour', 'Production Rate', 'Skill Level', 'Equipment Required'
            ]);
            
            foreach ($processes as $process) {
                fputcsv($file, [
                    $process->name,
                    $process->category,
                    $process->description,
                    $process->setup_time,
                    $process->cycle_time,
                    $process->cost_per_hour,
                    $process->production_rate,
                    $process->skill_level_required,
                    is_array($process->equipment_required) ? implode(', ', $process->equipment_required) : $process->equipment_required
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
