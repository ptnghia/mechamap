@extends('layouts.app')

@section('title', 'Cơ sở dữ liệu tiêu chuẩn')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tools.index') }}">Công cụ</a></li>
                    <li class="breadcrumb-item active">Cơ sở dữ liệu tiêu chuẩn</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-2">
                        <i class="fa-solid fa-book-open me-2"></i>
                        Cơ sở dữ liệu tiêu chuẩn
                    </h1>
                    <p class="text-muted mb-0">Thư viện toàn diện các tiêu chuẩn kỹ thuật quốc tế và thông số kỹ thuật</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tools.standards') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-balance-scale me-1"></i>
                        So sánh tiêu chuẩn
                    </a>
                    <a href="{{ route('tools.standards') }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-check-circle me-1"></i>
                        Kiểm tra tuân thủ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">{{ $standards->total() ?? 0 }}</h5>
                            <p class="card-text">Tiêu chuẩn có sẵn</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">{{ $organizations->count() ?? 0 }}</h5>
                            <p class="card-text">Tổ chức</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">15+</h5>
                            <p class="card-text">Ngành được bao phủ</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-industry fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">98%</h5>
                            <p class="card-text">Tỷ lệ tuân thủ</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa-solid fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tìm kiếm tiêu chuẩn</h5>
                    <form method="GET" action="{{ route('tools.standards') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Tìm theo số tiêu chuẩn, tiêu đề hoặc mô tả..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <select class="form-select" name="organization">
                                        <option value="">Tất cả tổ chức</option>
                                        @foreach($organizations as $org)
                                            <option value="{{ $org }}" {{ request('organization') == $org ? 'selected' : '' }}>
                                                {{ $org }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-search me-1"></i>
                                        Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Standards List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Danh sách tiêu chuẩn</h5>
                </div>
                <div class="card-body">
                    @if($standards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Số tiêu chuẩn</th>
                                        <th>Tiêu đề</th>
                                        <th>Tổ chức</th>
                                        <th>Ngày phát hành</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($standards as $standard)
                                        <tr>
                                            <td><strong>{{ $standard->number ?? 'N/A' }}</strong></td>
                                            <td>{{ $standard->title ?? $standard->name ?? 'Không có tiêu đề' }}</td>
                                            <td>{{ $standard->organization ?? 'N/A' }}</td>
                                            <td>{{ $standard->published_date ? $standard->published_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-success">Có hiệu lực</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('tools.standards') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-eye me-1"></i>
                                                    Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $standards->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                            <h5>Không tìm thấy tiêu chuẩn nào</h5>
                            <p class="text-muted">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
