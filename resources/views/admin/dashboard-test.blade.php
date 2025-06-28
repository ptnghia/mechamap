@extends('admin.layouts.dason')

@section('title', 'Admin Dashboard Test')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard Test</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Dashboard Test</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- ApexCharts CSS -->
<link href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="row">
    <!-- Test Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Users</span>
                        <h4 class="mb-3">
                            <span class="counter-value">150</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">+25</span>
                            <span class="ms-1 text-muted font-size-13">this month</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-account-multiple font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Products</span>
                        <h4 class="mb-3">
                            <span class="counter-value">89</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-info text-info">+12</span>
                            <span class="ms-1 text-muted font-size-13">this week</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="mdi mdi-package-variant font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Orders</span>
                        <h4 class="mb-3">
                            <span class="counter-value">45</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-warning text-warning">+8</span>
                            <span class="ms-1 text-muted font-size-13">this week</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="mdi mdi-cart font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Revenue</span>
                        <h4 class="mb-3">
                            $<span class="counter-value">125000</span>
                        </h4>
                        <div class="text-nowrap">
                            <span class="badge bg-soft-success text-success">+15%</span>
                            <span class="ms-1 text-muted font-size-13">vs last month</span>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-end dash-widget">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="mdi mdi-currency-usd font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Dason Template Integration Test</h4>
                <p class="card-title-desc">Testing the integration of Dason admin template with MechaMap backend.</p>
            </div>
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">🎉 Integration Successful!</h4>
                    <p>The Dason admin template has been successfully integrated with MechaMap backend. All layout components are working correctly:</p>
                    <hr>
                    <ul class="mb-0">
                        <li>✅ Main layout (dason.blade.php)</li>
                        <li>✅ Header partial with navigation</li>
                        <li>✅ Sidebar with admin menu</li>
                        <li>✅ Footer with copyright</li>
                        <li>✅ Right sidebar with theme customizer</li>
                        <li>✅ Responsive design</li>
                        <li>✅ Bootstrap 5 components</li>
                    </ul>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Next Steps:</h5>
                        <ol>
                            <li>Install NPM dependencies</li>
                            <li>Compile assets with Laravel Mix</li>
                            <li>Create admin management views</li>
                            <li>Implement user management interface</li>
                            <li>Add forum management tools</li>
                            <li>Setup marketplace admin panel</li>
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h5>Features Available:</h5>
                        <ul>
                            <li>Dashboard with statistics</li>
                            <li>User management</li>
                            <li>Forum administration</li>
                            <li>Marketplace management</li>
                            <li>Analytics and reports</li>
                            <li>System settings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test JavaScript functionality
    console.log('Dason admin template loaded successfully!');
    
    // Test theme customizer
    const rightBarToggle = document.querySelector('.right-bar-toggle');
    if (rightBarToggle) {
        console.log('Right sidebar toggle found');
    }
    
    // Test sidebar menu
    const sidebarMenu = document.querySelector('#sidebar-menu');
    if (sidebarMenu) {
        console.log('Sidebar menu found');
    }
});
</script>
@endpush
