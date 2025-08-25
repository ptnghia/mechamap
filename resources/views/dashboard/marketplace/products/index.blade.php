@extends('layouts.user-dashboard')

@section('title', 'Quản lý sản phẩm')

@push('styles')
<style>
.stats-card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}
.stats-card:hover {
    transform: translateY(-2px);
}
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}
.product-card {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.product-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #667eea;
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}
.status-draft { background: #f8f9fa; color: #6c757d; }
.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d1edff; color: #0c63e4; }
.status-rejected { background: #f8d7da; color: #721c24; }
.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}
.filter-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-box text-primary me-2"></i>
                Quản lý sản phẩm
            </h1>
            <p class="text-muted">Quản lý tất cả sản phẩm của bạn trên marketplace</p>
        </div>
        <div>
            <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-2"></i>Thêm sản phẩm
            </a>
            <a href="{{ route('dashboard.marketplace.seller.products.create.advanced') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm chuyên nghiệp
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-primary me-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Tổng sản phẩm</h6>
                        <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-success me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Đã duyệt</h6>
                        <h4 class="mb-0">{{ $stats['approved'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-warning me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Chờ duyệt</h6>
                        <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-info me-3">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0">Lượt xem</h6>
                        <h4 class="mb-0">{{ number_format($stats['total_views'] ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('dashboard.marketplace.seller.products.index') }}">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Tên sản phẩm, SKU...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="product_type" class="form-label">Loại sản phẩm</label>
                    <select class="form-select" id="product_type" name="product_type">
                        <option value="">Tất cả</option>
                        <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>Sản phẩm số</option>
                        <option value="new_product" {{ request('product_type') === 'new_product' ? 'selected' : '' }}>Sản phẩm mới</option>
                        <option value="used_product" {{ request('product_type') === 'used_product' ? 'selected' : '' }}>Sản phẩm cũ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="category" class="form-label">Danh mục</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Tất cả</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Tìm kiếm
                    </button>
                    <a href="{{ route('dashboard.marketplace.seller.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Products List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Danh sách sản phẩm ({{ $products->total() }})
            </h5>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="view" id="grid-view" checked>
                <label class="btn btn-outline-primary" for="grid-view">
                    <i class="fas fa-th"></i>
                </label>
                <input type="radio" class="btn-check" name="view" id="list-view">
                <label class="btn btn-outline-primary" for="list-view">
                    <i class="fas fa-list"></i>
                </label>
            </div>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="row" id="products-grid">
                    @foreach($products as $product)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="product-card p-3">
                                <div class="d-flex align-items-start">
                                    <img src="{{ $product->featured_image ? asset('storage/' . $product->featured_image) : asset('images/placeholder-product.png') }}"
                                         alt="{{ $product->name }}" class="product-image me-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="#" class="text-decoration-none text-dark">
                                                {{ Str::limit($product->name, 40) }}
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-2">SKU: {{ $product->sku }}</p>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-primary">
                                                {{ number_format($product->price, 0, ',', '.') }} VND
                                            </span>
                                            <span class="status-badge status-{{ $product->status }}">
                                                @switch($product->status)
                                                    @case('draft') Bản nháp @break
                                                    @case('pending') Chờ duyệt @break
                                                    @case('approved') Đã duyệt @break
                                                    @case('rejected') Bị từ chối @break
                                                    @default {{ $product->status }}
                                                @endswitch
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted small">
                                            <span>
                                                <i class="fas fa-eye me-1"></i>{{ $product->view_count }}
                                            </span>
                                            <span>
                                                <i class="fas fa-shopping-cart me-1"></i>{{ $product->purchase_count }}
                                            </span>
                                            <span>{{ $product->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <a href="#" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('dashboard.marketplace.seller.products.edit', $product) }}" class="btn btn-outline-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger"
                                                        onclick="confirmDelete('{{ $product->slug }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                    <p class="text-muted">Bắt đầu bán hàng bằng cách tạo sản phẩm đầu tiên của bạn</p>
                    <a href="{{ route('dashboard.marketplace.seller.products.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tạo sản phẩm đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(productSlug) {
    const form = document.getElementById('deleteForm');
    form.action = `/dashboard/marketplace/seller/products/${productSlug}`;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// View toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const productsContainer = document.getElementById('products-grid');

    listView.addEventListener('change', function() {
        if (this.checked) {
            productsContainer.className = 'list-group';
            // Convert to list view
            const products = productsContainer.querySelectorAll('.col-md-6');
            products.forEach(product => {
                product.className = 'list-group-item';
            });
        }
    });

    gridView.addEventListener('change', function() {
        if (this.checked) {
            productsContainer.className = 'row';
            // Convert back to grid view
            const products = productsContainer.querySelectorAll('.list-group-item');
            products.forEach(product => {
                product.className = 'col-md-6 col-lg-4 mb-4';
            });
        }
    });
});
</script>
@endpush
