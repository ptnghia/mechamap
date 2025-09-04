@extends('layouts.app-full')

@section('title', __('showcase.public_showcases') . ' - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/showcase.css') }}">
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@push('scripts')
<script src="{{ asset_versioned('js/showcase-search.js') }}"></script>
@endpush

@section('content')
<div class="body_page">
    <div class="d-flex justify-content-between align-items-center mb-4 g-3">
        <div class="div_title_page">
            <h1 class="h2 mb-1 title_page">{{ seo_title_short( __('showcase.public_showcases')) }}</h1>
            <p class="text-muted mb-0">{{ seo_value('description',__('showcase.page_description'))  }}</p>
        </div>
        <a href="{{ route('showcase.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>{{ __('showcase.create_new') }}
        </a>
    </div>
    <!-- SECTION 0: DANH MỤC showcase with Bootstrap 5 -->
    <section class="mb-5">
        @if(count($categories) > 0)
        <div class="swiper showcases_categories-swiper">
            <div class="swiper-wrapper pb-3">
                @foreach($categories as $category)
                <div class="swiper-slide">
                    <a href="{{ $category['url'] }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 category-card-hover showcases_categories_item">
                            <!-- Category Image/Icon -->
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center position-relative" style="height: 120px;">
                                @if($category['cover_image'])
                                    <img src="{{ $category['cover_image'] }}" alt="{{ $category['display_name'] }}" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;">
                                @else
                                    <i class="{{ $category['icon'] }} text-primary" style="font-size: 3rem;"></i>
                                @endif
                            </div>

                            <!-- Category Info -->
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold mb-2 text-dark">{{ $category['display_name'] }}</h6>

                                @if($category['description'])
                                <p class="card-text text-muted small mb-3" style="font-size: 0.85rem; line-height: 1.3;">
                                    {{ Str::limit($category['description'], 60) }}
                                </p>
                                @endif

                                <!-- Statistics -->
                                <div class="row g-1 text-center">
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-project-diagram text-primary mb-1" style="font-size: 0.8rem;"></i>
                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $category['showcase_count'] }}</span>
                                            <span class="text-muted d-none" style="font-size: 0.65rem;">{{ __('showcase.projects') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-file-alt text-success mb-1" style="font-size: 0.8rem;"></i>
                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $category['file_count'] }}</span>
                                            <span class="text-muted d-none" style="font-size: 0.65rem;">{{ __('showcase.files') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-download text-info mb-1" style="font-size: 0.8rem;"></i>
                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $category['download_count'] }}</span>
                                            <span class="text-muted d-none" style="font-size: 0.65rem;">{{ __('showcase.downloads') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            <!-- Navigation -->
            <div class="swiper-button-next showcasescategories-next"></div>
            <div class="swiper-button-prev showcasescategories-prev"></div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
            <p class="text-muted">{{ __('showcase.no_categories_available') }}</p>
        </div>
        @endif
    </section>

    <!-- SECTION 1: FEATURED SHOWCASES with Bootstrap 5 -->
    <section class="mb-3">
        <div class="text-center mb-4 title_page_sub">
            <h2 class="h3 text-dark mb-0">{{ __('showcase.featured_projects') }}</h2>
        </div>

        @if($featuredShowcases->count() > 0)
        <div class="swiper showcases-swiper">
            <div class="swiper-wrapper pb-3">
                @foreach($featuredShowcases as $showcase)
                <div class="swiper-slide">
                    @include('partials.showcase-item', ['showcase' => $showcase])
                </div>
                @endforeach
            </div>
            <!-- Navigation -->
            <div class="swiper-button-next showcases-next"></div>
            <div class="swiper-button-prev showcases-prev"></div>
        </div>
        @endif
    </section>

    <!-- SECTION: ADVANCED SEARCH FORM -->
    @include('partials.showcase-search-form', ['searchFilters' => $searchFilters])




    <!-- SECTION 4: ALL SHOWCASES LISTING with Bootstrap 5 -->
    <section class="mb-5">
        <div class="text-center title_page_sub mt-5 d-flex align-items-center justify-content-center">
            <h2 class="h3 text-dark mb-0 d-flex align-content-center">{{ __('showcase.all_projects') }}
                @if(request('category'))
                {{ collect($searchFilters['categories'])->firstWhere('value', request('category'))['label'] ?? request('category') }}
                @endif
            </h2>
        </div>

        @if($allShowcases->count() > 0)
        <div class="row g-3 mb-4">
            @foreach($allShowcases as $showcase)
            <div class="col-md-4 col-lg-3 col-sm-6">
                @include('partials.showcase-item', ['showcase' => $showcase])
            </div>
            @endforeach
        </div>

        <!-- Bootstrap 5 Pagination -->
        <div class="">
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
<script src="{{ asset_versioned('js/showcase-actions.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Featured Showcases Swiper
    const featuredSwiper = new Swiper('.showcases-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true, // Tắt loop để tránh warning khi ít slides
        autoplay: {
           delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.showcases-next',
            prevEl: '.showcases-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 15,
            },
            768: {
                slidesPerView:2,
                spaceBetween: 10,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 10,
            },
            1366: {
                slidesPerView: 4,
                spaceBetween: 10,
            }
        }
    });
    const showcases_categoriesSwiper = new Swiper('.showcases_categories-swiper', {
        slidesPerView: 3,
        spaceBetween: 10,
        loop: true, // Tắt loop để tránh warning khi ít slides
        //autoplay: {
        //   delay: 5000,
        //    disableOnInteraction: false,
        //},
        navigation: {
            nextEl: '.showcasescategories-next',
            prevEl: '.showcasescategories-prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 3,
                spaceBetween: 15,
            },
            768: {
                slidesPerView:4,
                spaceBetween: 10,
            },
            1024: {
                slidesPerView: 6,
                spaceBetween: 10,
            },
            1366: {
                slidesPerView: 6,
                spaceBetween: 10,
            }
        }
    });
});


</script>

{{-- Showcase Actions JavaScript --}}
<script src="{{ asset('js/showcase-actions.js') }}"></script>
@endpush
