@extends('layouts.app')

@section('title', t_navigation('main.home'))

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<!-- Home Page Specific CSS -->
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/home.css') }}">
@endpush

@section('content')
<!-- Featured Showcases Section -->
@if(isset($featuredShowcases) && $featuredShowcases->count() > 0)
<section class="featured-showcases mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="section-title mb-1">{{ t_homepage('sections.featured_showcases') }}</h2>
            <p class="text-muted mb-0">{{ t_homepage('sections.featured_showcases_desc') }}</p>
        </div>
        <a href="{{ route('showcase.index') }}" class="btn btn-outline-primary">
            {{ t_ui('buttons.view_all') }}
        </a>
    </div>

    <!-- Swiper Container -->
    <div class="swiper showcases-swiper">
        <div class="swiper-wrapper pb-5">
            @foreach($featuredShowcases as $showcase)
            <div class="swiper-slide">
                @include('partials.showcase-item', ['showcase' => $showcase])
            </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
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
        <button id="load-more-threads" class="btn btn-outline-primary">{{ t_ui('pagination.load_more') }}</button>
    </div>
</div>
@endsection

@push('scripts')
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
        //autoplay: {
        //    delay: 5000,
        //    disableOnInteraction: false,
        //},
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
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
});
</script>
@endpush
