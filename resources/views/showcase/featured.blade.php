@extends('layouts.app')

@section('title', 'Featured Showcases - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/showcase-item.css') }}">
@endpush

@section('content')

<div class="py-5">
    <div class="container">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2">{{ __('Featured Showcases') }}</h1>
                        <p class="text-muted mb-0">{{ __('Discover outstanding mechanical engineering projects from our community') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('showcase.public') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('All Showcases') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Showcases Grid -->
        @if($showcases->count() > 0)
        <div class="row">
            @foreach($showcases as $showcase)
            <div class="col-lg-4 col-md-6 mb-4">
                @include('partials.showcase-item', ['showcase' => $showcase])
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $showcases->links() }}
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-star text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="h4 mb-3">{{ __('No Featured Showcases Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('Featured showcases will appear here when they are selected by our community moderators.') }}</p>
                    <a href="{{ route('showcase.public') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>{{ __('Browse All Showcases') }}
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Call to Action -->
        @auth
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body text-center py-4">
                        <h4 class="mb-3">{{ __('Share Your Engineering Project') }}</h4>
                        <p class="text-muted mb-4">{{ __('Showcase your mechanical engineering projects and get recognition from the community.') }}</p>
                        <a href="{{ route('showcase.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>{{ __('Create Showcase') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add any specific JavaScript for featured showcases page
    console.log('Featured Showcases page loaded');
});
</script>
@endpush
