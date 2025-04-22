@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('actions')
    <div class="btn-group me-2">
        <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-share"></i> {{ __('Export') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-printer"></i> {{ __('Print') }}
        </button>
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-calendar3"></i> {{ __('This week') }}
    </button>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ __('Total Users') }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['users']) }}</h2>
                            <p class="text-success mb-0">
                                <i class="bi bi-arrow-up"></i> {{ $stats['new_users_today'] }} {{ __('today') }}
                            </p>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ __('Total Threads') }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['threads']) }}</h2>
                            <p class="text-success mb-0">
                                <i class="bi bi-arrow-up"></i> {{ $stats['new_threads_today'] }} {{ __('today') }}
                            </p>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-chat-left-text fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-0">{{ __('Total Posts') }}</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($stats['posts']) }}</h2>
                            <p class="text-success mb-0">
                                <i class="bi bi-arrow-up"></i> {{ $stats['new_posts_today'] }} {{ __('today') }}
                            </p>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-chat-right-text fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Latest Users') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Joined') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="32" height="32">
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <div class="small text-muted">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->isAdmin())
                                                <span class="badge bg-danger">{{ __('Admin') }}</span>
                                            @elseif($user->isModerator())
                                                <span class="badge bg-primary">{{ __('Moderator') }}</span>
                                            @elseif($user->isSenior())
                                                <span class="badge bg-success">{{ __('Senior') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Member') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-sm btn-primary">{{ __('View All Users') }}</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Latest Threads') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Author') }}</th>
                                    <th>{{ __('Forum') }}</th>
                                    <th>{{ __('Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestThreads as $thread)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-truncate" style="max-width: 200px;">
                                                {{ $thread->title }}
                                            </div>
                                        </td>
                                        <td>{{ $thread->user->name }}</td>
                                        <td>
                                            @if($thread->forum)
                                                {{ $thread->forum->name }}
                                            @else
                                                <span class="text-muted">{{ __('Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $thread->created_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-sm btn-primary">{{ __('View All Threads') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
