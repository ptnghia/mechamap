@extends('admin.layouts.dason')

@section('title', 'Cấu hình Social Media')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Cấu hình Social Media</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Cấu hình Social Media</li>
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
                        <a href="{{ route('admin.seo.robots') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-robot me-2"></i> {{ 'Robots.txt' }}
                        </a>
                        <a href="{{ route('admin.seo.sitemap') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-sitemap me-2"></i> {{ 'Sitemap' }}
                        </a>
                        <a href="{{ route('admin.seo.social') }}" class="list-group-item list-group-item-action active">
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
                        {{ 'Cấu hình Social Media giúp tối ưu hóa cách trang web của bạn hiển thị khi được chia sẻ trên các mạng xã hội.' }}
                    </p>
                    <p class="mb-0">
                        {{ 'Các thẻ Open Graph (og:) được sử dụng bởi Facebook và nhiều nền tảng khác, trong khi các thẻ Twitter Card được sử dụng bởi Twitter.' }}
                    </p>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Xem trước' }}</h5>
                </div>
                <div class="card-body">
                    <div class="social-preview facebook-preview mb-4">
                        <h6 class="text-muted mb-2">
                            <i class="fab fa-facebook me-1"></i> {{ 'Facebook' }}
                        </h6>
                        <div class="card">
                            <div class="card-img-top bg-light text-center py-3" style="height: 150px;" id="og-image-preview">
                                @if(!empty($settings['og_image'] ?? ''))
                                    <img src="{{ $settings['og_image'] }}" alt="OG Image" class="h-100">
                                @else
                                    <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="card-text small text-muted mb-1">{{ config('app.url') }}</p>
                                <h6 class="card-title" id="og-title-preview">{{ $settings['og_title'] ?? config('app.name') }}</h6>
                                <p class="card-text small" id="og-description-preview">{{ $settings['og_description'] ?? 'Mô tả trang web của bạn sẽ hiển thị ở đây.' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-preview twitter-preview">
                        <h6 class="text-muted mb-2">
                            <i class="fab fa-twitter me-1"></i> {{ 'Twitter' }}
                        </h6>
                        <div class="card">
                            <div class="card-img-top bg-light text-center py-3" style="height: 150px;" id="twitter-image-preview">
                                @if(!empty($settings['twitter_image'] ?? ''))
                                    <img src="{{ $settings['twitter_image'] }}" alt="Twitter Image" class="h-100">
                                @else
                                    <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                @endif
                            </div>
                            <div class="card-body">
                                <h6 class="card-title" id="twitter-title-preview">{{ $settings['twitter_title'] ?? config('app.name') }}</h6>
                                <p class="card-text small" id="twitter-description-preview">{{ $settings['twitter_description'] ?? 'Mô tả trang web của bạn sẽ hiển thị ở đây.' }}</p>
                                <p class="card-text small text-muted mb-0">{{ config('app.url') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Cấu hình Open Graph (Facebook, LinkedIn, ...)' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.seo.update-social') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="og_title" class="form-label">{{ 'Tiêu đề Open Graph' }}</label>
                            <input type="text" class="form-control @error('og_title') is-invalid @enderror" id="og_title" name="og_title" value="{{ old('og_title', $settings['og_title'] ?? '') }}">
                            <div class="form-text">{{ 'Tiêu đề khi chia sẻ trang web của bạn trên Facebook và các nền tảng khác.' }}</div>
                            @error('og_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="og_description" class="form-label">{{ 'Mô tả Open Graph' }}</label>
                            <textarea class="form-control @error('og_description') is-invalid @enderror" id="og_description" name="og_description" rows="3">{{ old('og_description', $settings['og_description'] ?? '') }}</textarea>
                            <div class="form-text">{{ 'Mô tả khi chia sẻ trang web của bạn trên Facebook và các nền tảng khác.' }}</div>
                            @error('og_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="og_image" class="form-label">{{ 'Hình ảnh Open Graph' }}</label>
                            <input type="file" class="form-control @error('og_image') is-invalid @enderror" id="og_image" name="og_image">
                            <div class="form-text">{{ 'Hình ảnh khi chia sẻ trang web của bạn trên Facebook và các nền tảng khác. Kích thước tối thiểu: 1200x630 pixels.' }}</div>
                            @error('og_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if(!empty($settings['og_image'] ?? ''))
                                <div class="mt-2">
                                    <img src="{{ $settings['og_image'] }}" alt="OG Image" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label for="twitter_card" class="form-label">{{ 'Loại Twitter Card' }}</label>
                            <select class="form-select @error('twitter_card') is-invalid @enderror" id="twitter_card" name="twitter_card">
                                <option value="summary" {{ (old('twitter_card', $settings['twitter_card'] ?? '')) == 'summary' ? 'selected' : '' }}>{{ 'Summary' }}</option>
                                <option value="summary_large_image" {{ (old('twitter_card', $settings['twitter_card'] ?? '')) == 'summary_large_image' ? 'selected' : '' }}>{{ 'Summary with Large Image' }}</option>
                                <option value="app" {{ (old('twitter_card', $settings['twitter_card'] ?? '')) == 'app' ? 'selected' : '' }}>{{ 'App' }}</option>
                                <option value="player" {{ (old('twitter_card', $settings['twitter_card'] ?? '')) == 'player' ? 'selected' : '' }}>{{ 'Player' }}</option>
                            </select>
                            <div class="form-text">{{ 'Loại thẻ Twitter Card để sử dụng khi chia sẻ trang web của bạn trên Twitter.' }}</div>
                            @error('twitter_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="twitter_title" class="form-label">{{ 'Tiêu đề Twitter' }}</label>
                            <input type="text" class="form-control @error('twitter_title') is-invalid @enderror" id="twitter_title" name="twitter_title" value="{{ old('twitter_title', $settings['twitter_title'] ?? '') }}">
                            <div class="form-text">{{ 'Tiêu đề khi chia sẻ trang web của bạn trên Twitter.' }}</div>
                            @error('twitter_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="twitter_description" class="form-label">{{ 'Mô tả Twitter' }}</label>
                            <textarea class="form-control @error('twitter_description') is-invalid @enderror" id="twitter_description" name="twitter_description" rows="3">{{ old('twitter_description', $settings['twitter_description'] ?? '') }}</textarea>
                            <div class="form-text">{{ 'Mô tả khi chia sẻ trang web của bạn trên Twitter.' }}</div>
                            @error('twitter_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="twitter_image" class="form-label">{{ 'Hình ảnh Twitter' }}</label>
                            <input type="file" class="form-control @error('twitter_image') is-invalid @enderror" id="twitter_image" name="twitter_image">
                            <div class="form-text">{{ 'Hình ảnh khi chia sẻ trang web của bạn trên Twitter. Kích thước tối thiểu: 1200x600 pixels.' }}</div>
                            @error('twitter_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if(!empty($settings['twitter_image'] ?? ''))
                                <div class="mt-2">
                                    <img src="{{ $settings['twitter_image'] }}" alt="Twitter Image" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ 'Lưu cấu hình' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Kiểm tra công cụ chia sẻ xã hội' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fab fa-facebook me-2"></i>{{ 'Facebook Sharing Debugger' }}
                                    </h5>
                                    <p class="card-text">
                                        {{ 'Kiểm tra cách trang web của bạn hiển thị khi được chia sẻ trên Facebook.' }}
                                    </p>
                                    <a href="https://developers.facebook.com/tools/debug/" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ 'Mở Facebook Debugger' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fab fa-twitter me-2"></i>{{ 'Twitter Card Validator' }}
                                    </h5>
                                    <p class="card-text">
                                        {{ 'Kiểm tra cách trang web của bạn hiển thị khi được chia sẻ trên Twitter.' }}
                                    </p>
                                    <a href="https://cards-dev.twitter.com/validator" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ 'Mở Twitter Card Validator' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fab fa-linkedin me-2"></i>{{ 'LinkedIn Post Inspector' }}
                                    </h5>
                                    <p class="card-text">
                                        {{ 'Kiểm tra cách trang web của bạn hiển thị khi được chia sẻ trên LinkedIn.' }}
                                    </p>
                                    <a href="https://www.linkedin.com/post-inspector/" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ 'Mở LinkedIn Post Inspector' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fab fa-pinterest me-2"></i>{{ 'Pinterest Rich Pins Validator' }}
                                    </h5>
                                    <p class="card-text">
                                        {{ 'Kiểm tra cách trang web của bạn hiển thị khi được chia sẻ trên Pinterest.' }}
                                    </p>
                                    <a href="https://developers.pinterest.com/tools/url-debugger/" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ 'Mở Pinterest Validator' }}
                                    </a>
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
    // Live preview for Open Graph
    document.getElementById('og_title').addEventListener('input', function() {
        document.getElementById('og-title-preview').textContent = this.value || '{{ config('app.name') }}';
    });
    
    document.getElementById('og_description').addEventListener('input', function() {
        document.getElementById('og-description-preview').textContent = this.value || 'Mô tả trang web của bạn sẽ hiển thị ở đây.';
    });
    
    // Live preview for Twitter
    document.getElementById('twitter_title').addEventListener('input', function() {
        document.getElementById('twitter-title-preview').textContent = this.value || '{{ config('app.name') }}';
    });
    
    document.getElementById('twitter_description').addEventListener('input', function() {
        document.getElementById('twitter-description-preview').textContent = this.value || 'Mô tả trang web của bạn sẽ hiển thị ở đây.';
    });
    
    // Preview image upload
    document.getElementById('og_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('og-image-preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="OG Image Preview" class="h-100">`;
            }
            reader.readAsDataURL(file);
        }
    });
    
    document.getElementById('twitter_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('twitter-image-preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Twitter Image Preview" class="h-100">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
