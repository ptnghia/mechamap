@extends('emails.layouts.notification')

@section('title', 'Nhận được đánh giá mới')

@section('header')
    <div class="header-content">
        <div class="notification-icon" style="background-color: #ffc107;">
            <i class="fas fa-star" style="color: white; font-size: 24px;"></i>
        </div>
        <h1 style="color: #ffc107; margin: 16px 0 8px 0; font-size: 24px; font-weight: 600;">
            Nhận được đánh giá mới!
        </h1>
        <p style="color: #6c757d; margin: 0; font-size: 16px;">
            Khách hàng đã đánh giá sản phẩm của bạn
        </p>
    </div>
@endsection

@section('content')
    <div class="notification-content">
        <!-- Greeting -->
        <div class="greeting">
            <p>Xin chào <strong>{{ $seller->name }}</strong>,</p>
        </div>

        <!-- Main Message -->
        <div class="main-message">
            <p>Bạn vừa nhận được một đánh giá mới cho sản phẩm của mình:</p>
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
                        <strong>Giá:</strong> {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}₫
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Information -->
        <div class="review-card" style="background: #fff; border: 2px solid #ffc107; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <!-- Review Header -->
            <div style="display: flex; justify-content-between; align-items-start; margin-bottom: 16px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #212529;">
                        Đánh giá từ {{ $reviewer->name }}
                        @if($review->is_verified_purchase)
                            <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-left: 8px;">
                                ✓ Đã mua hàng
                            </span>
                        @endif
                    </h4>
                    <div style="color: #6c757d; font-size: 14px;">
                        {{ $review->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                
                <!-- Rating Stars -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="display: flex; gap: 2px;">
                        @for($i = 1; $i <= 5; $i++)
                            <span style="color: {{ $i <= $review->rating ? '#ffc107' : '#e9ecef' }}; font-size: 18px;">★</span>
                        @endfor
                    </div>
                    <span style="font-weight: 600; color: #ffc107; font-size: 16px;">
                        {{ $review->rating }}/5
                    </span>
                </div>
            </div>

            <!-- Review Title -->
            @if($review->title)
                <div style="margin-bottom: 12px;">
                    <h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #495057;">
                        "{{ $review->title }}"
                    </h5>
                </div>
            @endif

            <!-- Review Content -->
            <div style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                <p style="margin: 0; color: #495057; line-height: 1.6; font-style: italic;">
                    "{{ $review->content }}"
                </p>
            </div>

            <!-- Review Rating Text -->
            <div style="text-align: center; padding: 12px; background: {{ $review->rating >= 4 ? '#d4edda' : ($review->rating >= 3 ? '#fff3cd' : '#f8d7da') }}; border-radius: 6px;">
                <span style="font-weight: 600; color: {{ $review->rating >= 4 ? '#155724' : ($review->rating >= 3 ? '#856404' : '#721c24') }}; font-size: 16px;">
                    {{ $notificationData['rating_text'] ?? 'Đánh giá' }}
                </span>
            </div>
        </div>

        <!-- Review Impact -->
        <div class="review-impact" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #0056b3;">
                <i class="fas fa-chart-line" style="margin-right: 8px;"></i>
                Tác động của đánh giá
            </h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #007bff;">
                        {{ $review->rating >= 4 ? '+' : '' }}{{ $review->rating >= 4 ? 'Tích cực' : ($review->rating >= 3 ? 'Trung tính' : 'Cần cải thiện') }}
                    </div>
                    <div style="font-size: 12px; color: #6c757d;">Ảnh hưởng</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #28a745;">
                        {{ $review->is_verified_purchase ? 'Có' : 'Không' }}
                    </div>
                    <div style="font-size: 12px; color: #6c757d;">Mua hàng xác thực</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 600; color: #ffc107;">
                        Công khai
                    </div>
                    <div style="font-size: 12px; color: #6c757d;">Hiển thị</div>
                </div>
            </div>
        </div>

        <!-- Response Suggestion -->
        @if($review->rating >= 4)
            <div class="response-suggestion" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 16px; margin: 20px 0;">
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #155724;">
                    <i class="fas fa-thumbs-up" style="margin-right: 8px;"></i>
                    Đánh giá tích cực!
                </h4>
                <p style="margin: 0; color: #155724; font-size: 14px; line-height: 1.5;">
                    Đây là một đánh giá tuyệt vời! Hãy cảm ơn khách hàng và tiếp tục duy trì chất lượng sản phẩm. 
                    Bạn có thể trả lời đánh giá để thể hiện sự quan tâm đến khách hàng.
                </p>
            </div>
        @elseif($review->rating >= 3)
            <div class="response-suggestion" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 16px; margin: 20px 0;">
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #856404;">
                    <i class="fas fa-balance-scale" style="margin-right: 8px;"></i>
                    Đánh giá trung bình
                </h4>
                <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.5;">
                    Hãy xem xét phản hồi của khách hàng để cải thiện sản phẩm. 
                    Bạn có thể trả lời để hiểu rõ hơn về trải nghiệm của họ.
                </p>
            </div>
        @else
            <div class="response-suggestion" style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 6px; padding: 16px; margin: 20px 0;">
                <h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #721c24;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    Cần cải thiện
                </h4>
                <p style="margin: 0; color: #721c24; font-size: 14px; line-height: 1.5;">
                    Đánh giá này cho thấy có vấn đề cần khắc phục. Hãy liên hệ trực tiếp với khách hàng để giải quyết 
                    và cải thiện sản phẩm/dịch vụ.
                </p>
            </div>
        @endif

        <!-- Call to Action -->
        <div class="cta-section" style="text-align: center; margin: 32px 0;">
            <a href="{{ $notificationData['action_url'] ?? '#' }}" 
               class="cta-button"
               style="display: inline-block; background: #007bff; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 16px; margin-right: 12px;">
                <i class="fas fa-eye me-1"></i>
                Xem đánh giá
            </a>
            
            <a href="{{ route('marketplace.seller.products.index') }}" 
               style="display: inline-block; background: #6c757d; color: white; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 16px;">
                <i class="fas fa-cog me-1"></i>
                Quản lý sản phẩm
            </a>
        </div>

        <!-- Tips for Sellers -->
        <div class="seller-tips" style="background: #f8f9fa; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
                Mẹo cho người bán
            </h4>
            <ul style="margin: 0; padding-left: 20px; color: #6c757d; line-height: 1.6;">
                <li style="margin-bottom: 8px;">
                    <strong>Trả lời đánh giá:</strong> Luôn trả lời các đánh giá để thể hiện sự quan tâm đến khách hàng
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Cải thiện sản phẩm:</strong> Sử dụng phản hồi để nâng cao chất lượng sản phẩm
                </li>
                <li style="margin-bottom: 8px;">
                    <strong>Khuyến khích đánh giá:</strong> Nhắc nhở khách hàng hài lòng để lại đánh giá tích cực
                </li>
                <li>
                    <strong>Xử lý khiếu nại:</strong> Liên hệ trực tiếp với khách hàng không hài lòng để giải quyết vấn đề
                </li>
            </ul>
        </div>

        <!-- Review Statistics -->
        <div class="review-stats" style="background: #e9ecef; border-radius: 6px; padding: 16px; margin: 20px 0;">
            <h4 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #495057;">
                Thống kê đánh giá sản phẩm
            </h4>
            <p style="margin: 0; font-size: 14px; color: #6c757d; line-height: 1.5;">
                Để xem thống kê chi tiết về tất cả đánh giá của sản phẩm này, vui lòng truy cập trang quản lý sản phẩm 
                hoặc xem trực tiếp trên trang sản phẩm.
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
