@extends('admin.layouts.dason')

@section('title', 'Test Permissions')

@section('page-title', 'Test Permissions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
    <li class="breadcrumb-item active">Test Permissions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permission Test Results</h4>
            </div>
            <div class="card-body">
                @php
                    $user = auth()->user();
                @endphp

                <h5>User Information</h5>
                <ul>
                    <li><strong>Name:</strong> {{ $user->name }}</li>
                    <li><strong>Email:</strong> {{ $user->email }}</li>
                    <li><strong>Role:</strong> {{ $user->role }}</li>
                    <li><strong>Role Group:</strong> {{ $user->role_group ?? 'N/A' }}</li>
                </ul>

                <h5>Permission Tests</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Permission/Check</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>User Role</td>
                            <td><span class="badge bg-info">{{ $user->role }}</span></td>
                        </tr>
                        <tr>
                            <td>User Role === 'super_admin'</td>
                            <td>
                                @if($user->role === 'super_admin')
                                    <span class="badge bg-success">TRUE</span>
                                @else
                                    <span class="badge bg-danger">FALSE</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>AdminPermissionHelper::isAdmin()</td>
                            <td>
                                @if(\App\Helpers\AdminPermissionHelper::isAdmin())
                                    <span class="badge bg-success">TRUE</span>
                                @else
                                    <span class="badge bg-danger">FALSE</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>User hasPermission('manage-categories')</td>
                            <td>
                                @if($user->hasPermission('manage-categories'))
                                    <span class="badge bg-success">TRUE</span>
                                @else
                                    <span class="badge bg-danger">FALSE</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>User hasPermission('view_products')</td>
                            <td>
                                @if($user->hasPermission('view_products'))
                                    <span class="badge bg-success">TRUE</span>
                                @else
                                    <span class="badge bg-danger">FALSE</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
