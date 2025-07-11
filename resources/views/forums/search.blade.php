@extends('layouts.app')

@section('title', "Search Results for '{$query}' - MechaMap Forums")

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/views/forums/search.css') }}">
<link rel="stylesheet" href="{{ asset_versioned('css/frontend/components/thread-item.css') }}">
@endpush

@section('content')
<div class="py-5">
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Search Header -->
                <div class="search-header rounded-lg p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-search me-2"></i>
                                {{ __('forums.search.results') }}
                            </h1>
                            <p class="mb-0 opacity-90">
                                {{ __('forums.search.results_for') }}: <strong>"{{ $query }}"</strong>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="stats">
                                <span class="badge bg-light text-dark me-2">
                                    {{ $threads->total() }} threads
                                </span>
                                <span class="badge bg-light text-dark">
                                    {{ $posts->total() }} posts
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('forums.search') }}" class="row g-3">
                            <div class="col-md-10">
                                <input type="text" name="q" class="form-control"
                                    placeholder="Search threads, posts, and discussions..." value="{{ $query }}" required
                                    minlength="3">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Thread Results -->
                @if($threads->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comments me-2"></i>
                            Thread Results ({{ $threads->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($threads as $thread)
                        <div class="search-result-wrapper border-bottom">
                            @php
                            // Highlight search query in thread title and content for search results
                            $originalTitle = $thread->title;
                            $originalContent = $thread->content ?? $thread->body ?? '';

                            // Apply highlighting
                            $thread->title = highlightSearchQuery($thread->title, $query);
                            $thread->content = highlightSearchQuery(strip_tags($originalContent), $query);
                            @endphp

                            @include('partials.thread-item', ['thread' => $thread])

                            @php
                            // Restore original values
                            $thread->title = $originalTitle;
                            $thread->content = $originalContent;
                            @endphp
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $threads->links() }}
                    </div>
                </div>
                @endif

                <!-- Post Results -->
                @if($posts->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-comment me-2"></i>
                            Post Results ({{ $posts->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach($posts as $post)
                        <div class="search-result-item p-3 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ $post->user->getAvatarUrl() }}"
                                        alt="{{ $post->user->name }}" class="rounded-circle" width="50" height="50"
                                        style="object-fit: cover;"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(strtoupper(substr($post->user->name, 0, 1))) }}&background=6366f1&color=fff&size=200'">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('threads.show', $post->thread) }}#post-{{ $post->id }}"
                                            class="text-decoration-none">
                                            Re: {!! highlightSearchQuery($post->thread->title, $query) !!}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        {!! Str::limit(highlightSearchQuery(strip_tags($post->body), $query), 200) !!}
                                    </p>
                                    <div class="d-flex align-items-center text-sm text-muted">
                                        <span class="me-3">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $post->user->name }}
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-folder me-1"></i>
                                            <a href="{{ route('forums.show', $post->thread->forum) }}"
                                                class="text-muted text-decoration-none">
                                                {{ $post->thread->forum->name }}
                                            </a>
                                        </span>
                                        <span>
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $post->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        {{ $posts->links() }}
                    </div>
                </div>
                @endif

                <!-- No Results -->
                @if($threads->count() === 0 && $posts->count() === 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mb-2">Không tìm thấy kết quả</h4>
                        <p class="text-muted mb-4">
                            Chúng tôi không thể tìm thấy bất kỳ thread hoặc bài viết nào phù hợp với "<strong>{{ $query }}</strong>".
                        </p>
                        <div class="suggestions">
                            <h6 class="text-muted mb-2">Hãy thử:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Kiểm tra chính tả</li>
                                <li>• Sử dụng từ khóa tổng quát hơn</li>
                                <li>• Thử các từ khóa khác</li>
                                <li>• Duyệt qua <a href="{{ route('forums.index') }}">các danh mục forum</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Fixed Sidebar -->
            <div class="col-lg-3">
                <!-- Search Tips -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Mẹo tìm kiếm
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <strong>Sử dụng dấu ngoặc kép</strong> cho cụm từ chính xác: "thiết kế cơ khí"
                            </li>
                            <li class="mb-2">
                                <strong>Nhiều từ</strong> sẽ tìm kiếm threads chứa tất cả các từ
                            </li>
                            <li class="mb-2">
                                <strong>Tối thiểu 3 ký tự</strong> cần thiết để tìm kiếm
                            </li>
                            <li class="mb-0">
                                <strong>Duyệt danh mục</strong> để tìm chủ đề cụ thể hơn
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Popular Categories -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-fire me-2"></i>
                            Danh mục phổ biến
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                        $popularCategories = App\Models\Forum::withCount('threads')
                        ->where('parent_id', null)
                        ->orderBy('threads_count', 'desc')
                        ->limit(5)
                        ->get();
                        @endphp

                        @foreach($popularCategories as $category)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <a href="{{ route('forums.show', $category) }}" class="text-decoration-none">
                                {{ $category->title }}
                            </a>
                            <span class="badge bg-light text-dark">
                                {{ $category->threads_count }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Highlight search query in results
    function highlightSearchQuery(text, query) {
        if (!query) return text;

        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    // Initialize search page
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on search input
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
        }

        // Add search result click tracking
        document.querySelectorAll('.search-result-wrapper .thread-item a').forEach(link => {
            link.addEventListener('click', function() {
                // Track search result clicks if analytics is available
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'search_result_click', {
                        'search_term': '{{ $query }}',
                        'result_title': this.textContent.trim()
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
