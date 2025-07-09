@extends('layouts.app')

@section('title', __('companies.directory'))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-building-user text-primary me-2"></i>
                        {{ __('companies.directory') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('companies.header_description') }}</p>
                </div>
                <div class="d-flex gap-2">
                    @auth
                    @if(Auth::user()->hasAnyRole(['supplier', 'manufacturer', 'brand']))
                    <a href="{{ route('companies.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        {{ __('companies.add_company') }}
                    </a>
                    @endif
                    @endauth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download me-1"></i>
                            {{ __('companies.export') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('companies.export', ['format' => 'csv']) }}">
                                <i class="fa-solid fa-file-csv me-2"></i>{{ __('companies.csv_format') }}
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('companies.export', ['format' => 'json']) }}">
                                <i class="fa-solid fa-file-code me-2"></i>{{ __('companies.json_format') }}
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
                    <h5 class="card-title">{{ $stats['total_companies'] }}</h5>
                    <p class="card-text text-muted">{{ __('companies.verified_companies') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-industry text-success mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $stats['total_industries'] }}</h5>
                    <p class="card-text text-muted">{{ __('companies.industries') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-map-marker-alt text-info mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ $stats['total_cities'] }}</h5>
                    <p class="card-text text-muted">{{ __('companies.cities') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fa-solid fa-star text-warning mb-2" style="font-size: 2rem;"></i>
                    <h5 class="card-title">{{ number_format($stats['average_rating'], 1) }}</h5>
                    <p class="card-text text-muted">{{ __('companies.average_rating') }}</p>
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
                            <label for="search" class="form-label">{{ __('companies.search_companies') }}</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('companies.search_placeholder') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="industry" class="form-label">{{ __('companies.industry') }}</label>
                            <select class="form-select" id="industry" name="industry">
                                <option value="">{{ __('companies.all_industries') }}</option>
                                <option value="manufacturing" {{ request('industry') == 'manufacturing' ? 'selected' : '' }}>{{ __('companies.manufacturing') }}</option>
                                <option value="automotive" {{ request('industry') == 'automotive' ? 'selected' : '' }}>{{ __('companies.automotive') }}</option>
                                <option value="aerospace" {{ request('industry') == 'aerospace' ? 'selected' : '' }}>{{ __('companies.aerospace') }}</option>
                                <option value="construction" {{ request('industry') == 'construction' ? 'selected' : '' }}>{{ __('companies.construction') }}</option>
                                <option value="energy" {{ request('industry') == 'energy' ? 'selected' : '' }}>{{ __('companies.energy') }}</option>
                                <option value="electronics" {{ request('industry') == 'electronics' ? 'selected' : '' }}>{{ __('companies.electronics') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="company_type" class="form-label">{{ __('companies.company_type') }}</label>
                            <select class="form-select" id="company_type" name="company_type">
                                <option value="">{{ __('companies.all_types') }}</option>
                                <option value="supplier" {{ request('company_type') == 'supplier' ? 'selected' : '' }}>{{ __('companies.supplier') }}</option>
                                <option value="manufacturer" {{ request('company_type') == 'manufacturer' ? 'selected' : '' }}>{{ __('companies.manufacturer') }}</option>
                                <option value="distributor" {{ request('company_type') == 'distributor' ? 'selected' : '' }}>{{ __('companies.distributor') }}</option>
                                <option value="service" {{ request('company_type') == 'service' ? 'selected' : '' }}>{{ __('companies.service_provider') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="location" class="form-label">{{ __('companies.location') }}</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">{{ __('companies.all_locations') }}</option>
                                <option value="ho-chi-minh" {{ request('location') == 'ho-chi-minh' ? 'selected' : '' }}>{{ __('companies.ho_chi_minh') }}</option>
                                <option value="hanoi" {{ request('location') == 'hanoi' ? 'selected' : '' }}>{{ __('companies.hanoi') }}</option>
                                <option value="da-nang" {{ request('location') == 'da-nang' ? 'selected' : '' }}>{{ __('companies.da_nang') }}</option>
                                <option value="hai-phong" {{ request('location') == 'hai-phong' ? 'selected' : '' }}>{{ __('companies.hai_phong') }}</option>
                                <option value="can-tho" {{ request('location') == 'can-tho' ? 'selected' : '' }}>{{ __('companies.can_tho') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="verification" class="form-label">{{ __('companies.verification') }}</label>
                            <select class="form-select" id="verification" name="verification">
                                <option value="">{{ __('companies.all_companies') }}</option>
                                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>{{ __('companies.verified_only') }}</option>
                                <option value="premium" {{ request('verification') == 'premium' ? 'selected' : '' }}>{{ __('companies.premium_members') }}</option>
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
        @forelse($companies as $company)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 company-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="company-logo me-3">
                            @if($company->store_logo)
                                <img src="{{ Storage::url($company->store_logo) }}" alt="{{ $company->business_name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <i class="fa-solid fa-building text-primary" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $company->business_name }}</h6>
                            <small class="text-muted">{{ $company->business_type }}</small>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        @if($company->verification_status === 'verified')
                        <span class="badge bg-success mb-1">
                            <i class="fa-solid fa-check-circle me-1"></i>
                            {{ __('companies.verified') }}
                        </span>
                        @endif
                        @if($company->is_featured)
                        <span class="badge bg-warning">
                            <i class="fa-solid fa-crown me-1"></i>
                            {{ __('companies.premium') }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-map-marker-alt me-1"></i>
                                @if(is_array($company->business_address))
                                    {{ $company->business_address['city'] ?? 'N/A' }}, {{ $company->business_address['country'] ?? 'Vietnam' }}
                                @else
                                    {{ $company->business_address ?? 'N/A' }}
                                @endif
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-envelope me-1"></i>
                                {{ $company->contact_email ?? $company->user->email }}
                            </small>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fa-solid fa-calendar me-1"></i>
                                {{ __('companies.joined') }} {{ $company->created_at->format('M Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <span class="me-2">{{ __('companies.rating') }}:</span>
                            @for($j = 1; $j <= 5; $j++)
                                <i class="fa-solid fa-star {{ $j <= $company->rating_average ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <small class="text-muted ms-2">({{ number_format($company->rating_average, 1) }}/5)</small>
                        </div>
                        <small class="text-muted">{{ $company->rating_count }} {{ __('companies.reviews') }}</small>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('companies.specialties') }}:</small>
                        <div class="mt-1">
                            @if($company->specializations && is_array($company->specializations))
                                @foreach(array_slice($company->specializations, 0, 3) as $specialty)
                                    <span class="badge bg-light text-dark me-1">{{ $specialty }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-light text-dark me-1">{{ $company->business_type }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('companies.contact') }}:</small>
                        <div class="mt-1">
                            @if($company->contact_phone)
                            <small class="d-block">
                                <i class="fa-solid fa-phone me-1"></i>
                                {{ $company->contact_phone }}
                            </small>
                            @endif
                            <small class="d-block">
                                <i class="fa-solid fa-envelope me-1"></i>
                                {{ $company->contact_email ?? $company->user->email }}
                            </small>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted">{{ __('companies.products') }}</small>
                            <div class="fw-medium">{{ $company->total_products }}+</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('companies.processing_time') }}</small>
                            <div class="fw-medium">{{ $company->processing_time_days ?? 'N/A' }} {{ __('companies.days') }}</div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary" onclick="addToFavorites({{ $company->id }}, this)">
                                @auth
                                    @if(isset($company->is_favorited) && $company->is_favorited)
                                        <i class="fa-solid fa-heart text-danger"></i>
                                    @else
                                        <i class="fa-regular fa-heart"></i>
                                    @endif
                                @else
                                    <i class="fa-regular fa-heart"></i>
                                @endauth
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="shareCompany({{ $company->id }}, this)">
                                <i class="fa-solid fa-share"></i>
                            </button>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i>
                                {{ __('companies.view_profile') }}
                            </a>
                            <a href="{{ route('companies.contact', $company->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-envelope me-1"></i>
                                {{ __('companies.contact') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fa-solid fa-building text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">{{ __('companies.no_companies_found') }}</h5>
                <p class="text-muted">{{ __('companies.adjust_search_criteria') }}</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($companies->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Industry Categories -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-industry me-2"></i>
                        {{ __('companies.browse_by_industry') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(['manufacturing', 'automotive', 'aerospace', 'construction', 'energy', 'electronics'] as $industry)
                        <div class="col-md-2 mb-3">
                            <div class="text-center">
                                <div class="bg-light rounded p-3 mb-2">
                                    <i class="fa-solid fa-industry text-primary" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="mb-1">{{ __('companies.' . $industry) }}</h6>
                                <small class="text-muted">{{ rand(3, 12) }} {{ __('companies.companies_count') }}</small>
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
                        {{ __('companies.top_rated_companies') }}
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
                <h5 class="modal-title">{{ __('companies.contact_modal_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <div class="mb-3">
                        <label for="contactSubject" class="form-label">{{ __('companies.subject') }}</label>
                        <select class="form-select" id="contactSubject">
                            <option value="inquiry">{{ __('companies.general_inquiry') }}</option>
                            <option value="quote">{{ __('companies.request_quote') }}</option>
                            <option value="partnership">{{ __('companies.partnership_opportunity') }}</option>
                            <option value="support">{{ __('companies.technical_support') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">{{ __('companies.message') }}</label>
                        <textarea class="form-control" id="contactMessage" rows="4"
                                  placeholder="{{ __('companies.message_placeholder') }}"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="contactPhone" class="form-label">{{ __('companies.phone_optional') }}</label>
                        <input type="tel" class="form-control" id="contactPhone" placeholder="{{ __('companies.phone_placeholder') }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('companies.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="sendMessage()">{{ __('companies.send_message') }}</button>
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
    showToast('{{ __('companies.message_sent_successfully') }}', 'success');
    bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
}

function addToFavorites(companyId, buttonElement) {
    // Check if user is logged in
    @auth
        fetch(`/companies/${companyId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                const icon = buttonElement.querySelector('i');
                if (data.favorited) {
                    icon.className = 'fa-solid fa-heart text-danger';
                    showToast('{{ __('companies.company_added_to_favorites') }}', 'success');
                } else {
                    icon.className = 'fa-regular fa-heart';
                    showToast('{{ __('companies.company_removed_from_favorites') }}', 'info');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __('companies.error_updating_favorites') }}', 'error');
        });
    @else
        showToast('{{ __('companies.please_login_to_add_favorites') }}', 'warning');
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    @endauth
}

function shareCompany(companyId, buttonElement) {
    const companyUrl = `${window.location.origin}/companies/${companyId}`;
    const companyName = buttonElement.closest('.card').querySelector('h6').textContent;

    if (navigator.share) {
        navigator.share({
            title: `${companyName} - MechaMap`,
            text: `Check out ${companyName} on MechaMap - Vietnam's Mechanical Engineering Community`,
            url: companyUrl
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(companyUrl).then(() => {
            showToast('{{ __('companies.company_link_copied') }}', 'success');
        }).catch(() => {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = companyUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('{{ __('companies.company_link_copied') }}', 'success');
        });
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Add to toast container or create one
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
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
