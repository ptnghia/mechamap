@extends('layouts.user-dashboard')

@section('title', __('ui.marketplace.products.create_product'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ __('ui.marketplace.products.create_product') }}</h1>
                    <p class="text-muted">{{ __('ui.marketplace.products.create_description') }}</p>
                </div>
                <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>{{ __('ui.common.back') }}
                </a>
            </div>

            <!-- Product Creation Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('ui.marketplace.products.product_information') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.marketplace.seller.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Product Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('ui.marketplace.products.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Short Description -->
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">{{ __('ui.marketplace.products.short_description') }}</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Full Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('ui.marketplace.products.description') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Product Type -->
                                <div class="mb-3">
                                    <label for="product_type" class="form-label">{{ __('ui.marketplace.products.product_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_type') is-invalid @enderror"
                                            id="product_type" name="product_type" required>
                                        <option value="">{{ __('ui.common.select_option') }}</option>
                                        <option value="digital" {{ old('product_type') == 'digital' ? 'selected' : '' }}>
                                            {{ __('ui.marketplace.products.types.digital') }}
                                        </option>
                                        <option value="new_product" {{ old('product_type') == 'new_product' ? 'selected' : '' }}>
                                            {{ __('ui.marketplace.products.types.new_product') }}
                                        </option>
                                        <option value="used_product" {{ old('product_type') == 'used_product' ? 'selected' : '' }}>
                                            {{ __('ui.marketplace.products.types.used_product') }}
                                        </option>
                                    </select>
                                    @error('product_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="product_category_id" class="form-label">{{ __('ui.marketplace.products.category') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_category_id') is-invalid @enderror"
                                            id="product_category_id" name="product_category_id" required>
                                        <option value="">{{ __('ui.common.select_option') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('ui.marketplace.products.price') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                                               id="price" name="price" value="{{ old('price') }}"
                                               step="0.01" min="0" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Sale Price (Optional) -->
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">{{ __('ui.marketplace.products.sale_price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                               id="sale_price" name="sale_price" value="{{ old('sale_price') }}"
                                               step="0.01" min="0">
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">{{ __('ui.marketplace.products.sale_price_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Images Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6>{{ __('ui.marketplace.products.images') }}</h6>
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">{{ __('ui.marketplace.products.featured_image') }}</label>
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror"
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('ui.marketplace.products.featured_image_help') }}</small>
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ __('ui.marketplace.products.additional_images') }}</label>
                                    <input type="file" class="form-control @error('images') is-invalid @enderror"
                                           id="images" name="images[]" accept="image/*" multiple>
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('ui.marketplace.products.additional_images_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Digital Product Files (shown only for digital products) -->
                        <div class="row mt-4" id="digitalFilesSection" style="display: none;">
                            <div class="col-12">
                                <h6>{{ __('ui.marketplace.products.digital_files') }}</h6>
                                <div class="mb-3">
                                    <label for="digital_files" class="form-label">{{ __('ui.marketplace.products.upload_files') }}</label>
                                    <input type="file" class="form-control @error('digital_files') is-invalid @enderror"
                                           id="digital_files" name="digital_files[]" multiple>
                                    @error('digital_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('ui.marketplace.products.digital_files_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Management (shown only for physical products) -->
                        <div class="row mt-4" id="stockSection">
                            <div class="col-12">
                                <h6>{{ __('ui.marketplace.products.stock_management') }}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">{{ __('ui.marketplace.products.stock_quantity') }}</label>
                                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror"
                                                   id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0">
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="low_stock_threshold" class="form-label">{{ __('ui.marketplace.products.low_stock_threshold') }}</label>
                                            <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror"
                                                   id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" min="0">
                                            @error('low_stock_threshold')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">{{ __('ui.marketplace.products.tags') }}</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror"
                                           id="tags" name="tags" value="{{ old('tags') }}"
                                           placeholder="{{ __('ui.marketplace.products.tags_placeholder') }}">
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('ui.marketplace.products.tags_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-secondary">
                                        {{ __('ui.common.cancel') }}
                                    </a>
                                    <div>
                                        <button type="submit" name="status" value="draft" class="btn btn-outline-primary me-2">
                                            {{ __('ui.marketplace.products.save_as_draft') }}
                                        </button>
                                        <button type="submit" name="status" value="pending" class="btn btn-primary">
                                            {{ __('ui.marketplace.products.submit_for_review') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productTypeSelect = document.getElementById('product_type');
    const digitalFilesSection = document.getElementById('digitalFilesSection');
    const stockSection = document.getElementById('stockSection');

    function toggleSections() {
        const productType = productTypeSelect.value;

        if (productType === 'digital') {
            digitalFilesSection.style.display = 'block';
            stockSection.style.display = 'none';
        } else if (productType === 'new_product' || productType === 'used_product') {
            digitalFilesSection.style.display = 'none';
            stockSection.style.display = 'block';
        } else {
            digitalFilesSection.style.display = 'none';
            stockSection.style.display = 'none';
        }
    }

    productTypeSelect.addEventListener('change', toggleSections);
    toggleSections(); // Initial call
});
</script>
@endpush
@endsection
