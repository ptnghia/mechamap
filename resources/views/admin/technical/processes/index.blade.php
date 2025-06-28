@extends('admin.layouts.dason')

@section('title', 'Quản lý Quy trình Sản xuất')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý Quy trình Sản xuất</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Kỹ thuật</a></li>
                        <li class="breadcrumb-item active">Quy trình sản xuất</li>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng quy trình</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="mdi mdi-cog-outline font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">CNC Machining</p>
                            <h4 class="mb-0">{{ $stats['cnc'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="mdi mdi-wrench font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">3D Printing</p>
                            <h4 class="mb-0">{{ $stats['printing'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="mdi mdi-printer-3d font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Welding</p>
                            <h4 class="mb-0">{{ $stats['welding'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="mdi mdi-fire font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Processes Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách quy trình sản xuất</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.technical.processes.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus me-1"></i> Thêm quy trình
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">Tất cả loại</option>
                                <option value="cnc">CNC Machining</option>
                                <option value="3d-printing">3D Printing</option>
                                <option value="welding">Welding</option>
                                <option value="casting">Casting</option>
                                <option value="forging">Forging</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="complexityFilter">
                                <option value="">Tất cả độ phức tạp</option>
                                <option value="basic">Cơ bản</option>
                                <option value="intermediate">Trung bình</option>
                                <option value="advanced">Nâng cao</option>
                                <option value="expert">Chuyên gia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm quy trình..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="exportProcesses()">
                                <i class="mdi mdi-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="processesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Quy trình</th>
                                    <th>Loại</th>
                                    <th>Độ phức tạp</th>
                                    <th>Thời gian</th>
                                    <th>Chi phí</th>
                                    <th>Lượt xem</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($processes ?? [] as $process)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $process->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded">
                                                    <i class="mdi mdi-cog-outline font-size-18"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $process->name ?? 'CNC Milling Process' }}</h6>
                                                <p class="text-muted mb-0">{{ $process->description ?? 'Quy trình gia công CNC phay' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $process->type ?? 'CNC Machining' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $complexity = $process->complexity ?? 'intermediate';
                                            $badgeClass = [
                                                'basic' => 'bg-success',
                                                'intermediate' => 'bg-warning',
                                                'advanced' => 'bg-danger',
                                                'expert' => 'bg-dark'
                                            ][$complexity] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($complexity) }}
                                        </span>
                                    </td>
                                    <td>{{ $process->duration ?? '2-4 giờ' }}</td>
                                    <td>{{ $process->cost ?? '$50-100' }}</td>
                                    <td>{{ $process->view_count ?? 0 }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-eye font-size-16 text-success me-1"></i> Xem chi tiết
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Chỉnh sửa
                                                </a></li>
                                                <li><a class="dropdown-item" href="#">
                                                    <i class="mdi mdi-content-duplicate font-size-16 text-info me-1"></i> Nhân bản
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteProcess({{ $process->id ?? 1 }})">
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
                                            <i class="mdi mdi-cog-outline font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có quy trình sản xuất nào</p>
                                            <a href="{{ route('admin.technical.processes.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="mdi mdi-plus me-1"></i> Thêm quy trình đầu tiên
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
    document.getElementById('typeFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('complexityFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    // Check all functionality
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});

function deleteProcess(id) {
    if (confirm('Bạn có chắc chắn muốn xóa quy trình này?')) {
        // Implement delete logic
        console.log('Delete process:', id);
    }
}

function exportProcesses() {
    // Implement export logic
    console.log('Export processes');
}
</script>
@endsection
