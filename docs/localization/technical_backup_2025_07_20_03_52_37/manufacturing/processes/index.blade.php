@extends('layouts.app')

@section('title', 'Manufacturing Processes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-gears text-primary me-2"></i>
                        Manufacturing Processes
                    </h1>
                    <p class="text-muted mb-0">Comprehensive guide to manufacturing processes and techniques</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manufacturing.processes.selector') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-route me-1"></i>
                        Process Selector
                    </a>
                    <a href="{{ route('manufacturing.processes.calculator') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-calculator me-1"></i>
                        Cost Calculator
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('manufacturing.processes.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('manufacturing.processes.export', ['format' => 'json']) }}">
                                <i class="fa-solid fa-file-code me-2"></i>JSON Format
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('manufacturing.processes.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Processes</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by name, category, or description...">
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="skill_level" class="form-label">Skill Level</label>
                            <select class="form-select" id="skill_level" name="skill_level">
                                <option value="">All Levels</option>
                                @foreach($skillLevels as $level)
                                <option value="{{ $level }}" {{ request('skill_level') == $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-muted">
                        Showing {{ $processes->firstItem() ?? 0 }} to {{ $processes->lastItem() ?? 0 }} 
                        of {{ $processes->total() }} processes
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0 text-muted">Sort by:</label>
                    <select class="form-select form-select-sm" id="sort" onchange="updateSort(this.value)">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="category" {{ request('sort') == 'category' ? 'selected' : '' }}>Category</option>
                        <option value="cost_per_hour" {{ request('sort') == 'cost_per_hour' ? 'selected' : '' }}>Cost</option>
                        <option value="production_rate" {{ request('sort') == 'production_rate' ? 'selected' : '' }}>Production Rate</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Processes Grid -->
    <div class="row">
        @forelse($processes as $process)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 process-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">{{ $process->name }}</h6>
                    <span class="badge bg-primary">{{ ucfirst($process->category) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Setup Time</small>
                            <div class="fw-medium">{{ $process->setup_time ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cycle Time</small>
                            <div class="fw-medium">{{ $process->cycle_time ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Cost/Hour</small>
                            <div class="fw-medium">${{ number_format($process->cost_per_hour ?? 0) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Skill Level</small>
                            <div class="fw-medium">{{ ucfirst($process->skill_level_required ?? 'Basic') }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Production Rate</small>
                        <div class="fw-medium">{{ $process->production_rate ?? 'Variable' }}</div>
                    </div>
                    
                    <p class="card-text text-muted small">
                        {{ Str::limit($process->description ?? 'Manufacturing process description', 100) }}
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <input type="checkbox" class="form-check-input process-compare" 
                                   value="{{ $process->id }}" id="compare_{{ $process->id }}">
                            <label class="form-check-label small text-muted" for="compare_{{ $process->id }}">
                                Compare
                            </label>
                        </div>
                        <a href="{{ route('manufacturing.processes.show', $process) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fa-solid fa-gears text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No processes found</h4>
                <p class="text-muted">Try adjusting your search criteria or filters</p>
                <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-refresh me-1"></i>
                    Reset Filters
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($processes->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $processes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Compare Processes Button -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <button type="button" class="btn btn-success btn-lg rounded-pill shadow" 
                id="compareButton" style="display: none;" onclick="compareProcesses()">
            <i class="fa-solid fa-balance-scale me-2"></i>
            Compare (<span id="compareCount">0</span>)
        </button>
    </div>
</div>

<script>
function updateSort(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location.href = url.toString();
}

// Compare processes functionality
let selectedProcesses = [];

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.process-compare');
    const compareButton = document.getElementById('compareButton');
    const compareCount = document.getElementById('compareCount');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedProcesses.push(this.value);
            } else {
                selectedProcesses = selectedProcesses.filter(id => id !== this.value);
            }
            
            compareCount.textContent = selectedProcesses.length;
            compareButton.style.display = selectedProcesses.length > 1 ? 'block' : 'none';
        });
    });
});

function compareProcesses() {
    if (selectedProcesses.length < 2) {
        alert('Please select at least 2 processes to compare');
        return;
    }
    
    const url = new URL('{{ route("manufacturing.processes.compare") }}');
    selectedProcesses.forEach(id => {
        url.searchParams.append('processes[]', id);
    });
    
    window.location.href = url.toString();
}
</script>

<style>
.process-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.process-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
