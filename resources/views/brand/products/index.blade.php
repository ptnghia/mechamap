@extends('layouts.app')

@section('title', 'Quản lý Showcase - Brand Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-star text-primary me-2"></i>
                        Quản lý Showcase Sản phẩm
                    </h1>
                    <p class="text-muted mb-0">Trưng bày sản phẩm thương hiệu của bạn trên MechaMap Marketplace</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('brand.products.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Thêm Showcase
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng showcase</h6>
                            <h4 class="mb-0">{{ $products->total() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-star text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Đang hiển thị</h6>
                            <h4 class="mb-0 text-success">{{ $products->where('status', 'approved')->count() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-show text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Lượt xem</h6>
                            <h4 class="mb-0 text-info">{{ $products->sum('view_count') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-show text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Lượt thích</h6>
                            <h4 class="mb-0 text-danger">{{ $products->sum('like_count') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-heart text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('brand.products.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"{{ t_features('brand.actions.search') }}/label>
                    <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Loại sản phẩm</label>
                    <select name="product_type" class="form-select">
                        <option value="">Tất cả loại</option>
                        <option value="physical" {{ request('product_type') === 'physical' ? 'selected' : '' }}>Vật lý</option>
                        <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>Kỹ thuật số</option>
                        <option value="service" {{ request('product_type') === 'service' ? 'selected' : '' }}>Dịch vụ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đang hiển thị</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i>
                            Lọc
                        </button>
                        <a href="{{ route('brand.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <!-- Product Image -->
                        <div class="position-relative">
                            @if($product->featured_image)
                                <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="bx bx-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($product->status === 'approved')
                                    <span class="badge bg-success">
                                        <i class="bx bx-check me-1"></i>Đang hiển thị
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bx bx-time me-1"></i>Bản nháp
                                    </span>
                                @endif
                            </div>

                            <!-- Product Type Badge -->
                            <div class="position-absolute top-0 start-0 m-2">
                                @switch($product->product_type)
                                    @case('physical')
                                        <span class="badge bg-primary">
                                            <i class="bx bx-package me-1"></i>Vật lý
                                        </span>
                                        @break
                                    @case('digital')
                                        <span class="badge bg-info">
                                            <i class="bx bx-file me-1"></i>Kỹ thuật số
                                        </span>
                                        @break
                                    @case('service')
                                        <span class="badge bg-success">
                                            <i class="bx bx-wrench me-1"></i>Dịch vụ
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- Product Info -->
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-2">{{ $product->name }}</h5>
                                
                                @if($product->category)
                                    <span class="badge bg-light text-dark mb-2">{{ $product->category->name }}</span>
                                @endif

                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->short_description ?: $product->description, 100) }}
                                </p>

                                <!-- Price -->
                                <div class="mb-3">
                                    <h6 class="text-primary mb-0">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                        @if($product->sale_price)
                                            <small class="text-muted text-decoration-line-through ms-2">
                                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                            </small>
                                        @endif
                                    </h6>
                                </div>

                                <!-- Stats -->
                                <div class="d-flex justify-content-between text-muted small mb-3">
                                    <span>
                                        <i class="bx bx-show me-1"></i>
                                        {{ $product->view_count }} lượt xem
                                    </span>
                                    <span>
                                        <i class="bx bx-heart me-1"></i>
                                        {{ $product->like_count }} lượt thích
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('marketplace.products.show', $product->slug) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill" target="_blank">
                                    <i class="bx bx-show me-1"></i>
                                    Xem
                                </a>
                                <a href="{{ route('brand.products.edit', $product) }}" 
                                   class="btn btn-outline-secondary btn-sm flex-fill">
                                    <i class="bx bx-edit me-1"></i>
                                    Sửa
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-danger btn-sm dropdown-toggle" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-horizontal-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('brand.products.destroy', $product) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa showcase này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Created Date -->
                            <small class="text-muted mt-2">
                                Tạo ngày {{ $product->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bx bx-star text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Chưa có showcase nào</h4>
                <p class="text-muted">Bắt đầu bằng việc tạo showcase sản phẩm đầu tiên của thương hiệu</p>
                <a href="{{ route('brand.products.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>
                    Tạo Showcase
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.card {
    border-radius: 0.75rem;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
}

.card-img-top {
    border-radius: 0.75rem 0.75rem 0 0;
}
</style>
@endpush
