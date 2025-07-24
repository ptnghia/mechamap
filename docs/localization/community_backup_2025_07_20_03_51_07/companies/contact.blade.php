@extends('layouts.app')

@section('title', __('companies.contact_company'))

@section('content')
<!-- Company Contact Form -->
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Company Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="company-avatar me-3">
                                @if($company->logo)
                                    <img src="{{ asset($company->logo) }}" alt="{{ $company->business_name }}" class="rounded-circle" width="60" height="60">
                                @else
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: #007bff; color: white; font-size: 24px; font-weight: bold;">
                                        {{ strtoupper(substr($company->business_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h1 class="h4 mb-1">{{ $company->business_name }}</h1>
                                <p class="text-muted mb-0">{{ $company->business_description ?? 'Professional mechanical parts supplier' }}</p>
                                <div class="d-flex align-items-center mt-1">
                                    @if($company->verification_status === 'verified')
                                        <span class="badge bg-success me-2">
                                            <i class="fas fa-check-circle"></i> {{ __('companies.verified') }}
                                        </span>
                                    @endif
                                    <span class="badge bg-secondary">{{ ucfirst($company->company_type ?? 'company') }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> {{ __('companies.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope"></i> {{ __('companies.contact_form_title', ['company' => $company->business_name]) }}
                    </h5>
                </div>
                <div class="card-body">
                    @guest
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('companies.login_required') }}</strong> {{ __('companies.login_required_description') }}
                            <div class="mt-2">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-sign-in-alt"></i> {{ __('companies.login') }}
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-user-plus"></i> {{ __('companies.register') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('companies.sendMessage', $company) }}" method="POST">
                            @csrf

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ __('companies.chat_system_info') }}</strong> {{ __('companies.chat_system_description') }} <a href="{{ route('chat.index') }}" class="alert-link">{{ __('companies.messages_section') }}</a>.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="inquiry_type" class="form-label">{{ __('companies.inquiry_type') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('inquiry_type') is-invalid @enderror"
                                                id="inquiry_type" name="inquiry_type" required>
                                            <option value="">{{ __('companies.select_inquiry_type') }}</option>
                                            <option value="general" {{ old('inquiry_type') === 'general' ? 'selected' : '' }}>{{ __('companies.general_info') }}</option>
                                            <option value="quote" {{ old('inquiry_type') === 'quote' ? 'selected' : '' }}>{{ __('companies.product_quote') }}</option>
                                            <option value="partnership" {{ old('inquiry_type') === 'partnership' ? 'selected' : '' }}>{{ __('companies.business_partnership') }}</option>
                                            <option value="support" {{ old('inquiry_type') === 'support' ? 'selected' : '' }}>{{ __('companies.technical_support_short') }}</option>
                                        </select>
                                        @error('inquiry_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">{{ __('companies.title') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                               id="subject" name="subject" value="{{ old('subject') }}"
                                               placeholder="{{ __('companies.title_placeholder') }}" required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">{{ __('companies.message_content') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="6"
                                          placeholder="{{ __('companies.message_content_placeholder') }}" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('companies.minimum_characters') }}</div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('companies.message_will_be_sent', ['company' => $company->business_name]) }}
                                    </small>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" onclick="history.back()">
                                        <i class="fas fa-times"></i> {{ __('companies.cancel') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> {{ __('companies.send_message') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endguest
                </div>
            </div>

            <!-- Company Contact Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book"></i> {{ __('companies.contact_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if($company->contact_person)
                                <div class="mb-3">
                                    <strong>{{ __('companies.contact_person') }}:</strong>
                                    <span class="text-muted">{{ $company->contact_person }}</span>
                                </div>
                            @endif

                            @if($company->business_email)
                                <div class="mb-3">
                                    <strong>{{ __('companies.email') }}:</strong>
                                    <a href="mailto:{{ $company->business_email }}" class="text-decoration-none">
                                        {{ $company->business_email }}
                                    </a>
                                </div>
                            @endif

                            @if($company->business_phone)
                                <div class="mb-3">
                                    <strong>{{ __('companies.phone') }}:</strong>
                                    <a href="tel:{{ $company->business_phone }}" class="text-decoration-none">
                                        {{ $company->business_phone }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($company->business_address)
                                <div class="mb-3">
                                    <strong>{{ __('companies.address') }}:</strong>
                                    <span class="text-muted">{{ $company->business_address }}</span>
                                </div>
                            @endif

                            @if($company->website)
                                <div class="mb-3">
                                    <strong>{{ __('companies.website') }}:</strong>
                                    <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                        {{ $company->website }} <i class="fas fa-external-link-alt fa-sm"></i>
                                    </a>
                                </div>
                            @endif

                            <div class="mb-3">
                                <strong>{{ __('companies.response_time') }}:</strong>
                                <span class="text-muted">{{ $company->response_time ?? '24' }} {{ __('companies.hours') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <x-sidebar />
        </div>
    </div>
</div>

<!-- Success Toast -->
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Thành công</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
.company-avatar img,
.avatar-placeholder {
    object-fit: cover;
}

.card {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.toast {
    min-width: 300px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide toast after 5 seconds
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(function(toast) {
        setTimeout(function() {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        }, 5000);
    });

    // Character counter for message
    const messageTextarea = document.getElementById('message');
    if (messageTextarea) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const formText = this.parentNode.querySelector('.form-text');
            if (length < 20) {
                formText.textContent = `Tối thiểu 20 ký tự (${length}/20)`;
                formText.className = 'form-text text-danger';
            } else {
                formText.textContent = `${length} ký tự`;
                formText.className = 'form-text text-success';
            }
        });
    }
});
</script>
@endpush
