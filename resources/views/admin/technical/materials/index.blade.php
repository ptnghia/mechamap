@extends('admin.layouts.dason')

@section('title', 'Quản Lý Vật Liệu')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý Vật Liệu</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.technical.materials.index') }}">Kỹ Thuật</a></li>
                    <li class="breadcrumb-item active">Vật Liệu</li>
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
                                <p class="text-muted fw-medium">Tổng Vật Liệu</p>
                                <h4 class="mb-0">{{ $stats['total_materials'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i data-feather="layers"></i>
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
                                <p class="text-muted fw-medium">Kim Loại</p>
                                <h4 class="mb-0">{{ $stats['metal_materials'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i data-feather="hexagon"></i>
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
                                <p class="text-muted fw-medium">Polyme</p>
                                <h4 class="mb-0">{{ $stats['polymer_materials'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i data-feather="circle"></i>
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
                                <p class="text-muted fw-medium">Nguy Hiểm</p>
                                <h4 class="mb-0">{{ $stats['hazardous_materials'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="fas fa-exclamation-triangle"></i>
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
                        <h4 class="card-title">Danh Sách Vật Liệu</h4>
                        <div class="card-title-desc">Quản lý cơ sở dữ liệu vật liệu kỹ thuật</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.technical.materials.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus" class="me-1"></i> Thêm Vật Liệu
                            </a>
                            <button type="button" class="btn btn-info btn-sm" onclick="compareMaterials()">
                                <i data-feather="bar-chart-2" class="me-1"></i> So Sánh
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportMaterials()">
                                <i class="fas fa-download" class="me-1"></i> Xuất Excel
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
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm vật liệu...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category">
                                <option value="">Tất cả danh mục</option>
                                <option value="Metal" {{ request('category') === 'Metal' ? 'selected' : '' }}>Kim loại</option>
                                <option value="Polymer" {{ request('category') === 'Polymer' ? 'selected' : '' }}>Polyme</option>
                                <option value="Ceramic" {{ request('category') === 'Ceramic' ? 'selected' : '' }}>Gốm sứ</option>
                                <option value="Composite" {{ request('category') === 'Composite' ? 'selected' : '' }}>Tổng hợp</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="material_type">
                                <option value="">Loại vật liệu</option>
                                <option value="Structural" {{ request('material_type') === 'Structural' ? 'selected' : '' }}>Kết cấu</option>
                                <option value="Tool" {{ request('material_type') === 'Tool' ? 'selected' : '' }}>Dụng cụ</option>
                                <option value="Special" {{ request('material_type') === 'Special' ? 'selected' : '' }}>Đặc biệt</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">Trạng thái</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.technical.materials.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Materials Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>Vật Liệu</th>
                                <th>Danh Mục</th>
                                <th>Tính Chất Cơ Học</th>
                                <th>Tính Chất Vật Lý</th>
                                <th>Giá/kg</th>
                                <th>Trạng Thái</th>
                                <th>Lượt Xem</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials ?? [] as $material)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input material-checkbox" type="checkbox" value="{{ $material->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $material->name }}</h6>
                                            <p class="text-muted mb-0 small">{{ $material->code }}</p>
                                            @if($material->grade)
                                                <p class="text-muted mb-0 small">Grade: {{ $material->grade }}</p>
                                            @endif
                                            @if($material->is_featured)
                                                <span class="badge bg-warning">Nổi bật</span>
                                            @endif
                                            @if($material->hazardous)
                                                <span class="badge bg-danger">Nguy hiểm</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $material->category_label }}</span>
                                        @if($material->subcategory)
                                            <p class="text-muted mb-0 small mt-1">{{ $material->subcategory }}</p>
                                        @endif
                                        <p class="text-muted mb-0 small">{{ $material->material_type }}</p>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($material->yield_strength)
                                                <div>Giới hạn chảy: {{ number_format($material->yield_strength) }} MPa</div>
                                            @endif
                                            @if($material->tensile_strength)
                                                <div>Độ bền kéo: {{ number_format($material->tensile_strength) }} MPa</div>
                                            @endif
                                            @if($material->youngs_modulus)
                                                <div>Modun đàn hồi: {{ number_format($material->youngs_modulus) }} GPa</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($material->density)
                                                <div>Khối lượng riêng: {{ $material->density }} kg/m³</div>
                                            @endif
                                            @if($material->melting_point)
                                                <div>Nhiệt độ nóng chảy: {{ $material->melting_point }}°C</div>
                                            @endif
                                            @if($material->thermal_conductivity)
                                                <div>Dẫn nhiệt: {{ $material->thermal_conductivity }} W/m·K</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($material->cost_per_kg)
                                            <span class="fw-bold">{{ number_format($material->cost_per_kg) }} VND</span>
                                            @if($material->availability)
                                                <p class="text-muted mb-0 small mt-1">{{ $material->availability_label }}</p>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa có giá</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'deprecated' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$material->status] ?? 'secondary' }}">
                                            {{ $material->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ number_format($material->view_count) }}</span> lượt xem
                                            <p class="text-muted mb-0 small">{{ number_format($material->usage_count) }} lần sử dụng</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.technical.materials.show', $material) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.technical.materials.edit', $material) }}" class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($material->datasheet_path)
                                                <a href="{{ route('admin.technical.materials.datasheet', $material) }}" class="btn btn-outline-success" title="Tải datasheet">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-outline-warning" onclick="toggleFeatured({{ $material->id }})" title="Nổi bật">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteMaterial({{ $material->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i data-feather="layers" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                            <h5 class="text-muted">Chưa có vật liệu nào</h5>
                                            <p class="text-muted mb-0">Thêm vật liệu đầu tiên vào cơ sở dữ liệu</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($materials) && $materials->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $materials->firstItem() }} đến {{ $materials->lastItem() }}
                                    của {{ $materials->total() }} vật liệu
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $materials->links() }}
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
    const checkboxes = document.querySelectorAll('.material-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Compare materials
function compareMaterials() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length < 2 || selectedIds.length > 5) {
        alert('Vui lòng chọn từ 2 đến 5 vật liệu để so sánh');
        return;
    }

    // TODO: Implement material comparison
    alert('Chức năng so sánh vật liệu sẽ được triển khai');
}

// Export materials
function exportMaterials() {
    alert('Chức năng xuất Excel sẽ được triển khai');
}

// Toggle featured
function toggleFeatured(materialId) {
    if (confirm('Bạn có muốn thay đổi trạng thái nổi bật của vật liệu này?')) {
        // TODO: Implement toggle featured
        alert('Chức năng đánh dấu nổi bật sẽ được triển khai');
    }
}

// Delete material
function deleteMaterial(materialId) {
    if (confirm('Bạn có chắc muốn xóa vật liệu này? Hành động này không thể hoàn tác!')) {
        // TODO: Implement delete
        alert('Chức năng xóa sẽ được triển khai');
    }
}

// Get selected material IDs
function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.material-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch (error) {
            console.warn('Feather Icons error in materials page:', error);
        }
    }
});
</script>
@endsection
