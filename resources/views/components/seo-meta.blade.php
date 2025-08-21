@props(['locale' => null])

@php
    $seoService = app(\App\Services\MultilingualSeoService::class);
    $seoData = $seoService->getSeoData(request(), $locale);
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

{{-- Canonical URL --}}
@if(!empty($seoData['canonical_url']))
    <link rel="canonical" href="{{ $seoData['canonical_url'] }}">
@endif

{{-- Robots Meta --}}
@if(!empty($seoData['no_index']))
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow">
@endif

{{-- Open Graph Tags --}}
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
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
    <meta property="og:image:alt" content="{{ $seoData['og_title'] ?? 'MechaMap' }}">
@else
    <meta property="og:image" content="{{ asset('images/seo/mechamap-og-default.jpg') }}">
    <meta property="og:image:alt" content="MechaMap - Vietnam Mechanical Engineering Community">
@endif

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@mechamap_vn">

@if(!empty($seoData['twitter_title']))
    <meta name="twitter:title" content="{{ $seoData['twitter_title'] }}">
@endif

@if(!empty($seoData['twitter_description']))
    <meta name="twitter:description" content="{{ $seoData['twitter_description'] }}">
@endif

@if(!empty($seoData['twitter_image']))
    <meta name="twitter:image" content="{{ $seoData['twitter_image'] }}">
@else
    <meta name="twitter:image" content="{{ asset('images/seo/mechamap-twitter-default.jpg') }}">
@endif

{{-- Hreflang Tags for Multilingual --}}
<link rel="alternate" hreflang="vi" href="{{ request()->url() }}">
<link rel="alternate" hreflang="en" href="{{ request()->url() }}">
<link rel="alternate" hreflang="x-default" href="{{ request()->url() }}">

{{-- Additional Meta Tags --}}
<meta name="author" content="MechaMap">
<meta name="generator" content="Laravel {{ app()->version() }}">
<meta name="theme-color" content="#0d6efd">

{{-- Extra Meta Tags --}}
@if(!empty($seoData['extra_meta']))
    {!! $seoData['extra_meta'] !!}
@endif

{{-- Structured Data (JSON-LD) --}}
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
