@extends('admin.layouts.app')

@section('title', 'Marketplace Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Marketplace Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Marketplace</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng Sản Phẩm</p>
                            <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
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
                            <p class="text-truncate font-size-14 mb-2">Sản Phẩm Kỹ Thuật Số</p>
                            <h4 class="mb-0">{{ $stats['digital_products'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-download font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Sản Phẩm Mới</p>
                            <h4 class="mb-0">{{ $stats['new_products'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-box font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Sản Phẩm Cũ</p>
                            <h4 class="mb-0">{{ $stats['used_products'] }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-recycle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Permission Matrix --}}
    <div class="row">
        <div class="col-12">
            @include('admin.marketplace.components.permission-matrix')
        </div>
    </div>

    {{-- Product Distribution Chart --}}
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân Bố Sản Phẩm Theo Loại</h4>
                </div>
                <div class="card-body">
                    <canvas id="productTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sản Phẩm Theo Seller Type</h4>
                </div>
                <div class="card-body">
                    <canvas id="sellerTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Products --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sản Phẩm Mới Nhất</h4>
                    <div class="card-title-desc">10 sản phẩm được tạo gần đây nhất</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th>Loại</th>
                                    <th>Seller Type</th>
                                    <th>Giá</th>
                                    <th>Trạng Thái</th>
                                    <th>Ngày Tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->featured_image)
                                                <img src="{{ asset('storage/' . $product->featured_image) }}" 
                                                     alt="{{ $product->name }}" class="avatar-sm rounded me-3">
                                            @else
                                                <div class="avatar-sm bg-light rounded me-3 d-flex align-items-center justify-content-center">
                                                    <i class="bx bx-package text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ Str::limit($product->name, 40) }}</h6>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </div>
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
                                        <span class="badge bg-light text-dark">{{ ucfirst($product->seller_type) }}</span>
                                    </td>
                                    <td>{{ number_format($product->price) }}đ</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'draft' => 'bg-secondary'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$product->status] ?? 'bg-info' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Product Type Chart
const productTypeCtx = document.getElementById('productTypeChart').getContext('2d');
new Chart(productTypeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Kỹ thuật số', 'Sản phẩm mới', 'Sản phẩm cũ'],
        datasets: [{
            data: [{{ $stats['digital_products'] }}, {{ $stats['new_products'] }}, {{ $stats['used_products'] }}],
            backgroundColor: ['#556ee6', '#34c38f', '#f1b44c'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Seller Type Chart
const sellerTypeCtx = document.getElementById('sellerTypeChart').getContext('2d');
new Chart(sellerTypeCtx, {
    type: 'bar',
    data: {
        labels: ['Supplier', 'Manufacturer', 'Brand'],
        datasets: [{
            label: 'Số lượng sản phẩm',
            data: [{{ $stats['supplier_products'] }}, {{ $stats['manufacturer_products'] }}, {{ $stats['brand_products'] }}],
            backgroundColor: ['#556ee6', '#34c38f', '#f1b44c'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
