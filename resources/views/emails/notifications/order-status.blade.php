@extends('emails.layouts.base')

@section('title', 'Cập nhật trạng thái đơn hàng - MechaMap')

@section('content')
<div class="greeting">
    Xin chào {{ $user->name }}! 👋
</div>

@php
    $status = $notificationData['status'] ?? $order->status;
    $statusConfig = [
        'pending' => [
            'icon' => '⏳',
            'title' => 'Đơn hàng đang chờ xử lý',
            'class' => 'warning',
            'message' => 'Đơn hàng của bạn đã được tiếp nhận và đang chờ người bán xác nhận.'
        ],
        'confirmed' => [
            'icon' => '✅',
            'title' => 'Đơn hàng đã được xác nhận',
            'class' => 'success',
            'message' => 'Người bán đã xác nhận đơn hàng và đang chuẩn bị hàng cho bạn.'
        ],
        'processing' => [
            'icon' => '📦',
            'title' => 'Đơn hàng đang được xử lý',
            'class' => 'primary',
            'message' => 'Đơn hàng đang được đóng gói và chuẩn bị giao hàng.'
        ],
        'shipped' => [
            'icon' => '🚚',
            'title' => 'Đơn hàng đã được giao cho đơn vị vận chuyển',
            'class' => 'primary',
            'message' => 'Đơn hàng đã được giao cho đơn vị vận chuyển và đang trên đường đến bạn.'
        ],
        'delivered' => [
            'icon' => '🎉',
            'title' => 'Đơn hàng đã được giao thành công',
            'class' => 'success',
            'message' => 'Đơn hàng đã được giao đến địa chỉ của bạn. Cảm ơn bạn đã mua sắm tại MechaMap!'
        ],
        'cancelled' => [
            'icon' => '❌',
            'title' => 'Đơn hàng đã bị hủy',
            'class' => 'danger',
            'message' => 'Đơn hàng đã bị hủy. Nếu bạn đã thanh toán, số tiền sẽ được hoàn lại trong 3-5 ngày làm việc.'
        ],
        'refunded' => [
            'icon' => '💰',
            'title' => 'Đơn hàng đã được hoàn tiền',
            'class' => 'success',
            'message' => 'Đơn hàng đã được hoàn tiền thành công. Số tiền sẽ được chuyển về tài khoản của bạn.'
        ]
    ];
    
    $config = $statusConfig[$status] ?? $statusConfig['pending'];
@endphp

<div class="info-box {{ $config['class'] }}">
    <h4>{{ $config['icon'] }} {{ $config['title'] }}</h4>
    <p>{{ $config['message'] }}</p>
</div>

<div class="order-details">
    <h5>📋 Thông tin đơn hàng</h5>
    <table class="order-info-table">
        <tr>
            <td><strong>Mã đơn hàng:</strong></td>
            <td>#{{ $order->order_number ?? $order->id }}</td>
        </tr>
        <tr>
            <td><strong>Ngày đặt:</strong></td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Tổng tiền:</strong></td>
            <td><strong>{{ number_format($order->total_amount ?? 0) }}đ</strong></td>
        </tr>
        @if(isset($order->shipping_address))
        <tr>
            <td><strong>Địa chỉ giao hàng:</strong></td>
            <td>{{ $order->shipping_address }}</td>
        </tr>
        @endif
        @if(isset($notificationData['tracking_number']))
        <tr>
            <td><strong>Mã vận đơn:</strong></td>
            <td><strong>{{ $notificationData['tracking_number'] }}</strong></td>
        </tr>
        @endif
        @if(isset($notificationData['estimated_delivery']))
        <tr>
            <td><strong>Dự kiến giao hàng:</strong></td>
            <td>{{ $notificationData['estimated_delivery'] }}</td>
        </tr>
        @endif
    </table>
</div>

@if($status === 'shipped' && isset($notificationData['tracking_url']))
<div class="btn-container">
    <a href="{{ $notificationData['tracking_url'] }}" class="btn btn-primary">
        📍 Theo dõi đơn hàng
    </a>
</div>
@elseif($status === 'delivered')
<div class="btn-container">
    <a href="{{ config('app.url') }}/orders/{{ $order->id }}/review" class="btn btn-success">
        ⭐ Đánh giá sản phẩm
    </a>
</div>
@else
<div class="btn-container">
    <a href="{{ config('app.url') }}/orders/{{ $order->id }}" class="btn btn-primary">
        👀 Xem chi tiết đơn hàng
    </a>
</div>
@endif

@if(isset($notificationData['note']) && $notificationData['note'])
<div class="message">
    <h5>📝 Ghi chú từ người bán:</h5>
    <div class="seller-note">
        {{ $notificationData['note'] }}
    </div>
</div>
@endif

@if($status === 'cancelled' && isset($notificationData['cancellation_reason']))
<div class="info-box secondary">
    <h5>❓ Lý do hủy đơn:</h5>
    <p>{{ $notificationData['cancellation_reason'] }}</p>
</div>
@endif

@if(in_array($status, ['delivered', 'cancelled', 'refunded']))
<div class="info-box secondary">
    <h4>🛍️ Tiếp tục mua sắm</h4>
    <p>Khám phá thêm nhiều sản phẩm chất lượng khác trên MechaMap Marketplace!</p>
    <a href="{{ config('app.url') }}/marketplace" class="btn btn-outline">
        🏪 Khám phá Marketplace
    </a>
</div>
@endif

<div class="help-section">
    <p>
        <strong>Cần hỗ trợ?</strong> Liên hệ với chúng tôi:
        <a href="mailto:support@mechamap.com">support@mechamap.com</a>
        hoặc gọi hotline: <strong>1900-MECHA</strong>
    </p>
</div>
@endsection

@section('footer-links')
<a href="{{ config('app.url') }}/orders">Đơn hàng của tôi</a>
<a href="{{ config('app.url') }}/marketplace">Marketplace</a>
<a href="{{ config('app.url') }}/help/orders">Hỗ trợ đơn hàng</a>
@endsection
