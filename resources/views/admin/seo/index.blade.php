@extends('admin.layouts.dason')

@section('title', 'Cấu hình SEO')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Cấu hình SEO</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Cấu hình SEO</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.page-seo.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-text me-1"></i> {{ __('Cấu hình trang') }}
        </a>
        <a href="{{ route('admin.seo.robots') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-robot me-1"></i> {{ __('Robots.txt') }}
        </a>
        <a href="{{ route('admin.seo.sitemap') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-sitemap me-1"></i> {{ __('Sitemap') }}
        </a>
        <a href="{{ route('admin.seo.social') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-share me-1"></i> {{ __('Social Media') }}
        </a>
        <a href="{{ route('admin.seo.advanced') }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-cog me-1"></i> {{ __('Cấu hình nâng cao') }}
        </a>
    </div>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Điều hướng') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.seo.index') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-cog me-2"></i> {{ __('Cấu hình chung') }}
                        </a>
                        <a href="{{ route('admin.page-seo.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-text me-2"></i> {{ __('Cấu hình trang') }}
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
                        <a href="{{ route('admin.seo.advanced') }}" class="list-group-item list-group-item-action">
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
                        {{ __('Cấu hình SEO giúp tối ưu hóa website của bạn cho các công cụ tìm kiếm.') }}
                    </p>
                    <ul class="mb-0">
                        <li>{{ __('Tiêu đề trang nên ngắn gọn, dưới 60 ký tự.') }}</li>
                        <li>{{ __('Mô tả trang nên dưới 160 ký tự.') }}</li>
                        <li>{{ __('Từ khóa nên liên quan đến nội dung trang.') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Cấu hình SEO chung') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.seo.update-general') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="site_title" class="form-label">{{ __('Tiêu đề trang web') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('site_title') is-invalid @enderror" id="site_title" name="site_title" value="{{ old('site_title', $settings['site_title'] ?? config('app.name')) }}" required>
                            <div class="form-text">{{ __('Tiêu đề mặc định cho trang web của bạn. Hiển thị trên thanh tiêu đề trình duyệt và kết quả tìm kiếm.') }}</div>
                            @error('site_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">{{ __('Mô tả trang web') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description" name="site_description" rows="3" required>{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                            <div class="form-text">{{ __('Mô tả ngắn gọn về trang web của bạn. Hiển thị trong kết quả tìm kiếm.') }}</div>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="site_keywords" class="form-label">{{ __('Từ khóa') }}</label>
                            <input type="text" class="form-control @error('site_keywords') is-invalid @enderror" id="site_keywords" name="site_keywords" value="{{ old('site_keywords', $settings['site_keywords'] ?? '') }}">
                            <div class="form-text">{{ __('Các từ khóa liên quan đến trang web của bạn, phân cách bằng dấu phẩy.') }}</div>
                            @error('site_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_indexing" name="allow_indexing" {{ old('allow_indexing', $settings['allow_indexing'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_indexing">{{ __('Cho phép công cụ tìm kiếm lập chỉ mục trang web') }}</label>
                            </div>
                            <div class="form-text">{{ __('Nếu tắt, trang web sẽ không xuất hiện trong kết quả tìm kiếm.') }}</div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="google_analytics_id" class="form-label">{{ __('Google Analytics ID') }}</label>
                            <input type="text" class="form-control @error('google_analytics_id') is-invalid @enderror" id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" placeholder="G-XXXXXXXXXX hoặc UA-XXXXXXXX-X">
                            <div class="form-text">{{ __('ID theo dõi Google Analytics của bạn.') }}</div>
                            @error('google_analytics_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="google_search_console_id" class="form-label">{{ __('Google Search Console Verification') }}</label>
                            <input type="text" class="form-control @error('google_search_console_id') is-invalid @enderror" id="google_search_console_id" name="google_search_console_id" value="{{ old('google_search_console_id', $settings['google_search_console_id'] ?? '') }}" placeholder="Mã xác minh">
                            <div class="form-text">{{ __('Mã xác minh Google Search Console của bạn.') }}</div>
                            @error('google_search_console_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="facebook_app_id" class="form-label">{{ __('Facebook App ID') }}</label>
                            <input type="text" class="form-control @error('facebook_app_id') is-invalid @enderror" id="facebook_app_id" name="facebook_app_id" value="{{ old('facebook_app_id', $settings['facebook_app_id'] ?? '') }}">
                            <div class="form-text">{{ __('ID ứng dụng Facebook của bạn.') }}</div>
                            @error('facebook_app_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="twitter_username" class="form-label">{{ __('Twitter Username') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control @error('twitter_username') is-invalid @enderror" id="twitter_username" name="twitter_username" value="{{ old('twitter_username', $settings['twitter_username'] ?? '') }}">
                            </div>
                            <div class="form-text">{{ __('Tên người dùng Twitter của bạn, không bao gồm ký tự @.') }}</div>
                            @error('twitter_username')
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
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection