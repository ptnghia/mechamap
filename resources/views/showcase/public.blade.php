@extends('layouts.app')

@section('title', 'Public Showcase - MechaMap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase.css') }}">
@endpush

@section('content')

<div class="body_page ">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 title_page">Dự án công khai</h1>
            <p class="text-muted mb-0">Khám phá các dự án cơ khí, sản phẩm kỹ thuật, và giải pháp công nghệ từ cộng đồng.</p>
        </div>
        <div>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>{{ __('Create New Showcase') }}
            </a>
        </div>
    </div>
    <!-- Featured Showcases -->
    <h5 class="title_page_sub mb-3">{{ __('Featured Showcases') }}</h5>
    @if($featuredShowcases->count() > 0)
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
    @else
    <div class="text-center py-4">
        <p class="mb-0">{{ __('No featured showcases available.') }}</p>
    </div>
    @endif

    <h5 class="title_page_sub mb-3 mt-4">{{ __('Community Showcases') }}</h5>
    <div class="showcases_list">
        @if($userShowcases->count() > 0)
        <div class="row g-3">
            @foreach($userShowcases as $showcase)
            <div class="col-lg-4 col-md-6 mb-4">
                @include('partials.showcase-item', ['showcase' => $showcase])
            </div>
            @endforeach
        </div>

        <div class="list_post_threads_footer mt-4">
            {{ $userShowcases->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <p class="mb-0">{{ __('No showcase items available.') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
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
        breakpoints: {
            640: {
                slidesPerView: 1,
                spaceBetween: 15,
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 10,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 10,
            }
        }
    });
});
</script>
@endpush
