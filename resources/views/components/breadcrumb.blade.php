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

<style>
/* Breadcrumb Styling */
.breadcrumb-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
    font-size: 0.875rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
    font-size: 1rem;
    margin: 0 0.5rem;
}

.breadcrumb-item a {
    color: #0d6efd;
    text-decoration: none;
    transition: all 0.2s ease;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.breadcrumb-item a:hover {
    color: #0a58ca;
    background-color: rgba(13, 110, 253, 0.1);
    text-decoration: none;
}

.breadcrumb-item.active span {
    color: #6c757d;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .breadcrumb-container {
        padding: 0.5rem 0;
    }

    .breadcrumb {
        font-size: 0.8rem;
        flex-wrap: wrap;
    }

    .breadcrumb-item {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        margin: 0 0.25rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .breadcrumb-container {
        background: linear-gradient(135deg, #343a40 0%, #495057 100%);
        border-bottom-color: #495057;
    }

    .breadcrumb-item a {
        color: #66b3ff;
    }

    .breadcrumb-item a:hover {
        color: #99ccff;
        background-color: rgba(102, 179, 255, 0.1);
    }

    .breadcrumb-item.active span {
        color: #adb5bd;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #adb5bd;
    }
}

/* Animation */
.breadcrumb-container {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Accessibility */
.breadcrumb-item a:focus {
    outline: 2px solid #0d6efd;
    outline-offset: 2px;
    border-radius: 0.25rem;
}

/* Print Styles */
@media print {
    .breadcrumb-container {
        background: white !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }

    .breadcrumb-item a {
        color: #000 !important;
        text-decoration: underline !important;
    }
}
</style>
@endif
