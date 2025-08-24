@props(['breadcrumbs' => []])

@if(!empty($breadcrumbs))
<div class="breadcrumb-container bg-light border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="py-2">
                    <ol class="breadcrumb mb-0">
                        @foreach($breadcrumbs as $index => $breadcrumb)
                            @if($breadcrumb['active'] ?? false)
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span class="text-muted">{{ $breadcrumb['title'] }}</span>
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none text-primary">
                                        @if($index === 0)
                                            <i class="fas fa-home me-1"></i>
                                        @endif
                                        {{ $breadcrumb['title'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

@endif
