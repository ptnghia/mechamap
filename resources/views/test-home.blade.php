<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechaMap - Test Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .feature {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .auth-buttons {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 MechaMap</h1>
            <p>Cộng đồng Kỹ thuật Cơ khí Việt Nam</p>
        </div>

        <div class="welcome">
            <h2>Chào mừng đến với MechaMap!</h2>
            <p>Nơi chia sẻ kiến thức và kết nối cộng đồng kỹ sư cơ khí Việt Nam</p>
        </div>

        <div class="features">
            <div class="feature">
                <h3>💬 Diễn đàn thảo luận</h3>
                <p>Tham gia thảo luận về các chủ đề kỹ thuật, chia sẻ kinh nghiệm và học hỏi từ cộng đồng.</p>
            </div>
            
            <div class="feature">
                <h3>📚 Thư viện tài liệu</h3>
                <p>Truy cập kho tài liệu phong phú về thiết kế cơ khí, CAD, và các công nghệ mới.</p>
            </div>
            
            <div class="feature">
                <h3>🛒 Marketplace</h3>
                <p>Mua bán thiết bị, phụ tùng cơ khí và các sản phẩm kỹ thuật chất lượng cao.</p>
            </div>
            
            <div class="feature">
                <h3>🎯 Showcase</h3>
                <p>Trưng bày các dự án, sản phẩm và thành tựu của bạn với cộng đồng.</p>
            </div>
        </div>

        <div class="auth-buttons">
            <a href="{{ route('login') }}" class="btn btn-primary">Đăng nhập</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Đăng ký</a>
        </div>

        <div style="text-align: center; margin-top: 40px; color: #6c757d;">
            <p><strong>Trang chủ test đang hoạt động bình thường!</strong></p>
            <p>Điều này chứng tỏ không có vấn đề với middleware hoặc authentication system.</p>
            <p>Vấn đề có thể nằm ở HomeController hoặc view home.blade.php gốc.</p>
        </div>
    </div>
</body>
</html>
