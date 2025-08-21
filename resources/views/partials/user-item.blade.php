@php
// Thiết lập các biến cần thiết cho user item với error handling
$userName = $user->name ?? 'Người dùng';
$userUsername = $user->username ?? 'user';
$userAvatar = $user->getAvatarUrl() ?? route('avatar.generate', ['initial' => strtoupper(substr($userName, 0, 1))]);
$userProfileUrl = route('profile.show', $userUsername);

// User stats - sử dụng counts đã load sẵn nếu có
$threadsCount = $user->threads_count ?? $user->threads()->count() ?? 0;
$postsCount = $user->posts_count ?? $user->posts()->count() ?? 0;
$joinedDate = $user->created_at ? $user->created_at->format('M Y') : 'N/A';
@endphp

<div class="col-md-3">
    <div class="card h-100 item_member_grid">
        <div class="card-body text-center">
            <!-- User Avatar -->
            <img src="{{ $userAvatar }}"
                 alt="{{ $userName }}"
                 class="rounded-circle mb-3"
                 width="80"
                 height="80"
                 style="object-fit: cover;"
                 onerror="this.src='{{ route('avatar.generate', ['initial' => strtoupper(substr($userName, 0, 1))]) }}'">

            <!-- User Name -->
            <h5 class="card-title mb-1">
                <a href="{{ $userProfileUrl }}" class="text-decoration-none">
                    {{ $userName }}
                </a>
            </h5>

            <!-- Username -->
            <p class="text-muted mb-2">{{ '@' . $userUsername }}</p>

            <!-- Online Status & Role Badges -->
            @if(method_exists($user, 'isOnline') && $user->isOnline())
                <span class="badge bg-success mb-2">{{ function_exists('t_common') ? t_common("members.online") : 'Trực tuyến' }}</span>
            @endif

            @if($user->role == 'admin' || $user->role == 'super_admin')
                <span class="badge bg-danger mb-2">{{ function_exists('t_common') ? t_common("members.admin") : 'Quản trị' }}</span>
            @elseif(in_array($user->role, ['moderator', 'community_moderator', 'content_moderator', 'marketplace_moderator']))
                <span class="badge bg-primary mb-2">{{ function_exists('t_common') ? t_common("members.moderator") : 'Điều hành' }}</span>
            @elseif(in_array($user->role, ['manufacturer', 'supplier', 'brand']))
                <span class="badge bg-success mb-2">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
            @elseif($user->role == 'senior_member')
                <span class="badge bg-info mb-2">Thành viên cao cấp</span>
            @endif

            <!-- User Stats Grid -->
            <div class="row text-center">
                <div class="col-4">
                    <div class="fw-bold">{{ $postsCount }}</div>
                    <div class="small text-muted">Bài viết</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold">{{ $threadsCount }}</div>
                    <div class="small text-muted">Chủ đề</div>
                </div>
                <div class="col-4">
                    <div class="fw-bold">{{ $joinedDate }}</div>
                    <div class="small text-muted">Tham gia</div>
                </div>
            </div>
        </div>
    </div>
</div>
