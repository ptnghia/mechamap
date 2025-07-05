@extends('layouts.app')

@section('title', 'Select a Forum to Post In')

@section('content')
<div class="py-4">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('forums.index') }}">Forums</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Thread</li>
            </ol>
        </nav>

        <div class="card shadow-sm rounded-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Select a forum to post in</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{ route('forums.select.submit') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for a forum" id="forum-search">
                                <button class="btn btn-outline-secondary" type="button" id="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <div id="forum-list">
                            @foreach($parentForums as $parentForum)
                                <div class="forum-category mb-4">
                                    <h6 class="fw-bold mb-3">{{ $parentForum->name }}</h6>
                                    
                                    @if($parentForum->subForums->count() > 0)
                                        <div class="list-group">
                                            @foreach($parentForum->subForums as $forum)
                                                <div class="list-group-item list-group-item-action forum-item">
                                                    <form action="{{ route('forums.select.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <h6 class="mb-1">{{ $forum->name }}</h6>
                                                                <p class="small text-muted mb-0">{{ $forum->description }}</p>
                                                            </div>
                                                            <div>
                                                                <span class="badge bg-light text-dark me-2">
                                                                    Threads: {{ $forum->threads->count() }}
                                                                </span>
                                                                <button type="submit" class="btn btn-sm btn-primary">Select</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No forums found in this category.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Popular Forums</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($popularForums as $forum)
                                        <div class="list-group-item">
                                            <form action="{{ route('forums.select.submit') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="forum_id" value="{{ $forum->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">{{ $forum->name }}</h6>
                                                        <p class="small text-muted mb-0">{{ $forum->threads_count }} threads</p>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Select</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Help</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Select a forum that best matches the topic of your thread. This helps other users find your content more easily.</p>
                                <p class="card-text">If you're not sure which forum to choose, you can:</p>
                                <ul>
                                    <li>Browse the forum categories to find the most relevant one</li>
                                    <li>Use the search box to find forums by keyword</li>
                                    <li>Choose from the popular forums list</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('forum-search');
        const forumItems = document.querySelectorAll('.forum-item');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            forumItems.forEach(item => {
                const forumName = item.querySelector('h6').textContent.toLowerCase();
                const forumDesc = item.querySelector('p').textContent.toLowerCase();
                
                if (forumName.includes(searchTerm) || forumDesc.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide category headers based on visible forums
            document.querySelectorAll('.forum-category').forEach(category => {
                const visibleForums = category.querySelectorAll('.forum-item[style="display: block"]').length;
                const categoryHeader = category.querySelector('h6');
                
                if (visibleForums === 0 && searchTerm !== '') {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });
        });
    });
</script>
@endpush
@endsection
