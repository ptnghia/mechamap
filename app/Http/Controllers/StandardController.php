<?php

namespace App\Http\Controllers;

use App\Models\EngineeringStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StandardController extends Controller
{
    /**
     * Display standards library index
     */
    public function index(Request $request)
    {
        $query = EngineeringStandard::where('status', 'active');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('organization', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        
        // Filter by organization
        if ($request->filled('organization')) {
            $query->where('organization', $request->get('organization'));
        }
        
        // Sort options
        $sortBy = $request->get('sort', 'code');
        $sortOrder = $request->get('order', 'asc');
        
        $allowedSorts = ['code', 'title', 'organization', 'version', 'effective_date', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $standards = $query->paginate(12);
        
        // Get filter options
        $categories = Cache::remember('standard_categories', 3600, function() {
            return EngineeringStandard::where('status', 'active')
                                    ->distinct()
                                    ->pluck('category')
                                    ->filter()
                                    ->sort()
                                    ->values();
        });
        
        $organizations = Cache::remember('standard_organizations', 3600, function() {
            return EngineeringStandard::where('status', 'active')
                                    ->distinct()
                                    ->pluck('organization')
                                    ->filter()
                                    ->sort()
                                    ->values();
        });
        
        return view('technical.standards.index', compact('standards', 'categories', 'organizations'));
    }
    
    /**
     * Display standard details
     */
    public function show(EngineeringStandard $standard)
    {
        if ($standard->status !== 'active') {
            abort(404);
        }
        
        // Get related standards
        $relatedStandards = EngineeringStandard::where('status', 'active')
                                             ->where('id', '!=', $standard->id)
                                             ->where(function($q) use ($standard) {
                                                 $q->where('category', $standard->category)
                                                   ->orWhere('organization', $standard->organization);
                                             })
                                             ->limit(6)
                                             ->get();
        
        return view('technical.standards.show', compact('standard', 'relatedStandards'));
    }
    
    /**
     * Standards compliance checker
     */
    public function complianceChecker(Request $request)
    {
        $selectedStandards = [];
        $complianceResults = [];
        
        if ($request->filled('standards')) {
            $standardIds = $request->get('standards', []);
            $selectedStandards = EngineeringStandard::whereIn('id', $standardIds)
                                                  ->where('status', 'active')
                                                  ->get();
            
            // Mock compliance check results
            foreach ($selectedStandards as $standard) {
                $complianceResults[] = [
                    'standard' => $standard,
                    'compliance_level' => rand(70, 100),
                    'requirements_met' => rand(15, 25),
                    'total_requirements' => 25,
                    'critical_issues' => rand(0, 3),
                    'recommendations' => $this->generateRecommendations($standard)
                ];
            }
        }
        
        $allStandards = EngineeringStandard::where('status', 'active')
                                         ->orderBy('code')
                                         ->get(['id', 'code', 'title', 'category']);
        
        return view('technical.standards.compliance-checker', compact(
            'allStandards', 
            'selectedStandards', 
            'complianceResults'
        ));
    }
    
    /**
     * Generate mock recommendations for compliance
     */
    private function generateRecommendations($standard)
    {
        $recommendations = [
            'quality' => [
                'Implement quality management system documentation',
                'Establish regular audit procedures',
                'Define clear quality objectives and metrics'
            ],
            'safety' => [
                'Conduct regular safety assessments',
                'Implement proper safety training programs',
                'Establish emergency response procedures'
            ],
            'environmental' => [
                'Implement environmental monitoring systems',
                'Establish waste management procedures',
                'Define environmental impact assessment protocols'
            ],
            'fasteners' => [
                'Verify material specifications and certifications',
                'Implement proper torque specifications',
                'Establish inspection and testing procedures'
            ],
            'tolerances' => [
                'Define clear dimensional tolerance requirements',
                'Implement statistical process control',
                'Establish measurement and calibration procedures'
            ]
        ];
        
        return $recommendations[$standard->category] ?? [
            'Review standard requirements thoroughly',
            'Implement proper documentation procedures',
            'Establish regular compliance monitoring'
        ];
    }
    
    /**
     * Standards comparison tool
     */
    public function compare(Request $request)
    {
        $standardIds = $request->get('standards', []);
        
        if (count($standardIds) < 2) {
            return redirect()->route('standards.index')
                           ->with('warning', 'Please select at least 2 standards to compare');
        }
        
        $standards = EngineeringStandard::whereIn('id', $standardIds)
                                      ->where('status', 'active')
                                      ->get();
        
        return view('technical.standards.compare', compact('standards'));
    }
    
    /**
     * API endpoint for standard search
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $standards = EngineeringStandard::where('status', 'active')
                                      ->where(function($q) use ($query) {
                                          $q->where('code', 'LIKE', "%{$query}%")
                                            ->orWhere('title', 'LIKE', "%{$query}%");
                                      })
                                      ->limit(10)
                                      ->get(['id', 'code', 'title', 'organization']);
        
        return response()->json($standards);
    }
    
    /**
     * Export standards data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $standards = EngineeringStandard::where('status', 'active')->get();
        
        if ($format === 'json') {
            return response()->json($standards);
        }
        
        // CSV export
        $filename = 'engineering_standards_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($standards) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Code', 'Title', 'Organization', 'Category', 'Version', 
                'Status', 'Effective Date', 'Review Date', 'Description'
            ]);
            
            foreach ($standards as $standard) {
                fputcsv($file, [
                    $standard->code,
                    $standard->title,
                    $standard->organization,
                    $standard->category,
                    $standard->version,
                    $standard->status,
                    $standard->effective_date ? $standard->effective_date->format('Y-m-d') : '',
                    $standard->review_date ? $standard->review_date->format('Y-m-d') : '',
                    $standard->description
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
