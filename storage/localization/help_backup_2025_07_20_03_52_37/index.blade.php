@extends('layouts.app')

@section('title', 'Help Center')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-life-ring text-primary me-2"></i>
                        Help Center
                    </h1>
                    <p class="text-muted mb-0">Get help and support for using MechaMap platform</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fa-solid fa-search me-1"></i>
                        Search Help
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fa-solid fa-envelope me-1"></i>
                        Contact Support
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Content -->
    <div class="row">
        <div class="col-12">
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="fa-solid fa-life-ring text-primary mb-4" style="font-size: 5rem;"></i>
                    <h2 class="text-primary mb-3">Help Center Coming Soon</h2>
                    <p class="text-muted mb-4 lead">
                        We're building a comprehensive help center with tutorials, guides, 
                        and support resources to help you make the most of MechaMap.
                    </p>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-question-circle text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6>FAQ</h6>
                                        <small class="text-muted">Frequently asked questions</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-play-circle text-info mb-2" style="font-size: 2rem;"></i>
                                        <h6>Video Tutorials</h6>
                                        <small class="text-muted">Step-by-step video guides</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-headset text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>Live Support</h6>
                                        <small class="text-muted">Chat with our support team</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Need Help Now?</strong> Visit our FAQ section or contact support directly.
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('faq.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-question-circle me-1"></i>
                            View FAQ
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-home me-1"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
