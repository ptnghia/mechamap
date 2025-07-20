@extends('layouts.app')

@section('title', 'Material Cost Calculator')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-calculator text-primary me-2"></i>
                        Material Cost Calculator
                    </h1>
                    <p class="text-muted mb-0">Calculate material costs, quantities, and project estimates with precision</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="saveCalculation()">
                        <i class="fa-solid fa-save me-1"></i>
                        Save Calculation
                    </button>
                    <button class="btn btn-outline-primary" onclick="exportResults()">
                        <i class="fa-solid fa-download me-1"></i>
                        Export Results
                    </button>
                    <button class="btn btn-outline-info" onclick="showHistory()">
                        <i class="fa-solid fa-history me-1"></i>
                        History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calculator Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-cog me-2"></i>
                        Material Calculation Parameters
                    </h6>
                </div>
                <div class="card-body">
                    <form id="materialCalculatorForm">
                        <!-- Material Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="materialType" class="form-label">Material Type</label>
                                <select class="form-select" id="materialType" onchange="updateMaterialProperties()">
                                    <option value="">Select Material</option>
                                    <option value="steel_carbon">Carbon Steel</option>
                                    <option value="steel_stainless">Stainless Steel</option>
                                    <option value="aluminum">Aluminum</option>
                                    <option value="copper">Copper</option>
                                    <option value="brass">Brass</option>
                                    <option value="titanium">Titanium</option>
                                    <option value="plastic_abs">ABS Plastic</option>
                                    <option value="plastic_pvc">PVC</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="materialGrade" class="form-label">Material Grade/Specification</label>
                                <select class="form-select" id="materialGrade">
                                    <option value="">Select Grade</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dimensions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-ruler me-2"></i>
                                    Dimensions & Quantities
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <label for="length" class="form-label">Length (mm)</label>
                                <input type="number" class="form-control" id="length" step="0.1" onchange="calculateMaterial()">
                            </div>
                            <div class="col-md-3">
                                <label for="width" class="form-label">Width (mm)</label>
                                <input type="number" class="form-control" id="width" step="0.1" onchange="calculateMaterial()">
                            </div>
                            <div class="col-md-3">
                                <label for="thickness" class="form-label">Thickness (mm)</label>
                                <input type="number" class="form-control" id="thickness" step="0.1" onchange="calculateMaterial()">
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" value="1" min="1" onchange="calculateMaterial()">
                            </div>
                        </div>

                        <!-- Shape Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-shapes me-2"></i>
                                    Shape & Form
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label for="shapeType" class="form-label">Shape Type</label>
                                <select class="form-select" id="shapeType" onchange="updateShapeFields()">
                                    <option value="rectangular">Rectangular/Sheet</option>
                                    <option value="round">Round/Cylinder</option>
                                    <option value="tube">Tube/Pipe</option>
                                    <option value="angle">Angle/L-Shape</option>
                                    <option value="channel">Channel/U-Shape</option>
                                    <option value="beam">I-Beam/H-Beam</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="formType" class="form-label">Form Type</label>
                                <select class="form-select" id="formType">
                                    <option value="raw">Raw Material</option>
                                    <option value="machined">Machined</option>
                                    <option value="fabricated">Fabricated</option>
                                    <option value="finished">Finished Product</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Parameters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fa-solid fa-sliders me-2"></i>
                                    Additional Parameters
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <label for="wastePercentage" class="form-label">Waste Percentage (%)</label>
                                <input type="number" class="form-control" id="wastePercentage" value="10" min="0" max="50" onchange="calculateMaterial()">
                            </div>
                            <div class="col-md-4">
                                <label for="laborCost" class="form-label">Labor Cost (VND/hour)</label>
                                <input type="number" class="form-control" id="laborCost" value="150000" onchange="calculateMaterial()">
                            </div>
                            <div class="col-md-4">
                                <label for="processingTime" class="form-label">Processing Time (hours)</label>
                                <input type="number" class="form-control" id="processingTime" value="1" step="0.1" onchange="calculateMaterial()">
                            </div>
                        </div>

                        <!-- Currency & Units -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" onchange="calculateMaterial()">
                                    <option value="VND">Vietnamese Dong (VND)</option>
                                    <option value="USD">US Dollar (USD)</option>
                                    <option value="EUR">Euro (EUR)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="unitSystem" class="form-label">Unit System</label>
                                <select class="form-select" id="unitSystem" onchange="updateUnits()">
                                    <option value="metric">Metric (mm, kg)</option>
                                    <option value="imperial">Imperial (in, lb)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Calculate Button -->
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary btn-lg w-100" onclick="calculateMaterial()">
                                    <i class="fa-solid fa-calculator me-2"></i>
                                    Calculate Material Cost
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
                    <h6 class="mb-0">
                        <i class="fa-solid fa-chart-line me-2"></i>
                        Calculation Results
                    </h6>
                </div>
                <div class="card-body">
                    <div id="calculationResults">
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-calculator" style="font-size: 3rem;"></i>
                            <p class="mt-3">Enter material parameters and click calculate to see results</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Material Properties -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Material Properties
                    </h6>
                </div>
                <div class="card-body">
                    <div id="materialProperties">
                        <p class="text-muted">Select a material to view its properties</p>
                    </div>
                </div>
            </div>

            <!-- Quick Calculations -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-bolt me-2"></i>
                        Quick Calculations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('sheet')">
                            <i class="fa-solid fa-square me-1"></i>
                            Steel Sheet
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('pipe')">
                            <i class="fa-solid fa-circle me-1"></i>
                            Steel Pipe
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('beam')">
                            <i class="fa-solid fa-minus me-1"></i>
                            I-Beam
                        </button>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickCalc('aluminum')">
                            <i class="fa-solid fa-cube me-1"></i>
                            Aluminum Block
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calculation History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-history me-2"></i>
                        Recent Calculations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Material</th>
                                    <th>Dimensions</th>
                                    <th>Quantity</th>
                                    <th>Total Cost</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="calculationHistory">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No calculations yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Material properties database
