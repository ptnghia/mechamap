@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Latest Threads -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bài viết mới nhất</h5>
        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
    </div>
    <div class="list-group list-group-flush" id="latest-threads">
        @foreach($latestThreads as $thread)
        <div class="list-group-item thread-item">
            <div class="row">
                <!-- Nội dung chính: 12 cột nếu không có ảnh, 9 cột nếu có ảnh -->
                <div class="{{ $thread->featured_image ? 'col-md-9' : 'col-12' }}">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3 d-none d-sm-block">
                            <img src="{{ get_avatar_url($thread->user) }}" alt="{{ $thread->user->name }}"
                                class="avatar avatar-md">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="thread-title">
                                    <a href="{{ route('threads.show', $thread) }}">{{ $thread->title }}</a>
                                    @if($thread->is_sticky)
                                    <span class="badge bg-primary ms-1">{{ __('messages.thread_status.sticky') }}</span>
                                    @endif
                                    @if($thread->is_locked)
                                    <span class="badge bg-danger ms-1">{{ __('messages.thread_status.locked') }}</span>
                                    @endif
                                </div>
                                <small class="text-muted d-md-none">{{ $thread->created_at->diffForHumans() }}</small>
                            </div>

                            <!-- Project Details -->
                            @if($thread->status)
                            <div class="project-details mb-2 small">
                                @if($thread->status)
                                <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                                @endif
                            </div>
                            @endif

                            <!-- Mô tả ngắn thread -->
                            @if($thread->content)
                            <p class="text-muted small mb-2">{{ Str::limit(strip_tags($thread->content), 120) }}</p>
                            @endif

                            <div class="thread-meta">
                                <span class="me-3"><i class="bi bi-person"></i> {{ $thread->user->name }}</span>
                                <span class="me-3"><i class="bi bi-eye"></i> {{ $thread->view_count }} lượt xem</span>
                                <span class="me-3"><i class="bi bi-chat"></i> {{ $thread->allComments->count() }} phản
                                    hồi</span>
                                <span class="d-none d-md-inline text-muted">{{ $thread->created_at->diffForHumans()
                                    }}</span>

                                @if($thread->category)
                                <a href="{{ route('threads.index', ['category' => $thread->category->id]) }}"
                                    class="badge bg-secondary text-decoration-none ms-2">{{ $thread->category->name
                                    }}</a>
                                @endif

                                @if($thread->forum)
                                <a href="{{ route('threads.index', ['forum' => $thread->forum->id]) }}"
                                    class="badge bg-info text-decoration-none ms-1">{{ $thread->forum->name }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hình ảnh (3 cột) - chỉ hiển thị khi có ảnh -->
                @if($thread->featured_image)
                <div class="col-md-3 d-none d-md-block">
                    <div class="thread-image">
                        <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}" class="img-fluid rounded"
                            style="max-height: 100px; width: 100%; object-fit: cover;">
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="card-footer text-center">
        <button id="load-more-threads" class="btn btn-outline-primary">Tải thêm</button>
    </div>
</div>

