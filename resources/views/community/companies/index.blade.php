@extends('layouts.app')

@section('title', 'Company Directory')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-building-user text-primary me-2"></i>
                        Company Directory
                    </h1>
                    <p class="text-muted mb-0">Connect with verified companies and suppliers in the mechanical engineering industry</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    @if(Auth::user()->hasAnyRole(['supplier', 'manufacturer', 'brand']))
                    <a href="{{ route('companies.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        Add Company
                    </a>
                    @endif
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('companies.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>CSV Format
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('companies.export', ['format' => 'json']) }}">
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
                    <i class="fa-solid fa-building text-primary mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">27</h5>
                    <p class="card-text text-muted">Verified Companies</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-industry text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">15</h5>
                    <p class="card-text text-muted">Industries</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-map-marker-alt text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">8</h5>
                    <p class="card-text text-muted">Cities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-star text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">4.6</h5>
                    <p class="card-text text-muted">Average Rating</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('companies.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search Companies</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Company name, industry, or location...">
                        </div>
                        <div class="col-md-2">
                            <label for="industry" class="form-label">Industry</label>
                            <select class="form-select" id="industry" name="industry">
                                <option value="">All Industries</option>
                                <option value="manufacturing" {{ request('industry') == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="automotive" {{ request('industry') == 'automotive' ? 'selected' : '' }}>Automotive</option>
                                <option value="aerospace" {{ request('industry') == 'aerospace' ? 'selected' : '' }}>Aerospace</option>
                                <option value="construction" {{ request('industry') == 'construction' ? 'selected' : '' }}>Construction</option>
                                <option value="energy" {{ request('industry') == 'energy' ? 'selected' : '' }}>Energy</option>
                                <option value="electronics" {{ request('industry') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="company_type" class="form-label">Company Type</label>
                            <select class="form-select" id="company_type" name="company_type">
                                <option value="">All Types</option>
                                <option value="supplier" {{ request('company_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                <option value="manufacturer" {{ request('company_type') == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                <option value="distributor" {{ request('company_type') == 'distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="service" {{ request('company_type') == 'service' ? 'selected' : '' }}>Service Provider</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="location" class="form-label">Location</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">All Locations</option>
                                <option value="ho-chi-minh" {{ request('location') == 'ho-chi-minh' ? 'selected' : '' }}>Ho Chi Minh City</option>
                                <option value="hanoi" {{ request('location') == 'hanoi' ? 'selected' : '' }}>Hanoi</option>
                                <option value="da-nang" {{ request('location') == 'da-nang' ? 'selected' : '' }}>Da Nang</option>
                                <option value="hai-phong" {{ request('location') == 'hai-phong' ? 'selected' : '' }}>Hai Phong</option>
                                <option value="can-tho" {{ request('location') == 'can-tho' ? 'selected' : '' }}>Can Tho</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="verification" class="form-label">Verification</label>
                            <select class="form-select" id="verification" name="verification">
                                <option value="">All Companies</option>
                                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified Only</option>
                                <option value="premium" {{ request('verification') == 'premium' ? 'selected' : '' }}>Premium Members</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                                <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Companies Grid -->
    <div class="row">
        @for($i = 1; $i <= 12; $i++)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 company-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="company-logo me-3">
                            <i class="fa-solid fa-building text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ ['Vietnam Manufacturing Corp', 'Precision Engineering Ltd', 'Steel Solutions Co', 'Tech Components Inc', 'Industrial Parts Vietnam', 'Quality Manufacturing', 'Advanced Materials Co', 'Mechanical Solutions', 'Engineering Services Ltd', 'Metal Works Vietnam', 'Automation Systems', 'Fabrication Experts'][$i-1] }}</h6>
                            <small class="text-muted">{{ ['Manufacturing', 'Engineering Services', 'Materials Supply', 'Component Manufacturing'][$i % 4] }}</small>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        @if($i % 3 == 0)
                        <span class="badge bg-success mb-1">
                            <i class="fa-solid fa-check-circle me-1"></i>
                            Verified
                        </span>
                        @endif
                        @if($i % 5 == 0)
                        <span class="badge bg-warning">
                            <i class="fa-solid fa-crown me-1"></i>
                            Premium
                        </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-map-marker-alt me-1"></i>
                                {{ ['Ho Chi Minh City', 'Hanoi', 'Da Nang', 'Hai Phong'][$i % 4] }}, Vietnam
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-users me-1"></i>
                                {{ rand(50, 500) }} employees
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-calendar me-1"></i>
                                Established {{ rand(1995, 2015) }}
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <span class="me-2">Rating:</span>
                            @for($j = 1; $j <= 5; $j++)
                                <i class="fa-solid fa-star {{ $j <= 4 ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <small class="text-muted ms-2">(4.{{ rand(2, 9) }}/5)</small>
                        </div>
                        <small class="text-muted">{{ rand(15, 85) }} reviews</small>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Specialties:</small>
                        <div class="mt-1">
                            @foreach(['CNC Machining', 'Quality Control', 'Custom Fabrication', 'Material Supply', 'Engineering Design', 'Precision Parts'][$i % 6] as $index => $specialty)
                            @if($index < 3)
                            <span class="badge bg-light text-dark me-1">{{ $specialty }}</span>
                            @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Contact:</small>
                        <div class="mt-1">
                            <small class="d-block">
                                <i class="fa-solid fa-phone me-1"></i>
                                +84 {{ rand(20, 99) }} {{ rand(1000, 9999) }} {{ rand(1000, 9999) }}
                            </small>
                            <small class="d-block">
                                <i class="fa-solid fa-envelope me-1"></i>
                                info@company{{ $i }}.com.vn
                            </small>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">Products</small>
                            <div class="fw-medium">{{ rand(25, 150) }}+</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Response Time</small>
                            <div class="fw-medium">{{ rand(2, 24) }}h</div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary" onclick="addToFavorites({{ $i }})">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="shareCompany({{ $i }})">
                                <i class="fa-solid fa-share"></i>
                            </button>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('companies.show', $i) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                View Profile
                            </a>
                            <button class="btn btn-sm btn-primary" onclick="contactCompany({{ $i }})">
                                <i class="fa-solid fa-envelope me-1"></i>
                                Contact
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Industry Categories -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-industry me-2"></i>
                        Browse by Industry
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(['Manufacturing', 'Automotive', 'Aerospace', 'Construction', 'Energy', 'Electronics'] as $industry)
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-industry text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ $industry }}</h6>
                                <small class="text-muted">{{ rand(3, 12) }} companies</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Rated Companies -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-trophy me-2"></i>
                        Top Rated Companies This Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @for($i = 1; $i <= 4; $i++)
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold">{{ $i }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ ['Precision Tech', 'Quality Manufacturing', 'Steel Solutions', 'Advanced Materials'][$i-1] }}</h6>
                                    <div class="d-flex align-items-center">
                                        @for($j = 1; $j <= 5; $j++)
                                            <i class="fa-solid fa-star text-warning"></i>
                                        @endfor
                                        <small class="text-muted ms-2">(5.0)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Company Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contact Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <div class="mb-3">
                        <label for="contactSubject" class="form-label">Subject</label>
                        <select class="form-select" id="contactSubject">
                            <option value="inquiry">General Inquiry</option>
                            <option value="quote">Request Quote</option>
                            <option value="partnership">Partnership Opportunity</option>
                            <option value="support">Technical Support</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="contactMessage" rows="4"
                                  placeholder="Describe your inquiry or requirements..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contactPhone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="contactPhone" placeholder="+84 xxx xxx xxxx">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">Send Message</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentCompanyId = null;

function contactCompany(companyId) {
    currentCompanyId = companyId;
    const modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

function sendMessage() {
    // Implement message sending
    alert('Message sent successfully! The company will contact you soon.');
    bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
}

function addToFavorites(companyId) {
    alert('Company added to your favorites!');
}

function shareCompany(companyId) {
    if (navigator.share) {
        navigator.share({
            title: 'Company Profile',
            text: 'Check out this company on MechaMap',
            url: window.location.href
        });
    } else {
        alert('Company profile link copied to clipboard!');
    }
}
</script>

<style>
.company-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.company-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.company-logo {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>
@endsection
