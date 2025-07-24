@extends('layouts.app')

@section('title', 'Tài nguyên Kỹ thuật - MechaMap')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fa-solid fa-wrench me-3"></i>
                    Tài nguyên Kỹ thuật
                </h1>
                <p class="lead text-muted">
                    Trung tâm tài nguyên kỹ thuật dành cho kỹ sư cơ khí chuyên nghiệp
                </p>
            </div>
        </div>

        <!-- Technical Resources Grid -->
        <div class="row g-4">
            <!-- Technical Drawings -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-drafting-compass fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Bản vẽ Kỹ thuật</h5>
                        <p class="card-text text-muted">
                            Thư viện bản vẽ kỹ thuật chuẩn, chi tiết gia công và assembly drawings
                        </p>
                        <a href="{{ route('technical.drawings.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-right me-2"></i>Xem thêm
                        </a>
                    </div>
                </div>
            </div>

            <!-- CAD Files -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-cube fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold">File CAD</h5>
                        <p class="card-text text-muted">
                            Thư viện file CAD 3D, 2D drawings và models cho các ứng dụng cơ khí
                        </p>
                        <a href="#" class="btn btn-success">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>

            <!-- Material Database -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-flask fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title fw-bold">Cơ sở dữ liệu Vật liệu</h5>
                        <p class="card-text text-muted">
                            Thông tin chi tiết về tính chất vật liệu, thép, hợp kim và vật liệu composite
                        </p>
                        <a href="#" class="btn btn-warning">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>

            <!-- Engineering Standards -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-book fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title fw-bold">Tiêu chuẩn Kỹ thuật</h5>
                        <p class="card-text text-muted">
                            Tiêu chuẩn TCVN, ISO, ASME, DIN và các quy chuẩn kỹ thuật quốc tế
                        </p>
                        <a href="#" class="btn btn-info">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>

            <!-- Calculation Tools -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-calculator fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">Công cụ Tính toán</h5>
                        <p class="card-text text-muted">
                            Bộ công cụ tính toán kỹ thuật: độ bền, ứng suất, thiết kế trục, bánh răng
                        </p>
                        <a href="#" class="btn btn-danger">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manufacturing Processes -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-cogs fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Quy trình Sản xuất</h5>
                        <p class="card-text text-muted">
                            Hướng dẫn quy trình gia công, nhiệt luyện, hàn và các công nghệ sản xuất
                        </p>
                        <a href="#" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="fa-solid fa-bolt me-2 text-warning"></i>
                            Truy cập nhanh
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('forums.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fa-solid fa-comments me-2"></i>Diễn đàn
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-store me-2"></i>Marketplace
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('showcase.index') }}" class="btn btn-outline-info w-100">
                                    <i class="fa-solid fa-trophy me-2"></i>Showcase
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-warning w-100">
                                    <i class="fa-solid fa-graduation-cap me-2"></i>Học tập
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.fa-3x {
    transition: transform 0.3s ease;
}

.card:hover .fa-3x {
    transform: scale(1.1);
}
</style>
@endpush
