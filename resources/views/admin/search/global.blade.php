@extends('admin.layouts.dason')

@section('title', 'Tìm Kiếm Toàn Cục')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Kết Quả Tìm Kiếm</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Tìm Kiếm</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Search Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Tìm kiếm: "{{ $query }}"</h5>
                        <p class="text-muted mb-0">Tìm thấy kết quả trong Users, Posts, Products và Pages</p>
                    </div>
                    <div>
                        <form action="{{ route('admin.search.global') }}" method="GET" class="d-flex">
                            <input type="text" class="form-control me-2" name="q" value="{{ $query }}" placeholder="Nhập từ khóa tìm kiếm...">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($query)
            <!-- Search Results Tabs -->
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#users-tab" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-account"></i></span>
                                <span class="d-none d-sm-block">Người Dùng</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#posts-tab" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-post"></i></span>
                                <span class="d-none d-sm-block">Bài Đăng</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#products-tab" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-package"></i></span>
                                <span class="d-none d-sm-block">Sản Phẩm</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pages-tab" role="tab">
                                <span class="d-block d-sm-none"><i class="mdi mdi-file"></i></span>
                                <span class="d-none d-sm-block">Trang</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-3 text-muted">
                        <!-- Users Tab -->
                        <div class="tab-pane active" id="users-tab" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                Chức năng tìm kiếm đang được phát triển. Sẽ sớm ra mắt!
                            </div>
                            
                            <div class="row">
                                <!-- Sample User Result -->
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/images/users/avatar-2.jpg') }}" class="rounded-circle avatar-sm me-3" alt="User">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Nguyễn Văn A</h6>
                                                    <p class="text-muted mb-1">Kỹ sư cơ khí</p>
                                                    <small class="text-muted">Tham gia: 15/01/2024</small>
                                                </div>
                                                <div>
                                                    <a href="#" class="btn btn-sm btn-outline-primary">Xem</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Empty State -->
                                <div class="col-12 text-center py-4">
                                    <i class="mdi mdi-account-search font-size-48 text-muted"></i>
                                    <h5 class="text-muted mt-2">Không tìm thấy người dùng</h5>
                                    <p class="text-muted">Thử tìm kiếm với từ khóa khác</p>
                                </div>
                            </div>
                        </div>

                        <!-- Posts Tab -->
                        <div class="tab-pane" id="posts-tab" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                Chức năng tìm kiếm bài đăng đang được phát triển.
                            </div>
                            
                            <div class="text-center py-4">
                                <i class="mdi mdi-post-outline font-size-48 text-muted"></i>
                                <h5 class="text-muted mt-2">Không tìm thấy bài đăng</h5>
                                <p class="text-muted">Thử tìm kiếm với từ khóa khác</p>
                            </div>
                        </div>

                        <!-- Products Tab -->
                        <div class="tab-pane" id="products-tab" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                Chức năng tìm kiếm sản phẩm đang được phát triển.
                            </div>
                            
                            <div class="text-center py-4">
                                <i class="mdi mdi-package-variant font-size-48 text-muted"></i>
                                <h5 class="text-muted mt-2">Không tìm thấy sản phẩm</h5>
                                <p class="text-muted">Thử tìm kiếm với từ khóa khác</p>
                            </div>
                        </div>

                        <!-- Pages Tab -->
                        <div class="tab-pane" id="pages-tab" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information-outline me-2"></i>
                                Chức năng tìm kiếm trang đang được phát triển.
                            </div>
                            
                            <div class="text-center py-4">
                                <i class="mdi mdi-file-document-outline font-size-48 text-muted"></i>
                                <h5 class="text-muted mt-2">Không tìm thấy trang</h5>
                                <p class="text-muted">Thử tìm kiếm với từ khóa khác</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Search Query -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-magnify font-size-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Nhập từ khóa để tìm kiếm</h5>
                    <p class="text-muted mb-4">Tìm kiếm người dùng, bài đăng, sản phẩm và trang trong hệ thống</p>
                    
                    <form action="{{ route('admin.search.global') }}" method="GET" class="d-inline-flex">
                        <input type="text" class="form-control me-2" name="q" placeholder="Nhập từ khóa tìm kiếm..." style="width: 300px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-magnify me-1"></i> Tìm Kiếm
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});
</script>
@endsection
