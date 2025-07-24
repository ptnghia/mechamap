<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MechaMap</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .email-header .logo {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .email-footer a {
            color: #3498db;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .social-links {
            margin: 15px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #ecf0f1;
            font-size: 18px;
            text-decoration: none;
        }
        .social-links a:hover {
            color: #3498db;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-header, .email-body, .email-footer {
                padding: 20px 15px !important;
            }
            .email-header h1 {
                font-size: 24px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">
                <i class="fas fa-cogs"></i>
            </div>
            <h1>MechaMap</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 16px;">
                Cộng đồng Kỹ thuật Cơ khí Việt Nam
            </p>
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="social-links">
                <a href="https://facebook.com/mechamap" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://youtube.com/mechamap" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://linkedin.com/company/mechamap" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://github.com/mechamap" title="GitHub">
                    <i class="fab fa-github"></i>
                </a>
            </div>
            
            <p style="margin: 15px 0 5px 0;">
                <strong>MechaMap</strong> - Nền tảng chia sẻ kiến thức kỹ thuật cơ khí
            </p>
            
            <p style="margin: 5px 0; font-size: 13px; opacity: 0.8;">
                📧 Email: <a href="mailto:support@mechamap.com">support@mechamap.com</a><br>
                🌐 Website: <a href="https://mechamap.com">mechamap.com</a><br>
                📱 Hotline: <a href="tel:+84123456789">+84 123 456 789</a>
            </p>
            
            <hr style="border: none; border-top: 1px solid #34495e; margin: 20px 0;">
            
            <p style="margin: 10px 0; font-size: 12px; opacity: 0.7;">
                © {{ date('Y') }} MechaMap. Tất cả quyền được bảo lưu.<br>
                Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh, Việt Nam
            </p>
            
            <p style="margin: 15px 0 0 0; font-size: 11px; opacity: 0.6;">
                Email này được gửi tự động. Vui lòng không trả lời email này.<br>
                Nếu bạn không muốn nhận email thông báo, 
                <a href="{{ route('profile.notifications') }}">click vào đây để cập nhật cài đặt</a>.
            </p>
        </div>
    </div>
</body>
</html>
