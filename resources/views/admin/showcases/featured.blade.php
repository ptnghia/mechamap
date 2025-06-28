@extends('admin.layouts.dason')

@section('title', 'Sản Phẩm Nổi Bật')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Sản Phẩm Nổi Bật</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.showcases.index') }}">Trưng Bày</a></li>
                    <li class="breadcrumb-item active">Nổi Bật</li>
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
                                <p class="text-muted fw-medium">Tổng Nổi Bật</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-star font-size-24"></i>
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
                                <p class="text-muted fw-medium">Lượt Xem Hôm Nay</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-eye font-size-24"></i>
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
                                <p class="text-muted fw-medium">Tương Tác</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-heart font-size-24"></i>
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
                                <p class="text-muted fw-medium">Điểm TB</p>
                                <h4 class="mb-0">0.0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-star-outline font-size-24"></i>
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
                        <h4 class="card-title">Quản Lý Sản Phẩm Nổi Bật</h4>
                        <div class="card-title-desc">Sắp xếp và quản lý các sản phẩm showcase nổi bật</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addFeaturedModal">
                                <i class="mdi mdi-plus me-1"></i> Thêm Nổi Bật
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="reorderFeatured()">
                                <i class="mdi mdi-sort me-1"></i> Sắp Xếp
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Chức năng này đang được phát triển. Sẽ sớm ra mắt!
                </div>
                
                <!-- Featured Products Grid -->
                <div class="row" id="featuredGrid">
                    <!-- Sample Featured Product Card -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border">
                            <div class="position-relative">
                                <img src="{{ asset('assets/images/small/img-1.jpg') }}" class="card-img-top" alt="Featured Product" style="height: 200px; object-fit: cover;">
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-warning">
                                        <i class="mdi mdi-star me-1"></i> Nổi Bật
                                    </span>
                                </div>
                                <div class="position-absolute top-0 start-0 p-2">
                                    <span class="badge bg-primary">#1</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Mẫu Sản Phẩm Cơ Khí</h5>
                                <p class="card-text text-muted">Mô tả ngắn về sản phẩm cơ khí này...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="mdi mdi-eye me-1"></i> 1,234 lượt xem
                                    </small>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" title="Chỉnh sửa">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" title="Bỏ nổi bật">
                                            <i class="mdi mdi-star-off"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div class="col-12" id="emptyState" style="display: none;">
                        <div class="text-center py-5">
                            <i class="mdi mdi-star-outline font-size-48 text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có sản phẩm nổi bật</h5>
                            <p class="text-muted mb-3">Thêm sản phẩm vào danh sách nổi bật để hiển thị ở trang chủ</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeaturedModal">
                                <i class="mdi mdi-plus me-1"></i> Thêm Sản Phẩm Nổi Bật
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Featured Modal -->
<div class="modal fade" id="addFeaturedModal" tabindex="-1" aria-labelledby="addFeaturedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFeaturedModalLabel">Thêm Sản Phẩm Nổi Bật</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Chức năng này sẽ được triển khai để cho phép chọn sản phẩm từ danh sách showcase hiện có.
                </div>
                <form>
                    <div class="mb-3">
                        <label for="productSearch" class="form-label">Tìm Kiếm Sản Phẩm</label>
                        <input type="text" class="form-control" id="productSearch" placeholder="Nhập tên sản phẩm...">
                    </div>
                    <div class="mb-3">
                        <label for="featuredPosition" class="form-label">Vị Trí Hiển Thị</label>
                        <select class="form-select" id="featuredPosition">
                            <option value="1">Vị trí 1 (Ưu tiên cao nhất)</option>
                            <option value="2">Vị trí 2</option>
                            <option value="3">Vị trí 3</option>
                            <option value="auto">Tự động</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary">Thêm Vào Nổi Bật</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function reorderFeatured() {
    alert('Chức năng sắp xếp lại thứ tự sẽ được triển khai');
}

// Sortable functionality would be implemented here
// using libraries like SortableJS
</script>
@endsection
