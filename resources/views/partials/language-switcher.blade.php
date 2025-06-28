@php
    use App\Services\LanguageService;
    $currentLanguage = LanguageService::getCurrentLanguageInfo();
    $otherLanguages = LanguageService::getOtherLanguages();
@endphp

<div class="language-switcher dropdown">
    <button class="btn btn-link dropdown-toggle p-0 border-0 bg-transparent"
            type="button"
            id="languageDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="{{ __('messages.language.switch_language') }}">
        <i class="flag-icon flag-icon-{{ $currentLanguage['flag'] }} me-1"></i>
        <span class="d-none d-md-inline">{{ $currentLanguage['name'] }}</span>
        <i class="fas fa-chevron-down ms-1 small"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li class="dropdown-header">
            <i class="fas fa-globe me-2"></i>
            {{ __('messages.language.select_language') }}
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
                {{ __('messages.language.auto_detect') }}
            </a>
        </li>
    </ul>
</div>

<style>
.language-switcher .dropdown-toggle {
    color: inherit;
    text-decoration: none;
}

.language-switcher .dropdown-toggle:hover {
    color: var(--bs-primary);
}

.language-switcher .dropdown-toggle:focus {
    box-shadow: none;
}

.language-switcher .dropdown-menu {
    min-width: 200px;
    border: 1px solid rgba(0,0,0,.15);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

.language-switcher .dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.language-switcher .dropdown-item:hover {
    background-color: var(--bs-primary);
    color: white;
}

.language-switcher .dropdown-item:hover .text-muted {
    color: rgba(255,255,255,0.8) !important;
}

.flag-icon {
    width: 20px;
    height: 15px;
    background-size: cover;
    background-position: center;
    display: inline-block;
    border-radius: 2px;
}

.flag-icon-vn {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjEiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMSAxNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIxIiBoZWlnaHQ9IjE1IiBmaWxsPSIjREEwMjBFIi8+Cjxwb2x5Z29uIHBvaW50cz0iMTAuNSw0IDEyLjM1LDguNSA4LjY1LDguNSIgZmlsbD0iI0ZGRkYwMCIvPgo8L3N2Zz4K');
}

.flag-icon-us {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjEiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMSAxNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIxIiBoZWlnaHQ9IjE1IiBmaWxsPSIjQjIyMjM0Ii8+CjxyZWN0IHdpZHRoPSI5IiBoZWlnaHQ9IjgiIGZpbGw9IiMzQzNDNDEiLz4KPHN0cmlwZSB3aWR0aD0iMjEiIGhlaWdodD0iMSIgZmlsbD0iI0ZGRkZGRiIvPgo8L3N2Zz4K');
}

@media (max-width: 768px) {
    .language-switcher .dropdown-menu {
        min-width: 150px;
    }
}
</style>

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
            showNotification(data.message || '{{ __("messages.language.switched_successfully") }}', 'success');
            // Reload trang để áp dụng ngôn ngữ mới
            setTimeout(() => window.location.reload(), 500);
        } else {
            // Hiển thị thông báo lỗi
            showNotification(data.message || '{{ __("messages.language.switch_failed") }}', 'error');
            // Khôi phục button
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Language switch error:', error);
        showNotification('{{ __("messages.language.switch_failed") }}', 'error');
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
        showNotification('{{ __("messages.language.auto_detect_failed") }}', 'error');
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
