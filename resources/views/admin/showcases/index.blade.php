@extends('admin.layouts.dason')

@section('title', 'Quản lý Showcase')

@push('styles')
<!-- Page specific CSS -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Showcase</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.showcases.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm vào Showcase
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>Người tạo</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($showcases as $showcase)
                                <tr>
                                    <td>{{ $showcase->id }}</td>
                                    <td>
                                        @if($showcase->showcaseable)
                                            @if($showcase->showcaseable_type === 'App\\Models\\Thread')
                                                <a href="{{ route('threads.show', $showcase->showcaseable->slug) }}" target="_blank">
                                                    {{ $showcase->showcaseable->title }}
                                                </a>
                                            @elseif($showcase->showcaseable_type === 'App\\Models\\Post')
                                                <a href="{{ route('threads.show', $showcase->showcaseable->thread->slug) }}" target="_blank">
                                                    {{ $showcase->showcaseable->thread->title }}
                                                </a>
                                            @else
                                                <span class="text-muted">Không xác định</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Nội dung đã bị xóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($showcase->showcaseable_type === 'App\\Models\\Thread')
                                            <span class="badge badge-primary">Chủ đề</span>
                                        @elseif($showcase->showcaseable_type === 'App\\Models\\Post')
                                            <span class="badge badge-info">Bài viết</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $showcase->showcaseable_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="user-block">
                                            <img class="img-circle img-bordered-sm" src="{{ $showcase->user->getAvatarUrl() }}" alt="User Image">
                                            <span class="username">
                                                <a href="{{ route('users.show', $showcase->user->username) }}">{{ $showcase->user->name }}</a>
                                            </span>
                                            <span class="description">{{ $showcase->user->username }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $showcase->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <form action="{{ route('admin.showcases.destroy', $showcase->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khỏi showcase?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có dữ liệu showcase nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    {{ $showcases->links() }}
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

@push('scripts')
<!-- Page specific JS -->
@endpush
@endsection