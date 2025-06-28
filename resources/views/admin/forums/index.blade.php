@extends('admin.layouts.dason')

@section('title', 'Quản lý diễn đàn')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý diễn đàn</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý diễn đàn</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.forums.create') }}" class="btn btn-sm btn-primary">
        <i class="class="fas fa-plus"-lg me-1"></i> {{ __('Tạo diễn đàn mới') }}
    </a>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách diễn đàn') }}</h5>
            <span class="badge bg-primary">{{ $forums->count() }} {{ __('diễn đàn') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tên diễn đàn') }}</th>
                            <th scope="col" width="150">{{ __('Diễn đàn cha') }}</th>
                            <th scope="col" width="100">{{ __('Thứ tự') }}</th>
                            <th scope="col" width="100">{{ __('Bài đăng') }}</th>
                            <th scope="col" width="100">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rootForums as $forum)
                            <tr>
                                <td>{{ $forum->id }}</td>
                                <td>
                                    <a href="{{ route('admin.forums.show', $forum) }}" class="text-decoration-none fw-bold">
                                        {{ $forum->name }}
                                    </a>
                                    @if($forum->description)
                                        <div class="small text-muted">{{ Str::limit($forum->description, 50) }}</div>
                                    @endif
                                </td>
                                <td>{{ $forum->parent ? $forum->parent->name : '-' }}</td>
                                <td>{{ $forum->order }}</td>
                                <td>{{ $forum->threads_count }}</td>
                                <td>
                                    @if($forum->is_private)
                                        <span class="badge bg-warning">{{ __('Riêng tư') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('Công khai') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.forums.show', $forum) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                            <i class="class="fas fa-eye""></i>
                                        </a>
                                        <a href="{{ route('admin.forums.edit', $forum) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="class="fas fa-edit""></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $forum->id }}" title="{{ __('Xóa') }}">
                                            <i class="class="fas fa-trash""></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $forum->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $forum->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $forum->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Bạn có chắc chắn muốn xóa diễn đàn này?') }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    <form action="{{ route('admin.forums.destroy', $forum) }}" method="POST">
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
                            
                            @foreach($forums->where('parent_id', $forum->id) as $subForum)
                                <tr>
                                    <td>{{ $subForum->id }}</td>
                                    <td>
                                        <div class="ms-3">
                                            <i class="fas fa-reply me-1 text-muted"></i>
                                            <a href="{{ route('admin.forums.show', $subForum) }}" class="text-decoration-none">
                                                {{ $subForum->name }}
                                            </a>
                                            @if($subForum->description)
                                                <div class="small text-muted ms-4">{{ Str::limit($subForum->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $subForum->parent->name }}</td>
                                    <td>{{ $subForum->order }}</td>
                                    <td>{{ $subForum->threads_count }}</td>
                                    <td>
                                        @if($subForum->is_private)
                                            <span class="badge bg-warning">{{ __('Riêng tư') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('Công khai') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.forums.show', $subForum) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                                <i class="class="fas fa-eye""></i>
                                            </a>
                                            <a href="{{ route('admin.forums.edit', $subForum) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                                <i class="class="fas fa-edit""></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $subForum->id }}" title="{{ __('Xóa') }}">
                                                <i class="class="fas fa-trash""></i>
                                            </button>
                                        </div>

                                        <!-- Modal xóa -->
                                        <div class="modal fade" id="deleteModal{{ $subForum->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $subForum->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $subForum->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ __('Bạn có chắc chắn muốn xóa diễn đàn này?') }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                        <form action="{{ route('admin.forums.destroy', $subForum) }}" method="POST">
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
                        
                        @if($forums->count() == 0)
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ __('Không có diễn đàn nào.') }}</td>
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