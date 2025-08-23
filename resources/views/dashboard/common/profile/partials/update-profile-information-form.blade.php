<section>
    <header class="mb-4">
        <h3 class="fw-bold">{{ __('profile.edit.profile_information') }}</h3>
        <p class="text-muted">{{ __('profile.edit.description') }}</p>
    </header>

    <form method="post" action="{{ route('dashboard.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Avatar Upload -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="avatar-upload-container position-relative">
                        <img src="{{ auth()->user()->avatar_url ?? '/images/default-avatar.png' }}"
                             alt="Avatar"
                             class="rounded-circle border"
                             style="width: 120px; height: 120px; object-fit: cover;"
                             id="avatar-preview">
                        <div class="avatar-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center rounded-circle"
                             style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s;">
                            <i class="fas fa-camera text-white"></i>
                        </div>
                    </div>
                    <input type="file"
                           id="avatar"
                           name="avatar"
                           accept="image/*"
                           class="d-none">
                    <button type="button"
                            class="btn btn-outline-primary btn-sm mt-2"
                            onclick="document.getElementById('avatar').click()">
                        <i class="fas fa-camera me-1"></i>
                        Thay đổi ảnh
                    </button>
                    @error('avatar')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-9">
                <!-- Basic Information -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('profile.edit.name') }} <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', auth()->user()->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">{{ __('profile.edit.email') }} <span class="text-danger">*</span></label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', auth()->user()->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="job_title" class="form-label">Chức danh</label>
                        <input type="text"
                               class="form-control @error('job_title') is-invalid @enderror"
                               id="job_title"
                               name="job_title"
                               value="{{ old('job_title', auth()->user()->job_title) }}"
                               placeholder="VD: Kỹ sư cơ khí">
                        @error('job_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="company" class="form-label">Công ty</label>
                        <input type="text"
                               class="form-control @error('company') is-invalid @enderror"
                               id="company"
                               name="company"
                               value="{{ old('company', auth()->user()->company) }}"
                               placeholder="VD: ABC Engineering">
                        @error('company')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Location & Experience -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Địa điểm</label>
                        <input type="text"
                               class="form-control @error('location') is-invalid @enderror"
                               id="location"
                               name="location"
                               value="{{ old('location', auth()->user()->location) }}"
                               placeholder="VD: Hồ Chí Minh, Việt Nam">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">Kinh nghiệm (năm)</label>
                        <select class="form-select @error('experience_years') is-invalid @enderror"
                                id="experience_years"
                                name="experience_years">
                            <option value="">Chọn kinh nghiệm</option>
                            <option value="0-1" {{ old('experience_years', auth()->user()->experience_years) == '0-1' ? 'selected' : '' }}>0-1 năm</option>
                            <option value="1-3" {{ old('experience_years', auth()->user()->experience_years) == '1-3' ? 'selected' : '' }}>1-3 năm</option>
                            <option value="3-5" {{ old('experience_years', auth()->user()->experience_years) == '3-5' ? 'selected' : '' }}>3-5 năm</option>
                            <option value="5-10" {{ old('experience_years', auth()->user()->experience_years) == '5-10' ? 'selected' : '' }}>5-10 năm</option>
                            <option value="10+" {{ old('experience_years', auth()->user()->experience_years) == '10+' ? 'selected' : '' }}>10+ năm</option>
                        </select>
                        @error('experience_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="mb-3">
                    <label for="bio" class="form-label">Giới thiệu bản thân</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror"
                              id="bio"
                              name="bio"
                              rows="4"
                              placeholder="Chia sẻ về bản thân, kinh nghiệm và chuyên môn của bạn...">{{ old('bio', auth()->user()->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Tối đa 500 ký tự</div>
                </div>

                <!-- Skills -->
                <div class="mb-3">
                    <label for="skills" class="form-label">Kỹ năng chuyên môn</label>
                    <input type="text"
                           class="form-control @error('skills') is-invalid @enderror"
                           id="skills"
                           name="skills"
                           value="{{ old('skills', auth()->user()->skills) }}"
                           placeholder="VD: SolidWorks, AutoCAD, ANSYS, Phân tích FEA">
                    @error('skills')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Phân cách bằng dấu phẩy</div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel"
                       class="form-control @error('phone') is-invalid @enderror"
                       id="phone"
                       name="phone"
                       value="{{ old('phone', auth()->user()->phone) }}"
                       placeholder="VD: +84 123 456 789">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="website" class="form-label">Website/Portfolio</label>
                <input type="url"
                       class="form-control @error('website') is-invalid @enderror"
                       id="website"
                       name="website"
                       value="{{ old('website', auth()->user()->website) }}"
                       placeholder="https://yourwebsite.com">
                @error('website')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Social Links -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="linkedin_url" class="form-label">LinkedIn</label>
                <input type="url"
                       class="form-control @error('linkedin_url') is-invalid @enderror"
                       id="linkedin_url"
                       name="linkedin_url"
                       value="{{ old('linkedin_url', auth()->user()->linkedin_url) }}"
                       placeholder="https://linkedin.com/in/yourprofile">
                @error('linkedin_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="github_url" class="form-label">GitHub</label>
                <input type="url"
                       class="form-control @error('github_url') is-invalid @enderror"
                       id="github_url"
                       name="github_url"
                       value="{{ old('github_url', auth()->user()->github_url) }}"
                       placeholder="https://github.com/yourusername">
                @error('github_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>
                {{ __('profile.edit.save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="ms-3">
                    <span class="text-success">
                        <i class="fas fa-check me-1"></i>
                        {{ __('profile.edit.saved') }}
                    </span>
                </div>
            @endif
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview functionality
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarOverlay = document.querySelector('.avatar-overlay');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Avatar hover effect
    if (avatarOverlay) {
        const avatarContainer = document.querySelector('.avatar-upload-container');
        avatarContainer.addEventListener('mouseenter', function() {
            avatarOverlay.style.opacity = '1';
        });
        avatarContainer.addEventListener('mouseleave', function() {
            avatarOverlay.style.opacity = '0';
        });
    }
});
</script>
