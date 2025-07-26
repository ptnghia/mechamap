@extends('emails.layouts.base')

@section('title', 'Newsletter - MechaMap')

@section('content')
<!-- Header -->
<tr>
    <td style="padding: 40px 30px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h1 style="color: #ffffff; font-size: 28px; margin: 0; font-weight: 600;">
            📰 MechaMap Newsletter
        </h1>
        <p style="color: #ffffff; font-size: 16px; margin: 10px 0 0 0; opacity: 0.9;">
            Tin tức và cập nhật mới nhất từ cộng đồng
        </p>
    </td>
</tr>

<!-- Main Content -->
<tr>
    <td style="padding: 40px 30px;">
        <div style="font-size: 16px; line-height: 1.6; color: #333333;">
            {!! $emailContent !!}
        </div>
    </td>
</tr>

@if(!empty($articles))
<!-- Featured Articles -->
<tr>
    <td style="padding: 0 30px 40px 30px;">
        <h2 style="color: #333333; font-size: 22px; margin: 0 0 20px 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            📚 Bài viết nổi bật
        </h2>
        
        @foreach($articles as $article)
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px; overflow: hidden;">
            @if(isset($article['image']))
            <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" style="width: 100%; height: 200px; object-fit: cover;">
            @endif
            
            <div style="padding: 20px;">
                <h3 style="color: #333333; font-size: 18px; margin: 0 0 10px 0;">
                    <a href="{{ $article['url'] }}" style="color: #667eea; text-decoration: none;">
                        {{ $article['title'] }}
                    </a>
                </h3>
                
                @if(isset($article['excerpt']))
                <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0 0 15px 0;">
                    {{ $article['excerpt'] }}
                </p>
                @endif
                
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #999999;">
                    @if(isset($article['author']))
                    <span>Bởi {{ $article['author'] }}</span>
                    @endif
                    
                    @if(isset($article['date']))
                    <span>{{ $article['date'] }}</span>
                    @endif
                </div>
                
                <a href="{{ $article['url'] }}" style="display: inline-block; margin-top: 15px; padding: 8px 16px; background-color: #667eea; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 14px;">
                    Đọc thêm →
                </a>
            </div>
        </div>
        @endforeach
    </td>
</tr>
@endif

<!-- Community Stats -->
<tr>
    <td style="padding: 0 30px 40px 30px;">
        <div style="background-color: #f8f9fa; border-radius: 8px; padding: 25px; text-align: center;">
            <h3 style="color: #333333; font-size: 18px; margin: 0 0 20px 0;">
                📊 Thống kê cộng đồng
            </h3>
            
            <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
                <div style="text-align: center; margin: 10px;">
                    <div style="font-size: 24px; font-weight: bold; color: #667eea;">
                        {{ number_format($stats['total_users']) }}
                    </div>
                    <div style="font-size: 12px; color: #666666; text-transform: uppercase;">
                        Thành viên
                    </div>
                </div>
                
                <div style="text-align: center; margin: 10px;">
                    <div style="font-size: 24px; font-weight: bold; color: #28a745;">
                        {{ number_format($stats['total_threads']) }}
                    </div>
                    <div style="font-size: 12px; color: #666666; text-transform: uppercase;">
                        Chủ đề
                    </div>
                </div>
                
                <div style="text-align: center; margin: 10px;">
                    <div style="font-size: 24px; font-weight: bold; color: #ffc107;">
                        {{ number_format($stats['total_comments']) }}
                    </div>
                    <div style="font-size: 12px; color: #666666; text-transform: uppercase;">
                        Bình luận
                    </div>
                </div>
                
                <div style="text-align: center; margin: 10px;">
                    <div style="font-size: 24px; font-weight: bold; color: #dc3545;">
                        {{ number_format($stats['total_showcases']) }}
                    </div>
                    <div style="font-size: 12px; color: #666666; text-transform: uppercase;">
                        Showcase
                    </div>
                </div>
            </div>
        </div>
    </td>
</tr>

<!-- Call to Action -->
<tr>
    <td style="padding: 0 30px 40px 30px; text-align: center;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 30px; color: #ffffff;">
            <h3 style="margin: 0 0 15px 0; font-size: 20px;">
                🚀 Tham gia thảo luận ngay!
            </h3>
            <p style="margin: 0 0 20px 0; font-size: 16px; opacity: 0.9;">
                Khám phá những chủ đề thú vị và chia sẻ kinh nghiệm với cộng đồng MechaMap
            </p>
            <a href="{{ url('/') }}" style="display: inline-block; padding: 12px 30px; background-color: #ffffff; color: #667eea; text-decoration: none; border-radius: 25px; font-weight: 600; font-size: 16px;">
                Truy cập MechaMap
            </a>
        </div>
    </td>
</tr>

<!-- Unsubscribe -->
@if($unsubscribeUrl)
<tr>
    <td style="padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
        <p style="font-size: 12px; color: #999999; margin: 0;">
            Bạn nhận được email này vì đã đăng ký nhận newsletter từ MechaMap.<br>
            <a href="{{ $unsubscribeUrl }}" style="color: #667eea; text-decoration: none;">
                Hủy đăng ký newsletter
            </a>
        </p>
    </td>
</tr>
@endif
@endsection
