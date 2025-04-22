@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('members.index') }}" method="GET" class="d-flex">
                                <input type="text" name="filter" class="form-control me-2" placeholder="{{ __('Search members...') }}" value="{{ $filter }}">
                                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end mt-3 mt-md-0">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('members.index') }}" class="btn btn-outline-secondary {{ !request('view') ? 'active' : '' }}">
                                        <i class="bi bi-list"></i> {{ __('List') }}
                                    </a>
                                    <a href="{{ route('members.index') }}?view=grid" class="btn btn-outline-secondary {{ request('view') == 'grid' ? 'active' : '' }}">
                                        <i class="bi bi-grid-3x3-gap"></i> {{ __('Grid') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm rounded-3">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('members.index') && !request('filter') ? 'active' : '' }}" href="{{ route('members.index') }}">
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted">{{ __('Total') }}: {{ $members->total() }} {{ __('members') }}</span>
                            @if($filter)
                                <span class="ms-2">{{ __('Filtered by') }}: <strong>{{ $filter }}</strong></span>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ __('Sort by') }}: 
                                @switch($sort)
                                    @case('name')
                                        {{ __('Name') }}
                                        @break
                                    @case('posts')
                                        {{ __('Posts') }}
                                        @break
                                    @case('threads')
                                        {{ __('Threads') }}
                                        @break
                                    @default
                                        {{ __('Join Date') }}
                                @endswitch
                                ({{ $direction == 'desc' ? __('Descending') : __('Ascending') }})
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                                <li><h6 class="dropdown-header">{{ __('Sort by') }}</h6></li>
                                <li><a class="dropdown-item {{ $sort == 'joined' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'joined', 'direction' => $direction, 'filter' => $filter]) }}">{{ __('Join Date') }}</a></li>
                                <li><a class="dropdown-item {{ $sort == 'name' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'name', 'direction' => $direction, 'filter' => $filter]) }}">{{ __('Name') }}</a></li>
                                <li><a class="dropdown-item {{ $sort == 'posts' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'posts', 'direction' => $direction, 'filter' => $filter]) }}">{{ __('Posts') }}</a></li>
                                <li><a class="dropdown-item {{ $sort == 'threads' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => 'threads', 'direction' => $direction, 'filter' => $filter]) }}">{{ __('Threads') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">{{ __('Direction') }}</h6></li>
                                <li><a class="dropdown-item {{ $direction == 'desc' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => $sort, 'direction' => 'desc', 'filter' => $filter]) }}">{{ __('Descending') }}</a></li>
                                <li><a class="dropdown-item {{ $direction == 'asc' ? 'active' : '' }}" href="{{ route('members.index', ['sort' => $sort, 'direction' => 'asc', 'filter' => $filter]) }}">{{ __('Ascending') }}</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    @if($members->count() > 0)
                        @if(request('view') == 'grid')
                            <!-- Grid View -->
                            <div class="row">
                                @foreach($members as $member)
                                    <div class="col-md-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" class="rounded-circle mb-3" width="80" height="80">
                                                <h5 class="card-title mb-1">
                                                    <a href="{{ route('profile.show', $member->username) }}" class="text-decoration-none">
                                                        {{ $member->name }}
                                                    </a>
                                                </h5>
                                                <p class="text-muted mb-2">{{ '@' . $member->username }}</p>
                                                
                                                @if($member->isOnline())
                                                    <span class="badge bg-success">{{ __('Online') }}</span>
                                                @endif
                                                
                                                @if($member->role == 'admin')
                                                    <span class="badge bg-danger">{{ __('Admin') }}</span>
                                                @elseif($member->role == 'moderator')
                                                    <span class="badge bg-primary">{{ __('Moderator') }}</span>
                                                @endif
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <div class="row text-center">
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
                        @else
                            <!-- List View -->
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
                                                            
                                                            @if($member->isOnline())
                                                                <span class="badge bg-success ms-2">{{ __('Online') }}</span>
                                                            @endif
                                                            
                                                            @if($member->role == 'admin')
                                                                <span class="badge bg-danger ms-1">{{ __('Admin') }}</span>
                                                            @elseif($member->role == 'moderator')
                                                                <span class="badge bg-primary ms-1">{{ __('Moderator') }}</span>
                                                            @endif
                                                        </h5>
                                                        <p class="mb-0 text-muted small">{{ '@' . $member->username }} · {{ __('Joined') }} {{ $member->created_at->format('M Y') }}</p>
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
                                                        <div class="fw-bold">{{ $member->followers_count ?? 0 }}</div>
                                                        <div class="small text-muted">{{ __('Followers') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $members->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-people fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('No members found.') }}</p>
                            @if($filter)
                                <p class="text-muted">{{ __('Try a different search term.') }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
