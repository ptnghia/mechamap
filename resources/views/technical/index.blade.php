@extends('layouts.app')

@section('title', __('technical.index.title') . ' - MechaMap')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fa-solid fa-wrench me-3"></i>
                    {{ __('technical.index.title') }}
                </h1>
                <p class="lead text-muted">
                    {{ __('technical.index.subtitle') }}
                </p>
            </div>
        </div>

        <!-- Technical Resources Grid -->
        <div class="row g-4">
            <!-- Technical Drawings -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-drafting-compass fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.drawings.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.drawings.description') }}
                        </p>
                        <a href="{{ route('technical.drawings.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.drawings.view_more') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- CAD Files -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-cube fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.cad.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.cad.description') }}
                        </p>
                        <a href="#" class="btn btn-success">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.cad.coming_soon') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Material Database -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-flask fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.materials.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.materials.description') }}
                        </p>
                        <a href="#" class="btn btn-warning">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.cad.coming_soon') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Engineering Standards -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-book fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.standards.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.standards.description') }}
                        </p>
                        <a href="#" class="btn btn-info">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.cad.coming_soon') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calculation Tools -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-calculator fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.tools.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.tools.description') }}
                        </p>
                        <a href="#" class="btn btn-danger">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.cad.coming_soon') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manufacturing Processes -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-cogs fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title fw-bold">{{ __('technical.index.processes.title') }}</h5>
                        <p class="card-text text-muted">
                            {{ __('technical.index.processes.description') }}
                        </p>
                        <a href="#" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-right me-2"></i>{{ __('technical.index.cad.coming_soon') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="fa-solid fa-bolt me-2 text-warning"></i>
                            {{ __('technical.index.quick_access.title') }}
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('forums.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fa-solid fa-comments me-2"></i>{{ __('technical.index.quick_access.forums') }}
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-store me-2"></i>{{ __('technical.index.quick_access.marketplace') }}
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('showcase.index') }}" class="btn btn-outline-info w-100">
                                    <i class="fa-solid fa-trophy me-2"></i>{{ __('technical.index.quick_access.showcase') }}
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-warning w-100">
                                    <i class="fa-solid fa-graduation-cap me-2"></i>{{ __('technical.index.quick_access.learning') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.fa-3x {
    transition: transform 0.3s ease;
}

.card:hover .fa-3x {
    transform: scale(1.1);
}
</style>
@endpush
