@extends('layouts.app')

@section('title', __('dashboard.layout.title'))

@push('styles')
    <!-- Dashboard Custom CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        @include('dashboard.layouts.sidebar')

        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Content -->
            <div class="dashboard-content">
                <!-- Breadcrumb -->
                @if(isset($breadcrumb) && count($breadcrumb) > 0)
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            @foreach($breadcrumb as $item)
                                @if($loop->last)
                                    <li class="breadcrumb-item active" aria-current="page">{{ $item['name'] }}</li>
                                @else
                                    <li class="breadcrumb-item">
                                        @if(isset($item['route']) && $item['route'] && Route::has($item['route']))
                                            <a href="{{ route($item['route']) }}">{{ $item['name'] }}</a>
                                        @elseif(isset($item['url']) && $item['url'])
                                            <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                                        @else
                                            {{ $item['name'] }}
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Main Content Area -->
                <main>
                    @yield('dashboard-content')
                </main>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Dashboard JS -->
    <script src="{{ asset('js/dashboard.js') }}"></script>

    <script>
        // Dashboard is automatically initialized by dashboard.js
        // No additional initialization needed here
    </script>
@endpush
