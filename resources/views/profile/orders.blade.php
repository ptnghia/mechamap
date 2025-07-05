@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bag-check me-2"></i>
                        Đơn hàng của tôi
                    </h5>
                    <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="shop me-1"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            <div class="order-item border rounded p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">Đơn hàng #{{ $order->order_number }}</h6>
                                                <small class="text-muted">
                                                    Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                @php
                                                    $statusClass = match($order->status) {
                                                        'pending' => 'bg-warning',
                                                        'processing' => 'bg-info',
                                                        'shipped' => 'bg-primary',
                                                        'delivered' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    $statusText = match($order->status) {
                                                        'pending' => 'Chờ xử lý',
                                                        'processing' => 'Đang xử lý',
                                                        'shipped' => 'Đã gửi',
                                                        'delivered' => 'Đã giao',
                                                        'cancelled' => 'Đã hủy',
                                                        default => ucfirst($order->status)
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </div>
                                        </div>

                                        <!-- Order Items -->
                                        <div class="order-items">
                                            @foreach($order->items as $item)
                                                <div class="d-flex align-items-center mb-2">
                                                    @if($item->product && $item->product->featured_image)
                                                        <img src="{{ get_product_image_url($item->product->featured_image) }}"
                                                             alt="{{ $item->product->name }}"
                                                             class="rounded me-3"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                             style="width: 50px; height: 50px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $item->product_name }}</h6>
                                                        <small class="text-muted">
                                                            Số lượng: {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <strong>${{ number_format($item->unit_price * $item->quantity, 2) }}</strong>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="text-end">
                                            <div class="mb-2">
                                                <small class="text-muted">Tổng cộng:</small><br>
                                                <h5 class="mb-0">${{ number_format($order->total_amount, 2) }}</h5>
                                            </div>

                                            @if($order->shippingAddress)
                                                <div class="mb-2">
                                                    <small class="text-muted">Giao đến:</small><br>
                                                    <small>
                                                        {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}<br>
                                                        {{ $order->shippingAddress->address_line_1 }}<br>
                                                        {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}
                                                    </small>
                                                </div>
                                            @endif

                                            <div class="btn-group-vertical w-100" role="group">
                                                <a href="{{ route('marketplace.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Xem chi tiết
                                                </a>
                                                @if($order->status === 'delivered')
                                                    <a href="#" class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-download me-1"></i>
                                                        Tải hóa đơn
                                                    </a>
                                                @endif
                                                @if(in_array($order->status, ['pending', 'processing']))
                                                    <button class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-times-circle me-1"></i>
                                                        Hủy đơn
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bag-x display-1 text-muted mb-3"></i>
                            <h5>Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Bạn chưa có đơn hàng nào. Hãy khám phá marketplace để tìm sản phẩm yêu thích!</p>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
                                <i class="shop me-2"></i>
                                Khám phá Marketplace
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="person-circle me-2"></i>
                        Tài khoản của tôi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action">
                            <i class="person me-2"></i>
                            Thông tin cá nhân
                        </a>
                        <a href="{{ route('profile.orders') }}" class="list-group-item list-group-item-action active">
                            <i class="bag-check me-2"></i>
                            Đơn hàng của tôi
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-heart me-2"></i>
                            Sản phẩm yêu thích
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Địa chỉ giao hàng
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-credit-card me-2"></i>
                            Phương thức thanh toán
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Thống kê đơn hàng
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalOrders = $orders->total();
                        $pendingOrders = $orders->where('status', 'pending')->count();
                        $deliveredOrders = $orders->where('status', 'delivered')->count();
                        $totalSpent = $orders->sum('total_amount');

                        // Tính toán từ collection hiện tại
                        $currentPagePending = $orders->where('status', 'pending')->count();
                        $currentPageDelivered = $orders->where('status', 'delivered')->count();
                    @endphp
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="mb-0 text-primary">{{ $totalOrders }}</h4>
                                <small class="text-muted">Tổng đơn hàng</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0 text-success">${{ number_format($totalSpent, 2) }}</h4>
                            <small class="text-muted">Tổng chi tiêu</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="mb-0 text-warning">{{ $currentPagePending }}</h5>
                                <small class="text-muted">Chờ xử lý</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0 text-success">{{ $currentPageDelivered }}</h5>
                            <small class="text-muted">Đã giao</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.order-item {
    transition: all 0.3s ease;
}

.order-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.order-items img {
    border: 1px solid #dee2e6;
}

.btn-group-vertical .btn {
    margin-bottom: 0.25rem;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}
</style>
@endpush
