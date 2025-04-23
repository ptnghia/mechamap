@extends('admin.layouts.app')

@section('title', 'Quản lý chuyên mục')

@section('header', 'Quản lý chuyên mục')

@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i> {{ __('Tạo chuyên mục mới') }}
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách chuyên mục') }}</h5>
            <span class="badge bg-primary">{{ $categories->count() }} {{ __('chuyên mục') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tên chuyên mục') }}</th>
                            <th scope="col" width="150">{{ __('Chuyên mục cha') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Bài đăng') }}</th>
                            <th scope="col" width="150">{{ __('Ngày tạo') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
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
                                        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}" title="{{ __('Xóa') }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Bạn có chắc chắn muốn xóa chuyên mục này?') }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
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
                            
                            @foreach($categories->where('parent_id', $category->id) as $childCategory)
                                <tr>
                                    <td>{{ $childCategory->id }}</td>
                                    <td>
                                        <div class="ms-3">
                                            <i class="bi bi-arrow-return-right me-1 text-muted"></i>
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
                                            <a href="{{ route('admin.categories.show', $childCategory) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $childCategory) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $childCategory->id }}" title="{{ __('Xóa') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal xóa -->
                                        <div class="modal fade" id="deleteModal{{ $childCategory->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $childCategory->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $childCategory->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ __('Bạn có chắc chắn muốn xóa chuyên mục này?') }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                        <form action="{{ route('admin.categories.destroy', $childCategory) }}" method="POST">
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
@endsection
