@extends('admin.layouts.dason')

@section('title', 'Quản lý bài đăng')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Quản lý bài đăng</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Quản lý bài đăng</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.threads.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Tạo bài đăng mới') }}
    </a>
    <a href="{{ route('admin.threads.statistics') }}" class="btn btn-sm btn-info">
        <i class="fas fa-chart-bar me-1"></i> {{ __('Thống kê') }}
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
            <form action="{{ route('admin.threads.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">{{ __('Trạng thái') }}</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">{{ __('Tất cả') }}</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Bản nháp') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Chờ duyệt') }}</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Đã xuất bản') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Đã từ chối') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="forum_id" class="form-label">{{ __(__('forum.forums.title')) }}</label>
                    <select class="form-select" id="forum_id" name="forum_id">
                        <option value="">{{ __('Tất cả') }}</option>
                        @foreach($forums as $forum)
                            <option value="{{ $forum->id }}" {{ request('forum_id') == $forum->id ? 'selected' : '' }}>{{ $forum->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">{{ __('Chuyên mục') }}</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">{{ __('Tất cả') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">{{ __(__('ui.actions.search')) }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Tiêu đề, nội dung...') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> {{ __('Lọc') }}
                    </button>
                    <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times-circle me-1"></i> {{ __('Xóa bộ lọc') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Danh sách bài đăng') }}</h5>
            <span class="badge bg-primary">{{ $threads->total() }} {{ __('bài đăng') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col" width="60">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Tiêu đề') }}</th>
                            <th scope="col" width="150">{{ __('Tác giả') }}</th>
                            <th scope="col" width="150">{{ __(__('forum.forums.title')) }}</th>
                            <th scope="col" width="150">{{ __('Chuyên mục') }}</th>
                            <th scope="col" width="120">{{ __('Trạng thái') }}</th>
                            <th scope="col" width="150">{{ __('Ngày tạo') }}</th>
                            <th scope="col" width="120">{{ __('Thao tác') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($threads as $thread)
                            <tr>
                                <td>{{ $thread->id }}</td>
                                <td>
                                    <a href="{{ route('admin.threads.show', $thread) }}" class="text-decoration-none fw-bold">
                                        {{ $thread->title }}
                                    </a>
                                    <div class="small text-muted">
                                        @if($thread->is_sticky)
                                            <span class="badge bg-info me-1">{{ __(__('forum.threads.pinned')) }}</span>
                                        @endif
                                        @if($thread->is_featured)
                                            <span class="badge bg-warning me-1">{{ __('Nổi bật') }}</span>
                                        @endif
                                        {{ __(__('ui.common.views')) }}: {{ $thread->view_count }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $thread->user) }}" class="text-decoration-none">
                                        {{ $thread->user->name }}
                                    </a>
                                </td>
                                <td>{{ $thread->forum->name }}</td>
                                <td>{{ $thread->category->name }}</td>
                                <td>
                                    @if($thread->status == 'draft')
                                        <span class="badge bg-secondary">{{ __('Bản nháp') }}</span>
                                    @elseif($thread->status == 'pending')
                                        <span class="badge bg-warning">{{ __('Chờ duyệt') }}</span>
                                    @elseif($thread->status == 'published')
                                        <span class="badge bg-success">{{ __('Đã xuất bản') }}</span>
                                    @elseif($thread->status == 'rejected')
                                        <span class="badge bg-danger">{{ __('Đã từ chối') }}</span>
                                    @endif
                                </td>
                                <td>{{ $thread->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.threads.show', $thread) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Xem') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.threads.edit', $thread) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Sửa') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Moderation Actions -->
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('Moderation') }}">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <!-- Pin/Unpin -->
                                                <li>
                                                    <form action="{{ route('admin.threads.toggle-pin', $thread) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-thumbtack me-2"></i>
                                                            {{ $thread->is_sticky ? __('Bỏ ghim') : __('Ghim bài đăng') }}
                                                        </button>
                                                    </form>
                                                </li>

                                                <!-- Lock/Unlock -->
                                                <li>
                                                    <form action="{{ route('admin.threads.toggle-lock', $thread) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-{{ $thread->is_locked ? 'unlock' : 'lock' }} me-2"></i>
                                                            {{ $thread->is_locked ? __('Mở khóa') : __('Khóa bài đăng') }}
                                                        </button>
                                                    </form>
                                                </li>

                                                <!-- Feature/Unfeature -->
                                                <li>
                                                    <form action="{{ route('admin.threads.toggle-feature', $thread) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-star me-2"></i>
                                                            {{ $thread->is_featured ? __('Bỏ nổi bật') : __('Đánh dấu nổi bật') }}
                                                        </button>
                                                    </form>
                                                </li>

                                                <li><hr class="dropdown-divider"></li>

                                                <!-- Approve/Reject for pending threads -->
                                                @if($thread->status == 'pending')
                                                <li>
                                                    <form action="{{ route('admin.threads.approve', $thread) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check me-2"></i>
                                                            {{ __('Duyệt bài đăng') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $thread->id }}" title="{{ __('Xóa') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal xóa -->
                                    <div class="modal fade" id="deleteModal{{ $thread->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $thread->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $thread->id }}">{{ __('Xác nhận xóa') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Bạn có chắc chắn muốn xóa bài đăng này?') }}
                                                    <p class="text-danger mt-2">{{ __('Lưu ý: Hành động này không thể hoàn tác và sẽ xóa tất cả bình luận, phản hồi liên quan.') }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                                                    <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST">
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
                                <td colspan="8" class="text-center py-4">{{ __('Không có bài đăng nào.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $threads->withQueryString()->links() }}
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection
