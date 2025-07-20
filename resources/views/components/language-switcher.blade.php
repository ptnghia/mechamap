@props(['position' => 'dropdown']) {{-- dropdown, inline, modal --}}

@php
    $currentLocale = app()->getLocale();
    $availableLocales = [
        'vi' => [
            'name' => __('language.vietnamese'),
            'flag' => '🇻🇳',
            'code' => 'vi'
        ],
        'en' => [
            'name' => __('language.english'),
            'flag' => '🇺🇸',
            'code' => 'en'
        ]
    ];
@endphp

@if($position === 'dropdown')
<!-- Language Switcher Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="me-1">{{ $availableLocales[$currentLocale]['flag'] }}</span>
        <span class="d-none d-md-inline">{{ $availableLocales[$currentLocale]['name'] }}</span>
        <span class="d-md-none">{{ strtoupper($currentLocale) }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li><h6 class="dropdown-header">
            <i class="fa-solid fa-language me-2"></i>{{ __('language.select_language') }}
        </h6></li>
        @foreach($availableLocales as $locale => $data)
            <li>
                <a class="dropdown-item {{ $locale === $currentLocale ? 'active' : '' }}" 
                   href="#" 
                   onclick="switchLanguage('{{ $locale }}')">
                    <span class="me-2">{{ $data['flag'] }}</span>
                    {{ $data['name'] }}
                    @if($locale === $currentLocale)
                        <i class="fa-solid fa-check ms-auto text-success"></i>
                    @endif
                </a>
            </li>
        @endforeach
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-muted" href="#" onclick="autoDetectLanguage()">
                <i class="fa-solid fa-magic me-2"></i>{{ __('language.auto_detect') }}
            </a>
        </li>
    </ul>
</li>

@elseif($position === 'inline')
<!-- Inline Language Switcher -->
<div class="language-switcher-inline d-flex align-items-center">
    <span class="me-2 text-muted small">{{ __('language.select_language') }}:</span>
    @foreach($availableLocales as $locale => $data)
        <button type="button" 
                class="btn btn-sm {{ $locale === $currentLocale ? 'btn-primary' : 'btn-outline-secondary' }} me-1" 
                onclick="switchLanguage('{{ $locale }}')"
                title="{{ $data['name'] }}">
            <span class="me-1">{{ $data['flag'] }}</span>
            {{ strtoupper($locale) }}
        </button>
    @endforeach
</div>

@elseif($position === 'modal')
<!-- Modal Language Switcher -->
<div class="modal fade" id="languageModal" tabindex="-1" aria-labelledby="languageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="languageModalLabel">
                    <i class="fa-solid fa-language me-2"></i>{{ __('language.select_language') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.common.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    @foreach($availableLocales as $locale => $data)
                        <a href="#" 
                           class="list-group-item list-group-item-action {{ $locale === $currentLocale ? 'active' : '' }}"
                           onclick="switchLanguage('{{ $locale }}'); bootstrap.Modal.getInstance(document.getElementById('languageModal')).hide();">
                            <div class="d-flex align-items-center">
                                <span class="me-3 fs-4">{{ $data['flag'] }}</span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $data['name'] }}</div>
                                    <small class="text-muted">{{ strtoupper($locale) }}</small>
                                </div>
                                @if($locale === $currentLocale)
                                    <i class="fa-solid fa-check text-success"></i>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-3 pt-3 border-top">
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="autoDetectLanguage()">
                        <i class="fa-solid fa-magic me-2"></i>{{ __('language.auto_detect') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
/**
 * Switch language function
 */
function switchLanguage(locale) {
    // Show loading state
    const loadingToast = showToast('{{ __("ui/common.loading") }}...', 'info');
    
    // Make AJAX request to switch language
    fetch('/language/switch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            locale: locale
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide loading toast
            if (loadingToast) loadingToast.hide();
            
            // Show success message
            showToast('{{ __("language.switched_successfully") }}', 'success');
            
            // Reload page to apply new language
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            throw new Error(data.message || '{{ __("language.switch_failed") }}');
        }
    })
    .catch(error => {
        console.error('Language switch error:', error);
        
        // Hide loading toast
        if (loadingToast) loadingToast.hide();
        
        // Show error message
        showToast('{{ __("language.switch_failed") }}', 'error');
    });
}

/**
 * Auto detect language from browser
 */
function autoDetectLanguage() {
    const browserLang = navigator.language || navigator.userLanguage;
    const detectedLocale = browserLang.startsWith('vi') ? 'vi' : 'en';
    
    if (detectedLocale !== '{{ $currentLocale }}') {
        switchLanguage(detectedLocale);
    } else {
        showToast('{{ __("language.auto_detected") }}', 'info');
    }
}

/**
 * Show toast notification (requires Bootstrap Toast or similar)
 */
function showToast(message, type = 'info') {
    // This is a placeholder - implement based on your toast system
    console.log(`Toast [${type}]: ${message}`);
    
    // Example with Bootstrap Toast (if available)
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'primary'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Add to toast container (create if doesn't exist)
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        const toastElement = document.createElement('div');
        toastElement.innerHTML = toastHtml;
        toastContainer.appendChild(toastElement.firstElementChild);
        
        const toast = new bootstrap.Toast(toastElement.firstElementChild);
        toast.show();
        
        return toast;
    }
    
    return null;
}
</script>
@endpush
