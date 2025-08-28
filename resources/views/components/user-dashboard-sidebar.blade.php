@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();

    // Define menu items based on user role
    $menuItems = [
        'dashboard' => [
            'title' => t_sidebar('user_dashboard.dashboard'),
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'dashboard',
            'badge' => null,
        ],
        'my-threads' => [
            'title' => t_sidebar('user_dashboard.my_threads'),
            'icon' => 'fas fa-comments',
            'route' => 'dashboard.community.threads.index',
            'badge' => $user->threads()->count(),
        ],
        'comments' => [
            'title' => t_sidebar('user_dashboard.my_comments'),
            'icon' => 'fas fa-comment-dots',
            'route' => 'dashboard.community.comments.index',
            'badge' => $user->comments()->count(),
        ],
        'bookmarks' => [
            'title' => t_sidebar('user_dashboard.bookmarks'),
            'icon' => 'fas fa-bookmark',
            'route' => 'dashboard.community.bookmarks.index',
            'badge' => $user->bookmarks()->count(),
        ],
        'activity' => [
            'title' => t_sidebar('user_dashboard.activity'),
            'icon' => 'fas fa-chart-line',
            'route' => 'dashboard.activity',
            'badge' => null,
        ],
        // 'following' => [
        //     'title' => t_sidebar('user_dashboard.following'),
        //     'icon' => 'fas fa-heart',
        //     'route' => 'dashboard.community.following.index',
        //     'badge' => $user->following()->count(),
        // ],
        // 'ratings' => [
        //     'title' => t_sidebar('user_dashboard.ratings'),
        //     'icon' => 'fas fa-star',
        //     'route' => 'dashboard.community.ratings.index',
        //     'badge' => 0, // TODO: Implement ratings count
        // ],

    ];

    if ($user->role === 'guest') {
        // Remove some features for guest users
        unset($menuItems['ratings']);
    }
@endphp

<div class="user-dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-info">
            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="user-avatar">
            <div class="user-details">
                <h6 class="user-name">{{ $user->name }}</h6>
                <span class="user-role badge badge-primary">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav nav-pills flex-column">
            @foreach($menuItems as $key => $item)
                <li class="nav-item">
                    <a href="{{ route($item['route']) }}"
                       class="nav-link {{ $currentRoute === $item['route'] ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }} me-2"></i>
                        <span>{{ $item['title'] }}</span>
                        @if($item['badge'] && $item['badge'] > 0)
                            <span class="badge bg-primary ms-auto">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <!-- Quick Stats -->
    <div class="sidebar-stats mt-4">
        <h6 class="stats-title">{{ t_sidebar('user_dashboard.quick_stats') }}</h6>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $user->threads()->count() }}</div>
                <div class="stat-label">{{ t_sidebar('user_dashboard.threads') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">0</div>
                <div class="stat-label">{{ t_sidebar('user_dashboard.comments') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">0</div>
                <div class="stat-label">{{ t_sidebar('user_dashboard.following') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $user->points ?? 0 }}</div>
                <div class="stat-label">{{ t_sidebar('user_dashboard.points') }}</div>
            </div>
        </div>
    </div>

    <!-- Role-specific Quick Actions -->
    @if($user->role === 'guest')
        <div class="sidebar-upgrade mt-4">
            <div class="upgrade-card">
                <h6>{{ t_sidebar('user_dashboard.upgrade_account') }}</h6>
                <p class="small text-muted">{{ t_sidebar('user_dashboard.upgrade_to_member_desc') }}</p>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    {{ t_sidebar('user_dashboard.upgrade_now') }}
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.user-dashboard-sidebar {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.sidebar-header .user-info {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-right: 12px;
}

.user-details .user-name {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.user-role {
    font-size: 0.75rem;
}

.sidebar-nav .nav-link {
    color: #6c757d;
    border-radius: 6px;
    margin-bottom: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
}

.sidebar-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.sidebar-nav .nav-link.active {
    background-color: #007bff;
    color: white;
}

.sidebar-stats {
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.stats-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #495057;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #007bff;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 2px;
}

.upgrade-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 6px;
    text-align: center;
}

.upgrade-card h6 {
    color: white;
    margin-bottom: 0.5rem;
}

.upgrade-card .btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
}

.upgrade-card .btn:hover {
    background: rgba(255,255,255,0.3);
}
</style>
