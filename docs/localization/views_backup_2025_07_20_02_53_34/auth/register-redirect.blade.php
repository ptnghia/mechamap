@extends('layouts.app')

@section('title', 'Đăng ký tài khoản MechaMap')

@section('content')
<div class="register-redirect-container">
    <div class="redirect-card shadow-lg">
        {{-- Header --}}
        <div class="redirect-header">
            <div class="upgrade-icon">
                <i class="fas fa-magic"></i>
            </div>
            <h1 class="redirect-title">
                🎉 Quy trình đăng ký mới!
            </h1>
            <p class="redirect-subtitle">
                Chúng tôi đã cải tiến trải nghiệm đăng ký để phục vụ bạn tốt hơn
            </p>
        </div>

        {{-- Content --}}
        <div class="redirect-body">
            <div class="improvements-grid">
                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-route text-primary"></i>
                    </div>
                    <h5>Quy trình từng bước</h5>
                    <p>Đăng ký dễ dàng với hướng dẫn rõ ràng từng bước</p>
                </div>

                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-shield-check text-success"></i>
                    </div>
                    <h5>Xác thực thời gian thực</h5>
                    <p>Kiểm tra thông tin ngay lập tức, tránh lỗi khi submit</p>
                </div>

                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-building text-info"></i>
                    </div>
                    <h5>Thông tin doanh nghiệp</h5>
                    <p>Dành riêng cho đối tác kinh doanh với form chuyên biệt</p>
                </div>

                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-mobile-alt text-warning"></i>
                    </div>
                    <h5>Tối ưu mobile</h5>
                    <p>Trải nghiệm mượt mà trên mọi thiết bị</p>
                </div>

                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-save text-secondary"></i>
                    </div>
                    <h5>Lưu tự động</h5>
                    <p>Không lo mất dữ liệu khi điền form</p>
                </div>

                <div class="improvement-item">
                    <div class="improvement-icon">
                        <i class="fas fa-universal-access text-purple"></i>
                    </div>
                    <h5>Dễ tiếp cận</h5>
                    <p>Hỗ trợ đầy đủ cho người khuyết tật</p>
                </div>
            </div>

            <div class="redirect-notice">
                <div class="alert alert-info border-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Lưu ý:</strong> Bạn sẽ được chuyển hướng tự động đến quy trình đăng ký mới trong <span id="countdown">5</span> giây.
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="redirect-footer">
            <div class="action-buttons">
                <a href="{{ route('register.wizard.step1') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-right me-2"></i>
                    Bắt đầu đăng ký ngay
                </a>

                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Đã có tài khoản? Đăng nhập
                </a>
            </div>

            <div class="help-text">
                <p class="text-muted mb-0">
                    <i class="fas fa-question-circle me-1"></i>
                    Cần hỗ trợ? <a href="{{ route('contact') }}" class="text-decoration-none">Liên hệ với chúng tôi</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.register-redirect-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.redirect-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.redirect-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
}

.upgrade-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    margin: 0 auto 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.redirect-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.redirect-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.redirect-body {
    padding: 2rem;
}

.improvements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.improvement-item {
    text-align: center;
    padding: 1.5rem;
    border: 1px solid #f1f3f4;
    border-radius: 12px;
    transition: all 0.3s ease-out;
}

.improvement-item:hover {
    border-color: #007bff;
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
}

.improvement-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f8f9fa;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.improvement-item h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #212529;
}

.improvement-item p {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
    line-height: 1.5;
}

.redirect-notice {
    margin: 2rem 0;
}

.redirect-footer {
    background-color: #f8f9fa;
    padding: 2rem;
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.help-text {
    margin-top: 1rem;
}

.text-purple {
    color: #6f42c1 !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .redirect-header {
        padding: 2rem 1rem;
    }

    .redirect-title {
        font-size: 1.5rem;
    }

    .redirect-body {
        padding: 1.5rem;
    }

    .improvements-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .improvement-item {
        padding: 1rem;
    }

    .action-buttons {
        flex-direction: column;
        align-items: center;
    }

    .action-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .redirect-card {
        background: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }

    .redirect-footer {
        background-color: #1a202c;
        border-color: #4a5568;
    }

    .improvement-item {
        background: #374151;
        border-color: #4a5568;
        color: #e2e8f0;
    }

    .improvement-icon {
        background: #4a5568;
    }
}

/* Countdown animation */
#countdown {
    font-weight: bold;
    color: #007bff;
    animation: countdownPulse 1s infinite;
}

@keyframes countdownPulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');

    const timer = setInterval(() => {
        countdown--;
        if (countdownElement) {
            countdownElement.textContent = countdown;
        }

        if (countdown <= 0) {
            clearInterval(timer);
            // Redirect to wizard
            window.location.href = '{{ route("register.wizard.step1") }}';
        }
    }, 1000);

    // Add click tracking for analytics
    document.querySelectorAll('a[href*="wizard"]').forEach(link => {
        link.addEventListener('click', function() {
            // Track wizard redirect click
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    event_category: 'registration',
                    event_label: 'wizard_redirect_manual'
                });
            }
        });
    });

    // Track auto-redirect
    setTimeout(() => {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'redirect', {
                event_category: 'registration',
                event_label: 'wizard_redirect_auto'
            });
        }
    }, 5000);
});
</script>
@endpush
