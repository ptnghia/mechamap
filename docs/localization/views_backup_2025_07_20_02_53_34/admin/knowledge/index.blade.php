@extends('admin.layouts.dason')

@section('title', 'Cơ Sở Tri Thức')

@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Cơ Sở Tri Thức</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                        <li class="breadcrumb-item active">Cơ Sở Tri Thức</li>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng Bài Viết</p>
                            <h4 class="mb-0">{{ number_format($stats['articles_count']) }}</h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-file-alt font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Video Hướng Dẫn</p>
                            <h4 class="mb-0">{{ number_format($stats['videos_count']) }}</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-video font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Tài Liệu</p>
                            <h4 class="mb-0">{{ number_format($stats['documents_count']) }}</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-folder font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Lượt Xem</p>
                            <h4 class="mb-0">{{ number_format($stats['total_views']) }}</h4>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-eye font-size-24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Cards -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md me-4">
                            <span class="avatar-title rounded-circle bg-light text-primary">
                                <i class="fas fa-file-alt font-size-16"></i>
                            </span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h5 class="text-truncate font-size-15">Bài Viết Kỹ Thuật</h5>
                            <p class="text-muted mb-0">Quản lý bài viết và hướng dẫn kỹ thuật</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-1">
                        <div class="d-flex justify-content-between">
                            <p class="text-muted mb-0">Đã xuất bản: <span class="fw-semibold">{{ $stats['published_articles'] }}</span></p>
                            <p class="text-muted mb-0">Nháp: <span class="fw-semibold">{{ $stats['draft_articles'] }}</span></p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.knowledge.articles.create') }}" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-plus me-1"></i> Thêm Bài Viết
                            </a>
                            <a href="{{ route('admin.knowledge.articles') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list me-1"></i> Quản Lý
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md me-4">
                            <span class="avatar-title rounded-circle bg-light text-success">
                                <i class="fas fa-video font-size-16"></i>
                            </span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h5 class="text-truncate font-size-15">Video Hướng Dẫn</h5>
                            <p class="text-muted mb-0">Quản lý video tutorials và webinars</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-1">
                        <div class="d-flex justify-content-between">
                            <p class="text-muted mb-0">Hoạt động: <span class="fw-semibold">{{ $stats['published_videos'] }}</span></p>
                            <p class="text-muted mb-0">Nháp: <span class="fw-semibold">{{ $stats['draft_videos'] }}</span></p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.knowledge.videos.create') }}" class="btn btn-success btn-sm me-2">
                                <i class="fas fa-plus me-1"></i> Thêm Video
                            </a>
                            <a href="{{ route('admin.knowledge.videos') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-list me-1"></i> Quản Lý
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md me-4">
                            <span class="avatar-title rounded-circle bg-light text-warning">
                                <i class="fas fa-folder font-size-16"></i>
                            </span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <h5 class="text-truncate font-size-15">Tài Liệu Kỹ Thuật</h5>
                            <p class="text-muted mb-0">Quản lý tài liệu và file downloads</p>
                        </div>
                    </div>
                    <div class="mt-3 pt-1">
                        <div class="d-flex justify-content-between">
                            <p class="text-muted mb-0">Công khai: <span class="fw-semibold">{{ $stats['public_documents'] }}</span></p>
                            <p class="text-muted mb-0">Riêng tư: <span class="fw-semibold">{{ $stats['private_documents'] }}</span></p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.knowledge.documents.create') }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-plus me-1"></i> Thêm Tài Liệu
                            </a>
                            <a href="{{ route('admin.knowledge.documents') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-list me-1"></i> Quản Lý
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Nội Dung Gần Đây</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Tiêu Đề</th>
                                    <th scope="col">Loại</th>
                                    <th scope="col">Tác Giả</th>
                                    <th scope="col">Trạng Thái</th>
                                    <th scope="col">Ngày Tạo</th>
                                    <th scope="col">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h6 class="mb-0">Hướng Dẫn Thiết Kế Bánh Răng</h6>
                                        <p class="text-muted font-size-13 mb-0">Cách tính toán và thiết kế bánh răng...</p>
                                    </td>
                                    <td><span class="badge bg-primary">Bài viết</span></td>
                                    <td>Nguyễn Văn A</td>
                                    <td><span class="badge bg-success">Đã xuất bản</span></td>
                                    <td>01/07/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6 class="mb-0">Video: Sử Dụng AutoCAD 2025</h6>
                                        <p class="text-muted font-size-13 mb-0">Tutorial cơ bản về AutoCAD...</p>
                                    </td>
                                    <td><span class="badge bg-success">Video</span></td>
                                    <td>Trần Thị B</td>
                                    <td><span class="badge bg-warning">Nháp</span></td>
                                    <td>30/06/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h6 class="mb-0">Tiêu Chuẩn ISO 9001:2015</h6>
                                        <p class="text-muted font-size-13 mb-0">Tài liệu tiêu chuẩn chất lượng...</p>
                                    </td>
                                    <td><span class="badge bg-warning">Tài liệu</span></td>
                                    <td>Lê Văn C</td>
                                    <td><span class="badge bg-success">Đã xuất bản</span></td>
                                    <td>29/06/2025</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
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

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh Mục Phổ Biến</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center border-bottom pb-2">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-primary text-white">
                                <i class="fas fa-cog"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">Cơ Khí Chế Tạo</h6>
                            <p class="font-size-13 text-muted mb-0">45 bài viết</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom pb-2 pt-2">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-success text-white">
                                <i class="fas fa-tools"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">CAD/CAM</h6>
                            <p class="font-size-13 text-muted mb-0">38 bài viết</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center border-bottom pb-2 pt-2">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-warning text-white">
                                <i class="fas fa-industry"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">Tự Động Hóa</h6>
                            <p class="font-size-13 text-muted mb-0">32 bài viết</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center pt-2">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title rounded-circle bg-info text-white">
                                <i class="fas fa-calculator"></i>
                            </span>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1">Tính Toán Kỹ Thuật</h6>
                            <p class="font-size-13 text-muted mb-0">28 bài viết</p>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
