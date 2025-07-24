@extends('layouts.app')

@section('title', __('marketplace.seller.setup_title'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bx bx-store-alt me-2"></i>
                        {{ __('marketplace.seller.setup_heading') }} - {{ $user->getRoleDisplayName() }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle bg-soft-primary">
                                <i class="bx bx-store font-size-24"></i>
                            </div>
                        </div>
                        <h5>{{ __('marketplace.seller.welcome_message') }}</h5>
                        <p class="text-muted">{{ __('marketplace.seller.welcome_description') }}</p>
                    </div>

                    <form action="{{ route('marketplace.seller.setup.store') }}" method="POST" enctype="multipart/form-data" id="sellerSetupForm">
                        @csrf

                        <!-- Progress Steps -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="progress-nav">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="progress-steps">
                                        <div class="step active">
                                            <span class="step-number">1</span>
                                            <span class="step-title">{{ __('marketplace.seller.business_info') }}</span>
                                        </div>
                                        <div class="step">
                                            <span class="step-number">2</span>
                                            <span class="step-title">{{ __('marketplace.seller.store_info') }}</span>
                                        </div>
                                        <div class="step">
                                            <span class="step-number">3</span>
                                            <span class="step-title">Xác nhận</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1: Business Information -->
                        <div class="step-content" id="step-1">
                            <h6 class="mb-3">
                                <i class="bx bx-building me-2"></i>{{ __('marketplace.seller.business_info') }}
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_name" class="form-label">Tên doanh nghiệp <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('business_name') is-invalid @enderror"
                                               id="business_name" name="business_name" value="{{ old('business_name') }}" required>
                                        @error('business_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_type" class="form-label">Loại hình kinh doanh <span class="text-danger">*</span></label>
                                        <select class="form-select @error('business_type') is-invalid @enderror"
                                                id="business_type" name="business_type" required>
                                            <option value="">Chọn loại hình</option>
                                            <option value="individual" {{ old('business_type') === 'individual' ? 'selected' : '' }}>Cá nhân</option>
                                            <option value="company" {{ old('business_type') === 'company' ? 'selected' : '' }}>Công ty</option>
                                            <option value="partnership" {{ old('business_type') === 'partnership' ? 'selected' : '' }}>Hợp danh</option>
                                        </select>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_id" class="form-label">Mã số thuế</label>
                                        <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                               id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                                        @error('tax_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">{{ __('marketplace.seller.phone_number') }} <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('marketplace.seller.business_address') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror"
                                          id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                    Tiếp theo <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Store Information -->
                        <div class="step-content d-none" id="step-2">
                            <h6 class="mb-3">
                                <i class="bx bx-store me-2"></i>{{ __('marketplace.seller.store_info') }}
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="store_name" class="form-label">{{ __('marketplace.seller.store_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('store_name') is-invalid @enderror"
                                               id="store_name" name="store_name" value="{{ old('store_name') }}" required>
                                        @error('store_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="store_slug" class="form-label">URL cửa hàng <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">mechamap.test/store/</span>
                                            <input type="text" class="form-control @error('store_slug') is-invalid @enderror"
                                                   id="store_slug" name="store_slug" value="{{ old('store_slug') }}" required>
                                        </div>
                                        <div id="slug-feedback" class="form-text"></div>
                                        @error('store_slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="store_description" class="form-label">{{ __('marketplace.seller.store_description') }}</label>
                                <textarea class="form-control @error('store_description') is-invalid @enderror"
                                          id="store_description" name="store_description" rows="4"
                                          placeholder="{{ __('marketplace.seller.store_desc_placeholder') }}">{{ old('store_description') }}</textarea>
                                @error('store_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="store_logo" class="form-label">Logo cửa hàng</label>
                                <input type="file" class="form-control @error('store_logo') is-invalid @enderror"
                                       id="store_logo" name="store_logo" accept="image/*">
                                <small class="text-muted">Định dạng: JPG, PNG. Kích thước tối đa: 2MB</small>
                                @error('store_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Product Categories -->
                            <div class="mb-4">
                                <label class="form-label">Danh mục sản phẩm</label>
                                <div class="row">
                                    @php
                                        $categories = [
                                            'mechanical_parts' => 'Linh kiện cơ khí',
                                            'tools' => 'Dụng cụ & Thiết bị',
                                            'materials' => 'Vật liệu',
                                            'electronics' => 'Linh kiện điện tử',
                                            'safety' => 'Thiết bị an toàn',
                                            'other' => 'Khác'
                                        ];
                                    @endphp
                                    @foreach($categories as $value => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="cat_{{ $value }}" name="categories[]" value="{{ $value }}"
                                                   {{ in_array($value, old('categories', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                                    <i class="bx bx-left-arrow-alt me-1"></i> Quay lại
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                    Tiếp theo <i class="bx bx-right-arrow-alt ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="step-content d-none" id="step-3">
                            <h6 class="mb-3">
                                <i class="bx bx-check-circle me-2"></i>{{ __('marketplace.seller.confirm_info') }}
                            </h6>

                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                {{ __('marketplace.seller.review_message') }}
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('agree_terms') is-invalid @enderror"
                                           type="checkbox" id="agree_terms" name="agree_terms" required
                                           {{ old('agree_terms') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="agree_terms">
                                        Tôi đồng ý với <a href="#" target="_blank">Điều khoản dịch vụ</a> và
                                        <a href="#" target="_blank">Chính sách bán hàng</a> của MechaMap
                                        <span class="text-danger">*</span>
                                    </label>
                                    @error('agree_terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                                    <i class="bx bx-left-arrow-alt me-1"></i> Quay lại
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bx bx-check me-2"></i>{{ __('marketplace.seller.complete_setup') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-nav {
    position: relative;
    margin-bottom: 2rem;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
}

.step {
    text-align: center;
    flex: 1;
}

.step-number {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.step.active .step-number {
    background: #007bff;
    color: white;
}

.step-title {
    display: block;
    font-size: 0.875rem;
    color: #6c757d;
}

.step.active .step-title {
    color: #007bff;
    font-weight: 500;
}

.avatar-lg {
    width: 80px;
    height: 80px;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.bg-soft-primary {
    background-color: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;

function nextStep(step) {
    if (validateCurrentStep()) {
        document.getElementById(`step-${currentStep}`).classList.add('d-none');
        document.getElementById(`step-${step}`).classList.remove('d-none');

        // Update progress
        const progress = (step / 3) * 100;
        document.querySelector('.progress-bar').style.width = progress + '%';

        // Update step indicators
        document.querySelectorAll('.step').forEach((el, index) => {
            if (index + 1 <= step) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });

        currentStep = step;
    }
}

function prevStep(step) {
    document.getElementById(`step-${currentStep}`).classList.add('d-none');
    document.getElementById(`step-${step}`).classList.remove('d-none');

    // Update progress
    const progress = (step / 3) * 100;
    document.querySelector('.progress-bar').style.width = progress + '%';

    // Update step indicators
    document.querySelectorAll('.step').forEach((el, index) => {
        if (index + 1 <= step) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });

    currentStep = step;
}

function validateCurrentStep() {
    const currentStepEl = document.getElementById(`step-${currentStep}`);
    const requiredFields = currentStepEl.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Auto-generate store slug from store name
document.getElementById('store_name').addEventListener('input', function() {
    const storeName = this.value;
    const storeSlug = storeName.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('store_slug').value = storeSlug;
    checkSlugAvailability(storeSlug);
});

// Check slug availability
function checkSlugAvailability(slug) {
    if (slug.length < 3) return;

    fetch(`{{ route('marketplace.seller.check-slug') }}?slug=${slug}`)
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('slug-feedback');
            if (data.available) {
                feedback.className = 'form-text text-success';
                feedback.textContent = '✓ URL có thể sử dụng';
            } else {
                feedback.className = 'form-text text-danger';
                feedback.textContent = '✗ URL này đã được sử dụng';
            }
        });
}

document.getElementById('store_slug').addEventListener('input', function() {
    checkSlugAvailability(this.value);
});
</script>
@endpush
