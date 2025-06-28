@extends('admin.layouts.dason')

@section('title', 'Quản Lý Thành Viên')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-success mr-2"></i>
            Quản Lý Thành Viên
        </h1>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Thêm Thành Viên
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Thành Viên</li>
        </ol>
    </nav>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng Thành Viên
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Senior Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['senior'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['member'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Hoạt Động Hôm Nay
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_today'] ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter và Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Bộ Lọc & Tìm Kiếm</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.members') }}">
                <div class="row">
                    <!-- Tìm kiếm -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Tên, email, số điện thoại...">
                    </div>

                    <!-- Vai trò -->
                    <div class="col-md-2 mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">Tất cả</option>
                            <option value="senior" {{ request('role')==='senior' ? 'selected' : '' }}>
                                Senior Member
                            </option>
                            <option value="member" {{ request('role')==='member' ? 'selected' : '' }}>
                                Member
                            </option>
                            <option value="guest" {{ request('role')==='guest' ? 'selected' : '' }}>
                                Guest
                            </option>
                        </select>
                    </div>

                    <!-- Trạng thái -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>
                                🟢 Hoạt động
                            </option>
                            <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>
                                🔴 Tạm khóa
                            </option>
                            <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>
                                🟡 Chờ duyệt
                            </option>
                        </select>
                    </div>

                    <!-- Email verified -->
                    <div class="col-md-2 mb-3">
                        <label for="email_verified" class="form-label">Email</label>
                        <select class="form-control" id="email_verified" name="email_verified">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('email_verified')==='1' ? 'selected' : '' }}>
                                ✅ Đã xác thực
                            </option>
                            <option value="0" {{ request('email_verified')==='0' ? 'selected' : '' }}>
                                ❌ Chưa xác thực
                            </option>
                        </select>
                    </div>

                    <!-- Sắp xếp -->
                    <div class="col-md-2 mb-3">
                        <label for="sort" class="form-label">Sắp xếp</label>
                        <select class="form-control" id="sort" name="sort">
                            <option value="newest" {{ request('sort')==='newest' ? 'selected' : '' }}>
                                Mới nhất
                            </option>
                            <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>
                                Cũ nhất
                            </option>
                            <option value="name" {{ request('sort')==='name' ? 'selected' : '' }}>
                                Tên A-Z
                            </option>
                            <option value="last_login" {{ request('sort')==='last_login' ? 'selected' : '' }}>
                                Hoạt động gần đây
                            </option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i> Tìm Kiếm
                        </button>
                        <a href="{{ route('admin.users.members') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-info" onclick="exportData()">
                            <i class="fas fa-download mr-1"></i> Export Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách thành viên -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-success">
                Danh Sách Thành Viên
                <span class="badge badge-success badge-pill ml-2">{{ $members->total() }}</span>
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Hành động:</div>
                    <a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                        <i class="fas fa-check text-success mr-2"></i>Kích hoạt đã chọn
                    </a>
                    <a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                        <i class="fas fa-ban text-warning mr-2"></i>Khóa đã chọn
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="bulkAction('delete')">
                        <i class="fas fa-trash text-danger mr-2"></i>Xóa đã chọn
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($members->count() > 0)
            <!-- Bulk actions -->
            <div class="mb-3" id="bulk-actions" style="display: none;">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> thành viên đã được chọn.
                    <button class="btn btn-sm btn-success ml-2" onclick="bulkAction('activate')">
                        <i class="fas fa-check mr-1"></i> Kích hoạt
                    </button>
                    <button class="btn btn-sm btn-warning ml-1" onclick="bulkAction('deactivate')">
                        <i class="fas fa-ban mr-1"></i> Khóa
                    </button>
                    <button class="btn btn-sm btn-danger ml-1" onclick="bulkAction('delete')">
                        <i class="fas fa-trash mr-1"></i> Xóa
                    </button>
                </div>
            </div>

            <!-- Bảng dữ liệu -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="80">Avatar</th>
                            <th>Thông Tin</th>
                            <th width="120">Vai Trò</th>
                            <th width="100">Trạng Thái</th>
                            <th width="130">Hoạt Động</th>
                            <th width="150">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $user)
                        <tr>
                            <td>
                                <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                            </td>
                            <td class="text-center">
                                @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                    class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                <div class="bg-{{ $user->getRoleColor() }} rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <span class="text-white font-weight-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                                @if($user->phone)
                                <br><small class="text-muted">📞 {{ $user->phone }}</small>
                                @endif
                                @if($user->email_verified_at)
                                <br><small class="text-success"><i class="fas fa-check-circle"></i> Email đã xác
                                    thực</small>
                                @else
                                <br><small class="text-warning"><i class="fas fa-exclamation-circle"></i> Email chưa xác
                                    thực</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->getRoleColor() }} badge-pill">
                                    {{ $user->getRoleDisplayName() }}
                                </span>
                                @if($user->role === 'senior')
                                <br><small class="text-muted mt-1">⭐ Thành viên cao cấp</small>
                                @elseif($user->role === 'member')
                                <br><small class="text-muted mt-1">👤 Thành viên thường</small>
                                @else
                                <br><small class="text-muted mt-1">👁️ Khách</small>
                                @endif
                            </td>
                            <td>
                                @if($user->status === 'active')
                                <span class="badge badge-success">🟢 Hoạt động</span>
                                @elseif($user->status === 'inactive')
                                <span class="badge badge-danger">🔴 Tạm khóa</span>
                                @else
                                <span class="badge badge-warning">🟡 Chờ duyệt</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    <strong>Tham gia:</strong><br>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </small>
                                @if($user->last_login_at)
                                <br><small class="text-info">
                                    <strong>Hoạt động:</strong><br>
                                    {{ $user->last_login_at->diffForHumans() }}
                                </small>
                                @else
                                <br><small class="text-muted">Chưa đăng nhập</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm"
                                        title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm"
                                        title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($user->status === 'active')
                                    <button class="btn btn-warning btn-sm"
                                        onclick="toggleStatus({{ $user->id }}, 'inactive')" title="Khóa tài khoản">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-success btn-sm"
                                        onclick="toggleStatus({{ $user->id }}, 'active')" title="Kích hoạt">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif

                                    <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})"
                                        title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Hiển thị {{ $members->firstItem() ?? 0 }} đến {{ $members->lastItem() ?? 0 }}
                        trong tổng số {{ $members->total() }} thành viên
                    </small>
                </div>
                <div>
                    {{ $members->appends(request()->query())->links() }}
                </div>
            </div>
            @else
            <!-- Empty state -->
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-gray-400 mb-3"></i>
                <h5 class="text-gray-600">Không có thành viên nào</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'role', 'status', 'email_verified']))
                    Không tìm thấy thành viên nào phù hợp với bộ lọc hiện tại.
                    <br><a href="{{ route('admin.users.members') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="fas fa-redo mr-1"></i> Xóa bộ lọc
                    </a>
                    @else
                    Chưa có thành viên nào trong hệ thống.
                    <br><a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm mt-2">
                        <i class="fas fa-plus mr-1"></i> Thêm thành viên đầu tiên
                    </a>
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.user-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    // Individual checkbox
    $('.user-checkbox').change(function() {
        updateBulkActions();

        // Update select all checkbox
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;

        $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#select-all').prop('checked', checkedCheckboxes === totalCheckboxes);
    });
});

