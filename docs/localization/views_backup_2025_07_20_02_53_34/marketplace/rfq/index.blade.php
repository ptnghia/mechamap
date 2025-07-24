@extends('layouts.app')

@section('title', 'Request for Quote (RFQ)')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-file-invoice text-primary me-2"></i>
                        Request for Quote (RFQ)
                    </h1>
                    <p class="text-muted mb-0">Submit RFQ requests and receive competitive quotes from verified suppliers</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    <a href="{{ route('marketplace.rfq.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        Create RFQ
                    </a>
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-chart-bar me-1"></i>
                            Statistics
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showRFQStats()">
                                <i class="fa-solid fa-chart-line me-2"></i>RFQ Analytics
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showSupplierStats()">
                                <i class="fa-solid fa-building me-2"></i>Supplier Performance
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
                    <i class="fa-solid fa-file-invoice text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="totalRFQs">45</h5>
                    <p class="card-text text-muted">Active RFQs</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-handshake text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="totalQuotes">156</h5>
                    <p class="card-text text-muted">Quotes Received</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-building text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="activeSuppliers">28</h5>
                    <p class="card-text text-muted">Active Suppliers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-check-circle text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title" id="completedRFQs">89</h5>
                    <p class="card-text text-muted">Completed RFQs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('marketplace.rfq.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search RFQs</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search by title, description, or RFQ number...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="awarded" {{ request('status') == 'awarded' ? 'selected' : '' }}>Awarded</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="mechanical" {{ request('category') == 'mechanical' ? 'selected' : '' }}>Mechanical Parts</option>
                                <option value="materials" {{ request('category') == 'materials' ? 'selected' : '' }}>Raw Materials</option>
                                <option value="services" {{ request('category') == 'services' ? 'selected' : '' }}>Services</option>
                                <option value="equipment" {{ request('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="budget_range" class="form-label">Budget Range</label>
                            <select class="form-select" id="budget_range" name="budget_range">
                                <option value="">All Budgets</option>
                                <option value="0-10000000" {{ request('budget_range') == '0-10000000' ? 'selected' : '' }}>< 10M VND</option>
                                <option value="10000000-50000000" {{ request('budget_range') == '10000000-50000000' ? 'selected' : '' }}>10M - 50M VND</option>
                                <option value="50000000-100000000" {{ request('budget_range') == '50000000-100000000' ? 'selected' : '' }}>50M - 100M VND</option>
                                <option value="100000000+" {{ request('budget_range') == '100000000+' ? 'selected' : '' }}>> 100M VND</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="deadline" class="form-label">Deadline</label>
                            <select class="form-select" id="deadline" name="deadline">
                                <option value="">All Deadlines</option>
                                <option value="urgent" {{ request('deadline') == 'urgent' ? 'selected' : '' }}>Urgent (< 7 days)</option>
                                <option value="normal" {{ request('deadline') == 'normal' ? 'selected' : '' }}>Normal (< 30 days)</option>
                                <option value="flexible" {{ request('deadline') == 'flexible' ? 'selected' : '' }}>Flexible (> 30 days)</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('marketplace.rfq.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- RFQ List -->
    <div class="row">
        <div class="col-12">
            @for($i = 1; $i <= 8; $i++)
            <div class="card mb-3 rfq-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <h5 class="card-title mb-0 me-3">
                                            RFQ-{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}: {{ ['Precision Machined Parts', 'Steel Raw Materials', 'CNC Machining Services', 'Bearing Components', 'Custom Fabrication', 'Quality Control Equipment', 'Hydraulic Components', 'Electronic Assemblies'][$i-1] }}
                                        </h5>
                                        <span class="badge bg-{{ ['success', 'warning', 'primary', 'info'][$i % 4] }}">
                                            {{ ['Open', 'In Progress', 'Closed', 'Awarded'][$i % 4] }}
                                        </span>
                                    </div>
                                    
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <i class="fa-solid fa-building me-1"></i>
                                        {{ ['Vietnam Manufacturing Co.', 'Tech Solutions Ltd.', 'Industrial Parts Inc.', 'Precision Engineering'][$i % 4] }}
                                    </h6>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        {{ ['We need high-precision machined parts for our automotive assembly line. Specifications include tight tolerances and quality certifications.', 'Looking for reliable supplier of steel raw materials for construction projects. Long-term partnership preferred.', 'Seeking CNC machining services for prototype development. Quick turnaround time required.', 'Custom bearing components needed for industrial equipment. Technical drawings available.'][$i % 4] }}
                                    </p>
                                    
                                    <div class="row g-2 mb-2">
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-tag me-1"></i>
                                                Category: {{ ['Mechanical Parts', 'Raw Materials', 'Services', 'Components'][$i % 4] }}
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-calendar me-1"></i>
                                                Deadline: {{ \Carbon\Carbon::now()->addDays(rand(7, 45))->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                {{ rand(15, 85) }} views
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fa-solid fa-comments me-1"></i>
                                                {{ rand(2, 12) }} quotes
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-clock me-1"></i>
                                            Posted {{ rand(1, 15) }} days ago
                                        </small>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-map-marker-alt me-1"></i>
                                            {{ ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Hai Phong'][$i % 4] }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-md-end">
                            <div class="mb-2">
                                <div class="fw-bold text-success">
                                    Budget: {{ number_format(rand(10, 100) * 1000000) }} VND
                                </div>
                                <small class="text-muted">Estimated value</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar" style="width: {{ rand(20, 90) }}%"></div>
                                </div>
                                <small class="text-muted">{{ rand(2, 8) }}/{{ rand(8, 15) }} quotes received</small>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-md-end">
                                <a href="{{ route('marketplace.rfq.show', $i) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-eye me-1"></i>
                                    View Details
                                </a>
                                @auth
                                @if(Auth::user()->hasRole('supplier'))
                                <button class="btn btn-primary btn-sm" onclick="submitQuote({{ $i }})">
                                    <i class="fa-solid fa-paper-plane me-1"></i>
                                    Submit Quote
                                </button>
                                @endif
                                @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-sign-in-alt me-1"></i>
                                    Login to Quote
                                </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Featured Suppliers -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-star me-2"></i>
                        Top Performing Suppliers
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @for($i = 1; $i <= 4; $i++)
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-building text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ ['Precision Manufacturing', 'Steel Solutions Co.', 'Tech Components Ltd.', 'Quality Parts Inc.'][$i-1] }}</h6>
                                <div class="mb-1">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="fa-solid fa-star {{ $j <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <small class="text-muted">(4.{{ rand(5, 9) }})</small>
                                </div>
                                <small class="text-muted">{{ rand(15, 45) }} successful quotes</small>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quote Submission Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quoteForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quotePrice" class="form-label">Quote Price (VND)</label>
                            <input type="number" class="form-control" id="quotePrice" required>
                        </div>
                        <div class="col-md-6">
                            <label for="deliveryTime" class="form-label">Delivery Time (days)</label>
                            <input type="number" class="form-control" id="deliveryTime" required>
                        </div>
                        <div class="col-12">
                            <label for="quoteDescription" class="form-label">Quote Description</label>
                            <textarea class="form-control" id="quoteDescription" rows="4" 
                                      placeholder="Describe your solution, capabilities, and any additional services..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="validityPeriod" class="form-label">Quote Validity (days)</label>
                            <input type="number" class="form-control" id="validityPeriod" value="30">
                        </div>
                        <div class="col-md-6">
                            <label for="paymentTerms" class="form-label">Payment Terms</label>
                            <select class="form-select" id="paymentTerms">
                                <option value="net30">Net 30 days</option>
                                <option value="net15">Net 15 days</option>
                                <option value="advance">50% advance</option>
                                <option value="cod">Cash on delivery</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="attachments" class="form-label">Attachments (Optional)</label>
                            <input type="file" class="form-control" id="attachments" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                            <small class="text-muted">Upload technical specifications, certifications, or samples</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitQuoteForm()">Submit Quote</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentRFQId = null;

function submitQuote(rfqId) {
    currentRFQId = rfqId;
    const modal = new bootstrap.Modal(document.getElementById('quoteModal'));
    modal.show();
}

function submitQuoteForm() {
    // Implement quote submission
    alert('Quote submitted successfully! The buyer will review and contact you if selected.');
    bootstrap.Modal.getInstance(document.getElementById('quoteModal')).hide();
}

function showRFQStats() {
    alert('RFQ Analytics: Average response time: 2.5 days, Success rate: 68%, Most popular category: Mechanical Parts');
}

function showSupplierStats() {
    alert('Supplier Performance: Top suppliers have 95%+ on-time delivery, Average rating: 4.3/5, Response time: < 24 hours');
}

// Load stats from API
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("marketplace.rfq.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.total_rfqs) {
                document.getElementById('totalRFQs').textContent = data.total_rfqs;
            }
            if (data.total_quotes) {
                document.getElementById('totalQuotes').textContent = data.total_quotes;
            }
            if (data.active_suppliers) {
                document.getElementById('activeSuppliers').textContent = data.active_suppliers;
            }
            if (data.completed_rfqs) {
                document.getElementById('completedRFQs').textContent = data.completed_rfqs;
            }
        })
        .catch(error => console.log('Stats loading failed:', error));
});
</script>

<style>
.rfq-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.rfq-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
@endsection
