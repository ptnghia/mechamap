@extends('admin.layouts.dason')

@section('title', 'Sản Phẩm Chờ Duyệt')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Sản Phẩm Chờ Duyệt</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.showcases.index') }}">Trưng Bày</a></li>
                    <li class="breadcrumb-item active">Chờ Duyệt</li>
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
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Chờ Duyệt</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="fas fa-clock-outline font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Đã Duyệt Hôm Nay</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="fas fa-check-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Từ Chối</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title">
                                        <i class="fas fa-times-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Thời Gian Chờ TB</p>
                                <h4 class="mb-0">0h</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-timer-sand font-size-24"></i>
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
                        <h4 class="card-title">Danh Sách Sản Phẩm Chờ Duyệt</h4>
                        <div class="card-title-desc">Quản lý các sản phẩm showcase đang chờ phê duyệt</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm" onclick="approveSelected()">
                                <i class="fas fa-check me-1"></i> Duyệt Đã Chọn
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="rejectSelected()">
                                <i class="fas fa-times me-1"></i> Từ Chối Đã Chọn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle-outline me-2"></i>
                    Chức năng này đang được phát triển. Sẽ sớm ra mắt!
                </div>
                
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Tất cả danh mục</option>
                            <option value="mechanical">Cơ khí</option>
                            <option value="electrical">Điện - Điện tử</option>
                            <option value="automotive">Ô tô</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="priorityFilter">
                            <option value="">Tất cả mức độ</option>
                            <option value="high">Ưu tiên cao</option>
                            <option value="medium">Ưu tiên trung bình</option>
                            <option value="low">Ưu tiên thấp</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="dateFilter" placeholder="Ngày gửi">
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm..." id="searchInput">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>Sản Phẩm</th>
                                <th>Người Gửi</th>
                                <th>Danh Mục</th>
                                <th>Ngày Gửi</th>
                                <th>Mức Độ</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="mdi mdi-inbox font-size-48 text-muted mb-2"></i>
                                        <h5 class="text-muted">Không có sản phẩm nào chờ duyệt</h5>
                                        <p class="text-muted mb-0">Tất cả sản phẩm đã được xử lý</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function approveSelected() {
    // Implementation for bulk approve
    alert('Chức năng duyệt hàng loạt sẽ được triển khai');
}

function rejectSelected() {
    // Implementation for bulk reject
    alert('Chức năng từ chối hàng loạt sẽ được triển khai');
}

// Check all functionality
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection
