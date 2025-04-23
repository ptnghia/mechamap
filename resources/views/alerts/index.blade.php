@extends('layouts.app')

@section('title', 'Alerts')

@section('content')

    <div class="py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ __('Alerts') }}</h1>

                @if($alerts->count() > 0)
                    <form action="{{ route('alerts.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-check-all me-1"></i> {{ __('Mark All as Read') }}
                        </button>
                    </form>
                @endif
            </div>

            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Notifications') }}</h5>
                        <div class="d-flex align-items-center">
                            <div class="dropdown me-3">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filtersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-funnel me-1"></i> {{ __('Filter') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filtersDropdown">
                                    <li><a class="dropdown-item {{ !isset($filter) ? 'active' : '' }}" href="{{ route('alerts.index') }}">{{ __('All notifications') }}</a></li>
                                    <li><a class="dropdown-item {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('alerts.index', ['filter' => 'unread']) }}">{{ __('Unread only') }}</a></li>
                                    <li><a class="dropdown-item {{ $filter === 'read' ? 'active' : '' }}" href="{{ route('alerts.index', ['filter' => 'read']) }}">{{ __('Read only') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item {{ $filter === 'messages' ? 'active' : '' }}" href="{{ route('alerts.index', ['filter' => 'messages']) }}">{{ __('Messages only') }}</a></li>
                                </ul>
                            </div>
                            <span class="badge bg-primary">{{ $alerts->where('read_at', null)->count() }} {{ __('unread') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($alerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($alerts as $alert)
                                <div class="list-group-item list-group-item-action py-3 px-3 {{ $alert->read_at ? '' : 'bg-light' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-start">
                                            <div class="alert-icon me-3">
                                                @if($alert->alertable_type === 'App\\Models\\Conversation')
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-chat-dots text-white"></i>
                                                    </div>
                                                @elseif($alert->type === 'success')
                                                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-check-lg text-white"></i>
                                                    </div>
                                                @elseif($alert->type === 'warning')
                                                    <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-exclamation-triangle text-white"></i>
                                                    </div>
                                                @elseif($alert->type === 'danger')
                                                    <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-x-circle text-white"></i>
                                                    </div>
                                                @else
                                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-info-circle text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1 d-flex align-items-center">
                                                    @if(!$alert->read_at)
                                                        <span class="badge bg-primary me-2">{{ __('New') }}</span>
                                                    @endif
                                                    <span class="{{ !$alert->read_at ? 'fw-bold' : '' }}">{{ $alert->title }}</span>
                                                </h6>
                                                <p class="mb-1">{{ $alert->content }}</p>
                                                <div class="d-flex align-items-center">
                                                    <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>

                                                    @if($alert->alertable_type === 'App\\Models\\Conversation' && $alert->alertable)
                                                        <a href="{{ route('conversations.show', $alert->alertable_id) }}" class="btn btn-sm btn-link ms-2 p-0">
                                                            {{ __('View conversation') }} <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    @elseif($alert->alertable_type === 'App\\Models\\Comment' && $alert->alertable)
                                                        <a href="{{ route('threads.show', $alert->alertable->thread_id) }}#comment-{{ $alert->alertable_id }}" class="btn btn-sm btn-link ms-2 p-0">
                                                            {{ __('View comment') }} <i class="bi bi-arrow-right"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            @if(!$alert->read_at)
                                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="me-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-check"></i> {{ __('Mark as Read') }}
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('alerts.destroy', $alert) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $alerts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-bell fs-1 text-muted mb-3"></i>
                            <p class="mb-0">{{ __('You don\'t have any notifications.') }}</p>
                            <p class="text-muted">{{ __('When you receive alerts or messages, they will appear here.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
