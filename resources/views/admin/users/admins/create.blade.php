@extends('admin.layouts.dason')

@section('title', 'Thêm quản trị viên')

@push('styles')
<!-- Page specific CSS -->
@endpush

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
            <h1 class="h3 mb-0">Thêm quản trị viên mới</h1>
            <p class="text-muted">Tạo tài khoản Admin hoặc Moderator mới</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.users.admins') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin quản trị viên</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.admins.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Thông tin cơ bản -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ và tên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Tên đăng nhập <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Vai trò chính <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">Chọn vai trò chính</option>
                                    <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="moderator" {{ old('role')==='moderator' ? 'selected' : '' }}>
                                        Moderator</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Multiple Roles Option -->
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="enable_multiple_roles"
                                           name="enable_multiple_roles" value="1" {{ old('enable_multiple_roles') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_multiple_roles">
                                        <i class="fas fa-users-cog me-1"></i>
                                        Gán Multiple Roles (Nâng cao)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mật khẩu <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                                name="avatar" accept="image/*">
                            @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Cho phép: JPG, PNG, GIF, WebP, AVIF. Tối đa 2MB.</div>
                        </div>

                        <!-- Thông tin thêm -->
                        <div class="mb-3">
                            <label for="about_me" class="form-label">Giới thiệu</label>
                            <textarea class="form-control @error('about_me') is-invalid @enderror" id="about_me"
                                name="about_me" rows="3"
                                placeholder="Giới thiệu ngắn về bản thân...">{{ old('about_me') }}</textarea>
                            @error('about_me')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                    id="website" name="website" value="{{ old('website') }}"
                                    placeholder="https://congty.vn">
                                @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                    id="location" name="location" value="{{ old('location') }}"
                                    placeholder="Thành phố, Quốc gia">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="signature" class="form-label">Chữ ký</label>
                            <textarea class="form-control @error('signature') is-invalid @enderror" id="signature"
                                name="signature" rows="2"
                                placeholder="Chữ ký hiển thị trong bài viết...">{{ old('signature') }}</textarea>
                            @error('signature')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Multiple Roles Section (Hidden by default) -->
                        <div id="multiple_roles_section" class="mb-4" style="display: none;">
                            <hr>
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-users-cog me-2"></i>
                                Gán Multiple Roles
                            </h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Vai trò chính sẽ được tự động chọn. Bạn có thể chọn thêm các vai trò phụ.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-secondary">System Management</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="super_admin" id="role_super_admin">
                                        <label class="form-check-label" for="role_super_admin">
                                            <i class="fas fa-crown me-1 text-warning"></i>
                                            Super Admin
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="system_admin" id="role_system_admin">
                                        <label class="form-check-label" for="role_system_admin">
                                            <i class="fas fa-cog me-1 text-primary"></i>
                                            System Admin
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="content_admin" id="role_content_admin">
                                        <label class="form-check-label" for="role_content_admin">
                                            <i class="fas fa-edit me-1 text-info"></i>
                                            Content Admin
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-secondary">Community Management</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="content_moderator" id="role_content_moderator">
                                        <label class="form-check-label" for="role_content_moderator">
                                            <i class="fas fa-shield-alt me-1 text-success"></i>
                                            Content Moderator
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="marketplace_moderator" id="role_marketplace_moderator">
                                        <label class="form-check-label" for="role_marketplace_moderator">
                                            <i class="fas fa-shopping-cart me-1 text-warning"></i>
                                            Marketplace Moderator
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="additional_roles[]" value="community_moderator" id="role_community_moderator">
                                        <label class="form-check-label" for="role_community_moderator">
                                            <i class="fas fa-users me-1 text-info"></i>
                                            Community Moderator
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="assignment_reason" class="form-label">Lý do gán multiple roles</label>
                                <textarea class="form-control" id="assignment_reason" name="assignment_reason" rows="2"
                                          placeholder="Nhập lý do gán multiple roles cho user này...">{{ old('assignment_reason') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary">Hủy</a>
                            <button type="submit" class="btn btn-primary">Tạo quản trị viên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar với thông tin vai trò -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Thông tin vai trò</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-danger">Admin</h6>
                        <ul class="small text-muted">
                            <li>Toàn quyền truy cập hệ thống</li>
                            <li>Quản lý tất cả người dùng</li>
                            <li>Cấu hình hệ thống</li>
                            <li>Quản lý danh mục và diễn đàn</li>
                            <li>Xem báo cáo thống kê</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-warning">Moderator</h6>
                        <ul class="small text-muted">
                            <li>Kiểm duyệt bài viết</li>
                            <li>Quản lý bình luận</li>
                            <li>Cấm/bỏ cấm thành viên</li>
                            <li>Xóa nội dung vi phạm</li>
                            <li>Xem báo cáo nội dung</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Lưu ý</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Chú ý:</strong> Tài khoản quản trị viên có quyền hạn cao. Hãy đảm bảo chỉ tạo cho những
                        người được tin tưởng.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview avatar
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // You can add avatar preview here if needed
        }
        reader.readAsDataURL(file);
    }
});

// Toggle Multiple Roles Section
document.getElementById('enable_multiple_roles').addEventListener('change', function() {
    const multipleRolesSection = document.getElementById('multiple_roles_section');
    const primaryRoleSelect = document.getElementById('role');

    if (this.checked) {
        multipleRolesSection.style.display = 'block';
        // Auto-check primary role in additional roles
        const primaryRole = primaryRoleSelect.value;
        if (primaryRole) {
            const primaryRoleCheckbox = document.querySelector(`input[name="additional_roles[]"][value="${primaryRole}"]`);
            if (primaryRoleCheckbox) {
                primaryRoleCheckbox.checked = true;
                primaryRoleCheckbox.disabled = true; // Prevent unchecking primary role
            }
        }
    } else {
        multipleRolesSection.style.display = 'none';
        // Uncheck all additional roles
        document.querySelectorAll('input[name="additional_roles[]"]').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });
    }
});

// Update primary role checkbox when primary role changes
document.getElementById('role').addEventListener('change', function() {
    const multipleRolesCheckbox = document.getElementById('enable_multiple_roles');

    if (multipleRolesCheckbox.checked) {
        // Reset all checkboxes
        document.querySelectorAll('input[name="additional_roles[]"]').forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });

        // Auto-check new primary role
        const primaryRole = this.value;
        if (primaryRole) {
            const primaryRoleCheckbox = document.querySelector(`input[name="additional_roles[]"][value="${primaryRole}"]`);
            if (primaryRoleCheckbox) {
                primaryRoleCheckbox.checked = true;
                primaryRoleCheckbox.disabled = true;
            }
        }
    }
});
</script>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
