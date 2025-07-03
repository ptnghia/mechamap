@extends('admin.layouts.dason')

@section('title', 'Quản lý Tiêu chuẩn Kỹ thuật')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý Tiêu chuẩn Kỹ thuật</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Kỹ thuật</a></li>
                        <li class="breadcrumb-item active">Tiêu chuẩn kỹ thuật</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng tiêu chuẩn</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="mdi mdi-certificate font-size-24"></i>
                                </span>
                            </div>
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
                            <p class="text-truncate font-size-14 mb-2">ISO Standards</p>
                            <h4 class="mb-0">{{ $stats['iso'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="mdi mdi-earth font-size-24"></i>
                                </span>
                            </div>
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
                            <p class="text-truncate font-size-14 mb-2">ASTM Standards</p>
                            <h4 class="mb-0">{{ $stats['astm'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="fas fa-flag font-size-24"></i>
                                </span>
                            </div>
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
                            <p class="text-truncate font-size-14 mb-2">TCVN Standards</p>
                            <h4 class="mb-0">{{ $stats['tcvn'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="fas fa-flag-variant font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Standards Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách tiêu chuẩn kỹ thuật</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.technical.standards.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Thêm tiêu chuẩn
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="organizationFilter">
                                <option value="">Tất cả tổ chức</option>
                                <option value="iso">ISO</option>
                                <option value="astm">ASTM</option>
                                <option value="tcvn">TCVN</option>
                                <option value="din">DIN</option>
                                <option value="jis">JIS</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                <option value="materials">Vật liệu</option>
                                <option value="dimensions">Kích thước</option>
                                <option value="testing">Thử nghiệm</option>
                                <option value="quality">Chất lượng</option>
                                <option value="safety">An toàn</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm tiêu chuẩn..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="exportStandards()">
                                <i class="mdi mdi-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="standardsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Tiêu chuẩn</th>
                                    <th>Tổ chức</th>
                                    <th>Danh mục</th>
                                    <th>Trạng thái</th>
                                    <th>Năm ban hành</th>
                                    <th>Lượt xem</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($standards ?? [] as $standard)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $standard->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded">
                                                    <i class="mdi mdi-certificate font-size-18"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $standard->code ?? 'ISO 9001:2015' }}</h6>
                                                <p class="text-muted mb-0">{{ $standard->title ?? 'Quality Management Systems' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ strtoupper($standard->organization ?? 'ISO') }}</span>
                                    </td>
                                    <td>{{ $standard->category ?? 'Quality' }}</td>
                                    <td>
                                        @if(($standard->status ?? 'active') === 'active')
                                            <span class="badge bg-success">Hiệu lực</span>
                                        @elseif(($standard->status ?? 'draft') === 'draft')
                                            <span class="badge bg-warning">Dự thảo</span>
                                        @else
                                            <span class="badge bg-danger">Hết hiệu lực</span>
                                        @endif
                                    </td>
                                    <td>{{ $standard->published_year ?? '2015' }}</td>
                                    <td>{{ $standard->view_count ?? 0 }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="fas fa-eye font-size-16 text-success me-1"></i> Xem chi tiết
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="fas fa-download font-size-16 text-info me-1"></i> Tải PDF
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="fas fa-edit font-size-16 text-success me-1"></i> Chỉnh sửa
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteStandard({{ $standard->id ?? 1 }})">
                                                    <i class="mdi mdi-trash-can font-size-16 text-danger me-1"></i> Xóa
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="mdi mdi-certificate font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có tiêu chuẩn kỹ thuật nào</p>
                                            <a href="{{ route('admin.technical.standards.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="fas fa-plus me-1"></i> Thêm tiêu chuẩn đầu tiên
                                            </a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Implement search logic
    });

    // Filter functionality
    document.getElementById('organizationFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('categoryFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    // Check all functionality
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});

function deleteStandard(id) {
    if (confirm('Bạn có chắc chắn muốn xóa tiêu chuẩn này?')) {
        // Implement delete logic
        console.log('Delete standard:', id);
    }
}

function exportStandards() {
    // Implement export logic
    console.log('Export standards');
}
</script>
@endsection
