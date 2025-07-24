@extends('layouts.app')

@section('title', 'File Tải Xuống - Đơn Hàng #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('marketplace.partials.sidebar')
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Order Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-receipt me-2"></i>
                                Đơn Hàng #{{ $order->order_number }}
                            </h4>
                            <p class="card-title-desc mb-0">Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }} fs-6">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Trạng thái thanh toán:</strong> 
                                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                            <p><strong>Tổng tiền:</strong> {{ number_format($order->total_amount) }} VNĐ</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
                            <p><strong>Ghi chú:</strong> {{ $order->customer_notes ?? 'Không có' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Digital Products -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-download me-2"></i>
                        Sản Phẩm Kỹ Thuật Số ({{ $digitalItems->count() }})
                    </h4>
                    <p class="card-title-desc mb-0">Các file có thể tải xuống từ đơn hàng này</p>
                </div>
                <div class="card-body">
                    @if($digitalItems->count() > 0)
                        <div class="row">
                            @foreach($digitalItems as $item)
                            <div class="col-lg-6 mb-4">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                @if($item->product->featured_image)
                                                    <img src="{{ Storage::url($item->product->featured_image) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 80px; height: 80px;">
                                                        <i class="fas fa-cube fa-2x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-2">{{ $item->product->name }}</h5>
                                                <p class="text-muted mb-2">{{ Str::limit($item->product->short_description, 100) }}</p>
                                                
                                                <div class="row text-sm mb-3">
                                                    <div class="col-6">
                                                        <strong>Số lượng:</strong> {{ $item->quantity }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Giá:</strong> {{ number_format($item->unit_price) }} VNĐ
                                                    </div>
                                                </div>

                                                <!-- Download Info -->
                                                @if($item->download_links)
                                                    <div class="mb-3">
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            {{ count($item->download_links) }} file có sẵn để tải
                                                        </small>
                                                    </div>
                                                @endif

                                                <!-- Action Buttons -->
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('marketplace.downloads.item-files', $item) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-download me-1"></i>
                                                        Xem File ({{ count($item->download_links ?? []) }})
                                                    </a>
                                                    <a href="{{ route('marketplace.products.show', $item->product) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        Chi Tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không có sản phẩm kỹ thuật số</h5>
                            <p class="text-muted">Đơn hàng này không chứa sản phẩm kỹ thuật số nào có thể tải xuống.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Download Instructions -->
            @if($digitalItems->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Hướng Dẫn Tải Xuống
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-shield-alt text-success me-2"></i>Bảo Mật</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>File được bảo vệ bằng token bảo mật</li>
                                <li><i class="fas fa-check text-success me-2"></i>Chỉ tài khoản đã mua mới có thể tải</li>
                                <li><i class="fas fa-check text-success me-2"></i>Theo dõi lịch sử tải xuống</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock text-info me-2"></i>Thời Gian</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-infinity text-info me-2"></i>Không giới hạn thời gian tải</li>
                                <li><i class="fas fa-redo text-info me-2"></i>Có thể tải lại nhiều lần</li>
                                <li><i class="fas fa-history text-info me-2"></i>Lưu trữ lịch sử tải xuống</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Lưu ý:</strong> Mỗi lần tải xuống sẽ tạo một token bảo mật có thời hạn 24 giờ. 
                        Bạn có thể tải lại file bất cứ lúc nào từ trang lịch sử tải xuống.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card.border {
    border: 1px solid #e9ecef !important;
    transition: all 0.3s ease;
}

.card.border:hover {
    border-color: #007bff !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.075);
}

.text-sm {
    font-size: 0.875rem;
}

.list-unstyled li {
    padding: 0.25rem 0;
}
</style>
@endpush
