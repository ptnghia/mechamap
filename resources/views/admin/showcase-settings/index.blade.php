@extends('admin.layouts.app')

@section('title', 'Quản lý Showcase Settings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Quản lý Showcase Settings</h1>
                    <p class="text-muted">Quản lý các tùy chọn động cho showcase</p>
                </div>
                <div>
                    <a href="{{ route('admin.showcase-settings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Thêm Setting
                    </a>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearCache()">
                        <i class="fas fa-sync"></i> Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Tổng Settings</h5>
                            <h3 class="mb-0">{{ $statistics['total_settings'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Đang hoạt động</h5>
                            <h3 class="mb-0">{{ $statistics['active_settings'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Có thể tìm kiếm</h5>
                            <h3 class="mb-0">{{ $statistics['searchable_settings'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Nhóm</h5>
                            <h3 class="mb-0">{{ count($statistics['groups']) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-layer-group fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Danh sách Settings</h5>
        </div>
        <div class="card-body">
            @if(count($settings) > 0)
                @foreach($settings as $group => $groupSettings)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted mb-3">
                            <i class="fas fa-folder"></i> 
                            {{ ucfirst($group) }}
                        </h6>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Key</th>
                                        <th>Tên</th>
                                        <th>Loại Input</th>
                                        <th>Số Options</th>
                                        <th>Trạng thái</th>
                                        <th>Thứ tự</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupSettings as $setting)
                                        <tr>
                                            <td>
                                                <code>{{ $setting['key'] }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ $setting['name'] }}</strong>
                                                @if($setting['description'])
                                                    <br><small class="text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $setting['input_type'] }}</span>
                                                @if($setting['is_multiple'])
                                                    <span class="badge badge-info">Multiple</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light">{{ count($setting['options']) }}</span>
                                            </td>
                                            <td>
                                                @if($setting['is_active'])
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                @endif
                                                
                                                @if($setting['is_searchable'])
                                                    <span class="badge badge-info">Tìm kiếm</span>
                                                @endif
                                                
                                                @if($setting['is_required'])
                                                    <span class="badge badge-warning">Bắt buộc</span>
                                                @endif
                                            </td>
                                            <td>{{ $setting['sort_order'] }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.showcase-settings.show', $setting['id']) }}" 
                                                       class="btn btn-outline-info" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.showcase-settings.edit', $setting['id']) }}" 
                                                       class="btn btn-outline-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-{{ $setting['is_active'] ? 'warning' : 'success' }}"
                                                            onclick="toggleActive({{ $setting['id'] }})"
                                                            title="{{ $setting['is_active'] ? 'Tạm dừng' : 'Kích hoạt' }}">
                                                        <i class="fas fa-{{ $setting['is_active'] ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger"
                                                            onclick="deleteSetting({{ $setting['id'] }})"
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
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-cogs fa-3x text-muted mb-3"></i>
                    <h5>Chưa có settings nào</h5>
                    <p class="text-muted">Hãy tạo setting đầu tiên để bắt đầu</p>
                    <a href="{{ route('admin.showcase-settings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tạo Setting
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleActive(id) {
    fetch(`/admin/showcase-settings/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái');
    });
}

function deleteSetting(id) {
    if (confirm('Bạn có chắc chắn muốn xóa setting này?')) {
        fetch(`/admin/showcase-settings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa setting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa setting');
        });
    }
}

function clearCache() {
    fetch('/admin/showcase-settings/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cache đã được xóa thành công!');
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa cache');
    });
}
</script>
@endpush
@endsection
