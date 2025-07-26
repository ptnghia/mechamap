@extends('emails.layouts.base')

@section('title', 'Đặt lại mật khẩu - MechaMap')

@section('content')
<div class="greeting">
    Xin chào {{ $user->name }}! 🔐
</div>

<div class="message">
    Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản MechaMap của bạn. 
    Nếu bạn đã yêu cầu điều này, vui lòng nhấp vào nút bên dưới để tạo mật khẩu mới:
</div>

<div class="btn-container">
    <a href="{{ $resetUrl }}" class="btn">
        🔑 Đặt lại mật khẩu
    </a>
</div>

<div class="info-box warning">
    <h4>⏰ Thời hạn có hiệu lực</h4>
    <p>
        Link đặt lại mật khẩu này sẽ hết hạn sau <strong>60 phút</strong> kể từ khi được gửi.
        Nếu bạn không sử dụng trong thời gian này, vui lòng yêu cầu đặt lại mật khẩu mới.
    </p>
</div>

<div class="message">
    <strong>Vì lý do bảo mật, vui lòng:</strong>
    <ul style="margin: 15px 0; padding-left: 20px;">
        <li>Chọn mật khẩu mạnh với ít nhất 8 ký tự</li>
        <li>Sử dụng kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
        <li>Không sử dụng thông tin cá nhân dễ đoán</li>
        <li>Không chia sẻ mật khẩu với bất kỳ ai</li>
    </ul>
</div>

<div class="info-box">
    <h4>🛡️ Bảo mật tài khoản</h4>
    <p>
        Sau khi đặt lại mật khẩu thành công, chúng tôi khuyến nghị bạn:
        <br>• Đăng xuất khỏi tất cả thiết bị khác
        <br>• Kiểm tra hoạt động đăng nhập gần đây
        <br>• Cập nhật mật khẩu cho các ứng dụng liên kết (nếu có)
    </p>
</div>

<div class="message" style="font-size: 14px; color: #666; margin-top: 30px;">
    Nếu bạn không thể nhấp vào nút trên, hãy sao chép và dán link sau vào trình duyệt:
    <br>
    <a href="{{ $resetUrl }}" style="color: #3498db; word-break: break-all;">{{ $resetUrl }}</a>
</div>

<div class="info-box warning">
    <h4>🚨 Không phải bạn yêu cầu?</h4>
    <p>
        Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng:
        <br>• Bỏ qua email này - tài khoản của bạn vẫn an toàn
        <br>• Kiểm tra bảo mật tài khoản và thay đổi mật khẩu nếu cần
        <br>• Liên hệ hỗ trợ nếu bạn nghi ngờ có hoạt động đáng ngờ
    </p>
</div>

<div class="message" style="font-size: 14px; color: #666; margin-top: 20px;">
    Nếu bạn cần hỗ trợ, vui lòng liên hệ với chúng tôi qua email 
    <a href="mailto:support@mechamap.com" style="color: #3498db;">support@mechamap.com</a>
</div>
@endsection
