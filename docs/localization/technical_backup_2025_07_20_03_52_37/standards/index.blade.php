@extends('layouts.app')

@section('title', 'Engineering Standards')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-certificate text-primary me-2"></i>
                        Engineering Standards
                    </h1>
                    <p class="text-muted mb-0">Comprehensive library of international engineering standards and specifications</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('standards.compare') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        Compare Standards
                    </a>
                    <a href="{{ route('standards.compliance-checker') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-check-circle me-1"></i>
                        Compliance Checker
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('standards.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('standards.export', ['format' => 'json']) }}">
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
                    <i class="fa-solid fa-certificate text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">8+</h5>
                    <p class="card-text text-muted">Standards Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-globe text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">5</h5>
                    <p class="card-text text-muted">Organizations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-industry text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">12</h5>
                    <p class="card-text text-muted">Industries Covered</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-check-double text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">95%</h5>
                    <p class="card-text text-muted">Compliance Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('standards.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Standards</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by standard number, title, or description...">
                        </div>
                        <div class="col-md-3">
                            <label for="organization" class="form-label">Organization</label>
                            <select class="form-select" id="organization" name="organization">
                                <option value="">All Organizations</option>
                                <option value="ISO" {{ request('organization') == 'ISO' ? 'selected' : '' }}>ISO</option>
                                <option value="ASTM" {{ request('organization') == 'ASTM' ? 'selected' : '' }}>ASTM</option>
                                <option value="ASME" {{ request('organization') == 'ASME' ? 'selected' : '' }}>ASME</option>
                                <option value="DIN" {{ request('organization') == 'DIN' ? 'selected' : '' }}>DIN</option>
                                <option value="JIS" {{ request('organization') == 'JIS' ? 'selected' : '' }}>JIS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="materials" {{ request('category') == 'materials' ? 'selected' : '' }}>Materials</option>
                                <option value="mechanical" {{ request('category') == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                                <option value="quality" {{ request('category') == 'quality' ? 'selected' : '' }}>Quality</option>
                                <option value="safety" {{ request('category') == 'safety' ? 'selected' : '' }}>Safety</option>
                                <option value="testing" {{ request('category') == 'testing' ? 'selected' : '' }}>Testing</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('standards.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Standards Grid -->
    <div class="row">
        @forelse($standards ?? [] as $standard)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100 standard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">{{ $standard->standard_number ?? 'ISO 9001:2015' }}</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">{{ $standard->organization ?? 'ISO' }}</span>
                        <span class="badge bg-success">{{ ucfirst($standard->category ?? 'Quality') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ $standard->title ?? 'Quality Management Systems - Requirements' }}</h6>
                    <p class="card-text text-muted small">
                        {{ Str::limit($standard->description ?? 'This standard specifies requirements for a quality management system when an organization needs to demonstrate its ability to consistently provide products and services that meet customer and applicable statutory and regulatory requirements.', 150) }}
                    </p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Status</small>
                            <div class="fw-medium">
                                <span class="badge bg-{{ ($standard->status ?? 'active') == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($standard->status ?? 'Active') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-medium">{{ $standard->last_updated ?? '2015' }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Industry</small>
                            <div class="fw-medium">{{ $standard->industry ?? 'All Industries' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Compliance Level</small>
                            <div class="fw-medium">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $standard->compliance_rate ?? 95 }}%"></div>
                                </div>
                                <small class="text-muted">{{ $standard->compliance_rate ?? 95 }}%</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($standard->key_requirements ?? false)
                    <div class="mb-3">
                        <small class="text-muted">Key Requirements:</small>
                        <ul class="small mt-1 mb-0">
                            @foreach(array_slice($standard->key_requirements, 0, 3) as $requirement)
                            <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <input type="checkbox" class="form-check-input standard-compare" 
                                   value="{{ $standard->id ?? 1 }}" id="compare_{{ $standard->id ?? 1 }}">
                            <label class="form-check-label small text-muted" for="compare_{{ $standard->id ?? 1 }}">
                                Compare
                            </label>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('standards.show', $standard->id ?? 1) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                View Details
                            </a>
                            <button class="btn btn-sm btn-success" onclick="checkCompliance({{ $standard->id ?? 1 }})">
                                <i class="fa-solid fa-check me-1"></i>
                                Check
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <!-- Mock Standards for Demo -->
        @php
        $mockStandards = [
            ['number' => 'ISO 9001:2015', 'title' => 'Quality Management Systems', 'org' => 'ISO', 'category' => 'Quality', 'compliance' => 95],
            ['number' => 'ASTM A36', 'title' => 'Carbon Structural Steel', 'org' => 'ASTM', 'category' => 'Materials', 'compliance' => 88],
            ['number' => 'ASME B31.3', 'title' => 'Process Piping', 'org' => 'ASME', 'category' => 'Mechanical', 'compliance' => 92],
            ['number' => 'ISO 14001:2015', 'title' => 'Environmental Management', 'org' => 'ISO', 'category' => 'Environmental', 'compliance' => 85],
            ['number' => 'DIN 6912', 'title' => 'Socket Head Cap Screws', 'org' => 'DIN', 'category' => 'Fasteners', 'compliance' => 98],
            ['number' => 'JIS G3101', 'title' => 'Rolled Steels for General Structure', 'org' => 'JIS', 'category' => 'Materials', 'compliance' => 90]
        ];
        @endphp
        
        @foreach($mockStandards as $index => $standard)
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100 standard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">{{ $standard['number'] }}</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">{{ $standard['org'] }}</span>
                        <span class="badge bg-success">{{ $standard['category'] }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle mb-2">{{ $standard['title'] }}</h6>
                    <p class="card-text text-muted small">
                        This standard provides comprehensive guidelines and requirements for {{ strtolower($standard['title']) }} in mechanical engineering applications.
                    </p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Status</small>
                            <div class="fw-medium">
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-medium">{{ rand(2015, 2023) }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Industry</small>
                            <div class="fw-medium">Manufacturing</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Compliance Level</small>
                            <div class="fw-medium">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $standard['compliance'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $standard['compliance'] }}%</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Key Requirements:</small>
                        <ul class="small mt-1 mb-0">
                            <li>Documentation and record keeping</li>
                            <li>Quality control procedures</li>
                            <li>Testing and validation methods</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <input type="checkbox" class="form-check-input standard-compare" 
                                   value="{{ $index + 1 }}" id="compare_{{ $index + 1 }}">
                            <label class="form-check-label small text-muted" for="compare_{{ $index + 1 }}">
                                Compare
                            </label>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewStandard({{ $index + 1 }})">
                                <i class="fa-solid fa-eye me-1"></i>
                                View Details
                            </button>
                            <button class="btn btn-sm btn-success" onclick="checkCompliance({{ $index + 1 }})">
                                <i class="fa-solid fa-check me-1"></i>
                                Check
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endforelse
    </div>

    <!-- Standards Organizations -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-building me-2"></i>
                        Standards Organizations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(['ISO', 'ASTM', 'ASME', 'DIN'] as $org)
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-certificate text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ $org }}</h6>
                                <small class="text-muted">{{ rand(2, 8) }} standards available</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compare Standards Button -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <button type="button" class="btn btn-success btn-lg rounded-pill shadow" 
                id="compareButton" style="display: none;" onclick="compareStandards()">
            <i class="fa-solid fa-balance-scale me-2"></i>
            Compare (<span id="compareCount">0</span>)
        </button>
    </div>
</div>

<script>
// Compare standards functionality
let selectedStandards = [];

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.standard-compare');
    const compareButton = document.getElementById('compareButton');
    const compareCount = document.getElementById('compareCount');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedStandards.push(this.value);
            } else {
                selectedStandards = selectedStandards.filter(id => id !== this.value);
            }
            
            compareCount.textContent = selectedStandards.length;
            compareButton.style.display = selectedStandards.length > 1 ? 'block' : 'none';
        });
    });
});

function compareStandards() {
    if (selectedStandards.length < 2) {
        alert('Please select at least 2 standards to compare');
        return;
    }
    
    const url = new URL('{{ route("standards.compare") }}');
    selectedStandards.forEach(id => {
        url.searchParams.append('standards[]', id);
    });
    
    window.location.href = url.toString();
}

function viewStandard(id) {
    // Implement view functionality
    alert('Standard details view will show comprehensive information and requirements');
}

function checkCompliance(id) {
    // Implement compliance check
    alert('Compliance checker will analyze your processes against this standard');
}
</script>

<style>
.standard-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.standard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
