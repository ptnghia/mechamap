@extends('layouts.app')

@section('title', __('tools.process_calculator.title'))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('tools.breadcrumb.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('manufacturing.processes.index') }}">{{ __('tools.breadcrumb.manufacturing_processes') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('tools.breadcrumb.process_calculator') }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-calculator text-primary me-2"></i>
                        {{ __('tools.process_calculator.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('tools.process_calculator.description') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manufacturing.processes.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-list me-1"></i>
                        {{ __('tools.process_calculator.view_processes') }}
                    </a>
                    <a href="{{ route('manufacturing.processes.compare') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        {{ __('tools.process_calculator.compare_processes') }}
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
                        {{ __('tools.process_calculator.parameters') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="processCalculatorForm">
                        <!-- Process Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="process_id" class="form-label">Manufacturing Process</label>
                                <select class="form-select" id="process_id" name="process_id" required>
                                    <option value="">Select a process...</option>
                                    @foreach($processes as $process)
                                    <option value="{{ $process->id }}" data-category="{{ $process->category }}">
                                        {{ $process->name }} ({{ ucfirst($process->category) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="material_type" class="form-label">Material Type</label>
                                <select class="form-select" id="material_type" name="material_type">
                                    <option value="">Select material...</option>
                                    <option value="steel">Steel</option>
                                    <option value="aluminum">Aluminum</option>
                                    <option value="plastic">Plastic</option>
                                    <option value="titanium">Titanium</option>
                                    <option value="copper">Copper</option>
                                    <option value="composite">Composite</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dimensions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Part Dimensions</h6>
                            </div>
                            <div class="col-md-3">
                                <label for="length" class="form-label">Length (mm)</label>
                                <input type="number" class="form-control" id="length" name="length" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="width" class="form-label">Width (mm)</label>
                                <input type="number" class="form-control" id="width" name="width" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="height" class="form-label">Height (mm)</label>
                                <input type="number" class="form-control" id="height" name="height" step="0.01" min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1">
                            </div>
                        </div>

                        <!-- Process Parameters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Process Parameters</h6>
                            </div>
                            <div class="col-md-4">
                                <label for="cutting_speed" class="form-label">Cutting Speed (m/min)</label>
                                <input type="number" class="form-control" id="cutting_speed" name="cutting_speed" step="0.1" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="feed_rate" class="form-label">Feed Rate (mm/min)</label>
                                <input type="number" class="form-control" id="feed_rate" name="feed_rate" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="depth_of_cut" class="form-label">Depth of Cut (mm)</label>
                                <input type="number" class="form-control" id="depth_of_cut" name="depth_of_cut" step="0.01" min="0">
                            </div>
                        </div>

                        <!-- Cost Parameters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Cost Parameters</h6>
                            </div>
                            <div class="col-md-4">
                                <label for="machine_rate" class="form-label">Machine Rate ($/hour)</label>
                                <input type="number" class="form-control" id="machine_rate" name="machine_rate" value="50" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="labor_rate" class="form-label">Labor Rate ($/hour)</label>
                                <input type="number" class="form-control" id="labor_rate" name="labor_rate" value="25" step="0.01" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="material_cost" class="form-label">Material Cost ($/kg)</label>
                                <input type="number" class="form-control" id="material_cost" name="material_cost" step="0.01" min="0">
                            </div>
                        </div>

                        <!-- Calculate Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-calculator me-2"></i>
                                    Calculate Process Parameters
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
                    <div id="calculationResults" class="text-center text-muted py-5">
                        <i class="fa-solid fa-calculator fa-3x mb-3"></i>
                        <p>Enter parameters and click calculate to see results</p>
                    </div>
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
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('turning')">
                            <i class="fa-solid fa-sync-alt me-1"></i>
                            Turning Operation
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('milling')">
                            <i class="fa-solid fa-cog me-1"></i>
                            Milling Operation
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('drilling')">
                            <i class="fa-solid fa-circle-notch me-1"></i>
                            Drilling Operation
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('grinding')">
                            <i class="fa-solid fa-circle me-1"></i>
                            Grinding Operation
                        </button>
                    </div>
                </div>
            </div>

            <!-- Process Tips -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-lightbulb me-2"></i>
                        Process Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div id="processTips">
                        <div class="alert alert-info">
                            <small>
                                <strong>Tip:</strong> Select a manufacturing process to see specific recommendations and optimal parameters.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Process History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-history me-2"></i>
                        Calculation History
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
    const form = document.getElementById('processCalculatorForm');
    const processSelect = document.getElementById('process_id');

    // Process selection change handler
    processSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const category = selectedOption.dataset.category;
        updateProcessTips(category);
        setDefaultParameters(category);
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        calculateProcess();
    });
});

function updateProcessTips(category) {
    const tipsContainer = document.getElementById('processTips');
    let tips = '';

    switch(category) {
        case 'machining':
            tips = `
                <div class="alert alert-info">
                    <small><strong>Machining Tips:</strong><br>
                    • Use higher cutting speeds for softer materials<br>
                    • Maintain proper coolant flow<br>
                    • Consider tool wear and replacement costs</small>
                </div>
            `;
            break;
        case 'forming':
            tips = `
                <div class="alert alert-warning">
                    <small><strong>Forming Tips:</strong><br>
                    • Consider material springback<br>
                    • Ensure proper die clearance<br>
                    • Monitor forming force requirements</small>
                </div>
            `;
            break;
        case 'joining':
            tips = `
                <div class="alert alert-success">
                    <small><strong>Joining Tips:</strong><br>
                    • Prepare surfaces properly<br>
                    • Control heat input and cooling rate<br>
                    • Consider post-process treatments</small>
                </div>
            `;
            break;
        default:
            tips = `
                <div class="alert alert-info">
                    <small><strong>General Tips:</strong><br>
                    • Always consider safety requirements<br>
                    • Plan for quality control checkpoints<br>
                    • Factor in setup and changeover times</small>
                </div>
            `;
    }

    tipsContainer.innerHTML = tips;
}

function setDefaultParameters(category) {
    // Set default parameters based on process category
    const defaults = {
        'machining': {
            cutting_speed: 100,
            feed_rate: 0.2,
            depth_of_cut: 2.0
        },
        'forming': {
            cutting_speed: 0,
            feed_rate: 10,
            depth_of_cut: 5.0
        },
        'joining': {
            cutting_speed: 0,
            feed_rate: 5,
            depth_of_cut: 0
        }
    };

    const params = defaults[category] || defaults['machining'];

    document.getElementById('cutting_speed').value = params.cutting_speed;
    document.getElementById('feed_rate').value = params.feed_rate;
    document.getElementById('depth_of_cut').value = params.depth_of_cut;
}

function calculateProcess() {
    // Get form data
    const formData = new FormData(document.getElementById('processCalculatorForm'));
    const data = Object.fromEntries(formData);

    // Perform calculations (simplified example)
    const volume = (data.length || 100) * (data.width || 50) * (data.height || 25) / 1000000; // cm³
    const machineTime = volume / (data.feed_rate || 1) * 60; // minutes
    const totalTime = machineTime * (data.quantity || 1);
    const machineCost = (totalTime / 60) * (data.machine_rate || 50);
    const laborCost = (totalTime / 60) * (data.labor_rate || 25);
    const materialCost = volume * 0.008 * (data.material_cost || 30); // approximate weight
    const totalCost = machineCost + laborCost + materialCost;

    // Display results
    const resultsHtml = `
        <div class="text-start">
            <h6 class="text-primary mb-3">Calculation Results</h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Machine Time</div>
                        <div class="fw-bold">${totalTime.toFixed(1)} min</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Volume</div>
                        <div class="fw-bold">${volume.toFixed(2)} cm³</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Machine Cost</div>
                        <div class="fw-bold">$${machineCost.toFixed(2)}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Labor Cost</div>
                        <div class="fw-bold">$${laborCost.toFixed(2)}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3 text-center bg-primary text-white">
                        <div class="small">Total Cost</div>
                        <div class="h4 mb-0">$${totalCost.toFixed(2)}</div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-sm btn-success w-100" onclick="saveCalculation()">
                    <i class="fa-solid fa-save me-1"></i>
                    Save Calculation
                </button>
            </div>
        </div>
    `;

    document.getElementById('calculationResults').innerHTML = resultsHtml;

    // Add to history
    addToHistory(data, {
        machineTime: totalTime,
        totalCost: totalCost,
        volume: volume
    });
}

function quickCalc(processType) {
    // Set quick calculation parameters
    const quickParams = {
        'turning': { process_id: '1', cutting_speed: 120, feed_rate: 0.3, depth_of_cut: 2.5 },
        'milling': { process_id: '2', cutting_speed: 80, feed_rate: 0.2, depth_of_cut: 1.5 },
        'drilling': { process_id: '3', cutting_speed: 60, feed_rate: 0.1, depth_of_cut: 10 },
        'grinding': { process_id: '4', cutting_speed: 30, feed_rate: 0.05, depth_of_cut: 0.1 }
    };

    const params = quickParams[processType];
    if (params) {
        Object.keys(params).forEach(key => {
            const element = document.getElementById(key);
            if (element) element.value = params[key];
        });

        // Set default dimensions
        document.getElementById('length').value = 100;
        document.getElementById('width').value = 50;
        document.getElementById('height').value = 25;
        document.getElementById('quantity').value = 1;

        calculateProcess();
    }
}

function resetForm() {
    document.getElementById('processCalculatorForm').reset();
    document.getElementById('calculationResults').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="fa-solid fa-calculator fa-3x mb-3"></i>
            <p>Enter parameters and click calculate to see results</p>
        </div>
    `;
    document.getElementById('processTips').innerHTML = `
        <div class="alert alert-info">
            <small><strong>Tip:</strong> Select a manufacturing process to see specific recommendations and optimal parameters.</small>
        </div>
    `;
}

function saveCalculation() {
    alert('Calculation saved to your history!');
}

function addToHistory(params, results) {
    const historyContainer = document.getElementById('calculationHistory');
    const timestamp = new Date().toLocaleString();

    const historyItem = `
        <div class="border rounded p-3 mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Process:</strong> ${params.process_id || 'N/A'} |
                    <strong>Material:</strong> ${params.material_type || 'N/A'} |
                    <strong>Cost:</strong> $${results.totalCost.toFixed(2)}
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
    .col-md-3, .col-md-4, .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection
