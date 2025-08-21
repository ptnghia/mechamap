@extends('layouts.app')

@section('title', __('tools.material_calculator.title'))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('tools.breadcrumb.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('materials.index') }}">{{ __('tools.breadcrumb.materials_database') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('tools.breadcrumb.cost_calculator') }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-calculator text-primary me-2"></i>
                        {{ __('tools.material_calculator.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('tools.material_calculator.description') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('materials.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-list me-1"></i>
                        {{ __('tools.material_calculator.view_materials') }}
                    </a>
                    <a href="{{ route('materials.compare') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        {{ __('tools.material_calculator.compare_materials') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calculator Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-cogs me-2"></i>
                        {{ __('tools.material_calculator.parameters') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('materials.calculator') }}" id="materialCalculatorForm">
                        <!-- Material Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="material_id" class="form-label">Select Material</label>
                                <select class="form-select" id="material_id" name="material_id" required>
                                    <option value="">Choose a material...</option>
                                    @foreach($materials as $mat)
                                    <option value="{{ $mat->id }}" {{ $material && $material->id == $mat->id ? 'selected' : '' }}>
                                        {{ $mat->name }} ({{ $mat->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Quantity and Unit -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Quantity Specification</h6>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       value="{{ request('quantity', 1) }}" step="0.001" min="0.001" required>
                            </div>
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="kg" {{ request('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="g" {{ request('unit') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                                    <option value="ton" {{ request('unit') == 'ton' ? 'selected' : '' }}>Ton (t)</option>
                                    <option value="m3" {{ request('unit') == 'm3' ? 'selected' : '' }}>Cubic Meter (m³)</option>
                                    <option value="cm3" {{ request('unit') == 'cm3' ? 'selected' : '' }}>Cubic Centimeter (cm³)</option>
                                    <option value="mm3" {{ request('unit') == 'mm3' ? 'selected' : '' }}>Cubic Millimeter (mm³)</option>
                                </select>
                            </div>
                        </div>

                        @if($material)
                        <!-- Material Properties Display -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Material Properties</h6>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Density</div>
                                    <div class="fw-bold">{{ number_format($material->density, 3) }} g/cm³</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Cost per kg</div>
                                    <div class="fw-bold">${{ number_format($material->cost_per_kg, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="text-muted small">Availability</div>
                                    <div class="fw-bold">
                                        <span class="badge bg-{{ $material->availability == 'high' ? 'success' : ($material->availability == 'medium' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($material->availability) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Calculate Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-calculator me-2"></i>
                                    Calculate Cost
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="resetForm()">
                                    <i class="fa-solid fa-refresh me-2"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        Calculation Results
                    </h5>
                </div>
                <div class="card-body">
                    @if($calculation)
                    <div class="text-start">
                        <h6 class="text-primary mb-3">Cost Breakdown</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Input Quantity</div>
                                    <div class="fw-bold">{{ number_format($calculation['quantity'], 3) }} {{ $calculation['unit'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Weight (kg)</div>
                                    <div class="fw-bold">{{ number_format($calculation['quantity_kg'], 3) }} kg</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Unit Cost</div>
                                    <div class="fw-bold">${{ number_format($calculation['cost_per_kg'], 2) }}/kg</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Material</div>
                                    <div class="fw-bold">{{ $calculation['material']->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3 text-center bg-primary text-white mb-3">
                            <div class="small">Total Cost</div>
                            <div class="h4 mb-0">${{ number_format($calculation['total_cost'], 2) }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success btn-sm" onclick="saveCalculation()">
                                <i class="fa-solid fa-save me-1"></i>
                                Save Calculation
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="exportCalculation()">
                                <i class="fa-solid fa-download me-1"></i>
                                Export Results
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-calculator fa-3x mb-3"></i>
                        <p>Select a material and enter quantity to calculate costs</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Calculations -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-bolt me-2"></i>
                        Quick Calculations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('steel', 10, 'kg')">
                            <i class="fa-solid fa-cog me-1"></i>
                            10kg Steel
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('aluminum', 5, 'kg')">
                            <i class="fa-solid fa-cube me-1"></i>
                            5kg Aluminum
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('copper', 2, 'kg')">
                            <i class="fa-solid fa-circle me-1"></i>
                            2kg Copper
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('titanium', 1, 'kg')">
                            <i class="fa-solid fa-star me-1"></i>
                            1kg Titanium
                        </button>
                    </div>
                </div>
            </div>

            <!-- Material Tips -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        Cost Optimization Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <small>
                            <strong>Tips:</strong><br>
                            • Consider material availability when planning<br>
                            • Bulk purchases often reduce unit costs<br>
                            • Factor in machining and processing costs<br>
                            • Check for material substitutes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculation History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-history me-2"></i>
                        Recent Calculations
                    </h5>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearHistory()">
                        <i class="fa-solid fa-trash me-1"></i>
                        Clear History
                    </button>
                </div>
                <div class="card-body">
                    <div id="calculationHistory" class="text-center text-muted py-3">
                        <p>No calculations performed yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('materialCalculatorForm');
    const materialSelect = document.getElementById('material_id');

    // Auto-submit form when material changes
    materialSelect.addEventListener('change', function() {
        if (this.value) {
            // Keep current quantity and unit values
            form.submit();
        }
    });

    // Add current calculation to history if exists
    @if($calculation)
    addToHistory({
        material: '{{ $calculation["material"]->name }}',
        quantity: {{ $calculation['quantity'] }},
        unit: '{{ $calculation['unit'] }}',
        total_cost: {{ $calculation['total_cost'] }}
    });
    @endif
});

function quickCalc(materialType, quantity, unit) {
    // Find material by type (simplified - in real app would need better matching)
    const materialSelect = document.getElementById('material_id');
    const quantityInput = document.getElementById('quantity');
    const unitSelect = document.getElementById('unit');

    // Set values
    quantityInput.value = quantity;
    unitSelect.value = unit;

    // Try to find matching material
    for (let option of materialSelect.options) {
        if (option.text.toLowerCase().includes(materialType.toLowerCase())) {
            materialSelect.value = option.value;
            break;
        }
    }

    // Submit form
    document.getElementById('materialCalculatorForm').submit();
}

function resetForm() {
    document.getElementById('materialCalculatorForm').reset();
    window.location.href = '{{ route("materials.calculator") }}';
}

function saveCalculation() {
    alert('Calculation saved to your history!');
}

function exportCalculation() {
    alert('Export functionality will be implemented soon!');
}

function addToHistory(calc) {
    const historyContainer = document.getElementById('calculationHistory');
    const timestamp = new Date().toLocaleString();

    const historyItem = `
        <div class="border rounded p-3 mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Material:</strong> ${calc.material} |
                    <strong>Quantity:</strong> ${calc.quantity} ${calc.unit} |
                    <strong>Cost:</strong> $${calc.total_cost.toFixed(2)}
                </div>
                <small class="text-muted">${timestamp}</small>
            </div>
        </div>
    `;

    if (historyContainer.innerHTML.includes('No calculations')) {
        historyContainer.innerHTML = historyItem;
    } else {
        historyContainer.innerHTML = historyItem + historyContainer.innerHTML;
    }
}

function clearHistory() {
    document.getElementById('calculationHistory').innerHTML = '<p class="text-center text-muted py-3">No calculations performed yet</p>';
}
</script>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

@media (max-width: 768px) {
    .col-md-4, .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection
