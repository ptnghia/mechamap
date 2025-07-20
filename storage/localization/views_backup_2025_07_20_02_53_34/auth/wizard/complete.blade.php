@extends('layouts.app')

@section('title', 'Đăng ký thành công')

@section('content')
<div class="registration-complete-container">
    <div class="complete-card shadow-lg">
        {{-- Success Header --}}
        <div class="complete-header">
            <div class="success-animation">
                <div class="checkmark-circle">
                    <div class="checkmark"></div>
                </div>
            </div>
            
            <h1 class="complete-title">
                🎉 Đăng ký thành công!
            </h1>
            
            <p class="complete-subtitle">
                Chào mừng {{ $user->name }} đến với MechaMap
            </p>
        </div>

        {{-- Success Content --}}
        <div class="complete-body">
            @if($isBusiness)
                {{-- Business Account Success --}}
                <div class="business-success">
                    <div class="alert alert-info border-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Tài khoản doanh nghiệp đã được tạo</h5>
                                <p class="mb-0">
                                    Tài khoản của bạn đang chờ xác minh từ admin. 
                                    Bạn sẽ nhận được email thông báo khi tài khoản được phê duyệt.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="verification-status">
                        <h4 class="status-title">
                            <i class="fas fa-clipboard-check text-primary me-2"></i>
                            Trạng thái xác minh
                        </h4>
                        
                        <div class="status-steps">
                            <div class="status-step completed">
                                <div class="step-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Thông tin cơ bản</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class="status-step completed">
                                <div class="step-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Thông tin doanh nghiệp</h6>
                                    <p>Đã hoàn thành</p>
                                </div>
                            </div>
                            
                            <div class="status-step pending">
                                <div class="step-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="step-content">
                                    <h6>Xác minh admin</h6>
                                    <p>Đang chờ xử lý</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="next-steps">
                        <h4 class="steps-title">
                            <i class="fas fa-list-check text-success me-2"></i>
                            Bước tiếp theo
                        </h4>
                        
                        <div class="steps-list">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-text">
                                    <strong>Xác minh email</strong><br>
                                    Kiểm tra hộp thư và click vào link xác minh
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-text">
                                    <strong>Chờ xác thực admin</strong><br>
                                    Admin sẽ xem xét và phê duyệt tài khoản trong 1-3 ngày
                                </div>
                            </div>
                            
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-text">
                                    <strong>Nhận thông báo</strong><br>
                                    Bạn sẽ nhận email khi tài khoản được kích hoạt
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="business-info">
                        <div class="alert alert-warning border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Lưu ý:</strong> 
                            Trong thời gian chờ xác minh, bạn có thể đăng nhập và cập nhật thông tin profile, 
                            nhưng một số tính năng doanh nghiệp sẽ bị hạn chế.
                        </div>
                    </div>
                </div>
            @else
                {{-- Community Account Success --}}
                <div class="community-success">
                    <div class="alert alert-success border-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-3 mt-1"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Tài khoản cộng đồng đã được tạo</h5>
                                <p class="mb-0">
                                    Bạn có thể bắt đầu sử dụng MechaMap ngay lập tức. 
                                    Hãy xác minh email để mở khóa tất cả tính năng.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="community-features">
                        <h4 class="features-title">
                            <i class="fas fa-star text-warning me-2"></i>
                            Tính năng có sẵn
                        </h4>
                        
                        <div class="features-grid">
                            <div class="feature-item">
                                <i class="fas fa-comments text-primary"></i>
                                <h6>Thảo luận</h6>
                                <p>Tham gia các cuộc thảo luận về cơ khí</p>
                            </div>
                            
                            <div class="feature-item">
                                <i class="fas fa-share-alt text-info"></i>
                                <h6>Chia sẻ</h6>
                                <p>Chia sẻ kiến thức và kinh nghiệm</p>
                            </div>
                            
                            <div class="feature-item">
                                <i class="fas fa-users text-success"></i>
                                <h6>Kết nối</h6>
                                <p>Kết nối với cộng đồng cơ khí</p>
                            </div>
                            
                            <div class="feature-item">
                                <i class="fas fa-book text-warning"></i>
                                <h6>Học tập</h6>
                                <p>Truy cập tài liệu và khóa học</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Email Verification Notice --}}
            <div class="email-verification">
                <div class="verification-card">
                    <div class="verification-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="verification-content">
                        <h5>Xác minh email</h5>
                        <p>
                            Chúng tôi đã gửi email xác minh đến: 
                            <strong>{{ $user->email }}</strong>
                        </p>
                        <p class="text-muted">
                            Vui lòng kiểm tra hộp thư (bao gồm cả thư mục spam) và click vào link xác minh.
                        </p>
                    </div>
                </div>
                
                <div class="verification-actions">
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            Gửi lại email xác minh
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="complete-footer">
            <div class="action-buttons">
                @if($isBusiness)
                    <a href="{{ route('business.dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Đi đến Dashboard
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>
                        Đi đến Dashboard
                    </a>
                @endif
                
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-user-edit me-2"></i>
                    Hoàn thiện Profile
                </a>
            </div>
            
            <div class="help-links">
                <p class="text-muted mb-2">Cần hỗ trợ?</p>
                <div class="help-actions">
                    <a href="{{ route('help.getting-started') }}" class="text-decoration-none me-3">
                        <i class="fas fa-question-circle me-1"></i>
                        Hướng dẫn bắt đầu
                    </a>
                    <a href="{{ route('contact') }}" class="text-decoration-none">
                        <i class="fas fa-headset me-1"></i>
                        Liên hệ hỗ trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.registration-complete-container {
    max-width: 700px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.complete-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

.complete-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 3rem 2rem;
    text-align: center;
}

.success-animation {
    margin-bottom: 2rem;
}

.checkmark-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    margin: 0 auto;
    position: relative;
    animation: scaleIn 0.5s ease-out;
}

.checkmark {
    width: 30px;
    height: 15px;
    border: 3px solid white;
    border-top: none;
    border-right: none;
    transform: rotate(-45deg);
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -10px;
    margin-left: -15px;
    animation: checkmarkDraw 0.5s ease-out 0.3s both;
}

@keyframes scaleIn {
    0% { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes checkmarkDraw {
    0% { width: 0; height: 0; }
    100% { width: 30px; height: 15px; }
}

.complete-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.complete-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.complete-body {
    padding: 2rem;
}

.verification-status {
    margin: 2rem 0;
}

.status-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.status-steps {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.status-step {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.status-step.completed .step-icon {
    background: #28a745;
    color: white;
}

.status-step.pending .step-icon {
    background: #ffc107;
    color: #212529;
}

.step-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.step-content p {
    margin-bottom: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.next-steps {
    margin: 2rem 0;
}

.steps-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.steps-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.step-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.step-text {
    flex: 1;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.feature-item {
    text-align: center;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.2s ease-out;
}

.feature-item:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
}

.feature-item i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.feature-item h6 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.feature-item p {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}

.email-verification {
    margin: 2rem 0;
}

.verification-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.verification-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e3f2fd;
    color: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.verification-content h5 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.verification-content p {
    margin-bottom: 0.5rem;
}

.verification-actions {
    text-align: center;
}

.complete-footer {
    background-color: #f8f9fa;
    padding: 2rem;
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.help-links {
    margin-top: 1rem;
}

.help-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .complete-header {
        padding: 2rem 1rem;
    }
    
    .complete-title {
        font-size: 1.5rem;
    }
    
    .complete-body {
        padding: 1.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .action-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .verification-card {
        flex-direction: column;
        text-align: center;
    }
    
    .help-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush
