@extends('admin.layouts.dason')

@section('title', 'Quản Lý Multiple Roles - ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Multiple Roles</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['title'] }}</li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a></li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md me-3">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                 alt="{{ $user->name }}" class="img-fluid rounded-circle">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="font-size-16 mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user-tag me-1"></i>
                                Primary Role: <span class="badge bg-primary">{{ $primaryRole->display_name ?? 'Chưa có' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Roles -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users-cog me-2"></i>
                        Roles Hiện Tại ({{ $userRoles->count() }})
                    </h4>
                </div>
                <div class="card-body">
                    @if($userRoles->count() > 0)
                        @foreach($userRoles as $role)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-{{ $role->icon ?? 'user' }} me-2 text-{{ $role->color ?? 'primary' }}"></i>
                                <span class="me-2">{{ $role->display_name }}</span>
                                @if($role->pivot->is_primary)
                                    <span class="badge bg-success">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Chưa có roles nào được gán.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Multiple Roles Assignment Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Gán Multiple Roles
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.roles.update', $user) }}" method="POST">
                        @csrf

                        <!-- Primary Role Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="primary_role_id" class="form-label">Primary Role <span class="text-danger">*</span></label>
                                <select name="primary_role_id" id="primary_role_id" class="form-select @error('primary_role_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn Primary Role --</option>
                                    @foreach($roles->flatten() as $role)
                                        <option value="{{ $role->id }}"
                                                {{ old('primary_role_id', $primaryRole?->id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('primary_role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Multiple Roles Selection -->
                        <div class="mb-3">
                            <label class="form-label">Chọn Multiple Roles <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($roles as $groupName => $groupRoles)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-primary">{{ ucfirst(str_replace('_', ' ', $groupName)) }}</h6>
                                        @foreach($groupRoles as $role)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                       name="role_ids[]" value="{{ $role->id }}"
                                                       id="role_{{ $role->id }}"
                                                       {{ in_array($role->id, old('role_ids', $userRoles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    <i class="fas fa-{{ $role->icon ?? 'user' }} me-1 text-{{ $role->color ?? 'primary' }}"></i>
                                                    {{ $role->display_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            @error('role_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Lý do gán roles <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="3"
                                      class="form-control @error('reason') is-invalid @enderror"
                                      placeholder="Nhập lý do gán multiple roles cho user này..." required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i>
                                Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Cập nhật Multiple Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 📚 Role Descriptions Guide --}}
    @include('admin.components.role-descriptions')
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-check primary role when selected
    const primaryRoleSelect = document.getElementById('primary_role_id');
    const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]');

    primaryRoleSelect.addEventListener('change', function() {
        const selectedRoleId = this.value;

        // Uncheck all first
        roleCheckboxes.forEach(checkbox => {
            if (checkbox.value === selectedRoleId) {
                checkbox.checked = true;
            }
        });
    });

    // Ensure primary role is always checked
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const primaryRoleId = primaryRoleSelect.value;

            if (this.value === primaryRoleId && !this.checked) {
                this.checked = true;
                alert('Primary role không thể bỏ chọn. Vui lòng chọn primary role khác trước.');
            }
        });
    });
});
</script>
@endpush
@endsection
