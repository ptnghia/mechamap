@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Latest Threads -->
<div class="card">
    <div class="list-group list-group-flush" id="latest-threads">
        @foreach($latestThreads as $thread)
        @include('partials.thread-item', ['thread' => $thread])
        @endforeach
    </div>
    <div class="card-footer text-center">
        <button id="load-more-threads" class="btn btn-outline-primary">Tải thêm</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/thread-item.js') }}"></script>
<script>
    // Biến dịch cho JavaScript
    const translations = {
        sticky: '{{ __("messages.thread_status.sticky") }}',
        locked: '{{ __("messages.thread_status.locked") }}'
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
        loadMoreButton.textContent = 'Đang tải...';

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
