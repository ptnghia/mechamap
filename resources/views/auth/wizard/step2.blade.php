@extends('layouts.app')

@section('title', __('auth.register.step2_title'))

@section('full-width-content')
<x-registration-wizard
    :current-step="$step"
    :total-steps="$totalSteps"
    :progress="$progress"
    title="{{ __('auth.register.wizard_title') }}"
    subtitle="{{ __('auth.register.step2_subtitle') }}"
    next-button-text="{{ __('auth.register.complete_button') }}"
    back-button-text="{{ __('auth.register.back_button') }}"
    :show-back-button="true"
    form-id="step2Form"
    :session-data="$sessionData">

    <form id="step2Form" method="POST" action="{{ route('register.wizard.step2') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Account Type Display --}}
        <div class="account-type-display mb-4">
            <div class="alert alert-primary">
                <i class="fas fa-building me-2"></i>
                <strong>{{ __('auth.register.account_type_label') }}:</strong>
                @switch($accountType)
                    @case('manufacturer')
                        {{ __('auth.register.manufacturer_role') }}
                        @break
                    @case('supplier')
                        {{ __('auth.register.supplier_role') }}
                        @break
                    @case('brand')
                        {{ __('auth.register.brand_role') }}
                        @break
                    @default
                        {{ __('auth.register.business_partner_title') }}
                @endswitch
            </div>
        </div>

        {{-- Section: Company Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-building text-primary me-2"></i>
                {{ __('auth.register.company_info_title') }}
            </h3>
            <p class="section-description text-muted mb-4">
                {{ __('auth.register.company_info_description') }}
            </p>

            {{-- Company Name --}}
            <div class="mb-3">
                <label for="company_name" class="form-label required">
                    <i class="fas fa-building me-1"></i>
                    {{ __('auth.register.company_name_label') }}
                </label>
                <input type="text"
                       class="form-control @error('company_name') is-invalid @enderror"
                       id="company_name"
                       name="company_name"
                       value="{{ old('company_name', $sessionData['company_name'] ?? '') }}"
                       placeholder="{{ __('auth.register.company_name_placeholder') }}"
                       required>
                <div class="invalid-feedback">
                    @error('company_name'){{ $message }}@enderror
                </div>
                <small class="form-text text-muted">
                    {{ __('auth.register.company_name_help') }}
                </small>
            </div>

            <div class="row">
                {{-- Business License --}}
                <div class="col-md-6 mb-3">
                    <label for="business_license" class="form-label required">
                        <i class="fas fa-certificate me-1"></i>
                        {{ __('auth.register.business_license_label') }}
                    </label>
                    <input type="text"
                           class="form-control @error('business_license') is-invalid @enderror"
                           id="business_license"
                           name="business_license"
                           value="{{ old('business_license', $sessionData['business_license'] ?? '') }}"
                           placeholder="{{ __('auth.register.business_license_placeholder') }}"
                           required>
                    <div class="invalid-feedback">
                        @error('business_license'){{ $message }}@enderror
                    </div>
                </div>

                {{-- Tax Code --}}
                <div class="col-md-6 mb-3">
                    <label for="tax_code" class="form-label required">
                        <i class="fas fa-hashtag me-1"></i>
                        {{ __('auth.register.tax_code_label') }}
                    </label>
                    <input type="text"
                           class="form-control @error('tax_code') is-invalid @enderror"
                           id="tax_code"
                           name="tax_code"
                           value="{{ old('tax_code', $sessionData['tax_code'] ?? '') }}"
                           placeholder="{{ __('auth.register.tax_code_placeholder') }}"
                           pattern="[0-9]{10,13}"
                           required>
                    <div class="invalid-feedback">
                        @error('tax_code'){{ $message }}@enderror
                    </div>
                    <small class="form-text text-muted">
                        {{ __('auth.register.tax_code_help') }}
                    </small>
                </div>
            </div>

            {{-- Business Description --}}
            <div class="mb-3">
                <label for="business_description" class="form-label required">
                    <i class="fas fa-align-left me-1"></i>
                    {{ __('auth.register.company_description_label') }}
                </label>

                <x-tinymce-editor
                    name="business_description"
                    id="business_description"
                    :value="old('business_description', $sessionData['business_description'] ?? '')"
                    placeholder="Mô tả chi tiết về hoạt động kinh doanh, sản phẩm/dịch vụ chính của công ty..."
                    context="minimal"
                    :height="150"
                    :required="true"
                    class="@error('business_description') is-invalid @enderror" />

                <small class="form-text text-muted">
                    {{ __('auth.register.company_description_help') }}
                </small>
            </div>

            {{-- Business Categories --}}
            <div class="mb-3">
                <label class="form-label required">
                    <i class="fas fa-tags me-1"></i>
                    {{ __('auth.register.business_field_label') }}
                </label>
                <div class="business-categories">
                    @php
                        $categories = \App\Models\BusinessCategory::active()->ordered()->get();
                        $selectedCategories = old('business_categories', $sessionData['business_categories'] ?? []);
                    @endphp

                    <div class="row">
                        @foreach($categories as $category)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="business_categories[]"
                                           id="category_{{ $category->key }}"
                                           value="{{ $category->key }}"
                                           {{ in_array($category->key, $selectedCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $category->key }}">
                                        <i class="{{ $category->icon }}" style="color: {{ $category->color }}"></i>
                                        {{ $category->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @error('business_categories')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
                <small class="form-text text-muted">
                    {{ __('auth.register.business_field_help') }}
                </small>
            </div>
        </div>

        {{-- Section: Contact Information --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-address-book text-primary me-2"></i>
                {{ __('auth.register.contact_info_title') }}
            </h3>
            <p class="section-description text-muted mb-4">
                {{ __('auth.register.contact_info_description') }}
            </p>

            <div class="row">
                {{-- Business Phone --}}
                <div class="col-md-6 mb-3">
                    <label for="business_phone" class="form-label">
                        <i class="fas fa-phone me-1"></i>
                        {{ __('auth.register.company_phone') }}
                    </label>
                    <input type="tel"
                           class="form-control @error('business_phone') is-invalid @enderror"
                           id="business_phone"
                           name="business_phone"
                           value="{{ old('business_phone', $sessionData['business_phone'] ?? '') }}"
                           placeholder="Ví dụ: +84 123 456 789">
                    <div class="invalid-feedback">
                        @error('business_phone'){{ $message }}@enderror
                    </div>
                </div>

                {{-- Business Email --}}
                <div class="col-md-6 mb-3">
                    <label for="business_email" class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        {{ __('auth.register.company_email_label') }}
                    </label>
                    <input type="email"
                           class="form-control @error('business_email') is-invalid @enderror"
                           id="business_email"
                           name="business_email"
                           value="{{ old('business_email', $sessionData['business_email'] ?? '') }}"
                           placeholder="Ví dụ: info@company.com">
                    <div class="invalid-feedback">
                        @error('business_email'){{ $message }}@enderror
                    </div>
                    <small class="form-text text-muted">
                        {{ __('auth.register.company_email_help') }}
                    </small>
                </div>
            </div>

            {{-- Business Address --}}
            <div class="mb-3">
                <label for="business_address" class="form-label">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ __('auth.register.company_address') }}
                </label>
                <textarea class="form-control @error('business_address') is-invalid @enderror"
                          id="business_address"
                          name="business_address"
                          rows="3"
                          placeholder="Nhập địa chỉ đầy đủ của công ty...">{{ old('business_address', $sessionData['business_address'] ?? '') }}</textarea>
                <div class="invalid-feedback">
                    @error('business_address'){{ $message }}@enderror
                </div>
            </div>
        </div>

        {{-- Section: Document Upload --}}
        <div class="form-section mb-4">
            <h3 class="section-title">
                <i class="fas fa-file-upload text-primary me-2"></i>
                {{ __('auth.register.verification_docs_title') }}
            </h3>
            <p class="section-description text-muted mb-4">
                {{ __('auth.register.verification_docs_description') }}
            </p>

            <x-advanced-file-upload
                name="verification_documents"
                :file-types="['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']"
                max-size="10MB"
                :multiple="true"
                :max-files="5"
                :required="false"
                :show-progress="true"
                :show-preview="true"
                :drag-drop="true"
                id="verification-docs-upload"
                context="document"
                upload-text="{{ __('auth.register.verification_docs_title') }}"
                accept-description="{{ __('auth.register.verification_docs_description') }}"
            />

            @error('verification_documents')
                <div class="invalid-feedback d-block mt-2">
                    {{ $message }}
                </div>
            @enderror

            <div class="mt-2">
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    {{ __('auth.register.document_suggestions') }}
                </small>
                <small class="form-text text-primary d-block mt-1">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    {{ __('auth.register.file_upload_limits') }}
                </small>
            </div>
        </div>

        {{-- Verification Notice --}}
        <div class="verification-notice">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>{{ __('auth.register.important_notes_title') }}:</strong>
                <ul class="mb-0 mt-2">
                    <li>{{ __('auth.register.note_verification_required') }}</li>
                    <li>{{ __('auth.register.note_verification_time') }}</li>
                    <li>{{ __('auth.register.note_email_notification') }}</li>
                    <li>{{ __('auth.register.note_pending_access') }}</li>
                </ul>
            </div>
        </div>
    </form>

</x-registration-wizard>
@endsection

@push('scripts')
<script src="{{ asset('js/frontend/registration-wizard.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Business categories validation
    const categoryCheckboxes = document.querySelectorAll('input[name="business_categories[]"]');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('input[name="business_categories[]"]:checked').length;
            if (checkedCount > 5) {
                this.checked = false;
                alert('{{ __("auth.register.max_categories_error") }}');
            }
        });
    });

    // Form validation enhancement
    const form = document.getElementById('step2Form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate TinyMCE content
            const businessDescEditor = tinymce.get('business_description');
            if (businessDescEditor) {
                const content = businessDescEditor.getContent().trim();
                if (!content) {
                    e.preventDefault();
                    alert('{{ __("auth.register.business_description_required") }}');
                    businessDescEditor.focus();
                    return false;
                }
            }
        });
    }
});
</script>
@endpush
