@extends('layouts.app')

@section('title', __('messages.nav.home'))

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- Home Page Specific CSS -->
<link rel="stylesheet" href="{{ asset('css/frontend/views/home.css') }}">
@endpush

@section('content')
<!-- Featured Showcases Section -->
@if(isset($featuredShowcases) && $featuredShowcases->count() > 0)
<section class="featured-showcases mb-5">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="section-title mb-1">{{ __('home.featured_showcases') }}</h2>
                        <p class="text-muted">{{ __('home.featured_showcases_desc') }}</p>
                    </div>
                    <a href="{{ route('showcase.public') }}" class="btn btn-outline-primary">
                        {{ __('buttons.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Swiper Container -->
        <div class="swiper showcases-swiper">
            <div class="swiper-wrapper">
                @foreach($featuredShowcases as $showcase)
                <div class="swiper-slide">
                    @include('partials.showcase-item', ['showcase' => $showcase])
                </div>
                @endforeach
            </div>

            <!-- Navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif

<!-- Latest Threads -->
<div class="body_left">
    <div class="list-group list-group-flush" id="latest-threads">
        @foreach($latestThreads as $thread)
        @include('partials.thread-item', ['thread' => $thread])
        @endforeach
    </div>
    <div class="text-center">
        <button id="load-more-threads" class="btn btn-outline-primary">{{ __('messages.common.load_more') }}</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Biến dịch cho JavaScript
    const translations = {
        sticky: '{{ __("status.sticky") }}',
        locked: '{{ __("status.locked") }}',
        loading: '{{ __("messages.common.loading") }}',
        loadMore: '{{ __("messages.common.load_more") }}',
        noMorePosts: '{{ __("messages.common.no_more_posts") }}',
        errorOccurred: '{{ __("messages.common.error_occurred") }}'
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

@push('styles')
<style>
    .thread-item {
        transition: all 0.3s ease;
    }

    #load-more-threads {
        transition: all 0.3s ease;
        min-width: 150px;
        border-radius: 6px;
        padding: 12px;
        background-color: var(--user-primary);
        border: 1px solid var(--user-primary);
    }

    #load-more-threads:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .thread-image img {
        transition: transform 0.2s ease;
    }

    .thread-image img:hover {
        transform: scale(1.05);
    }

    .rounded-circle {
        transition: transform 0.2s ease;
    }

    .rounded-circle:hover {
        transform: scale(1.1);
    }

    /* Featured Showcases Styles */
    .featured-showcases {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 3rem 0;
        margin-top: -1rem;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    /* Swiper Styles */
    .showcases-swiper {
        padding: 0 50px 50px 50px;
    }

    .showcases-swiper .swiper-slide {
        height: auto;
    }

    .showcases-swiper .swiper-button-next,
    .showcases-swiper .swiper-button-prev {
        color: #007bff;
        background: white;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .showcases-swiper .swiper-button-next:after,
    .showcases-swiper .swiper-button-prev:after {
        font-size: 18px;
        font-weight: bold;
    }

    .showcases-swiper .swiper-pagination-bullet {
        background: #007bff;
        opacity: 0.3;
    }

    .showcases-swiper .swiper-pagination-bullet-active {
        opacity: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .featured-showcases {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .showcases-swiper {
            padding: 0 20px 40px 20px;
        }

        .showcases-swiper .swiper-button-next,
        .showcases-swiper .swiper-button-prev {
            display: none;
        }
    }
</style>
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
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: {
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
            }
        }
    });
});
</script>
@endpush
