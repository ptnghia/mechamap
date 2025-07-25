@extends('admin.layouts.dason')

@section('title', 'Mạng xã hội')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Mạng xã hội</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Mạng xã hội</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            @include('admin.settings.partials.sidebar')
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Liên kết mạng xã hội') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-social') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="social_facebook" class="form-label">
                                <i class="fab fa-facebook me-1 text-primary"></i> {{ 'Facebook' }}
                            </label>
                            <input type="url" class="form-control @error('social_facebook') is-invalid @enderror" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}" placeholder="https://facebook.com/yourpage">
                            <div class="form-text">{{ __('Liên kết đến trang Facebook của bạn.') }}</div>
                            @error('social_facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_twitter" class="form-label">
                                <i class="fab fa-twitter me-1 text-info"></i> {{ 'Twitter / X' }}
                            </label>
                            <input type="url" class="form-control @error('social_twitter') is-invalid @enderror" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}" placeholder="https://twitter.com/yourusername">
                            <div class="form-text">{{ __('Liên kết đến trang Twitter/X của bạn.') }}</div>
                            @error('social_twitter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_instagram" class="form-label">
                                <i class="fab fa-instagram me-1 text-danger"></i> {{ 'Instagram' }}
                            </label>
                            <input type="url" class="form-control @error('social_instagram') is-invalid @enderror" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}" placeholder="https://instagram.com/yourusername">
                            <div class="form-text">{{ __('Liên kết đến trang Instagram của bạn.') }}</div>
                            @error('social_instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_linkedin" class="form-label">
                                <i class="fab fa-linkedin me-1 text-primary"></i> {{ 'LinkedIn' }}
                            </label>
                            <input type="url" class="form-control @error('social_linkedin') is-invalid @enderror" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}" placeholder="https://linkedin.com/company/yourcompany">
                            <div class="form-text">{{ __('Liên kết đến trang LinkedIn của bạn.') }}</div>
                            @error('social_linkedin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_youtube" class="form-label">
                                <i class="fab fa-youtube me-1 text-danger"></i> {{ 'YouTube' }}
                            </label>
                            <input type="url" class="form-control @error('social_youtube') is-invalid @enderror" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube'] ?? '') }}" placeholder="https://youtube.com/c/yourchannel">
                            <div class="form-text">{{ __('Liên kết đến kênh YouTube của bạn.') }}</div>
                            @error('social_youtube')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_tiktok" class="form-label">
                                <i class="fab fa-tiktok me-1"></i> {{ 'TikTok' }}
                            </label>
                            <input type="url" class="form-control @error('social_tiktok') is-invalid @enderror" id="social_tiktok" name="social_tiktok" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}" placeholder="https://tiktok.com/@yourusername">
                            <div class="form-text">{{ __('Liên kết đến trang TikTok của bạn.') }}</div>
                            @error('social_tiktok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_pinterest" class="form-label">
                                <i class="fab fa-pinterest me-1 text-danger"></i> {{ 'Pinterest' }}
                            </label>
                            <input type="url" class="form-control @error('social_pinterest') is-invalid @enderror" id="social_pinterest" name="social_pinterest" value="{{ old('social_pinterest', $settings['social_pinterest'] ?? '') }}" placeholder="https://pinterest.com/yourusername">
                            <div class="form-text">{{ __('Liên kết đến trang Pinterest của bạn.') }}</div>
                            @error('social_pinterest')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="social_github" class="form-label">
                                <i class="fab fa-github me-1"></i> {{ 'GitHub' }}
                            </label>
                            <input type="url" class="form-control @error('social_github') is-invalid @enderror" id="social_github" name="social_github" value="{{ old('social_github', $settings['social_github'] ?? '') }}" placeholder="https://github.com/yourusername">
                            <div class="form-text">{{ __('Liên kết đến trang GitHub của bạn.') }}</div>
                            @error('social_github')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                    <h5 class="card-title mb-0">{{ 'Xem trước' }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @if(!empty($settings['social_facebook'] ?? ''))
                            <a href="{{ $settings['social_facebook'] }}" target="_blank" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_twitter'] ?? ''))
                            <a href="{{ $settings['social_twitter'] }}" target="_blank" class="btn btn-outline-info" data-bs-toggle="tooltip" title="Twitter / X">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_instagram'] ?? ''))
                            <a href="{{ $settings['social_instagram'] }}" target="_blank" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_linkedin'] ?? ''))
                            <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="LinkedIn">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_youtube'] ?? ''))
                            <a href="{{ $settings['social_youtube'] }}" target="_blank" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_tiktok'] ?? ''))
                            <a href="{{ $settings['social_tiktok'] }}" target="_blank" class="btn btn-outline-dark" data-bs-toggle="tooltip" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_pinterest'] ?? ''))
                            <a href="{{ $settings['social_pinterest'] }}" target="_blank" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Pinterest">
                                <i class="fab fa-pinterest"></i>
                            </a>
                        @endif
                        
                        @if(!empty($settings['social_github'] ?? ''))
                            <a href="{{ $settings['social_github'] }}" target="_blank" class="btn btn-outline-dark" data-bs-toggle="tooltip" title="GitHub">
                                <i class="fab fa-github"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
