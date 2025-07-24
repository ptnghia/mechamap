@extends('layouts.app')

@section('title', 'Knowledge Base')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fa-solid fa-book-open text-primary me-2"></i>
                        Knowledge Base
                    </h1>
                    <p class="text-muted mb-0">Comprehensive technical knowledge and best practices for mechanical engineering</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fa-solid fa-search me-1"></i>
                        Search Knowledge
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fa-solid fa-bookmark me-1"></i>
                        My Bookmarks
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
                    <i class="fa-solid fa-book-open text-primary mb-4" style="font-size: 5rem;"></i>
                    <h2 class="text-primary mb-3">Knowledge Base Coming Soon</h2>
                    <p class="text-muted mb-4 lead">
                        We're building a comprehensive knowledge base with technical articles, best practices, 
                        and expert insights for the mechanical engineering community.
                    </p>
                    
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-lightbulb text-warning mb-2" style="font-size: 2rem;"></i>
                                        <h6>Technical Articles</h6>
                                        <small class="text-muted">In-depth technical guides and tutorials</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-tools text-info mb-2" style="font-size: 2rem;"></i>
                                        <h6>Best Practices</h6>
                                        <small class="text-muted">Industry standards and methodologies</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="bg-light rounded p-3">
                                        <i class="fa-solid fa-graduation-cap text-success mb-2" style="font-size: 2rem;"></i>
                                        <h6>Expert Insights</h6>
                                        <small class="text-muted">Professional tips and case studies</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <strong>Stay Updated:</strong> Subscribe to our newsletter to be notified when the Knowledge Base launches.
                    </div>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home me-1"></i>
                            Back to Home
                        </a>
                        <a href="{{ route('forums.index') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-comments me-1"></i>
                            Visit Forums
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
