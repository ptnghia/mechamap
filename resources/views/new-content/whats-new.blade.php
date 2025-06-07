@extends('layouts.app')

@section('title', "What's New")

@section('content')

<div class="py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
                <li class="breadcrumb-item active" aria-current="page">What's New</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">What's New</h1>
            @auth
            <a href="{{ route('forums.select') }}" class="btn btn-primary create-thread">
                <i class="bi bi-plus-lg"></i> <span>Create thread</span>
            </a>
            @endauth
        </div>

        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'posts' ? '' : 'active' }}"
                            href="{{ route('whats-new') }}">
                            {{ __('messages.new_posts') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'popular' ? 'active' : '' }}"
                            href="{{ route('whats-new') }}?type=popular">
                            {{ __('messages.popular') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'showcase' ? 'active' : '' }}"
                            href="{{ route('whats-new') }}?type=showcase">
                            New Showcase
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'gallery' ? 'active' : '' }}"
                            href="{{ route('whats-new') }}?type=gallery">
                            New Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'articles' ? 'active' : '' }}"
                            href="{{ route('whats-new') }}?type=articles">
                            News & Articles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'replies' ? 'active' : '' }}"
                            href="{{ route('whats-new') }}?type=replies">
                            {{ __('messages.looking_for_replies') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <!-- Thread List -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="5%"></th>
                                <th scope="col" width="50%">Title</th>
                                <th scope="col" width="15%">Forum</th>
                                <th scope="col" width="10%" class="text-center">{{ __('messages.replies') }}</th>
                                <th scope="col" width="10%" class="text-center">Views</th>
                                <th scope="col" width="10%">Last Post</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($threads->count() > 0)
                            @foreach($threads as $thread)
                            <tr>
                                <td class="text-center">
                                    <i class="bi bi-chat-left-text text-primary"></i>
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ $thread->forum ? route('forums.show', $thread->forum) . '#thread-' . $thread->id : '#' }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $thread->title }}
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        By <a href="{{ route('profile.show', $thread->user->username) }}"
                                            class="text-decoration-none">{{ $thread->user->name }}</a>
                                        • {{ $thread->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ $thread->forum ? route('forums.show', $thread->forum) : '#' }}"
                                        class="text-decoration-none">
                                        {{ $thread->forum ? $thread->forum->name : 'Unknown Forum' }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ $thread->posts_count ?? 0 }}
                                </td>
                                <td class="text-center">
                                    {{ $thread->views ?? 0 }}
                                </td>
                                <td>
                                    <div class="small">
                                        {{ $thread->updated_at->diffForHumans() }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No threads found.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div>
                        Showing 1 of {{ $threads->lastPage() }}
                    </div>
                    <div>
                        {{ $threads->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection