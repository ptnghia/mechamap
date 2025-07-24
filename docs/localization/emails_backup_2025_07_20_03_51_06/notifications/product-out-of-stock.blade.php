@extends('emails.layouts.notification')

@section('title', 'Sản phẩm hết hàng')

@section('header')
    <div class="header-content">
        <div class="notification-icon" style="background-color: #dc3545;">
            <i class="fas fa-exclamation-triangle" style="color: white; font-size: 24px;"></i>
        </div>
        <h1 style="color: #dc3545; margin: 16px 0 8px 0; font-size: 24px; font-weight: 600;">
            Sản phẩm hết hàng
        </h1>
        <p style="color: #6c757d; margin: 0; font-size: 16px;">
            Thông báo về tình trạng kho hàng
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
            <p>Chúng tôi thông báo rằng sản phẩm của bạn đã hết hàng:</p>
        </div>

        <!-- Product Information -->
        <div class="product-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                @if($product->featured_image)
                    <div style="flex-shrink: 0;">
                        <img src="{{ asset('storage/' . $product->featured_image) }}" 
                             alt="{{ $product->name }}"
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                    </div>
                @endif
                
                <div style="flex: 1; min-width: 0;">
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600; color: #212529;">
                        {{ $product->name }}
                    </h3>
                    
                    <div style="margin-bottom: 12px;">
                        <span style="display: inline-block; background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            SKU: {{ $product->sku }}
                        </span>
                    </div>
                    
                    <div style="color: #6c757d; font-size: 14px; margin-bottom: 8px;">
                        <strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }}₫
                        @if($product->is_on_sale && $product->sale_price)
                            <span style="text-decoration: line-through; margin-left: 8px;">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
                        @endif
                    </div>
                    
                    <div style="color: #6c757d; font-size: 14px;">
                        <strong>Loại:</strong> {{ $product->getProductTypes()[$product->product_type] ?? $product->product_type }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status -->
        <div class="stock-status" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #ffc107; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-exclamation" style="font-size: 14px;"></i>
                </div>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #856404;">
                        Trạng thái: Hết hàng
                    </h4>
                    <p style="margin: 0; color: #856404; font-size: 14px;">
                        Số lượng hiện tại: <strong>{{ $product->stock_quantity }}</strong> sản phẩm
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Required -->
        <div class="action-required">
            <h3 style="color: #dc3545; font-size: 18px; font-weight: 600; margin: 24px 0 12px 0;">
                <i class="fas fa-tasks" style="margin-right: 8px;"></i>
                Hành động cần thiết
            </h3>
            
            <ul style="color: #495057; line-height: 1.6; margin: 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">
                    <strong>Cập nhật kho hàng:</strong> Thêm sản phẩm mới vào kho để tiếp tục bán hàng
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Thông báo khách hàng:</strong> Liên hệ với khách hàng đang quan tâm về thời gian có hàng trở lại
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Kiểm tra đơn hàng:</strong> Xem xét các đơn hàng đang chờ xử lý
                </li>
            </ul>
        </div>

        <!-- Statistics -->
        @if(isset($notificationData['stats']))
        <div class="product-stats" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                Thống kê sản phẩm
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px;">
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #007bff;">{{ $notificationData['stats']['views'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #6c757d;">Lượt xem</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #28a745;">{{ $notificationData['stats']['sales'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #6c757d;">Đã bán</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #ffc107;">{{ $notificationData['stats']['wishlist'] ?? 0 }}</div>
                    <div style="font-size: 12px; color: #6c757d;">Wishlist</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Call to Action -->
        <div class="cta-section" style="text-align: center; margin: 32px 0;">
            <a href="{{ $notificationData['action_url'] ?? '#' }}" 
               class="cta-button"
               style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-edit" style="margin-right: 8px;"></i>
                Cập nhật sản phẩm
            </a>
        </div>

        <!-- Additional Help -->
        <div class="help-section" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #0056b3;">
                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                Gợi ý
            </h4>
            <p style="margin: 0; font-size: 14px; color: #0056b3; line-height: 1.5;">
                Để tránh hết hàng trong tương lai, bạn có thể thiết lập cảnh báo kho thấp trong phần quản lý sản phẩm. 
                Hệ thống sẽ tự động thông báo khi số lượng sản phẩm dưới ngưỡng bạn đặt.
            </p>
        </div>
    </div>
@endsection

@section('footer-links')
    <a href="{{ route('marketplace.seller.products.index') }}" style="color: #007bff; text-decoration: none;">
        Quản lý sản phẩm
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    <a href="{{ route('marketplace.seller.dashboard') }}" style="color: #007bff; text-decoration: none;">
        Dashboard
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    <a href="{{ route('marketplace.seller.orders.index') }}" style="color: #007bff; text-decoration: none;">
        Đơn hàng
    </a>
@endsection
