@extends('layouts.app')

@section('title', 'Cộng đồng - MechaMap')

@section('content')
<div class="py-5">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fa-solid fa-users me-3"></i>
                    Cộng đồng MechaMap
                </h1>
                <p class="lead text-muted">
                    Kết nối với hàng nghìn kỹ sư cơ khí chuyên nghiệp trên toàn quốc
                </p>
            </div>
        </div>

        <!-- Community Features Grid -->
        <div class="row g-4">
            <!-- Forums -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-comments fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Diễn đàn Thảo luận</h5>
                        <p class="card-text text-muted">
                            Tham gia thảo luận về kỹ thuật, chia sẻ kinh nghiệm và giải đáp thắc mắc
                        </p>
                        <a href="{{ route('forums.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-right me-2"></i>Tham gia ngay
                        </a>
                    </div>
                </div>
            </div>

            <!-- Members Directory -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-address-book fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title fw-bold">Danh bạ Thành viên</h5>
                        <p class="card-text text-muted">
                            Tìm kiếm và kết nối với các kỹ sư, chuyên gia trong lĩnh vực của bạn
                        </p>
                        <a href="{{ route('members.index') }}" class="btn btn-success">
                            <i class="fa-solid fa-arrow-right me-2"></i>Khám phá
                        </a>
                    </div>
                </div>
            </div>

            <!-- Companies -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-building fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title fw-bold">Doanh nghiệp</h5>
                        <p class="card-text text-muted">
                            Khám phá các công ty, nhà máy và doanh nghiệp trong ngành cơ khí
                        </p>
                        <a href="#" class="btn btn-warning">
                            <i class="fa-solid fa-arrow-right me-2"></i>Xem danh sách
                        </a>
                    </div>
                </div>
            </div>

            <!-- Events -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-calendar-days fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title fw-bold">Sự kiện</h5>
                        <p class="card-text text-muted">
                            Tham gia hội thảo, workshop và các sự kiện kỹ thuật chuyên ngành
                        </p>
                        <a href="#" class="btn btn-info">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sự kiện sắp tới
                        </a>
                    </div>
                </div>
            </div>

            <!-- Job Board -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-briefcase fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title fw-bold">Việc làm</h5>
                        <p class="card-text text-muted">
                            Tìm kiếm cơ hội nghề nghiệp và đăng tin tuyển dụng trong ngành cơ khí
                        </p>
                        <a href="#" class="btn btn-danger">
                            <i class="fa-solid fa-arrow-right me-2"></i>Tìm việc ngay
                        </a>
                    </div>
                </div>
            </div>

            <!-- Groups -->
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fa-solid fa-user-group fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title fw-bold">Nhóm chuyên môn</h5>
                        <p class="card-text text-muted">
                            Tham gia các nhóm chuyên môn theo lĩnh vực: CAD/CAM, FEA, Robot...
                        </p>
                        <a href="#" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-right me-2"></i>Sắp ra mắt
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Stats -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4 text-center">
                            <i class="fa-solid fa-chart-line me-2"></i>
                            Thống kê Cộng đồng
                        </h4>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <i class="fa-solid fa-users fa-2x mb-2"></i>
                                    <h3 class="fw-bold">{{ number_format(1250) }}+</h3>
                                    <p class="mb-0">Thành viên</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <i class="fa-solid fa-comments fa-2x mb-2"></i>
                                    <h3 class="fw-bold">{{ number_format(8500) }}+</h3>
                                    <p class="mb-0">Thảo luận</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <i class="fa-solid fa-building fa-2x mb-2"></i>
                                    <h3 class="fw-bold">{{ number_format(180) }}+</h3>
                                    <p class="mb-0">Doanh nghiệp</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <i class="fa-solid fa-calendar fa-2x mb-2"></i>
                                    <h3 class="fw-bold">{{ number_format(45) }}+</h3>
                                    <p class="mb-0">Sự kiện/tháng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">
                            <i class="fa-solid fa-rocket me-2 text-primary"></i>
                            Bắt đầu ngay
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="{{ route('threads.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="fa-solid fa-plus me-2"></i>Tạo thảo luận
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('register') }}" class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-user-plus me-2"></i>Đăng ký thành viên
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('marketplace.index') }}" class="btn btn-outline-warning w-100">
                                    <i class="fa-solid fa-store me-2"></i>Marketplace
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-info w-100">
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

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
</style>
@endpush
