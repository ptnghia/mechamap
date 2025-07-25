@extends('admin.layouts.dason')

@section('title', 'Quản lý Robots.txt')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý Robots.txt</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý Robots.txt</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ 'Quay lại' }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Điều hướng' }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.seo.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> {{ 'Cấu hình chung' }}
                        </a>
                        <a href="{{ route('admin.seo.robots') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-robot me-2"></i> {{ 'Robots.txt' }}
                        </a>
                        <a href="{{ route('admin.seo.sitemap') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-sitemap me-2"></i> {{ 'Sitemap' }}
                        </a>
                        <a href="{{ route('admin.seo.social') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-share me-2"></i> {{ 'Social Media' }}
                        </a>
                        <a href="{{ route('admin.seo.advanced') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog-wide-connected me-2"></i> {{ 'Cấu hình nâng cao' }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Hướng dẫn' }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ __('File robots.txt cho phép bạn kiểm soát cách các công cụ tìm kiếm truy cập và lập chỉ mục trang web của bạn.') }}
                    </p>
                    <p class="mb-0">{{ __('Ví dụ cơ bản:') }}</p>
                    <pre class="bg-light p-2 mt-2 rounded"><code>User-agent: *
Allow: /
Disallow: /admin/
Disallow: /private/
Sitemap: {{ url('sitemap.xml') }}</code></pre>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Chỉnh sửa file Robots.txt') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.seo.update-robots') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="robots_content" class="form-label">{{ __('Nội dung file Robots.txt') }}</label>
                            <textarea class="form-control font-monospace @error('robots_content') is-invalid @enderror" id="robots_content" name="robots_content" rows="15" style="resize: vertical;">{{ old('robots_content', $robotsContent) }}</textarea>
                            @error('robots_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ url('robots.txt') }}" target="_blank" class="btn btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i> {{ __('Xem file hiện tại') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ 'Lưu thay đổi' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Mẫu robots.txt phổ biến') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Cho phép tất cả') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>User-agent: *
Allow: /
Sitemap: {{ url('sitemap.xml') }}</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-template="allow-all">
                                        <i class="fas fa-clipboard me-1"></i> {{ 'Sao chép' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Chặn trang admin') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>User-agent: *
Allow: /
Disallow: /admin/
Disallow: /login
Disallow: /register
Sitemap: {{ url('sitemap.xml') }}</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-template="block-admin">
                                        <i class="fas fa-clipboard me-1"></i> {{ 'Sao chép' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Chặn tất cả') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>User-agent: *
Disallow: /</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-template="block-all">
                                        <i class="fas fa-clipboard me-1"></i> {{ 'Sao chép' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ __('Chặn một số User-agent') }}</h6>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-2 rounded mb-0"><code>User-agent: *
Allow: /

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Disallow: /

Sitemap: {{ url('sitemap.xml') }}</code></pre>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-template" data-template="block-agents">
                                        <i class="fas fa-clipboard me-1"></i> {{ 'Sao chép' }}
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
        'allow-all': `User-agent: *
Allow: /
Sitemap: {{ url('sitemap.xml') }}`,
        'block-admin': `User-agent: *
Allow: /
Disallow: /admin/
Disallow: /login
Disallow: /register
Sitemap: {{ url('sitemap.xml') }}`,
        'block-all': `User-agent: *
Disallow: /`,
        'block-agents': `User-agent: *
Allow: /

User-agent: Googlebot
Allow: /

User-agent: Bingbot
Disallow: /

Sitemap: {{ url('sitemap.xml') }}`
    };

    // Copy template to editor
    document.querySelectorAll('.copy-template').forEach(button => {
        button.addEventListener('click', function() {
            const template = this.getAttribute('data-template');
            if (templates[template]) {
                document.getElementById('robots_content').value = templates[template];
                
                // Show success message
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i> {{ 'Đã sao chép' }}';
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
