<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .message-body {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">MechaMap</div>
            <div>Cộng đồng Kỹ sư Cơ khí Việt Nam</div>
        </div>

        <div class="content">
            <h2>{{ $title }}</h2>
            
            <div class="message-body">
                {!! nl2br(e($message)) !!}
            </div>

            @if(isset($data['action_url']) && $data['action_url'])
                <div style="text-align: center; margin: 20px 0;">
                    <a href="{{ $data['action_url'] }}" 
                       style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                        {{ $data['action_text'] ?? 'Xem chi tiết' }}
                    </a>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>
                Email này được gửi từ hệ thống thông báo MechaMap.<br>
                Nếu bạn không muốn nhận email này, vui lòng 
                <a href="{{ config('app.url') }}/notifications/preferences">cập nhật tùy chọn thông báo</a>.
            </p>
            <p>
                © {{ date('Y') }} MechaMap - Cộng đồng Kỹ sư Cơ khí Việt Nam<br>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
