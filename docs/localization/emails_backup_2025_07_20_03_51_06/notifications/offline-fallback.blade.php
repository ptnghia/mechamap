@extends('emails.layouts.notification')

@section('title', 'Thông báo quan trọng')

@section('header')
    <div class="header-content">
        <div class="notification-icon" style="background-color: #dc3545;">
            <i class="fas fa-exclamation-triangle" style="color: white; font-size: 24px;"></i>
        </div>
        <h1 style="color: #dc3545; margin: 16px 0 8px 0; font-size: 24px; font-weight: 600;">
            Thông báo quan trọng!
        </h1>
        <p style="color: #6c757d; margin: 0; font-size: 16px;">
            Chúng tôi đã cố gắng gửi thông báo này nhiều lần
        </p>
    </div>
@endsection

@section('content')
    <div class="notification-content">
        <!-- Greeting -->
        <div class="greeting">
            <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
        </div>

        <!-- Main Message -->
        <div class="main-message">
            <p>Chúng tôi đã cố gắng gửi thông báo quan trọng này đến bạn qua hệ thống real-time nhưng không thành công. 
            Đây là email dự phòng để đảm bảo bạn nhận được thông tin:</p>
        </div>

        <!-- Original Notification -->
        <div class="original-notification" style="background: #fff; border: 2px solid #dc3545; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 600; color: #dc3545;">
                <i class="fas fa-bell" style="margin-right: 8px;"></i>
                {{ $notification->title }}
            </h4>
            
            <div style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                <p style="margin: 0; color: #495057; line-height: 1.6;">
                    {{ $notification->message }}
                </p>
            </div>

            <!-- Notification Details -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Loại thông báo</div>
                    <div style="font-size: 14px; color: #495057; font-weight: 500;">{{ $notification->type }}</div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Mức độ ưu tiên</div>
                    <div style="font-size: 14px; color: {{ $notification->priority === 'high' ? '#dc3545' : ($notification->priority === 'normal' ? '#ffc107' : '#6c757d') }}; font-weight: 500;">
                        {{ $notification->priority === 'high' ? 'Cao' : ($notification->priority === 'normal' ? 'Trung bình' : 'Thấp') }}
                    </div>
                </div>
                <div>
                    <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Thời gian tạo</div>
                    <div style="font-size: 14px; color: #495057;">{{ $notification->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- Action URL if available -->
            @if(isset($notification->data['action_url']))
                <div style="text-align: center; margin-top: 16px;">
                    <a href="{{ $notification->data['action_url'] }}" 
                       style="display: inline-block; background: #dc3545; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                        <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i>
                        Xem chi tiết
                    </a>
                </div>
            @endif
        </div>

        <!-- Delivery Information -->
        <div class="delivery-info" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #856404;">
                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                Thông tin gửi thông báo
            </h4>
            <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.5;">
                Chúng tôi đã thử gửi thông báo này qua hệ thống real-time {{ $notification->data['delivery_attempts'] ?? 'nhiều' }} lần 
                trong khoảng thời gian từ {{ $notification->created_at->format('d/m/Y H:i') }} 
                đến {{ now()->format('d/m/Y H:i') }}. 
                Do bạn không online hoặc có vấn đề kết nối, chúng tôi gửi email này để đảm bảo bạn nhận được thông tin.
            </p>
        </div>

        <!-- Why This Happened -->
        <div class="explanation" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #0056b3;">
                <i class="fas fa-question-circle" style="margin-right: 8px;"></i>
                Tại sao tôi nhận được email này?
            </h4>
            <ul style="margin: 0; padding-left: 20px; color: #0056b3; line-height: 1.6;">
                <li style="margin-bottom: 8px;">
                    Bạn không online khi thông báo được gửi
                </li>
                <li style="margin-bottom: 8px;">
                    Có thể có vấn đề với kết nối internet hoặc trình duyệt
                </li>
                <li style="margin-bottom: 8px;">
                    Đây là thông báo có mức độ ưu tiên cao cần được xử lý ngay
                </li>
                <li>
                    Hệ thống tự động gửi email dự phòng để đảm bảo bạn không bỏ lỡ thông tin quan trọng
                </li>
            </ul>
        </div>

        <!-- Call to Action -->
        <div class="cta-section" style="text-align: center; margin: 32px 0;">
            <a href="{{ route('login') }}" 
               class="cta-button"
               style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; margin-right: 12px;">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                Đăng nhập MechaMap
            </a>
            
            <a href="{{ route('notification-preferences.index') }}" 
               style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-cog" style="margin-right: 8px;"></i>
                Cài đặt thông báo
            </a>
        </div>

        <!-- Technical Details -->
        <div class="technical-details" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #495057;">
                Chi tiết kỹ thuật
            </h4>
            <div style="font-size: 12px; color: #6c757d; line-height: 1.4;">
                <div>Notification ID: {{ $notification->id }}</div>
                <div>Delivery attempts: {{ $notification->data['delivery_attempts'] ?? 'N/A' }}</div>
                <div>Fallback sent: {{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>
@endsection

@section('footer-links')
    <a href="{{ route('notifications.index') }}" style="color: #007bff; text-decoration: none;">
        Tất cả thông báo
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    <a href="{{ route('notification-preferences.index') }}" style="color: #007bff; text-decoration: none;">
        Cài đặt thông báo
    </a>
@endsection
