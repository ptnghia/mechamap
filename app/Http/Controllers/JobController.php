<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display jobs index
     */
    public function index(Request $request)
    {
        // Mock jobs data - replace with actual model when implemented
        $jobs = collect([
            [
                'id' => 1,
                'title' => 'Senior Mechanical Engineer',
                'company' => 'Vietnam Manufacturing Corp',
                'location' => 'Ho Chi Minh City',
                'type' => 'full_time',
                'experience_level' => 'senior',
                'salary_min' => 25000000,
                'salary_max' => 40000000,
                'currency' => 'VND',
                'description' => 'We are seeking an experienced mechanical engineer to lead our product development team.',
                'requirements' => [
                    '5+ years experience in mechanical design',
                    'Proficiency in SolidWorks/AutoCAD',
                    'Experience with manufacturing processes',
                    'Strong problem-solving skills'
                ],
                'benefits' => [
                    'Competitive salary',
                    'Health insurance',
                    'Professional development',
                    'Flexible working hours'
                ],
                'posted_date' => '2024-01-15',
                'deadline' => '2024-02-15',
                'status' => 'active',
                'applications_count' => 12,
                'views_count' => 156,
                'remote_allowed' => false,
                'category' => 'engineering'
            ],
            [
                'id' => 2,
                'title' => 'CAD Designer',
                'company' => 'Design Solutions Ltd',
                'location' => 'Hanoi',
                'type' => 'contract',
                'experience_level' => 'mid',
                'salary_min' => 15000000,
                'salary_max' => 25000000,
                'currency' => 'VND',
                'description' => 'Looking for a skilled CAD designer to work on various mechanical projects.',
                'requirements' => [
                    '3+ years CAD experience',
                    'Knowledge of GD&T',
                    'Experience with 3D modeling',
                    'Attention to detail'
                ],
                'benefits' => [
                    'Project-based compensation',
                    'Remote work options',
                    'Skill development opportunities'
                ],
                'posted_date' => '2024-01-20',
                'deadline' => '2024-02-20',
                'status' => 'active',
                'applications_count' => 8,
                'views_count' => 89,
                'remote_allowed' => true,
                'category' => 'design'
            ],
            [
                'id' => 3,
                'title' => 'Manufacturing Engineer',
                'company' => 'Industrial Automation Co',
                'location' => 'Da Nang',
                'type' => 'full_time',
                'experience_level' => 'junior',
                'salary_min' => 12000000,
                'salary_max' => 18000000,
                'currency' => 'VND',
                'description' => 'Entry-level position for recent graduates in manufacturing engineering.',
                'requirements' => [
                    'Bachelor degree in Mechanical Engineering',
                    'Fresh graduate or 1-2 years experience',
                    'Knowledge of lean manufacturing',
                    'Willingness to learn'
                ],
                'benefits' => [
                    'Training program',
                    'Career advancement',
                    'Health benefits',
                    'Annual bonus'
                ],
                'posted_date' => '2024-01-25',
                'deadline' => '2024-02-25',
                'status' => 'active',
                'applications_count' => 25,
                'views_count' => 234,
                'remote_allowed' => false,
                'category' => 'manufacturing'
            ]
        ]);
        
        // Filter by type
        if ($request->filled('type')) {
            $jobs = $jobs->where('type', $request->get('type'));
        }
        
        // Filter by experience level
        if ($request->filled('experience_level')) {
            $jobs = $jobs->where('experience_level', $request->get('experience_level'));
        }
        
        // Filter by location
        if ($request->filled('location')) {
            $location = strtolower($request->get('location'));
            $jobs = $jobs->filter(function($job) use ($location) {
                return str_contains(strtolower($job['location']), $location);
            });
        }
        
        // Filter by remote work
        if ($request->filled('remote')) {
            $jobs = $jobs->where('remote_allowed', $request->get('remote') === '1');
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $jobs = $jobs->filter(function($job) use ($search) {
                return str_contains(strtolower($job['title']), $search) ||
                       str_contains(strtolower($job['company']), $search) ||
                       str_contains(strtolower($job['description']), $search);
            });
        }
        
        // Sort by date (newest first)
        $jobs = $jobs->sortByDesc('posted_date');
        
        // Pagination simulation
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedJobs = $jobs->slice($offset, $perPage)->values();
        
        $jobTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance'
        ];
        
        $experienceLevels = [
            'entry' => 'Entry Level',
            'junior' => 'Junior (1-3 years)',
            'mid' => 'Mid Level (3-5 years)',
            'senior' => 'Senior (5+ years)',
            'lead' => 'Lead/Manager'
        ];
        
        return view('community.jobs.index', compact('paginatedJobs', 'jobTypes', 'experienceLevels'));
    }
    
    /**
     * Display job details
     */
    public function show($id)
    {
        // Mock job data - replace with actual model when implemented
        $job = [
            'id' => $id,
            'title' => 'Senior Mechanical Engineer',
            'company' => 'Vietnam Manufacturing Corp',
            'company_logo' => '/images/companies/vmc-logo.png',
            'location' => 'Ho Chi Minh City, Vietnam',
            'type' => 'full_time',
            'experience_level' => 'senior',
            'salary_min' => 25000000,
            'salary_max' => 40000000,
            'currency' => 'VND',
            'description' => 'We are seeking an experienced mechanical engineer to lead our product development team and drive innovation in our manufacturing processes.',
            'full_description' => 'Join our dynamic team as a Senior Mechanical Engineer where you will be responsible for designing, developing, and testing mechanical systems and components. You will work closely with cross-functional teams to bring innovative products from concept to market.',
            'responsibilities' => [
                'Lead mechanical design projects from concept to production',
                'Perform engineering calculations and simulations',
                'Create detailed technical drawings and specifications',
                'Collaborate with manufacturing teams on production processes',
                'Mentor junior engineers and provide technical guidance',
                'Ensure compliance with industry standards and regulations'
            ],
            'requirements' => [
                'Bachelor\'s degree in Mechanical Engineering or related field',
                '5+ years of experience in mechanical design and development',
                'Proficiency in CAD software (SolidWorks, AutoCAD, Inventor)',
                'Strong knowledge of manufacturing processes and materials',
                'Experience with FEA and CFD analysis tools',
                'Excellent problem-solving and analytical skills',
                'Strong communication and leadership abilities'
            ],
            'preferred_qualifications' => [
                'Master\'s degree in Mechanical Engineering',
                'Professional Engineer (PE) license',
                'Experience in automotive or aerospace industry',
                'Knowledge of lean manufacturing principles',
                'Project management certification'
            ],
            'benefits' => [
                'Competitive salary package',
                'Comprehensive health insurance',
                'Annual performance bonus',
                'Professional development opportunities',
                'Flexible working arrangements',
                'Company-sponsored training and certifications'
            ],
            'posted_date' => '2024-01-15',
            'deadline' => '2024-02-15',
            'status' => 'active',
            'applications_count' => 12,
            'views_count' => 156,
            'remote_allowed' => false,
            'category' => 'engineering',
            'contact_email' => 'careers@vmc.com.vn',
            'contact_person' => 'Ms. Nguyen Thi HR'
        ];
        
        return view('community.jobs.show', compact('job'));
    }
    
    /**
     * Apply for job
     */
    public function apply(Request $request, $id)
    {
        $validated = $request->validate([
            'cover_letter' => 'required|string|min:100',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'portfolio' => 'nullable|file|mimes:pdf,zip|max:10240',
            'expected_salary' => 'nullable|numeric|min:0',
            'available_start_date' => 'nullable|date|after:today'
        ]);
        
        // Store application (implement actual storage)
        // JobApplication::create([...]);
        
        return back()->with('success', 'Your application has been submitted successfully! The employer will contact you if you are selected for an interview.');
    }
    
    /**
     * Create new job posting (for employers)
     */
    public function create()
    {
        $this->authorize('post-jobs'); // Implement authorization
        
        $jobTypes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance'
        ];
        
        $experienceLevels = [
            'entry' => 'Entry Level',
            'junior' => 'Junior (1-3 years)',
            'mid' => 'Mid Level (3-5 years)',
            'senior' => 'Senior (5+ years)',
            'lead' => 'Lead/Manager'
        ];
        
        return view('community.jobs.create', compact('jobTypes', 'experienceLevels'));
    }
    
    /**
     * Store new job posting
     */
    public function store(Request $request)
    {
        $this->authorize('post-jobs'); // Implement authorization
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'type' => 'required|string',
            'experience_level' => 'required|string',
            'location' => 'required|string',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'deadline' => 'required|date|after:today',
            'remote_allowed' => 'boolean'
        ]);
        
        // Store job (implement actual storage)
        // Job::create($validated);
        
        return redirect()->route('jobs.index')
                        ->with('success', 'Job posted successfully!');
    }
    
    /**
     * Get job statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_jobs' => 156,
            'active_jobs' => 89,
            'applications_today' => 23,
            'companies_hiring' => 45
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Export jobs data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Mock data - replace with actual query
        $jobs = collect([
            [
                'title' => 'Senior Mechanical Engineer',
                'company' => 'Vietnam Manufacturing Corp',
                'location' => 'Ho Chi Minh City',
                'type' => 'full_time',
                'salary_min' => 25000000,
                'salary_max' => 40000000,
                'posted_date' => '2024-01-15'
            ]
        ]);
        
        if ($format === 'json') {
            return response()->json($jobs);
        }
        
        // CSV export
        $filename = 'jobs_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($jobs) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Title', 'Company', 'Location', 'Type', 'Salary Min', 'Salary Max', 'Posted Date']);
            
            foreach ($jobs as $job) {
                fputcsv($file, [
                    $job['title'],
                    $job['company'],
                    $job['location'],
                    $job['type'],
                    $job['salary_min'],
                    $job['salary_max'],
                    $job['posted_date']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
