@extends('emails.layout')

@section('title', 'Reply mới trong thread bạn theo dõi')

@section('content')
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
    <h2 style="color: #2c3e50; margin: 0 0 10px 0; font-size: 24px;">
        <i class="fas fa-reply" style="color: #17a2b8; margin-right: 8px;"></i>
        Reply mới trong thread bạn theo dõi
    </h2>
    <p style="color: #6c757d; margin: 0; font-size: 14px;">
        Có bình luận mới trong thread mà bạn đang theo dõi
    </p>
</div>

<div style="background-color: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
    <h3 style="color: #495057; margin: 0 0 15px 0; font-size: 20px;">
        {{ $comment->thread->title }}
    </h3>
    
    <div style="margin-bottom: 15px;">
        <span style="background-color: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;">
            {{ $comment->thread->forum->name }}
        </span>
        @if($comment->thread->category)
        <span style="background-color: #f3e5f5; color: #7b1fa2; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; margin-left: 8px;">
            {{ $comment->thread->category->name }}
        </span>
        @endif
    </div>

    <div style="color: #6c757d; font-size: 14px; margin-bottom: 15px;">
        <strong>Người bình luận:</strong> {{ $comment->user->name }}<br>
        <strong>Thời gian:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}
    </div>

    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #17a2b8;">
        <p style="margin: 0; color: #495057; line-height: 1.6;">
            {{ Str::limit(strip_tags($comment->content), 300) }}
        </p>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}" 
       style="background-color: #17a2b8; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; display: inline-block;">
        <i class="fas fa-comment" style="margin-right: 8px;"></i>
        Xem Bình Luận
    </a>
</div>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <p style="margin: 0; color: #6c757d; font-size: 13px; text-align: center;">
        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
        Bạn nhận được email này vì bạn đang theo dõi thread <strong>{{ $comment->thread->title }}</strong>.
    </p>
    <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 13px; text-align: center;">
        <a href="{{ route('threads.unfollow', $comment->thread->slug) }}" style="color: #dc3545; text-decoration: none;">
            Bỏ theo dõi thread này
        </a> | 
        <a href="{{ route('profile.notifications') }}" style="color: #007bff; text-decoration: none;">
            Quản lý cài đặt thông báo
        </a>
    </p>
</div>
@endsection
