@extends('layouts.app-full')

@section('title', __('technical.materials.title'))
@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/tool.css') }}">
@endpush
@section('content')
<div class="body_page">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page"><i class="fa-solid fa-cube text-primary me-2"></i>  {{ __('technical.materials.title') }}</h1>
            <p class="text-muted mb-0">{{ __('technical.materials.description') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tools.material-calculator') }}" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-calculator me-1"></i>
                {{ __('technical.materials.cost_calculator') }}
            </a>
            <a href="{{ route('tools.material-calculator') }}" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-balance-scale me-1"></i>
                {{ __('technical.materials.compare_materials') }}
            </a>
            <button class="btn btn-sm btn-outline-secondary" disabled>
                <i class="fa-solid fa-download me-1"></i>
                {{ __('technical.materials.export') }}
            </button>
        </div>
    </div>


    <!-- Filters Section -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('tools.materials') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('technical.materials.search_materials') }}</label>
                    <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('technical.materials.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">{{ __('technical.materials.category') }}</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">{{ __('technical.materials.all_categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">{{ __('technical.materials.category') }}</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">{{ __('technical.materials.all_categories') }}</option>
                        @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-search"></i>
                        </button>
                        <a href="{{ route('tools.materials') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <span class="text-muted">
                {{ __('technical.materials.showing_results') }} {{ $materials->firstItem() ?? 0 }} {{ __('technical.materials.to') }} {{ $materials->lastItem() ?? 0 }}
                {{ __('technical.materials.of') }} {{ $materials->total() }} {{ __('technical.materials.materials') }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="sort" class="form-label mb-0 text-muted text-nowrap">{{ __('technical.materials.sort_by') }}</label>
            <select class="form-select form-select-sm" id="sort" onchange="updateSort(this.value)">
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('technical.materials.name') }}</option>
                <option value="code" {{ request('sort') == 'code' ? 'selected' : '' }}>{{ __('technical.materials.code') }}</option>
                <option value="density" {{ request('sort') == 'density' ? 'selected' : '' }}>{{ __('technical.materials.density') }}</option>
                <option value="tensile_strength" {{ request('sort') == 'tensile_strength' ? 'selected' : '' }}>{{ __('technical.materials.tensile_strength') }}</option>
                <option value="cost_per_kg" {{ request('sort') == 'cost_per_kg' ? 'selected' : '' }}>{{ __('technical.materials.cost') }}</option>
            </select>
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
                            <small class="text-muted">{{ __('technical.materials.category') }}</small>
                            <div class="fw-medium">{{ ucfirst($material->category) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.materials.type') }}</small>
                            <div class="fw-medium">{{ ucfirst($material->material_type) }}</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.materials.density') }}</small>
                            <div class="fw-medium">{{ number_format($material->density, 2) }} g/cm³</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.materials.tensile_strength') }}</small>
                            <div class="fw-medium">{{ number_format($material->tensile_strength) }} MPa</div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.materials.yield_strength') }}</small>
                            <div class="fw-medium">{{ number_format($material->yield_strength) }} MPa</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('technical.materials.cost') }}</small>
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
                                {{ __('technical.materials.compare') }}
                            </label>
                        </div>
                        <a href="{{ route('tools.materials.show', $material) }}" class="btn btn-sm btn-primary">
                            {{ __('technical.materials.view_details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5">
            <i class="fa-solid fa-cube text-muted" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-muted">No materials found</h4>
            <p class="text-muted">Try adjusting your search criteria or filters</p>
            <a href="{{ route('tools.materials') }}" class="btn btn-primary">
                <i class="fa-solid fa-refresh me-1"></i>
                Reset Filters
            </a>
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
            {{ __('technical.materials.compare_button') }} (<span id="compareCount">0</span>)
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

    const url = new URL('{{ route("tools.material-calculator") }}');
    selectedMaterials.forEach(id => {
        url.searchParams.append('materials[]', id);
    });

    window.location.href = url.toString();
}
</script>

@endsection
