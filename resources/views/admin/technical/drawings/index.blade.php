@extends('admin.layouts.dason')

@section('title', 'Quản Lý Bản Vẽ Kỹ Thuật')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý Bản Vẽ Kỹ Thuật</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.technical.drawings.index') }}">Kỹ Thuật</a></li>
                    <li class="breadcrumb-item active">Bản Vẽ Kỹ Thuật</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Tổng Bản Vẽ</p>
                                <h4 class="mb-0">{{ $stats['total_drawings'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Chờ Duyệt</p>
                                <h4 class="mb-0">{{ $stats['pending_approval'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Đã Duyệt</p>
                                <h4 class="mb-0">{{ $stats['approved_drawings'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Lượt Tải</p>
                                <h4 class="mb-0">{{ number_format($stats['total_downloads'] ?? 0) }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="fas fa-download"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Bản Vẽ Kỹ Thuật</h4>
                        <div class="card-title-desc">Quản lý tất cả bản vẽ kỹ thuật trong hệ thống</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.technical.drawings.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus" class="me-1"></i> Thêm Bản Vẽ
                            </a>
                            <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                                <i class="fas fa-check" class="me-1"></i> Duyệt Đã Chọn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm bản vẽ...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="drawing_type">
                                <option value="">Loại bản vẽ</option>
                                <option value="Assembly" {{ request('drawing_type') === 'Assembly' ? 'selected' : '' }}>Lắp ráp</option>
                                <option value="Detail" {{ request('drawing_type') === 'Detail' ? 'selected' : '' }}>Chi tiết</option>
                                <option value="Schematic" {{ request('drawing_type') === 'Schematic' ? 'selected' : '' }}>Sơ đồ</option>
                                <option value="Layout" {{ request('drawing_type') === 'Layout' ? 'selected' : '' }}>Bố trí</option>
                                <option value="Wiring" {{ request('drawing_type') === 'Wiring' ? 'selected' : '' }}>Dây điện</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="visibility">
                                <option value="">Quyền truy cập</option>
                                <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Riêng tư</option>
                                <option value="company_only" {{ request('visibility') === 'company_only' ? 'selected' : '' }}>Chỉ công ty</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.technical.drawings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Drawings Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>Bản Vẽ</th>
                                <th>Loại</th>
                                <th>Dự Án</th>
                                <th>Người Tạo</th>
                                <th>Trạng Thái</th>
                                <th>Lượt Xem</th>
                                <th>Ngày Tạo</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drawings ?? [] as $drawing)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input drawing-checkbox" type="checkbox" value="{{ $drawing->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $drawing->title }}</h6>
                                            <p class="text-muted mb-0 small">{{ $drawing->drawing_number }}</p>
                                            @if($drawing->is_featured)
                                                <span class="badge bg-warning">Nổi bật</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $drawing->drawing_type }}</span>
                                        @if($drawing->scale)
                                            <p class="text-muted mb-0 small mt-1">Tỷ lệ: {{ $drawing->scale }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if($drawing->project_name)
                                                <h6 class="mb-1">{{ $drawing->project_name }}</h6>
                                            @endif
                                            @if($drawing->part_number)
                                                <p class="text-muted mb-0 small">Mã: {{ $drawing->part_number }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $drawing->creator->name ?? 'N/A' }}</h6>
                                            @if($drawing->company)
                                                <p class="text-muted mb-0 small">{{ $drawing->company->business_name }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'archived' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$drawing->status] ?? 'secondary' }}">
                                            {{ $drawing->status_label }}
                                        </span>
                                        <p class="text-muted mb-0 small mt-1">{{ $drawing->visibility_label }}</p>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ number_format($drawing->view_count) }}</span> lượt xem
                                            <p class="text-muted mb-0 small">{{ number_format($drawing->download_count) }} lượt tải</p>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $drawing->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.technical.drawings.show', $drawing) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.technical.drawings.edit', $drawing) }}" class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($drawing->canBeDownloaded())
                                                <a href="{{ route('admin.technical.drawings.download', $drawing) }}" class="btn btn-outline-success" title="Tải xuống">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-outline-warning" onclick="toggleFeatured({{ $drawing->id }})" title="Nổi bật">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteDrawing({{ $drawing->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-file-alt" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                            <h5 class="text-muted">Chưa có bản vẽ kỹ thuật nào</h5>
                                            <p class="text-muted mb-0">Thêm bản vẽ kỹ thuật đầu tiên vào hệ thống</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($drawings) && $drawings->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $drawings->firstItem() }} đến {{ $drawings->lastItem() }}
                                    của {{ $drawings->total() }} bản vẽ
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $drawings->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Check all functionality
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.drawing-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk approve
function bulkApprove() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất một bản vẽ');
        return;
    }

    if (confirm(`Bạn có chắc muốn duyệt ${selectedIds.length} bản vẽ đã chọn?`)) {
        // TODO: Implement bulk approve
        alert('Chức năng duyệt hàng loạt sẽ được triển khai');
    }
}

// Toggle featured
function toggleFeatured(drawingId) {
    if (confirm('Bạn có muốn thay đổi trạng thái nổi bật của bản vẽ này?')) {
        // TODO: Implement toggle featured
        alert('Chức năng đánh dấu nổi bật sẽ được triển khai');
    }
}

// Delete drawing
function deleteDrawing(drawingId) {
    if (confirm('Bạn có chắc muốn xóa bản vẽ này? Hành động này không thể hoàn tác!')) {
        // TODO: Implement delete
        alert('Chức năng xóa sẽ được triển khai');
    }
}

// Get selected drawing IDs
function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.drawing-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Technical drawings page loaded');
});
</script>
@endsection
