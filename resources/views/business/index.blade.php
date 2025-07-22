@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3>{{ __('business.title') }}</h3>
                                <p class="lead">{{ __('business.subtitle') }}</p>
                                <a href="{{ route('business.services') }}" class="btn btn-primary">{{ __('business.explore_services') }}</a>
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="{{ placeholder_image(300, 200, 'Business Growth') }}" alt="Business Growth"
                                    class="img-fluid rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fs-1 text-primary mb-3"></i>
                        <h4>{{ __('business.increase_visibility') }}</h4>
                        <p>{{ __('business.visibility_description') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-users fs-1 text-primary mb-3"></i>
                        <h4>{{ __('business.build_connections') }}</h4>
                        <p>{{ __('business.connections_description') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fs-1 text-primary mb-3"></i>
                        <h4>{{ __('business.track_performance') }}</h4>
                        <p>{{ __('business.performance_description') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('business.success_stories') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="https://ui-avatars.com/api/?name=Coteccons&size=64&background=0d6efd&color=fff&rounded=true"
                                            alt="Coteccons Logo" class="rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>{{ __('business.coteccons_name') }}</h5>
                                        <p class="mb-1">{{ __('business.coteccons_testimonial') }}</p>
                                        <small class="text-muted">{{ __('business.coteccons_author') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="https://ui-avatars.com/api/?name=VinGroup&size=64&background=dc3545&color=fff&rounded=true"
                                            alt="VinGroup Logo" class="rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5>{{ __('business.vingroup_name') }}</h5>
                                        <p class="mb-1">{{ __('business.vingroup_testimonial') }}</p>
                                        <small class="text-muted">{{ __('business.vingroup_author') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
