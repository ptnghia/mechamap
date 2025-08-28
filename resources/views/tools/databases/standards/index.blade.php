@extends('layouts.app-full')

@section('title', __('technical.standards.title'))
@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/tool.css') }}">
@endpush
@section('content')
<div class="body_page">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page"><i class="fa-solid fa-certificate text-primary me-2"></i>  {{ __('technical.standards.title') }}</h1>
            <p class="text-muted mb-0">{{ __('technical.standards.description') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tools.standards') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-balance-scale me-1"></i>
                {{ __('technical.standards.compare_standards') }}
            </a>
            <a href="{{ route('tools.standards') }}" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-check-circle me-1"></i>
                {{ __('technical.standards.compliance_checker') }}
            </a>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-download me-1"></i>
                    {{ __('technical.standards.export') }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('tools.standards') }}">
                        <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('tools.standards') }}">
                        <i class="fa-solid fa-file-code me-2"></i>JSON Format
                    </a></li>
                </ul>
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
                    <p class="card-text text-muted">{{ __('technical.standards.standards_available') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-globe text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">5</h5>
                    <p class="card-text text-muted">{{ __('technical.standards.organizations_count') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-industry text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">12</h5>
                    <p class="card-text text-muted">{{ __('technical.standards.industries_covered') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-check-double text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">95%</h5>
                    <p class="card-text text-muted">{{ __('technical.standards.compliance_rate') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('tools.standards') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">{{ __('technical.standards.search_standards') }}</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('technical.standards.search_placeholder') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="organization" class="form-label">{{ __('technical.standards.organization') }}</label>
                            <select class="form-select" id="organization" name="organization">
                                <option value="">{{ __('technical.standards.all_organizations') }}</option>
                                <option value="ISO" {{ request('organization') == 'ISO' ? 'selected' : '' }}>ISO</option>
                                <option value="ASTM" {{ request('organization') == 'ASTM' ? 'selected' : '' }}>ASTM</option>
                                <option value="ASME" {{ request('organization') == 'ASME' ? 'selected' : '' }}>ASME</option>
                                <option value="DIN" {{ request('organization') == 'DIN' ? 'selected' : '' }}>DIN</option>
                                <option value="JIS" {{ request('organization') == 'JIS' ? 'selected' : '' }}>JIS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">{{ __('technical.standards.category') }}</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">{{ __('technical.standards.all_categories') }}</option>
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
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('tools.standards') }}" class="btn btn-outline-secondary">
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
        <div class="col-lg-4 col-md-6 mb-4">
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
                            <small class="text-muted">{{ __('technical.standards.status') }}</small>
                            <div class="fw-medium">
                                <span class="badge bg-{{ ($standard->status ?? 'active') == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($standard->status ?? 'Active') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.last_updated') }}</small>
                            <div class="fw-medium">{{ $standard->last_updated ?? '2015' }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.industry') }}</small>
                            <div class="fw-medium">{{ $standard->industry ?? 'All Industries' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.compliance_level') }}</small>
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
                        <small class="text-muted">{{ __('technical.standards.key_requirements') }}</small>
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
                                {{ __('technical.standards.compare') }}
                            </label>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('tools.standards') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                {{ __('technical.standards.view_details') }}
                            </a>
                            <button class="btn btn-sm btn-success" onclick="checkCompliance({{ $standard->id ?? 1 }})">
                                <i class="fa-solid fa-check me-1"></i>
                                {{ __('technical.standards.check') }}
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
        <div class="col-lg-4 col-md-6 mb-4">
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
                            <small class="text-muted">{{ __('technical.standards.status') }}</small>
                            <div class="fw-medium">
                                <span class="badge bg-success">{{ __('technical.standards.active') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.last_updated') }}</small>
                            <div class="fw-medium">{{ rand(2015, 2023) }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.industry') }}</small>
                            <div class="fw-medium">Manufacturing</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.standards.compliance_level') }}</small>
                            <div class="fw-medium">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $standard['compliance'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $standard['compliance'] }}%</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('technical.standards.key_requirements') }}</small>
                        <ul class="small mt-1 mb-0">
                            <li>{{ __('technical.standards.documentation_record_keeping') }}</li>
                            <li>{{ __('technical.standards.quality_control_procedures') }}</li>
                            <li>{{ __('technical.standards.testing_validation_methods') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <input type="checkbox" class="form-check-input standard-compare"
                                   value="{{ $index + 1 }}" id="compare_{{ $index + 1 }}">
                            <label class="form-check-label small text-muted" for="compare_{{ $index + 1 }}">
                                {{ __('technical.standards.compare') }}
                            </label>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewStandard({{ $index + 1 }})">
                                <i class="fa-solid fa-eye me-1"></i>
                                {{ __('technical.standards.view_details') }}
                            </button>
                            <button class="btn btn-sm btn-success" onclick="checkCompliance({{ $index + 1 }})">
                                <i class="fa-solid fa-check me-1"></i>
                                {{ __('technical.standards.check') }}
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
                        {{ __('technical.standards.standards_organizations') }}
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
                                <small class="text-muted">{{ rand(2, 8) }} {{ __('technical.standards.standards_available_count') }}</small>
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

    const url = new URL('{{ route("tools.standards") }}');
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
