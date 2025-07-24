@extends('emails.layout')

@section('title', 'Đăng nhập từ thiết bị mới')

@section('content')
<div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
    <h2 style="color: #856404; margin: 0 0 10px 0; font-size: 24px;">
        <i class="fas fa-shield-alt" style="color: #ffc107; margin-right: 8px;"></i>
        Cảnh báo bảo mật: Đăng nhập từ thiết bị mới
    </h2>
    <p style="color: #856404; margin: 0; font-size: 14px;">
        Tài khoản của bạn đã được đăng nhập từ một thiết bị mới
    </p>
</div>

<div style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
    <h3 style="color: #495057; margin: 0 0 15px 0; font-size: 20px;">
        Chi tiết đăng nhập
    </h3>
    
    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500; width: 30%;">
                    <i class="fas fa-laptop" style="margin-right: 8px; color: #007bff;"></i>
                    Thiết bị:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $device->display_name }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #28a745;"></i>
                    Vị trí:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $device->location }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">
                    <i class="fas fa-network-wired" style="margin-right: 8px; color: #6f42c1;"></i>
                    IP Address:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $device->ip_address }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #6c757d; font-weight: 500;">
                    <i class="fas fa-clock" style="margin-right: 8px; color: #fd7e14;"></i>
                    Thời gian:
                </td>
                <td style="padding: 8px 0; color: #495057;">
                    {{ $device->first_seen_at->format('d/m/Y H:i:s') }}
                </td>
            </tr>
        </table>
    </div>

    <div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff;">
        <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">
            <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
            Đây có phải là bạn không?
        </h4>
        <p style="margin: 0; color: #495057; font-size: 14px; line-height: 1.5;">
            Nếu đây là bạn, bạn có thể bỏ qua email này. Thiết bị này sẽ được ghi nhớ cho các lần đăng nhập tiếp theo.
        </p>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('profile.security') }}" 
       style="background-color: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; display: inline-block; margin-right: 10px;">
        <i class="fas fa-check" style="margin-right: 8px;"></i>
        Đây là tôi
    </a>
    
    <a href="{{ route('profile.security') }}" 
       style="background-color: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; display: inline-block;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
        Không phải tôi
    </a>
</div>

<div style="background-color: #f8d7da; padding: 15px; border-radius: 6px; margin-top: 20px; border: 1px solid #f5c6cb;">
    <h4 style="color: #721c24; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
        Nếu đây không phải là bạn
    </h4>
    <p style="margin: 0 0 10px 0; color: #721c24; font-size: 14px; line-height: 1.5;">
        Tài khoản của bạn có thể đã bị xâm phạm. Hãy thực hiện ngay các bước sau:
    </p>
    <ul style="margin: 0; padding-left: 20px; color: #721c24; font-size: 14px;">
        <li>Đổi mật khẩu ngay lập tức</li>
        <li>Kiểm tra và cập nhật thông tin bảo mật</li>
        <li>Đăng xuất khỏi tất cả thiết bị</li>
        <li>Liên hệ hỗ trợ nếu cần thiết</li>
    </ul>
</div>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <p style="margin: 0; color: #6c757d; font-size: 13px; text-align: center;">
        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
        Email này được gửi tự động để bảo vệ tài khoản của bạn.
    </p>
    <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 13px; text-align: center;">
        <a href="{{ route('profile.security') }}" style="color: #007bff; text-decoration: none;">
            Quản lý thiết bị đáng tin cậy
        </a> | 
        <a href="{{ route('profile.notifications') }}" style="color: #007bff; text-decoration: none;">
            Cài đặt thông báo
        </a>
    </p>
</div>

<div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; margin-top: 15px; border-left: 4px solid #007bff;">
    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
        Mẹo bảo mật
    </h4>
    <p style="margin: 0; color: #495057; font-size: 13px; line-height: 1.5;">
        Để tăng cường bảo mật, hãy sử dụng mật khẩu mạnh, bật xác thực hai yếu tố, và chỉ đăng nhập từ các thiết bị đáng tin cậy.
    </p>
</div>
@endsection
