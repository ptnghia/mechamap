@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng - Supplier Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('supplier.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">📦 Chi tiết đơn hàng #{{ $orderItem->order->order_number }}</h1>
                    <p class="text-muted">Thông tin chi tiết về đơn hàng</p>
                </div>
                <div>
                    <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <!-- Order Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-1">Trạng thái đơn hàng</h5>
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipped' => 'Đã giao',
                                            'delivered' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$orderItem->fulfillment_status] ?? 'secondary' }} fs-6">
                                        {{ $statusLabels[$orderItem->fulfillment_status] ?? $orderItem->fulfillment_status }}
                                    </span>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    @if(in_array($orderItem->fulfillment_status, ['pending', 'processing']))
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-edit"></i> Cập nhật trạng thái
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($orderItem->fulfillment_status == 'pending')
                                            <li>
                                                <button class="dropdown-item" onclick="updateStatus('processing')">
                                                    <i class="fas fa-play text-info"></i> Bắt đầu xử lý
                                                </button>
                                            </li>
                                            @endif
                                            @if($orderItem->fulfillment_status == 'processing')
                                            <li>
                                                <button class="dropdown-item" onclick="updateStatus('shipped')">
                                                    <i class="fas fa-truck text-primary"></i> Đánh dấu đã giao
                                                </button>
                                            </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" onclick="updateStatus('cancelled')">
                                                    <i class="fas fa-times"></i> Hủy đơn hàng
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">👤 Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Tên khách hàng:</strong><br>
                                {{ $orderItem->order->customer->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                {{ $orderItem->order->customer->email }}
                            </div>
                            <div class="mb-3">
                                <strong>Số điện thoại:</strong><br>
                                {{ $orderItem->order->customer->phone ?? 'Chưa cung cấp' }}
                            </div>
                            <div class="mb-0">
                                <strong>Địa chỉ giao hàng:</strong><br>
                                {{ $orderItem->order->shipping_address ?? 'Chưa cung cấp' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📋 Thông tin đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Mã đơn hàng:</strong><br>
                                #{{ $orderItem->order->order_number }}
                            </div>
                            <div class="mb-3">
                                <strong>Ngày đặt:</strong><br>
                                {{ $orderItem->created_at->format('d/m/Y H:i') }}
                            </div>
                            @if($orderItem->shipped_at)
                            <div class="mb-3">
                                <strong>Ngày giao:</strong><br>
                                {{ $orderItem->shipped_at->format('d/m/Y H:i') }}
                            </div>
                            @endif
                            @if($orderItem->tracking_number)
                            <div class="mb-3">
                                <strong>Mã vận đơn:</strong><br>
                                {{ $orderItem->tracking_number }}
                            </div>
                            @endif
                            @if($orderItem->carrier)
                            <div class="mb-0">
                                <strong>Đơn vị vận chuyển:</strong><br>
                                {{ $orderItem->carrier }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📦 Thông tin sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($orderItem->product && $orderItem->product->featured_image)
                                    <img src="{{ $orderItem->product->featured_image }}" 
                                         alt="{{ $orderItem->product_name }}" 
                                         class="img-fluid rounded">
                                    @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                        <i class="fas fa-box fa-2x text-muted"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $orderItem->product_name }}</h6>
                                    @if($orderItem->product_sku)
                                    <small class="text-muted">SKU: {{ $orderItem->product_sku }}</small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <strong>Số lượng</strong><br>
                                    {{ number_format($orderItem->quantity) }}
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>Tổng tiền</strong><br>
                                    <span class="text-success fs-5">{{ number_format($orderItem->total_amount, 0, ',', '.') }} VND</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">📅 Lịch sử đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Đơn hàng được tạo</h6>
                                        <small class="text-muted">{{ $orderItem->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                @if($orderItem->fulfillment_status != 'pending')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Bắt đầu xử lý</h6>
                                        <small class="text-muted">{{ $orderItem->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endif
                                
                                @if($orderItem->shipped_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Đã giao hàng</h6>
                                        <small class="text-muted">{{ $orderItem->shipped_at->format('d/m/Y H:i') }}</small>
                                        @if($orderItem->tracking_number)
                                        <br><small class="text-muted">Mã vận đơn: {{ $orderItem->tracking_number }}</small>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                
                                @if($orderItem->delivered_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Đã hoàn thành</h6>
                                        <small class="text-muted">{{ $orderItem->delivered_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endif
                                
                                @if($orderItem->fulfillment_status == 'cancelled')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-danger"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Đơn hàng đã bị hủy</h6>
                                        <small class="text-muted">{{ $orderItem->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
function updateStatus(status) {
    if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?')) {
        fetch(`{{ route('supplier.orders.updateStatus', $orderItem->id) }}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ fulfillment_status: status })
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.');
        });
    }
}
</script>
@endpush
@endsection
