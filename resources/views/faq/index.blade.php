@extends('layouts.app')

@section('title', 'Câu hỏi thường gặp - MechaMap')

@push('styles')
<style>
.faq-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.faq-search {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
}
.faq-search::placeholder {
    color: rgba(255,255,255,0.7);
}
.faq-search:focus {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
}
.category-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.accordion-button {
    font-weight: 600;
    background: #f8f9fa;
}
.accordion-button:not(.collapsed) {
    background: #e3f2fd;
    color: #1976d2;
}
.faq-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin: 0 auto 20px;
}
</style>
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Header -->
    <div class="faq-header py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="faq-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">❓ Câu hỏi thường gặp</h1>
                    <p class="lead mb-0">Tìm câu trả lời cho các thắc mắc về cộng đồng kỹ sư cơ khí MechaMap</p>
                </div>
                <div class="col-lg-4">
                    <div class="card bg-transparent border-0">
                        <div class="card-body">
                            <form action="{{ route('search.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="query" class="form-control faq-search"
                                           placeholder="Tìm kiếm câu hỏi...">
                                    <button type="submit" class="btn btn-light">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container py-5">

            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm rounded-3 sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ __('faq.categories') }}</h5>
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
                            <h5 class="mb-3">{{ __('faq.still_have_questions') }}</h5>
                            <p>{{ __('If you couldn\'t find the answer to your question, feel free to contact us.') }}</p>
                            <a href="#" class="btn btn-primary">{{ __('faq.contact_support') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
