@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng - MechaMap')

@push('styles')
<link rel="stylesheet" href="{{ asset_versioned('assets/css/cart-ux-enhancements.css') }}">
<style>
.payment-status-loading {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.bank-info-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #dee2e6;
}

.copy-btn {
    padding: 4px 8px;
    font-size: 12px;
}

.qr-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.payment-success-box {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}
</style>
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-white border-bottom">
        <div class="container-fluid py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="fas fa-home me-2"></i>
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.index') }}" class="text-decoration-none">Marketplace</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.cart.index') }}" class="text-decoration-none">Cart</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('marketplace.checkout.index') }}" class="text-decoration-none">Checkout</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Success Payment Box (Hidden by default) -->
                <div id="success_pay_box" class="payment-success-box mb-4" style="display: none;">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h2 class="mb-3">Thanh toán thành công!</h2>
                    <p class="mb-0">Chúng tôi đã nhận được thanh toán của bạn. Đơn hàng sẽ được xử lý trong thời gian sớm nhất!</p>
                </div>

                <!-- Payment Instructions Box -->
                <div id="checkout_box">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card me-2"></i>
                                <h5 class="mb-0">Thanh toán đơn hàng #{{ $order->order_number }}</h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Hướng dẫn thanh toán qua chuyển khoản ngân hàng</strong>
                            </div>

                            <div class="row">
                                <!-- QR Code Payment -->
                                <div class="col-md-6">
                                    <div class="qr-container">
                                        <h6 class="mb-3">
                                            <i class="fas fa-qrcode me-2"></i>
                                            Cách 1: Quét mã QR
                                        </h6>

                                        <img src="{{ $qr_url }}"
                                             alt="QR Code thanh toán"
                                             class="img-fluid mb-3"
                                             style="max-width: 250px;">

                                        <div class="alert alert-warning">
                                            <small>
                                                <i class="fas fa-mobile-alt me-1"></i>
                                                Mở app ngân hàng và quét mã QR để thanh toán
                                            </small>
                                        </div>

                                        <!-- Payment Status -->
                                        <div class="payment-status mt-3">
                                            <div id="payment-status-alert" class="alert alert-warning payment-status-loading">
                                                <i class="fas fa-clock me-2"></i>
                                                <span id="payment-status-text">Đang chờ thanh toán...</span>
                                            </div>
                                            <div class="progress">
                                                <div id="payment-progress"
                                                     class="progress-bar progress-bar-striped progress-bar-animated"
                                                     role="progressbar"
                                                     style="width: 0%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Manual Transfer Info -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">
                                        <i class="fas fa-university me-2"></i>
                                        Cách 2: Chuyển khoản thủ công
                                    </h6>

                                    <div class="text-center mb-3">
                                        <img src="https://qr.sepay.vn/assets/img/banklogo/{{ $bank_info['bank_code'] }}.png"
                                             alt="{{ $bank_info['bank_name'] }}"
                                             class="img-fluid"
                                             style="max-height: 50px;">
                                        <p class="fw-bold mt-2">{{ $bank_info['bank_name'] }}</p>
                                    </div>

                                    <table class="table bank-info-table">
                                        <tbody>
                                            <tr>
                                                <td><strong>Chủ tài khoản:</strong></td>
                                                <td>
                                                    {{ $bank_info['account_name'] }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Số tài khoản:</strong></td>
                                                <td>
                                                    <span class="text-primary fw-bold">{{ $bank_info['account_number'] }}</span>
                                                    <button class="btn btn-outline-primary copy-btn ms-2"
                                                            onclick="copyToClipboard('{{ $bank_info['account_number'] }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Số tiền:</strong></td>
                                                <td>
                                                    <span class="text-danger fw-bold">{{ number_format($order->total_amount) }} VNĐ</span>
                                                    <button class="btn btn-outline-primary copy-btn ms-2"
                                                            onclick="copyToClipboard('{{ $order->total_amount }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nội dung CK:</strong></td>
                                                <td>
                                                    <span class="text-primary fw-bold">{{ $transfer_content }}</span>
                                                    <button class="btn btn-outline-primary copy-btn ms-2"
                                                            onclick="copyToClipboard('{{ $transfer_content }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small>
                                            <strong>Lưu ý:</strong> Vui lòng giữ nguyên nội dung chuyển khoản
                                            <strong>{{ $transfer_content }}</strong> để hệ thống tự động xác nhận thanh toán.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('marketplace.checkout.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Quay lại checkout
                                </a>
                                <button type="button" class="btn btn-primary" onclick="checkPaymentStatus()">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Kiểm tra thanh toán
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            Thông tin đơn hàng
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                            <br>
                                            <small class="text-muted">Số lượng: {{ $item->quantity }}</small>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item->price * $item->quantity) }} VNĐ
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td><strong>Phí vận chuyển:</strong></td>
                                        <td class="text-end">{{ number_format($order->shipping_amount) }} VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Thuế:</strong></td>
                                        <td class="text-end">{{ number_format($order->tax_amount) }} VNĐ</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Tổng cộng:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->total_amount) }} VNĐ</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let paymentStatus = 'pending';
