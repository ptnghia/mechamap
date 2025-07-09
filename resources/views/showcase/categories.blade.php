@extends('layouts.app')

@section('title', 'Showcase Categories - MechaMap')

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
                        <h1 class="h2 mb-2">{{ __('Showcase Categories') }}</h1>
                        <p class="text-muted mb-0">{{ __('Browse showcases by category to find projects that match your interests') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('showcase.public') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('All Showcases') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        @if(count($showcasesByCategory) > 0)
        @foreach($showcasesByCategory as $categoryKey => $categoryData)
        <div class="row mb-5">
            <div class="col-12">
                <!-- Category Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h4 mb-0">
                        <i class="fas fa-folder-open text-primary me-2"></i>
                        {{ $categoryData['name'] }}
                    </h3>
                    <span class="badge bg-light text-dark">{{ $categoryData['showcases']->count() }} {{ __('showcases') }}</span>
                </div>

                <!-- Showcases in Category -->
                @if($categoryData['showcases']->count() > 0)
                <div class="row">
                    @foreach($categoryData['showcases'] as $showcase)
                    <div class="col-lg-4 col-md-6 mb-4">
                        @include('partials.showcase-item', ['showcase' => $showcase])
                    </div>
                    @endforeach
                </div>

                <!-- View More Link -->
                @if($categoryData['showcases']->count() >= 6)
                <div class="text-center mt-3">
                    <a href="{{ route('showcase.public', ['category' => $categoryKey]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>{{ __('View All') }} {{ $categoryData['name'] }} {{ __('Showcases') }}
                    </a>
                </div>
                @endif
                @else
                <!-- Empty Category -->
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-folder-open mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">{{ __('No showcases in this category yet') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Divider -->
        @if(!$loop->last)
        <hr class="my-4">
        @endif
        @endforeach
        @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-folder text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="h4 mb-3">{{ __('No Categories Available') }}</h3>
                    <p class="text-muted mb-4">{{ __('Showcase categories will appear here when showcases are created.') }}</p>
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
    // Add any specific JavaScript for categories page
    console.log('Showcase Categories page loaded');
});
</script>
@endpush
