@extends('emails.layouts.notification')

@section('title', 'Sản phẩm có hàng trở lại')

@section('header')
    <div class="header-content">
        <div class="notification-icon" style="background-color: #28a745;">
            <i class="fas fa-heart" style="color: white; font-size: 24px;"></i>
        </div>
        <h1 style="color: #28a745; margin: 16px 0 8px 0; font-size: 24px; font-weight: 600;">
            Sản phẩm có hàng trở lại!
        </h1>
        <p style="color: #6c757d; margin: 0; font-size: 16px;">
            Sản phẩm trong wishlist của bạn đã có sẵn
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
            <p>Tin tuyệt vời! Sản phẩm mà bạn đã thêm vào wishlist đã có hàng trở lại:</p>
        </div>

        <!-- Product Information -->
        <div class="product-card" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                @if($product->featured_image)
                    <div style="flex-shrink: 0;">
                        <img src="{{ asset('storage/' . $product->featured_image) }}" 
                             alt="{{ $product->name }}"
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6;">
                    </div>
                @endif
                
                <div style="flex: 1; min-width: 0;">
                    <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #212529;">
                        {{ $product->name }}
                    </h3>
                    
                    <div style="margin-bottom: 12px;">
                        <span style="display: inline-block; background: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
                            SKU: {{ $product->sku }}
                        </span>
                        <span style="display: inline-block; background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 8px;">
                            {{ $product->getProductTypes()[$product->product_type] ?? $product->product_type }}
                        </span>
                    </div>
                    
                    <div style="color: #6c757d; font-size: 14px; margin-bottom: 8px;">
                        @if($product->short_description)
                            {{ Str::limit($product->short_description, 120) }}
                        @endif
                    </div>
                    
                    <!-- Price Information -->
                    <div style="margin-bottom: 12px;">
                        <div style="font-size: 18px; font-weight: 600; color: #28a745;">
                            {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}₫
                            @if($product->is_on_sale && $product->sale_price)
                                <span style="text-decoration: line-through; color: #6c757d; font-size: 14px; font-weight: normal; margin-left: 8px;">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                                <span style="background: #dc3545; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px; margin-left: 8px;">
                                    SALE
                                </span>
                            @endif
                        </div>
                        
                        @if(isset($notificationData['target_price']) && $notificationData['is_target_reached'])
                            <div style="color: #28a745; font-size: 14px; margin-top: 4px;">
                                <i class="fas fa-bullseye" style="margin-right: 4px;"></i>
                                Đã đạt giá mục tiêu: {{ number_format($notificationData['target_price'], 0, ',', '.') }}₫
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status -->
        <div class="stock-status" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #28a745; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-check" style="font-size: 14px;"></i>
                </div>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #155724;">
                        Có sẵn trong kho
                    </h4>
                    <p style="margin: 0; color: #155724; font-size: 14px;">
                        Số lượng hiện có: <strong>{{ $notificationData['stock_quantity'] ?? $product->stock_quantity }}</strong> sản phẩm
                    </p>
                </div>
            </div>
        </div>

        <!-- Urgency Message -->
        <div class="urgency-message" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #ffc107; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-clock" style="font-size: 14px;"></i>
                </div>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #856404;">
                        Đặt hàng ngay!
                    </h4>
                    <p style="margin: 0; color: #856404; font-size: 14px;">
                        Sản phẩm có thể hết hàng nhanh chóng. Đặt hàng ngay để đảm bảo có được sản phẩm bạn mong muốn.
                    </p>
                </div>
            </div>
        </div>

        <!-- Product Features -->
        @if($product->technical_specs || $product->material || $product->manufacturing_process)
        <div class="product-features" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                Thông tin kỹ thuật
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                @if($product->material)
                    <div>
                        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Vật liệu</div>
                        <div style="font-size: 14px; color: #495057;">{{ $product->material }}</div>
                    </div>
                @endif
                @if($product->manufacturing_process)
                    <div>
                        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Quy trình</div>
                        <div style="font-size: 14px; color: #495057;">{{ $product->manufacturing_process }}</div>
                    </div>
                @endif
                @if($product->standards_compliance)
                    <div>
                        <div style="font-size: 12px; color: #6c757d; text-transform: uppercase; font-weight: 500;">Tiêu chuẩn</div>
                        <div style="font-size: 14px; color: #495057;">{{ $product->standards_compliance }}</div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Call to Action -->
        <div class="cta-section" style="text-align: center; margin: 32px 0;">
            <a href="{{ $notificationData['action_url'] ?? '#' }}" 
               class="cta-button"
               style="display: inline-block; background: #28a745; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px; margin-right: 12px;">
                <i class="fas fa-shopping-cart" style="margin-right: 8px;"></i>
                Mua ngay
            </a>
            
            <a href="{{ route('marketplace.wishlist.index') }}" 
               style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 14px 28px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-heart" style="margin-right: 8px;"></i>
                Xem wishlist
            </a>
        </div>

        <!-- Related Products -->
        <div class="related-section" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: #0056b3;">
                <i class="fas fa-lightbulb" style="margin-right: 6px;"></i>
                Gợi ý
            </h4>
            <p style="margin: 0; font-size: 14px; color: #0056b3; line-height: 1.5;">
                Khám phá thêm các sản phẩm tương tự trong cùng danh mục hoặc từ cùng nhà cung cấp. 
                Có thể bạn sẽ tìm thấy những sản phẩm phù hợp khác cho dự án của mình.
            </p>
        </div>

        <!-- Wishlist Management -->
        <div class="wishlist-management" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                Quản lý wishlist
            </h4>
            <p style="margin: 0 0 12px 0; font-size: 14px; color: #6c757d; line-height: 1.5;">
                Bạn có thể cập nhật cài đặt thông báo cho sản phẩm này hoặc các sản phẩm khác trong wishlist.
            </p>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('marketplace.wishlist.index') }}" 
                   style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-size: 14px;">
                    Quản lý wishlist
                </a>
                <a href="{{ route('notification-preferences.index') }}" 
                   style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 4px; font-size: 14px;">
                    Cài đặt thông báo
                </a>
            </div>
        </div>
    </div>
@endsection

@section('footer-links')
    <a href="{{ route('marketplace.products.index') }}" style="color: #007bff; text-decoration: none;">
        Sản phẩm
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    <a href="{{ route('marketplace.wishlist.index') }}" style="color: #007bff; text-decoration: none;">
        Wishlist
    </a>
    <span style="color: #dee2e6; margin: 0 8px;">|</span>
    <a href="{{ route('marketplace.cart.index') }}" style="color: #007bff; text-decoration: none;">
        Giỏ hàng
    </a>
@endsection
