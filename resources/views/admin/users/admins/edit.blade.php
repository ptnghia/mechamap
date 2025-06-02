@extends('admin.layouts.app')

@section('title', 'Chỉnh Sửa Quản Trị Viên')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield text-primary mr-2"></i>
            Chỉnh Sửa Quản Trị Viên
        </h1>
        <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay Lại
        </a>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.admins') }}">Quản Trị Viên</a></li>
            <li class="breadcrumb-item active">Chỉnh Sửa</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Form chính -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông Tin Quản Trị Viên</h6>
                    <div class="dropdown no-arrow">
                        <span class="badge badge-{{ $user->getRoleColor() }} badge-pill">
                            {{ $user->getRoleDisplayName() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.admins.update', $user) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Tên đầy đủ -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tên Đầy Đủ <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Vai trò -->
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Vai Trò <span class="text-danger">*</span></label>
                                <select class="form-control @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">-- Chọn vai trò --</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        Admin (Quản trị viên cao cấp)
                                    </option>
                                    <option value="moderator" {{ old('role', $user->role) === 'moderator' ? 'selected' :
                                        '' }}>
                                        Moderator (Điều hành viên)
                                    </option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Trạng thái -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng Thái</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status"
                                    name="status">
                                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' :
                                        '' }}>
                                        🟢 Hoạt động
                                    </option>
                                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected'
                                        : '' }}>
                                        🔴 Tạm khóa
                                    </option>
                                    <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' :
                                        '' }}>
                                        🟡 Chờ duyệt
                                    </option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số Điện Thoại</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0123456789">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Địa chỉ -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Địa Chỉ</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address', $user->address) }}"
                                    placeholder="Nhập địa chỉ">
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ảnh đại diện -->
                            <div class="col-12 mb-3">
                                <label for="avatar" class="form-label">Ảnh Đại Diện</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                                    id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($user->avatar)
                                <div class="mt-2">
                                    <small class="text-muted">Ảnh hiện tại:</small><br>
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar hiện tại"
                                        class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                                @endif
                            </div>

                            <!-- Bio -->
                            <div class="col-12 mb-3">
                                <label for="bio" class="form-label">Mô Tả</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio"
                                    rows="3"
                                    placeholder="Mô tả ngắn về quản trị viên...">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Đổi mật khẩu -->
                        <hr class="my-4">
                        <h6 class="text-gray-600 mb-3">
                            <i class="fas fa-key mr-2"></i>
                            Đổi Mật Khẩu (Để trống nếu không muốn đổi)
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mật Khẩu Mới</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Nhập mật khẩu mới">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Nhập lại mật khẩu mới">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Cập Nhật
                                </button>
                                <a href="{{ route('admin.users.admins') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times mr-1"></i> Hủy
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('admin.users.admins.permissions', $user) }}" class="btn btn-info">
                                    <i class="fas fa-cog mr-1"></i> Quản Lý Quyền
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar thông tin -->
        <div class="col-xl-4 col-lg-5">
            <!-- Thông tin vai trò -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Thông Tin Vai Trò</h6>
                </div>
                <div class="card-body">
                    <div id="role-info">
                        @if($user->role === 'admin')
                        <div class="alert alert-primary" role="alert">
                            <h6><i class="fas fa-crown mr-2"></i>Admin - Quản Trị Viên Cao Cấp</h6>
                            <small>
                                • Toàn quyền quản lý hệ thống<br>
                                • Quản lý tất cả người dùng<br>
                                • Cấu hình hệ thống<br>
                                • Xem báo cáo và thống kê
                            </small>
                        </div>
                        @elseif($user->role === 'moderator')
                        <div class="alert alert-info" role="alert">
                            <h6><i class="fas fa-shield-alt mr-2"></i>Moderator - Điều Hành Viên</h6>
                            <small>
                                • Quản lý nội dung và bài viết<br>
                                • Kiểm duyệt thành viên<br>
                                • Xử lý báo cáo vi phạm<br>
                                • Hỗ trợ kỹ thuật cơ bản
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Thống kê nhanh -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Thống Kê</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <span class="text-white font-weight-bold" style="font-size: 24px;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <h6 class="font-weight-bold">{{ $user->name }}</h6>
                        <p class="text-muted small mb-2">{{ $user->email }}</p>
                        <span class="badge badge-{{ $user->getRoleColor() }} badge-pill mb-3">
                            {{ $user->getRoleDisplayName() }}
                        </span>
                    </div>

                    <hr>

                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ngày tạo:</span>
                            <span class="font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Lần cuối hoạt động:</span>
                            <span class="font-weight-bold">
                                {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa có' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Trạng thái:</span>
                            <span
                                class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'danger' : 'warning') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </div>
                        @if($user->email_verified_at)
                        <div class="d-flex justify-content-between">
                            <span>Email xác thực:</span>
                            <span class="text-success"><i class="fas fa-check-circle"></i> Đã xác thục</span>
                        </div>
                        @else
                        <div class="d-flex justify-content-between">
                            <span>Email xác thực:</span>
                            <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Chưa xác thực</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
    // Cập nhật thông tin vai trò khi thay đổi
    $('#role').change(function() {
        const role = $(this).val();
        let roleInfo = '';
        
        if (role === 'admin') {
            roleInfo = `
                <div class="alert alert-primary" role="alert">
                    <h6><i class="fas fa-crown mr-2"></i>Admin - Quản Trị Viên Cao Cấp</h6>
                    <small>
                        • Toàn quyền quản lý hệ thống<br>
                        • Quản lý tất cả người dùng<br>
                        • Cấu hình hệ thống<br>
                        • Xem báo cáo và thống kê
                    </small>
                </div>
            `;
        } else if (role === 'moderator') {
            roleInfo = `
                <div class="alert alert-info" role="alert">
                    <h6><i class="fas fa-shield-alt mr-2"></i>Moderator - Điều Hành Viên</h6>
                    <small>
                        • Quản lý nội dung và bài viết<br>
                        • Kiểm duyệt thành viên<br>
                        • Xử lý báo cáo vi phạm<br>
                        • Hỗ trợ kỹ thuật cơ bản
                    </small>
                </div>
            `;
        }
        
        $('#role-info').html(roleInfo);
    });

    // Xem trước ảnh khi chọn file
    $('#avatar').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Tạo hoặc cập nhật preview
                if ($('#avatar-preview').length === 0) {
                    $('#avatar').after(`
                        <div class="mt-2" id="avatar-preview">
                            <small class="text-muted">Ảnh xem trước:</small><br>
                            <img src="${e.target.result}" 
                                 alt="Preview" 
                                 class="img-thumbnail" 
                                 style="max-width: 100px; max-height: 100px;">
                        </div>
                    `);
                } else {
                    $('#avatar-preview img').attr('src', e.target.result);
                }
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
@endsection