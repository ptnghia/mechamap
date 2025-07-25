@extends('admin.layouts.dason')

@section('title', 'Quản lý Sitemap')
@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý Sitemap</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý Sitemap</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.seo.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ 'Quay lại' }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Điều hướng' }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.seo.index') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog me-2"></i> {{ 'Cấu hình chung' }}
                        </a>
                        <a href="{{ route('admin.seo.robots') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-robot me-2"></i> {{ 'Robots.txt' }}
                        </a>
                        <a href="{{ route('admin.seo.sitemap') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-sitemap me-2"></i> {{ 'Sitemap' }}
                        </a>
                        <a href="{{ route('admin.seo.social') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-share me-2"></i> {{ 'Social Media' }}
                        </a>
                        <a href="{{ route('admin.seo.advanced') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog-wide-connected me-2"></i> {{ 'Cấu hình nâng cao' }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ 'Hướng dẫn' }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        {{ __('Sitemap là file XML chứa danh sách các URL trên trang web của bạn. Nó giúp các công cụ tìm kiếm tìm thấy và lập chỉ mục trang web của bạn hiệu quả hơn.') }}
                    </p>
                    <p class="mb-0">
                        {{ __('Sau khi tạo sitemap, bạn nên:') }}
                    </p>
                    <ul class="mb-0">
                        <li>{{ __('Thêm URL sitemap vào file robots.txt') }}</li>
                        <li>{{ __('Gửi sitemap đến Google Search Console') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Quản lý Sitemap') }}</h5>
                    <form action="{{ route('admin.seo.generate-sitemap') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> {{ __('Tạo Sitemap mới') }}
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle-fill me-2"></i>
                        {{ __('Sitemap sẽ bao gồm: trang chủ, các trang chính, diễn đàn, chủ đề, và hồ sơ người dùng.') }}
                    </div>
                    
                    @if(count($sitemapFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ 'Tên file' }}</th>
                                        <th>{{ 'Kích thước' }}</th>
                                        <th>{{ 'Cập nhật lần cuối' }}</th>
                                        <th>{{ 'Thao tác' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sitemapFiles as $file)
                                        <tr>
                                            <td>
                                                <a href="{{ $file['url'] }}" target="_blank" class="text-decoration-none">
                                                    <i class="fas fa-file-code me-2"></i>{{ $file['name'] }}
                                                </a>
                                            </td>
                                            <td>{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                            <td>{{ date('d/m/Y H:i:s', $file['modified']) }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ $file['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="{{ 'Xem' }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-filename="{{ $file['name'] }}" title="{{ 'Xóa' }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-circle text-muted fs-1 d-block mb-3"></i>
                            <p class="text-muted mb-0">{{ __('Chưa có file sitemap nào. Hãy tạo sitemap mới.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Gửi Sitemap đến công cụ tìm kiếm') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fab fa-google me-2"></i>{{ __('Google Search Console') }}
                                    </h5>
                                    <p class="card-text">
                                        {{ __('Gửi sitemap của bạn đến Google Search Console để giúp Google lập chỉ mục trang web của bạn hiệu quả hơn.') }}
                                    </p>
                                    <a href="https://search.google.com/search-console" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ __('Mở Google Search Console') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-search me-2"></i>{{ __('Bing Webmaster Tools') }}
                                    </h5>
                                    <p class="card-text">
                                        {{ __('Gửi sitemap của bạn đến Bing Webmaster Tools để giúp Bing lập chỉ mục trang web của bạn hiệu quả hơn.') }}
                                    </p>
                                    <a href="https://www.bing.com/webmasters/about" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> {{ __('Mở Bing Webmaster Tools') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Xóa Sitemap') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.seo.delete-sitemap') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="filename" id="delete-filename">
                    <div class="modal-body">
                        <p>{{ __('Bạn có chắc chắn muốn xóa file sitemap này?') }}</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle-fill me-2"></i>
                            {{ __('Lưu ý: Việc xóa sitemap có thể ảnh hưởng đến khả năng lập chỉ mục của các công cụ tìm kiếm.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                        <button type="submit" class="btn btn-danger">{{ 'Xóa' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Set filename to delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const filename = button.getAttribute('data-filename');
                document.getElementById('delete-filename').value = filename;
            });
        }
        
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
