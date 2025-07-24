@extends('admin.layouts.dason')

@section('title', 'Multiple Roles Demo')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Multiple Roles Demo</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Multiple Roles Demo</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🧪 Multiple Roles System Demo</h4>
                    <p class="card-title-desc">Demonstration of users having multiple roles simultaneously</p>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Về Multiple Roles System</h5>
                        <p class="mb-2"><strong>Có, một tài khoản có thể có nhiều roles cùng lúc!</strong></p>
                        <ul class="mb-0">
                            <li><strong>Content Moderator + Marketplace Moderator:</strong> Có thể kiểm duyệt cả nội dung diễn đàn và marketplace</li>
                            <li><strong>Manufacturer + Supplier:</strong> Vừa sản xuất vừa cung cấp sản phẩm</li>
                            <li><strong>Primary Role:</strong> Một trong các roles sẽ là role chính</li>
                            <li><strong>Permission Merging:</strong> User sẽ có tất cả permissions từ các roles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Users with Multiple Roles -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">👥 Test Users với Multiple Roles</h4>
                </div>
                <div class="card-body">
                    @php
                        $testUsers = \App\Models\User::with(['roles' => function($query) {
                            $query->wherePivot('is_active', true);
                        }])->whereIn('email', [
                            'test.multi.roles@mechamap.test',
                            'business.multi@mechamap.test'
                        ])->get();
                    @endphp

                    @forelse($testUsers as $user)
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="text-primary">{{ $user->name }}</h5>
                                    <p class="text-muted mb-1">{{ $user->email }}</p>
                                    <p class="text-muted mb-0">Company: {{ $user->company_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-8">
                                    <h6>Roles ({{ $user->roles->count() }}):</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-{{ $role->color }} fs-6">
                                                <i class="{{ $role->icon }} me-1"></i>
                                                {{ $role->display_name }}
                                                @if($role->pivot->is_primary)
                                                    <i class="fas fa-crown ms-1" title="Primary Role"></i>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>

                                    <h6>Permissions Summary:</h6>
                                    @php
                                        $allPermissions = collect();
                                        foreach($user->roles as $role) {
                                            $rolePermissions = $role->permissions()->where('is_granted', true)->get();
                                            $allPermissions = $allPermissions->merge($rolePermissions);
                                        }
                                        $uniquePermissions = $allPermissions->unique('id');
                                        $permissionsByCategory = $uniquePermissions->groupBy('category');
                                    @endphp

                                    <div class="row">
                                        @foreach($permissionsByCategory as $category => $permissions)
                                        <div class="col-md-6 mb-2">
                                            <strong>{{ $category }}:</strong>
                                            <span class="badge bg-info">{{ $permissions->count() }} permissions</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-3">
                                        <strong>Total Permissions: </strong>
                                        <span class="badge bg-success fs-6">{{ $uniquePermissions->count() }}</span>
                                    </div>

                                    <!-- Admin Access Check -->
                                    <div class="mt-2">
                                        <strong>Admin Access: </strong>
                                        @if($user->canAccessAdmin())
                                            <span class="badge bg-success">✅ CÓ</span>
                                        @else
                                            <span class="badge bg-danger">❌ KHÔNG</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Chưa có test data</h5>
                        <p class="mb-2">Chạy command sau để tạo test data:</p>
                        <code>php artisan db:seed --class=TestMultipleRolesSeeder</code>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Multiple Roles Assignment Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🎯 Assign Multiple Roles</h4>
                    <p class="card-title-desc">Gán nhiều roles cho một user</p>
                </div>
                <div class="card-body">
                    <form id="multipleRolesForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Chọn User</label>
                                    <select name="user_id" class="form-select" required>
                                        <option value="">-- Chọn user --</option>
                                        @foreach(\App\Models\User::limit(20)->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Primary Role</label>
                                    <select name="primary_role_id" class="form-select">
                                        <option value="">-- Chọn primary role --</option>
                                        @foreach(\App\Models\Role::active()->get() as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chọn Roles</label>
                            <div class="row">
                                @foreach(\App\Models\Role::active()->get()->groupBy('role_group') as $group => $roles)
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary">{{ ucwords(str_replace('_', ' ', $group)) }}</h6>
                                    @foreach($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="role_ids[]"
                                               value="{{ $role->id }}" id="role_{{ $role->id }}">
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            <span class="badge bg-{{ $role->color }} me-2">
                                                <i class="{{ $role->icon }}"></i>
                                            </span>
                                            {{ $role->display_name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lý do gán roles</label>
                            <textarea name="assignment_reason" class="form-control" rows="2"
                                      placeholder="Nhập lý do gán multiple roles..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-1"></i> Gán Multiple Roles
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form listener');

    const form = document.getElementById('multipleRolesForm');
    if (!form) {
        console.error('Form not found!');
        return;
    }

    form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        e.preventDefault();

        const formData = new FormData(this);
        const selectedRoles = formData.getAll('role_ids[]');

        console.log('Selected roles:', selectedRoles);

        if (selectedRoles.length === 0) {
            alert('Vui lòng chọn ít nhất một role.');
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found!');
            alert('CSRF token not found!');
            return;
        }

        console.log('Sending AJAX request...');

        fetch('/admin/roles/assign-multiple', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Có lỗi xảy ra khi gán roles.');
        });
    });
});
</script>
@endpush
