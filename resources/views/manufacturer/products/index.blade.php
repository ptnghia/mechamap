@extends('layouts.app')

@section('title', 'Quản lý Sản phẩm - Manufacturer Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-1">
                        <i class="bx bx-cog text-primary me-2"></i>
                        Quản lý Sản phẩm Kỹ thuật
                    </h1>
                    <p class="text-muted mb-0">Quản lý file CAD, thiết kế và dịch vụ kỹ thuật trên MechaMap Marketplace</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manufacturer.products.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Thêm Sản phẩm
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
                            <h6 class="text-muted mb-1">Tổng sản phẩm</h6>
                            <h4 class="mb-0">{{ $products->total() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-file text-primary" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted mb-1">File CAD</h6>
                            <h4 class="mb-0 text-info">{{ $products->where('product_type', 'digital')->count() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-cube text-info" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted mb-1">Dịch vụ</h6>
                            <h4 class="mb-0 text-success">{{ $products->where('product_type', 'service')->count() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-wrench text-success" style="font-size: 2rem;"></i>
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
                            <h6 class="text-muted mb-1">Đã duyệt</h6>
                            <h4 class="mb-0 text-success">{{ $products->where('status', 'approved')->count() }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bx bx-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('manufacturer.products.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="...">{{ t_features('manufacturer.actions.search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm, SKU..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Loại sản phẩm</label>
                    <select name="product_type" class="form-select">
                        <option value="">Tất cả loại</option>
                        <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>File CAD</option>
                        <option value="service" {{ request('product_type') === 'service' ? 'selected' : '' }}>Dịch vụ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
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
                        <a href="{{ route('manufacturer.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Loại</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>File/Dịch vụ</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            @if($product->featured_image)
                                                <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" 
                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="bx {{ $product->product_type === 'digital' ? 'bx-file' : 'bx-wrench' }} text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->product_type === 'digital')
                                        <span class="badge bg-info">
                                            <i class="bx bx-file me-1"></i>File CAD
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bx bx-wrench me-1"></i>Dịch vụ
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">Chưa phân loại</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ number_format($product->price, 0, ',', '.') }}đ</strong>
                                        @if($product->sale_price)
                                            <br><small class="text-muted text-decoration-line-through">
                                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($product->product_type === 'digital' && $product->digital_files)
                                        <span class="badge bg-primary">{{ count($product->digital_files) }} files</span>
                                        @if($product->file_size_mb)
                                            <br><small class="text-muted">{{ $product->file_size_mb }}MB</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Dịch vụ</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($product->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">Bản nháp</span>
                                            @break
                                        @case('pending')
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">Đã duyệt</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Bị từ chối</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ $product->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small class="text-muted">{{ $product->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('marketplace.products.show', $product->slug) }}" target="_blank">
                                                    <i class="bx bx-show me-2"></i>Xem sản phẩm
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('manufacturer.products.edit', $product) }}">
                                                    <i class="bx bx-edit me-2"></i>Chỉnh sửa
                                                </a>
                                            </li>
                                            @if($product->product_type === 'digital' && $product->digital_files)
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="showFiles({{ $product->id }})">
                                                        <i class="bx bx-download me-2"></i>Xem files
                                                    </a>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('manufacturer.products.destroy', $product) }}" method="POST" 
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-2"></i>Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="card-footer bg-transparent">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bx bx-file text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Chưa có sản phẩm nào</h4>
                    <p class="text-muted">Bắt đầu bằng việc thêm file CAD hoặc dịch vụ kỹ thuật đầu tiên</p>
                    <a href="{{ route('manufacturer.products.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>
                        Thêm Sản phẩm
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
}

.card {
    border-radius: 0.75rem;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush
