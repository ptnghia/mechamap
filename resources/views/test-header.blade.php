@extends('layouts.app')

@section('title', 'Test Header Menu - Role-based Navigation')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bx bx-test-tube me-2"></i>
                        Test Header Menu - Role-based Navigation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Hướng dẫn test:</strong> Đăng nhập với các tài khoản khác nhau để xem menu phân quyền trong header.
                    </div>

                    <!-- Current User Info -->
                    @auth
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bx bx-user me-2"></i>Thông tin người dùng hiện tại
                                    </h6>
                                    <p class="mb-1"><strong>Tên:</strong> {{ Auth::user()->name }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                                    <p class="mb-1"><strong>Role:</strong> 
                                        <span class="badge bg-primary">{{ Auth::user()->getRoleDisplayName() }}</span>
                                    </p>
                                    <p class="mb-0"><strong>Username:</strong> {{ Auth::user()->username }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bx bx-shield-check me-2"></i>Quyền truy cập
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                                        <span class="badge bg-danger">Admin Dashboard</span>
                                        @endif
                                        
                                        @if(Auth::user()->hasRole('supplier'))
                                        <span class="badge bg-success">Supplier Dashboard</span>
                                        @endif
                                        
                                        @if(Auth::user()->hasRole('manufacturer'))
                                        <span class="badge bg-primary">Manufacturer Dashboard</span>
                                        @endif
                                        
                                        @if(Auth::user()->hasRole('brand'))
                                        <span class="badge bg-warning">Brand Dashboard</span>
                                        @endif
                                        
                                        <span class="badge bg-secondary">User Features</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bx bx-user-x me-2"></i>
                        Bạn chưa đăng nhập. Vui lòng đăng nhập để test menu phân quyền.
                    </div>
                    @endauth

                    <!-- Test Accounts -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="bx bx-users me-2"></i>Tài khoản test
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Password</th>
                                            <th>Menu trong Header</th>
                                            <th>Dashboard Access</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-danger">
                                            <td><strong>Admin</strong></td>
                                            <td>admin@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="badge bg-danger">Quản trị</span>
                                            </td>
                                            <td>/admin</td>
                                        </tr>
                                        <tr class="table-warning">
                                            <td><strong>Moderator</strong></td>
                                            <td>moderator@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="badge bg-danger">Quản trị</span>
                                            </td>
                                            <td>/admin</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>Supplier</strong></td>
                                            <td>supplier@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="badge bg-success">Nhà cung cấp</span>
                                            </td>
                                            <td>/supplier/dashboard</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td><strong>Manufacturer</strong></td>
                                            <td>manufacturer@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="badge bg-primary">Nhà sản xuất</span>
                                            </td>
                                            <td>/manufacturer/dashboard</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Brand</strong></td>
                                            <td>brand@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="badge bg-warning">Thương hiệu</span>
                                            </td>
                                            <td>/brand/dashboard</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Member</strong></td>
                                            <td>member@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="text-muted">Không có menu đặc biệt</span>
                                            </td>
                                            <td>/home</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Guest</strong></td>
                                            <td>guest@mechamap.test</td>
                                            <td>password123</td>
                                            <td>
                                                <span class="text-muted">Không có menu đặc biệt</span>
                                            </td>
                                            <td>/home</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Features to Test -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="bx bx-check-square me-2"></i>Tính năng cần test
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">Header Navigation</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="bx bx-check text-success me-2"></i>Role-based dropdown menu</li>
                                                <li><i class="bx bx-check text-success me-2"></i>User info in dropdown</li>
                                                <li><i class="bx bx-check text-success me-2"></i>Quick access links</li>
                                                <li><i class="bx bx-check text-success me-2"></i>Role-specific colors</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Access Control</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><i class="bx bx-check text-success me-2"></i>Dashboard redirects</li>
                                                <li><i class="bx bx-check text-success me-2"></i>Permission checks</li>
                                                <li><i class="bx bx-check text-success me-2"></i>Role validation</li>
                                                <li><i class="bx bx-check text-success me-2"></i>Unauthorized access blocking</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @auth
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="bx bx-zap me-2"></i>Thao tác nhanh
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if(Auth::user()->hasAnyRole(['admin', 'moderator']))
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm">
                                    <i class="bx bx-tachometer me-1"></i>Admin Dashboard
                                </a>
                                @endif
                                
                                @if(Auth::user()->hasRole('supplier'))
                                <a href="{{ route('supplier.dashboard') }}" class="btn btn-success btn-sm">
                                    <i class="bx bx-store me-1"></i>Supplier Dashboard
                                </a>
                                @endif
                                
                                @if(Auth::user()->hasRole('manufacturer'))
                                <a href="{{ route('manufacturer.dashboard') }}" class="btn btn-primary btn-sm">
                                    <i class="bx bx-cube me-1"></i>Manufacturer Dashboard
                                </a>
                                @endif
                                
                                @if(Auth::user()->hasRole('brand'))
                                <a href="{{ route('brand.dashboard') }}" class="btn btn-warning btn-sm">
                                    <i class="bx bx-bullhorn me-1"></i>Brand Dashboard
                                </a>
                                @endif
                                
                                <a href="{{ route('profile.show', Auth::user()->username) }}" class="btn btn-secondary btn-sm">
                                    <i class="bx bx-user me-1"></i>My Profile
                                </a>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.875rem;
}
</style>
@endpush
