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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('Members who have been active in the last 15 minutes are shown here.') }}
                    </div>
                    
                    @if($members->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($members as $member)
                                <div class="list-group-item py-3 px-0">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" class="rounded-circle me-3" width="50" height="50">
                                                <div>
                                                    <h5 class="mb-1">
                                                        <a href="{{ route('profile.show', $member->username) }}" class="text-decoration-none">
                                                            {{ $member->name }}
                                                        </a>
                                                        
                                                        <span class="badge bg-success ms-2">{{ __('Online') }}</span>
                                                        
                                                        @if($member->role == 'admin')
                                                            <span class="badge bg-danger ms-1">{{ __('Admin') }}</span>
                                                        @elseif($member->role == 'moderator')
                                                            <span class="badge bg-primary ms-1">{{ __('Moderator') }}</span>
                                                        @endif
                                                    </h5>
                                                    <p class="mb-0 text-muted small">
                                                        {{ '@' . $member->username }} · 
                                                        {{ __('Last seen') }}: {{ $member->last_seen_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row text-md-end">
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $member->posts_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Posts') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $member->threads_count ?? 0 }}</div>
                                                    <div class="small text-muted">{{ __('Threads') }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold">{{ $member->created_at->format('M Y') }}</div>
                                                    <div class="small text-muted">{{ __('Joined') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $members->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('No members are currently online.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
