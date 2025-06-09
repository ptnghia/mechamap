@extends('layouts.app')

@section('title', 'Quy Tắc Cộng Đồng')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Quy Tắc Cộng Đồng {{ config('app.name') }}
                    </h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Chào mừng bạn đến với cộng đồng!</strong>
                        Để duy trì một môi trường thân thiện và chuyên nghiệp, vui lòng tuân thủ các quy tắc sau đây.
                    </div>

                    <div class="rules-content">
                        <h4 class="text-primary mb-3">
                            <i class="bi bi-1-circle-fill me-2"></i>
                            Tôn Trọng Và Lịch Sự
                        </h4>
                        <ul class="list-unstyled ms-4 mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Tôn trọng ý kiến và
                                quan điểm của thành viên khác</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sử dụng ngôn ngữ lịch
                                sự, tránh từ ngữ thô tục</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Không công kích cá nhân
                                hoặc phân biệt đối xử</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Chấp nhận sự khác biệt
                                trong quan điểm chuyên môn</li>
                        </ul>

                        <h4 class="text-primary mb-3">
                            <i class="bi bi-2-circle-fill me-2"></i>
                            Nội Dung Chất Lượng
                        </h4>
                        <ul class="list-unstyled ms-4 mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Đăng bài vào đúng
                                chuyên mục phù hợp</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sử dụng tiêu đề mô tả
                                rõ ràng nội dung</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Cung cấp thông tin đầy
                                đủ, chính xác</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Kiểm tra chính tả và
                                ngữ pháp trước khi đăng</li>
                        </ul>

                        <h4 class="text-primary mb-3">
                            <i class="bi bi-3-circle-fill me-2"></i>
                            Cấm Spam Và Quảng Cáo
                        </h4>
                        <ul class="list-unstyled ms-4 mb-4">
                            <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Không spam hoặc đăng nội
                                dung lặp lại</li>
                            <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Không quảng cáo sản
                                phẩm/dịch vụ không liên quan</li>
                            <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Không đăng link rút gọn hoặc
                                link đáng nghi</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Chia sẻ kinh nghiệm
                                thực tế được khuyến khích</li>
                        </ul>

                        <h4 class="text-primary mb-3">
                            <i class="bi bi-4-circle-fill me-2"></i>
                            Bảo Mật Thông Tin
                        </h4>
                        <ul class="list-unstyled ms-4 mb-4">
                            <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Không chia sẻ thông tin cá
                                nhân (số điện thoại, địa chỉ nhà)</li>
                            <li class="mb-2"><i class="bi bi-x-circle text-danger me-2"></i>Không yêu cầu thông tin cá
                                nhân từ thành viên khác</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Sử dụng tin nhắn riêng
                                cho thông tin nhạy cảm</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Báo cáo các hành vi
                                đáng nghi cho admin</li>
                        </ul>

                        <h4 class="text-primary mb-3">
                            <i class="bi bi-5-circle-fill me-2"></i>
                            Quyền Sở Hữu Trí Tuệ
                        </h4>
                        <ul class="list-unstyled ms-4 mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Ghi rõ nguồn khi chia
                                sẻ nội dung từ nguồn khác</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Không sao chép nguyên
                                văn mà không có sự cho phép</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Tôn trọng bản quyền
                                hình ảnh và tài liệu</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Ưu tiên chia sẻ kinh
                                nghiệm và kiến thức cá nhân</li>
                        </ul>

                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Hậu Quả Vi Phạm
                            </h5>
                            <ul class="mb-0">
                                <li>Vi phạm lần đầu: <strong>Nhắc nhở và cảnh báo</strong></li>
                                <li>Vi phạm lần hai: <strong>Hạn chế quyền đăng bài 7 ngày</strong></li>
                                <li>Vi phạm nghiêm trọng: <strong>Khóa tài khoản vĩnh viễn</strong></li>
                            </ul>
                        </div>

                        <div class="alert alert-success">
                            <h5 class="alert-heading">
                                <i class="bi bi-trophy me-2"></i>
                                Thành Viên Xuất Sắc
                            </h5>
                            <p>Những thành viên tuân thủ quy tắc và đóng góp tích cực sẽ được:</p>
                            <ul class="mb-0">
                                <li>Cấp badge và danh hiệu đặc biệt</li>
                                <li>Quyền ưu tiên trong các sự kiện</li>
                                <li>Cơ hội trở thành moderator</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="text-center">
                            <h5 class="text-primary mb-3">Cần Hỗ Trợ?</h5>
                            <p class="text-muted mb-3">
                                Nếu bạn có thắc mắc về quy tắc hoặc cần báo cáo vi phạm, vui lòng liên hệ:
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="mailto:admin@{{ config('app.domain', 'example.com') }}"
                                    class="btn btn-outline-primary">
                                    <i class="bi bi-envelope me-2"></i>Email Admin
                                </a>
                                <a href="{{ route('contact.support') ?? '#' }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-chat-dots me-2"></i>Hỗ Trợ Trực Tuyến
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        Cập nhật lần cuối: {{ now()->format('d/m/Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .rules-content h4 {
        border-left: 4px solid var(--bs-primary);
        padding-left: 1rem;
        margin-left: -1rem;
    }

    .rules-content ul li {
        transition: all 0.2s ease;
    }

    .rules-content ul li:hover {
        padding-left: 0.5rem;
        background: rgba(0, 123, 255, 0.05);
        border-radius: 4px;
    }
</style>
@endpush