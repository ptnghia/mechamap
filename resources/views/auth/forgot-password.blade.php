@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<x-auth-layout
    title="Khôi phục mật khẩu"
    subtitle="Lấy lại quyền truy cập vào tài khoản của bạn"
    :show-social-login="false">

    <!-- Page Title -->
    <div class="text-center mb-4">
        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="fas fa-key text-primary fs-2"></i>
        </div>
        <h2 class="fw-bold text-dark mb-2">Quên mật khẩu?</h2>
        <p class="text-muted">Không sao cả! Chúng tôi sẽ gửi link khôi phục mật khẩu đến email của bạn</p>
    </div>

    <!-- Status Messages -->
    @if (session('status'))
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Reset Form -->
    <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Địa chỉ email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control form-control-lg @error('email') is-invalid @enderror"
                   required autofocus autocomplete="email"
                   placeholder="Nhập địa chỉ email đã đăng ký">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Chúng tôi sẽ gửi link khôi phục mật khẩu đến email này
            </small>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Gửi link khôi phục
            </button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
            </a>
        </div>
    </form>

    <!-- Help Section -->
    <div class="mt-5 p-4 bg-light rounded-3">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-question-circle me-2 text-primary"></i>Cần hỗ trợ?
        </h6>
        <div class="row g-3">
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-clock text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Không nhận được email?</strong>
                        <p class="mb-0 small text-muted">Kiểm tra thư mục spam hoặc thử lại sau 5 phút</p>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex align-items-start">
                    <i class="fas fa-envelope text-muted me-3 mt-1"></i>
                    <div>
                        <strong>Email không tồn tại?</strong>
                        <p class="mb-0 small text-muted">
                            Hãy <a href="{{ route('register') }}" class="text-primary">tạo tài khoản mới</a>
                            hoặc liên hệ hỗ trợ
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
                            Liên hệ: <a href="mailto:support@mechamap.com" class="text-primary">support@mechamap.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-auth-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus email input
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.focus();
    }

    // Form submission feedback
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>
@endpush
@endsection
