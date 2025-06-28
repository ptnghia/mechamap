@extends('admin.layouts.dason')

@section('title', 'Quản lý hỏi đáp')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý hỏi đáp</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý hỏi đáp</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Tạo câu hỏi mới') }}
    </a>
    <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-sm btn-outline-primary">
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
            <form action="{{ route('admin.faqs.index') }}" method="GET" class="row g-3">
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
                    <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('Tất cả') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Kích hoạt') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Vô hiệu hóa') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">{{ __('Tìm kiếm') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Câu hỏi, câu trả lời...') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> {{ __('Lọc') }}
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Xóa bộ lọc') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách câu hỏi') }}</h5>
            <span class="badge bg-primary">{{ $faqs->total() }} {{ __('câu hỏi') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Câu hỏi') }}</th>
                            <th scope="col" width="150">{{ __('Danh mục') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td>{{ $faq->id }}</td>
                                <td>
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-decoration-none fw-bold">
                                        {{ $faq->question }}
                                    </a>
                                </td>
                                <td>{{ $faq->category->name }}</td>
                                <td>{{ $faq->order }}</td>
                                <td>
                                    @if($faq->is_active)
                                        <span class="badge bg-success">{{ __('Kích hoạt') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Vô hiệu hóa') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.faqs.toggle-status', $faq) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $faq->is_active ? 'warning' : 'success' }}" title="{{ $faq->is_active ? __('Vô hiệu hóa') : __('Kích hoạt') }}">
                                                <i class="fas fa-{{ $faq->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $faq->id }}" title="{{ __('Xóa') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $faq->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $faq->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $faq->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Bạn có chắc chắn muốn xóa câu hỏi này?') }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST">
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
                                <td colspan="6" class="text-center py-4">{{ __('Không có câu hỏi nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $faqs->withQueryString()->links() }}
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
