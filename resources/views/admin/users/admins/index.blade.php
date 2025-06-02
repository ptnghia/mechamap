@extends('admin.layouts.app')

@section('title', 'Quản lý quản trị viên')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @foreach($breadcrumbs as $breadcrumb)
            @if($loop->last)
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
            @else
            <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
            @endif
            @endforeach
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3 mb-0">Quản lý quản trị viên</h1>
            <p class="text-muted">Quản lý tài khoản Admin và Moderator</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.users.admins.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm quản trị viên
            </a>
            <a href="{{ route('admin.users.members') }}" class="btn btn-outline-secondary">
                <i class="bi bi-people"></i> Thành viên thường
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $stats['total'] }}</h5>
                    <p class="card-text small">Tổng số</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-danger">{{ $stats['admin'] }}</h5>
                    <p class="card-text small">Admin</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">{{ $stats['moderator'] }}</h5>
                    <p class="card-text small">Moderator</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">{{ $stats['online'] }}</h5>
                    <p class="card-text small">Đang online</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted">{{ $stats['banned'] }}</h5>
                    <p class="card-text small">Bị cấm</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.admins') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ $search }}"
                            placeholder="Tên, username, email...">
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">Tất cả</option>
                            <option value="admin" {{ $role==='admin' ? 'selected' : '' }}>Admin</option>
                            <option value="moderator" {{ $role==='moderator' ? 'selected' : '' }}>Moderator</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" {{ $status==='active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="banned" {{ $status==='banned' ? 'selected' : '' }}>Bị cấm</option>
                            <option value="online" {{ $status==='online' ? 'selected' : '' }}>Đang online</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort_by" class="form-label">Sắp xếp</label>
                        <select class="form-select" id="sort_by" name="sort_by">
                            <option value="created_at" {{ $sortBy==='created_at' ? 'selected' : '' }}>Ngày tạo</option>
                            <option value="name" {{ $sortBy==='name' ? 'selected' : '' }}>Tên</option>
                            <option value="email" {{ $sortBy==='email' ? 'selected' : '' }}>Email</option>
                            <option value="last_seen_at" {{ $sortBy==='last_seen_at' ? 'selected' : '' }}>Hoạt động cuối
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select class="form-select" name="sort_order">
                            <option value="desc" {{ $sortOrder==='desc' ? 'selected' : '' }}>Giảm dần</option>
                            <option value="asc" {{ $sortOrder==='asc' ? 'selected' : '' }}>Tăng dần</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Admins Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Danh sách quản trị viên ({{ $admins->total() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($admins->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Thông tin</th>
                            <th>Vai trò</th>
                            <th>Hoạt động</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $admin->getAvatarUrl() }}" alt="{{ $admin->name }}"
                                        class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">{{ $admin->name }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> {{ $admin->username }}
                                            <br>
                                            <i class="bi bi-envelope"></i> {{ $admin->email }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($admin->role === 'admin')
                                <span class="badge bg-danger">Admin</span>
                                @else
                                <span class="badge bg-warning">Moderator</span>
                                @endif
                            </td>
                            <td>
                                <small>
                                    <i class="bi bi-chat-dots"></i> {{ $admin->threads_count }} bài viết<br>
                                    <i class="bi bi-chat"></i> {{ $admin->posts_count }} bình luận<br>
                                    @if($admin->last_seen_at)
                                    <i class="bi bi-clock"></i> {{ $admin->last_seen_at->diffForHumans() }}
                                    @else
                                    <i class="bi bi-clock"></i> Chưa có hoạt động
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($admin->banned_at)
                                <span class="badge bg-danger">Bị cấm</span>
                                @elseif($admin->isOnline())
                                <span class="badge bg-success">Online</span>
                                @else
                                <span class="badge bg-secondary">Offline</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $admin->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.users.show', $admin) }}" class="btn btn-outline-info"
                                        title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.admins.edit', $admin) }}"
                                        class="btn btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.users.admins.permissions', $admin) }}"
                                        class="btn btn-outline-success" title="Phân quyền">
                                        <i class="bi bi-shield-check"></i>
                                    </a>
                                    @if($admin->id !== auth()->guard('admin')->id())
                                    <form method="POST" action="{{ route('admin.users.toggle-ban', $admin) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-warning"
                                            title="{{ $admin->banned_at ? 'Bỏ cấm' : 'Cấm' }}"
                                            onclick="return confirm('Bạn có chắc chắn?')">
                                            <i class="bi bi-{{ $admin->banned_at ? 'unlock' : 'lock' }}"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 text-muted">Không tìm thấy quản trị viên nào</h5>
                <p class="text-muted">Thử thay đổi điều kiện lọc hoặc thêm quản trị viên mới.</p>
            </div>
            @endif
        </div>
        @if($admins->hasPages())
        <div class="card-footer">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>
@endsection