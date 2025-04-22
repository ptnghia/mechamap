@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Advanced Search Options') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('search.advanced.submit') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="keywords" class="form-label">{{ __('Keywords') }}</label>
                                <input type="text" name="keywords" id="keywords" class="form-control" value="{{ old('keywords') }}">
                                <div class="form-text">{{ __('Enter keywords to search for.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label for="author" class="form-label">{{ __('Author') }}</label>
                                <input type="text" name="author" id="author" class="form-control" value="{{ old('author') }}">
                                <div class="form-text">{{ __('Enter a username to search for posts by a specific author.') }}</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="forum_id" class="form-label">{{ __('Forum') }}</label>
                                <select name="forum_id" id="forum_id" class="form-select">
                                    <option value="">{{ __('All Forums') }}</option>
                                    @foreach($forums as $forum)
                                        <option value="{{ $forum->id }}" {{ old('forum_id') == $forum->id ? 'selected' : '' }}>
                                            {{ $forum->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">{{ __('Select a forum to search in.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from" class="form-label">{{ __('Date From') }}</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ old('date_from') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to" class="form-label">{{ __('Date To') }}</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ old('date_to') }}">
                                    </div>
                                </div>
                                <div class="form-text">{{ __('Specify a date range to search within.') }}</div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="sort_by" class="form-label">{{ __('Sort By') }}</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="relevance" {{ old('sort_by') == 'relevance' ? 'selected' : '' }}>{{ __('Relevance') }}</option>
                                    <option value="date" {{ old('sort_by', 'date') == 'date' ? 'selected' : '' }}>{{ __('Date') }}</option>
                                    <option value="replies" {{ old('sort_by') == 'replies' ? 'selected' : '' }}>{{ __('Number of Replies') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sort_dir" class="form-label">{{ __('Sort Direction') }}</label>
                                <select name="sort_dir" id="sort_dir" class="form-select">
                                    <option value="desc" {{ old('sort_dir', 'desc') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                                    <option value="asc" {{ old('sort_dir') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> {{ __('Back to Basic Search') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> {{ __('Search') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
