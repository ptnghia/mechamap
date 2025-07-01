@extends('layouts.unified')

@section('title', 'Test Dynamic Pages - MechaMap')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Header -->
    <div class="bg-white border-bottom">
        <div class="container py-4">
            <h1 class="h2 mb-2">🧪 Test Dynamic Pages System</h1>
            <p class="text-muted mb-0">Kiểm tra hệ thống trang động từ database</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-database me-2"></i>
                            Dynamic Pages Test
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>Test Links:</h6>
                        <div class="list-group">
                            <a href="{{ route('terms.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-contract me-2"></i>
                                Điều khoản sử dụng (từ database)
                            </a>
                            <a href="{{ route('privacy.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-shield-alt me-2"></i>
                                Chính sách bảo mật (từ database)
                            </a>
                            <a href="{{ route('about.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-building me-2"></i>
                                Về chúng tôi (từ database)
                            </a>
                            <a href="{{ route('contact') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-envelope me-2"></i>
                                Liên hệ (từ database)
                            </a>
                            <a href="{{ route('rules') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-gavel me-2"></i>
                                Quy định cộng đồng (từ database)
                            </a>
                        </div>

                        <hr class="my-4">

                        <h6>Page Management Links:</h6>
                        <div class="list-group">
                            <a href="{{ route('pages.categories') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-folder me-2"></i>
                                Danh mục trang
                            </a>
                            <a href="{{ route('pages.search') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-search me-2"></i>
                                Tìm kiếm trang
                            </a>
                            <a href="{{ route('pages.popular') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-fire me-2"></i>
                                Trang phổ biến
                            </a>
                            <a href="{{ route('pages.recent') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-clock me-2"></i>
                                Trang mới nhất
                            </a>
                        </div>

                        <hr class="my-4">

                        <h6>Direct Page Links:</h6>
                        <div class="list-group">
                            <a href="{{ route('pages.show', 've-chung-toi') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-link me-2"></i>
                                /pages/ve-chung-toi
                            </a>
                            <a href="{{ route('pages.show', 'dieu-khoan-su-dung') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-link me-2"></i>
                                /pages/dieu-khoan-su-dung
                            </a>
                            <a href="{{ route('pages.show', 'chinh-sach-bao-mat') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-link me-2"></i>
                                /pages/chinh-sach-bao-mat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            System Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>✅ PageController:</strong> Created
                        </div>
                        <div class="mb-3">
                            <strong>✅ Dynamic View:</strong> Created
                        </div>
                        <div class="mb-3">
                            <strong>✅ Routes:</strong> Updated
                        </div>
                        <div class="mb-3">
                            <strong>✅ Database:</strong> Seeded
                        </div>
                        <div class="mb-3">
                            <strong>✅ Static Files:</strong> Removed
                        </div>

                        <hr>

                        <h6>Database Info:</h6>
                        <div class="small text-muted">
                            <div>Page Categories: {{ \App\Models\PageCategory::count() }}</div>
                            <div>Pages: {{ \App\Models\Page::count() }}</div>
                            <div>Published: {{ \App\Models\Page::where('status', 'published')->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Admin Management
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Quản lý nội dung trang từ admin panel:
                        </p>
                        <a href="/admin/pages" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-edit me-1"></i>
                            Quản lý Pages
                        </a>
                        <a href="/admin/page-categories" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-folder me-1"></i>
                            Quản lý Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
