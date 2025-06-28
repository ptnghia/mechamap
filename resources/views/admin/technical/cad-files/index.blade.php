@extends('admin.layouts.dason')

@section('title', 'Quản lý File CAD')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý File CAD</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Kỹ thuật</a></li>
                        <li class="breadcrumb-item active">File CAD</li>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng file CAD</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="mdi mdi-cube-outline font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">SolidWorks</p>
                            <h4 class="mb-0">{{ $stats['solidworks'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="mdi mdi-file-cad font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">AutoCAD</p>
                            <h4 class="mb-0">{{ $stats['autocad'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="mdi mdi-file-outline font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Fusion 360</p>
                            <h4 class="mb-0">{{ $stats['fusion360'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="mdi mdi-vector-arrange-above font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CAD Files Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách file CAD</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.technical.cad-files.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Upload file CAD
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="softwareFilter">
                                <option value="">Tất cả phần mềm</option>
                                <option value="solidworks">SolidWorks</option>
                                <option value="autocad">AutoCAD</option>
                                <option value="fusion360">Fusion 360</option>
                                <option value="inventor">Inventor</option>
                                <option value="catia">CATIA</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                <option value="mechanical">Cơ khí</option>
                                <option value="automotive">Ô tô</option>
                                <option value="aerospace">Hàng không</option>
                                <option value="industrial">Công nghiệp</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm file CAD..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="exportCADFiles()">
                                <i class="mdi mdi-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="cadFilesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>File CAD</th>
                                    <th>Phần mềm</th>
                                    <th>Danh mục</th>
                                    <th>Kích thước</th>
                                    <th>Lượt tải</th>
                                    <th>Ngày upload</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cadFiles ?? [] as $file)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $file->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded">
                                                    <i class="mdi mdi-cube-outline font-size-18"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $file->name ?? 'Gear Assembly.sldprt' }}</h6>
                                                <p class="text-muted mb-0">{{ $file->description ?? 'Bộ bánh răng truyền động' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $file->software ?? 'SolidWorks' }}</span>
                                    </td>
                                    <td>{{ $file->category ?? 'Cơ khí' }}</td>
                                    <td>{{ $file->file_size ?? '2.5 MB' }}</td>
                                    <td>{{ $file->download_count ?? 0 }}</td>
                                    <td>{{ $file->created_at->format('d/m/Y') ?? date('d/m/Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-eye font-size-16 text-success me-1"></i> Xem trước
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-download font-size-16 text-info me-1"></i> Tải xuống
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Chỉnh sửa
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteCADFile({{ $file->id ?? 1 }})">
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
                                            <i class="mdi mdi-cube-outline font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có file CAD nào</p>
                                            <a href="{{ route('admin.technical.cad-files.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="mdi mdi-plus me-1"></i> Upload file CAD đầu tiên
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
    document.getElementById('softwareFilter').addEventListener('change', function() {
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

function deleteCADFile(id) {
    if (confirm('Bạn có chắc chắn muốn xóa file CAD này?')) {
        // Implement delete logic
        console.log('Delete CAD file:', id);
    }
}

function exportCADFiles() {
    // Implement export logic
    console.log('Export CAD files');
}
</script>
@endsection
