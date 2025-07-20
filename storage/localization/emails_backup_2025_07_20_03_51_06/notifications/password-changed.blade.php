@extends('emails.layout')

@section('title', 'Mật khẩu đã được thay đổi')

@section('content')
<div style="background-color: #f8d7da; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
    <h2 style="color: #721c24; margin: 0 0 10px 0; font-size: 24px;">
        <i class="fas fa-key" style="color: #dc3545; margin-right: 8px;"></i>
        Mật khẩu đã được thay đổi
    </h2>
    <p style="color: #721c24; margin: 0; font-size: 14px;">
        Mật khẩu tài khoản của bạn đã được thay đổi thành công
    </p>
</div>

<div style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
    <h3 style="color: #495057; margin: 0 0 15px 0; font-size: 20px;">
        Chi tiết thay đổi
    </h3>
    
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500; width: 30%;">
                    <i class="fas fa-user" style="margin-right: 8px; color: #007bff;"></i>
                    Tài khoản:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $recipient->email }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">
                    <i class="fas fa-clock" style="margin-right: 8px; color: #fd7e14;"></i>
                    Thời gian:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ now()->format('d/m/Y H:i:s') }}
                </td>
            </tr>
            @if(isset($data['ip_address']) && $data['ip_address'])
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">
                    <i class="fas fa-network-wired" style="margin-right: 8px; color: #6f42c1;"></i>
                    IP Address:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $data['ip_address'] }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    <div style="background-color: #d4edda; padding: 15px; border-radius: 6px; border-left: 4px solid #28a745; margin-bottom: 15px;">
        <h4 style="color: #155724; margin: 0 0 10px 0; font-size: 16px;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
            Thay đổi thành công
        </h4>
        <p style="margin: 0; color: #155724; font-size: 14px; line-height: 1.5;">
            Mật khẩu của bạn đã được cập nhật thành công. Bạn có thể sử dụng mật khẩu mới để đăng nhập.
        </p>
    </div>

    <div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff;">
        <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">
            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
            Bạn có thực hiện thay đổi này?
        </h4>
        <p style="margin: 0; color: #495057; font-size: 14px; line-height: 1.5;">
            Nếu bạn đã thực hiện thay đổi này, bạn có thể bỏ qua email này. Mật khẩu mới của bạn đã có hiệu lực.
        </p>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('profile.security') }}" 
       style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; display: inline-block;">
        <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
        Kiểm tra bảo mật tài khoản
    </a>
</div>

<div style="background-color: #f8d7da; padding: 15px; border-radius: 6px; margin-top: 20px; border: 1px solid #f5c6cb;">
    <h4 style="color: #721c24; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
        Nếu bạn không thực hiện thay đổi này
    </h4>
    <p style="margin: 0 0 10px 0; color: #721c24; font-size: 14px; line-height: 1.5;">
        Tài khoản của bạn có thể đã bị xâm phạm. Hãy thực hiện ngay các bước sau:
    </p>
    <ul style="margin: 0; padding-left: 20px; color: #721c24; font-size: 14px;">
        <li><strong>Liên hệ hỗ trợ ngay lập tức</strong> qua email: <a href="mailto:support@mechamap.com" style="color: #721c24;">support@mechamap.com</a></li>
        <li>Cung cấp thông tin chi tiết về sự việc</li>
        <li>Không sử dụng tài khoản cho đến khi được hỗ trợ</li>
        <li>Kiểm tra các tài khoản khác có cùng mật khẩu</li>
    </ul>
</div>

<div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; margin-top: 20px; border: 1px solid #ffeaa7;">
    <h4 style="color: #856404; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-clock" style="margin-right: 8px;"></i>
        Thông tin quan trọng
    </h4>
    <ul style="margin: 0; padding-left: 20px; color: #856404; font-size: 14px;">
        <li>Tất cả phiên đăng nhập hiện tại đã được đăng xuất</li>
        <li>Bạn cần đăng nhập lại với mật khẩu mới</li>
        <li>Các ứng dụng kết nối có thể cần cập nhật mật khẩu</li>
        <li>Email này được gửi để đảm bảo bảo mật tài khoản</li>
    </ul>
</div>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <p style="margin: 0; color: #6c757d; font-size: 13px; text-align: center;">
        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
        Email này được gửi tự động để thông báo về thay đổi bảo mật quan trọng.
    </p>
    <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 13px; text-align: center;">
        <a href="{{ route('profile.security') }}" style="color: #007bff; text-decoration: none;">
            Cài đặt bảo mật
        </a> | 
        <a href="mailto:support@mechamap.com" style="color: #007bff; text-decoration: none;">
            Liên hệ hỗ trợ
        </a>
    </p>
</div>

<div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; margin-top: 15px; border-left: 4px solid #007bff;">
    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
        Mẹo bảo mật mật khẩu
    </h4>
    <ul style="margin: 0; padding-left: 20px; color: #495057; font-size: 13px;">
        <li>Sử dụng mật khẩu dài ít nhất 12 ký tự</li>
        <li>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</li>
        <li>Không sử dụng thông tin cá nhân dễ đoán</li>
        <li>Sử dụng mật khẩu khác nhau cho mỗi tài khoản</li>
        <li>Cân nhắc sử dụng trình quản lý mật khẩu</li>
    </ul>
</div>
@endsection
