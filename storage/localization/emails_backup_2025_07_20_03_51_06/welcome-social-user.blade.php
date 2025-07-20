<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chào mừng bạn đến với MechaMap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a76a8;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #4a76a8;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Chào mừng bạn đến với MechaMap</h1>
    </div>
    
    <div class="content">
        <p>Xin chào {{ $user->name }},</p>
        
        <p>Cảm ơn bạn đã đăng ký tài khoản tại MechaMap thông qua {{ $provider == 'google' ? 'Google' : 'Facebook' }}.</p>
        
        <p>Chúng tôi đã tạo một tài khoản cho bạn với thông tin đăng nhập sau:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Tên đăng nhập:</strong> {{ $user->username }}</p>
            <p><strong>Mật khẩu:</strong> {{ $plainPassword }}</p>
        </div>
        
        <p>Bạn có thể sử dụng thông tin này để đăng nhập vào tài khoản của mình, hoặc tiếp tục đăng nhập thông qua {{ $provider == 'google' ? 'Google' : 'Facebook' }}.</p>
        
        <p>Để đăng nhập, vui lòng nhấp vào nút bên dưới:</p>
        
        <a href="{{ url('/login') }}" class="button">Đăng nhập ngay</a>
        
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
        
        <p>Trân trọng,<br>Đội ngũ MechaMap</p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động. Vui lòng không trả lời email này.</p>
        <p>&copy; {{ date('Y') }} MechaMap. Tất cả các quyền được bảo lưu.</p>
    </div>
</body>
</html>