function updateBulkActions() {
    const checkedCount = $('.user-checkbox:checked').length;

    if (checkedCount > 0) {
        $('#bulk-actions').show();
        $('#selected-count').text(checkedCount);
    } else {
        $('#bulk-actions').hide();
    }
}

function toggleStatus(userId, newStatus) {
    const statusText = newStatus === 'active' ? 'kích hoạt' : 'khóa';

    Swal.fire({
        title: `Xác nhận ${statusText}?`,
        text: `Bạn có chắc muốn ${statusText} tài khoản này?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus === 'active' ? '#28a745' : '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Có, ${statusText}!`,
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call để thay đổi trạng thái
            $.ajax({
                url: `/admin/users/${userId}/toggle-status`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            });
        }
    });
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        text: 'Bạn có chắc muốn xóa thành viên này? Hành động này không thể hoàn tác!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Có, xóa!',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call để xóa
            $.ajax({
                url: `/admin/users/${userId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Đã xóa!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            });
        }
    });
}

function bulkAction(action) {
    const selectedUsers = $('.user-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedUsers.length === 0) {
        Swal.fire('Cảnh báo!', 'Vui lòng chọn ít nhất một thành viên.', 'warning');
        return;
    }

    let actionText = '';
    let confirmButtonColor = '';

    switch(action) {
        case 'activate':
            actionText = 'kích hoạt';
            confirmButtonColor = '#28a745';
            break;
        case 'deactivate':
            actionText = 'khóa';
            confirmButtonColor = '#ffc107';
            break;
        case 'delete':
            actionText = 'xóa';
            confirmButtonColor = '#dc3545';
            break;
    }

    Swal.fire({
        title: `Xác nhận ${actionText}?`,
        text: `Bạn có chắc muốn ${actionText} ${selectedUsers.length} thành viên đã chọn?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Có, ${actionText}!`,
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX call cho bulk action
            $.ajax({
                url: `/admin/users/bulk-action`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action,
                    user_ids: selectedUsers
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Lỗi!', 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            });
        }
    });
}

function exportData() {
    // Export Excel functionality
    window.open('/admin/users/members/export?' + new URLSearchParams(window.location.search), '_blank');
}
</script>
@endpush
@endsection