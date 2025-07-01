@extends('layouts.unified')

@section('title', $seoData['title'] ?? $page->title)

@push('meta')
<meta name="description" content="{{ $seoData['description'] ?? $page->excerpt }}">
@if(!empty($seoData['keywords']))
<meta name="keywords" content="{{ $seoData['keywords'] }}">
@endif
<meta property="og:title" content="{{ $seoData['og_title'] ?? $page->title }}">
<meta property="og:description" content="{{ $seoData['og_description'] ?? $page->excerpt }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ $seoData['canonical'] ?? url()->current() }}">
@if($page->featured_image)
<meta property="og:image" content="{{ $page->featured_image }}">
@endif
<link rel="canonical" href="{{ $seoData['canonical'] ?? url()->current() }}">

<!-- Analytics Meta Tags -->
<meta name="page-id" content="{{ $page->slug }}">
<meta name="page-category" content="{{ $page->category->slug ?? 'general' }}">
@auth
<meta name="user-id" content="{{ auth()->id() }}">
@endauth

<!-- Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $page->title }}",
  "description": "{{ $page->excerpt }}",
  "author": {
    "@type": "Person",
    "name": "{{ $page->user->name ?? 'MechaMap' }}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "MechaMap",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('assets/images/logo.png') }}"
    }
  },
  "datePublished": "{{ $page->created_at->toISOString() }}",
  "dateModified": "{{ $page->updated_at->toISOString() }}",
  "url": "{{ url()->current() }}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  }
}
</script>
@endpush

@push('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    overflow: hidden;
    min-height: 300px;
    display: flex;
    align-items: center;
}
.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.1);
    z-index: 1;
}
.page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="white"></path></svg>') no-repeat center bottom;
    background-size: cover;
    z-index: 2;
}
.page-header .container {
    position: relative;
    z-index: 3;
}
.page-content {
    line-height: 1.8;
    font-size: 16px;
    color: #2c3e50;
}
.page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6 {
    color: #2c3e50;
    margin-top: 2.5rem;
    margin-bottom: 1.2rem;
    font-weight: 600;
}
.page-content h1 {
    font-size: 2.5rem;
    color: #1a202c;
    border-bottom: 3px solid #667eea;
    padding-bottom: 1rem;
}
.page-content h2 {
    font-size: 2rem;
    color: #2d3748;
    border-bottom: 2px solid #3498db;
    padding-bottom: 0.5rem;
    position: relative;
}
.page-content h2::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: -2px;
    width: 50px;
    height: 2px;
    background: #667eea;
}
.page-content h3 {
    font-size: 1.5rem;
    color: #4a5568;
    border-left: 4px solid #667eea;
    padding-left: 1rem;
}
.page-content ul, .page-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}
.page-content li {
    margin-bottom: 0.8rem;
    position: relative;
}
.page-content ul li::before {
    content: '▶';
    color: #667eea;
    font-size: 0.8rem;
    position: absolute;
    left: -1.5rem;
    top: 0.2rem;
}
.page-content blockquote {
    border-left: 4px solid #667eea;
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    padding: 1.5rem 2rem;
    margin: 2rem 0;
    border-radius: 0 12px 12px 0;
    position: relative;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}
