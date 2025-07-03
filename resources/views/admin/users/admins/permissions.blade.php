@extends('admin.layouts.dason')

@section('title', 'Quản Lý Quyền Hạn')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog text-warning mr-2"></i>
            Quản Lý Quyền Hạn
        </h1>
        <div>
            <a href="{{ route('admin.users.admins.edit', $user) }}" class="btn btn-info btn-sm shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Chỉnh Sửa User
            </a>
            <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay Lại
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.admins') }}">Quản Trị Viên</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.admins.edit', $user) }}">{{ $user->name }}</a>
            </li>
            <li class="breadcrumb-item active">Quyền Hạn</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Form quyền hạn -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">Cấu Hình Quyền Hạn</h6>
                    <div class="dropdown no-arrow">
                        <span class="badge badge-{{ $user->getRoleColor() }} badge-pill">
                            {{ $user->getRoleDisplayName() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- 🎯 HYBRID SYSTEM INFO -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>🎯 CUSTOM PERMISSIONS:</strong> Trang này cho phép bạn thêm permissions tùy chỉnh bổ sung cho
                        <a href="{{ route('admin.users.roles', $user) }}" class="alert-link"><strong>Multiple Roles</strong></a> hiện có.
                        <br><small class="text-muted">Permissions cuối cùng = Roles Permissions + Custom Permissions</small>
                    </div>

                    @php
                        $permissionsBreakdown = $user->getPermissionsBreakdown();
                        $rolePermissionsCount = count($permissionsBreakdown['role_permissions']);
                        $customPermissionsCount = count($permissionsBreakdown['custom_permissions']);
                        $totalPermissionsCount = count($permissionsBreakdown['total_permissions']);
                    @endphp

                    @if($user->roles && $user->roles->count() > 0)
                    <div class="alert alert-success">
                        <i class="fas fa-users-cog me-2"></i>
                        <strong>Current Roles:</strong>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary me-1">{{ $role->display_name }}</span>
                        @endforeach
                        <br><small class="text-muted">{{ $rolePermissionsCount }} permissions từ roles</small>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Chưa có roles:</strong> User chưa được gán roles nào.
                        <a href="{{ route('admin.users.roles', $user) }}" class="alert-link">Gán roles trước</a> để có base permissions.
                    </div>
                    @endif

                    @if($customPermissionsCount > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-plus-circle me-2"></i>
                        <strong>Custom Permissions:</strong> {{ $customPermissionsCount }} additional permissions
                        <br><small class="text-muted">Total: {{ $totalPermissionsCount }} permissions ({{ $rolePermissionsCount }} từ roles + {{ $customPermissionsCount }} custom)</small>
                    </div>
                    @endif

                    <!-- Thông báo vai trò -->
                    @if($user->role === 'admin')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Lưu ý:</strong> Admin có toàn quyền trên hệ thống. Việc thay đổi quyền chỉ áp dụng cho
                        các tính năng bổ sung.
                    </div>
                    @endif

                    <form action="{{ route('admin.users.admins.permissions.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Dynamic Permissions từ Database -->
                        @if($allPermissions && $allPermissions->count() > 0)
                            @foreach($allPermissions as $category => $permissions)
                                <div class="mb-4">
                                    <h6 class="text-primary mb-3">
                                        @php
                                            $categoryIcons = [
                                                'system' => 'cogs',
                                                'users' => 'users',
                                                'content' => 'edit',
                                                'marketplace' => 'shopping-cart',
                                                'community' => 'comments',
                                                'analytics' => 'chart-bar',
                                                'basic' => 'user',
                                                'business' => 'briefcase'
                                            ];
                                            $icon = $categoryIcons[$category] ?? 'shield-alt';
                                        @endphp
                                        <i class="fas fa-{{ $icon }} me-2"></i>
                                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                                    </h6>

                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="permission_{{ $permission->id }}"
                                                           name="permissions[]"
                                                           value="{{ $permission->name }}"
                                                           {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}
                                                           {{ $user->role === 'super_admin' ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        <strong>{{ $permission->display_name }}</strong>
                                                        @if($permission->description)
                                                            <small class="text-muted d-block">{{ $permission->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        @else
                            <!-- Fallback: Permission Groups từ Config -->
                            @if($permissionGroups && count($permissionGroups) > 0)
                                @foreach($permissionGroups as $groupKey => $group)
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            @php
                                                $groupIcons = [
                                                    'system' => 'cogs',
                                                    'users' => 'users',
                                                    'content' => 'edit',
                                                    'marketplace' => 'shopping-cart',
                                                    'community' => 'comments',
                                                    'analytics' => 'chart-bar',
                                                    'basic' => 'user',
                                                    'business' => 'briefcase'
                                                ];
                                                $icon = $groupIcons[$groupKey] ?? 'shield-alt';
                                            @endphp
                                            <i class="fas fa-{{ $icon }} me-2"></i>
                                            {{ $group['name'] }}
                                        </h6>

                                        <div class="row">
                                            @foreach($group['permissions'] as $permissionName)
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="permission_{{ $permissionName }}"
                                                               name="permissions[]"
                                                               value="{{ $permissionName }}"
                                                               {{ in_array($permissionName, $userPermissions) ? 'checked' : '' }}
                                                               {{ $user->role === 'super_admin' ? 'disabled' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permissionName }}">
                                                            <strong>{{ ucfirst(str_replace('-', ' ', $permissionName)) }}</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        @endif

                        <hr>

                        <!-- Quyền quản lý người dùng -->
                        <div class="mb-4">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-users mr-2"></i>
                                Quản Lý Người Dùng
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_users"
                                            name="permissions[]" value="manage_users" {{
                                            $user->hasPermission('manage_users') ? 'checked' : '' }}
                                        {{ $user->role === 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="can_manage_users">
                                            <strong>Quản Lý Thành Viên</strong>
                                            <small class="text-muted d-block">Thêm, sửa, xóa thành viên thường</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_admins"
                                            name="permissions[]" value="manage_admins" {{
                                            $user->hasPermission('manage_admins') ? 'checked' : '' }}
                                        {{ $user->role !== 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="can_manage_admins">
                                            <strong>Quản Lý Admin</strong>
                                            <small class="text-muted d-block">Thêm, sửa admin khác (chỉ Admin)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_ban_users"
                                            name="permissions[]" value="ban_users" {{ $user->hasPermission('ban_users')
                                        ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_ban_users">
                                            <strong>Khóa/Mở Khóa User</strong>
                                            <small class="text-muted d-block">Có thể khóa hoặc mở khóa tài khoản</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_view_user_details"
                                            name="permissions[]" value="view_user_details" {{
                                            $user->hasPermission('view_user_details') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_view_user_details">
                                            <strong>Xem Chi Tiết User</strong>
                                            <small class="text-muted d-block">Xem thông tin chi tiết người dùng</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Quyền quản lý nội dung -->
                        <div class="mb-4">
                            <h6 class="text-info mb-3">
                                <i class="fas fa-file-alt mr-2"></i>
                                Quản Lý Nội Dung
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_posts"
                                            name="permissions[]" value="manage_posts" {{
                                            $user->hasPermission('manage_posts') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_manage_posts">
                                            <strong>Quản Lý Bài Viết</strong>
                                            <small class="text-muted d-block">Tạo, sửa, xóa bài viết</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_moderate_content"
                                            name="permissions[]" value="moderate_content" {{
                                            $user->hasPermission('moderate_content') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_moderate_content">
                                            <strong>Kiểm Duyệt Nội Dung</strong>
                                            <small class="text-muted d-block">Duyệt và kiểm tra nội dung</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_comments"
                                            name="permissions[]" value="manage_comments" {{
                                            $user->hasPermission('manage_comments') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_manage_comments">
                                            <strong>Quản Lý Bình Luận</strong>
                                            <small class="text-muted d-block">Xóa, chỉnh sửa bình luận</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_categories"
                                            name="permissions[]" value="manage_categories" {{
                                            $user->hasPermission('manage_categories') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_manage_categories">
                                            <strong>Quản Lý Danh Mục</strong>
                                            <small class="text-muted d-block">Tạo, sửa danh mục bài viết</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Quyền hệ thống -->
                        <div class="mb-4">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-cogs mr-2"></i>
                                Quyền Hệ Thống
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_manage_settings"
                                            name="permissions[]" value="manage_settings" {{
                                            $user->hasPermission('manage_settings') ? 'checked' : '' }}
                                        {{ $user->role !== 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="can_manage_settings">
                                            <strong>Quản Lý Cài Đặt</strong>
                                            <small class="text-muted d-block">Thay đổi cài đặt hệ thống (chỉ
                                                Admin)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_view_logs"
                                            name="permissions[]" value="view_logs" {{ $user->hasPermission('view_logs')
                                        ? 'checked' : '' }}
                                        {{ $user->role !== 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="can_view_logs">
                                            <strong>Xem Logs Hệ Thống</strong>
                                            <small class="text-muted d-block">Truy cập logs và debug (chỉ Admin)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_backup_system"
                                            name="permissions[]" value="backup_system" {{
                                            $user->hasPermission('backup_system') ? 'checked' : '' }}
                                        {{ $user->role !== 'admin' ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="can_backup_system">
                                            <strong>Sao Lưu Hệ Thống</strong>
                                            <small class="text-muted d-block">Tạo và khôi phục backup (chỉ
                                                Admin)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="can_send_notifications"
                                            name="permissions[]" value="send_notifications" {{
                                            $user->hasPermission('send_notifications') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="can_send_notifications">
                                            <strong>Gửi Thông Báo</strong>
                                            <small class="text-muted d-block">Gửi thông báo hệ thống</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reason Field -->
                        <div class="mb-4">
                            <label for="reason" class="form-label">Lý do thay đổi permissions</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                      id="reason" name="reason" rows="3"
                                      placeholder="Nhập lý do thay đổi permissions cho user này...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save mr-1"></i> Cập Nhật Quyền
                                </button>
                                <a href="{{ route('admin.users.admins.edit', $user) }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times mr-1"></i> Hủy
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-info" onclick="selectAll()">
                                    <i class="fas fa-check-double mr-1"></i> Chọn Tất Cả
                                </button>
                                <button type="button" class="btn btn-outline-secondary ml-2" onclick="deselectAll()">
                                    <i class="fas fa-times mr-1"></i> Bỏ Chọn Tất Cả
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar thông tin -->
        <div class="col-xl-4 col-lg-5">
            <!-- Thông tin người dùng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Người Dùng</h6>
                </div>
                <div class="card-body text-center">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . ltrim($user->avatar, '/')) }}" alt="{{ $user->name }}"
                        class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <span class="text-white font-weight-bold" style="font-size: 24px;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    @endif

                    <h6 class="font-weight-bold">{{ $user->name }}</h6>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                    <span class="badge badge-{{ $user->getRoleColor() }} badge-pill mb-3">
                        {{ $user->getRoleDisplayName() }}
                    </span>

                    <hr>

                    <div class="text-left small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ngày tạo:</span>
                            <span class="font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Trạng thái:</span>
                            <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tổng quyền:</span>
                            <span class="font-weight-bold text-info">{{ $user->getAllPermissions()->count() }}
                                quyền</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Hướng Dẫn</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small" role="alert">
                        <h6><i class="fas fa-lightbulb mr-2"></i>Mẹo Quản Lý Quyền:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Admin:</strong> Có toàn quyền mặc định, không thể thu hồi</li>
                            <li><strong>Moderator:</strong> Cần cấp quyền cụ thể cho từng tính năng</li>
                            <li>Quyền có thể thay đổi bất cứ lúc nào</li>
                            <li>Người dùng sẽ thấy thay đổi ngay lập tức</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning small" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Lưu Ý Bảo Mật:</h6>
                        <ul class="mb-0 small">
                            <li>Chỉ cấp quyền cần thiết</li>
                            <li>Thường xuyên kiểm tra quyền hạn</li>
                            <li>Thu hồi quyền khi không còn sử dụng</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📚 Role Descriptions Guide --}}
    @include('admin.components.role-descriptions')
</div>

@push('scripts')
<script>
    function selectAll() {
    $('input[type="checkbox"]:not([disabled])').prop('checked', true);
}

function deselectAll() {
    $('input[type="checkbox"]:not([disabled])').prop('checked', false);
}

$(document).ready(function() {
    // Hiển thị tooltip cho các quyền bị disable
    $('input[type="checkbox"][disabled]').parent().attr('title', 'Quyền này được quản lý tự động theo vai trò');

    // Highlight quyền quan trọng
    $('input[value="manage_admins"], input[value="manage_settings"], input[value="view_logs"], input[value="backup_system"]')
        .closest('.form-check')
        .addClass('border-left border-danger pl-3');
});
</script>
@endpush
@endsection
