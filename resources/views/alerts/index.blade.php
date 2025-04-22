@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($alerts as $alert)
                                <div class="list-group-item list-group-item-action py-3 px-0 {{ $alert->read_at ? '' : 'bg-light' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">
                                                @if(!$alert->read_at)
                                                    <span class="badge bg-primary me-2">{{ __('New') }}</span>
                                                @endif
                                                {{ $alert->title }}
                                            </h6>
                                            <p class="mb-1">{{ $alert->content }}</p>
                                            <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="d-flex">
                                            @if(!$alert->read_at)
                                                <form action="{{ route('alerts.read', $alert) }}" method="POST" class="me-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        {{ __('Mark as Read') }}
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('alerts.destroy', $alert) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    {{ __('Delete') }}
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
                            <p class="mb-0">{{ __('You don\'t have any alerts.') }}</p>
                            <p class="text-muted">{{ __('When you receive notifications, they will appear here.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
