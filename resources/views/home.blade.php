@extends('layouts.app')

@section('title', __('messages.nav.home'))

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

        <div class="row g-4">
            @foreach($featuredShowcases->take(6) as $showcase)
            <div class="col-lg-4 col-md-6">
                <div class="showcase-card h-100">
                    <div class="showcase-image">
                        <img src="{{ $showcase->getCoverImageUrl() }}" alt="{{ $showcase->title }}" class="img-fluid">
                        <div class="showcase-overlay">
                            <div class="showcase-actions">
                                <a href="{{ route('showcase.show', $showcase) }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-eye"></i> {{ __('buttons.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="showcase-content">
                        <div class="showcase-meta">
                            <img src="{{ $showcase->user->getAvatarUrl() }}" alt="{{ $showcase->user->name }}" class="author-avatar">
                            <div class="author-info">
                                <h6 class="author-name">{{ $showcase->user->name }}</h6>
                                <small class="text-muted">{{ $showcase->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <h5 class="showcase-title">
                            <a href="{{ route('showcase.show', $showcase) }}">{{ $showcase->title }}</a>
                        </h5>
                        <p class="showcase-description">{{ Str::limit($showcase->description, 100) }}</p>
                        <div class="showcase-stats">
                            <span class="stat-item">
                                <i class="fas fa-eye"></i> {{ $showcase->view_count ?? 0 }}
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-heart"></i> {{ $showcase->likes_count ?? 0 }}
                            </span>
                            @if($showcase->category)
                            <span class="badge bg-primary">{{ $showcase->category }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
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
        loading: '{{ __("common.loading") }}',
        loadMore: '{{ __("common.load_more") }}'
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

        fetch(`/api/threads?page=${page}`)
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
                        loadMoreButton.textContent = 'Tải thêm';
                    } else {
                        loadMoreButton.disabled = true;
                        loadMoreButton.textContent = 'Không còn bài viết';
                    }
                } else {
                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = 'Không còn bài viết';
                }
            })
            .catch(error => {
                console.error('Error loading more threads:', error);
                ThreadItemBuilder.removeSkeletonLoading(threadsContainer);
                loadMoreButton.disabled = false; // Enable lại để user có thể thử lại
                loadMoreButton.textContent = 'Có lỗi xảy ra. Thử lại.';
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

    .showcase-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .showcase-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .showcase-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .showcase-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
    }

    .showcase-card:hover .showcase-overlay {
        opacity: 1;
    }

    .showcase-content {
        padding: 1.5rem;
    }

    .showcase-meta {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .author-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 0.75rem;
        object-fit: cover;
    }

    .author-name {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
    }

    .showcase-title {
        margin-bottom: 0.75rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .showcase-title a {
        color: #2c3e50;
        text-decoration: none;
    }

    .showcase-title a:hover {
        color: #3498db;
    }

    .showcase-description {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .showcase-stats {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-item i {
        font-size: 0.8rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .featured-showcases {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .showcase-image {
            height: 180px;
        }

        .showcase-content {
            padding: 1rem;
        }
    }
</style>
@endpush
