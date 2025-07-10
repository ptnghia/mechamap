@extends('admin.layouts.dason')

@section('title', 'Duyệt Sản Phẩm - Admin Dashboard')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Duyệt Sản Phẩm</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace.products.index') }}">Sản Phẩm</a></li>
                    <li class="breadcrumb-item active">Duyệt</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Chờ Duyệt</p>
                                <h4 class="mb-0 text-warning">{{ $pendingCount }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Đã Duyệt Hôm Nay</p>
                                <h4 class="mb-0 text-success">{{ $approvedToday }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Từ Chối Hôm Nay</p>
                                <h4 class="mb-0 text-danger">{{ $rejectedToday }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-danger">
                                    <span class="avatar-title">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium">Thời Gian Duyệt TB</p>
                                <h4 class="mb-0 text-info">{{ $avgApprovalTime }}h</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="fas fa-stopwatch"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.marketplace.products.pending') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm, seller..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Loại Seller</label>
                        <select name="seller_type" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="supplier" {{ request('seller_type') === 'supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="manufacturer" {{ request('seller_type') === 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                            <option value="brand" {{ request('seller_type') === 'brand' ? 'selected' : '' }}>Brand</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Loại Sản Phẩm</label>
                        <select name="product_type" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="physical" {{ request('product_type') === 'physical' ? 'selected' : '' }}>Vật lý</option>
                            <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>Kỹ thuật số</option>
                            <option value="service" {{ request('product_type') === 'service' ? 'selected' : '' }}>Dịch vụ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Thời gian tạo</label>
                        <select name="created_filter" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="today" {{ request('created_filter') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="yesterday" {{ request('created_filter') === 'yesterday' ? 'selected' : '' }}>Hôm qua</option>
                            <option value="week" {{ request('created_filter') === 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ request('created_filter') === 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>
                                Lọc
                            </button>
                            <a href="{{ route('admin.marketplace.products.pending') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh me-1"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pending Products List -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Sản Phẩm Chờ Duyệt ({{ $products->total() }})
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                            <i class="fas fa-check me-1"></i>
                            Duyệt Hàng Loạt
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="bulkReject()">
                            <i class="fas fa-times me-1"></i>
                            Từ Chối Hàng Loạt
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Sản Phẩm</th>
                                    <th>Seller</th>
                                    <th>Loại</th>
                                    <th>Giá</th>
                                    <th>Thời Gian Chờ</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox"
                                                   value="{{ $product->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @if($product->featured_image)
                                                    <img src="{{ $product->featured_image }}" alt="{{ $product->name }}"
                                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('admin.marketplace.products.show', $product) }}"
                                                       class="text-dark text-decoration-none">
                                                        {{ $product->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                                                @if($product->category)
                                                    <br><span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $product->seller->user->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $product->seller->business_name ?? 'N/A' }}</small>
                                            <br><span class="badge bg-{{ $product->seller_type === 'supplier' ? 'primary' : ($product->seller_type === 'manufacturer' ? 'info' : 'success') }}">
                                                {{ ucfirst($product->seller_type) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $product->product_type === 'physical' ? 'primary' : ($product->product_type === 'digital' ? 'info' : 'success') }}">
                                            {{ ucfirst($product->product_type) }}
                                        </span>
                                        @if($product->product_type === 'digital' && $product->digital_files)
                                            <br><small class="text-muted">{{ count($product->digital_files) }} files</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($product->price, 0, ',', '.') }}đ</strong>
                                        @if($product->sale_price)
                                            <br><small class="text-muted text-decoration-line-through">
                                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $product->created_at->diffForHumans() }}</span>
                                        <br><small class="text-muted">{{ $product->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-success btn-sm"
                                                    onclick="approveProduct({{ $product->id }})" title="Duyệt">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="rejectProduct({{ $product->id }})" title="Từ chối">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <a href="{{ route('admin.marketplace.products.show', $product) }}"
                                               class="btn btn-info btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('marketplace.products.show', $product->slug) }}"
                                               class="btn btn-outline-primary btn-sm" title="Xem trên marketplace" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="card-footer">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Không có sản phẩm nào chờ duyệt</h4>
                        <p class="text-muted">Tất cả sản phẩm đã được xử lý</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Từ Chối Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Lý do từ chối <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                  placeholder="Nhập lý do từ chối sản phẩm..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">
                    <i class="fas fa-times me-1"></i>
                    Từ Chối
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentProductId = null;
let selectedProducts = [];

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedProducts();
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-checkbox')) {
        updateSelectedProducts();
    }
});

// Update selected products array
function updateSelectedProducts() {
    selectedProducts = [];
    document.querySelectorAll('.product-checkbox:checked').forEach(checkbox => {
        selectedProducts.push(checkbox.value);
    });
}

// Approve single product
function approveProduct(productId) {
    if (confirm('Bạn có chắc chắn muốn duyệt sản phẩm này?')) {
        fetch(`/admin/marketplace/products/${productId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Sản phẩm đã được duyệt thành công!', 'success');
                location.reload();
            } else {
                showToast('Có lỗi xảy ra: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi duyệt sản phẩm', 'error');
        });
    }
}

// Reject single product
function rejectProduct(productId) {
    currentProductId = productId;
    const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    modal.show();
}

// Confirm rejection
function confirmReject() {
    const reason = document.getElementById('rejection_reason').value.trim();
    if (!reason) {
        showToast('Vui lòng nhập lý do từ chối', 'error');
        return;
    }

    fetch(`/admin/marketplace/products/${currentProductId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rejection_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sản phẩm đã được từ chối!', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectionModal'));
            modal.hide();
            location.reload();
        } else {
            showToast('Có lỗi xảy ra: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra khi từ chối sản phẩm', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'info')} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    // Add to toast container or create one
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }

    container.appendChild(toast);

    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove after hide
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endpush
