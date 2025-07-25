@extends('admin.layouts.dason')

@section('title', 'Chi tiết chuyên mục')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Chi tiết chuyên mục</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">MechaMap</a></li>
                    <li class="breadcrumb-item active">Chi tiết chuyên mục</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i> {{ 'Chỉnh sửa' }}
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
                    <h5 class="card-title mb-0">{{ __('Thông tin chuyên mục') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'ID' }}:</span>
                            <span class="text-muted">{{ $category->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Tên' }}:</span>
                            <span class="text-muted">{{ $category->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Slug' }}:</span>
                            <span class="text-muted">{{ $category->slug }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Chuyên mục cha' }}:</span>
                            <span class="text-muted">
                                @if($category->parent)
                                    <a href="{{ route('admin.categories.show', $category->parent) }}">{{ $category->parent->name }}</a>
                                @else
                                    {{ 'Không có' }}
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Thứ tự' }}:</span>
                            <span class="text-muted">{{ $category->order }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Số bài đăng' }}:</span>
                            <span class="text-muted">{{ $threads->total() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Ngày tạo' }}:</span>
                            <span class="text-muted">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ 'Cập nhật lần cuối' }}:</span>
                            <span class="text-muted">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            @if($category->description)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ 'Mô tả' }}</h5>
                    </div>
                    <div class="card-body">
                        {{ $category->description }}
                    </div>
                </div>
            @endif
            
            @if($category->children->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Chuyên mục con') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($category->children as $child)
                                <a href="{{ route('admin.categories.show', $child) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ $child->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $child->threads->count() }}</span>
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
                    <h5 class="card-title mb-0">{{ __('Bài đăng trong chuyên mục này') }}</h5>
                    <span class="badge bg-primary">{{ $threads->total() }} {{ 'bài đăng' }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ 'Tiêu đề' }}</th>
                                    <th>{{ 'Tác giả' }}</th>
                                    <th>{{ __('Diễn đàn') }}</th>
                                    <th>{{ 'Trạng thái' }}</th>
                                    <th>{{ 'Ngày tạo' }}</th>
                                    <th>{{ 'Thao tác' }}</th>
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
                                        <td>{{ $thread->forum->name }}</td>
                                        <td>
                                            @if($thread->status == 'draft')
                                                <span class="badge bg-secondary">{{ 'Bản nháp' }}</span>
                                            @elseif($thread->status == 'pending')
                                                <span class="badge bg-warning">{{ 'Chờ duyệt' }}</span>
                                            @elseif($thread->status == 'published')
                                                <span class="badge bg-success">{{ 'Đã xuất bản' }}</span>
                                            @elseif($thread->status == 'rejected')
                                                <span class="badge bg-danger">{{ 'Đã từ chối' }}</span>
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
                                        <td colspan="6" class="text-center py-4">{{ __('Không có bài đăng nào trong chuyên mục này.') }}</td>
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