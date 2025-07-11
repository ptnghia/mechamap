@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - Bước 1')

@section('full-width-content')
<x-registration-wizard
    :current-step="$step"
    :total-steps="$totalSteps"
    :progress="$progress"
    title="Đăng ký tài khoản MechaMap"
    subtitle="Bước 1: Thông tin cơ bản"
    next-button-text="Tiếp tục"
    :show-back-button="false"
    form-id="step1Form"
    :session-data="$sessionData">

    <form id="step1Form" method="POST" action="{{ route('register.wizard.step1') }}" novalidate>
        @csrf

        {{-- Section: Personal Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-user text-primary me-2"></i>
                Thông tin cá nhân
            </h3>
            <p class="section-description text-muted mb-4">
                Vui lòng nhập thông tin cá nhân chính xác để tạo tài khoản của bạn.
            </p>

            <div class="row">
                {{-- Full Name --}}
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">
                        <i class="fas fa-user me-1"></i>
                        Họ và tên
                    </label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $sessionData['name'] ?? '') }}"
                           placeholder="Nhập họ và tên đầy đủ"
                           required
                           autocomplete="name">
                    <div class="invalid-feedback" id="name-error">
                        @error('name'){{ $message }}@enderror
                    </div>
                    <div class="valid-feedback" id="name-success" style="display: none;">
                        <i class="fas fa-check"></i> Tên hợp lệ
                    </div>
                </div>

                {{-- Username --}}
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label required">
                        <i class="fas fa-at me-1"></i>
                        Tên đăng nhập
                    </label>
                    <input type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           id="username"
                           name="username"
                           value="{{ old('username', $sessionData['username'] ?? '') }}"
                           placeholder="Chọn tên đăng nhập duy nhất"
                           required
                           autocomplete="username">
                    <div class="invalid-feedback" id="username-error">
                        @error('username'){{ $message }}@enderror
                    </div>
                    <div class="valid-feedback" id="username-success" style="display: none;">
                        <i class="fas fa-check"></i> Tên đăng nhập khả dụng
                    </div>
                    <small class="form-text text-muted">
                        Tên đăng nhập sẽ được sử dụng trong URL profile của bạn
                    </small>
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label required">
                    <i class="fas fa-envelope me-1"></i>
                    Địa chỉ email
                </label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $sessionData['email'] ?? '') }}"
                       placeholder="Nhập địa chỉ email của bạn"
                       required
                       autocomplete="email">
                <div class="invalid-feedback" id="email-error">
                    @error('email'){{ $message }}@enderror
                </div>
                <div class="valid-feedback" id="email-success" style="display: none;">
                    <i class="fas fa-check"></i> Email hợp lệ
                </div>
                <small class="form-text text-muted">
                    Email này sẽ được sử dụng để xác minh tài khoản và nhận thông báo
                </small>
            </div>

            <div class="row">
                {{-- Password --}}
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label required">
                        <i class="fas fa-lock me-1"></i>
                        Mật khẩu
                    </label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="Tạo mật khẩu mạnh"
                               required
                               autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                        <div class="invalid-feedback" id="password-error">
                            @error('password'){{ $message }}@enderror
                        </div>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="strength-text" id="strengthText"></small>
                    </div>

                    <small class="form-text text-muted">
                        Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt
                    </small>
                </div>

                {{-- Confirm Password --}}
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label required">
                        <i class="fas fa-lock me-1"></i>
                        Xác nhận mật khẩu
                    </label>
                    <input type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Nhập lại mật khẩu"
                           required
                           autocomplete="new-password">
                    <div class="invalid-feedback" id="password-confirmation-error">
                        @error('password_confirmation'){{ $message }}@enderror
                    </div>
                    <div class="valid-feedback" id="password-confirmation-success" style="display: none;">
                        <i class="fas fa-check"></i> Mật khẩu khớp
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Account Type --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-user-tag text-primary me-2"></i>
                Loại tài khoản
            </h3>
            <p class="section-description text-muted mb-4">
                Chọn loại tài khoản phù hợp với mục đích sử dụng của bạn.
            </p>

            {{-- Community Members --}}
            <div class="account-type-group mb-3" data-group="community">
                <div class="account-type-header">
                    <h4 class="account-group-title">
                        <i class="fas fa-users text-warning me-2"></i>
                        Thành viên cộng đồng
                    </h4>
                    <p class="account-group-description">
                        Dành cho những người muốn tham gia thảo luận và chia sẻ kiến thức
                    </p>
                </div>

                <div class="account-options">
                    <div class="account-option recommended">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="account_type"
                                   id="member"
                                   value="member"
                                   {{ old('account_type', $sessionData['account_type'] ?? '') == 'member' ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="member">
                                <strong>Thành viên</strong>
                                <span class="badge bg-primary ms-2">Khuyến nghị</span>
                                <span class="account-description">
                                    Tham gia thảo luận, chia sẻ kiến thức và kết nối với cộng đồng cơ khí
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="account-option">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="account_type"
                                   id="guest"
                                   value="guest"
                                   {{ old('account_type', $sessionData['account_type'] ?? '') == 'guest' ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="guest">
                                <strong>Đối tác cá nhân</strong>
                                <span class="account-description">
                                    Mua bán sản phẩm kỹ thuật số, xem nội dung cộng đồng
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="upgrade-notice mt-3">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Bạn có thể được nâng cấp lên <strong>Thành viên cao cấp</strong> sau khi tích cực tham gia cộng đồng và đóng góp chất lượng.
                    </div>
                </div>
            </div>

            {{-- Business Partners --}}
            <div class="account-type-group mb-3" data-group="business">
                <div class="account-type-header">
                    <h4 class="account-group-title">
                        <i class="fas fa-building text-primary me-2"></i>
                        Đối tác kinh doanh
                    </h4>
                    <p class="account-group-description">
                        Dành cho doanh nghiệp và tổ chức hoạt động trong lĩnh vực cơ khí
                    </p>
                </div>

                <div class="account-options">
                    <div class="account-option">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="account_type"
                                   id="manufacturer"
                                   value="manufacturer"
                                   {{ old('account_type', $sessionData['account_type'] ?? '') == 'manufacturer' ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="manufacturer">
                                <strong>Nhà sản xuất</strong>
                                <span class="account-description">
                                    Sản xuất và cung cấp sản phẩm, thiết bị cơ khí
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="account-option">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="account_type"
                                   id="supplier"
                                   value="supplier"
                                   {{ old('account_type', $sessionData['account_type'] ?? '') == 'supplier' ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="supplier">
                                <strong>Nhà cung cấp</strong>
                                <span class="account-description">
                                    Phân phối thiết bị, vật tư và linh kiện cơ khí
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="account-option">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="account_type"
                                   id="brand"
                                   value="brand"
                                   {{ old('account_type', $sessionData['account_type'] ?? '') == 'brand' ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label" for="brand">
                                <strong>Nhãn hàng</strong>
                                <span class="account-description">
                                    Quảng bá thương hiệu và sản phẩm trong ngành cơ khí
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="business-notice mt-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Tài khoản doanh nghiệp cần cung cấp thông tin công ty và chờ xác minh từ admin. Sau khi xác minh thành công, bạn có thể được nâng cấp lên <strong>Đối tác đã xác thực</strong>.
                    </div>
                </div>
            </div>

            @error('account_type')
                <div class="invalid-feedback d-block">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Section: Terms & Conditions --}}
        <div class="form-section mb-4">
            <div class="form-check">
                <input class="form-check-input @error('terms') is-invalid @enderror"
                       type="checkbox"
                       id="terms"
                       name="terms"
                       value="1"
                       {{ old('terms') ? 'checked' : '' }}
                       required>
                <label class="form-check-label" for="terms">
                    Tôi đồng ý với
                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">
                        Điều khoản sử dụng
                    </a> và
                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#privacyModal">
                        Chính sách bảo mật
                    </a> của MechaMap
                </label>
                @error('terms')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </form>

</x-registration-wizard>

{{-- Terms Modal --}}
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản sử dụng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Điều khoản sử dụng MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Privacy Modal --}}
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">Chính sách bảo mật</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Chính sách bảo mật MechaMap sẽ được hiển thị ở đây...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/frontend/registration-wizard.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/frontend/registration-wizard.js') }}"></script>
@endpush
