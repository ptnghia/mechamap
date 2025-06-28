@extends('layouts.app')

@section('title', 'About MechaMap')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-info-circle text-primary me-2"></i>
                        About MechaMap
                    </h1>
                    <p class="text-muted mb-0">Learn about our mission to revolutionize the mechanical engineering community</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fa-solid fa-envelope me-1"></i>
                        Contact Us
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fa-solid fa-users me-1"></i>
                        Join Community
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
                    <i class="fa-solid fa-cogs text-primary mb-4" style="font-size: 5rem;"></i>
                    <h2 class="text-primary mb-3">About MechaMap</h2>
                    <p class="text-muted mb-4 lead">
                        MechaMap is Vietnam's premier platform for the mechanical engineering community, 
                        connecting professionals, suppliers, and innovators in one comprehensive ecosystem.
                    </p>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-bullseye text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Our Mission</h6>
                                        <small class="text-muted">Empowering mechanical engineers with cutting-edge tools and resources</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-eye text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>Our Vision</h6>
                                        <small class="text-muted">To be the leading platform for mechanical engineering innovation in Vietnam</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-heart text-danger mb-2" style="font-size: 2rem;"></i>
                                        <h6>Our Values</h6>
                                        <small class="text-muted">Innovation, collaboration, excellence, and community-driven growth</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-users text-info mb-2" style="font-size: 2rem;"></i>
                                        <h6>Our Community</h6>
                                        <small class="text-muted">60+ active members across Vietnam's engineering industry</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <h4 class="text-primary mb-3">What We Offer</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="text-start">
                                        <h6><i class="fa-solid fa-store text-primary me-2"></i>Marketplace</h6>
                                        <p class="text-muted small">Connect with verified suppliers and find quality mechanical components</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="text-start">
                                        <h6><i class="fa-solid fa-comments text-primary me-2"></i>Community Forums</h6>
                                        <p class="text-muted small">Engage with fellow engineers and share knowledge</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="text-start">
                                        <h6><i class="fa-solid fa-database text-primary me-2"></i>Technical Resources</h6>
                                        <p class="text-muted small">Access comprehensive materials database and engineering standards</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="text-start">
                                        <h6><i class="fa-solid fa-briefcase text-primary me-2"></i>Career Opportunities</h6>
                                        <p class="text-muted small">Find jobs and connect with top engineering companies</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="fa-solid fa-rocket me-2"></i>
                        <strong>Join the Revolution:</strong> Be part of Vietnam's growing mechanical engineering community.
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home me-1"></i>
                            Explore Platform
                        </a>
                        <a href="{{ route('forums.index') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-users me-1"></i>
                            Join Community
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-success">
                            <i class="fa-solid fa-store me-1"></i>
                            Visit Marketplace
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
