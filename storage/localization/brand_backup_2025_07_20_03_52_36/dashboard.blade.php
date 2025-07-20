@extends('layouts.app')

@section('title', 'Bảng điều khiển Thương hiệu')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Bảng điều khiển Thương hiệu</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for View-Only Access -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                <strong>Chế độ xem:</strong> Tài khoản thương hiệu chỉ có quyền xem để phục vụ mục đích quảng cáo và phân tích thị trường.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>

    <!-- Market Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-truncate font-size-14 mb-2">Tổng sản phẩm Marketplace</p>
                            <h4 class="mb-0">{{ number_format($stats['total_marketplace_products']) }}</h4>
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
                            <p class="text-truncate font-size-14 mb-2">Tổng chủ đề Forum</p>
                            <h4 class="mb-0">{{ number_format($stats['total_forum_threads']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="bx bx-chat font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Người dùng hoạt động</p>
                            <h4 class="mb-0">{{ number_format($stats['active_users']) }}</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="bx bx-user font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Tỷ lệ tăng trưởng</p>
                            <h4 class="mb-0">+{{ number_format($stats['growth_rate']['products_growth'], 1) }}%</h4>
                        </div>
                        <div class="flex-shrink-0 align-self-center">
                            <div class="mini-stat-icon avatar-sm rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="bx bx-trending-up font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marketplace Insights & Forum Trends -->
    <div class="row">
        <!-- Top Categories -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Danh mục sản phẩm phổ biến</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Danh mục</th>
                                    <th>Số sản phẩm</th>
                                    <th>Tỷ lệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($marketplaceInsights['top_categories'] as $category)
                                <tr>
                                    <td>{{ $category->category ?? 'Chưa phân loại' }}</td>
                                    <td>{{ number_format($category->count) }}</td>
                                    <td>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-primary" style="width: {{ ($category->count / $stats['total_marketplace_products']) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hot Discussions -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thảo luận nổi bật</h4>
                </div>
                <div class="card-body">
                    @forelse($trendingTopics['hot_discussions'] as $discussion)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-1">
                            <h6 class="mb-1">{{ Str::limit($discussion->title, 50) }}</h6>
                            <p class="text-muted mb-0">
                                <i class="bx bx-message-dots me-1"></i>{{ $discussion->comments_count }} bình luận
                                <i class="bx bx-heart ms-2 me-1"></i>{{ $discussion->likes_count }} lượt thích
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-soft-primary">Trending</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Chưa có thảo luận nổi bật</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Price Range Analysis & Trending Keywords -->
    <div class="row">
        <!-- Price Range Distribution -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Phân bố giá sản phẩm</h4>
                </div>
                <div class="card-body">
                    @forelse($marketplaceInsights['price_ranges'] as $range)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>{{ $range->price_range }}</span>
                        <div class="d-flex align-items-center">
                            <div class="progress me-3" style="width: 100px; height: 6px;">
                                <div class="progress-bar bg-info" style="width: {{ ($range->count / $stats['total_marketplace_products']) * 100 }}%"></div>
                            </div>
                            <span class="text-muted">{{ $range->count }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Chưa có dữ liệu giá</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Trending Keywords -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Từ khóa xu hướng</h4>
                </div>
                <div class="card-body">
                    <div class="tag-cloud">
                        @forelse(array_slice($trendingTopics['trending_keywords'], 0, 20, true) as $keyword => $count)
                        <span class="badge bg-soft-primary me-2 mb-2" style="font-size: {{ min(16, 10 + ($count / 5)) }}px;">
                            {{ $keyword }} ({{ $count }})
                        </span>
                        @empty
                        <p class="text-center text-muted">Chưa có từ khóa xu hướng</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions for Brand -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Công cụ phân tích</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('brand.insights.index') }}" class="btn btn-primary btn-block">
                                <i class="bx bx-bulb me-2"></i>Insights chi tiết
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('brand.marketplace.analytics') }}" class="btn btn-warning btn-block">
                                <i class="bx bx-store me-2"></i>Phân tích Marketplace
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('brand.forum.analytics') }}" class="btn btn-info btn-block">
                                <i class="bx bx-chat me-2"></i>Phân tích Forum
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('brand.promotion.index') }}" class="btn btn-success btn-block">
                                <i class="bx bx-bullhorn me-2"></i>Cơ hội quảng cáo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Opportunities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Cơ hội thị trường</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-target-lock text-primary font-size-24"></i>
                                <h6 class="mt-2">Target Audience</h6>
                                <p class="text-muted">Phân tích đối tượng mục tiêu</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-trending-up text-success font-size-24"></i>
                                <h6 class="mt-2">Market Trends</h6>
                                <p class="text-muted">Xu hướng thị trường</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="bx bx-bullhorn text-info font-size-24"></i>
                                <h6 class="mt-2">Promotion Strategy</h6>
                                <p class="text-muted">Chiến lược quảng cáo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.mini-stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-block {
    width: 100%;
    margin-bottom: 10px;
}

.tag-cloud {
    line-height: 2;
}

.bg-soft-primary {
    background-color: rgba(116, 120, 141, 0.1);
    color: #74788d;
}

.progress-sm {
    height: 6px;
}
</style>
@endpush
