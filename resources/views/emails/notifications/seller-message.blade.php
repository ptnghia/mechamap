@extends('emails.layouts.notification')

@section('title', 'Tin nhắn mới')

@section('header')
    <div class="header-content">
        <div class="notification-icon" style="background-color: #007bff;">
            <i class="fas fa-envelope" style="color: white; font-size: 24px;"></i>
        </div>
        <h1 style="color: #007bff; margin: 16px 0 8px 0; font-size: 24px; font-weight: 600;">
            Tin nhắn mới!
        </h1>
        <p style="color: #6c757d; margin: 0; font-size: 16px;">
            Bạn có tin nhắn mới từ {{ $notificationData['sender_role'] ?? 'người dùng' }}
        </p>
    </div>
@endsection

@section('content')
    <div class="notification-content">
        <!-- Greeting -->
        <div class="greeting">
            <p>Xin chào <strong>{{ $recipient->name }}</strong>,</p>
        </div>

        <!-- Main Message -->
        <div class="main-message">
            <p>Bạn vừa nhận được một tin nhắn mới từ {{ $notificationData['sender_role'] ?? 'người dùng' }}:</p>
        </div>

        <!-- Sender Information -->
        <div class="sender-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <div style="display: flex; align-items: center; gap: 16px;">
                @if($sender->avatar)
                    <div style="flex-shrink: 0;">
                        <img src="{{ asset('storage/' . $sender->avatar) }}" 
                             alt="{{ $sender->name }}"
                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #dee2e6;">
                    </div>
                @else
                    <div style="flex-shrink: 0; width: 60px; height: 60px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-weight: 600; font-size: 20px;">
                            {{ strtoupper(substr($sender->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                
                <div style="flex: 1; min-width: 0;">
                    <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 600; color: #212529;">
                        {{ $sender->name }}
                    </h3>
                    
                    <div style="color: #6c757d; font-size: 14px; margin-bottom: 8px;">
                        <span style="display: inline-block; background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            {{ $notificationData['sender_role'] ?? 'Người dùng' }}
                        </span>
                        @if($notificationData['is_marketplace_message'] ?? false)
                            <span style="display: inline-block; background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 8px;">
                                Marketplace
                            </span>
                        @endif
                    </div>
                    
                    <div style="color: #6c757d; font-size: 14px;">
                        <i class="fas fa-clock me-1"></i>
                        {{ $message->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Content -->
        <div class="message-card" style="background: #fff; border: 2px solid #007bff; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #007bff;">
                <i class="fas fa-comment" style="margin-right: 8px;"></i>
                Nội dung tin nhắn
            </h4>
            
            <div style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                <p style="margin: 0; color: #495057; line-height: 1.6; white-space: pre-wrap;">{{ $message->content }}</p>
            </div>
            
            <!-- Message Preview for Long Messages -->
            @if(strlen($message->content) > 200)
                <div style="text-align: center; margin-top: 12px;">
                    <small style="color: #6c757d; font-style: italic;">
                        Tin nhắn đầy đủ có thể được xem trong cuộc trò chuyện
                    </small>
                </div>
            @endif
        </div>

        <!-- Conversation Context -->
        @if($conversation->title)
            <div class="conversation-context" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 6px; padding: 16px; margin: 20px 0;">
                <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #0056b3;">
                    <i class="fas fa-comments" style="margin-right: 6px;"></i>
                    Cuộc trò chuyện
                </h4>
                <p style="margin: 0; font-size: 14px; color: #0056b3;">
                    <strong>{{ $conversation->title }}</strong>
                </p>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="quick-actions" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                <i class="fas fa-bolt" style="margin-right: 8px;"></i>
                Hành động nhanh
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <div>
                    <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #495057;">Trả lời ngay</h5>
                    <p style="margin: 0; font-size: 12px; color: #6c757d;">Nhấn vào nút bên dưới để trả lời tin nhắn</p>
                </div>
                <div>
                    <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #495057;">Xem lịch sử</h5>
                    <p style="margin: 0; font-size: 12px; color: #6c757d;">Xem toàn bộ cuộc trò chuyện trước đó</p>
                </div>
                @if($notificationData['is_marketplace_message'] ?? false)
                    <div>
                        <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #495057;">Quản lý đơn hàng</h5>
                        <p style="margin: 0; font-size: 12px; color: #6c757d;">Kiểm tra đơn hàng liên quan (nếu có)</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-section" style="text-align: center; margin: 32px 0;">
            <a href="{{ $notificationData['action_url'] ?? '#' }}" 
               class="cta-button"
               style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; margin-right: 12px;">
                <i class="fas fa-reply" style="margin-right: 8px;"></i>
                Trả lời tin nhắn
            </a>
            
            <a href="{{ route('conversations.index') }}" 
               style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-list" style="margin-right: 8px;"></i>
                Xem tất cả tin nhắn
            </a>
        </div>

        <!-- Marketplace Tips -->
        @if($notificationData['is_marketplace_message'] ?? false)
            <div class="marketplace-tips" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 16px; margin: 20px 0;">
                <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #155724;">
                    <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
                    Mẹo giao tiếp hiệu quả
                </h4>
                <ul style="margin: 0; padding-left: 20px; color: #155724; line-height: 1.6;">
                    <li style="margin-bottom: 8px;">
                        <strong>Trả lời nhanh:</strong> Phản hồi trong vòng 24 giờ để duy trì uy tín
                    </li>
                    <li style="margin-bottom: 8px;">
                        <strong>Thông tin rõ ràng:</strong> Cung cấp thông tin chi tiết về sản phẩm/dịch vụ
                    </li>
                    <li style="margin-bottom: 8px;">
                        <strong>Chuyên nghiệp:</strong> Giữ thái độ lịch sự và chuyên nghiệp
                    </li>
                    <li>
                        <strong>Theo dõi đơn hàng:</strong> Cập nhật tình trạng đơn hàng thường xuyên
                    </li>
                </ul>
            </div>
        @endif

        <!-- Privacy Notice -->
        <div class="privacy-notice" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #856404;">
                <i class="fas fa-shield-alt" style="margin-right: 6px;"></i>
                Bảo mật thông tin
            </h4>
            <p style="margin: 0; font-size: 14px; color: #856404; line-height: 1.5;">
                Tin nhắn này được gửi qua hệ thống bảo mật của MechaMap. 
                Vui lòng không chia sẻ thông tin cá nhân nhạy cảm qua tin nhắn.
            </p>
        </div>

        <!-- Message Settings -->
        <div class="message-settings" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                Cài đặt thông báo
            </h4>
            <p style="margin: 0 0 12px 0; font-size: 14px; color: #6c757d; line-height: 1.5;">
                Bạn có thể tùy chỉnh cách nhận thông báo tin nhắn trong phần cài đặt tài khoản.
            </p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('notification-preferences.index') }}" 
                   style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-size: 14px;">
                    Cài đặt thông báo
                </a>
                <a href="{{ route('conversations.index') }}" 
                   style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-size: 14px;">
                    Quản lý tin nhắn
                </a>
            </div>
        </div>
    </div>
@endsection

@section('footer-links')
    <a href="{{ route('conversations.index') }}" style="color: #007bff; text-decoration: none;">
        Tin nhắn
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    @if($notificationData['is_marketplace_message'] ?? false)
        <a href="{{ route('marketplace.products.index') }}" style="color: #007bff; text-decoration: none;">
            Marketplace
        </a>
        <span style="color: #dee2e6; margin: 0 8px;">|</span>
    @endif
    <a href="{{ route('notification-preferences.index') }}" style="color: #007bff; text-decoration: none;">
        Cài đặt thông báo
    </a>
@endsection
