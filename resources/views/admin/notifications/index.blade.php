@extends('admin.layouts.dason')

@section('title', 'Thông Báo')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Thông Báo</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Thông Báo</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Notification Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Tổng Thông Báo</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-bell font-size-24"></i>
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
                                <p class="text-muted fw-medium">Chưa Đọc</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-bell-alert font-size-24"></i>
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
                                <p class="text-muted fw-medium">Hôm Nay</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-calendar-today font-size-24"></i>
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
                                <p class="text-muted fw-medium">Quan Trọng</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title">
                                        <i class="mdi mdi-alert-circle font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Thông Báo</h4>
                        <div class="card-title-desc">Quản lý tất cả thông báo hệ thống</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm" onclick="markAllAsRead()">
                                <i class="mdi mdi-check-all me-1"></i> Đánh Dấu Tất Cả Đã Đọc
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="mdi mdi-filter me-1"></i> Lọc
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?filter=unread">Chưa đọc</a></li>
                                <li><a class="dropdown-item" href="?filter=read">Đã đọc</a></li>
                                <li><a class="dropdown-item" href="?filter=important">Quan trọng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?">Tất cả</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Hệ thống thông báo đang được phát triển. Sẽ sớm ra mắt!
                </div>

                <!-- Sample Notifications -->
                <div class="list-group list-group-flush">
                    <!-- Sample Notification 1 -->
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm me-3">
                                    <span class="avatar-title bg-primary rounded-circle">
                                        <i class="mdi mdi-account-plus"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Người dùng mới đăng ký</h6>
                                    <p class="mb-1 text-muted">Nguyễn Văn A đã đăng ký tài khoản mới và đang chờ phê duyệt.</p>
                                    <small class="text-muted">2 giờ trước</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Đánh dấu đã đọc</a></li>
                                    <li><a class="dropdown-item" href="#">Xem chi tiết</a></li>
                                    <li><a class="dropdown-item text-danger" href="#">Xóa</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Notification 2 -->
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm me-3">
                                    <span class="avatar-title bg-warning rounded-circle">
                                        <i class="mdi mdi-alert-circle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Báo cáo vi phạm mới</h6>
                                    <p class="mb-1 text-muted">Có báo cáo vi phạm mới cần được xem xét và xử lý.</p>
                                    <small class="text-muted">5 giờ trước</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Đánh dấu đã đọc</a></li>
                                    <li><a class="dropdown-item" href="#">Xem chi tiết</a></li>
                                    <li><a class="dropdown-item text-danger" href="#">Xóa</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sample Notification 3 -->
                    <div class="list-group-item list-group-item-action bg-light">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm me-3">
                                    <span class="avatar-title bg-success rounded-circle">
                                        <i class="mdi mdi-check-circle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Sản phẩm đã được duyệt</h6>
                                    <p class="mb-1 text-muted">Sản phẩm "Máy tiện CNC" đã được phê duyệt và hiển thị công khai.</p>
                                    <small class="text-muted">1 ngày trước</small>
                                </div>
                            </div>
                            <span class="badge bg-success">Đã đọc</span>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="text-center py-5" style="display: none;" id="emptyState">
                    <i class="mdi mdi-bell-off font-size-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Không có thông báo</h5>
                    <p class="text-muted">Tất cả thông báo đã được xử lý</p>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-sm-6">
                        <div>
                            <p class="mb-sm-0">Hiển thị 1 đến 3 của 3 thông báo</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-sm-end">
                            <ul class="pagination pagination-rounded mb-sm-0">
                                <li class="page-item disabled">
                                    <a href="#" class="page-link"><i class="mdi mdi-chevron-left"></i></a>
                                </li>
                                <li class="page-item active">
                                    <a href="#" class="page-link">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <a href="#" class="page-link"><i class="mdi mdi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function markAllAsRead() {
    // Implementation for marking all notifications as read
    alert('Chức năng đánh dấu tất cả đã đọc sẽ được triển khai');
}

// Auto-refresh notifications every 30 seconds
setInterval(function() {
    // Implementation for auto-refresh
    console.log('Auto-refreshing notifications...');
}, 30000);
</script>
@endsection
