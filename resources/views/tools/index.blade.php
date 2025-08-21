@extends('layouts.app')

@section('title', 'Công cụ Kỹ thuật')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Công cụ</li>
                </ol>
            </nav>

            <div class="text-center mb-5">
                <h1 class="display-4 mb-3">
                    <i class="fa-solid fa-tools text-primary me-3"></i>
                    Công cụ Kỹ thuật
                </h1>
                <p class="lead text-muted">Bộ sưu tập công cụ, cơ sở dữ liệu và thư viện kỹ thuật chuyên nghiệp cho kỹ sư cơ khí</p>
            </div>
        </div>
    </div>

    <!-- Tools Categories -->
    <div class="row g-4">
        <!-- Calculators Section -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-calculator me-2"></i>
                        Máy tính
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">Công cụ tính toán chuyên nghiệp cho kỹ sư</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('tools.material-calculator') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-cube text-primary me-2"></i>
                                Máy tính vật liệu
                            </div>
                            <i class="fa-solid fa-arrow-right text-muted"></i>
                        </a>
                        <a href="{{ route('tools.process-calculator') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-cogs text-success me-2"></i>
                                Máy tính quy trình
                            </div>
                            <i class="fa-solid fa-arrow-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Databases Section -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-database me-2"></i>
                        {{ __('tools.categories.databases') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">{{ __('tools.categories.databases_desc') }}</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('tools.materials') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-cube text-primary me-2"></i>
                                {{ __('tools.databases.materials') }}
                            </div>
                            <span class="badge bg-primary rounded-pill">10+</span>
                        </a>
                        <a href="{{ route('tools.standards') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-clipboard-check text-warning me-2"></i>
                                {{ __('tools.databases.standards') }}
                            </div>
                            <span class="badge bg-warning rounded-pill">8+</span>
                        </a>
                        <a href="{{ route('tools.processes') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-industry text-info me-2"></i>
                                {{ __('tools.databases.processes') }}
                            </div>
                            <span class="badge bg-info rounded-pill">12+</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Libraries Section -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fa-solid fa-book me-2"></i>
                        {{ __('tools.categories.libraries') }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mb-4">{{ __('tools.categories.libraries_desc') }}</p>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('tools.cad-library') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-file-code text-warning me-2"></i>
                                {{ __('tools.libraries.cad_library') }}
                            </div>
                            <span class="badge bg-warning rounded-pill">20+</span>
                        </a>
                        <a href="{{ route('tools.technical-docs') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-wrench text-secondary me-2"></i>
                                {{ __('tools.libraries.technical_docs') }}
                            </div>
                            <i class="fa-solid fa-arrow-right text-muted"></i>
                        </a>
                        <a href="{{ route('tools.documentation') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-book-open text-primary me-2"></i>
                                {{ __('tools.libraries.documentation') }}
                            </div>
                            <span class="badge bg-primary rounded-pill">15+</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">
                        <i class="fa-solid fa-chart-bar text-primary me-2"></i>
                        {{ __('tools.stats.title') }}
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-primary mb-1">50+</h3>
                                <p class="text-muted mb-0">{{ __('tools.stats.total_resources') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-success mb-1">2</h3>
                                <p class="text-muted mb-0">{{ __('tools.stats.calculators') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-info mb-1">3</h3>
                                <p class="text-muted mb-0">{{ __('tools.stats.databases') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-item">
                                <h3 class="text-warning mb-1">3</h3>
                                <p class="text-muted mb-0">{{ __('tools.stats.libraries') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info border-0">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-info-circle fa-2x text-info me-3"></i>
                    <div>
                        <h6 class="alert-heading mb-1">{{ __('tools.help.title') }}</h6>
                        <p class="mb-0">{{ __('tools.help.description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.stat-item {
    padding: 1rem;
    border-radius: 0.5rem;
    transition: transform 0.2s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.list-group-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>
@endsection
