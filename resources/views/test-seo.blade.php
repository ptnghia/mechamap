@extends('layouts.app')

@section('title', 'Test SEO')

@section('content')
<div class="container">
    <h1>Test SEO Settings</h1>

    <div class="card">
        <div class="card-header">
            <h5>Debug Biến $seo</h5>
        </div>
        <div class="card-body">
            @if(isset($seo))
            <div class="alert alert-success">
                ✓ Biến $seo đã được truyền vào view
            </div>

            <h6>Dữ liệu SEO:</h6>
            <ul>
                <li><strong>Site Title:</strong> {{ $seo['site_title'] ?? 'N/A' }}</li>
                <li><strong>Twitter Username:</strong> {{ $seo['twitter_username'] ?? 'N/A' }}</li>
                <li><strong>Google Analytics ID:</strong> {{ $seo['google_analytics_id'] ?? 'N/A' }}</li>
                <li><strong>OG Title:</strong> {{ $seo['og_title'] ?? 'N/A' }}</li>
            </ul>

            <h6>Toàn bộ dữ liệu $seo:</h6>
            <pre class="bg-light p-3">{{ json_encode($seo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
            <div class="alert alert-danger">
                ✗ Biến $seo không được truyền vào view
            </div>
            @endif
        </div>
    </div>
</div>
@endsection