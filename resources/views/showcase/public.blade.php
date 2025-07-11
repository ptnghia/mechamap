@extends('layouts.app')

@section('title', __('showcase.public_showcases') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase.css') }}">
@endpush

@section('full-width-content')
<!-- Page Header with Bootstrap 5 -->
<div class="bg-light py-4 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-6 fw-bold text-primary mb-2">{{ __('showcase.public_showcases') }}</h1>
                <p class="lead text-muted mb-0">{{ __('showcase.page_description') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('showcase.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>{{ __('showcase.create_new') }}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- SECTION 2: CATEGORIES GRID with Bootstrap 5 -->
    <section class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-th-large text-info me-3 fs-4"></i>
            <h2 class="h3 fw-bold text-dark mb-0">{{ __('showcase.project_categories') }}</h2>
        </div>

        <div class="row g-3">
            @foreach($categories as $category)
            <div class="col-md-3 col-lg-2 col-sm-4 col-6">
                <a href="{{ $category['url'] }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 category-card-hover">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            @if($category['cover_image'])
                                <img src="{{ $category['cover_image'] }}" alt="{{ $category['display_name'] }}"
                                     class="img-fluid rounded" style="max-height: 180px; object-fit: cover;">
                            @else
                                <i class="fas fa-{{ $category['name'] === 'design' ? 'drafting-compass' : ($category['name'] === 'manufacturing' ? 'industry' : 'chart-line') }}
                                   text-primary" style="font-size: 4rem;"></i>
                            @endif
                        </div>

                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold text-dark mb-3">{{ $category['display_name'] }}</h5>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="fw-bold text-primary fs-4">{{ $category['showcase_count'] }}</div>
                                    <small class="text-muted">{{ __('showcase.projects') }}</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-warning fs-4">{{ number_format($category['avg_rating'], 1) }}</div>
                                    <small class="text-muted">{{ __('showcase.avg_rating') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    <!-- SECTION 1: FEATURED SHOWCASES with Bootstrap 5 -->
    <section class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-star text-warning me-3 fs-4"></i>
            <h2 class="h3 fw-bold text-dark mb-0">{{ __('showcase.featured_projects') }}</h2>
        </div>

        @if($featuredShowcases->count() > 0)
        <div class="swiper featured-swiper">
            <div class="swiper-wrapper">
                @foreach($featuredShowcases as $showcase)
                <div class="swiper-slide">
                    @include('partials.showcase-item', ['showcase' => $showcase])
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        @else
        <div class="text-center py-5">
            <div class="text-muted">
                <i class="fas fa-star fs-1 mb-3 opacity-50"></i>
                <p class="mb-0">{{ __('showcase.no_featured_projects') }}</p>
            </div>
        </div>
        @endif
    </section>



    <!-- SECTION 3: ADVANCED SEARCH FORM with Bootstrap 5 -->
    <section class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-search text-success me-3 fs-4"></i>
            <h2 class="h3 fw-bold text-dark mb-0">{{ __('showcase.advanced_search') }}</h2>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('showcase.public') }}" id="showcaseSearchForm">
                    <div class="row g-3">
                        <div class="col-md-4 col-lg-3 col-sm-6">
                            <label for="search" class="form-label fw-semibold">{{ __('showcase.project_name') }}</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                   class="form-control" placeholder="{{ __('showcase.search_placeholder') }}">
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="category" class="form-label fw-semibold">{{ __('showcase.category') }}</label>
                            <select id="category" name="category" class="form-select">
                                <option value="">{{ __('showcase.all_categories') }}</option>
                                @foreach($searchFilters['categories'] as $cat)
                                <option value="{{ $cat['value'] }}" {{ request('category') === $cat['value'] ? 'selected' : '' }}>
                                    {{ $cat['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="complexity" class="form-label fw-semibold">{{ __('showcase.complexity') }}</label>
                            <select id="complexity" name="complexity" class="form-select">
                                <option value="">{{ __('showcase.all_levels') }}</option>
                                @foreach($searchFilters['complexity_levels'] as $level)
                                <option value="{{ $level['value'] }}" {{ request('complexity') === $level['value'] ? 'selected' : '' }}>
                                    {{ $level['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="project_type" class="form-label fw-semibold">{{ __('showcase.project_type') }}</label>
                            <select id="project_type" name="project_type" class="form-select">
                                <option value="">{{ __('showcase.all_types') }}</option>
                                @foreach($searchFilters['project_types'] as $type)
                                <option value="{{ $type['value'] }}" {{ request('project_type') === $type['value'] ? 'selected' : '' }}>
                                    {{ $type['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="software" class="form-label fw-semibold">{{ __('showcase.software') }}</label>
                            <select id="software" name="software" class="form-select">
                                <option value="">{{ __('showcase.all_software') }}</option>
                                @foreach($searchFilters['software_options'] as $software)
                                <option value="{{ $software }}" {{ request('software') === $software ? 'selected' : '' }}>
                                    {{ $software }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="rating_min" class="form-label fw-semibold">{{ __('showcase.min_rating') }}</label>
                            <select id="rating_min" name="rating_min" class="form-select">
                                <option value="">{{ __('showcase.all_ratings') }}</option>
                                <option value="4" {{ request('rating_min') === '4' ? 'selected' : '' }}>{{ __('showcase.4_plus_stars') }}</option>
                                <option value="3" {{ request('rating_min') === '3' ? 'selected' : '' }}>{{ __('showcase.3_plus_stars') }}</option>
                                <option value="2" {{ request('rating_min') === '2' ? 'selected' : '' }}>{{ __('showcase.2_plus_stars') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-4 align-items-end">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>{{ __('showcase.search') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>{{ __('showcase.clear_filters') }}
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <label for="sort" class="form-label me-2 mb-0 fw-semibold">{{ __('showcase.sort_by') }}:</label>
                                <select id="sort" name="sort" class="form-select w-auto" onchange="document.getElementById('showcaseSearchForm').submit()">
                                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>{{ __('showcase.newest') }}</option>
                                    <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>{{ __('showcase.most_viewed') }}</option>
                                    <option value="highest_rated" {{ request('sort') === 'highest_rated' ? 'selected' : '' }}>{{ __('showcase.highest_rated') }}</option>
                                    <option value="most_downloads" {{ request('sort') === 'most_downloads' ? 'selected' : '' }}>{{ __('showcase.most_downloads') }}</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('showcase.oldest') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- SECTION 4: ALL SHOWCASES LISTING with Bootstrap 5 -->
    <section class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-list text-secondary me-3 fs-4"></i>
                <h2 class="h3 fw-bold text-dark mb-0">{{ __('showcase.all_projects') }}</h2>
                @if(request()->hasAny(['search', 'category', 'complexity', 'project_type', 'software', 'rating_min']))
                <span class="badge bg-primary ms-3">{{ $allShowcases->total() }} {{ __('showcase.results') }}</span>
                @endif
            </div>
        </div>

        @if($allShowcases->count() > 0)
        <div class="row g-3 mb-4">
            @foreach($allShowcases as $showcase)
            <div class="col-md-6 col-lg-4">
                @include('partials.showcase-item', ['showcase' => $showcase])
            </div>
            @endforeach
        </div>

        <!-- Bootstrap 5 Pagination -->
        <div class="d-flex justify-content-center">
            {{ $allShowcases->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-3">{{ __('showcase.no_projects_found') }}</h4>
            <p class="text-muted mb-4">{{ __('showcase.try_different_filters') }}</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>{{ __('showcase.create_new_project') }}
            </a>
        </div>
        @endif
    </section>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Featured Showcases Swiper
    const featuredSwiper = new Swiper('.featured-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            576: {
                slidesPerView: 1,
                spaceBetween: 15,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            1200: {
                slidesPerView: 3,
                spaceBetween: 15,
            }
        }
    });
});

// Clear filters function
function clearFilters() {
    // Reset all form inputs
    document.getElementById('showcaseSearchForm').reset();

    // Remove all query parameters and redirect
    window.location.href = '{{ route("showcase.public") }}';
}

// Auto-submit form when filters change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#showcaseSearchForm select:not(#sort)');

    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optional: Auto-submit on filter change
            // document.getElementById('showcaseSearchForm').submit();
        });
    });
});
</script>
@endpush
