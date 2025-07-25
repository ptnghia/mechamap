@extends('admin.layouts.dason')

@section('title', 'Quản lý bình luận')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý bình luận</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý bình luận</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.comments.statistics') }}" class="btn btn-sm btn-info">
        <i class="fas fa-chart-bar me-1"></i> {{ 'Thống kê' }}
    </a>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ 'Bộ lọc' }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">{{ 'Trạng thái' }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ 'Tất cả' }}</option>
                        <option value="flagged" {{ request('status') == 'flagged' ? 'selected' : '' }}>{{ 'Đã đánh dấu' }}</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>{{ 'Đã ẩn' }}</option>
                        <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>{{ __('Bị báo cáo') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="thread_id" class="form-label">{{ __('ID bài đăng') }}</label>
                    <input type="number" class="form-control" id="thread_id" name="thread_id" value="{{ request('thread_id') }}" placeholder="{{ __('Nhập ID bài đăng') }}">
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">{{ __('ID người dùng') }}</label>
                    <input type="number" class="form-control" id="user_id" name="user_id" value="{{ request('user_id') }}" placeholder="{{ __('Nhập ID người dùng') }}">
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ 'Tìm kiếm' }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Nội dung bình luận...') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ 'Lọc' }}
                    </button>
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-1"></i> {{ 'Xóa bộ lọc' }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ 'Danh sách bình luận' }}</h5>
            <span class="badge bg-primary">{{ $comments->total() }} {{ __('bình luận') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ 'ID' }}</th>
                            <th scope="col">{{ 'Nội dung' }}</th>
                            <th scope="col" width="150">{{ 'Người dùng' }}</th>
                            <th scope="col" width="150">{{ 'Bài đăng' }}</th>
                            <th scope="col" width="120">{{ 'Trạng thái' }}</th>
                            <th scope="col" width="150">{{ 'Ngày tạo' }}</th>
                            <th scope="col" width="120">{{ 'Thao tác' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                            <tr>
                                <td>{{ $comment->id }}</td>
                                <td>
                                    <div class="{{ $comment->is_hidden ? 'text-muted fst-italic' : '' }}">
                                        {{ Str::limit(strip_tags($comment->content), 100) }}
                                    </div>
                                    @if($comment->parent_id)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-reply me-1"></i> {{ __('Trả lời cho bình luận #') . $comment->parent_id }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $comment->user) }}" class="text-decoration-none">
                                        {{ $comment->user->name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.threads.show', $comment->thread) }}" class="text-decoration-none">
                                        {{ Str::limit($comment->thread->title, 30) }}
                                    </a>
                                </td>
                                <td>
                                    @if($comment->is_flagged)
                                        <span class="badge bg-warning">{{ 'Đã đánh dấu' }}</span>
                                    @endif
                                    
                                    @if($comment->is_hidden)
                                        <span class="badge bg-danger">{{ 'Đã ẩn' }}</span>
                                    @endif
                                </td>
                                <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.comments.show', $comment) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Xem' }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.comments.edit', $comment) }}" class="btn btn-sm btn-outline-primary" title="{{ 'Sửa' }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $comment->id }}" title="{{ 'Xóa' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $comment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $comment->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $comment->id }}">{{ 'Xác nhận xóa' }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ 'Bạn có chắc chắn muốn xóa bình luận này?' }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'Hủy' }}</button>
                                                    <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST">
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ __('Không có bình luận nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $comments->withQueryString()->links() }}
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection