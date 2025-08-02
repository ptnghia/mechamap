@extends('emails.layouts.base')

@section('title', 'Thông báo duyệt sản phẩm - MechaMap')

@section('content')
<div class="greeting">
    Xin chào {{ $seller->name }}! 👋
</div>

@if($notificationData['status'] === 'approved')
<div class="info-box success">
    <h4>🎉 Sản phẩm đã được duyệt!</h4>
    <p>
        Chúc mừng! Sản phẩm <strong>"{{ $product->name }}"</strong> của bạn đã được duyệt 
        và hiện đã có mặt trên MechaMap Marketplace.
    </p>
</div>

<div class="message">
    <p>
        Khách hàng có thể tìm thấy và mua sản phẩm của bạn ngay bây giờ. 
        Sản phẩm sẽ xuất hiện trong kết quả tìm kiếm và danh mục liên quan.
    </p>
</div>

<div class="btn-container">
    <a href="{{ config('app.url') }}/marketplace/products/{{ $product->slug }}" class="btn btn-success">
        👀 Xem sản phẩm trên Marketplace
    </a>
</div>

<div class="info-box primary">
    <h4>📈 Tối ưu hóa bán hàng</h4>
    <p>Để tăng doanh số, bạn có thể:</p>
    <ul>
        <li><strong>Cập nhật hình ảnh</strong> chất lượng cao</li>
        <li><strong>Viết mô tả chi tiết</strong> về tính năng sản phẩm</li>
        <li><strong>Thiết lập khuyến mãi</strong> để thu hút khách hàng</li>
        <li><strong>Phản hồi nhanh</strong> các câu hỏi từ khách hàng</li>
    </ul>
</div>

@elseif($notificationData['status'] === 'rejected')
<div class="info-box warning">
    <h4>❌ Sản phẩm cần chỉnh sửa</h4>
    <p>
        Sản phẩm <strong>"{{ $product->name }}"</strong> của bạn chưa được duyệt 
        và cần chỉnh sửa theo yêu cầu bên dưới.
    </p>
</div>

@if(isset($notificationData['rejection_reason']))
<div class="message">
    <h5>📝 Lý do từ chối:</h5>
    <div class="rejection-reason">
        {{ $notificationData['rejection_reason'] }}
    </div>
</div>
@endif

<div class="btn-container">
    <a href="{{ config('app.url') }}/marketplace/seller/products/{{ $product->id }}/edit" class="btn btn-warning">
        ✏️ Chỉnh sửa sản phẩm
    </a>
</div>

<div class="info-box secondary">
    <h4>💡 Hướng dẫn chỉnh sửa</h4>
    <p>Vui lòng kiểm tra và cập nhật:</p>
    <ul>
        <li><strong>Thông tin sản phẩm</strong> - Đảm bảo chính xác và đầy đủ</li>
        <li><strong>Hình ảnh</strong> - Rõ nét, không có watermark</li>
        <li><strong>Giá cả</strong> - Hợp lý và cạnh tranh</li>
        <li><strong>Mô tả</strong> - Chi tiết và không vi phạm quy định</li>
    </ul>
</div>

@else
<div class="info-box primary">
    <h4>⏳ Sản phẩm đang được xem xét</h4>
    <p>
        Sản phẩm <strong>"{{ $product->name }}"</strong> của bạn đang được đội ngũ 
        MechaMap xem xét và duyệt.
    </p>
</div>

<div class="message">
    <p>
        Quá trình duyệt thường mất 1-2 ngày làm việc. Chúng tôi sẽ thông báo 
        kết quả qua email ngay khi hoàn tất.
    </p>
</div>
@endif

<div class="help-section">
    <p>
        <strong>Cần hỗ trợ?</strong> Liên hệ với đội ngũ hỗ trợ seller:
        <a href="mailto:seller-support@mechamap.com">seller-support@mechamap.com</a>
    </p>
</div>
@endsection

@section('footer-links')
<a href="{{ config('app.url') }}/marketplace/seller/dashboard">Dashboard Seller</a>
<a href="{{ config('app.url') }}/marketplace/seller/products">Quản lý sản phẩm</a>
<a href="{{ config('app.url') }}/help/seller-guide">Hướng dẫn bán hàng</a>
@endsection
