@extends('admin.layouts.dason')

@section('title', 'Quản lý bài viết')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý bài viết</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý bài viết</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.pages.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Tạo bài viết mới') }}
    </a>
    <a href="{{ route('admin.page-categories.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="fas fa-folder me-1"></i> {{ __('Quản lý danh mục') }}
    </a>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Bộ lọc') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pages.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('Tất cả') }}</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Bản nháp') }}</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Đã xuất bản') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="category_id" class="form-label">{{ __('Danh mục') }}</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">{{ __('Tất cả') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('Tìm kiếm') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Tiêu đề, nội dung...') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ __('Lọc') }}
                    </button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Xóa bộ lọc') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách bài viết') }}</h5>
            <span class="badge bg-primary">{{ $pages->total() }} {{ __('bài viết') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tiêu đề') }}</th>
                            <th scope="col" width="150">{{ __('Danh mục') }}</th>
                            <th scope="col" width="120">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="120">{{ __('Lượt xem') }}</th>
                            <th scope="col" width="150">{{ __('Ngày tạo') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td>{{ $page->id }}</td>
                                <td>
                                    <a href="{{ route('admin.pages.show', $page) }}" class="text-decoration-none fw-bold">
                                        {{ $page->title }}
                                    </a>
                                    @if($page->is_featured)
                                        <span class="badge bg-warning ms-1">{{ __('Nổi bật') }}</span>
                                    @endif
                                </td>
                                <td>{{ $page->category->name }}</td>
                                <td>
                                    @if($page->status == 'draft')
                                        <span class="badge bg-secondary">{{ __('Bản nháp') }}</span>
                                    @elseif($page->status == 'published')
                                        <span class="badge bg-success">{{ __('Đã xuất bản') }}</span>
                                    @endif
                                </td>
                                <td>{{ $page->view_count }}</td>
                                <td>{{ $page->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $page->id }}" title="{{ __('Xóa') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $page->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $page->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $page->id }}">{{ __('Xác nhận xóa') }}</h5>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ __('Không có bài viết nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $pages->withQueryString()->links() }}
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