let paymentPollingInterval = null;
let paymentPollingAttempts = 0;
const MAX_POLLING_ATTEMPTS = 120; // 10 minutes (5 second intervals)

// Start polling when page loads
document.addEventListener('DOMContentLoaded', function() {
    startPaymentStatusPolling();
});

function startPaymentStatusPolling() {
    paymentPollingAttempts = 0;

    paymentPollingInterval = setInterval(() => {
        checkPaymentStatus();
        paymentPollingAttempts++;

        // Update progress bar
        const progress = Math.min((paymentPollingAttempts / MAX_POLLING_ATTEMPTS) * 100, 100);
        const progressBar = document.getElementById('payment-progress');
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }

        // Stop polling after max attempts
        if (paymentPollingAttempts >= MAX_POLLING_ATTEMPTS) {
            stopPaymentStatusPolling();
            updatePaymentStatus('timeout', 'Hết thời gian chờ thanh toán. Vui lòng kiểm tra lại.');
        }
    }, 5000); // Check every 5 seconds
}

function stopPaymentStatusPolling() {
    if (paymentPollingInterval) {
        clearInterval(paymentPollingInterval);
        paymentPollingInterval = null;
    }
}

function checkPaymentStatus() {
    fetch('/marketplace/checkout/check-payment-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            order_id: {{ $order->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.payment_status === 'paid') {
                stopPaymentStatusPolling();
                updatePaymentStatus('success', 'Thanh toán thành công!');

                // Show success box and hide checkout box
                document.getElementById('checkout_box').style.display = 'none';
                document.getElementById('success_pay_box').style.display = 'block';

                // Redirect to success page after 3 seconds
                setTimeout(() => {
                    window.location.href = `/marketplace/orders/{{ $order->uuid }}/success`;
                }, 3000);
            } else if (data.payment_status === 'failed') {
                stopPaymentStatusPolling();
                updatePaymentStatus('error', 'Thanh toán thất bại. Vui lòng thử lại.');
            }
            // Continue polling if still pending
        }
    })
    .catch(error => {
        console.error('Error checking payment status:', error);
    });
}

function updatePaymentStatus(type, message) {
    const alertElement = document.getElementById('payment-status-alert');
    const textElement = document.getElementById('payment-status-text');

    if (alertElement && textElement) {
        // Remove existing classes
        alertElement.className = 'alert';

        // Add new class based on type
        if (type === 'success') {
            alertElement.classList.add('alert-success');
            alertElement.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + message;
        } else if (type === 'error') {
            alertElement.classList.add('alert-danger');
            alertElement.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + message;
        } else if (type === 'timeout') {
            alertElement.classList.add('alert-warning');
            alertElement.innerHTML = '<i class="fas fa-clock me-2"></i>' + message;
        } else {
            alertElement.classList.add('alert-warning', 'payment-status-loading');
            alertElement.innerHTML = '<i class="fas fa-clock me-2"></i>' + message;
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        showToast('success', 'Đã sao chép vào clipboard');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showToast('error', 'Không thể sao chép');
    });
}

function showToast(type, message) {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
        ${message}
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush
@endsection
