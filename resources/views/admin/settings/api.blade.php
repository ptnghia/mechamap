@extends('admin.layouts.dason')

@section('title', 'API Keys')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">API Keys</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">API Keys</li>
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
                    <h5 class="card-title mb-0">{{ __('API Keys') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle-fill me-2"></i>
                        {{ __('Các API key được sử dụng để kết nối với các dịch vụ bên thứ ba. Hãy đảm bảo rằng bạn giữ các key này an toàn.') }}
                    </div>
                    
                    <form action="{{ route('admin.settings.update-api') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="mb-3">{{ __('Google Login') }}</h6>
                        
                        <div class="mb-3">
                            <label for="api_google_client_id" class="form-label">{{ __('Google Client ID') }}</label>
                            <input type="text" class="form-control @error('api_google_client_id') is-invalid @enderror" id="api_google_client_id" name="api_google_client_id" value="{{ old('api_google_client_id', $settings['api_google_client_id'] ?? '') }}">
                            <div class="form-text">{{ __('Client ID từ Google Cloud Console.') }}</div>
                            @error('api_google_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="api_google_client_secret" class="form-label">{{ __('Google Client Secret') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('api_google_client_secret') is-invalid @enderror" id="api_google_client_secret" name="api_google_client_secret" value="{{ old('api_google_client_secret', $settings['api_google_client_secret'] ?? '') }}">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="api_google_client_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">{{ __('Client Secret từ Google Cloud Console.') }}</div>
                            @error('api_google_client_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">{{ __('Facebook Login') }}</h6>
                        
                        <div class="mb-3">
                            <label for="api_facebook_app_id" class="form-label">{{ __('Facebook App ID') }}</label>
                            <input type="text" class="form-control @error('api_facebook_app_id') is-invalid @enderror" id="api_facebook_app_id" name="api_facebook_app_id" value="{{ old('api_facebook_app_id', $settings['api_facebook_app_id'] ?? '') }}">
                            <div class="form-text">{{ __('App ID từ Facebook Developer Console.') }}</div>
                            @error('api_facebook_app_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="api_facebook_app_secret" class="form-label">{{ __('Facebook App Secret') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('api_facebook_app_secret') is-invalid @enderror" id="api_facebook_app_secret" name="api_facebook_app_secret" value="{{ old('api_facebook_app_secret', $settings['api_facebook_app_secret'] ?? '') }}">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="api_facebook_app_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">{{ __('App Secret từ Facebook Developer Console.') }}</div>
                            @error('api_facebook_app_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">{{ __('Google reCAPTCHA') }}</h6>
                        
                        <div class="mb-3">
                            <label for="api_recaptcha_site_key" class="form-label">{{ __('reCAPTCHA Site Key') }}</label>
                            <input type="text" class="form-control @error('api_recaptcha_site_key') is-invalid @enderror" id="api_recaptcha_site_key" name="api_recaptcha_site_key" value="{{ old('api_recaptcha_site_key', $settings['api_recaptcha_site_key'] ?? '') }}">
                            <div class="form-text">{{ __('Site Key từ Google reCAPTCHA Admin Console.') }}</div>
                            @error('api_recaptcha_site_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="api_recaptcha_secret_key" class="form-label">{{ __('reCAPTCHA Secret Key') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('api_recaptcha_secret_key') is-invalid @enderror" id="api_recaptcha_secret_key" name="api_recaptcha_secret_key" value="{{ old('api_recaptcha_secret_key', $settings['api_recaptcha_secret_key'] ?? '') }}">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="api_recaptcha_secret_key">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">{{ __('Secret Key từ Google reCAPTCHA Admin Console.') }}</div>
                            @error('api_recaptcha_secret_key')
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
                    <h5 class="card-title mb-0">{{ __('Hướng dẫn cấu hình') }}</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accordionApiGuides">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingGoogle">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGoogle" aria-expanded="false" aria-controls="collapseGoogle">
                                    <i class="fab fa-google me-2"></i> {{ __('Cấu hình Google Login') }}
                                </button>
                            </h2>
                            <div id="collapseGoogle" class="accordion-collapse collapse" aria-labelledby="headingGoogle" data-bs-parent="#accordionApiGuides">
                                <div class="accordion-body">
                                    <ol class="mb-0">
                                        <li>{{ __('Truy cập') }} <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                                        <li>{{ __('Tạo một dự án mới hoặc chọn dự án hiện có') }}</li>
                                        <li>{{ __('Đi đến "APIs & Services" > "Credentials"') }}</li>
                                        <li>{{ __('Nhấp vào "Create Credentials" và chọn "OAuth client ID"') }}</li>
                                        <li>{{ __('Chọn "Web application" làm loại ứng dụng') }}</li>
                                        <li>{{ __('Thêm URI chuyển hướng:') }} <code>{{ url('/auth/google/callback') }}</code></li>
                                        <li>{{ __('Sao chép Client ID và Client Secret vào các trường tương ứng ở trên') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFacebook">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFacebook" aria-expanded="false" aria-controls="collapseFacebook">
                                    <i class="fab fa-facebook me-2"></i> {{ __('Cấu hình Facebook Login') }}
                                </button>
                            </h2>
                            <div id="collapseFacebook" class="accordion-collapse collapse" aria-labelledby="headingFacebook" data-bs-parent="#accordionApiGuides">
                                <div class="accordion-body">
                                    <ol class="mb-0">
                                        <li>{{ __('Truy cập') }} <a href="https://developers.facebook.com/" target="_blank">Facebook for Developers</a></li>
                                        <li>{{ __('Tạo một ứng dụng mới hoặc chọn ứng dụng hiện có') }}</li>
                                        <li>{{ __('Đi đến "Settings" > "Basic"') }}</li>
                                        <li>{{ __('Sao chép App ID và App Secret vào các trường tương ứng ở trên') }}</li>
                                        <li>{{ __('Đi đến "Facebook Login" > "Settings"') }}</li>
                                        <li>{{ __('Thêm URI chuyển hướng:') }} <code>{{ url('/auth/facebook/callback') }}</code></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRecaptcha">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRecaptcha" aria-expanded="false" aria-controls="collapseRecaptcha">
                                    <i class="fas fa-shield-alt me-2"></i> {{ __('Cấu hình Google reCAPTCHA') }}
                                </button>
                            </h2>
                            <div id="collapseRecaptcha" class="accordion-collapse collapse" aria-labelledby="headingRecaptcha" data-bs-parent="#accordionApiGuides">
                                <div class="accordion-body">
                                    <ol class="mb-0">
                                        <li>{{ __('Truy cập') }} <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin</a></li>
                                        <li>{{ __('Nhấp vào "Create" để tạo một site mới') }}</li>
                                        <li>{{ __('Chọn reCAPTCHA v2 "I\'m not a robot" Checkbox') }}</li>
                                        <li>{{ __('Thêm tên miền của bạn vào danh sách tên miền') }}</li>
                                        <li>{{ __('Sao chép Site Key và Secret Key vào các trường tương ứng ở trên') }}</li>
                                    </ol>
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
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });
</script>
@endpush
