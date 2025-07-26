@extends('emails.layouts.base')

@section('title', 'Cảnh báo bảo mật - MechaMap')

@section('content')
<!-- Header -->
<tr>
    <td style="padding: 40px 30px; text-align: center; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
        <h1 style="color: #ffffff; font-size: 28px; margin: 0; font-weight: 600;">
            🔐 Cảnh báo bảo mật
        </h1>
        <p style="color: #ffffff; font-size: 16px; margin: 10px 0 0 0; opacity: 0.9;">
            Chúng tôi phát hiện hoạt động bảo mật trên tài khoản của bạn
        </p>
    </td>
</tr>

<!-- Alert Content -->
<tr>
    <td style="padding: 40px 30px;">
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
            <h2 style="color: #856404; font-size: 18px; margin: 0 0 10px 0;">
                ⚠️ 
                @switch($alertType)
                    @case('login_from_new_device')
                        Đăng nhập từ thiết bị mới
                        @break
                    @case('password_changed')
                        Mật khẩu đã được thay đổi
                        @break
                    @case('email_changed')
                        Email đã được thay đổi
                        @break
                    @case('suspicious_activity')
                        Phát hiện hoạt động đáng ngờ
                        @break
                    @case('account_locked')
                        Tài khoản đã bị khóa
                        @break
                    @case('failed_login_attempts')
                        Nhiều lần đăng nhập thất bại
                        @break
                    @default
                        Cảnh báo bảo mật
                @endswitch
            </h2>
            
            <p style="color: #856404; margin: 0; font-size: 14px;">
                @switch($alertType)
                    @case('login_from_new_device')
                        Tài khoản của bạn đã được đăng nhập từ một thiết bị hoặc vị trí mới.
                        @break
                    @case('password_changed')
                        Mật khẩu tài khoản của bạn đã được thay đổi thành công.
                        @break
                    @case('email_changed')
                        Địa chỉ email tài khoản của bạn đã được thay đổi.
                        @break
                    @case('suspicious_activity')
                        Chúng tôi phát hiện hoạt động bất thường trên tài khoản của bạn.
                        @break
                    @case('account_locked')
                        Tài khoản của bạn đã bị khóa tạm thời do vi phạm chính sách bảo mật.
                        @break
                    @case('failed_login_attempts')
                        Có nhiều lần thử đăng nhập thất bại vào tài khoản của bạn.
                        @break
                    @default
                        Chúng tôi phát hiện hoạt động bảo mật trên tài khoản của bạn.
                @endswitch
            </p>
        </div>

        <!-- User Info -->
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
            <h3 style="color: #333333; font-size: 16px; margin: 0 0 15px 0;">
                👤 Thông tin tài khoản
            </h3>
            
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 5px 0; color: #666666; width: 120px;">Tên:</td>
                    <td style="padding: 5px 0; color: #333333; font-weight: 500;">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666666;">Email:</td>
                    <td style="padding: 5px 0; color: #333333; font-weight: 500;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666666;">Username:</td>
                    <td style="padding: 5px 0; color: #333333; font-weight: 500;">{{ $user->username }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666666;">Thời gian:</td>
                    <td style="padding: 5px 0; color: #333333; font-weight: 500;">{{ now()->format('d/m/Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <!-- Technical Details -->
        @if($ipAddress || $userAgent)
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
            <h3 style="color: #333333; font-size: 16px; margin: 0 0 15px 0;">
                🔍 Chi tiết kỹ thuật
            </h3>
            
            <table style="width: 100%; font-size: 14px;">
                @if($ipAddress)
                <tr>
                    <td style="padding: 5px 0; color: #666666; width: 120px;">Địa chỉ IP:</td>
                    <td style="padding: 5px 0; color: #333333; font-family: monospace;">{{ $ipAddress }}</td>
                </tr>
                @endif
                
                @if($userAgent)
                <tr>
                    <td style="padding: 5px 0; color: #666666; vertical-align: top;">Trình duyệt:</td>
                    <td style="padding: 5px 0; color: #333333; font-family: monospace; word-break: break-all;">{{ $userAgent }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Alert Data -->
        @if(!empty($alertData))
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
            <h3 style="color: #333333; font-size: 16px; margin: 0 0 15px 0;">
                📋 Thông tin bổ sung
            </h3>
            
            <table style="width: 100%; font-size: 14px;">
                @foreach($alertData as $key => $value)
                <tr>
                    <td style="padding: 5px 0; color: #666666; width: 120px;">{{ ucfirst(str_replace('_', ' ', $key)) }}:</td>
                    <td style="padding: 5px 0; color: #333333;">{{ is_array($value) ? json_encode($value) : $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <!-- Action Required -->
        <div style="border: 2px solid #dc3545; border-radius: 8px; padding: 25px; text-align: center;">
            <h3 style="color: #dc3545; font-size: 18px; margin: 0 0 15px 0;">
                🚨 Hành động cần thiết
            </h3>
            
            @switch($alertType)
                @case('login_from_new_device')
                    <p style="color: #333333; margin: 0 0 20px 0;">
                        Nếu đây không phải là bạn, vui lòng thay đổi mật khẩu ngay lập tức và kiểm tra hoạt động tài khoản.
                    </p>
                    @break
                @case('password_changed')
                    <p style="color: #333333; margin: 0 0 20px 0;">
                        Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ với chúng tôi ngay lập tức.
                    </p>
                    @break
                @case('suspicious_activity')
                    <p style="color: #333333; margin: 0 0 20px 0;">
                        Vui lòng kiểm tra hoạt động tài khoản và thay đổi mật khẩu nếu cần thiết.
                    </p>
                    @break
                @case('account_locked')
                    <p style="color: #333333; margin: 0 0 20px 0;">
                        Vui lòng liên hệ với bộ phận hỗ trợ để mở khóa tài khoản.
                    </p>
                    @break
                @default
                    <p style="color: #333333; margin: 0 0 20px 0;">
                        Vui lòng kiểm tra tài khoản của bạn và liên hệ với chúng tôi nếu có bất kỳ vấn đề gì.
                    </p>
            @endswitch
            
            <div style="margin-top: 20px;">
                <a href="{{ url('/login') }}" style="display: inline-block; margin: 0 10px 10px 0; padding: 12px 25px; background-color: #dc3545; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: 600;">
                    Đăng nhập ngay
                </a>
                
                <a href="{{ url('/password/reset') }}" style="display: inline-block; margin: 0 10px 10px 0; padding: 12px 25px; background-color: #6c757d; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: 600;">
                    Đổi mật khẩu
                </a>
            </div>
        </div>
    </td>
</tr>

<!-- Security Tips -->
<tr>
    <td style="padding: 0 30px 40px 30px;">
        <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 20px;">
            <h3 style="color: #0c5460; font-size: 16px; margin: 0 0 15px 0;">
                💡 Mẹo bảo mật
            </h3>
            
            <ul style="color: #0c5460; margin: 0; padding-left: 20px; font-size: 14px;">
                <li style="margin-bottom: 8px;">Sử dụng mật khẩu mạnh và duy nhất cho mỗi tài khoản</li>
                <li style="margin-bottom: 8px;">Bật xác thực hai yếu tố (2FA) nếu có thể</li>
                <li style="margin-bottom: 8px;">Không chia sẻ thông tin đăng nhập với người khác</li>
                <li style="margin-bottom: 8px;">Đăng xuất khỏi thiết bị công cộng sau khi sử dụng</li>
                <li style="margin-bottom: 0;">Cập nhật thường xuyên trình duyệt và hệ điều hành</li>
            </ul>
        </div>
    </td>
</tr>
@endsection
