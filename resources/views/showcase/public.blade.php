@extends('layouts.app-full')

@section('title', __('showcase.public_showcases') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/showcase.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-4">
            <x-sidebar />
        </div>
        <div class="col-lg-9 col-md-8">
            <!-- Page Header -->
            <div class="bg-light py-3 mb-4 mx-n3">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h1 class="h3 fw-bold text-primary mb-1">{{ __('showcase.public_showcases') }}</h1>
                        <p class="text-muted mb-0 small">{{ __('showcase.page_description') }}</p>
                    </div>
                    <div class="col-4 text-end">
                        <a href="{{ route('showcase.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('showcase.create_new') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- SECTION: SEARCH TAGS -->
            @if(request()->hasAny(['search', 'category', 'complexity', 'project_type', 'software', 'rating_min', 'sort']))
            <div class="search-tags-container mb-4">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="search-tags-label">
                        <i class="fas fa-filter me-1"></i>{{ __('showcase.active_filters') }}:
                    </span>

                    @if(request('search'))
                    <span class="search-tag">
                        <i class="fas fa-search me-1"></i>
                        "{{ request('search') }}"
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('search')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('category'))
                    <span class="search-tag">
                        <i class="fas fa-th-large me-1"></i>
                        {{ collect($searchFilters['categories'])->firstWhere('value', request('category'))['label'] ?? request('category') }}
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('category')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('complexity'))
                    <span class="search-tag">
                        <i class="fas fa-layer-group me-1"></i>
                        {{ collect($searchFilters['complexity_levels'])->firstWhere('value', request('complexity'))['label'] ?? request('complexity') }}
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('complexity')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('project_type'))
                    <span class="search-tag">
                        <i class="fas fa-cube me-1"></i>
                        {{ collect($searchFilters['project_types'])->firstWhere('value', request('project_type'))['label'] ?? request('project_type') }}
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('project_type')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('software'))
                    <span class="search-tag">
                        <i class="fas fa-cogs me-1"></i>
                        {{ request('software') }}
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('software')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('rating_min'))
                    <span class="search-tag">
                        <i class="fas fa-star me-1"></i>
                        {{ request('rating_min') }}+ {{ __('showcase.stars') }}
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('rating_min')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    @if(request('sort') && request('sort') !== 'newest')
                    <span class="search-tag">
                        <i class="fas fa-sort me-1"></i>
                        @switch(request('sort'))
                            @case('most_viewed')
                                {{ __('showcase.most_viewed') }}
                                @break
                            @case('highest_rated')
                                {{ __('showcase.highest_rated') }}
                                @break
                            @case('most_downloads')
                                {{ __('showcase.most_downloads') }}
                                @break
                            @case('oldest')
                                {{ __('showcase.oldest') }}
                                @break
                            @default
                                {{ request('sort') }}
                        @endswitch
                        <button type="button" class="search-tag-remove" onclick="removeSearchFilter('sort')">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                    @endif

                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="clearAllFilters()">
                        <i class="fas fa-times-circle me-1"></i>{{ __('showcase.clear_all') }}
                    </button>
                </div>
            </div>
            @endif

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
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Remove individual search filter
function removeSearchFilter(filterName) {
    const url = new URL(window.location);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

// Clear all filters
function clearAllFilters() {
    const url = new URL(window.location);
    // Keep only the base path
    window.location.href = url.origin + url.pathname;
}
</script>
@endpush
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


</script>

{{-- Showcase Actions JavaScript --}}
<script src="{{ asset('js/showcase-actions.js') }}"></script>
@endpush
