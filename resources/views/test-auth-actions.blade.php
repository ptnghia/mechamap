@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>🔐 Authentication & Thread Actions Test</h4>
                    <p class="mb-0">Test authentication status và thread bookmark/follow functionality</p>
                </div>
                <div class="card-body">
                    <!-- Authentication Status -->
                    <div class="mb-4">
                        <h5>1. Authentication Status</h5>
                        <div class="alert alert-info">
                            @auth
                            <strong>✅ Đã đăng nhập:</strong> {{ auth()->user()->name }} (ID: {{ auth()->user()->id }})
                            <br><small class="text-muted">Email: {{ auth()->user()->email }}</small>
                            @else
                            <strong>❌ Chưa đăng nhập</strong>
                            <br><a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-2">Đăng nhập</a>
                            @endauth
                        </div>
                    </div>

                    @auth
                    <!-- Sample Thread với Real Actions -->
                    <div class="mb-4">
                        <h5>2. Test Thread Actions (Real API Calls)</h5>
                        <div class="alert alert-warning">
                            <strong>⚠️ Lưu ý:</strong> Các buttons dưới đây sẽ thực hiện API calls thật đến database!
                        </div>

                        @php
                        // Lấy thread đầu tiên để test
                        $testThread = \App\Models\Thread::first();
                        @endphp

                        @if($testThread)
                        <div class="thread-item thread-item-container">
                            <div class="thread-item-header">
                                <div class="thread-user-info">
                                    <div class="flex-shrink-0 me-3 d-none d-sm-block">
                                        <img src="{{ $testThread->user->profile_photo_url ?? '/images/default-avatar.png' }}"
                                            alt="{{ $testThread->user->name }}" class="avatar">
                                    </div>
                                    <div>
                                        <strong class="thread-user-name">{{ $testThread->user->name }}</strong><br>
                                        <span class="text-muted">{{ $testThread->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="thread-title-section">
                                        <div class="thread-title">
                                            <a href="/threads/{{ $testThread->slug ?? $testThread->id }}">{{
                                                $testThread->title }}</a>
                                        </div>
                                    </div>

                                    @if($testThread->content)
                                    <p class="text-muted small mb-2 thread-content">
                                        {{ Str::limit($testThread->content, 120) }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <div class="thread-item-footer">
                                <div class="thread-meta">
                                    <span class="meta-item"><i class="bi bi-eye"></i> {{ $testThread->view_count ?? 0 }}
                                        lượt xem</span>
                                    <span class="meta-item"><i class="bi bi-chat"></i> {{ $testThread->comments_count ??
                                        0 }} phản hồi</span>
                                    @if($testThread->follow_count > 0)
                                    <span class="meta-item"><i class="bi bi-person-plus"></i> {{
                                        $testThread->follow_count }} theo dõi</span>
                                    @endif
                                </div>

                                <!-- Custom Actions với Real Data -->
                                <div class="thread-item-actions">
                                    @php
                                    $isBookmarked = $testThread->isBookmarkedBy(auth()->user());
                                    $isFollowed = \App\Models\ThreadFollow::where('thread_id', $testThread->id)
                                    ->where('user_id', auth()->id())->exists();
                                    @endphp

                                    <button class="btn btn-outline-warning bookmark-btn"
                                        data-thread-id="{{ $testThread->id }}"
                                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}">
                                        <i class="bi bi-bookmark{{ $isBookmarked ? '-fill' : '' }}"></i>
                                        {{ $isBookmarked ? 'Đã bookmark' : 'Bookmark' }}
                                    </button>

                                    <button class="btn btn-outline-info follow-btn"
                                        data-thread-id="{{ $testThread->id }}"
                                        data-followed="{{ $isFollowed ? 'true' : 'false' }}">
                                        <i class="bi bi-person-{{ $isFollowed ? 'check' : 'plus' }}"></i>
                                        {{ $isFollowed ? 'Đang theo dõi' : 'Theo dõi' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">Không tìm thấy thread nào để test</div>
                        @endif
                    </div>

                    <!-- API Response Log -->
                    <div class="mb-4">
                        <h5>3. API Response Log</h5>
                        <div id="api-log" class="bg-light p-3" style="height: 300px; overflow-y: auto;">
                            <small class="text-muted">API responses sẽ hiện ở đây...</small>
                        </div>
                    </div>

                    <!-- Database Status Check -->
                    <div class="mb-4">
                        <h5>4. Database Status</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Thread Bookmarks:</strong>
                                <pre>{{ \App\Models\ThreadBookmark::count() }} bookmarks total</pre>
                            </div>
                            <div class="col-md-6">
                                <strong>Thread Follows:</strong>
                                <pre>{{ \App\Models\ThreadFollow::count() }} follows total</pre>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <strong>ℹ️ Cần đăng nhập để test thread actions</strong>
                        <br><a href="{{ route('login') }}" class="btn btn-primary mt-2">Đăng nhập ngay</a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const apiLogDiv = document.getElementById('api-log');

    // Override fetch để log API calls
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const [url, options] = args;
        const timestamp = new Date().toLocaleTimeString();

        // Log request
        const logEntry = document.createElement('div');
        logEntry.className = 'small text-info mb-1';
        logEntry.innerHTML = `[${timestamp}] <strong>REQUEST:</strong> ${options?.method || 'GET'} ${url}`;
        apiLogDiv.appendChild(logEntry);
        apiLogDiv.scrollTop = apiLogDiv.scrollHeight;

        // Call original fetch và log response
        return originalFetch.apply(this, args).then(response => {
            const responseEntry = document.createElement('div');
            responseEntry.className = `small mb-2 ${response.ok ? 'text-success' : 'text-danger'}`;
            responseEntry.innerHTML = `[${timestamp}] <strong>RESPONSE:</strong> ${response.status} ${response.statusText}`;
            apiLogDiv.appendChild(responseEntry);
            apiLogDiv.scrollTop = apiLogDiv.scrollHeight;

            return response;
        }).catch(error => {
            const errorEntry = document.createElement('div');
            errorEntry.className = 'small text-danger mb-2';
            errorEntry.innerHTML = `[${timestamp}] <strong>ERROR:</strong> ${error.message}`;
            apiLogDiv.appendChild(errorEntry);
            apiLogDiv.scrollTop = apiLogDiv.scrollHeight;

            throw error;
        });
    };

    console.log('🔐 Authentication test page loaded');
});
</script>
@endpush