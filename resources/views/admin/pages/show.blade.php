@extends('admin.layouts.app')

@section('title', 'Chi tiết bài viết')

@section('header', 'Chi tiết bài viết')

@section('actions')
    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-pencil me-1"></i> {{ __('Chỉnh sửa') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Nội dung bài viết') }}</h5>
                </div>
                <div class="card-body">
                    @if($page->attachments->count() > 0)
                        <div class="mb-4 text-center">
                            <img src="{{ Storage::url($page->attachments->first()->file_path) }}" alt="{{ $page->title }}" class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    @endif
                    
                    <h1 class="mb-3">{{ $page->title }}</h1>
                    
                    @if($page->excerpt)
                        <div class="lead mb-4">
                            {{ $page->excerpt }}
                        </div>
                    @endif
                    
                    <div class="page-content">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('SEO') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Meta Title') }}</label>
                                <p>{{ $page->meta_title ?? $page->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Meta Description') }}</label>
                                <p>{{ $page->meta_description ?? Str::limit(strip_tags($page->content), 160) }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Meta Keywords') }}</label>
                                <p>{{ $page->meta_keywords }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin bài viết') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('ID') }}:</span>
                            <span class="text-muted">{{ $page->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Slug') }}:</span>
                            <span class="text-muted">{{ $page->slug }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Danh mục') }}:</span>
                            <span class="text-muted">{{ $page->category->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Tác giả') }}:</span>
                            <span class="text-muted">{{ $page->user->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Trạng thái') }}:</span>
                            <span>
                                @if($page->status == 'draft')
                                    <span class="badge bg-secondary">{{ __('Bản nháp') }}</span>
                                @elseif($page->status == 'published')
                                    <span class="badge bg-success">{{ __('Đã xuất bản') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Nổi bật') }}:</span>
                            <span>
                                @if($page->is_featured)
                                    <span class="badge bg-warning">{{ __('Có') }}</span>
                                @else
                                    <span class="text-muted">{{ __('Không') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Thứ tự') }}:</span>
                            <span class="text-muted">{{ $page->order }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Lượt xem') }}:</span>
                            <span class="text-muted">{{ $page->view_count }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Ngày tạo') }}:</span>
                            <span class="text-muted">{{ $page->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Cập nhật lần cuối') }}:</span>
                            <span class="text-muted">{{ $page->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thao tác') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> {{ __('Chỉnh sửa') }}
                        </a>
                        
                        @if($page->status == 'draft')
                            <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $page->title }}">
                                <input type="hidden" name="content" value="{{ $page->content }}">
                                <input type="hidden" name="excerpt" value="{{ $page->excerpt }}">
                                <input type="hidden" name="category_id" value="{{ $page->category_id }}">
                                <input type="hidden" name="status" value="published">
                                <input type="hidden" name="order" value="{{ $page->order }}">
                                <input type="hidden" name="is_featured" value="{{ $page->is_featured ? 1 : 0 }}">
                                <input type="hidden" name="meta_title" value="{{ $page->meta_title }}">
                                <input type="hidden" name="meta_description" value="{{ $page->meta_description }}">
                                <input type="hidden" name="meta_keywords" value="{{ $page->meta_keywords }}">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i> {{ __('Xuất bản') }}
                                </button>
                            </form>
                        @endif
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash me-1"></i> {{ __('Xóa bài viết') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal xóa bài viết -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Xác nhận xóa') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Bạn có chắc chắn muốn xóa bài viết này?') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Xóa') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