.page-content blockquote::before {
    content: '"';
    font-size: 4rem;
    color: #667eea;
    position: absolute;
    top: -10px;
    left: 10px;
    opacity: 0.3;
    font-family: Georgia, serif;
}
.page-content p {
    margin-bottom: 1.2rem;
    text-align: justify;
}
.page-content strong {
    color: #2d3748;
    font-weight: 600;
}
.page-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}
.page-content table th,
.page-content table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}
.page-content table th {
    background: #f8f9fa;
    font-weight: 600;
}
.page-meta {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 2rem;
}
.page-navigation {
    background: #e3f2fd;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 2rem;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}
.page-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 20px;
    background: rgba(255,255,255,0.2);
    color: white;
}
.related-pages .card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.related-pages .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
</style>
@endpush

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Page Header -->
    <div class="page-header py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    @if($page->icon)
                    <div class="page-icon">
                        <i class="{{ $page->icon }}"></i>
                    </div>
                    @endif
                    <h1 class="display-5 fw-bold mb-3">{{ $page->title }}</h1>
                    @if($page->excerpt)
                    <p class="lead mb-0">{{ $page->excerpt }}</p>
                    @endif
                </div>
                <div class="col-lg-4 text-lg-end">
                    @if($page->category)
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        <i class="fas fa-folder me-1"></i>
                        {{ $page->category->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-white border-bottom">
        <div class="container py-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Trang chủ
                        </a>
                    </li>
                    @if($page->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('pages.category', $page->category->slug) }}" class="text-decoration-none">
                            {{ $page->category->name }}
                        </a>
                    </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <!-- Page Meta -->
                        <div class="page-meta">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Cập nhật: {{ $page->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>
                                        {{ number_format($page->view_count) }} lượt xem
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Page Content -->
                        <div class="page-content">
                            {!! $page->content !!}
                        </div>

                        <!-- Page Footer -->
                        @if($page->show_author && $page->user)
                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $page->user->avatar ?? '/assets/images/default-avatar.png' }}"
                                     alt="{{ $page->user->name }}"
                                     class="rounded-circle me-3"
                                     width="50" height="50">
                                <div>
                                    <h6 class="mb-1">{{ $page->user->name }}</h6>
                                    <small class="text-muted">Tác giả</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Share Buttons -->
                        <div class="border-top pt-4 mt-4">
                            <h6 class="mb-3">Chia sẻ trang này:</h6>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-facebook-f me-1"></i>Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($page->title) }}"
                                   target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fab fa-twitter me-1"></i>Twitter
                                </a>
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-linkedin-in me-1"></i>LinkedIn
                                </a>
                                <button class="btn btn-outline-secondary btn-sm" onclick="copyToClipboard('{{ url()->current() }}')">
                                    <i class="fas fa-link me-1"></i>Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Navigation -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-compass me-2"></i>
                            Điều hướng nhanh
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('pages.show', 'dieu-khoan-su-dung') }}"
                               class="list-group-item list-group-item-action {{ $page->slug === 'dieu-khoan-su-dung' ? 'active' : '' }}">
                                <i class="fas fa-file-contract me-2"></i>Điều khoản sử dụng
                            </a>
                            <a href="{{ route('pages.show', 'chinh-sach-bao-mat') }}"
                               class="list-group-item list-group-item-action {{ $page->slug === 'chinh-sach-bao-mat' ? 'active' : '' }}">
                                <i class="fas fa-shield-alt me-2"></i>Chính sách bảo mật
                            </a>
                            <a href="{{ route('pages.show', 've-chung-toi') }}"
                               class="list-group-item list-group-item-action {{ $page->slug === 've-chung-toi' ? 'active' : '' }}">
                                <i class="fas fa-building me-2"></i>Về chúng tôi
                            </a>
                            <a href="{{ route('pages.show', 'lien-he') }}"
                               class="list-group-item list-group-item-action {{ $page->slug === 'lien-he' ? 'active' : '' }}">
                                <i class="fas fa-envelope me-2"></i>Liên hệ
                            </a>
                            <a href="{{ route('pages.show', 'tro-giup') }}"
                               class="list-group-item list-group-item-action {{ $page->slug === 'tro-giup' ? 'active' : '' }}">
                                <i class="fas fa-question-circle me-2"></i>Trợ giúp
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-phone me-2"></i>
                            Thông tin liên hệ
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>{{ get_site_name() }}</strong>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            123 Đường Kỹ Thuật, Quận 1, TP.HCM
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            Hotline: 1900 1234
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            Email: support@mechamap.vn
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            8:00 - 17:00 (T2-T6)
                        </div>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-envelope me-1"></i>Gửi tin nhắn
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Pages -->
        @if($page->category && $page->category->pages->where('id', '!=', $page->id)->count() > 0)
        <div class="related-pages mt-5">
            <h4 class="mb-4">
                <i class="fas fa-bookmark me-2"></i>
                Trang liên quan
            </h4>
            <div class="row">
                @foreach($page->category->pages->where('id', '!=', $page->id)->where('status', 'published')->take(3) as $relatedPage)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('pages.show', $relatedPage->slug) }}" class="text-decoration-none">
                                    {{ $relatedPage->title }}
                                </a>
                            </h6>
                            @if($relatedPage->excerpt)
                            <p class="card-text text-muted small">{{ Str::limit($relatedPage->excerpt, 100) }}</p>
                            @endif
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i>{{ number_format($relatedPage->view_count) }} lượt xem
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Page Analytics -->
<script src="{{ asset('assets/js/page-analytics.js') }}"></script>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show toast notification if available
        if (window.cartUX && window.cartUX.showToast) {
            window.cartUX.showToast('success', 'Thành công', 'Đã copy link vào clipboard');
        } else {
            alert('Đã copy link vào clipboard');
        }

        // Track copy action
        if (window.pageAnalytics) {
            window.pageAnalytics.trackInteraction('copy_link');
        }
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Không thể copy link');
    });
}

