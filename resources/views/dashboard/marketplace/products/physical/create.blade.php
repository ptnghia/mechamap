@extends('layouts.user-dashboard')

@section('title', __('marketplace.physical_products.create_title'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="fas fa-box me-2 text-primary"></i>
                    {{ __('marketplace.physical_products.create_title') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('common.dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.marketplace.seller.products.index') }}">{{ __('marketplace.products.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('marketplace.physical_products.create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading">{{ __('marketplace.physical_products.info_title') }}</h5>
                        <p class="mb-0">{{ __('marketplace.physical_products.info_description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('dashboard.marketplace.seller.products.physical.store') }}" method="POST" enctype="multipart/form-data" id="physicalProductForm">
        @csrf

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>
                            {{ __('marketplace.products.basic_info') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('marketplace.products.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    <div class="form-text">{{ __('marketplace.physical_products.name_help') }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">{{ __('marketplace.products.short_description') }}</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="2" maxlength="500">{{ old('short_description') }}</textarea>
                                    <div class="form-text">{{ __('marketplace.products.short_description_help') }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('marketplace.products.description') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    <div class="form-text">{{ __('marketplace.physical_products.description_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Type & Condition -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>
                            {{ __('marketplace.physical_products.type_condition') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_type" class="form-label">{{ __('marketplace.physical_products.product_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="product_type" name="product_type" required>
                                        <option value="">{{ __('marketplace.physical_products.choose_type') }}</option>
                                        @if(in_array('new_product', $canSellPhysical))
                                            <option value="new_product" {{ old('product_type') == 'new_product' ? 'selected' : '' }}>
                                                {{ __('marketplace.physical_products.new_product') }}
                                            </option>
                                        @endif
                                        @if(in_array('used_product', $canSellPhysical))
                                            <option value="used_product" {{ old('product_type') == 'used_product' ? 'selected' : '' }}>
                                                {{ __('marketplace.physical_products.used_product') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="condition" class="form-label">{{ __('marketplace.physical_products.condition') }} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="condition" name="condition" required>
                                        <option value="">{{ __('marketplace.physical_products.choose_condition') }}</option>
                                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>
                                            {{ __('marketplace.physical_products.condition_new') }}
                                        </option>
                                        <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>
                                            {{ __('marketplace.physical_products.condition_like_new') }}
                                        </option>
                                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>
                                            {{ __('marketplace.physical_products.condition_good') }}
                                        </option>
                                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>
                                            {{ __('marketplace.physical_products.condition_fair') }}
                                        </option>
                                        <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>
                                            {{ __('marketplace.physical_products.condition_poor') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Physical Specifications -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-ruler me-2"></i>
                            {{ __('marketplace.physical_products.physical_specs') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="weight" class="form-label">{{ __('marketplace.physical_products.weight') }}</label>
                                            <input type="number" step="0.01" class="form-control" id="weight" name="weight" value="{{ old('weight') }}">
                                            <div class="form-text">kg</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="length" class="form-label">{{ __('marketplace.physical_products.length') }}</label>
                                            <input type="number" step="0.01" class="form-control" id="length" name="length" value="{{ old('length') }}">
                                            <div class="form-text">cm</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="width" class="form-label">{{ __('marketplace.physical_products.width') }}</label>
                                            <input type="number" step="0.01" class="form-control" id="width" name="width" value="{{ old('width') }}">
                                            <div class="form-text">cm</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="height" class="form-label">{{ __('marketplace.physical_products.height') }}</label>
                                            <input type="number" step="0.01" class="form-control" id="height" name="height" value="{{ old('height') }}">
                                            <div class="form-text">cm</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="material" class="form-label">{{ __('marketplace.physical_products.material') }}</label>
                                            <input type="text" class="form-control" id="material" name="material" value="{{ old('material') }}">
                                            <div class="form-text">{{ __('marketplace.physical_products.material_help') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-images me-2"></i>
                            {{ __('marketplace.products.images') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">{{ __('marketplace.products.featured_image') }}</label>
                                    <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                                    <div class="form-text">{{ __('marketplace.products.featured_image_help') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="images" class="form-label">{{ __('marketplace.products.gallery_images') }}</label>
                                    <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                                    <div class="form-text">{{ __('marketplace.products.gallery_images_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Pricing & Stock -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            {{ __('marketplace.products.pricing') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="price" class="form-label">{{ __('marketplace.products.price') }} (VND) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="sale_price" class="form-label">{{ __('marketplace.products.sale_price') }} (VND)</label>
                                    <input type="number" step="1" min="0" class="form-control" id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                                    <div class="form-text">{{ __('marketplace.products.sale_price_help') }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label">{{ __('marketplace.physical_products.stock_quantity') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="0" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 1) }}" required>
                                    <div class="form-text">{{ __('marketplace.physical_products.stock_help') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2"></i>
                            {{ __('marketplace.products.category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="product_category_id" class="form-label">{{ __('marketplace.products.select_category') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_category_id" name="product_category_id" required>
                                <option value="">{{ __('marketplace.products.choose_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SEO & Tags -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-search me-2"></i>
                            {{ __('marketplace.products.seo_tags') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="tags" class="form-label">{{ __('marketplace.products.tags') }}</label>
                                    <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}">
                                    <div class="form-text">{{ __('marketplace.products.tags_help') }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">{{ __('marketplace.products.meta_title') }}</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">{{ __('marketplace.products.meta_description') }}</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                {{ __('marketplace.physical_products.create_product') }}
                            </button>
                            <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('common.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.condition-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate sale price validation
    const priceInput = document.getElementById('price');
    const salePriceInput = document.getElementById('sale_price');
    
    function validateSalePrice() {
        const price = parseFloat(priceInput.value) || 0;
        const salePrice = parseFloat(salePriceInput.value) || 0;
        
        if (salePrice > 0 && salePrice >= price) {
            salePriceInput.setCustomValidity('Giá khuyến mãi phải nhỏ hơn giá gốc');
        } else {
            salePriceInput.setCustomValidity('');
        }
    }
    
    priceInput.addEventListener('input', validateSalePrice);
    salePriceInput.addEventListener('input', validateSalePrice);
});
</script>
@endpush
@endsection
