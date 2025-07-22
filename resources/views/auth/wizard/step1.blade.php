@extends('layouts.app')

@section('title', __('auth.register.step1_title'))

@section('full-width-content')
<x-registration-wizard
    :current-step="$step"
    :total-steps="$totalSteps"
    :progress="$progress"
    title="{{ __('auth.register.wizard_title') }}"
    subtitle="{{ __('auth.register.step1_subtitle') }}"
    next-button-text="{{ __('auth.register.continue_button') }}"
    :show-back-button="false"
    form-id="step1Form"
    :session-data="$sessionData">

    <form id="step1Form" method="POST" action="{{ route('register.wizard.step1') }}" novalidate>
        @csrf

        {{-- Section: Personal Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-user text-primary me-2"></i>
                {{ __('auth.register.personal_info_title') }}
            </h3>
            <p class="section-description text-muted mb-4">
                {{ __('auth.register.personal_info_description') }}
            </p>

            <div class="row">
                {{-- Full Name --}}
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label required">
                        <i class="fas fa-user me-1"></i>
                        {{ __('auth.full_name_label') }}
                    </label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $sessionData['name'] ?? '') }}"
                           placeholder="{{ __('auth.full_name_placeholder') }}"
                           required
                           autocomplete="name">
                    <div class="invalid-feedback" id="name-error">
                        @error('name'){{ $message }}@enderror
                    </div>
                    <div class="valid-feedback" id="name-success" style="display: none;">
                        <i class="fas fa-check"></i> {{ __('auth.register.name_valid') }}
                    </div>
                </div>

                {{-- Username --}}
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label required">
                        <i class="fas fa-at me-1"></i>
                        {{ __('auth.username_label') }}
                    </label>
                    <input type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           id="username"
                           name="username"
                           value="{{ old('username', $sessionData['username'] ?? '') }}"
                           placeholder="{{ __('auth.username_placeholder') }}"
                           required
                           autocomplete="username">
                    <div class="invalid-feedback" id="username-error">
                        @error('username'){{ $message }}@enderror
                    </div>
                    <div class="valid-feedback" id="username-success" style="display: none;">
                        <i class="fas fa-check"></i> {{ __('auth.register.username_available') }}
                    </div>
                    <small class="form-text text-muted">
                        {{ __('auth.username_help') }}
                    </small>
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label required">
                    <i class="fas fa-envelope me-1"></i>
                    {{ __('auth.email_label') }}
                </label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $sessionData['email'] ?? '') }}"
                       placeholder="{{ __('auth.email_placeholder') }}"
                       required
                       autocomplete="email">
                <div class="invalid-feedback" id="email-error">
                    @error('email'){{ $message }}@enderror
                </div>
                <div class="valid-feedback" id="email-success" style="display: none;">
                    <i class="fas fa-check"></i> {{ __('auth.register.email_valid') }}
                </div>
                <small class="form-text text-muted">
                    {{ __('auth.register.email_help') }}
                </small>
            </div>

            <div class="row">
                {{-- Password --}}
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label required">
                        <i class="fas fa-lock me-1"></i>
                        {{ __('auth.password_label') }}
                    </label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               placeholder="{{ __('auth.password_placeholder') }}"
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
                        {{ __('auth.password_help') }}
                    </small>
                </div>

                {{-- Confirm Password --}}
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label required">
                        <i class="fas fa-lock me-1"></i>
                        {{ __('auth.confirm_password_label') }}
                    </label>
                    <input type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="{{ __('auth.confirm_password_placeholder') }}"
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
                {{ __('auth.register.account_type_title') }}
            </h3>
            <p class="section-description text-muted mb-4">
                {{ __('auth.register.account_type_description') }}
            </p>

            {{-- Community Members --}}
            <div class="account-type-group mb-3" data-group="community">
                <div class="account-type-header">
                    <h4 class="account-group-title">
                        <i class="fas fa-users text-warning me-2"></i>
                        {{ __('auth.register.community_member_title') }}
                    </h4>
                    <p class="account-group-description">
                        {{ __('auth.register.community_member_description') }}
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
                                <strong>{{ __('auth.register.member_role') }}</strong>
                                <span class="badge bg-primary ms-2">{{ __('auth.register.recommended') }}</span>
                                <span class="account-description">
                                    {{ __('auth.register.member_role_desc') }}
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
                                <strong>{{ __('auth.register.guest_role') }}</strong>
                                <span class="account-description">
                                    {{ __('auth.register.guest_role_desc') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="upgrade-notice mt-3">
                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ __("common.note") }}:</strong> {!! __('auth.register.note_community') !!}
                    </div>
                </div>
            </div>

            {{-- Business Partners --}}
            <div class="account-type-group mb-3" data-group="business">
                <div class="account-type-header">
                    <h4 class="account-group-title">
                        <i class="fas fa-building text-primary me-2"></i>
                        {{ __('auth.register.business_partner_title') }}
                    </h4>
                    <p class="account-group-description">
                        {{ __('auth.register.business_partner_description') }}
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
                                <strong>{{ __('auth.register.manufacturer_role') }}</strong>
                                <span class="account-description">
                                    {{ __('auth.register.manufacturer_role_desc') }}
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
                                <strong>{{ __('auth.register.supplier_role') }}</strong>
                                <span class="account-description">
                                    {{ __('auth.register.supplier_role_desc') }}
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
                                <strong>{{ __('auth.register.brand_role') }}</strong>
                                <span class="account-description">
                                    {{ __('auth.register.brand_role_desc') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="business-notice mt-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ __("common.note") }}:</strong> {!! __('auth.register.note_business') !!}
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
                    {!! __('auth.register.terms_agreement') !!}
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
