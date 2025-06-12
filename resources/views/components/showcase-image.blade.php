@props([
'showcase',
'size' => 'medium', // small|medium|large|card
'class' => '',
'showLink' => true,
'linkUrl' => null
])

@php
use App\Services\ShowcaseImageService;

// Định nghĩa kích thước hình ảnh
$sizes = [
'small' => 'width: 60px; height: 60px;',
'medium' => 'width: 80px; height: 80px;',
'large' => 'height: 200px;',
'card' => 'height: 180px;'
];

$imageStyle = $sizes[$size] ?? $sizes['medium'];
$imageClasses = in_array($size, ['large', 'card']) ? 'w-100' : 'rounded';

// Lấy featured image metadata
$imageMeta = ShowcaseImageService::getFeaturedImageMeta($showcase);

// Xác định link URL
$finalLinkUrl = $linkUrl ?? ($showcase->showcase_url ?? route('showcase.show', $showcase));
@endphp

@if($imageMeta['hasImage'] && $imageMeta['type'] !== 'placeholder')
<!-- Hiển thị hình ảnh thực -->
@if($showLink)
<a href="{{ $finalLinkUrl }}" class="showcase-image-link">
    @endif
    <img src="{{ $imageMeta['url'] }}" alt="{{ $imageMeta['alt'] }}"
        class="showcase-image {{ $imageClasses }} {{ $class }}" style="{{ $imageStyle }} object-fit: cover;"
        onerror="this.src='{{ asset('images/placeholder.svg') }}'" loading="lazy" @if($imageMeta['type']==='legacy' )
        data-image-type="legacy" @elseif($imageMeta['type']==='extracted' ) data-image-type="extracted" @else
        data-image-type="media" @endif>
    @if($showLink)
</a>
@endif
@else
<!-- Hiển thị placeholder khi không có hình ảnh -->
@if($showLink)
<a href="{{ $finalLinkUrl }}" class="showcase-placeholder-link">
    @endif
    <div class="showcase-placeholder bg-light d-flex align-items-center justify-content-center {{ $imageClasses }} {{ $class }}"
        style="{{ $imageStyle }}">
        @if($size === 'small')
        <i class="bi bi-image text-muted fs-6"></i>
        @elseif($size === 'medium')
        <i class="bi bi-image text-muted fs-5"></i>
        @else
        <i class="bi bi-image text-muted fs-1"></i>
        @endif
    </div>
    @if($showLink)
</a>
@endif
@endif

@push('styles')
<style>
    .showcase-image {
        transition: transform 0.2s ease-in-out;
    }

    .showcase-image-link:hover .showcase-image,
    .showcase-placeholder-link:hover .showcase-placeholder {
        transform: scale(1.02);
    }

    .showcase-placeholder {
        border: 2px dashed #dee2e6;
        transition: all 0.2s ease-in-out;
    }

    .showcase-placeholder-link:hover .showcase-placeholder {
        border-color: #6c757d;
        background-color: #f8f9fa !important;
    }

    /* Special styling cho different image types */
    .showcase-image[data-image-type="legacy"] {
        border: 2px solid #0d6efd;
    }

    .showcase-image[data-image-type="extracted"] {
        border: 2px solid #198754;
    }

    .showcase-image[data-image-type="media"] {
        border: 2px solid #6f42c1;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .showcase-image.w-100 {
            height: 150px !important;
        }
    }
</style>
@endpush