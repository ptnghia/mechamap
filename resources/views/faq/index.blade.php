@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm rounded-3 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-0">{{ __('Find answers to common questions about our community and features.') }}</p>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('search.index') }}" method="GET" class="d-flex">
                                <input type="text" name="query" class="form-control me-2" placeholder="{{ __('Search FAQ...') }}">
                                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm rounded-3 sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('Categories') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($categories as $index => $category)
                                    <a href="#category-{{ $index }}" class="list-group-item list-group-item-action">
                                        {{ $category['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9">
                    @foreach($categories as $index => $category)
                        <div class="card shadow-sm rounded-3 mb-4" id="category-{{ $index }}">
                            <div class="card-header bg-light">
                                <h4 class="card-title mb-0">{{ $category['name'] }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="accordion-{{ $index }}">
                                    @foreach($category['questions'] as $qIndex => $question)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-{{ $index }}-{{ $qIndex }}">
                                                <button class="accordion-button {{ $qIndex > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}-{{ $qIndex }}" aria-expanded="{{ $qIndex === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $index }}-{{ $qIndex }}">
                                                    {{ $question['question'] }}
                                                </button>
                                            </h2>
                                            <div id="collapse-{{ $index }}-{{ $qIndex }}" class="accordion-collapse collapse {{ $qIndex === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $index }}-{{ $qIndex }}" data-bs-parent="#accordion-{{ $index }}">
                                                <div class="accordion-body">
                                                    {{ $question['answer'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            <h5 class="mb-3">{{ __('Still have questions?') }}</h5>
                            <p>{{ __('If you couldn\'t find the answer to your question, feel free to contact us.') }}</p>
                            <a href="#" class="btn btn-primary">{{ __('Contact Support') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
