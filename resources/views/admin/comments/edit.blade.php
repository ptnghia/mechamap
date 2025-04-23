@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa bình luận')

@section('header', 'Chỉnh sửa bình luận')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Chỉnh sửa bình luận') }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.comments.update', $comment) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="content" class="form-label">{{ __('Nội dung') }}</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content', $comment->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_flagged" name="is_flagged" {{ old('is_flagged', $comment->is_flagged) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_flagged">
                            {{ __('Đánh dấu bình luận') }}
                        </label>
                        <div class="form-text">{{ __('Bình luận bị đánh dấu sẽ được đánh dấu để xem xét thêm.') }}</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_hidden" name="is_hidden" {{ old('is_hidden', $comment->is_hidden) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_hidden">
                            {{ __('Ẩn bình luận') }}
                        </label>
                        <div class="form-text">{{ __('Bình luận bị ẩn sẽ không hiển thị cho người dùng.') }}</div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.comments.show', $comment) }}" class="btn btn-secondary">{{ __('Hủy') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Cập nhật') }}</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Thông tin bình luận') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('ID') }}:</span>
                            <span class="text-muted">{{ $comment->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Người dùng') }}:</span>
                            <span class="text-muted">
                                <a href="{{ route('admin.users.show', $comment->user) }}">{{ $comment->user->name }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Bài đăng') }}:</span>
                            <span class="text-muted">
                                <a href="{{ route('admin.threads.show', $comment->thread) }}">{{ Str::limit($comment->thread->title, 30) }}</a>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Ngày tạo') }}:</span>
                            <span class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Cập nhật lần cuối') }}:</span>
                            <span class="text-muted">{{ $comment->updated_at->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('Trạng thái') }}:</span>
                            <span>
                                @if($comment->is_flagged)
                                    <span class="badge bg-warning">{{ __('Đã đánh dấu') }}</span>
                                @endif
                                
                                @if($comment->is_hidden)
                                    <span class="badge bg-danger">{{ __('Đã ẩn') }}</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 300,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap print preview anchor',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
    });
</script>
@endpush
