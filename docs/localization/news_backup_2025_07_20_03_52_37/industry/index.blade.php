@extends('layouts.app')

@section('title', 'Industry News')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-newspaper text-primary me-2"></i>
                        Industry News
                    </h1>
                    <p class="text-muted mb-0">Latest news and updates from the mechanical engineering industry</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fa-solid fa-rss me-1"></i>
                        Subscribe to Feed
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fa-solid fa-bell me-1"></i>
                        News Alerts
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
                    <i class="fa-solid fa-newspaper text-primary mb-4" style="font-size: 5rem;"></i>
                    <h2 class="text-primary mb-3">Industry News Coming Soon</h2>
                    <p class="text-muted mb-4 lead">
                        Stay informed with the latest developments, innovations, and trends 
                        in the mechanical engineering and manufacturing industry.
                    </p>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-rocket text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6>Innovation News</h6>
                                        <small class="text-muted">Latest technological breakthroughs</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-chart-line text-info mb-2" style="font-size: 2rem;"></i>
                                        <h6>Market Trends</h6>
                                        <small class="text-muted">Industry analysis and forecasts</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-building text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>Company Updates</h6>
                                        <small class="text-muted">Business news and partnerships</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Coming Soon:</strong> Real-time industry news aggregated from trusted sources worldwide.
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home me-1"></i>
                            Back to Home
                        </a>
                        <a href="{{ route('whats-new') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-fire-flame-curved me-1"></i>
                            What's New
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
