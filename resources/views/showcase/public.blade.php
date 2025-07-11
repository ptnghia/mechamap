@extends('layouts.app')

@section('title', 'Dự án công khai - MechaMap')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/showcase.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="showcase-page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1>Dự án công khai</h1>
                <p>Khám phá các dự án cơ khí, sản phẩm kỹ thuật, và giải pháp công nghệ từ cộng đồng.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('showcase.create') }}" class="create-showcase-btn">
                    <i class="fas fa-plus me-2"></i>Tạo Showcase Mới
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- SECTION 1: FEATURED SHOWCASES (15 items) -->
    <div class="featured-showcases-section">
        <h2 class="section-title">
            <i class="fas fa-star"></i>
            Dự án nổi bật
        </h2>

        @if($featuredShowcases->count() > 0)
        <div class="swiper featured-swiper">
            <div class="swiper-wrapper">
                @foreach($featuredShowcases as $showcase)
                <div class="swiper-slide">
                    <div class="featured-showcase-card">
                        <div class="featured-showcase-image">
                            <img src="{{ $showcase->featured_image ?? asset('images/placeholder-showcase.jpg') }}"
                                 alt="{{ $showcase->title }}" loading="lazy">

                            @if($showcase->complexity_level)
                            <div class="showcase-badges">
                                <span class="badge badge-complexity badge-{{ $showcase->complexity_level }}">
                                    {{ ucfirst($showcase->complexity_level) }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="featured-showcase-content">
                            <h3 class="featured-showcase-title">
                                <a href="{{ route('showcase.show', $showcase) }}">{{ $showcase->title }}</a>
                            </h3>

                            <div class="featured-showcase-meta">
                                @if($showcase->rating_average > 0)
                                <div class="featured-showcase-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($showcase->rating_average))
                                        <i class="fas fa-star text-warning"></i>
                                        @elseif($i - 0.5 <= $showcase->rating_average)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        @else
                                        <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-1">{{ number_format($showcase->rating_average, 1) }}</span>
                                </div>
                                @endif

                                <div class="featured-showcase-stats">
                                    <span><i class="fas fa-eye"></i> {{ number_format($showcase->view_count) }}</span>
                                    @if($showcase->download_count > 0)
                                    <span><i class="fas fa-download"></i> {{ number_format($showcase->download_count) }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($showcase->category)
                            <div class="showcase-categories">
                                <span class="badge bg-primary">{{ ucfirst($showcase->category) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
        @else
        <div class="text-center py-4">
            <p class="mb-0">Chưa có dự án nổi bật nào.</p>
        </div>
        @endif
    </div>

    <!-- SECTION 2: CATEGORIES GRID -->
    <div class="categories-section">
        <h2 class="section-title">
            <i class="fas fa-th-large"></i>
            Danh mục dự án
        </h2>

        <div class="categories-grid">
            @foreach($categories as $category)
            <a href="{{ $category['url'] }}" class="category-card">
                <div class="category-image {{ $category['cover_image'] ? '' : 'no-image' }}">
                    @if($category['cover_image'])
                        <img src="{{ $category['cover_image'] }}" alt="{{ $category['display_name'] }}" loading="lazy">
                    @else
                        <i class="fas fa-{{ $category['name'] === 'design' ? 'drafting-compass' : ($category['name'] === 'manufacturing' ? 'industry' : 'chart-line') }}"></i>
                    @endif
                </div>

                <div class="category-content">
                    <h3 class="category-name">{{ $category['display_name'] }}</h3>

                    <div class="category-stats">
                        <div class="category-stat">
                            <div class="category-stat-number">{{ $category['showcase_count'] }}</div>
                            <div class="category-stat-label">Dự án</div>
                        </div>
                        <div class="category-stat">
                            <div class="category-stat-number">{{ number_format($category['avg_rating'], 1) }}</div>
                            <div class="category-stat-label">Đánh giá TB</div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- SECTION 3: ADVANCED SEARCH FORM -->
    <div class="search-section">
        <h2 class="section-title">
            <i class="fas fa-search"></i>
            Tìm kiếm nâng cao
        </h2>

        <form method="GET" action="{{ route('showcase.public') }}" class="search-form" id="showcaseSearchForm">
            <div class="search-group">
                <label for="search">Tên dự án</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                       placeholder="Nhập tên hoặc mô tả dự án...">
            </div>

            <div class="search-group">
                <label for="category">Danh mục</label>
                <select id="category" name="category">
                    <option value="">Tất cả danh mục</option>
                    @foreach($searchFilters['categories'] as $cat)
                    <option value="{{ $cat['value'] }}" {{ request('category') === $cat['value'] ? 'selected' : '' }}>
                        {{ $cat['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="search-group">
                <label for="complexity">Độ phức tạp</label>
                <select id="complexity" name="complexity">
                    <option value="">Tất cả mức độ</option>
                    @foreach($searchFilters['complexity_levels'] as $level)
                    <option value="{{ $level['value'] }}" {{ request('complexity') === $level['value'] ? 'selected' : '' }}>
                        {{ $level['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="search-group">
                <label for="project_type">Loại dự án</label>
                <select id="project_type" name="project_type">
                    <option value="">Tất cả loại</option>
                    @foreach($searchFilters['project_types'] as $type)
                    <option value="{{ $type['value'] }}" {{ request('project_type') === $type['value'] ? 'selected' : '' }}>
                        {{ $type['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="search-group">
                <label for="software">Phần mềm</label>
                <select id="software" name="software">
                    <option value="">Tất cả phần mềm</option>
                    @foreach($searchFilters['software_options'] as $software)
                    <option value="{{ $software }}" {{ request('software') === $software ? 'selected' : '' }}>
                        {{ $software }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="search-group">
                <label for="rating_min">Đánh giá tối thiểu</label>
                <select id="rating_min" name="rating_min">
                    <option value="">Tất cả đánh giá</option>
                    <option value="4" {{ request('rating_min') === '4' ? 'selected' : '' }}>4+ sao</option>
                    <option value="3" {{ request('rating_min') === '3' ? 'selected' : '' }}>3+ sao</option>
                    <option value="2" {{ request('rating_min') === '2' ? 'selected' : '' }}>2+ sao</option>
                </select>
            </div>
        </form>

        <div class="search-actions">
            <button type="submit" form="showcaseSearchForm" class="search-btn">
                <i class="fas fa-search me-2"></i>Tìm kiếm
            </button>
            <button type="button" class="clear-btn" onclick="clearFilters()">
                <i class="fas fa-times me-2"></i>Xóa bộ lọc
            </button>

            <div class="sort-options">
                <label for="sort">Sắp xếp:</label>
                <select id="sort" name="sort" form="showcaseSearchForm" onchange="document.getElementById('showcaseSearchForm').submit()">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>Xem nhiều nhất</option>
                    <option value="highest_rated" {{ request('sort') === 'highest_rated' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                    <option value="most_downloads" {{ request('sort') === 'most_downloads' ? 'selected' : '' }}>Tải nhiều nhất</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SECTION 4: ALL SHOWCASES LISTING (Paginated) -->
    <div class="all-showcases-section">
        <h2 class="section-title">
            <i class="fas fa-list"></i>
            Tất cả dự án
            @if(request()->hasAny(['search', 'category', 'complexity', 'project_type', 'software', 'rating_min']))
            <small class="text-muted">({{ $allShowcases->total() }} kết quả)</small>
            @endif
        </h2>

        @if($allShowcases->count() > 0)
        <div class="showcases-grid">
            @foreach($allShowcases as $showcase)
                @include('partials.showcase-item', ['showcase' => $showcase])
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $allShowcases->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>Không tìm thấy dự án nào</h4>
            <p class="text-muted">Thử thay đổi bộ lọc tìm kiếm hoặc tạo dự án mới.</p>
            <a href="{{ route('showcase.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo dự án mới
            </a>
        </div>
        @endif
    </div>
</div>
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
                slidesPerView: 4,
                spaceBetween: 20,
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
