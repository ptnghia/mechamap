@extends('emails.layouts.base')

@section('title', 'Chào mừng đến với MechaMap!')

@section('content')
<div class="greeting">
    Chào mừng {{ $user->name }}! 🎉
</div>

<div class="message">
    Tuyệt vời! Bạn đã xác minh email thành công và chính thức trở thành thành viên của <strong>MechaMap</strong> - cộng đồng kỹ thuật cơ khí hàng đầu Việt Nam.
</div>

<div class="info-box success">
    <h4>✅ Tài khoản đã được kích hoạt</h4>
    <p>
        Bạn có thể bắt đầu khám phá tất cả các tính năng của MechaMap ngay bây giờ!
    </p>
</div>

<div class="btn-container">
    <a href="{{ config('app.url') }}/dashboard" class="btn btn-success">
        🚀 Khám phá MechaMap
    </a>
</div>

<div class="features">
    <div class="feature">
        <div class="feature-icon">🏠</div>
        <div class="feature-title">Dashboard</div>
        <div class="feature-desc">Quản lý hoạt động cá nhân</div>
    </div>
    <div class="feature">
        <div class="feature-icon">👥</div>
        <div class="feature-title">Community</div>
        <div class="feature-desc">Tham gia thảo luận kỹ thuật</div>
    </div>
    <div class="feature">
        <div class="feature-icon">🏆</div>
        <div class="feature-title">Showcase</div>
        <div class="feature-desc">Chia sẻ dự án của bạn</div>
    </div>
</div>

@if($user->role === 'manufacturer' || $user->role === 'supplier' || $user->role === 'brand')
<div class="info-box">
    <h4>🏢 Tài khoản doanh nghiệp</h4>
    <p>
        Với tư cách là <strong>{{ ucfirst($user->role) }}</strong>, bạn có quyền truy cập đặc biệt vào:
    </p>
    <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Đăng bán sản phẩm trên Marketplace</li>
        <li>Quản lý business profile chuyên nghiệp</li>
        <li>Truy cập analytics và insights</li>
        <li>Kết nối với đối tác kinh doanh</li>
    </ul>
</div>

<div class="btn-container">
    <a href="{{ config('app.url') }}/marketplace/seller/dashboard" class="btn btn-warning">
        💼 Quản lý Business
    </a>
</div>
@endif

<div class="message">
    <strong>Bước tiếp theo bạn có thể thực hiện:</strong>
    <ol style="margin: 15px 0; padding-left: 20px;">
        <li><strong>Hoàn thiện profile:</strong> Thêm ảnh đại diện và thông tin cá nhân</li>
        <li><strong>Khám phá cộng đồng:</strong> Tham gia các forum và thảo luận</li>
        <li><strong>Kết nối:</strong> Theo dõi các kỹ sư và chuyên gia khác</li>
        <li><strong>Chia sẻ:</strong> Đăng showcase về dự án của bạn</li>
        @if($user->role !== 'guest')
        <li><strong>Mua sắm:</strong> Khám phá marketplace với hàng ngàn sản phẩm</li>
        @endif
    </ol>
</div>

<div class="info-box">
    <h4>💡 Mẹo sử dụng MechaMap hiệu quả</h4>
    <p>
        • Sử dụng tính năng tìm kiếm để nhanh chóng tìm thông tin<br>
        • Tham gia các group chuyên đề phù hợp với lĩnh vực của bạn<br>
        • Đặt câu hỏi chi tiết để nhận được câu trả lời tốt nhất<br>
        • Chia sẻ kinh nghiệm để xây dựng uy tín trong cộng đồng
    </p>
</div>

<div class="message">
    Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi qua email 
    <a href="mailto:support@mechamap.com" style="color: #3498db;">support@mechamap.com</a> 
    hoặc tham gia group hỗ trợ trong cộng đồng.
</div>

<div class="message" style="margin-top: 30px; font-style: italic; color: #666;">
    Chúc bạn có những trải nghiệm tuyệt vời tại MechaMap!<br>
    <strong>Đội ngũ MechaMap</strong>
</div>
@endsection
