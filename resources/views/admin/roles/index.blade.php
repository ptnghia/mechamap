@extends('admin.layouts.dason')

@section('title', 'Roles & Permissions')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Roles & Permissions</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Roles & Permissions</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Roles & Permissions Management</h4>
                <div class="card-title-desc">Manage user roles and their permissions</div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    This feature is under development. Coming soon!
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Available Roles</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Admin
                                <span class="badge bg-primary rounded-pill">Full Access</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Moderator
                                <span class="badge bg-success rounded-pill">Limited Access</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Senior Member
                                <span class="badge bg-info rounded-pill">Forum Access</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Member
                                <span class="badge bg-secondary rounded-pill">Basic Access</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Permission Categories</h5>
                        <ul class="list-group">
                            <li class="list-group-item">User Management</li>
                            <li class="list-group-item">Content Management</li>
                            <li class="list-group-item">Forum Moderation</li>
                            <li class="list-group-item">System Settings</li>
                            <li class="list-group-item">Marketplace Management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
