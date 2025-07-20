@extends('admin.layouts.dason')

@section('title', 'Giao Dịch Thanh Toán')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Giao Dịch Thanh Toán</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Thị Trường</a></li>
                        <li class="breadcrumb-item active">Giao Dịch</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Giao Dịch</p>
                            <h4 class="mb-0">1,247</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-exchange-alt font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Thành Công</p>
                            <h4 class="mb-0">1,156</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Đang Xử Lý</p>
                            <h4 class="mb-0">67</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Thất Bại</p>
                            <h4 class="mb-0">24</h4>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh Sách Giao Dịch</h4>
                            <p class="card-title-desc">Quản lý tất cả giao dịch thanh toán trong marketplace</p>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary">
                                    <i class="fas fa-filter me-2"></i> Lọc
                                </button>
                                <button type="button" class="btn btn-outline-success">
                                    <i class="fas fa-download me-2"></i> Xuất Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã Giao Dịch</th>
                                    <th>Người Dùng</th>
                                    <th>Đơn Hàng</th>
                                    <th>Số Tiền</th>
                                    <th>Phương Thức</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><code>TXN-2025-001247</code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/users/avatar-2.jpg') }}" alt="" class="avatar-xs rounded-circle me-2">
                                            <div>
                                                <h6 class="mb-0">Nguyễn Văn A</h6>
                                                <small class="text-muted">nguyenvana@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">#ORD-2025-0156</a></td>
                                    <td><strong class="text-success">2,450,000 VNĐ</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fab fa-cc-visa me-1"></i> Visa
                                        </span>
                                    </td>
                                    <td><span class="badge bg-success">Thành công</span></td>
                                    <td>01/07/2025 10:30</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="In hóa đơn">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" title="Hoàn tiền">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><code>TXN-2025-001246</code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/users/avatar-3.jpg') }}" alt="" class="avatar-xs rounded-circle me-2">
                                            <div>
                                                <h6 class="mb-0">Trần Thị B</h6>
                                                <small class="text-muted">tranthib@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">#ORD-2025-0155</a></td>
                                    <td><strong class="text-warning">1,200,000 VNĐ</strong></td>
                                    <td>
                                        <span class="badge bg-warning">
                                            <i class="fab fa-paypal me-1"></i> PayPal
                                        </span>
                                    </td>
                                    <td><span class="badge bg-warning">Đang xử lý</span></td>
                                    <td>01/07/2025 09:15</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" title="Xác nhận">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Hủy">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><code>TXN-2025-001245</code></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/users/avatar-4.jpg') }}" alt="" class="avatar-xs rounded-circle me-2">
                                            <div>
                                                <h6 class="mb-0">Lê Văn C</h6>
                                                <small class="text-muted">levanc@email.com</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">#ORD-2025-0154</a></td>
                                    <td><strong class="text-danger">850,000 VNĐ</strong></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-university me-1"></i> Chuyển khoản
                                        </span>
                                    </td>
                                    <td><span class="badge bg-danger">Thất bại</span></td>
                                    <td>30/06/2025 16:45</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm" title="Thử lại">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Liên hệ">
                                                <i class="fas fa-envelope"></i>
                                            </button>
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
</div>
@endsection

@section('scripts')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<!-- Datatable init js -->
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
        },
        "pageLength": 25,
        "responsive": true,
        "order": [[ 0, "desc" ]]
    });
});
</script>
@endsection

@section('styles')
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
