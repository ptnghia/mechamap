@extends('admin.layouts.dason')

@section('title', 'Hồ sơ của tôi')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Hồ sơ của tôi</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Hồ sơ của tôi</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.profile.password') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-key me-1"></i> {{ __('Đổi mật khẩu') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle img-thumbnail mb-3" width="150" height="150">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">
                        @if($user->isAdmin())
                            <span class="badge bg-danger">{{ __('Admin') }}</span>
                        @elseif($user->isModerator())
                            <span class="badge bg-primary">{{ __('Moderator') }}</span>
                        @endif
                    </p>
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> {{ $user->username }}
                        </small>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-envelope"></i> {{ $user->email }}
                        </small>
                        @if($user->location)
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $user->location }}
                            </small>
                        @endif
                        @if($user->website)
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-globe"></i> <a href="{{ $user->website }}" target="_blank">{{ $user->website }}</a>
                            </small>
                        @endif
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ url('/users/' . $user->username) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eye me-1"></i> {{ __('Xem hồ sơ công khai') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin tài khoản') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('Ngày tham gia') }}</label>
                        <p>{{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('Hoạt động lần cuối') }}</label>
                        <p>{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : __('Chưa có') }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">{{ __('Trạng thái') }}</label>
                        <p>
                            @if($user->isOnline())
                                <span class="badge bg-success">{{ __('Đang hoạt động') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Không hoạt động') }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Cập nhật thông tin') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('Họ tên') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">{{ __('Tên đăng nhập') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">{{ __('Ảnh đại diện') }}</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                            <div class="form-text">{{ __('Chấp nhận các định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB.') }}</div>
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="about_me" class="form-label">{{ __('Giới thiệu') }}</label>
                            <textarea class="form-control @error('about_me') is-invalid @enderror" id="about_me" name="about_me" rows="3">{{ old('about_me', $user->about_me) }}</textarea>
                            @error('about_me')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="website" class="form-label">{{ __('Website') }}</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $user->website) }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">{{ __('Địa điểm') }}</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $user->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="signature" class="form-label">{{ __('Chữ ký') }}</label>
                            <textarea class="form-control @error('signature') is-invalid @enderror" id="signature" name="signature" rows="2">{{ old('signature', $user->signature) }}</textarea>
                            @error('signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('Lưu thay đổi') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Preview avatar before upload
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImg = document.querySelector('.img-thumbnail');
                avatarImg.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
