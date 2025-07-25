@extends('admin.layouts.dason')

@section('title', 'Quản lý chuyên mục')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý chuyên mục</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý chuyên mục</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> {{ 'Tạo chuyên mục mới' }}
    </a>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ 'Danh sách chuyên mục' }}</h5>
            <span class="badge bg-primary">{{ $categories->count() }} {{ __('chuyên mục') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ 'ID' }}</th>
                            <th scope="col">{{ 'Tên chuyên mục' }}</th>
                            <th scope="col" width="150">{{ 'Chuyên mục cha' }}</th>
                            <th scope="col" width="100">{{ 'Thứ tự' }}</th>
                            <th scope="col" width="100">{{ 'Bài đăng' }}</th>
                            <th scope="col" width="150">{{ 'Ngày tạo' }}</th>
                            <th scope="col" width="120">{{ 'Thao tác' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rootCategories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', $category) }}" class="text-decoration-none fw-bold">
                                        {{ $category->name }}
                                    </a>
                                    @if($category->description)
                                        <div class="small text-muted">{{ Str::limit($category->description, 50) }}</div>
                                    @endif
                                </td>
                                <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                                <td>{{ $category->order }}</td>
                                <td>{{ $category->threads_count }}</td>
                                <td>{{ $category->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Xem' }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Sửa' }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}" title="{{ 'Xóa' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">{{ 'Xác nhận xóa' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ 'Bạn có chắc chắn muốn xóa chuyên mục này?' }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">{{ 'Xóa' }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            @foreach($categories->where('parent_id', $category->id) as $childCategory)
                                <tr>
                                    <td>{{ $childCategory->id }}</td>
                                    <td>
                                        <div class="ms-3">
                                            <i data-feather="corner-down-right" class="me-1 text-muted" style="width: 16px; height: 16px;"></i>
                                            <a href="{{ route('admin.categories.show', $childCategory) }}" class="text-decoration-none">
                                                {{ $childCategory->name }}
                                            </a>
                                            @if($childCategory->description)
                                                <div class="small text-muted ms-4">{{ Str::limit($childCategory->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $childCategory->parent->name }}</td>
                                    <td>{{ $childCategory->order }}</td>
                                    <td>{{ $childCategory->threads_count }}</td>
                                    <td>{{ $childCategory->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.categories.show', $childCategory) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Xem' }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $childCategory) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Sửa' }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $childCategory->id }}" title="{{ 'Xóa' }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal xóa -->
                                        <div class="modal fade" id="deleteModal{{ $childCategory->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $childCategory->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $childCategory->id }}">{{ 'Xác nhận xóa' }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ 'Bạn có chắc chắn muốn xóa chuyên mục này?' }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                                                        <form action="{{ route('admin.categories.destroy', $childCategory) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">{{ 'Xóa' }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if($categories->count() == 0)
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ __('Không có chuyên mục nào.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
