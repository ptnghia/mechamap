@extends('admin.layouts.dason')

@section('title', 'Cấu hình SEO nâng cao')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Cấu hình SEO nâng cao</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Cấu hình SEO nâng cao</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Quay lại') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Điều hướng') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.seo.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> {{ __('Cấu hình chung') }}
                        </a>
                        <a href="{{ route('admin.seo.robots') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-robot me-2"></i> {{ __('Robots.txt') }}
                        </a>
                        <a href="{{ route('admin.seo.sitemap') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-sitemap me-2"></i> {{ __('Sitemap') }}
                        </a>
                        <a href="{{ route('admin.seo.social') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-share me-2"></i> {{ __('Social Media') }}
                        </a>
                        <a href="{{ route('admin.seo.advanced') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-cog-wide-connected me-2"></i> {{ __('Cấu hình nâng cao') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Hướng dẫn') }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ __('Cấu hình nâng cao cho phép bạn thêm các script tùy chỉnh vào trang web của bạn.') }}
                    </p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle-fill me-2"></i>
                        {{ __('Cẩn thận khi thêm các script tùy chỉnh. Script không hợp lệ có thể làm hỏng trang web của bạn.') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Cấu hình nâng cao') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.seo.update-advanced') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="header_scripts" class="form-label">{{ __('Header Scripts') }}</label>
                            <textarea class="form-control font-monospace @error('header_scripts') is-invalid @enderror" id="header_scripts" name="header_scripts" rows="6" style="resize: vertical;">{{ old('header_scripts', $settings['header_scripts'] ?? '') }}</textarea>
                            <div class="form-text">{{ __('Các script này sẽ được thêm vào thẻ <head> của trang web. Ví dụ: Google Analytics, Facebook Pixel, v.v.') }}</div>
                            @error('header_scripts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="footer_scripts" class="form-label">{{ __('Footer Scripts') }}</label>
                            <textarea class="form-control font-monospace @error('footer_scripts') is-invalid @enderror" id="footer_scripts" name="footer_scripts" rows="6" style="resize: vertical;">{{ old('footer_scripts', $settings['footer_scripts'] ?? '') }}</textarea>
                            <div class="form-text">{{ __('Các script này sẽ được thêm vào cuối thẻ <body> của trang web.') }}</div>
                            @error('footer_scripts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="custom_css" class="form-label">{{ __('Custom CSS') }}</label>
                            <textarea class="form-control font-monospace @error('custom_css') is-invalid @enderror" id="custom_css" name="custom_css" rows="6" style="resize: vertical;">{{ old('custom_css', $settings['custom_css'] ?? '') }}</textarea>
                            <div class="form-text">{{ __('CSS tùy chỉnh sẽ được thêm vào trang web.') }}</div>
                            @error('custom_css')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="canonical_url" class="form-label">{{ __('Canonical URL') }}</label>
                            <input type="url" class="form-control @error('canonical_url') is-invalid @enderror" id="canonical_url" name="canonical_url" value="{{ old('canonical_url', $settings['canonical_url'] ?? '') }}">
                            <div class="form-text">{{ __('URL chính thức của trang web. Để trống để sử dụng URL hiện tại.') }}</div>
                            @error('canonical_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('Lưu cấu hình') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Mẫu script phổ biến') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Google Analytics (GA4)') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>&lt;!-- Google Analytics --&gt;
&lt;script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"&gt;&lt;/script&gt;
&lt;script&gt;
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
&lt;/script&gt;</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-target="header_scripts" data-template="ga4">
                                        <i class="fas fa-clipboard me-1"></i> {{ __('Sao chép') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Facebook Pixel') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>&lt;!-- Facebook Pixel --&gt;
&lt;script&gt;
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'XXXXXXXXXXXXXXX');
  fbq('track', 'PageView');
&lt;/script&gt;</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-target="header_scripts" data-template="facebook-pixel">
                                        <i class="fas fa-clipboard me-1"></i> {{ __('Sao chép') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Google Tag Manager') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>&lt;!-- Google Tag Manager --&gt;
&lt;script&gt;(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXX');&lt;/script&gt;
&lt;!-- End Google Tag Manager --&gt;</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-target="header_scripts" data-template="gtm-header">
                                        <i class="fas fa-clipboard me-1"></i> {{ __('Sao chép') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Google Tag Manager (Body)') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>&lt;!-- Google Tag Manager (noscript) --&gt;
&lt;noscript&gt;&lt;iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"&gt;&lt;/iframe&gt;&lt;/noscript&gt;
&lt;!-- End Google Tag Manager (noscript) --&gt;</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-target="footer_scripts" data-template="gtm-body">
                                        <i class="fas fa-clipboard me-1"></i> {{ __('Sao chép') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Templates
    const templates = {
        'ga4': `<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>`,
        'facebook-pixel': `<!-- Facebook Pixel -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', 'XXXXXXXXXXXXXXX');
  fbq('track', 'PageView');
</script>`,
        'gtm-header': `<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXXX');</script>
<!-- End Google Tag Manager -->`,
        'gtm-body': `<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-XXXXXXX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->`
    };

    // Copy template to editor
    document.querySelectorAll('.copy-template').forEach(button => {
        button.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            const template = this.getAttribute('data-template');
            
            if (templates[template] && document.getElementById(target)) {
                const textarea = document.getElementById(target);
                const currentValue = textarea.value;
                
                if (currentValue) {
                    textarea.value = currentValue + '\n\n' + templates[template];
                } else {
                    textarea.value = templates[template];
                }
                
                // Show success message
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i> {{ __('Đã sao chép') }}';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-primary');
                }, 2000);
            }
        });
    });
</script>
@endpush