<!-- Featured Projects -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Dự án nổi bật</h5>
    </div>
    <div class="card-body p-0">
        <div class="row g-0">
            @foreach($featuredThreads as $thread)
            <div class="col-md-6 p-3 border-bottom {{ $loop->iteration % 2 == 0 ? 'border-start' : '' }}">
                <div class="featured-project">
                    <a href="{{ route('threads.show', $thread) }}" class="mb-2 d-block">
                        @if($thread->featured_image)
                        <img src="{{ $thread->featured_image }}" alt="{{ $thread->title }}"
                            class="featured-project-image">
                        @else
                        <div class="featured-project-image bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                        @endif
                    </a>
                    <h5 class="featured-project-title"><a href="{{ route('threads.show', $thread) }}">{{ $thread->title
                            }}</a></h5>
                    <div class="project-details mb-2 small">
                        @if($thread->status)
                        <span class="badge bg-light text-dark me-2">{{ $thread->status }}</span>
                        @endif
                    </div>
                    <p class="text-muted small mb-2">{{ Str::limit(strip_tags($thread->content), 80) }}</p>
                    <div class="thread-meta">
                        <i class="bi bi-person"></i> {{ $thread->user->name }} ·
                        <i class="bi bi-calendar"></i> {{ $thread->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Biến dịch cho JavaScript
    const translations = {
        sticky: '{{ __("messages.thread_status.sticky") }}',
        locked: '{{ __("messages.thread_status.locked") }}'
    };

    // Load more threads functionality
    let page = 0; // Bắt đầu từ 0, page 1 sẽ là trang đầu tiên "load more"
    const loadMoreButton = document.getElementById('load-more-threads');

    loadMoreButton.addEventListener('click', function() {
        page++;
        console.log('Loading page:', page);

        // Hiển thị trạng thái loading
        loadMoreButton.disabled = true;
        loadMoreButton.textContent = 'Đang tải...';

        fetch(`/api/threads?page=${page}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);

                if (data.threads && data.threads.length > 0) {
                    const threadsContainer = document.getElementById('latest-threads');

                    data.threads.forEach(thread => {
                        console.log('Adding thread:', thread.title);
                        const threadElement = createThreadElement(thread);
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
                loadMoreButton.disabled = false; // Enable lại để user có thể thử lại
                loadMoreButton.textContent = 'Có lỗi xảy ra. Thử lại.';
                page--; // Rollback page number để thử lại
            });
    });

    function createThreadElement(thread) {
        const listItem = document.createElement('div');
        listItem.className = 'list-group-item thread-item';

        // Format date
        const createdAt = new Date(thread.created_at);
        const timeAgo = timeSince(createdAt);

        // Đảm bảo user có avatar
        const userAvatar = thread.user?.profile_photo_url || '/images/default-avatar.png';

        // Đảm bảo thread có slug (fallback to id)
        const threadUrl = thread.slug ? `/threads/${thread.slug}` : `/threads/${thread.id}`;

        // Xử lý content preview
        const contentPreview = thread.content ?
            (thread.content.length > 120 ? thread.content.substring(0, 120) + '...' : thread.content) : '';

        // Build the HTML với logic layout tối ưu
        listItem.innerHTML = `
            <div class="row">
                <!-- Nội dung chính: 12 cột nếu không có ảnh, 9 cột nếu có ảnh -->
                <div class="${thread.featured_image ? 'col-md-9' : 'col-12'}">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3 d-none d-sm-block">
                            <img src="${userAvatar}" alt="${thread.user?.name || 'User'}" class="avatar avatar-md">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="thread-title">
                                    <a href="${threadUrl}">${thread.title}</a>
                                    ${thread.is_sticky ? `<span class="badge bg-primary ms-1">${translations.sticky}</span>` : ''}
                                    ${thread.is_locked ? `<span class="badge bg-danger ms-1">${translations.locked}</span>` : ''}
                                </div>
                                <small class="text-muted d-md-none">${timeAgo}</small>
                            </div>

                            <!-- Project Details -->
                            ${thread.status ? `<div class="project-details mb-2 small">
                                <span class="badge bg-light text-dark me-2">${thread.status}</span>
                            </div>` : ''}

                            <!-- Mô tả ngắn thread -->
                            ${contentPreview ? `<p class="text-muted small mb-2">${contentPreview}</p>` : ''}

                            <div class="thread-meta">
                                <span class="me-3"><i class="bi bi-person"></i> ${thread.user?.name || 'Unknown'}</span>
                                <span class="me-3"><i class="bi bi-eye"></i> ${thread.view_count || 0} lượt xem</span>
                                <span class="me-3"><i class="bi bi-chat"></i> ${thread.comments_count || 0} phản hồi</span>
                                <span class="d-none d-md-inline text-muted">${timeAgo}</span>

                                ${thread.category ? `<a href="/threads?category=${thread.category.id}"
                                    class="badge bg-secondary text-decoration-none ms-2">${thread.category.name}</a>` : ''}

                                ${thread.forum ? `<a href="/threads?forum=${thread.forum.id}"
                                    class="badge bg-info text-decoration-none ms-1">${thread.forum.name}</a>` : ''}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hình ảnh (3 cột) - chỉ hiển thị khi có ảnh -->
                ${thread.featured_image ? `
                <div class="col-md-3 d-none d-md-block">
                    <div class="thread-image">
                        <img src="${thread.featured_image}" alt="${thread.title}"
                            class="img-fluid rounded" style="max-height: 100px; width: 100%; object-fit: cover;"
                            onerror="this.style.display='none'">
                    </div>
                </div>
                ` : ''}
            </div>
        `;

        return listItem;
    }

    function timeSince(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) {
            return Math.floor(interval) + " năm trước";
        }

        interval = seconds / 2592000;
        if (interval > 1) {
            return Math.floor(interval) + " tháng trước";
        }

        interval = seconds / 86400;
        if (interval > 1) {
            return Math.floor(interval) + " ngày trước";
        }

        interval = seconds / 3600;
        if (interval > 1) {
            return Math.floor(interval) + " giờ trước";
        }

        interval = seconds / 60;
        if (interval > 1) {
            return Math.floor(interval) + " phút trước";
        }

        return Math.floor(seconds) + " giây trước";
    }
</script>
@endpush

@push('styles')
<style>
    .thread-item {
        transition: all 0.3s ease;
    }

    .thread-item:hover {
        background-color: #f8f9fa;
    }

    #load-more-threads {
        transition: all 0.3s ease;
        min-width: 120px;
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

    .avatar {
        transition: transform 0.2s ease;
    }

    .avatar:hover {
        transform: scale(1.1);
    }
</style>
@endpush