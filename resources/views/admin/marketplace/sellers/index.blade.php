@extends('admin.layouts.dason')

@section('title', 'Quản Lý Nhà Bán Hàng')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản Lý Nhà Bán Hàng</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace.index') }}">Marketplace</a></li>
                    <li class="breadcrumb-item active">Nhà Bán Hàng</li>
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
                                <p class="text-muted fw-medium">Tổng Nhà Bán</p>
                                <h4 class="mb-0">{{ $stats['total_sellers'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                    <span class="avatar-title">
                                        <i class="fas fa-users"></i>
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
                                <p class="text-muted fw-medium">Đã Xác Minh</p>
                                <h4 class="mb-0">{{ $stats['verified_sellers'] ?? 0 }}</h4>
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
                                <p class="text-muted fw-medium">Chờ Xác Minh</p>
                                <h4 class="mb-0">{{ $stats['pending_verification'] ?? 0 }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                    <span class="avatar-title">
                                        <i data-feather="clock"></i>
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
                                <p class="text-muted fw-medium">Doanh Thu</p>
                                <h4 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0) }} VND</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                    <span class="avatar-title">
                                        <i data-feather="dollar-sign"></i>
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
                        <h4 class="card-title">Danh Sách Nhà Bán Hàng</h4>
                        <div class="card-title-desc">Quản lý tất cả nhà bán hàng trong marketplace</div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group">
                            <a href="{{ route('admin.marketplace.sellers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus" class="me-1"></i> Thêm Nhà Bán Hàng
                            </a>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportSellers()">
                                <i data-feather="download" class="me-1"></i> Xuất Excel
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
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm tên doanh nghiệp, email...">
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
                            <select class="form-select" name="verification_status">
                                <option value="">Trạng thái xác minh</option>
                                <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>Chờ xác minh</option>
                                <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>Đã xác minh</option>
                                <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">Trạng thái hoạt động</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Đình chỉ</option>
                                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Bị cấm</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="is_featured">
                                <option value="">Tất cả</option>
                                <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>Nổi bật</option>
                                <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>Thường</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Sellers Table -->
                <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Doanh Nghiệp</th>
                                <th>Người Liên Hệ</th>
                                <th>Loại</th>
                                <th>Xác Minh</th>
                                <th>Trạng Thái</th>
                                <th>Sản Phẩm</th>
                                <th>Đánh Giá</th>
                                <th>Ngày Tham Gia</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sellers ?? [] as $seller)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($seller->store_logo)
                                                <img src="{{ Storage::url($seller->store_logo) }}" alt="{{ $seller->business_name }}" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i data-feather="building" class="text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $seller->business_name }}</h6>
                                                @if($seller->business_registration_number)
                                                    <p class="text-muted mb-0 small">MST: {{ $seller->business_registration_number }}</p>
                                                @endif
                                                @if($seller->is_featured)
                                                    <span class="badge bg-warning">Nổi bật</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $seller->contact_person_name }}</h6>
                                            <p class="text-muted mb-0 small">{{ $seller->contact_email }}</p>
                                            @if($seller->contact_phone)
                                                <p class="text-muted mb-0 small">{{ $seller->contact_phone }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $seller->seller_type_label }}</span>
                                        <p class="text-muted mb-0 small mt-1">{{ ucfirst($seller->business_type) }}</p>
                                    </td>
                                    <td>
                                        @php
                                            $verificationColors = [
                                                'pending' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $verificationColors[$seller->verification_status] ?? 'secondary' }}">
                                            {{ $seller->verification_status_label }}
                                        </span>
                                        @if($seller->verified_at)
                                            <p class="text-muted mb-0 small mt-1">{{ $seller->verified_at->format('d/m/Y') }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'suspended' => 'warning',
                                                'banned' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$seller->status] ?? 'secondary' }}">
                                            {{ $seller->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-bold">{{ $seller->total_products }}</span> sản phẩm
                                            <p class="text-muted mb-0 small">Hoạt động: {{ $seller->active_products }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $seller->rating_average)
                                                        <i data-feather="star" class="text-warning" style="width: 14px; height: 14px; fill: currentColor;"></i>
                                                    @else
                                                        <i data-feather="star" class="text-muted" style="width: 14px; height: 14px;"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <small class="text-muted">{{ number_format($seller->rating_average, 1) }} ({{ $seller->rating_count }})</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $seller->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.marketplace.sellers.show', $seller) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.marketplace.sellers.edit', $seller) }}" class="btn btn-outline-secondary" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($seller->verification_status === 'pending')
                                                <button type="button" class="btn btn-outline-success" onclick="verifySeller({{ $seller->id }})" title="Xác minh">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="rejectSeller({{ $seller->id }})" title="Từ chối">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            @if($seller->status === 'active')
                                                <button type="button" class="btn btn-outline-warning" onclick="suspendSeller({{ $seller->id }})" title="Đình chỉ">
                                                    <i data-feather="pause"></i>
                                                </button>
                                            @elseif($seller->status === 'suspended')
                                                <button type="button" class="btn btn-outline-success" onclick="reactivateSeller({{ $seller->id }})" title="Kích hoạt lại">
                                                    <i data-feather="play"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-info" onclick="toggleFeatured({{ $seller->id }})" title="Nổi bật">
                                                <i data-feather="star"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-users" style="width: 48px; height: 48px;" class="text-muted mb-2"></i>
                                            <h5 class="text-muted">Chưa có nhà bán hàng nào</h5>
                                            <p class="text-muted mb-0">Thêm nhà bán hàng đầu tiên vào marketplace</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(isset($sellers) && $sellers->hasPages())
                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">
                                    Hiển thị {{ $sellers->firstItem() }} đến {{ $sellers->lastItem() }}
                                    của {{ $sellers->total() }} nhà bán hàng
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $sellers->links() }}
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
// Export sellers
function exportSellers() {
    alert('Chức năng xuất Excel sẽ được triển khai');
}

// Verify seller
function verifySeller(sellerId) {
    const notes = prompt('Ghi chú xác minh (tùy chọn):');
    if (confirm('Bạn có chắc muốn xác minh nhà bán hàng này?')) {
        // TODO: Implement verify seller
        alert('Chức năng xác minh sẽ được triển khai');
    }
}

// Reject seller
function rejectSeller(sellerId) {
    const reason = prompt('Lý do từ chối xác minh:');
    if (reason && confirm('Bạn có chắc muốn từ chối xác minh nhà bán hàng này?')) {
        // TODO: Implement reject seller
        alert('Chức năng từ chối xác minh sẽ được triển khai');
    }
}

// Suspend seller
function suspendSeller(sellerId) {
    const reason = prompt('Lý do đình chỉ:');
    if (reason && confirm('Bạn có chắc muốn đình chỉ nhà bán hàng này?')) {
        // TODO: Implement suspend seller
        alert('Chức năng đình chỉ sẽ được triển khai');
    }
}

// Reactivate seller
function reactivateSeller(sellerId) {
    if (confirm('Bạn có chắc muốn kích hoạt lại nhà bán hàng này?')) {
        // TODO: Implement reactivate seller
        alert('Chức năng kích hoạt lại sẽ được triển khai');
    }
}

// Toggle featured
function toggleFeatured(sellerId) {
    if (confirm('Bạn có muốn thay đổi trạng thái nổi bật của nhà bán hàng này?')) {
        // TODO: Implement toggle featured
        alert('Chức năng đánh dấu nổi bật sẽ được triển khai');
    }
}

// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch (error) {
            console.warn('Feather Icons error in sellers page:', error);
        }
    }
});
</script>
@endsection
