@extends('layouts.app')

@section('title', 'Test Breadcrumb System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1>Test Breadcrumb System</h1>

            {{-- Display current breadcrumb --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Current Page Breadcrumb</h5>
                </div>
                <div class="card-body">
                    <x-breadcrumb />

                    <hr>

                    <h6>Breadcrumb Data (Debug):</h6>
                    <pre class="bg-light p-3 rounded">{{ json_encode(breadcrumb(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            {{-- Test different breadcrumb scenarios --}}
            <div class="card">
                <div class="card-header">
                    <h5>Test Different Routes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Forum Routes:</h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <a href="{{ route('forums.index') }}">Forums Index</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="{{ route('threads.index') }}">Threads Index</a>
                                </li>
                            </ul>

                            <h6>Marketplace Routes:</h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <a href="{{ route('marketplace.index') }}">Marketplace</a>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h6>Dashboard Routes:</h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <span class="text-muted">Dashboard (route not available in test)</span>
                                </li>
                            </ul>

                            <h6>Other Routes:</h6>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <a href="{{ route('home') }}">Home</a>
                                </li>
                                @if(Route::has('showcase.index'))
                                <li class="list-group-item">
                                    <a href="{{ route('showcase.index') }}">Showcase</a>
                                </li>
                                @endif
                                @if(Route::has('search.index'))
                                <li class="list-group-item">
                                    <a href="{{ route('search.index') }}">Search</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manual breadcrumb test --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Manual Breadcrumb Test</h5>
                </div>
                <div class="card-body">
                    <h6>Custom Breadcrumb Example:</h6>
                    <x-breadcrumb :items="[
                        [
                            'title' => 'Trang chủ',
                            'url' => route('home'),
                            'active' => false,
                            'icon' => 'fas fa-home'
                        ],
                        [
                            'title' => 'Diễn đàn',
                            'url' => route('forums.index'),
                            'active' => false,
                            'icon' => 'fas fa-comments'
                        ],
                        [
                            'title' => 'CAD/CAM Software',
                            'url' => '#',
                            'active' => false,
                            'icon' => 'fas fa-folder'
                        ],
                        [
                            'title' => 'SolidWorks Tips & Tricks',
                            'url' => '#',
                            'active' => true,
                            'icon' => 'fas fa-file-alt'
                        ]
                    ]" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
