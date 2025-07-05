@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }}" href="{{ route('members.index') }}">
                                {{ __('All Members') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('members.online') ? 'active' : '' }}" href="{{ route('members.online') }}">
                                {{ __('Online Now') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('members.staff') ? 'active' : '' }}" href="{{ route('members.staff') }}">
                                {{ __('Staff') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Administrators -->
                    <h4 class="mb-3">{{ __('Administrators') }}</h4>
                    
                    @if($admins->count() > 0)
                        <div class="row mb-5">
                            @foreach($admins as $admin)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <img src="{{ $admin->getAvatarUrl() }}" alt="{{ $admin->name }}" class="rounded-circle mb-3" width="100" height="100">
                                            <h5 class="card-title mb-1">
                                                <a href="{{ route('profile.show', $admin->username) }}" class="text-decoration-none">
                                                    {{ $admin->name }}
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-2">{{ '@' . $admin->username }}</p>
                                            
                                            <span class="badge bg-danger mb-2">{{ __('Administrator') }}</span>
                                            
                                            @if($admin->isOnline())
                                                <span class="badge bg-success ms-1">{{ __('Online') }}</span>
                                            @endif
                                            
                                            <p class="small mb-0">{{ $admin->bio ?? __('No bio available.') }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $admin->posts_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Posts') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $admin->threads_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Threads') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $admin->created_at->format('M Y') }}</div>
                                                    <div class="small text-muted">{{ __('Joined') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-5">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('No administrators found.') }}
                        </div>
                    @endif
                    
                    <!-- Moderators -->
                    <h4 class="mb-3">{{ __('Moderators') }}</h4>
                    
                    @if($moderators->count() > 0)
                        <div class="row">
                            @foreach($moderators as $moderator)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <img src="{{ $moderator->getAvatarUrl() }}" alt="{{ $moderator->name }}" class="rounded-circle mb-3" width="100" height="100">
                                            <h5 class="card-title mb-1">
                                                <a href="{{ route('profile.show', $moderator->username) }}" class="text-decoration-none">
                                                    {{ $moderator->name }}
                                                </a>
                                            </h5>
                                            <p class="text-muted mb-2">{{ '@' . $moderator->username }}</p>
                                            
                                            <span class="badge bg-primary mb-2">{{ __('Moderator') }}</span>
                                            
                                            @if($moderator->isOnline())
                                                <span class="badge bg-success ms-1">{{ __('Online') }}</span>
                                            @endif
                                            
                                            <p class="small mb-0">{{ $moderator->bio ?? __('No bio available.') }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $moderator->posts_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Posts') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $moderator->threads_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Threads') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $moderator->created_at->format('M Y') }}</div>
                                                    <div class="small text-muted">{{ __('Joined') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('No moderators found.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
