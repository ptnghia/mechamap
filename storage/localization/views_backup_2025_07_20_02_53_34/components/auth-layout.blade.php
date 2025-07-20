{{-- Auth Layout Component --}}
@props([
    'title' => 'MechaMap',
    'subtitle' => 'Nơi hội tụ tri thức cơ khí',
    'showSocialLogin' => true,
    'formWidth' => 'col-lg-6'
])

<div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Main Auth Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    <div class="row g-0">
                        <!-- Brand Section -->
                        <div class="col-lg-6 bg-primary text-white position-relative">
                            <div class="p-5 h-100 d-flex flex-column justify-content-center">
                                <!-- Background Pattern -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
                                    <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <defs>
                                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100%" height="100%" fill="url(#grid)" />
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="position-relative">
                                    <!-- Logo -->
                                    <div class="mb-4">
                                        <img src="{{ get_logo_url() }}" alt="MechaMap" class="mb-3" style="height: 50px; filter: brightness(0) invert(1);">
                                        <h3 class="fw-bold mb-2">{{ $title }}</h3>
                                        <p class="fs-5 mb-0 opacity-90">{{ $subtitle }}</p>
                                    </div>

                                    <!-- Value Propositions -->
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-users-cog me-3 fs-5"></i>
                                            <span>Kết nối với 64+ kỹ sư chuyên nghiệp</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-comments me-3 fs-5"></i>
                                            <span>Tham gia 118+ thảo luận kỹ thuật</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-tools me-3 fs-5"></i>
                                            <span>Chia sẻ kinh nghiệm CAD/CAM/CNC</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-graduation-cap me-3 fs-5"></i>
                                            <span>Học hỏi từ chuyên gia hàng đầu</span>
                                        </div>
                                    </div>

                                    <!-- Trust Indicators -->
                                    <div class="border-top border-white border-opacity-25 pt-3">
                                        <p class="small mb-2 opacity-75">Được tin tưởng bởi:</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-white bg-opacity-20 text-white">Kỹ sư CAD</span>
                                            <span class="badge bg-white bg-opacity-20 text-white">Chuyên gia CNC</span>
                                            <span class="badge bg-white bg-opacity-20 text-white">Nhà sản xuất</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Section -->
                        <div class="{{ $formWidth }} bg-white">
                            <div class="p-5">
                                <!-- Form Content -->
                                <div class="mb-4">
                                    {{ $slot }}
                                </div>

                                <!-- Social Login (if enabled) -->
                                @if($showSocialLogin)
                                <div class="my-4">
                                    <div class="position-relative text-center">
                                        <hr class="my-4">
                                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                                            Hoặc đăng nhập với
                                        </span>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('auth.socialite', 'google') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                                            <i class="fab fa-google me-2"></i>
                                            Google
                                        </a>
                                        <a href="{{ route('auth.socialite', 'facebook') }}" class="btn btn-primary d-flex align-items-center justify-content-center">
                                            <i class="fab fa-facebook-f me-2"></i>
                                            Facebook
                                        </a>
                                    </div>
                                </div>
                                @endif

                                <!-- Security Badge -->
                                <div class="text-center mt-4">
                                    <small class="text-muted d-flex align-items-center justify-content-center">
                                        <i class="fas fa-shield-alt me-2 text-success"></i>
                                        Bảo mật SSL 256-bit
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Community Highlights Section -->
<div class="bg-white py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark">Tham gia cộng đồng kỹ thuật hàng đầu Việt Nam</h4>
                    <p class="text-muted">Khám phá những thảo luận nổi bật và kết nối với các chuyên gia</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 community-card">
                            <div class="card-body text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-fire text-primary fs-4"></i>
                                </div>
                                <h5 class="card-title">Xu hướng nổi bật</h5>
                                <p class="card-text text-muted">Mastercam, Siemens PLC, Robot công nghiệp</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 community-card">
                            <div class="card-body text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-network-wired text-success fs-4"></i>
                                </div>
                                <h5 class="card-title">Mạng lưới chuyên gia</h5>
                                <p class="card-text text-muted">64+ kỹ sư từ các công ty hàng đầu</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 community-card">
                            <div class="card-body text-center">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-book-open text-info fs-4"></i>
                                </div>
                                <h5 class="card-title">Kho tri thức</h5>
                                <p class="card-text text-muted">118+ thảo luận chất lượng cao</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
