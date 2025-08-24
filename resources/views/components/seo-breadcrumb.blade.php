@props(['breadcrumbs' => [], 'showStructuredData' => true])

@if(!empty($breadcrumbs))
<div class="breadcrumb-container bg-light border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="py-2">
                    <ol class="breadcrumb mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
                        @foreach($breadcrumbs as $index => $breadcrumb)
                            @if($breadcrumb['active'] ?? false)
                                <li class="breadcrumb-item active" aria-current="page" 
                                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                    <span class="text-muted" itemprop="name">{{ $breadcrumb['title'] }}</span>
                                    <meta itemprop="position" content="{{ $index + 1 }}">
                                </li>
                            @else
                                <li class="breadcrumb-item" 
                                    itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                                    <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none text-primary" 
                                       itemprop="item">
                                        @if($index === 0)
                                            <i class="fas fa-home me-1"></i>
                                        @endif
                                        <span itemprop="name">{{ $breadcrumb['title'] }}</span>
                                    </a>
                                    <meta itemprop="position" content="{{ $index + 1 }}">
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

@if($showStructuredData)
<!-- JSON-LD Structured Data for Breadcrumbs -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        @foreach($breadcrumbs as $index => $breadcrumb)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "name": "{{ addslashes($breadcrumb['title']) }}",
            @if(!($breadcrumb['active'] ?? false))
            "item": "{{ $breadcrumb['url'] }}"
            @else
            "item": "{{ url()->current() }}"
            @endif
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif

@endif
