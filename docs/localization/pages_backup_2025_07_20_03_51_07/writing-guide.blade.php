@extends('layouts.app')

@section('title', 'Hướng Dẫn Viết Bài')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h1 class="h3 mb-0">
                        <i class="book me-2"></i>
                        Hướng Dẫn Viết Bài Chất Lượng
                    </h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sidebar menu -->
                        <div class="col-lg-3">
                            <div class="list-group sticky-top" style="top: 20px;">
                                <a href="#section-title" class="list-group-item list-group-item-action">
                                    <i class="1-circle me-2"></i>Viết Tiêu Đề
                                </a>
                                <a href="#section-content" class="list-group-item list-group-item-action">
                                    <i class="2-circle me-2"></i>Cấu Trúc Nội Dung
                                </a>
                                <a href="#section-images" class="list-group-item list-group-item-action">
                                    <i class="3-circle me-2"></i>Sử Dụng Hình Ảnh
                                </a>
                                <a href="#section-category" class="list-group-item list-group-item-action">
                                    <i class="4-circle me-2"></i>Chọn Danh Mục
                                </a>
                                <a href="#section-tips" class="list-group-item list-group-item-action">
                                    <i class="5-circle me-2"></i>Mẹo Bổ Sung
                                </a>
                            </div>
                        </div>

                        <!-- Main content -->
                        <div class="col-lg-9">
                            <section id="section-title" class="mb-5">
                                <h3 class="text-success mb-3">
                                    <i class="1-circle-fill me-2"></i>
                                    Cách Viết Tiêu Đề Hiệu Quả
                                </h3>

                                <div class="alert alert-info">
                                    <strong>Tiêu đề tốt = 50% thành công của bài viết!</strong>
                                </div>

                                <h5>✅ Nên làm:</h5>
                                <ul>
                                    <li><strong>Rõ ràng và cụ thể:</strong> "Cách tính toán kết cấu bê tông cốt thép cho
                                        nhà 3 tầng"</li>
                                    <li><strong>Sử dụng từ khóa quan trọng:</strong> "Autocad", "Quy hoạch", "Kết cấu"
                                    </li>
                                    <li><strong>Giới hạn 60-80 ký tự:</strong> Đủ dài để mô tả, đủ ngắn để dễ đọc</li>
                                    <li><strong>Tạo tò mò:</strong> "5 sai lầm phổ biến khi thiết kế cầu thang"</li>
                                </ul>

                                <h5>❌ Tránh:</h5>
                                <ul class="text-danger">
                                    <li>"Help me!!!", "Cần gấp!!!", "SOS!!!"</li>
                                    <li>"Hỏi về thiết kế" (quá chung chung)</li>
                                    <li>VIẾT HOA TOÀN BỘ TIÊU ĐỀ</li>
                                    <li>Dùng quá nhiều ký tự đặc biệt !!??***</li>
                                </ul>
                            </section>

                            <section id="section-content" class="mb-5">
                                <h3 class="text-success mb-3">
                                    <i class="2-circle-fill me-2"></i>
                                    Cấu Trúc Nội Dung Hoàn Hảo
                                </h3>

                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6>📝 Template chuẩn:</h6>
                                        <ol>
                                            <li><strong>Mở đầu:</strong> Giới thiệu vấn đề/chủ đề</li>
                                            <li><strong>Thân bài:</strong> Chi tiết, ví dụ, hình ảnh</li>
                                            <li><strong>Kết luận:</strong> Tóm tắt, đặt câu hỏi để thảo luận</li>
                                        </ol>
                                    </div>
                                </div>

                                <h5>💡 Mẹo viết hay:</h5>
                                <ul>
                                    <li><strong>Chia nhỏ đoạn văn:</strong> Mỗi đoạn 3-5 câu</li>
                                    <li><strong>Sử dụng bullet points:</strong> Dễ đọc và theo dõi</li>
                                    <li><strong>Thêm số liệu cụ thể:</strong> "Tăng 15%" thay vì "tăng nhiều"</li>
                                    <li><strong>Kể câu chuyện thực tế:</strong> Chia sẻ kinh nghiệm cá nhân</li>
                                </ul>
                            </section>

                            <section id="section-images" class="mb-5">
                                <h3 class="text-success mb-3">
                                    <i class="3-circle-fill me-2"></i>
                                    Sử Dụng Hình Ảnh Hiệu Quả
                                </h3>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>📸 Loại hình ảnh nên dùng:</h5>
                                        <ul>
                                            <li>Bản vẽ kỹ thuật, sơ đồ</li>
                                            <li>Ảnh chụp công trình thực tế</li>
                                            <li>Screenshots phần mềm</li>
                                            <li>Infographic, biểu đồ</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>⚙️ Yêu cầu kỹ thuật:</h5>
                                        <ul>
                                            <li>Định dạng: JPG, PNG, WebP</li>
                                            <li>Kích thước: Tối đa 2MB</li>
                                            <li>Độ phân giải: Tối thiểu 800px chiều rộng</li>
                                            <li>Chất lượng: Rõ nét, không mờ</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <strong>⚠️ Lưu ý bản quyền:</strong> Chỉ sử dụng hình ảnh do bạn chụp hoặc có quyền
                                    sử dụng. Ghi rõ nguồn nếu sử dụng hình từ internet.
                                </div>
                            </section>

                            <section id="section-category" class="mb-5">
                                <h3 class="text-success mb-3">
                                    <i class="4-circle-fill me-2"></i>
                                    Chọn Đúng Danh Mục
                                </h3>

                                <div class="row">
                                    @php
                                    $categories = [
                                    ['name' => 'Kiến Trúc & Thiết Kế', 'desc' => 'Thiết kế nhà, nội thất, cảnh quan',
                                    'icon' => 'building'],
                                    ['name' => 'Kết Cấu & Xây Dựng', 'desc' => 'Tính toán kết cấu, vật liệu xây dựng',
                                    'icon' => 'hammer'],
                                    ['name' => 'Giao Thông & Hạ Tầng', 'desc' => 'Đường bộ, cầu đường, quy hoạch',
                                    'icon' => 'signpost'],
                                    ['name' => 'Phần Mềm & Công Nghệ', 'desc' => 'AutoCAD, Revit, BIM, GIS', 'icon' =>
                                    'laptop'],
                                    ];
                                    @endphp

                                    @foreach($categories as $category)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="{{ $category['icon'] }} me-2 text-primary"></i>
                                                    {{ $category['name'] }}
                                                </h6>
                                                <p class="card-text small text-muted">{{ $category['desc'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>

                            <section id="section-tips" class="mb-5">
                                <h3 class="text-success mb-3">
                                    <i class="5-circle-fill me-2"></i>
                                    Mẹo Bổ Sung Để Bài Viết Nổi Bật
                                </h3>

                                <div class="accordion" id="tipsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#tip1">
                                                🚀 Tăng tương tác
                                            </button>
                                        </h2>
                                        <div id="tip1" class="accordion-collapse collapse show"
                                            data-bs-parent="#tipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Kết thúc bài viết bằng câu hỏi mở</li>
                                                    <li>Yêu cầu ý kiến từ cộng đồng</li>
                                                    <li>Trả lời comment một cách tận tình</li>
                                                    <li>Cập nhật bài viết khi có thông tin mới</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#tip2">
                                                📈 SEO và tìm kiếm
                                            </button>
                                        </h2>
                                        <div id="tip2" class="accordion-collapse collapse"
                                            data-bs-parent="#tipsAccordion">
                                            <div class="accordion-body">
                                                <ul>
                                                    <li>Sử dụng từ khóa chuyên ngành</li>
                                                    <li>Viết mô tả ngắn gọn, súc tích</li>
                                                    <li>Thêm tags liên quan</li>
                                                    <li>Liên kết đến bài viết khác trong cùng chủ đề</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#tip3">
                                                🎯 Tránh những lỗi phổ biến
                                            </button>
                                        </h2>
                                        <div id="tip3" class="accordion-collapse collapse"
                                            data-bs-parent="#tipsAccordion">
                                            <div class="accordion-body">
                                                <ul class="text-danger">
                                                    <li>Đăng bài không đúng chuyên mục</li>
                                                    <li>Copy-paste từ nguồn khác mà không ghi nguồn</li>
                                                    <li>Sử dụng quá nhiều từ viết tắt</li>
                                                    <li>Không kiểm tra chính tả trước khi đăng</li>
                                                    <li>Bài viết quá ngắn, thiếu thông tin</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <div class="text-center mt-5">
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle me-2"></i>Sẵn sàng viết bài?</h5>
                                    <p class="mb-3">Áp dụng những hướng dẫn trên và tạo ra những bài viết chất lượng!
                                    </p>
                                    <a href="{{ route('threads.create') }}" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Tạo Bài Viết Ngay
                                    </a>
                                </div>
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
    .list-group-item.active {
        background-color: var(--bs-success);
        border-color: var(--bs-success);
    }

    .card h3 {
        border-left: 4px solid var(--bs-success);
        padding-left: 1rem;
        margin-left: -1rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(25, 135, 84, 0.1);
        color: var(--bs-success);
    }

    section {
        scroll-margin-top: 80px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Smooth scroll cho navigation
document.querySelectorAll('.list-group-item').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }

        // Update active state
        document.querySelectorAll('.list-group-item').forEach(item => item.classList.remove('active'));
        this.classList.add('active');
    });
});

// Auto update active navigation on scroll
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.list-group-item');

    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (scrollY >= sectionTop) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});
</script>
@endpush