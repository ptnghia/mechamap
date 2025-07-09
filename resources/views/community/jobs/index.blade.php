@extends('layouts.app')

@section('title', 'Bảng Việc Làm - Đang Phát Triển')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-briefcase text-primary me-2"></i>
                        Bảng Việc Làm
                    </h1>
                    <p class="text-muted mb-0">Tìm kiếm cơ hội nghề nghiệp tiếp theo trong lĩnh vực cơ khí</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Under Development Notice -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="fa-solid fa-tools text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Title -->
                    <h2 class="h4 mb-3 text-dark">Tính năng đang được phát triển</h2>

                    <!-- Description -->
                    <p class="text-muted mb-4 lead">
                        Chúng tôi đang xây dựng một hệ thống việc làm chuyên nghiệp dành riêng cho cộng đồng kỹ sư cơ khí.
                        Tính năng này sẽ sớm ra mắt với đầy đủ các chức năng:
                    </p>

                    <!-- Features List -->
                    <div class="row text-start mb-4">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Đăng tin tuyển dụng
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Tìm kiếm việc làm theo chuyên ngành
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Ứng tuyển trực tuyến
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Quản lý hồ sơ ứng viên
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Thông báo việc làm phù hợp
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Đánh giá và review công ty
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Tiến độ phát triển</span>
                            <span class="text-primary fw-bold">75%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home me-2"></i>
                            Về Trang Chủ
                        </a>
                        <a href="{{ route('threads.index') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-comments me-2"></i>
                            Tham Gia Thảo Luận
                        </a>
                    </div>

                    <!-- Contact Info -->
                    <div class="mt-4 pt-4 border-top">
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-envelope me-2"></i>
                            Có ý kiến đóng góp? Liên hệ:
                            <a href="mailto:admin@mechamap.vn" class="text-decoration-none">admin@mechamap.vn</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
