@extends('layouts.app')

@section('title', t_navigation('main.home'))

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- Home Page Specific CSS -->
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/page/home.css') }}">
@endpush

@section('content')
<!-- Featured Showcases Section -->
@if(isset($featuredShowcases) && $featuredShowcases->count() > 0)
<section class="featured-showcases">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">{{ t_homepage('sections.featured_showcases') }}</h2>
            <p class="text-muted mb-0">{{ t_homepage('sections.featured_showcases_desc') }}</p>
        </div>
        <a href="{{ route('showcase.index') }}" class="btn btn-sm btn-primary">
            {{ t_ui('buttons.view_all') }}  <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>

    <!-- Swiper Container -->
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
</section>
@endif

<!-- Latest Threads -->
<div class="body_left mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">{{ t_homepage('sections.latest_threads') }}</h2>
            <p class="text-muted mb-0">{{ t_homepage('sections.latest_threads_desc') }}</p>
        </div>
        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-primary">
            {{ t_ui('buttons.view_all') }} <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
    <div class="list-group list-group-flush" id="latest-threads">
        @foreach($latestThreads as $thread)
        @include('partials.thread-item', ['thread' => $thread])
        @endforeach
    </div>
    <div class="text-center">
        <button id="load-more-threads" class="btn btn-outline-primary"><i class="fa-solid fa-spinner me-2"></i> {{ t_ui('pagination.load_more') }}</button>
    </div>
</div>

<!-- Latest Marketplace Products Section -->
@if(isset($latestProducts) && $latestProducts->count() > 0)
<section class="latest-products mb-5 mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">{{ t_marketplace('sections.latest_products') }}</h2>
            <p class="text-muted mb-0">{{ t_marketplace('sections.latest_products_desc') }}</p>
        </div>
        <a href="{{ route('marketplace.products.index') }}" class="btn btn-sm btn-primary">
            {{ t_ui('buttons.view_all') }}  <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>

    <!-- Swiper Container -->
    <div class="swiper products-swiper">
        <div class="swiper-wrapper pb-5">
            @foreach($latestProducts as $product)
            <div class="swiper-slide">
                @include('partials.product-item', ['product' => $product])
            </div>
            @endforeach
        </div>
        <!-- Navigation -->
        <div class="swiper-button-next products-next"></div>
        <div class="swiper-button-prev products-prev"></div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script src="{{ asset_versioned('js/showcase-actions.js') }}"></script>
<script>
    // Biến dịch cho JavaScript
    const translations = {
        sticky: '{{ t_ui("status.sticky") }}',
        locked: '{{ t_ui("status.locked") }}',
        loading: '{{ t_ui("common.loading") }}',
        loadMore: '{{ t_ui("pagination.load_more") }}',
        noMorePosts: '{{ t_ui("pagination.no_more_posts") }}',
        errorOccurred: '{{ t_ui("common.error_occurred") }}'
    };

    // Load more threads functionality
    let page = 0; // Bắt đầu từ 0, page 1 sẽ là trang đầu tiên "load more"
    const loadMoreButton = document.getElementById('load-more-threads');
    const threadsContainer = document.getElementById('latest-threads');

    loadMoreButton.addEventListener('click', function() {
        page++;
        console.log('Loading page:', page);

        // Hiển thị trạng thái loading và skeleton
        loadMoreButton.disabled = true;
        loadMoreButton.textContent = translations.loading;

        // Hiển thị skeleton loading
        ThreadItemBuilder.showSkeletonLoading(threadsContainer, 3);

        fetch(`/threads/load-more?page=${page}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);

                // Xóa skeleton loading
                ThreadItemBuilder.removeSkeletonLoading(threadsContainer);

                if (data.threads && data.threads.length > 0) {
                    data.threads.forEach(thread => {
                        console.log('Adding thread:', thread.title);
                        const threadElement = ThreadItemBuilder.createThreadElement(thread, translations);
                        threadsContainer.appendChild(threadElement);
                    });

                    // Reset button state nếu còn dữ liệu
                    if (data.has_more) {
                        loadMoreButton.disabled = false;
                        loadMoreButton.textContent = translations.loadMore;
                    } else {
                        loadMoreButton.disabled = true;
                        loadMoreButton.textContent = translations.noMorePosts;
                    }
                } else {
                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = translations.noMorePosts;
                }
            })
            .catch(error => {
                console.error('Error loading more threads:', error);
                ThreadItemBuilder.removeSkeletonLoading(threadsContainer);
                loadMoreButton.disabled = false; // Enable lại để user có thể thử lại
                loadMoreButton.textContent = translations.errorOccurred;
                page--; // Rollback page number để thử lại
            });
    });

</script>
@endpush

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Showcases Swiper
    const showcasesSwiper = new Swiper('.showcases-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: false, // Tắt loop để tránh warning khi ít slides
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
                slidesPerView: 2,
                spaceBetween: 10,
            }
        }
    });

    // Initialize Products Swiper
    const productsSwiper = new Swiper('.products-swiper', {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: false,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.products-next',
            prevEl: '.products-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 10,
            },
            1200: {
                slidesPerView: 3,
                spaceBetween: 10,
            }
        }
    });
});
</script>
@endpush
