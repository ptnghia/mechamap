@extends('layouts.app')

@section('title', 'Về MechaMap')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Về MechaMap - Cộng đồng Kỹ thuật Cơ khí
                    </h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="h4 text-primary mb-3">Chúng tôi là ai?</h2>
                            <p class="lead">
                                MechaMap là nền tảng forum chuyên về kỹ thuật cơ khí, nơi các kỹ sư, nhà thiết kế,
                                sinh viên và những người đam mê cơ khí có thể chia sẻ kiến thức, thảo luận về
                                các vấn đề kỹ thuật và cùng nhau phát triển.
                            </p>

                            <h3 class="h5 text-primary mt-4 mb-3">Sứ mệnh của chúng tôi</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle-fill text-success me-2"></i>
                                    Kết nối cộng đồng kỹ sư cơ khí Việt Nam và thế giới
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle-fill text-success me-2"></i>
                                    Chia sẻ kiến thức chuyên môn về thiết kế, chế tạo và vật liệu
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle-fill text-success me-2"></i>
                                    Hỗ trợ giải đáp các vấn đề kỹ thuật trong thực tế
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check-circle-fill text-success me-2"></i>
                                    Cập nhật xu hướng công nghệ mới trong ngành cơ khí
                                </li>
                            </ul>

                            <h3 class="h5 text-primary mt-4 mb-3">Các chuyên mục chính</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-cog-fill text-primary me-2"></i>
                                            Thiết kế Cơ khí
                                        </li>
                                        <li class="mb-2">
                                            <i class="tools text-primary me-2"></i>
                                            Công nghệ Chế tạo
                                        </li>
                                        <li class="mb-2">
                                            <i class="cpu text-primary me-2"></i>
                                            CAD/CAM/CAE
                                        </li>
                                        <li class="mb-2">
                                            <i class="robot text-primary me-2"></i>
                                            Tự động hóa
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="bricks text-primary me-2"></i>
                                            Vật liệu Kỹ thuật
                                        </li>
                                        <li class="mb-2">
                                            <i class="wrench-adjustable text-primary me-2"></i>
                                            Bảo trì & Sửa chữa
                                        </li>
                                        <li class="mb-2">
                                            <i class="lightning text-primary me-2"></i>
                                            Cơ điện tử
                                        </li>
                                        <li class="mb-2">
                                            <i class="globe text-primary me-2"></i>
                                            Tin tức Ngành
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h3 class="h5 text-primary mt-4 mb-3"{{ t_content('content/pages.community_rules') }}/h3>
                            <p>
                                Chúng tôi khuyến khích việc thảo luận chuyên nghiệp, chia sẻ kiến thức thực tế
                                và tôn trọng lẫn nhau. Mọi thành viên đều được khuyến khích đóng góp ý kiến
                                xây dựng và hỗ trợ nhau trong việc giải quyết các vấn đề kỹ thuật.
                            </p>

                            <div class="mt-4">
                                <a href="{{ route('rules') }}" class="btn btn-outline-primary me-2">
                                    <i class="book me-1"></i>Xem quy tắc chi tiết
                                </a>
                                <a href="{{ route('forums.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i>Khám phá diễn đàn
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="text-primary mb-3">Thống kê cộng đồng</h4>

                                    <div class="mb-3">
                                        <div class="display-6 text-primary fw-bold">{{
                                            number_format(\App\Models\User::count()) }}</div>
                                        <small class="text-muted">Thành viên</small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="display-6 text-success fw-bold">{{
                                            number_format(\App\Models\Thread::count()) }}</div>
                                        <small class="text-muted">Chủ đề thảo luận</small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="display-6 text-info fw-bold">{{
                                            number_format(\App\Models\Comment::count()) }}</div>
                                        <small class="text-muted">Bình luận</small>
                                    </div>

                                    <hr>

                                    <h5 class="text-primary mb-3">Tham gia ngay</h5>
                                    @guest
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('register') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i>Đăng ký
                                        </a>
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                            <i class="box-arrow-in-right me-1"></i>Đăng nhập
                                        </a>
                                    </div>
                                    @else
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Chào mừng {{ auth()->user()->name }}!
                                    </div>
                                    @endguest
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-body text-center">
                                    <h5 class="text-primary mb-3"{{ t_content('content/pages.contact') }}/h5>
                                    <p class="small text-muted">
                                        Có câu hỏi hoặc góp ý? Chúng tôi luôn sẵn sàng lắng nghe.
                                    </p>
                                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-envelope me-1"></i>Liên hệ với chúng tôi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        <i class="calendar me-1"></i>
                        Hoạt động từ {{ config('app.established_year', '2023') }} |
                        <i class="fas fa-heart-fill text-danger me-1"></i>
                        Được xây dựng với niềm đam mê về kỹ thuật cơ khí
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .display-6 {
        font-size: 2rem;
    }
</style>
@endpush