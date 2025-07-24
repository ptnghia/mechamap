@extends('admin.layouts.dason')

@section('title', 'Danh Mục Sản Phẩm')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Danh Mục Sản Phẩm</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Thị Trường</a></li>
                        <li class="breadcrumb-item active">Danh Mục Sản Phẩm</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Quản Lý Danh Mục Sản Phẩm</h4>
                            <p class="card-title-desc">Quản lý các danh mục sản phẩm trong marketplace MechaMap</p>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary waves-effect waves-light">
                                <i class="fas fa-plus me-2"></i> Thêm Danh Mục
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Danh Mục</th>
                                    <th>Slug</th>
                                    <th>Mô Tả</th>
                                    <th>Sản Phẩm</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-primary text-white font-size-16">
                                                    <i class="fas fa-cog"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Linh Kiện Cơ Khí</h6>
                                                <small class="text-muted">Mechanical Components</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>linh-kien-co-khi</code></td>
                                    <td>Các linh kiện cơ khí chính xác cao</td>
                                    <td><span class="badge bg-info">245 sản phẩm</span></td>
                                    <td><span class="badge bg-success">Hoạt động</span></td>
                                    <td>25/06/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-success text-white font-size-16">
                                                    <i class="fas fa-tools"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Dụng Cụ & Thiết Bị</h6>
                                                <small class="text-muted">Tools & Equipment</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>dung-cu-thiet-bi</code></td>
                                    <td>Dụng cụ và thiết bị cơ khí chuyên dụng</td>
                                    <td><span class="badge bg-info">189 sản phẩm</span></td>
                                    <td><span class="badge bg-success">Hoạt động</span></td>
                                    <td>20/06/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-warning text-white font-size-16">
                                                    <i class="fas fa-file-alt"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Tài Liệu Kỹ Thuật</h6>
                                                <small class="text-muted">Technical Documents</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>tai-lieu-ky-thuat</code></td>
                                    <td>Bản vẽ, tài liệu kỹ thuật và CAD files</td>
                                    <td><span class="badge bg-info">156 sản phẩm</span></td>
                                    <td><span class="badge bg-success">Hoạt động</span></td>
                                    <td>15/06/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa">
                                                <i class="fas fa-trash"></i>
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
