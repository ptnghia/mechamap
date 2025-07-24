@extends('layouts.app')

@section('title', 'Sự Kiện & Webinar - Đang Phát Triển')

@section('full-width-content')
<div class="container mt-4 mb-4">
    <!-- Header Section -->
    <!--div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-calendar-days text-primary me-2"></i>
                        Sự Kiện & Webinar
                    </h1>
                    <p class="text-muted mb-0">Khám phá các sự kiện, hội thảo và cơ hội kết nối sắp tới</p>
                </div>
            </div>
        </div>
    </div-->

    <!-- Under Development Notice -->
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <!-- Icon -->
                    <div class="mb-4">
                        <i class="fa-solid fa-calendar-plus text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Title -->
                    <h2 class="h4 mb-3 text-dark">Tính năng đang được phát triển</h2>

                    <!-- Description -->
                    <p class="text-muted mb-4 lead">
                        Chúng tôi đang xây dựng một hệ thống quản lý sự kiện toàn diện dành cho cộng đồng kỹ sư cơ khí.
                        Tính năng này sẽ sớm ra mắt với đầy đủ các chức năng:
                    </p>

                    <!-- Features List -->
                    <div class="row text-start mb-4">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Tạo và quản lý sự kiện
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Đăng ký tham gia trực tuyến
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Webinar và hội thảo online
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Lịch sự kiện tương tác
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Thông báo nhắc nhở
                                </li>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    Kết nối và networking
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Tiến độ phát triển</span>
                            <span class="text-primary fw-bold">60%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 60%"></div>
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

