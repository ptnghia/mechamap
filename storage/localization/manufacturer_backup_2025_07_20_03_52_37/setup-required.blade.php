@extends('layouts.app')

@section('title', __('messages.setup_required'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        {{ __('messages.setup_required') }}
                    </h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h5 class="mb-3">{{ __('messages.manufacturer_setup_required') }}</h5>
                    <p class="text-muted mb-4">
                        {{ __('messages.manufacturer_setup_description') }}
                    </p>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="setup-step">
                                <div class="step-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h6>{{ __('messages.business_profile') }}</h6>
                                <p class="small text-muted">{{ __('messages.complete_business_info') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="setup-step">
                                <div class="step-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <h6>{{ __('messages.verification_documents') }}</h6>
                                <p class="small text-muted">{{ __('messages.upload_business_documents') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="setup-step">
                                <div class="step-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <h6>{{ __('messages.marketplace_setup') }}</h6>
                                <p class="small text-muted">{{ __('messages.configure_seller_profile') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-md-2">
                            <i class="fas fa-rocket me-2"></i>
                            {{ __('messages.start_setup') }}
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>
                            {{ __('messages.back_to_home') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        {{ __('messages.need_help') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('messages.manufacturer_benefits') }}</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.sell_technical_products') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.access_b2b_marketplace') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.connect_with_suppliers') }}</li>
                                <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.verified_business_badge') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('messages.setup_requirements') }}</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-file-alt text-info me-2"></i>{{ __('messages.business_registration') }}</li>
                                <li><i class="fas fa-id-card text-info me-2"></i>{{ __('messages.tax_identification') }}</li>
                                <li><i class="fas fa-building text-info me-2"></i>{{ __('messages.company_address') }}</li>
                                <li><i class="fas fa-phone text-info me-2"></i>{{ __('messages.contact_information') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.setup-step {
    text-align: center;
    padding: 1rem;
}

.step-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
}

.setup-step h6 {
    color: #333;
    margin-bottom: 0.5rem;
}
</style>
@endsection
