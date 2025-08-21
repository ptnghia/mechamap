@extends('layouts.app')

@section('title', $material->name . ' - Material Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materials.index') }}">Materials Database</a></li>
                    <li class="breadcrumb-item active">{{ $material->name }}</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-cube text-primary me-2"></i>
                        {{ $material->name }}
                        <span class="badge bg-primary ms-2">{{ $material->code }}</span>
                    </h1>
                    <p class="text-muted mb-0">{{ $material->description }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('materials.calculator') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-calculator me-1"></i>
                        Cost Calculator
                    </a>
                    <a href="{{ route('materials.compare', ['materials[]' => $material->id]) }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        Compare
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Properties -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Basic Properties
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="text-muted small">Category</div>
                                <div class="fw-bold">{{ ucfirst($material->category) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="text-muted small">Material Type</div>
                                <div class="fw-bold">{{ ucfirst($material->material_type) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="text-muted small">Grade</div>
                                <div class="fw-bold">{{ $material->grade ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mechanical Properties -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-cogs me-2"></i>
                        Mechanical Properties
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Density</div>
                                <div class="fw-bold">{{ number_format($material->density, 2) }} g/cm³</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Tensile Strength</div>
                                <div class="fw-bold">{{ number_format($material->tensile_strength) }} MPa</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Yield Strength</div>
                                <div class="fw-bold">{{ number_format($material->yield_strength) }} MPa</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Young's Modulus</div>
                                <div class="fw-bold">{{ $material->youngs_modulus ? number_format($material->youngs_modulus) . ' MPa' : 'N/A' }}</div>
                            </div>
                        </div>
                        @if($material->hardness_hb)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Hardness (HB)</div>
                                <div class="fw-bold">{{ number_format($material->hardness_hb) }} HB</div>
                            </div>
                        </div>
                        @endif
                        @if($material->elongation)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Elongation</div>
                                <div class="fw-bold">{{ number_format($material->elongation) }}%</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thermal Properties -->
            @if($material->melting_point || $material->thermal_conductivity)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-thermometer-half me-2"></i>
                        Thermal Properties
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($material->melting_point)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Melting Point</div>
                                <div class="fw-bold">{{ number_format($material->melting_point) }}°C</div>
                            </div>
                        </div>
                        @endif
                        @if($material->thermal_conductivity)
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Thermal Conductivity</div>
                                <div class="fw-bold">{{ number_format($material->thermal_conductivity, 2) }} W/m·K</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Chemical Composition -->
            @if($material->chemical_composition)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-flask me-2"></i>
                        Chemical Composition
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $composition = json_decode($material->chemical_composition, true);
                    @endphp
                    @if($composition)
                    <div class="row g-2">
                        @foreach($composition as $element => $percentage)
                        <div class="col-md-3">
                            <div class="border rounded p-2 text-center">
                                <div class="fw-bold">{{ $element }}</div>
                                <div class="text-muted small">{{ $percentage }}%</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Applications -->
            @if($material->typical_applications)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-tools me-2"></i>
                        Typical Applications
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $applications = json_decode($material->typical_applications, true);
                    @endphp
                    @if($applications)
                    <div class="row g-2">
                        @foreach($applications as $application)
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-check-circle text-success me-2"></i>
                                {{ $application }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Cost Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-dollar-sign me-2"></i>
                        Cost Information
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="display-6 text-primary fw-bold">
                        ${{ number_format($material->cost_per_kg, 2) }}
                    </div>
                    <div class="text-muted">per kilogram</div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $material->availability == 'high' ? 'success' : ($material->availability == 'medium' ? 'warning' : 'danger') }}">
                            {{ ucfirst($material->availability) }} Availability
                        </span>
                    </div>
                </div>
            </div>

            <!-- Manufacturing Info -->
            @if($material->machinability || $material->weldability)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-industry me-2"></i>
                        Manufacturing
                    </h5>
                </div>
                <div class="card-body">
                    @if($material->machinability)
                    @php
                        $machinability = json_decode($material->machinability, true);
                    @endphp
                    <div class="mb-3">
                        <div class="text-muted small">Machinability</div>
                        <div class="fw-bold">{{ ucfirst($machinability['rating'] ?? 'N/A') }}</div>
                        @if(isset($machinability['cutting_speed']))
                        <div class="small text-muted">Cutting Speed: {{ $machinability['cutting_speed'] }}</div>
                        @endif
                    </div>
                    @endif
                    
                    @if($material->weldability)
                    @php
                        $weldability = json_decode($material->weldability, true);
                    @endphp
                    <div>
                        <div class="text-muted small">Weldability</div>
                        <div class="fw-bold">{{ ucfirst($weldability['rating'] ?? 'N/A') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-chart-bar me-2"></i>
                        Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Views</span>
                        <span class="fw-bold">{{ number_format($material->view_count) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Usage Count</span>
                        <span class="fw-bold">{{ number_format($material->usage_count) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Updated</span>
                        <span class="fw-bold">{{ $material->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Materials -->
    @if($relatedMaterials && $relatedMaterials->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-cubes me-2"></i>
                        Related Materials
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedMaterials as $related)
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body p-3">
                                    <h6 class="card-title">{{ $related->name }}</h6>
                                    <p class="card-text small text-muted">{{ $related->code }}</p>
                                    <div class="small">
                                        <div class="text-muted">Cost: ${{ number_format($related->cost_per_kg, 2) }}/kg</div>
                                    </div>
                                </div>
                                <div class="card-footer p-2">
                                    <a href="{{ route('materials.show', $related) }}" class="btn btn-sm btn-outline-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    .btn, .breadcrumb, .card-footer {
        display: none !important;
    }
}
</style>
@endsection
