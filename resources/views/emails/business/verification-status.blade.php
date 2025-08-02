@extends('emails.layouts.base')

@section('title', 'Cập nhật trạng thái xác minh doanh nghiệp - MechaMap')

@section('content')
<div class="greeting">
    Xin chào {{ $user->name }}! 🏢
</div>

@if($status === 'approved')
<div class="info-box success">
    <h4>🎉 Xác minh doanh nghiệp thành công!</h4>
    <p>
        Chúc mừng! Tài khoản doanh nghiệp của bạn đã được xác minh và phê duyệt thành công.
    </p>
</div>

<div class="message">
    <strong>{{ $businessInfo['company_name'] }}</strong> hiện đã được xác minh chính thức trên MechaMap với tư cách là <strong>{{ ucfirst($user->role) }}</strong>.
</div>

<div class="btn-container">
    <a href="{{ config('app.url') }}/marketplace/seller/dashboard" class="btn btn-success">
        🚀 Bắt đầu bán hàng
    </a>
</div>

<div class="features">
    <div class="feature">
        <div class="feature-icon">✅</div>
        <div class="feature-title">Verified Badge</div>
        <div class="feature-desc">Hiển thị trên profile</div>
    </div>
    <div class="feature">
        <div class="feature-icon">🛒</div>
        <div class="feature-title">Marketplace</div>
        <div class="feature-desc">Đăng bán sản phẩm</div>
    </div>
    <div class="feature">
        <div class="feature-icon">📊</div>
        <div class="feature-title">Analytics</div>
        <div class="feature-desc">Theo dõi hiệu suất</div>
    </div>
</div>

<div class="message">
    <strong>Quyền lợi của tài khoản đã xác minh:</strong>
    <ul style="margin: 15px 0; padding-left: 20px;">
        <li>Đăng bán sản phẩm không giới hạn trên Marketplace</li>
        <li>Hiển thị badge "Verified" trên profile và sản phẩm</li>
        <li>Truy cập dashboard quản lý doanh nghiệp chuyên nghiệp</li>
        <li>Nhận analytics chi tiết về hiệu suất bán hàng</li>
        <li>Ưu tiên hiển thị trong kết quả tìm kiếm</li>
        <li>Hỗ trợ khách hàng ưu tiên</li>
    </ul>
</div>

@elseif($status === 'rejected')
<div class="info-box warning">
    <h4>❌ Xác minh doanh nghiệp không thành công</h4>
    <p>
        Rất tiếc, yêu cầu xác minh doanh nghiệp của bạn chưa được phê duyệt.
    </p>
</div>

<div class="message">
    <strong>Lý do từ chối:</strong><br>
    {{ $rejectionReason ?? 'Thông tin doanh nghiệp chưa đầy đủ hoặc không chính xác.' }}
</div>

<div class="btn-container">
    <a href="{{ config('app.url') }}/profile/business" class="btn btn-warning">
        📝 Cập nhật thông tin
    </a>
</div>

<div class="message">
    <strong>Để được xác minh thành công, vui lòng:</strong>
    <ul style="margin: 15px 0; padding-left: 20px;">
        <li>Kiểm tra và cập nhật thông tin doanh nghiệp chính xác</li>
        <li>Đảm bảo giấy phép kinh doanh còn hiệu lực</li>
        <li>Cung cấp đầy đủ tài liệu xác minh</li>
        <li>Sử dụng email doanh nghiệp chính thức</li>
    </ul>
</div>

@else
<div class="info-box">
    <h4>⏳ Đang xử lý xác minh doanh nghiệp</h4>
    <p>
        Cảm ơn bạn đã gửi thông tin xác minh doanh nghiệp. Chúng tôi đang xem xét hồ sơ của bạn.
    </p>
</div>

<div class="message">
    Thông tin doanh nghiệp của <strong>{{ $businessInfo['company_name'] }}</strong> đang được đội ngũ MechaMap xem xét và xác minh.
</div>

<div class="message">
    <strong>Thời gian xử lý dự kiến:</strong> 1-3 ngày làm việc<br>
    <strong>Trạng thái hiện tại:</strong> Đang xem xét tài liệu
</div>
@endif

<div class="info-box">
    <h4>📋 Thông tin doanh nghiệp</h4>
    <p>
        <strong>Tên công ty:</strong> {{ $businessInfo['company_name'] }}<br>
        <strong>Mã số thuế:</strong> {{ $businessInfo['tax_code'] }}<br>
        <strong>Loại hình:</strong> {{ ucfirst($user->role) }}<br>
        <strong>Ngày đăng ký:</strong> {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
    </p>
</div>

<div class="message" style="font-size: 14px; color: #666; margin-top: 30px;">
    Nếu bạn có câu hỏi về quá trình xác minh, vui lòng liên hệ với chúng tôi qua email
    <a href="mailto:business@mechamap.com" style="color: #3498db;">business@mechamap.com</a>
</div>
@endsection
