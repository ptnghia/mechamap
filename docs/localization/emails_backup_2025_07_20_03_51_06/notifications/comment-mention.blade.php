@extends('emails.layout')

@section('title', 'Bạn được nhắc đến trong bình luận')

@section('content')
<div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffeaa7;">
    <h2 style="color: #856404; margin: 0 0 10px 0; font-size: 24px;">
        <i class="fas fa-at" style="color: #ffc107; margin-right: 8px;"></i>
        Bạn được nhắc đến trong bình luận
    </h2>
    <p style="color: #856404; margin: 0; font-size: 14px;">
        <strong>{{ $comment->user->name }}</strong> đã nhắc đến bạn trong một bình luận
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
        <strong>Người nhắc đến:</strong> {{ $comment->user->name }}<br>
        <strong>Thời gian:</strong> {{ $comment->created_at->format('d/m/Y H:i') }}
    </div>

    <div style="background-color: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
        <p style="margin: 0; color: #856404; line-height: 1.6; font-weight: 500;">
            {{ strip_tags($comment->content) }}
        </p>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('threads.show', $comment->thread->slug) }}#comment-{{ $comment->id }}" 
       style="background-color: #ffc107; color: #212529; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; display: inline-block;">
        <i class="fas fa-reply" style="margin-right: 8px;"></i>
        Trả Lời Ngay
    </a>
</div>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
    <p style="margin: 0; color: #6c757d; font-size: 13px; text-align: center;">
        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
        Bạn nhận được email này vì <strong>{{ $comment->user->name }}</strong> đã nhắc đến bạn (@{{ auth()->user()->username ?? 'username' }}) trong bình luận.
    </p>
    <p style="margin: 10px 0 0 0; color: #6c757d; font-size: 13px; text-align: center;">
        <a href="{{ route('profile.notifications') }}" style="color: #007bff; text-decoration: none;">
            Quản lý cài đặt thông báo
        </a>
    </p>
</div>

<div style="background-color: #e7f3ff; padding: 15px; border-radius: 6px; margin-top: 15px; border-left: 4px solid #007bff;">
    <h4 style="color: #0056b3; margin: 0 0 10px 0; font-size: 16px;">
        <i class="fas fa-lightbulb" style="margin-right: 8px;"></i>
        Mẹo sử dụng @mention
    </h4>
    <p style="margin: 0; color: #495057; font-size: 13px; line-height: 1.5;">
        Để nhắc đến ai đó trong bình luận, hãy gõ <strong>@username</strong> trong nội dung bình luận. 
        Họ sẽ nhận được thông báo email như thế này!
    </p>
</div>
@endsection
