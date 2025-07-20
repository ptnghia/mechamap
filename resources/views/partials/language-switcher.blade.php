@php
    use App\Services\LanguageService;
    $currentLanguage = LanguageService::getCurrentLanguageInfo();
    $otherLanguages = LanguageService::getOtherLanguages();
@endphp

<div class="language-switcher dropdown">
    <button class="btn btn-link dropdown-toggle border-0 bg-transparent"
            type="button"
            id="languageDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="{{ __('ui.common.language.switch') }}">
        <i class="flag-icon flag-icon-{{ $currentLanguage['flag'] }} me-1"></i>
        <!--span class="d-none d-md-inline">{{ $currentLanguage['name'] }}</span-->
    </button>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li class="dropdown-header">
            <i class="fas fa-globe me-2"></i>
            {{ __('ui.common.language.select') }}
        </li>
        <li><hr class="dropdown-divider"></li>

        @foreach($otherLanguages as $locale => $language)
            <li>
                <a class="dropdown-item language-option"
                   href="{{ route('language.switch', $locale) }}"
                   data-locale="{{ $locale }}"
                   onclick="switchLanguage('{{ $locale }}'); return false;">
                    <i class="flag-icon flag-icon-{{ $language['flag'] }} me-2"></i>
                    {{ $language['name'] }}
                    @if($locale === 'vi')
                        <small class="text-muted ms-1">(Mặc định)</small>
                    @endif
                </a>
            </li>
        @endforeach

        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-muted small"
               href="#"
               onclick="autoDetectLanguage(); return false;">
                <i class="fas fa-magic me-2"></i>
                {{ __('ui.common.language.auto_detect') }}
            </a>
        </li>
    </ul>
</div>


<script>
/**
 * Chuyển đổi ngôn ngữ
 */
function switchLanguage(locale) {
    // Hiển thị loading
    const button = document.getElementById('languageDropdown');
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    // Gửi request với Accept header để nhận JSON
    fetch(`{{ url('/language/switch') }}/${locale}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Network response was not ok');
        }
    })
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            showNotification(data.message || '{{ __("core/messages.language.switched_successfully") }}', 'success');
            // Reload trang để áp dụng ngôn ngữ mới
            setTimeout(() => window.location.reload(), 500);
        } else {
            // Hiển thị thông báo lỗi
            showNotification(data.message || '{{ __("core/messages.language.switch_failed") }}', 'error');
            // Khôi phục button
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Language switch error:', error);
        showNotification('{{ __("core/messages.language.switch_failed") }}', 'error');
        // Khôi phục button
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

/**
 * Tự động phát hiện ngôn ngữ
 */
function autoDetectLanguage() {
    fetch('{{ route("language.auto-detect") }}', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'info');
        }
    })
    .catch(error => {
        console.error('Auto detect error:', error);
        showNotification('{{ __("core/messages.language.auto_detect_failed") }}', 'error');
    });
}

/**
 * Hiển thị thông báo
 */
function showNotification(message, type = 'info') {
    // Tạo toast notification đơn giản
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Tự động ẩn sau 5 giây
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}
</script>
