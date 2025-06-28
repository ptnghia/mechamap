@extends('admin.layouts.dason')

@section('title', 'Knowledge Base')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Knowledge Base</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item active">Knowledge Base</li>
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
                <h4 class="card-title">Knowledge Base Management</h4>
                <div class="card-title-desc">Manage technical articles and documentation</div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    This feature is under development. Coming soon!
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="mdi mdi-book-open-page-variant font-size-48 text-primary mb-3"></i>
                                <h5>Technical Articles</h5>
                                <p class="text-muted">Manage engineering articles and tutorials</p>
                                <button class="btn btn-primary btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="mdi mdi-file-document-multiple font-size-48 text-success mb-3"></i>
                                <h5>Documentation</h5>
                                <p class="text-muted">System and API documentation</p>
                                <button class="btn btn-success btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="mdi mdi-video-outline font-size-48 text-info mb-3"></i>
                                <h5>Video Tutorials</h5>
                                <p class="text-muted">Video guides and tutorials</p>
                                <button class="btn btn-info btn-sm" disabled>Coming Soon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
