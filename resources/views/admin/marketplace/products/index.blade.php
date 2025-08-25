@extends('admin.layouts.dason')

@section('title', 'Quản Lý Sản Phẩm Marketplace')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý Sản Phẩm Marketplace</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace.index') }}">Marketplace</a></li>
                    <li class="breadcrumb-item active">Sản Phẩm</li>
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
                                <p class="text-muted fw-medium">Tổng Sản Phẩm</p>
                                <h4 class="mb-0">{{ $products->total() ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i data-feather="package"></i>
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
                                <p class="text-muted fw-medium">Chờ Duyệt</p>
                                <h4 class="mb-0">0</h4>
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
                                <p class="text-muted fw-medium">Đã Duyệt</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                    <span class="avatar-title">
                                        <i class="fas fa-check-circle"></i>
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
                                <p class="text-muted fw-medium">Nổi Bật</p>
                                <h4 class="mb-0">0</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i class="fas fa-star"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Danh Sách Sản Phẩm</h4>
                        <div class="card-title-desc">Quản lý tất cả sản phẩm trong marketplace</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.marketplace.products.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus" class="me-1"></i> Thêm Sản Phẩm
                            </a>
                            <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                                <i class="fas fa-check" class="me-1"></i> Duyệt Đã Chọn
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="bulkReject()">
                                <i class="fas fa-times" class="me-1"></i> Từ Chối Đã Chọn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">Tất cả trạng thái</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Nháp</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Đình chỉ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="seller_type">
                                <option value="">Loại nhà bán</option>
                                <option value="supplier" {{ request('seller_type') === 'supplier' ? 'selected' : '' }}>Nhà cung cấp</option>
                                <option value="manufacturer" {{ request('seller_type') === 'manufacturer' ? 'selected' : '' }}>Nhà sản xuất</option>
                                <option value="brand" {{ request('seller_type') === 'brand' ? 'selected' : '' }}>Thương hiệu</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="product_type">
                                <option value="">Loại sản phẩm</option>
                                <option value="digital" {{ request('product_type') === 'digital' ? 'selected' : '' }}>Sản phẩm kỹ thuật số</option>
                                <option value="new_product" {{ request('product_type') === 'new_product' ? 'selected' : '' }}>Sản phẩm mới</option>
                                <option value="used_product" {{ request('product_type') === 'used_product' ? 'selected' : '' }}>Sản phẩm cũ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>Sản Phẩm</th>
                                <th>Nhà Bán</th>
                                <th>Loại</th>
                                <th>Giá</th>
                                <th>Kho</th>
                                <th>Thông Tin Đặc Biệt</th>
                                <th>Trạng Thái</th>
                                <th>Ngày Tạo</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products ?? [] as $product)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-checkbox" type="checkbox" value="{{ $product->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->featured_image)
                                                <img src="{{ get_product_image_url($product->featured_image) }}" alt="{{ $product->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder-product.jpg') }}'">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image" class="text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                <p class="text-muted mb-0 small">SKU: {{ $product->sku }}</p>
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $product->seller->business_name ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-0 small">{{ $product->seller->seller_type_label ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeLabels = [
                                                'digital' => 'Kỹ thuật số',
                                                'new_product' => 'Sản phẩm mới',
                                                'used_product' => 'Sản phẩm cũ'
                                            ];
                                            $typeColors = [
                                                'digital' => 'bg-primary',
                                                'new_product' => 'bg-success',
                                                'used_product' => 'bg-warning'
                                            ];
                                        @endphp
                                        <span class="badge {{ $typeColors[$product->product_type] ?? 'bg-info' }}">
                                            {{ $typeLabels[$product->product_type] ?? $product->product_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            @if($product->is_on_sale && $product->sale_price)
                                                <span class="text-decoration-line-through text-muted">{{ number_format($product->price) }} VND</span><br>
                                                <span class="text-success fw-bold">{{ number_format($product->sale_price) }} VND</span>
                                            @else
                                                <span class="fw-bold">{{ number_format($product->price) }} VND</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->product_type === 'digital')
                                            <span class="badge bg-info">
                                                <i class="fas fa-infinity me-1"></i>Không giới hạn
                                            </span>
                                        @elseif($product->manage_stock)
                                            <span class="badge {{ $product->stock_quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Không quản lý</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->product_type === 'digital')
                                            <div class="small">
                                                @if($product->digital_files && count($product->digital_files) > 0)
                                                    <span class="badge bg-success mb-1">
                                                        <i class="fas fa-file me-1"></i>{{ count($product->digital_files) }} files
                                                    </span><br>
                                                @endif
                                                @if($product->file_formats && count($product->file_formats) > 0)
                                                    <span class="text-muted">{{ implode(', ', array_slice($product->file_formats, 0, 3)) }}</span>
                                                @endif
                                            </div>
                                        @elseif($product->product_type === 'new_product')
                                            <div class="small">
                                                @if($product->material)
                                                    <span class="badge bg-info mb-1">{{ $product->material }}</span><br>
                                                @endif
                                                @if($product->manufacturing_process)
                                                    <span class="text-muted">{{ Str::limit($product->manufacturing_process, 30) }}</span>
                                                @endif
                                            </div>
                                        @elseif($product->product_type === 'used_product')
                                            <div class="small">
                                                <span class="badge bg-warning">Đã qua sử dụng</span><br>
                                                @if($product->material)
                                                    <span class="text-muted">{{ $product->material }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'draft' => 'secondary',
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'suspended' => 'dark'
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Nháp',
                                                'pending' => 'Chờ duyệt',
                                                'approved' => 'Đã duyệt',
                                                'rejected' => 'Từ chối',
                                                'suspended' => 'Đình chỉ'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$product->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$product->status] ?? $product->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $product->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($product->status === 'pending')
                                                <button type="button" class="btn btn-success" onclick="approveProduct('{{ $product->slug }}')" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" onclick="rejectProduct('{{ $product->slug }}')" title="Từ chối">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.marketplace.products.show', $product) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.marketplace.products.edit', $product) }}" class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($product->status === 'approved')
                                                <button type="button" class="btn btn-outline-warning" onclick="toggleFeatured({{ $product->id }})" title="Nổi bật">
                                                    <i class="fas fa-star{{ $product->is_featured ? '' : '-o' }}"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteProduct({{ $product->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i data-feather="package" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                            <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                                            <p class="text-muted mb-0">Thêm sản phẩm đầu tiên vào marketplace</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($products) && $products->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $products->firstItem() }} đến {{ $products->lastItem() }}
                                    của {{ $products->total() }} sản phẩm
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Check all functionality
document.getElementById('checkAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk approve
function bulkApprove() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm');
        return;
    }

    if (confirm(`Bạn có chắc muốn duyệt ${selectedIds.length} sản phẩm đã chọn?`)) {
        // TODO: Implement bulk approve
        alert('Chức năng duyệt hàng loạt sẽ được triển khai');
    }
}

// Bulk reject
function bulkReject() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm');
        return;
    }

    const reason = prompt('Lý do từ chối:');
    if (reason && confirm(`Bạn có chắc muốn từ chối ${selectedIds.length} sản phẩm đã chọn?`)) {
        // TODO: Implement bulk reject
        alert('Chức năng từ chối hàng loạt sẽ được triển khai');
    }
}

// Approve single product
function approveProduct(productSlug) {
    if (confirm('Bạn có chắc chắn muốn duyệt sản phẩm này?')) {
        fetch(`/admin/marketplace/products/${productSlug}/approve`, {
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
function rejectProduct(productSlug) {
    const reason = prompt('Vui lòng nhập lý do từ chối:');
    if (!reason || reason.trim() === '') {
        alert('Vui lòng nhập lý do từ chối');
        return;
    }

    fetch(`/admin/marketplace/products/${productSlug}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rejection_reason: reason.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Sản phẩm đã được từ chối!', 'success');
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

// Toggle featured
function toggleFeatured(productId) {
    if (confirm('Bạn có muốn thay đổi trạng thái nổi bật của sản phẩm này?')) {
        fetch(`/admin/marketplace/products/${productId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                location.reload();
            } else {
                showToast('Có lỗi xảy ra: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Có lỗi xảy ra khi thay đổi trạng thái nổi bật', 'error');
        });
    }
}

// Delete product
function deleteProduct(productId) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
        // TODO: Implement delete
        alert('Chức năng xóa sẽ được triển khai');
    }
}

// Get selected product IDs
function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Show toast notification
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to page
    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Products page loaded');
});
</script>
@endsection