// Enhanced page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // Track anchor click
                if (window.pageAnalytics) {
                    window.pageAnalytics.trackInteraction('anchor_click', this);
                }
            }
        });
    });

    // Reading progress indicator
    createReadingProgressIndicator();

    // Table of contents generator
    generateTableOfContents();

    // Image lazy loading and zoom
    setupImageEnhancements();

    // Print functionality
    setupPrintFunctionality();
});

/**
 * Create reading progress indicator
 */
function createReadingProgressIndicator() {
    const progressBar = document.createElement('div');
    progressBar.id = 'reading-progress';
    progressBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        z-index: 9999;
        transition: width 0.3s ease;
    `;
    document.body.appendChild(progressBar);

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        progressBar.style.width = scrollPercent + '%';
    });
}

/**
 * Generate table of contents
 */
function generateTableOfContents() {
    const headings = document.querySelectorAll('.page-content h2, .page-content h3');
    if (headings.length < 3) return; // Only show TOC if there are enough headings

    const tocContainer = document.createElement('div');
    tocContainer.className = 'table-of-contents card shadow-sm mb-4';
    tocContainer.innerHTML = `
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-list me-2"></i>
                Mục lục
            </h6>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0" id="toc-list"></ul>
        </div>
    `;

    const tocList = tocContainer.querySelector('#toc-list');
    headings.forEach((heading, index) => {
        const id = `heading-${index}`;
        heading.id = id;

        const li = document.createElement('li');
        li.className = heading.tagName === 'H2' ? 'mb-2' : 'mb-1 ms-3';
        li.innerHTML = `
            <a href="#${id}" class="text-decoration-none toc-link" data-heading="${id}">
                ${heading.textContent}
            </a>
        `;
        tocList.appendChild(li);
    });

    // Insert TOC after the first paragraph
    const firstParagraph = document.querySelector('.page-content p');
    if (firstParagraph) {
        firstParagraph.parentNode.insertBefore(tocContainer, firstParagraph.nextSibling);
    }
}

/**
 * Setup image enhancements
 */
function setupImageEnhancements() {
    const images = document.querySelectorAll('.page-content img');
    images.forEach(img => {
        // Lazy loading
        img.loading = 'lazy';

        // Add zoom functionality
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function() {
            showImageModal(this);
        });

        // Add caption if alt text exists
        if (img.alt) {
            const caption = document.createElement('figcaption');
            caption.className = 'text-muted small text-center mt-2';
            caption.textContent = img.alt;
            img.parentNode.insertBefore(caption, img.nextSibling);
        }
    });
}

/**
 * Show image in modal
 */
function showImageModal(img) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${img.alt || 'Image'}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="${img.src}" class="img-fluid" alt="${img.alt}">
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

/**
 * Setup print functionality
 */
function setupPrintFunctionality() {
    const printBtn = document.createElement('button');
    printBtn.className = 'btn btn-outline-secondary btn-sm';
    printBtn.innerHTML = '<i class="fas fa-print me-1"></i>In trang';
    printBtn.onclick = () => {
        window.print();
        if (window.pageAnalytics) {
            window.pageAnalytics.trackInteraction('print');
        }
    };

    const shareButtons = document.querySelector('.d-flex.gap-2');
    if (shareButtons) {
        shareButtons.appendChild(printBtn);
    }
}

// Enhanced social sharing with tracking
document.addEventListener('click', function(e) {
    const socialLink = e.target.closest('[href*="facebook.com"], [href*="twitter.com"], [href*="linkedin.com"]');
    if (socialLink && window.pageAnalytics) {
        const platform = socialLink.href.includes('facebook') ? 'facebook' :
                        socialLink.href.includes('twitter') ? 'twitter' : 'linkedin';
        window.pageAnalytics.trackSocialShare(platform);
    }
});
</script>

<!-- Enhanced CSS for new features -->
<style>
.table-of-contents {
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    border: none;
}

.toc-link {
    color: #495057;
    transition: all 0.2s ease;
}

.toc-link:hover {
    color: #667eea;
    transform: translateX(5px);
}

.page-content img {
    transition: transform 0.3s ease;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.page-content img:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.page-content figcaption {
    font-style: italic;
}

@media print {
    .page-header, .breadcrumb, .sidebar, .share-buttons, .related-pages {
        display: none !important;
    }

    .page-content {
        font-size: 12pt;
        line-height: 1.5;
    }

    .page-content h1, .page-content h2, .page-content h3 {
        page-break-after: avoid;
    }
}

/* Reading progress animation */
#reading-progress {
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
}
</style>
@endpush