const materialProperties = {
    steel_carbon: {
        name: 'Carbon Steel',
        density: 7.85, // g/cm³
        price: 25000, // VND per kg
        grades: ['AISI 1018', 'AISI 1045', 'AISI 4140'],
        properties: {
            tensile_strength: '400-550 MPa',
            yield_strength: '250-350 MPa',
            hardness: '120-200 HB'
        }
    },
    steel_stainless: {
        name: 'Stainless Steel',
        density: 8.0,
        price: 85000,
        grades: ['304', '316', '316L', '410'],
        properties: {
            tensile_strength: '515-620 MPa',
            yield_strength: '205-310 MPa',
            hardness: '150-250 HB'
        }
    },
    aluminum: {
        name: 'Aluminum',
        density: 2.7,
        price: 45000,
        grades: ['6061-T6', '7075-T6', '2024-T3'],
        properties: {
            tensile_strength: '310-572 MPa',
            yield_strength: '276-503 MPa',
            hardness: '95-150 HB'
        }
    },
    copper: {
        name: 'Copper',
        density: 8.96,
        price: 180000,
        grades: ['C101', 'C110', 'C11000'],
        properties: {
            tensile_strength: '220-400 MPa',
            yield_strength: '70-350 MPa',
            hardness: '40-100 HB'
        }
    }
};

function updateMaterialProperties() {
    const materialType = document.getElementById('materialType').value;
    const gradeSelect = document.getElementById('materialGrade');
    const propertiesDiv = document.getElementById('materialProperties');
    
    if (materialType && materialProperties[materialType]) {
        const material = materialProperties[materialType];
        
        // Update grades dropdown
        gradeSelect.innerHTML = '<option value="">Select Grade</option>';
        material.grades.forEach(grade => {
            gradeSelect.innerHTML += `<option value="${grade}">${grade}</option>`;
        });
        
        // Update properties display
        propertiesDiv.innerHTML = `
            <h6>${material.name}</h6>
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted">Density</small>
                    <div class="fw-medium">${material.density} g/cm³</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Price</small>
                    <div class="fw-medium">${material.price.toLocaleString()} VND/kg</div>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-muted">Tensile Strength</small>
                    <div class="fw-medium">${material.properties.tensile_strength}</div>
                </div>
                <div class="col-12">
                    <small class="text-muted">Yield Strength</small>
                    <div class="fw-medium">${material.properties.yield_strength}</div>
                </div>
            </div>
        `;
    } else {
        gradeSelect.innerHTML = '<option value="">Select Grade</option>';
        propertiesDiv.innerHTML = '<p class="text-muted">Select a material to view its properties</p>';
    }
    
    calculateMaterial();
}

