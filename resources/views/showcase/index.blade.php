@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="py-5">
    <div class="container">
        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <div class="alert alert-info">
                    <h5 class="alert-heading">{{ __('About Showcase') }}</h5>
                    <p>{{ __('Your showcase is a collection of your best content that you want to highlight on your
                        profile. Add your favorite threads, posts, or projects to showcase your contributions.') }}</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('My Showcase Items') }}</h5>
            </div>
            <div class="card-body">
                @if($showcaseItems->count() > 0)
                <div class="row">
                    @foreach($showcaseItems as $item)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            @if($item->showcaseable_type === 'App\\Models\\Thread' && $item->showcaseable &&
                            $item->showcaseable->featured_image)
                            <img src="{{ $item->showcaseable->featured_image }}" class="card-img-top"
                                style="height: 200px; object-fit: cover;" alt="{{ $item->showcaseable->title }}">
                            @endif
                            <div class="card-body">
                                @if($item->showcaseable_type === 'App\\Models\\Thread')
                                <h5 class="card-title">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    <a href="{{ route('threads.show', $item->showcaseable) }}"
                                        class="text-decoration-none">
                                        {{ $item->showcaseable->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ __('Thread in') }} {{ $item->showcaseable->forum->name }}
                                </p>
                                @elseif($item->showcaseable_type === 'App\\Models\\Post')
                                <h5 class="card-title">
                                    <i class="bi bi-chat-right me-2"></i>
                                    <a href="{{ route('threads.show', $item->showcaseable->thread_id) }}#post-{{ $item->showcaseable->id }}"
                                        class="text-decoration-none">
                                        {{ __('Reply in') }} {{ $item->showcaseable->thread->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit(strip_tags($item->showcaseable->content), 100) }}
                                </p>
                                @elseif($item->showcaseable_type === 'App\\Models\\Project')
                                <h5 class="card-title">
                                    <i class="bi bi-briefcase me-2"></i>
                                    <a href="{{ route('projects.show', $item->showcaseable) }}"
                                        class="text-decoration-none">
                                        {{ $item->showcaseable->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($item->showcaseable->description, 100) }}
                                </p>
                                @else
                                <h5 class="card-title">
                                    <i class="bi bi-star me-2"></i>
                                    {{ __('Showcase item') }}
                                </h5>
                                @endif

                                @if($item->description)
                                <div class="mt-3 p-2 bg-light rounded">
                                    <small>{{ __('Why I\'m showcasing this') }}: {{ $item->description }}</small>
                                </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ __('Added') }} {{ $item->created_at->diffForHumans()
                                        }}</small>
                                    <form action="{{ route('showcase.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $showcaseItems->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-stars fs-1 text-muted mb-3"></i>
                    <p class="mb-0">{{ __('You don\'t have any showcase items yet.') }}</p>
                    <p class="text-muted">{{ __('Add your best content to your showcase to highlight it on your
                        profile.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection