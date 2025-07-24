@extends('admin.layouts.dason')

@section('title', 'Quản lý Nhà bán hàng')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản lý Nhà bán hàng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Marketplace</a></li>
                        <li class="breadcrumb-item active">Nhà bán hàng</li>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng nhà bán hàng</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="bx bx-store font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Đã xác minh</p>
                            <h4 class="mb-0">{{ $stats['verified'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-check-shield font-size-24"></i>
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
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-time font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Hoạt động</p>
                            <h4 class="mb-0">{{ $stats['active'] ?? 0 }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-user-check font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sellers Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Danh sách nhà bán hàng</h4>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary" onclick="exportSellers()">
                                <i class="bx bx-export me-1"></i> Xuất Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="active">Hoạt động</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="suspended">Tạm khóa</option>
                                <option value="banned">Cấm</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="verificationFilter">
                                <option value="">Tất cả xác minh</option>
                                <option value="verified">Đã xác minh</option>
                                <option value="unverified">Chưa xác minh</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">Tất cả loại</option>
                                <option value="individual">Cá nhân</option>
                                <option value="business">Doanh nghiệp</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm nhà bán hàng..." id="searchInput">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0" id="sellersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhà bán hàng</th>
                                    <th>Loại</th>
                                    <th>Sản phẩm</th>
                                    <th>Doanh thu</th>
                                    <th>Đánh giá</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tham gia</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sellers ?? [] as $seller)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <img src="{{ $seller->avatar_url ?? '/images/default-avatar.png' }}"
                                                     alt="{{ $seller->business_name ?? $seller->name }}"
                                                     class="img-fluid rounded-circle">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $seller->business_name ?? $seller->name }}</h6>
                                                <p class="text-muted mb-0">{{ $seller->email }}</p>
                                                @if($seller->is_verified)
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bx bx-check-shield me-1"></i>Đã xác minh
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($seller->seller_type === 'business')
                                            <span class="badge bg-primary">Doanh nghiệp</span>
                                        @else
                                            <span class="badge bg-info">Cá nhân</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $seller->products_count ?? 0 }}</span>
                                        <small class="text-muted">sản phẩm</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($seller->total_revenue ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= ($seller->rating ?? 0))
                                                        <i class="bx bxs-star"></i>
                                                    @else
                                                        <i class="bx bx-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-muted">({{ $seller->reviews_count ?? 0 }})</span>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($seller->status)
                                            @case('active')
                                                <span class="badge bg-success">Hoạt động</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                                @break
                                            @case('suspended')
                                                <span class="badge bg-danger">Tạm khóa</span>
                                                @break
                                            @case('banned')
                                                <span class="badge bg-dark">Cấm</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($seller->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $seller->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.sellers.show', $seller) }}">
                                                    <i class="fas fa-eye font-size-16 text-success me-1"></i> Xem chi tiết
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.marketplace.sellers.edit', $seller) }}">
                                                    <i class="fas fa-edit font-size-16 text-success me-1"></i> Chỉnh sửa
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($seller->status === 'active')
                                                    <li><a class="dropdown-item text-warning" href="#" onclick="suspendSeller({{ $seller->id }})">
                                                        <i class="mdi mdi-pause font-size-16 text-warning me-1"></i> Tạm khóa
                                                    </a></li>
                                                @else
                                                    <li><a class="dropdown-item text-success" href="#" onclick="activateSeller({{ $seller->id }})">
                                                        <i class="mdi mdi-play font-size-16 text-success me-1"></i> Kích hoạt
                                                    </a></li>
                                                @endif
                                                <li><a class="dropdown-item text-danger" href="#" onclick="banSeller({{ $seller->id }})">
                                                    <i class="mdi mdi-cancel font-size-16 text-danger me-1"></i> Cấm
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-store font-size-48 text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Chưa có nhà bán hàng nào</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($sellers) && $sellers->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <div class="dataTables_info">
                                Hiển thị {{ $sellers->firstItem() }} đến {{ $sellers->lastItem() }}
                                trong tổng số {{ $sellers->total() }} nhà bán hàng
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="dataTables_paginate paging_simple_numbers float-end">
                                {{ $sellers->links() }}
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
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        // Implement search logic
    });

    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('verificationFilter').addEventListener('change', function() {
        // Implement filter logic
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        // Implement filter logic
    });
});

function exportSellers() {
    // Implement export logic
    console.log('Export sellers');
}

function suspendSeller(id) {
    if (confirm('Bạn có chắc chắn muốn tạm khóa nhà bán hàng này?')) {
        // Implement suspend logic
        console.log('Suspend seller:', id);
    }
}

function activateSeller(id) {
    if (confirm('Bạn có chắc chắn muốn kích hoạt nhà bán hàng này?')) {
        // Implement activate logic
        console.log('Activate seller:', id);
    }
}

function banSeller(id) {
    if (confirm('Bạn có chắc chắn muốn cấm nhà bán hàng này? Hành động này không thể hoàn tác.')) {
        // Implement ban logic
        console.log('Ban seller:', id);
    }
}
</script>
@endsection
