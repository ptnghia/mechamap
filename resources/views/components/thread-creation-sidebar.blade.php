@php use Illuminate\Support\Facades\Cache; @endphp
<!-- Sidebar dành riêng cho trang tạo threads -->
<div class="sidebar-container thread-creation-sidebar">
    <!-- Hướng dẫn viết bài -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-lightbulb-fill me-2 text-warning"></i>
                Mẹo Viết Bài Hay
            </h5>
        </div>
        <div class="card-body">
            <div class="writing-tips">
                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>Tiêu đề rõ ràng</strong>
                            <p class="text-muted small mb-0">Sử dụng tiêu đề mô tả chính xác nội dung bài viết</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>Nội dung chi tiết</strong>
                            <p class="text-muted small mb-0">Cung cấp thông tin đầy đủ, ví dụ cụ thể</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>Sử dụng hình ảnh</strong>
                            <p class="text-muted small mb-0">Thêm hình ảnh để minh họa rõ hơn</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>Chọn đúng danh mục</strong>
                            <p class="text-muted small mb-0">Đăng bài đúng chuyên mục để dễ tìm kiếm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy tắc cộng đồng -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-shield-check-fill me-2 text-primary"></i>
                Quy Tắc Cộng Đồng
            </h5>
        </div>
        <div class="card-body">
            <div class="rules-list">
                <div class="rule-item mb-2">
                    <i class="bi bi-dot text-primary"></i>
                    <span class="small">Tôn trọng ý kiến của thành viên khác</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="bi bi-dot text-primary"></i>
                    <span class="small">Không spam hoặc quảng cáo không liên quan</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="bi bi-dot text-primary"></i>
                    <span class="small">Sử dụng ngôn ngữ phù hợp, văn minh</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="bi bi-dot text-primary"></i>
                    <span class="small">Không chia sẻ thông tin cá nhân</span>
                </div>
                <div class="rule-item">
                    <i class="bi bi-dot text-primary"></i>
                    <span class="small">Kiểm tra thông tin trước khi đăng</span>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('rules') ?? '/rules' }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-book me-1"></i>
                    Đọc đầy đủ quy tắc
                </a>
            </div>
        </div>
    </div>

    <!-- Các danh mục phổ biến -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-collection-fill me-2 text-info"></i>
                Danh Mục Phổ Biến
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush"> @php
                // Cache các forum phổ biến trong 1 giờ để tối ưu hiệu suất
                $popularForums = Cache::remember('popular_forums_sidebar', 3600, function () {
                return App\Models\Forum::withCount('threads')
                ->where('is_private', false) // Chỉ lấy forum công khai
                ->orderBy('threads_count', 'desc')
                ->take(5)
                ->get();
                });
                @endphp @forelse($popularForums as $forum)
                <div class="list-group-item py-2 border-0">
                    <a href="{{ route('forums.show', $forum->slug) }}"
                        class="d-flex justify-content-between align-items-center text-decoration-none"
                        aria-label="Xem forum {{ $forum->name }}">
                        <div class="d-flex align-items-center">
                            @if(isset($forum->icon) && $forum->icon)
                            <i class="{{ $forum->icon }} me-2 text-primary" aria-hidden="true"></i>
                            @else
                            <i class="bi bi-folder me-2 text-primary" aria-hidden="true"></i>
                            @endif
                            <div>
                                <span class="fw-medium">{{ $forum->name }}</span>
                                <div class="text-muted small">{{ number_format($forum->threads_count) }} bài đăng</div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </small>
                    </a>
                </div>
                @empty
                <div class="list-group-item py-2 border-0">
                    <p class="text-muted mb-0 small">Chưa có danh mục nào.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hỗ trợ và trợ giúp -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-question-circle-fill me-2 text-secondary"></i>
                Cần Hỗ Trợ?
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Gặp khó khăn khi tạo bài viết? Chúng tôi sẵn sàng hỗ trợ bạn!
            </p>
            <div class="d-grid gap-2">
                <a href="{{ route('help.writing-guide') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chat-dots me-1"></i>
                    Hướng dẫn chi tiết
                </a>
                <a href="{{ route('contact') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-envelope me-1"></i>
                    Liên hệ hỗ trợ
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê cá nhân (nếu đã đăng nhập) -->
    @auth
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-person-circle me-2 text-success"></i>
                Hoạt Động Của Bạn
            </h5>
        </div>
        <div class="card-body"> @php
            try {
            $userThreadsCount = Auth::user()->threads()->count();
            $userCommentsCount = method_exists(Auth::user(), 'comments') ? Auth::user()->comments()->count() : 0;
            $userLastThread = Auth::user()->threads()->latest()->first();
            } catch (Exception $e) {
            $userThreadsCount = 0;
            $userCommentsCount = 0;
            $userLastThread = null;
            }
            @endphp

            <div class="user-stats">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Bài đăng</span>
                    <strong>{{ $userThreadsCount }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Bình luận</span>
                    <strong>{{ $userCommentsCount }}</strong>
                </div>

                @if($userLastThread)
                <div class="last-activity">
                    <p class="text-muted small mb-1">Bài viết gần nhất:</p>
                    <a href="{{ route('threads.show', $userLastThread) }}" class="text-decoration-none small">
                        <i class="bi bi-arrow-right me-1"></i>
                        {{ Str::limit($userLastThread->title, 30) }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endauth
</div>

<style>
    .sidebar-container .card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .sidebar-container .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .tip-item,
    .rule-item {
        transition: all 0.2s ease;
    }

    .tip-item:hover,
    .rule-item:hover {
        padding-left: 8px;
    }

    .user-stats {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.05), rgba(40, 167, 69, 0.05));
        border-radius: 8px;
        padding: 12px;
    }

    .last-activity a:hover {
        color: #007bff !important;
    }

    @media (max-width: 768px) {
        .sidebar-container {
            margin-top: 20px;
        }
    }
</style>