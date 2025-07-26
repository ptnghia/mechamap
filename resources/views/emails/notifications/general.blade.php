@extends('emails.layouts.base')

@section('title', $subject . ' - MechaMap')

@section('content')
<div class="greeting">
    Xin chào {{ $user->name }}! 👋
</div>

<div class="message">
    {{ $message }}
</div>

@if(isset($actionUrl) && isset($actionText))
<div class="btn-container">
    <a href="{{ $actionUrl }}" class="btn {{ $actionType ?? '' }}">
        {{ $actionText }}
    </a>
</div>
@endif

@if(isset($details) && is_array($details))
<div class="info-box">
    <h4>📋 Chi tiết</h4>
    @foreach($details as $key => $value)
    <p><strong>{{ $key }}:</strong> {{ $value }}</p>
    @endforeach
</div>
@endif

@if(isset($additionalInfo))
<div class="message">
    {{ $additionalInfo }}
</div>
@endif

@if(isset($relatedItems) && count($relatedItems) > 0)
<div class="message">
    <strong>Có thể bạn quan tâm:</strong>
    <ul style="margin: 15px 0; padding-left: 20px;">
        @foreach($relatedItems as $item)
        <li>
            <a href="{{ $item['url'] }}" style="color: #3498db;">{{ $item['title'] }}</a>
            @if(isset($item['description']))
            <br><small style="color: #666;">{{ $item['description'] }}</small>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif

@if(isset($unsubscribeUrl))
<div class="message" style="font-size: 14px; color: #666; margin-top: 30px;">
    Nếu bạn không muốn nhận loại thông báo này, 
    <a href="{{ $unsubscribeUrl }}" style="color: #3498db;">hủy đăng ký tại đây</a>.
</div>
@endif
@endsection
