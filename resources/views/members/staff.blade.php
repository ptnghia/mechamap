@extends('layouts.app')

@section('title', t_common("members.staff_title"))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/members.css') }}">
@endpush

@section('content')
<div class="body_page">
     <div class="mb-4">
        <h1 class="h2 mb-1 title_page">{{ t_common("members.staff_title") }}</h1>
        <p class="mb-0 opacity-90">{{ t_common("members.staff_description") }}</p>
    </div>
    <div class="card shadow-sm rounded-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }}" href="{{ route('members.index') }}">
                        {{ t_common("members.all_members") }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.online') ? 'active' : '' }}" href="{{ route('members.online') }}">
                        {{ t_common("members.online_now") }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.staff') ? 'active' : '' }}" href="{{ route('members.staff') }}">
                        {{ t_common("members.staff") }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <!-- Administrators -->
            <h4 class="mb-3 title_page_sub">{{ t_common("members.administrators") }}</h4>

            @if($admins->count() > 0)
                <div class="row mb-5">
                    @foreach($admins as $admin)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 item_member_grid">
                                <div class="card-body text-center">
                                    <img src="{{ $admin->getAvatarUrl() }}" alt="{{ $admin->name }}" class="rounded-circle mb-3" width="100" height="100">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('profile.show', $admin->username) }}" class="text-decoration-none">
                                            {{ $admin->name }}
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-2">{{ '@' . $admin->username }}</p>

                                    <span class="badge bg-danger mb-2">{{ t_common("members.administrator") }}</span>

                                    @if($admin->isOnline())
                                        <span class="badge bg-success ms-1">{{ t_common("members.online") }}</span>
                                    @endif

                                    <p class="small mb-0">{{ $admin->bio ?? t_common("members.no_bio_available") }}</p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $admin->posts_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ t_common("members.posts") }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $admin->threads_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ t_common("members.threads") }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $admin->created_at->format('M Y') }}</div>
                                            <div class="small text-muted">{{ t_common("members.joined") }}</div>
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
                    {{ t_common("members.no_administrators_found") }}
                </div>
            @endif

            <!-- Moderators -->
            <h4 class="mb-3 title_page_sub">{{ t_common("members.moderators") }}</h4>

            @if($moderators->count() > 0)
                <div class="row">
                    @foreach($moderators as $moderator)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 item_member_grid">
                                <div class="card-body text-center">
                                    <img src="{{ $moderator->getAvatarUrl() }}" alt="{{ $moderator->name }}" class="rounded-circle mb-3" width="100" height="100">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('profile.show', $moderator->username) }}" class="text-decoration-none">
                                            {{ $moderator->name }}
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-2">{{ '@' . $moderator->username }}</p>

                                    <span class="badge bg-primary mb-2">{{ t_common("members.moderator") }}</span>

                                    @if($moderator->isOnline())
                                        <span class="badge bg-success ms-1">{{ t_common("members.online") }}</span>
                                    @endif

                                    <p class="small mb-0">{{ $moderator->bio ?? t_common("members.no_bio_available") }}</p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $moderator->posts_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ t_common("members.posts") }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $moderator->threads_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ t_common("members.threads") }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $moderator->created_at->format('M Y') }}</div>
                                            <div class="small text-muted">{{ t_common("members.joined") }}</div>
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
                    {{ t_common("members.no_moderators_found") }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
