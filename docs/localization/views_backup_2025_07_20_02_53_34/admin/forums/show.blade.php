@extends('admin.layouts.dason')

@section('title', 'Chi tiết diễn đàn')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chi tiết diễn đàn</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chi tiết diễn đàn</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.forums.edit', $forum) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i> {{ __('Chỉnh sửa') }}
    </a>
@endsection

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Thông tin diễn đàn') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('ID') }}:</span>
                            <span class="text-muted">{{ $forum->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Tên') }}:</span>
                            <span class="text-muted">{{ $forum->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Slug') }}:</span>
                            <span class="text-muted">{{ $forum->slug }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Diễn đàn cha') }}:</span>
                            <span class="text-muted">
                                @if($forum->parent)
                                    <a href="{{ route('admin.forums.show', $forum->parent) }}">{{ $forum->parent->name }}</a>
                                @else
                                    {{ __('Không có') }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Thứ tự') }}:</span>
                            <span class="text-muted">{{ $forum->order }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Trạng thái') }}:</span>
                            <span>
                                @if($forum->is_private)
                                    <span class="badge bg-warning">{{ __('Riêng tư') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('Công khai') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Số bài đăng') }}:</span>
                            <span class="text-muted">{{ $threads->total() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Ngày tạo') }}:</span>
                            <span class="text-muted">{{ $forum->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Cập nhật lần cuối') }}:</span>
                            <span class="text-muted">{{ $forum->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if($forum->description)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Mô tả') }}</h5>
                    </div>
                    <div class="card-body">
                        {{ $forum->description }}
                    </div>
                </div>
            @endif
            
            @if($forum->subForums->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Diễn đàn con') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($forum->subForums as $subForum)
                                <a href="{{ route('admin.forums.show', $subForum) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ $subForum->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $subForum->threads->count() }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Bài đăng trong diễn đàn này') }}</h5>
                    <span class="badge bg-primary">{{ $threads->total() }} {{ __('bài đăng') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Tiêu đề') }}</th>
                                    <th>{{ __('Tác giả') }}</th>
                                    <th>{{ __('Chuyên mục') }}</th>
                                    <th>{{ __('Trạng thái') }}</th>
                                    <th>{{ __('Ngày tạo') }}</th>
                                    <th>{{ __('Thao tác') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($threads as $thread)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.threads.show', $thread) }}" class="text-decoration-none fw-bold">
                                                {{ $thread->title }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $thread->user) }}" class="text-decoration-none">
                                                {{ $thread->user->name }}
                                            </a>
                                        </td>
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
                                        <td>{{ $thread->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.threads.show', $thread) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">{{ __('Không có bài đăng nào trong diễn đàn này.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $threads->links() }}
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection