@extends('layouts.app')

@section('title', 'Liên Hệ Hỗ Trợ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary">
                    <i class="headset me-3"></i>
                    Liên Hệ Hỗ Trợ
                </h1>
                <p class="lead text-muted">
                    Chúng tôi luôn sẵn sàng hỗ trợ bạn. Hãy liên hệ với chúng tôi qua các kênh dưới đây.
                </p>
            </div>

            <!-- Contact Methods -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3">
                                <i class="fas fa-envelope-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="card-title">Email Hỗ Trợ</h5>
                            <p class="card-text text-muted">
                                Gửi email cho chúng tôi, chúng tôi sẽ phản hồi trong vòng 24 giờ.
                            </p>
                            <a href="mailto:support@mechamap.com" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>
                                support@mechamap.com
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="text-success mb-3">
                                <i class="fas fa-comment-dots-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="card-title">Diễn Đàn Cộng Đồng</h5>
                            <p class="card-text text-muted">
                                Tham gia thảo luận với cộng đồng và được hỗ trợ từ các thành viên khác.
                            </p>
                            <a href="{{ route('forums.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-users me-2"></i>
                                Tham Gia Diễn Đàn
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="question-circle me-2"></i>
                        Câu Hỏi Thường Gặp
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    Làm thế nào để tạo chủ đề mới?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Để tạo chủ đề mới, bạn cần:</p>
                                    <ol>
                                        <li>Đăng nhập vào tài khoản của bạn</li>
                                        <li>Chọn "Tạo Chủ Đề Mới" trên menu</li>
                                        <li>Điền đầy đủ thông tin theo các bước hướng dẫn</li>
                                        <li>Xem lại và đăng bài</li>
                                    </ol>
                                    <a href="{{ route('help.writing-guide') }}" class="btn btn-sm btn-outline-primary">
                                        Xem hướng dẫn chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    Làm thế nào để tải lên hình ảnh?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Bạn có thể tải lên hình ảnh bằng cách:</p>
                                    <ul>
                                        <li>Kéo và thả file vào khu vực tải lên</li>
                                        <li>Hoặc nhấn vào nút "Chọn File" để chọn từ máy tính</li>
                                        <li>Hỗ trợ định dạng: JPG, PNG, GIF (tối đa 5MB)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    Tôi quên mật khẩu, làm sao để lấy lại?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Để lấy lại mật khẩu:</p>
                                    <ol>
                                        <li>Nhấn vào "Quên mật khẩu?" ở trang đăng nhập</li>
                                        <li>Nhập email đã đăng ký</li>
                                        <li>Kiểm tra email và làm theo hướng dẫn</li>
                                        <li>Tạo mật khẩu mới</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-paper-plane me-2"></i>
                        Gửi Tin Nhắn Cho Chúng Tôi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Họ và Tên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Chủ Đề <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Chọn chủ đề...</option>
                                    <option value="technical">Hỗ trợ kỹ thuật</option>
                                    <option value="account">Vấn đề tài khoản</option>
                                    <option value="suggestion">Góp ý, đề xuất</option>
                                    <option value="report">Báo cáo vi phạm</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Nội Dung <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5"
                                    placeholder="Mô tả chi tiết vấn đề của bạn..." required></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree" name="agree" required>
                                    <label class="form-check-label" for="agree">
                                        Tôi đồng ý với <a href="{{ route('rules') }}">quy tắc cộng đồng</a> và
                                        chính sách bảo mật của website.
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Gửi Tin Nhắn
                                </button>
                                <button type="reset" class="btn btn-outline-secondary ms-2">
                                    <i class="arrow-clockwise me-2"></i>
                                    Làm Lại
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: var(--bs-primary);
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .btn {
        transition: all 0.3s ease;
    }
</style>
@endsection