@extends('admin.layouts.dason')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Roles & Permissions</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item active">Roles & Permissions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Roles</p>
                            <h4 class="mb-0">{{ $stats['total_roles'] }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users-cog font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Roles Hoạt Động</p>
                            <h4 class="mb-0">{{ $stats['active_roles'] }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Permissions</p>
                            <h4 class="mb-0">{{ $stats['total_permissions'] }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-key font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">System Roles</p>
                            <h4 class="mb-0">{{ $stats['system_roles'] }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-shield-alt font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Quản Lý Roles & Permissions</h4>
                            <p class="card-title-desc">Quản lý vai trò và phân quyền hệ thống MechaMap</p>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Tạo Role Mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nhóm Role</label>
                                <select name="role_group" class="form-select">
                                    <option value="">Tất cả nhóm</option>
                                    @foreach($roleGroups as $key => $group)
                                        <option value="{{ $key }}" {{ $roleGroup === $key ? 'selected' : '' }}>
                                            {{ $group['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Tìm theo tên role..." value="{{ $search }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Lọc
                                    </button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Role</th>
                                    <th width="15%">Nhóm</th>
                                    <th width="10%">Level</th>
                                    <th width="10%">Users</th>
                                    <th width="15%">Permissions</th>
                                    <th width="10%">Trạng thái</th>
                                    <th width="15%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-{{ $role->color }} text-white">
                                                    <i class="{{ $role->icon }}"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $role->display_name }}</h6>
                                                <small class="text-muted">{{ $role->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $role->role_group_color }}">
                                            <i class="{{ $role->role_group_icon }} me-1"></i>
                                            {{ $role->role_group_display_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $role->hierarchy_level }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $role->users_count ?? 0 }}</span>
                                        @if($role->max_users)
                                            <small class="text-muted">/ {{ $role->max_users }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $role->permissions->count() }}</span>
                                        <a href="#" class="text-primary ms-1"
                                           onclick="viewPermissions({{ $role->id }})"
                                           title="Xem permissions">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                    <td>
                                        @if($role->is_active)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-danger">Vô hiệu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.roles.show', $role) }}"
                                               class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(!$role->is_system || $role->name !== 'super_admin')
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        onclick="toggleStatus({{ $role->id }})"
                                                        title="Thay đổi trạng thái">
                                                    <i class="fas fa-toggle-{{ $role->is_active ? 'on' : 'off' }}"></i>
                                                </button>
                                            @endif

                                            @if(!$role->is_system)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteRole({{ $role->id }})"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-2"></i>
                                            <p>Không tìm thấy role nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permissions của Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="permissionModalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// View permissions
function viewPermissions(roleId) {
    console.log('viewPermissions called with roleId:', roleId);
    fetch(`/admin/roles/${roleId}/permissions`)
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                let html = '';
                Object.keys(data.permissions).forEach(category => {
                    html += `<h6 class="text-primary">${category}</h6>`;
                    html += '<ul class="list-unstyled ms-3">';
                    data.permissions[category].forEach(permission => {
                        html += `<li><i class="fas fa-check text-success me-2"></i>${permission.display_name}</li>`;
                    });
                    html += '</ul>';
                });
                document.getElementById('permissionModalBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('permissionModal')).show();
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

// Toggle status
function toggleStatus(roleId) {
    if (confirm('Bạn có chắc muốn thay đổi trạng thái role này?')) {
        fetch(`/admin/roles/${roleId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}

// Delete role
function deleteRole(roleId) {
    if (confirm('Bạn có chắc muốn xóa role này? Hành động này không thể hoàn tác.')) {
        fetch(`/admin/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        });
    }
}
</script>
@endsection
