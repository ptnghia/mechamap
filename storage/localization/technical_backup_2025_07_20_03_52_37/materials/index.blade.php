@extends('layouts.app')

@section('title', 'Materials Database')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-cube text-primary me-2"></i>
                        Materials Database
                    </h1>
                    <p class="text-muted mb-0">Comprehensive database of engineering materials with properties and specifications</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('materials.calculator') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-calculator me-1"></i>
                        Cost Calculator
                    </a>
                    <a href="{{ route('materials.compare') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        Compare Materials
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('materials.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('materials.export', ['format' => 'json']) }}">
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
                    <form method="GET" action="{{ route('materials.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Materials</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by name, code, or description...">
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
                            <label for="type" class="form-label">Material Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
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
                                <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary">
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
                        Showing {{ $materials->firstItem() ?? 0 }} to {{ $materials->lastItem() ?? 0 }} 
                        of {{ $materials->total() }} materials
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="sort" class="form-label mb-0 text-muted">Sort by:</label>
                    <select class="form-select form-select-sm" id="sort" onchange="updateSort(this.value)">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>Code</option>
                        <option value="density" {{ request('sort') == 'density' ? 'selected' : '' }}>Density</option>
                        <option value="tensile_strength" {{ request('sort') == 'tensile_strength' ? 'selected' : '' }}>Tensile Strength</option>
                        <option value="cost_per_kg" {{ request('sort') == 'cost_per_kg' ? 'selected' : '' }}>Cost</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="row">
        @forelse($materials as $material)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 material-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">{{ $material->name }}</h6>
                    <span class="badge bg-primary">{{ $material->code }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Category</small>
                            <div class="fw-medium">{{ ucfirst($material->category) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Type</small>
                            <div class="fw-medium">{{ ucfirst($material->material_type) }}</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Density</small>
                            <div class="fw-medium">{{ number_format($material->density, 2) }} g/cm³</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Tensile Strength</small>
                            <div class="fw-medium">{{ number_format($material->tensile_strength) }} MPa</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Yield Strength</small>
                            <div class="fw-medium">{{ number_format($material->yield_strength) }} MPa</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Cost</small>
                            <div class="fw-medium">${{ number_format($material->cost_per_kg, 2) }}/kg</div>
                        </div>
                    </div>
                    
                    <p class="card-text text-muted small">
                        {{ Str::limit($material->description, 100) }}
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <input type="checkbox" class="form-check-input material-compare" 
                                   value="{{ $material->id }}" id="compare_{{ $material->id }}">
                            <label class="form-check-label small text-muted" for="compare_{{ $material->id }}">
                                Compare
                            </label>
                        </div>
                        <a href="{{ route('materials.show', $material) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fa-solid fa-cube text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No materials found</h4>
                <p class="text-muted">Try adjusting your search criteria or filters</p>
                <a href="{{ route('materials.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-refresh me-1"></i>
                    Reset Filters
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($materials->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $materials->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Compare Materials Button -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <button type="button" class="btn btn-success btn-lg rounded-pill shadow" 
                id="compareButton" style="display: none;" onclick="compareMaterials()">
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

// Compare materials functionality
let selectedMaterials = [];

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.material-compare');
    const compareButton = document.getElementById('compareButton');
    const compareCount = document.getElementById('compareCount');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedMaterials.push(this.value);
            } else {
                selectedMaterials = selectedMaterials.filter(id => id !== this.value);
            }
            
            compareCount.textContent = selectedMaterials.length;
            compareButton.style.display = selectedMaterials.length > 1 ? 'block' : 'none';
        });
    });
});

function compareMaterials() {
    if (selectedMaterials.length < 2) {
        alert('Please select at least 2 materials to compare');
        return;
    }
    
    const url = new URL('{{ route("materials.compare") }}');
    selectedMaterials.forEach(id => {
        url.searchParams.append('materials[]', id);
    });
    
    window.location.href = url.toString();
}
</script>

<style>
.material-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.material-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
