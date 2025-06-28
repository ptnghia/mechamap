@extends('layouts.app')

@section('title', 'Job Board')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-briefcase text-primary me-2"></i>
                        Job Board
                    </h1>
                    <p class="text-muted mb-0">Find your next career opportunity in mechanical engineering</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        Post a Job
                    </a>
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('jobs.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('jobs.export', ['format' => 'json']) }}">
                                <i class="fa-solid fa-file-code me-2"></i>JSON Format
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-briefcase text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedJobs->count() }}</h5>
                    <p class="card-text text-muted">Active Jobs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-building text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedJobs->pluck('company')->unique()->count() }}</h5>
                    <p class="card-text text-muted">Companies Hiring</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-clock text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedJobs->where('type', 'full_time')->count() }}</h5>
                    <p class="card-text text-muted">Full Time</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-home text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $paginatedJobs->where('remote_allowed', true)->count() }}</h5>
                    <p class="card-text text-muted">Remote Jobs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Jobs</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Job title, company, or keywords...">
                        </div>
                        <div class="col-md-2">
                            <label for="type" class="form-label">Job Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                @foreach($jobTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="experience_level" class="form-label">Experience</label>
                            <select class="form-select" id="experience_level" name="experience_level">
                                <option value="">All Levels</option>
                                @foreach($experienceLevels as $key => $label)
                                <option value="{{ $key }}" {{ request('experience_level') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="{{ request('location') }}" placeholder="City or region...">
                        </div>
                        <div class="col-md-2">
                            <label for="remote" class="form-label">Remote Work</label>
                            <select class="form-select" id="remote" name="remote">
                                <option value="">All Jobs</option>
                                <option value="1" {{ request('remote') == '1' ? 'selected' : '' }}>Remote OK</option>
                                <option value="0" {{ request('remote') == '0' ? 'selected' : '' }}>On-site Only</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs List -->
    <div class="row">
        <div class="col-12">
            @forelse($paginatedJobs as $job)
            <div class="card mb-3 job-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('jobs.show', $job['id']) }}" class="text-decoration-none">
                                            {{ $job['title'] }}
                                        </a>
                                    </h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fa-solid fa-building me-1"></i>
                                        {{ $job['company'] }}
                                    </h6>
                                    
                                    <div class="row g-2 mb-2">
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-location-dot me-1"></i>
                                                {{ $job['location'] }}
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-primary">{{ $jobTypes[$job['type']] }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-success">{{ $experienceLevels[$job['experience_level']] }}</span>
                                        </div>
                                        @if($job['remote_allowed'])
                                        <div class="col-auto">
                                            <span class="badge bg-info">Remote OK</span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        {{ Str::limit($job['description'], 150) }}
                                    </p>
                                    
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-calendar me-1"></i>
                                            Posted {{ \Carbon\Carbon::parse($job['posted_date'])->diffForHumans() }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-eye me-1"></i>
                                            {{ $job['views_count'] }} views
                                        </small>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-users me-1"></i>
                                            {{ $job['applications_count'] }} applications
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end">
                            <div class="mb-2">
                                <div class="fw-bold text-success">
                                    {{ number_format($job['salary_min']) }} - {{ number_format($job['salary_max']) }} {{ $job['currency'] }}
                                </div>
                                <small class="text-muted">per month</small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    Deadline: {{ \Carbon\Carbon::parse($job['deadline'])->format('M d, Y') }}
                                </small>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-md-end">
                                <a href="{{ route('jobs.show', $job['id']) }}" class="btn btn-outline-primary btn-sm">
                                    View Details
                                </a>
                                @auth
                                <button class="btn btn-primary btn-sm" onclick="quickApply({{ $job['id'] }})">
                                    Quick Apply
                                </button>
                                @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                    Login to Apply
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fa-solid fa-briefcase text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No jobs found</h4>
                <p class="text-muted">Try adjusting your search criteria or check back later for new opportunities</p>
                <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-refresh me-1"></i>
                    Reset Filters
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Featured Companies -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-star me-2"></i>
                        Featured Companies
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($paginatedJobs->pluck('company')->unique()->take(4) as $company)
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-building text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ $company }}</h6>
                                <small class="text-muted">
                                    {{ $paginatedJobs->where('company', $company)->count() }} open positions
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Apply Modal -->
<div class="modal fade" id="quickApplyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Apply</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickApplyForm">
                    <div class="mb-3">
                        <label for="coverLetter" class="form-label">Cover Letter</label>
                        <textarea class="form-control" id="coverLetter" rows="4" 
                                  placeholder="Tell the employer why you're interested in this position..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="resume" class="form-label">Resume</label>
                        <input type="file" class="form-control" id="resume" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="mb-3">
                        <label for="expectedSalary" class="form-label">Expected Salary (VND)</label>
                        <input type="number" class="form-control" id="expectedSalary" placeholder="e.g., 25000000">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitQuickApply()">Submit Application</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentJobId = null;

function quickApply(jobId) {
    currentJobId = jobId;
    const modal = new bootstrap.Modal(document.getElementById('quickApplyModal'));
    modal.show();
}

function submitQuickApply() {
    // Implement quick apply submission
    alert('Quick apply feature will be implemented with backend integration');
    bootstrap.Modal.getInstance(document.getElementById('quickApplyModal')).hide();
}
</script>

<style>
.job-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.job-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
