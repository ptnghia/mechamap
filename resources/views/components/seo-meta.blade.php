@props(['locale' => null])
@php
    $seoService = app(\App\Services\MultilingualSeoService::class);
    $seoData = $seoService->getSeoData(request(), $locale);
    // Get additional SEO settings from middleware (for backward compatibility)
    $additionalSeo = $seo ?? [];
@endphp
{{-- Basic Meta Tags --}}
@if(!empty($seoData['title']))
    <title>{{ $seoData['title'] }}</title>
@endif
@if(!empty($seoData['description']))
    <meta name="description" content="{{ $seoData['description'] }}">
@endif
@if(!empty($seoData['keywords']))
    <meta name="keywords" content="{{ $seoData['keywords'] }}">
@endif
@if(!empty($seoData['canonical_url']))
    <link rel="canonical" href="{{ $seoData['canonical_url'] }}">
@endif
@if(!empty($seoData['no_index']))
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow">
@endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="MechaMap">
@if(!empty($seoData['og_title']))
    <meta property="og:title" content="{{ $seoData['og_title'] }}">
@endif
@if(!empty($seoData['og_description']))
    <meta property="og:description" content="{{ $seoData['og_description'] }}">
@endif
@if(!empty($seoData['og_image']))
    <meta property="og:image" content="{{ url($seoData['og_image']) }}">
    <meta property="og:image:alt" content="{{ $seoData['og_title'] ?? 'MechaMap' }}">
@else
    <meta property="og:image" content="{{ asset('images/seo/mechamap-og-default.jpg') }}">
    <meta property="og:image:alt" content="MechaMap - Vietnam Mechanical Engineering Community">
@endif
@if(!empty($additionalSeo['facebook_app_id']))
    <!-- Facebook App ID -->
    <meta property="fb:app_id" content="{{ $additionalSeo['facebook_app_id'] }}">
@endif
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="{{ $additionalSeo['twitter_card'] ?? 'summary_large_image' }}">
@if(!empty($additionalSeo['twitter_username']))
    <meta name="twitter:site" content="{{ '@' . $additionalSeo['twitter_username'] }}">
    <meta name="twitter:creator" content="{{ '@' . $additionalSeo['twitter_username'] }}">
@else
    <meta name="twitter:site" content="@mechamap_vn">
@endif
    <meta name="twitter:url" content="{{ request()->url() }}">
@if(!empty($seoData['twitter_title']))
    <meta name="twitter:title" content="{{ $seoData['twitter_title'] }}">
@endif
@if(!empty($seoData['twitter_description']))
    <meta name="twitter:description" content="{{ $seoData['twitter_description'] }}">
@endif
@if(!empty($seoData['twitter_image']))
    <meta name="twitter:image" content="{{ url($seoData['twitter_image']) }}">
@else
    <meta name="twitter:image" content="{{ asset('images/seo/mechamap-twitter-default.jpg') }}">
@endif
@php
    $currentUrl = request()->url();
    $currentLocale = app()->getLocale();
    $availableLocales = ['vi', 'en'];
@endphp
    <!-- HREFLANG TAGS -->
@foreach($availableLocales as $hreflangLocale)
@if($hreflangLocale === $currentLocale)
    <link rel="alternate" hreflang="{{ $hreflangLocale }}" href="{{ $currentUrl }}">
@else
    <link rel="alternate" hreflang="{{ $hreflangLocale }}" href="{{ $currentUrl }}?lang={{ $hreflangLocale }}">
@endif
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $currentUrl }}">
    <meta name="author" content="{{ __('ui.layout.meta_author') }}">
    <meta name="generator" content="Laravel {{ app()->version() }}">
    <meta name="theme-color" content="#0d6efd">
@if(!empty($additionalSeo['google_search_console_id']))
    <meta name="google-site-verification" content="{{ $additionalSeo['google_search_console_id'] }}">
@endif
@if(!empty($seoData['extra_meta']))
    {!! $seoData['extra_meta'] !!}
@endif
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "MechaMap",
        "description": "{{ $seoData['description'] ?? 'Vietnam Mechanical Engineering Community' }}",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/search?q={search_term_string}') }}",
            "query-input": "required name=search_term_string"
        },
        "publisher": {
            "@type": "Organization",
            "name": "MechaMap",
            "url": "{{ url('/') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/logo/mechamap-logo.png') }}"
            }
        }
    }
    </script>
    <!-- Google Search Console Verification -->
@if(!empty($seo['google_search_console_id'] ?? ''))
    <meta name="google-site-verification" content="{{ $seo['google_search_console_id'] }}">
@endif
    <!-- Favicon -->
    <link rel="icon" href="{{ get_favicon_url() }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ get_favicon_url() }}" type="image/x-icon">
