@php use Illuminate\Support\Facades\Cache; @endphp
<!-- Sidebar dành riêng cho trang tạo threads -->
<div class="sidebar-container thread-creation-sidebar">
    <!-- Hướng dẫn viết bài -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="lightbulb-fill me-2 text-warning"></i>
                {{ __('sidebar.writing_tips') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="writing-tips">
                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>{{ __('sidebar.clear_title') }}</strong>
                            <p class="text-muted small mb-0">{{ __('sidebar.clear_title_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>{{ __('sidebar.detailed_content') }}</strong>
                            <p class="text-muted small mb-0">{{ __('sidebar.detailed_content_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item mb-3">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>{{ __('sidebar.use_images') }}</strong>
                            <p class="text-muted small mb-0">{{ __('sidebar.use_images_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="tip-item">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-check-circle-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>{{ __('sidebar.choose_right_category') }}</strong>
                            <p class="text-muted small mb-0">{{ __('sidebar.choose_right_category_desc') }}</p>
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
                <i class="fas fa-shield-alt-check-fill me-2 text-primary"></i>
                {{ __('sidebar.community_rules') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="rules-list">
                <div class="rule-item mb-2">
                    <i class="dot text-primary"></i>
                    <span class="small">{{ __('sidebar.respect_opinions') }}</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="dot text-primary"></i>
                    <span class="small">{{ __('sidebar.no_spam') }}</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="dot text-primary"></i>
                    <span class="small">{{ __('sidebar.appropriate_language') }}</span>
                </div>
                <div class="rule-item mb-2">
                    <i class="dot text-primary"></i>
                    <span class="small">{{ __('sidebar.no_personal_info') }}</span>
                </div>
                <div class="rule-item">
                    <i class="dot text-primary"></i>
                    <span class="small">{{ __('sidebar.verify_info') }}</span>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('rules') ?? '/rules' }}" class="btn btn-sm btn-outline-primary">
                    <i class="book me-1"></i>
                    {{ __('sidebar.read_full_rules') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Các danh mục phổ biến -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-folder me-2 text-info"></i>
                {{ __('sidebar.popular_categories') }}
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
                            <i class="folder me-2 text-primary" aria-hidden="true"></i>
                            @endif
                            <div>
                                <span class="fw-medium">{{ $forum->name }}</span>
                                <div class="text-muted small">{{ number_format($forum->threads_count) }} {{ __('sidebar.posts') }}</div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </small>
                    </a>
                </div>
                @empty
                <div class="list-group-item py-2 border-0">
                    <p class="text-muted mb-0 small">{{ __('sidebar.no_categories') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hỗ trợ và trợ giúp -->
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="question-circle-fill me-2 text-secondary"></i>
                {{ __('sidebar.need_support') }}
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                {{ __('sidebar.support_description') }}
            </p>
            <div class="d-grid gap-2">
                <a href="{{ route('help.writing-guide') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-comment-dots me-1"></i>
                    {{ __('sidebar.detailed_guide') }}
                </a>
                <a href="{{ route('contact') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-envelope me-1"></i>
                    {{ __('sidebar.contact_support') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê cá nhân (nếu đã đăng nhập) -->
    @auth
    <div class="card shadow-sm rounded-3 mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="person-circle me-2 text-success"></i>
                {{ __('sidebar.your_activity') }}
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
                    <span class="text-muted small">{{ __('sidebar.posts_count') }}</span>
                    <strong>{{ $userThreadsCount }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">{{ __('sidebar.comments_count') }}</span>
                    <strong>{{ $userCommentsCount }}</strong>
                </div>

                @if($userLastThread)
                <div class="last-activity">
                    <p class="text-muted small mb-1">{{ __('sidebar.recent_post') }}</p>
                    <a href="{{ route('threads.show', $userLastThread) }}" class="text-decoration-none small">
                        <i class="fas fa-arrow-right me-1"></i>
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
