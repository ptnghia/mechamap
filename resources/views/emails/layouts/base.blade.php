<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'MechaMap')</title>
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
        }
        
        /* Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            padding: 30px 40px;
            text-align: center;
            color: white;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .tagline {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        /* Content */
        .email-content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .message {
            font-size: 16px;
            line-height: 1.8;
            color: #555555;
            margin-bottom: 30px;
        }
        
        /* Button Styles */
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 35px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }
        
        /* Info Box */
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .info-box.success {
            border-left-color: #27ae60;
            background-color: #f8fff9;
        }
        
        .info-box.warning {
            border-left-color: #f39c12;
            background-color: #fffbf0;
        }
        
        .info-box h4 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .info-box p {
            color: #666666;
            margin: 0;
            font-size: 14px;
        }
        
        /* Stats/Features */
        .features {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            text-align: center;
        }
        
        .feature {
            flex: 1;
            padding: 0 15px;
        }
        
        .feature-icon {
            font-size: 24px;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .feature-title {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .feature-desc {
            font-size: 12px;
            color: #666666;
        }
        
        /* Footer */
        .email-footer {
            background-color: #2c3e50;
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .footer-content {
            margin-bottom: 20px;
        }
        
        .footer-links {
            margin: 20px 0;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }
        
        .footer-links a:hover {
            color: #3498db;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #bdc3c7;
            font-size: 18px;
            text-decoration: none;
        }
        
        .social-links a:hover {
            color: #3498db;
        }
        
        .copyright {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #34495e;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }
            
            .email-header,
            .email-content,
            .email-footer {
                padding: 25px 20px;
            }
            
            .features {
                flex-direction: column;
            }
            
            .feature {
                margin-bottom: 20px;
            }
            
            .btn {
                display: block;
                margin: 0 auto;
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">MechaMap</div>
            <div class="tagline">Cộng đồng Kỹ thuật Cơ khí Việt Nam</div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            @yield('content')
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="{{ config('app.url') }}">Trang chủ</a>
                    <a href="{{ config('app.url') }}/community">Cộng đồng</a>
                    <a href="{{ config('app.url') }}/showcase">Showcase</a>
                    <a href="{{ config('app.url') }}/marketplace">Marketplace</a>
                </div>
                
                <div class="social-links">
                    <a href="https://facebook.com/mechamap.vietnam">📘</a>
                    <a href="https://twitter.com/mechamap_vn">🐦</a>
                    <a href="https://instagram.com/mechamap.vietnam">📷</a>
                    <a href="https://linkedin.com/company/mechamap-vietnam">💼</a>
                    <a href="https://youtube.com/@MechaMapVietnam">📺</a>
                </div>
            </div>
            
            <div class="copyright">
                © {{ date('Y') }} MechaMap. All rights reserved.<br>
                Nếu bạn không muốn nhận email này, <a href="#" style="color: #3498db;">hủy đăng ký</a>
            </div>
        </div>
    </div>
</body>
</html>
