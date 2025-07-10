@extends('layouts.app')

@section('title', __('messages.members.online_title'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/frontend/views/members.css') }}">
@endpush

@section('content')
<div class="body_page">
    <div class="mb-4">
        <h1 class="h2 mb-1 title_page">{{ __('messages.members.online_title') }}</h1>
        <p class="mb-0 opacity-90">{{ __('messages.members.online_description') }}</p>
    </div>
    <div class="card shadow-sm rounded-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.index') ? 'active' : '' }}" href="{{ route('members.index') }}">
                        {{ __('messages.members.all_members') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.online') ? 'active' : '' }}" href="{{ route('members.online') }}">
                        {{ __('messages.members.online_now') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('members.staff') ? 'active' : '' }}" href="{{ route('members.staff') }}">
                        {{ __('messages.members.staff') }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('messages.members.online_members_info') }}
            </div>

            @if($members->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($members as $member)
                        <div class="list-group-item item_member_list py-3 px-0">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" class="rounded-circle me-3" width="50" height="50">
                                        <div>
                                            <h5 class="mb-1">
                                                <a href="{{ route('profile.show', $member->username) }}" class="text-decoration-none">
                                                    {{ $member->name }}
                                                </a>

                                                <span class="badge bg-success ms-2">{{ __('messages.members.online') }}</span>

                                                @if($member->role == 'admin')
                                                    <span class="badge bg-danger ms-1">{{ __('messages.members.admin') }}</span>
                                                @elseif($member->role == 'moderator')
                                                    <span class="badge bg-primary ms-1">{{ __('messages.members.moderator') }}</span>
                                                @endif
                                            </h5>
                                            <p class="mb-0 text-muted small">
                                                {{ '@' . $member->username }} ·
                                                {{ __('messages.members.last_seen') }}: {{ $member->last_seen_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row text-md-end">
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $member->posts_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ __('messages.members.posts') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $member->threads_count ?? 0 }}</div>
                                            <div class="small text-muted">{{ __('messages.members.threads') }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="fw-bold">{{ $member->created_at->format('M Y') }}</div>
                                            <div class="small text-muted">{{ __('messages.members.joined') }}</div>
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
                    <p class="mb-0">{{ __('messages.members.no_members_online') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
