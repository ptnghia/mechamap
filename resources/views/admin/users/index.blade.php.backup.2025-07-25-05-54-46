@extends('admin.layouts.dason')

@section('title', 'Quản lý thành viên')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý thành viên</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý thành viên</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-user-plus me-1"></i> {{ 'Thêm thành viên' }}
    </a>
@endsection

@section('content')
    <!-- Thống kê -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ 'Tổng thành viên' }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['total']) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ 'Đang hoạt động' }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['online']) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-check fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ 'Quản trị viên' }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['admin'] + $stats['moderator']) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shield-alt fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ 'Bị cấm' }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['banned']) }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-x fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tìm kiếm và lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="{{ 'Tìm theo tên, username, email...' }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-select" name="role">
                        <option value="">{{ 'Tất cả vai trò' }}</option>
                        <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>{{ 'Admin' }}</option>
                        <option value="moderator" {{ $role === 'moderator' ? 'selected' : '' }}>{{ 'Moderator' }}</option>
                        <option value="senior" {{ $role === 'senior' ? 'selected' : '' }}>{{ 'Senior' }}</option>
                        <option value="member" {{ $role === 'member' ? 'selected' : '' }}>{{ 'Thành viên' }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">{{ 'Tất cả trạng thái' }}</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>{{ 'Đang hoạt động' }}</option>
                        <option value="banned" {{ $status === 'banned' ? 'selected' : '' }}>{{ 'Bị cấm' }}</option>
                        <option value="online" {{ $status === 'online' ? 'selected' : '' }}>{{ 'Đang online' }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select" name="sort_by">
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>{{ 'Ngày tham gia' }}</option>
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>{{ 'Tên' }}</option>
                        <option value="last_seen_at" {{ $sortBy === 'last_seen_at' ? 'selected' : '' }}>{{ 'Hoạt động gần đây' }}</option>
                        <option value="posts_count" {{ $sortBy === 'posts_count' ? 'selected' : '' }}>{{ 'Số bài viết' }}</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <select class="form-select" name="sort_order">
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>{{ 'Giảm dần' }}</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>{{ 'Tăng dần' }}</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách thành viên -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ 'Danh sách thành viên' }}</h5>
            <span class="badge bg-secondary">{{ 'Tổng' }}: {{ $users->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="50">#</th>
                            <th scope="col">{{ 'Thành viên' }}</th>
                            <th scope="col">{{ 'Vai trò' }}</th>
                            <th scope="col">{{ __('Bài viết') }}</th>
                            <th scope="col">{{ 'Ngày tham gia' }}</th>
                            <th scope="col">{{ 'Hoạt động gần đây' }}</th>
                            <th scope="col">{{ 'Trạng thái' }}</th>
                            <th scope="col" width="120">{{ 'Thao tác' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="40" height="40">
                                        <div>
                                            <div class="fw-bold">{{ $user->name }}</div>
                                            <div class="small text-muted">
                                                <span>{{ $user->username }}</span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-danger">{{ 'Admin' }}</span>
                                    @elseif($user->isModerator())
                                        <span class="badge bg-primary">{{ 'Moderator' }}</span>
                                    @elseif($user->isSenior())
                                        <span class="badge bg-success">{{ 'Senior' }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ 'Thành viên' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ 'Chủ đề' }}: {{ $user->threads_count }}</span>
                                        <span>{{ 'Bài viết' }}: {{ $user->posts_count }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($user->last_seen_at)
                                        <span data-bs-toggle="tooltip" title="{{ $user->last_seen_at->format('d/m/Y H:i:s') }}">
                                            {{ $user->last_seen_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ 'Chưa có' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->banned_at)
                                        <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $user->banned_reason }}">
                                            {{ 'Bị cấm' }}
                                        </span>
                                    @elseif($user->isOnline())
                                        <span class="badge bg-success">{{ 'Online' }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ 'Offline' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ 'Xem chi tiết' }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ 'Chỉnh sửa' }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm {{ $user->banned_at ? 'btn-outline-success' : 'btn-outline-warning' }}" data-bs-toggle="modal" data-bs-target="#banModal{{ $user->id }}" title="{{ $user->banned_at ? 'Bỏ cấm' : 'Cấm' }}">
                                            <i data-feather="{{ $user->banned_at ? 'user-check' : 'user-x' }}"></i>
                                        </button>
                                    </div>

                                    <!-- Ban Modal -->
                                    <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1" aria-labelledby="banModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="banModalLabel{{ $user->id }}">
                                                        {{ $user->banned_at ? 'Bỏ cấm thành viên' : 'Cấm thành viên' }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        @if($user->banned_at)
                                                            <p>{{ 'Bạn có chắc chắn muốn bỏ cấm thành viên này?' }}</p>
                                                            <div class="alert alert-info">
                                                                <strong>{{ 'Lý do cấm:' }}</strong> {{ $user->banned_reason }}
                                                            </div>
                                                        @else
                                                            <p>{{ 'Bạn có chắc chắn muốn cấm thành viên này?' }}</p>
                                                            <div class="mb-3">
                                                                <label for="reason{{ $user->id }}" class="form-label">{{ 'Lý do cấm' }}</label>
                                                                <textarea class="form-control" id="reason{{ $user->id }}" name="reason" rows="3" required>Vi phạm nội quy</textarea>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                                                        <button type="submit" class="btn {{ $user->banned_at ? 'btn-success' : 'btn-warning' }}">
                                                            {{ $user->banned_at ? 'Bỏ cấm' : 'Cấm' }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search fs-1 d-block mb-2"></i>
                                        {{ 'Không tìm thấy thành viên nào' }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