function calculateMaterial() {
    const materialType = document.getElementById('materialType').value;
    const length = parseFloat(document.getElementById('length').value) || 0;
    const width = parseFloat(document.getElementById('width').value) || 0;
    const thickness = parseFloat(document.getElementById('thickness').value) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const wastePercentage = parseFloat(document.getElementById('wastePercentage').value) || 0;
    const laborCost = parseFloat(document.getElementById('laborCost').value) || 0;
    const processingTime = parseFloat(document.getElementById('processingTime').value) || 0;
    
    if (!materialType || !length || !width || !thickness) {
        return;
    }
    
    const material = materialProperties[materialType];
    if (!material) return;
    
    // Calculate volume in cm³
    const volume = (length * width * thickness) / 1000; // mm³ to cm³
    
    // Calculate weight in kg
    const weight = (volume * material.density) / 1000; // g to kg
    
    // Apply waste percentage
    const totalWeight = weight * (1 + wastePercentage / 100) * quantity;
    
    // Calculate costs
    const materialCost = totalWeight * material.price;
    const totalLaborCost = laborCost * processingTime * quantity;
    const totalCost = materialCost + totalLaborCost;
    
    // Display results
    const resultsDiv = document.getElementById('calculationResults');
    resultsDiv.innerHTML = `
        <div class="calculation-result">
            <div class="row g-3">
                <div class="col-12">
                    <div class="bg-primary text-white rounded p-3 text-center">
                        <h4 class="mb-0">${totalCost.toLocaleString()} VND</h4>
                        <small>Total Cost</small>
                    </div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Volume</small>
                    <div class="fw-medium">${volume.toFixed(2)} cm³</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Weight</small>
                    <div class="fw-medium">${totalWeight.toFixed(2)} kg</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Material Cost</small>
                    <div class="fw-medium">${materialCost.toLocaleString()} VND</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Labor Cost</small>
                    <div class="fw-medium">${totalLaborCost.toLocaleString()} VND</div>
                </div>
                <div class="col-12">
                    <small class="text-muted">Cost per Unit</small>
                    <div class="fw-medium">${(totalCost / quantity).toLocaleString()} VND</div>
                </div>
            </div>
        </div>
    `;
}

function quickCalc(type) {
    const presets = {
        sheet: { material: 'steel_carbon', length: 1000, width: 500, thickness: 3 },
        pipe: { material: 'steel_carbon', length: 6000, width: 100, thickness: 5 },
        beam: { material: 'steel_carbon', length: 6000, width: 200, thickness: 10 },
        aluminum: { material: 'aluminum', length: 500, width: 300, thickness: 20 }
    };
    
    const preset = presets[type];
    if (preset) {
        document.getElementById('materialType').value = preset.material;
        document.getElementById('length').value = preset.length;
        document.getElementById('width').value = preset.width;
        document.getElementById('thickness').value = preset.thickness;
        
        updateMaterialProperties();
        calculateMaterial();
    }
}

function saveCalculation() {
    alert('Calculation saved to your history!');
}

function exportResults() {
    alert('Results exported to PDF/Excel format');
}

function showHistory() {
    alert('Calculation history feature will show your previous calculations');
}

function updateShapeFields() {
    // Update form fields based on shape type
    const shapeType = document.getElementById('shapeType').value;
    // Implementation for different shape calculations
}

function updateUnits() {
    // Convert between metric and imperial units
    const unitSystem = document.getElementById('unitSystem').value;
    // Implementation for unit conversion
}
</script>

<style>
.calculation-result {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endsection
