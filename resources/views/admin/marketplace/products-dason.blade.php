@extends('admin.layouts.dason')

@section('title', 'Quản lý Sản phẩm Marketplace')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý Sản phẩm</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Marketplace</a></li>
                        <li class="breadcrumb-item active">Sản phẩm</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng sản phẩm</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-package font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Đang hoạt động</p>
                            <h4 class="mb-0">{{ $stats['active'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Nổi bật</p>
                            <h4 class="mb-0">{{ $stats['featured'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-star font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Chờ duyệt</p>
                            <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách sản phẩm</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.marketplace.products.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Thêm sản phẩm
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                                <option value="pending">Chờ duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                <option value="mechanical-parts">Linh kiện cơ khí</option>
                                <option value="cad-files">File CAD</option>
                                <option value="materials">Vật liệu</option>
                                <option value="tools">Công cụ</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" onclick="exportProducts()">
                                <i class="bx bx-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="productsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Giá</th>
                                    <th>Người bán</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products ?? [] as $product)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $product->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <img src="{{ $product->image_url ?? '/images/placeholder-product.jpg' }}"
                                                     alt="{{ $product->name }}" class="img-fluid rounded">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $product->name }}</h6>
                                                <p class="text-muted mb-0">SKU: {{ $product->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="fw-bold text-primary">${{ number_format($product->price, 2) }}</span>
                                    </td>
                                    <td>{{ $product->seller->business_name ?? $product->seller->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">Hoạt động</span>
                                        @elseif($product->status === 'pending')
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @else
                                            <span class="badge bg-danger">Không hoạt động</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.products.show', $product) }}">
                                                    <i class="mdi mdi-eye font-size-16 text-success me-1"></i> Xem
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.products.edit', $product) }}">
                                                    <i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Sửa
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct({{ $product->id }})">
                                                    <i class="mdi mdi-trash-can font-size-16 text-danger me-1"></i> Xóa
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-package font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có sản phẩm nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($products) && $products->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="dataTables_info">
                                Hiển thị {{ $products->firstItem() }} đến {{ $products->lastItem() }}
                                trong tổng số {{ $products->total() }} sản phẩm
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable-like functionality
    const table = document.getElementById('productsTable');

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Implement search logic
    });

    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('categoryFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    // Check all functionality
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});

function deleteProduct(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        // Implement delete logic
        console.log('Delete product:', id);
    }
}

function exportProducts() {
    // Implement export logic
    console.log('Export products');
}
</script>
@endsection
