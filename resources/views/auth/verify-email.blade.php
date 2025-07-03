@extends('layouts.app')

@section('title', 'Xác minh email')

@section('content')
<x-auth-layout
    title="Xác minh email"
    subtitle="Hoàn tất quá trình đăng ký tài khoản"
    :show-social-login="false">

    <!-- Page Title -->
    <div class="text-center mb-4">
        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="fas fa-envelope-open-text text-warning fs-2"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Kiểm tra email của bạn</h2>
        <p class="text-muted">Chúng tôi đã gửi link xác minh đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-info mb-4">
            <i class="fas fa-paper-plane me-2"></i>
            Link xác minh mới đã được gửi đến địa chỉ email bạn đã đăng ký.
        </div>
    @endif

    <!-- Instructions -->
    <div class="mb-4 p-4 bg-light rounded-3">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-list-ol me-2 text-primary"></i>Hướng dẫn xác minh
        </h6>
        <ol class="mb-0">
            <li class="mb-2">Mở ứng dụng email trên thiết bị của bạn</li>
            <li class="mb-2">Tìm email từ <strong>MechaMap</strong> với tiêu đề "Xác minh địa chỉ email"</li>
            <li class="mb-2">Nhấp vào nút <strong>"Xác minh Email"</strong> trong email</li>
            <li class="mb-0">Quay lại trang này để tiếp tục</li>
        </ol>
    </div>

    <!-- Action Buttons -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-redo me-2"></i>Gửi lại email xác minh
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-4 p-4 bg-light rounded-3">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-question-circle me-2 text-primary"></i>Cần hỗ trợ?
        </h6>
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-search text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Không tìm thấy email?</strong>
                        <p class="mb-0 small text-muted">Kiểm tra thư mục spam/junk mail hoặc thư mục quảng cáo</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-clock text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Email chưa đến?</strong>
                        <p class="mb-0 small text-muted">Đợi 2-3 phút rồi kiểm tra lại, hoặc nhấn "Gửi lại email xác minh"</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-edit text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Email sai?</strong>
                        <p class="mb-0 small text-muted">
                            Đăng xuất và <a href="{{ route('register') }}" class="text-primary">đăng ký lại</a>
                            với email đúng
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-headset text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Vẫn gặp vấn đề?</strong>
                        <p class="mb-0 small text-muted">
                            Liên hệ hỗ trợ: <a href="mailto:support@mechamap.com" class="text-primary">support@mechamap.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh notice -->
    <div class="text-center mt-4">
        <small class="text-muted">
            <i class="fas fa-sync-alt me-1"></i>
            Trang này sẽ tự động làm mới sau khi bạn xác minh email
        </small>
    </div>
</x-auth-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-check verification status every 10 seconds
    let checkInterval;

    function checkVerificationStatus() {
        fetch('{{ route("verification.check") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                clearInterval(checkInterval);
                // Show success message and redirect
                showSuccessAndRedirect();
            }
        })
        .catch(error => {
            console.log('Verification check failed:', error);
        });
    }

    function showSuccessAndRedirect() {
        // Create success overlay
        const overlay = document.createElement('div');
        overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
        overlay.style.zIndex = '9999';

        overlay.innerHTML = `
            <div class="text-center text-white">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h3>Email đã được xác minh!</h3>
                <p>Đang chuyển hướng...</p>
                <div class="spinner-border text-light" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Redirect after 2 seconds
        setTimeout(() => {
            window.location.href = '{{ route("home") }}';
        }, 2000);
    }

    // Start checking every 10 seconds
    checkInterval = setInterval(checkVerificationStatus, 10000);

    // Form submission feedback
    const resendForm = document.querySelector('form[action="{{ route("verification.send") }}"]');
    if (resendForm) {
        resendForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
                submitBtn.disabled = true;

                // Re-enable after 5 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = '<i class="fas fa-redo me-2"></i>Gửi lại email xác minh';
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    }

    // Cleanup interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });
});
</script>
@endpush
@endsection
