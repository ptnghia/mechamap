@extends('layouts.app')

@section('title', 'Tổng quan trang cố định - MechaMap')

@push('styles')
<style>
.page-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.page-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}
.page-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 20px;
}
.completed { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
.in-progress { background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; }
.planned { background: linear-gradient(135deg, #6c757d, #495057); color: white; }
</style>
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Header -->
    <div class="bg-white border-bottom">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2">📄 Tổng quan Trang cố định</h1>
                    <p class="text-muted mb-0">Danh sách và trạng thái các trang thông tin cố định của MechaMap</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <span class="badge bg-success">✅ Hoàn thành</span>
                        <span class="badge bg-warning">🚧 Đang phát triển</span>
                        <span class="badge bg-secondary">📋 Kế hoạch</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container py-5">
        <!-- Statistics -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">7</h3>
                        <small>Trang hoàn thành</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">2</h3>
                        <small>Đang phát triển</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">3</h3>
                        <small>Kế hoạch</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-1">83%</h3>
                        <small>Tiến độ hoàn thành</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pages Grid -->
        <div class="row">
            <!-- Completed Pages -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="card-title"{{ t_content('pages.pages.about_us') }}/h5>
                        <p class="card-text text-muted">Thông tin về MechaMap, tầm nhìn, sứ mệnh và đội ngũ phát triển.</p>
                        <a href="{{ route('about.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h5 class="card-title"{{ t_content('pages.pages.contact') }}/h5>
                        <p class="card-text text-muted">Thông tin liên hệ, form gửi tin nhắn và địa chỉ văn phòng.</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h5 class="card-title"{{ t_content('pages.pages.terms_of_service') }}/h5>
                        <p class="card-text text-muted">Quy định và điều khoản sử dụng dịch vụ MechaMap.</p>
                        <a href="{{ route('terms.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5 class="card-title"{{ t_content('pages.pages.privacy_policy') }}/h5>
                        <p class="card-text text-muted">Cam kết bảo vệ thông tin cá nhân và quyền riêng tư người dùng.</p>
                        <a href="{{ route('privacy.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h5 class="card-title">Trợ giúp</h5>
                        <p class="card-text text-muted">Hướng dẫn sử dụng và trung tâm hỗ trợ người dùng.</p>
                        <a href="{{ route('help.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h5 class="card-title">Quy định cộng đồng</h5>
                        <p class="card-text text-muted">Quy tắc ứng xử và nguyên tắc tham gia cộng đồng.</p>
                        <a href="{{ route('rules') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-success status-badge">✅ Hoàn thành</span>
                    <div class="card-body text-center">
                        <div class="page-icon completed">
                            <i class="fas fa-pen-fancy"></i>
                        </div>
                        <h5 class="card-title">Hướng dẫn viết bài</h5>
                        <p class="card-text text-muted">Hướng dẫn cách viết bài hiệu quả trên diễn đàn.</p>
                        <a href="{{ route('help.writing-guide') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <!-- In Progress Pages -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-warning status-badge">🚧 Đang phát triển</span>
                    <div class="card-body text-center">
                        <div class="page-icon in-progress">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h5 class="card-title">FAQ</h5>
                        <p class="card-text text-muted">Câu hỏi thường gặp và câu trả lời chi tiết.</p>
                        <a href="{{ route('faq.index') }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-eye me-1"></i>Xem trang
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-warning status-badge">🚧 Đang phát triển</span>
                    <div class="card-body text-center">
                        <div class="page-icon in-progress">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <h5 class="card-title">Sitemap</h5>
                        <p class="card-text text-muted">Bản đồ trang web và cấu trúc điều hướng.</p>
                        <a href="/sitemap.xml" class="btn btn-outline-warning btn-sm" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>Xem XML
                        </a>
                    </div>
                </div>
            </div>

            <!-- Planned Pages -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-secondary status-badge">📋 Kế hoạch</span>
                    <div class="card-body text-center">
                        <div class="page-icon planned">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h5 class="card-title">Blog/Tin tức</h5>
                        <p class="card-text text-muted">Tin tức ngành, bài viết chuyên môn và cập nhật từ MechaMap.</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="fas fa-clock me-1"></i>Sắp ra mắt
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-secondary status-badge">📋 Kế hoạch</span>
                    <div class="card-body text-center">
                        <div class="page-icon planned">
                            <i class="fas fa-code"></i>
                        </div>
                        <h5 class="card-title">API Documentation</h5>
                        <p class="card-text text-muted">Tài liệu API cho developers và tích hợp bên thứ ba.</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="fas fa-clock me-1"></i>Sắp ra mắt
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card page-card h-100 position-relative">
                    <span class="badge bg-secondary status-badge">📋 Kế hoạch</span>
                    <div class="card-body text-center">
                        <div class="page-icon planned">
                            <i class="fas fa-cookie-bite"></i>
                        </div>
                        <h5 class="card-title">Cookie Policy</h5>
                        <p class="card-text text-muted">Chính sách sử dụng cookie và công nghệ theo dõi.</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="fas fa-clock me-1"></i>Sắp ra mắt
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mt-5">
            <a href="{{ route('home') }}" class="btn btn-primary me-2">
                <i class="fas fa-home me-1"></i>Về trang chủ
            </a>
            <a href="{{ route('contact') }}" class="btn btn-outline-secondary">
                <i class="fas fa-envelope me-1"></i>Góp ý cải thiện
            </a>
        </div>
    </div>
</div>
@endsection
