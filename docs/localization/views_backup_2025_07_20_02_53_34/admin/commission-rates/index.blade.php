@extends('admin.layouts.dason')

@section('title', 'Quản Lý Tỷ Lệ Hoa Hồng')

@section('css')
<link href="{{ asset('css/admin/verification.css') }}" rel="stylesheet" type="text/css" />
<style>
.commission-rate-card {
    border-left: 4px solid #0d6efd;
    transition: all 0.3s ease;
}
.commission-rate-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.rate-input {
    width: 80px;
    text-align: center;
}
.rate-badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Quản Lý Tỷ Lệ Hoa Hồng</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tỷ Lệ Hoa Hồng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card commission-rate-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Tổng Nhà Bán</p>
                            <h4 class="mb-2">{{ $statistics['total_sellers'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    {{ $statistics['verified_sellers'] ?? 0 }} đã xác thực
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-store-2-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card commission-rate-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Hoa Hồng Tháng Này</p>
                            <h4 class="mb-2">{{ number_format($statistics['total_commission_this_month'] ?? 0) }} VND</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="ri-arrow-right-up-line me-1 align-middle"></i>
                                    +12.5%
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-money-dollar-circle-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card commission-rate-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Tỷ Lệ Trung Bình</p>
                            <h4 class="mb-2">{{ number_format($statistics['average_commission_rate'] ?? 0, 1) }}%</h4>
                            <p class="text-muted mb-0">
                                <span class="text-info fw-bold font-size-12 me-2">
                                    <i class="ri-information-line me-1 align-middle"></i>
                                    Tất cả vai trò
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-percent-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card commission-rate-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Cập Nhật Gần Đây</p>
                            <h4 class="mb-2">{{ count($recentChanges) }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-warning fw-bold font-size-12 me-2">
                                    <i class="ri-time-line me-1 align-middle"></i>
                                    7 ngày qua
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-history-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Rates Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Cấu Hình Tỷ Lệ Hoa Hồng</h4>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetToDefaults()">
                                <i class="ri-refresh-line me-1"></i> Khôi Phục Mặc Định
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveAllRates()">
                                <i class="ri-save-line me-1"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="commissionRatesForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-nowrap table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vai Trò</th>
                                        <th>Số Người Dùng</th>
                                        <th>Tỷ Lệ Hiện Tại (%)</th>
                                        <th>Tỷ Lệ Mặc Định (%)</th>
                                        <th>Tỷ Lệ Mới (%)</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($commissionRates as $rate)
                                    <tr data-role="{{ $rate['role'] }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        @if(str_contains($rate['role'], 'manufacturer'))
                                                            <i class="ri-building-line"></i>
                                                        @elseif(str_contains($rate['role'], 'supplier'))
                                                            <i class="ri-truck-line"></i>
                                                        @elseif(str_contains($rate['role'], 'brand'))
                                                            <i class="ri-award-line"></i>
                                                        @else
                                                            <i class="ri-vip-crown-line"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-size-14 mb-1">{{ $rate['display_name'] }}</h5>
                                                    <p class="text-muted font-size-12 mb-0">{{ $rate['role'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info">{{ $rate['user_count'] }} người</span>
                                        </td>
                                        <td>
                                            <span class="rate-badge badge bg-soft-primary text-primary">
                                                {{ number_format($rate['current_rate'], 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="rate-badge badge bg-soft-secondary text-secondary">
                                                {{ number_format($rate['default_rate'], 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control rate-input" 
                                                   name="rates[{{ $rate['role'] }}]"
                                                   value="{{ $rate['current_rate'] }}"
                                                   min="0" 
                                                   max="50" 
                                                   step="0.1"
                                                   data-original="{{ $rate['current_rate'] }}">
                                        </td>
                                        <td>
                                            <span class="status-indicator badge bg-soft-success text-success">
                                                <i class="ri-check-line me-1"></i> Hoạt động
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Changes -->
    @if(count($recentChanges) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thay Đổi Gần Đây</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Thời Gian</th>
                                    <th>Vai Trò</th>
                                    <th>Tỷ Lệ Cũ</th>
                                    <th>Tỷ Lệ Mới</th>
                                    <th>Người Thực Hiện</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentChanges as $change)
                                <tr>
                                    <td>{{ $change['created_at'] ?? 'N/A' }}</td>
                                    <td>{{ $change['role'] ?? 'N/A' }}</td>
                                    <td>{{ $change['old_rate'] ?? 'N/A' }}%</td>
                                    <td>{{ $change['new_rate'] ?? 'N/A' }}%</td>
                                    <td>{{ $change['admin_name'] ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('script')
<script>
// Commission Rate Management JavaScript
function saveAllRates() {
    const formData = new FormData(document.getElementById('commissionRatesForm'));
    
    fetch('{{ route("admin.commission-rates.update") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Thành công!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Lỗi!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Lỗi!',
            text: 'Có lỗi xảy ra khi cập nhật tỷ lệ hoa hồng',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function resetToDefaults() {
    Swal.fire({
        title: 'Xác nhận khôi phục?',
        text: 'Bạn có chắc muốn khôi phục tất cả tỷ lệ hoa hồng về mặc định?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Khôi phục',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.commission-rates.reset-defaults") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Thành công!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

// Highlight changed rates
document.querySelectorAll('.rate-input').forEach(input => {
    input.addEventListener('input', function() {
        const original = parseFloat(this.dataset.original);
        const current = parseFloat(this.value);
        
        if (Math.abs(original - current) > 0.01) {
            this.classList.add('border-warning');
            this.classList.remove('border-success');
        } else {
            this.classList.remove('border-warning');
            this.classList.add('border-success');
        }
    });
});
</script>
@endsection
