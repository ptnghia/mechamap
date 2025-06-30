@extends('admin.layouts.dason')

@section('title', 'Custom KPI Builder')

@section('css')
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/apexcharts/apexcharts.min.css') }}" rel="stylesheet" type="text/css" />
<style>
.kpi-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.kpi-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.kpi-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
}

.kpi-status.good { color: #28a745; }
.kpi-status.warning { color: #ffc107; }
.kpi-status.critical { color: #dc3545; }

.metric-builder {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.template-card {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.template-card:hover {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.template-card.selected {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}

.step {
    flex: 1;
    text-align: center;
    padding: 10px;
    border-radius: 5px;
    background: #e9ecef;
    margin: 0 5px;
    transition: all 0.3s ease;
}

.step.active {
    background: #007bff;
    color: white;
}

.step.completed {
    background: #28a745;
    color: white;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Custom KPI Builder</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.analytics.realtime') }}">Analytics</a></li>
                    <li class="breadcrumb-item active">KPI Builder</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Saved KPIs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Your Custom KPIs</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kpiBuilderModal">
                        <i class="fas fa-plus me-1"></i> Create New KPI
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($savedKPIs as $kpi)
                    <div class="col-xl-4 col-md-6">
                        <div class="kpi-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="mb-0">{{ $kpi['name'] }}</h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="editKPI({{ $kpi['id'] }})">Edit</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="duplicateKPI({{ $kpi['id'] }})">Duplicate</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="exportKPI({{ $kpi['id'] }})">Export</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteKPI({{ $kpi['id'] }})">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="kpi-value kpi-status {{ $kpi['status'] }}">
                                {{ number_format($kpi['current_value'], 1) }}
                                @if($kpi['metric_type'] === 'percentage')%@endif
                            </div>
                            
                            <p class="text-muted mb-2">{{ $kpi['description'] }}</p>
                            
                            @if($kpi['target_value'])
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Target: {{ number_format($kpi['target_value'], 1) }}@if($kpi['metric_type'] === 'percentage')%@endif</small>
                                <div class="progress" style="width: 60px; height: 6px;">
                                    <div class="progress-bar bg-{{ $kpi['status'] === 'good' ? 'success' : ($kpi['status'] === 'warning' ? 'warning' : 'danger') }}" 
                                         style="width: {{ min(($kpi['current_value'] / $kpi['target_value']) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewKPIDetails({{ $kpi['id'] }})">
                                    <i class="fas fa-chart-line me-1"></i> View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Templates -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">KPI Templates</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($templates as $template)
                    <div class="col-xl-4 col-md-6">
                        <div class="template-card" onclick="selectTemplate('{{ $template['name'] }}')">
                            <h6 class="mb-2">{{ $template['name'] }}</h6>
                            <p class="text-muted mb-3">{{ $template['description'] }}</p>
                            <div class="mb-2">
                                @foreach($template['kpis'] as $kpi)
                                <span class="badge bg-soft-primary text-primary me-1 mb-1">{{ $kpi }}</span>
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-outline-primary">Use Template</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Builder Modal -->
<div class="modal fade" id="kpiBuilderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Custom KPI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">1. Basic Info</div>
                    <div class="step" data-step="2">2. Data Source</div>
                    <div class="step" data-step="3">3. Configuration</div>
                    <div class="step" data-step="4">4. Preview</div>
                </div>

                <form id="kpiBuilderForm">
                    <!-- Step 1: Basic Information -->
                    <div class="form-step active" data-step="1">
                        <div class="mb-3">
                            <label class="form-label">KPI Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chart Type *</label>
                            <select class="form-select" name="chart_type" required>
                                <option value="">Select chart type</option>
                                <option value="line">Line Chart</option>
                                <option value="bar">Bar Chart</option>
                                <option value="area">Area Chart</option>
                                <option value="pie">Pie Chart</option>
                                <option value="donut">Donut Chart</option>
                                <option value="gauge">Gauge Chart</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 2: Data Source -->
                    <div class="form-step" data-step="2">
                        <div class="mb-3">
                            <label class="form-label">Data Source *</label>
                            <select class="form-select" name="data_source" required onchange="updateMetricOptions()">
                                <option value="">Select data source</option>
                                <option value="users">Users</option>
                                <option value="revenue">Revenue</option>
                                <option value="orders">Orders</option>
                                <option value="threads">Forum Threads</option>
                                <option value="products">Products</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metric Type *</label>
                            <select class="form-select" name="metric_type" required>
                                <option value="">Select metric type</option>
                                <option value="count">Count</option>
                                <option value="sum">Sum</option>
                                <option value="avg">Average</option>
                                <option value="percentage">Percentage</option>
                                <option value="ratio">Ratio</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Time Period *</label>
                            <select class="form-select" name="time_period" required>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 3: Configuration -->
                    <div class="form-step" data-step="3">
                        <div class="mb-3">
                            <label class="form-label">Target Value</label>
                            <input type="number" class="form-control" name="target_value" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alert Threshold</label>
                            <input type="number" class="form-control" name="alert_threshold" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Filters</label>
                            <div id="filtersContainer">
                                <!-- Dynamic filters will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addFilter()">
                                <i class="fas fa-plus me-1"></i> Add Filter
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Preview -->
                    <div class="form-step" data-step="4">
                        <div class="metric-builder">
                            <h6 class="mb-3">KPI Preview</h6>
                            <div id="kpiPreview">
                                <!-- Preview will be generated here -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevStep" onclick="previousStep()" style="display: none;">Previous</button>
                <button type="button" class="btn btn-primary" id="nextStep" onclick="nextStep()">Next</button>
                <button type="button" class="btn btn-success" id="createKPI" onclick="createKPI()" style="display: none;">Create KPI</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
let currentStep = 1;
const totalSteps = 4;

function nextStep() {
    if (currentStep < totalSteps) {
        if (validateStep(currentStep)) {
            currentStep++;
            updateStepDisplay();
            
            if (currentStep === 4) {
                generatePreview();
            }
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

function updateStepDisplay() {
    // Update step indicator
    document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 < currentStep) {
            step.classList.add('completed');
        } else if (index + 1 === currentStep) {
            step.classList.add('active');
        }
    });
    
    // Update form steps
    document.querySelectorAll('.form-step').forEach((step, index) => {
        step.classList.remove('active');
        if (index + 1 === currentStep) {
            step.classList.add('active');
        }
    });
    
    // Update buttons
    document.getElementById('prevStep').style.display = currentStep > 1 ? 'block' : 'none';
    document.getElementById('nextStep').style.display = currentStep < totalSteps ? 'block' : 'none';
    document.getElementById('createKPI').style.display = currentStep === totalSteps ? 'block' : 'none';
}

function validateStep(step) {
    const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            alert('Please fill in all required fields.');
            return false;
        }
    }
    
    return true;
}

function updateMetricOptions() {
    const dataSource = document.querySelector('[name="data_source"]').value;
    const metricType = document.querySelector('[name="metric_type"]');
    
    // Update available metric types based on data source
    // This is a simplified example
    console.log('Data source changed to:', dataSource);
}

function addFilter() {
    const container = document.getElementById('filtersContainer');
    const filterDiv = document.createElement('div');
    filterDiv.className = 'row mb-2';
    filterDiv.innerHTML = `
        <div class="col-4">
            <select class="form-select" name="filter_field[]">
                <option value="status">Status</option>
                <option value="role">Role</option>
                <option value="category">Category</option>
            </select>
        </div>
        <div class="col-3">
            <select class="form-select" name="filter_operator[]">
                <option value="equals">Equals</option>
                <option value="not_equals">Not Equals</option>
                <option value="contains">Contains</option>
            </select>
        </div>
        <div class="col-4">
            <input type="text" class="form-control" name="filter_value[]" placeholder="Value">
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFilter(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(filterDiv);
}

function removeFilter(button) {
    button.closest('.row').remove();
}

function generatePreview() {
    const formData = new FormData(document.getElementById('kpiBuilderForm'));
    const data = Object.fromEntries(formData.entries());
    
    const preview = document.getElementById('kpiPreview');
    preview.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>KPI Configuration</h6>
                <ul class="list-unstyled">
                    <li><strong>Name:</strong> ${data.name}</li>
                    <li><strong>Data Source:</strong> ${data.data_source}</li>
                    <li><strong>Metric Type:</strong> ${data.metric_type}</li>
                    <li><strong>Time Period:</strong> ${data.time_period}</li>
                    <li><strong>Chart Type:</strong> ${data.chart_type}</li>
                    ${data.target_value ? `<li><strong>Target:</strong> ${data.target_value}</li>` : ''}
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Sample Chart</h6>
                <div id="previewChart"></div>
            </div>
        </div>
    `;
    
    // Generate sample chart
    generateSampleChart(data.chart_type);
}

function generateSampleChart(chartType) {
    const options = {
        series: chartType === 'pie' || chartType === 'donut' ? [44, 55, 13, 43] : [{
            name: 'Sample Data',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        chart: {
            type: chartType === 'donut' ? 'donut' : chartType,
            height: 200
        },
        labels: chartType === 'pie' || chartType === 'donut' ? ['A', 'B', 'C', 'D'] : undefined,
        xaxis: chartType !== 'pie' && chartType !== 'donut' ? {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep']
        } : undefined
    };

    const chart = new ApexCharts(document.querySelector("#previewChart"), options);
    chart.render();
}

function createKPI() {
    const formData = new FormData(document.getElementById('kpiBuilderForm'));
    const data = Object.fromEntries(formData.entries());
    
    fetch('{{ route("admin.analytics.kpi.create") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('KPI created successfully!');
            location.reload();
        } else {
            alert('Error creating KPI: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the KPI.');
    });
}

function selectTemplate(templateName) {
    // Implement template selection logic
    console.log('Selected template:', templateName);
}

function editKPI(kpiId) {
    // Implement KPI editing
    console.log('Edit KPI:', kpiId);
}

function duplicateKPI(kpiId) {
    // Implement KPI duplication
    console.log('Duplicate KPI:', kpiId);
}

function exportKPI(kpiId) {
    // Implement KPI export
    window.open(`{{ route('admin.analytics.kpi.export') }}?kpi_id=${kpiId}&format=csv`);
}

function deleteKPI(kpiId) {
    if (confirm('Are you sure you want to delete this KPI?')) {
        // Implement KPI deletion
        console.log('Delete KPI:', kpiId);
    }
}

function viewKPIDetails(kpiId) {
    // Implement KPI details view
    console.log('View KPI details:', kpiId);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();
});
</script>
@endsection
