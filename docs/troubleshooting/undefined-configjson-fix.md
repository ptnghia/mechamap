# Fix: Undefined variable $configJson Error

## 🚨 **Vấn đề**

Khi deploy lên VPS, gặp lỗi:
```
Undefined variable $configJson (View: /path/to/resources/views/components/websocket-config.blade.php)
```

## 🔍 **Nguyên nhân**

### **1. Component property không được set trong constructor**
- `configJson` property không được khởi tạo trong constructor
- Laravel component system không tự động truyền properties vào view
- Method `configJson()` được gọi nhưng kết quả không được lưu vào property

### **2. Config cache outdated**
- Laravel config cache chứa cấu hình cũ
- WebSocket config không được load đúng

### **3. Environment variables thiếu**
- File `.env` thiếu WebSocket configuration
- Các biến `WEBSOCKET_SERVER_*` không được set

### **4. Config files thiếu**
- File `config/websocket.php` không tồn tại trên VPS
- Component không thể khởi tạo đúng

### **5. Component initialization error**
- WebSocketConfig component fail khi khởi tạo
- Method `configJson()` return null hoặc throw exception

## ✅ **Giải pháp nhanh**

### **Bước 1: Fix component constructor (FIXED - 2025-07-19)**
```bash
# Component đã được fix để set configJson trong constructor
# Không cần thực hiện thêm bước nào
```

### **Bước 2: Clear caches**
```bash
php artisan view:clear
php artisan config:clear
```

### **Bước 3: Debug chi tiết (nếu cần)**
```bash
# Kiểm tra cấu hình
php scripts/debug_websocket_config.php
```

## 🔧 **Giải pháp thủ công**

### **1. Clear all caches**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **2. Kiểm tra .env file**
```bash
# Thêm vào .env nếu thiếu:
WEBSOCKET_SERVER_URL=https://realtime.your-domain.com
WEBSOCKET_SERVER_HOST=realtime.your-domain.com
WEBSOCKET_SERVER_PORT=443
WEBSOCKET_SERVER_SECURE=true
REALTIME_SERVER_URL=https://realtime.your-domain.com
```

### **3. Kiểm tra config files**
```bash
# Đảm bảo file tồn tại
ls -la config/websocket.php

# Nếu thiếu, copy từ repository
scp config/websocket.php user@vps:/path/to/project/config/
```

### **4. Fix permissions**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **5. Regenerate autoload**
```bash
composer dump-autoload --optimize
```

### **6. Restart services**
```bash
# Nginx
sudo systemctl reload nginx

# PHP-FPM
sudo systemctl reload php8.1-fpm

# Realtime server
cd realtime-server && pm2 restart ecosystem.config.js
```

## 🧪 **Testing**

### **1. Test component directly**
```bash
php artisan tinker --execute="
\$component = new App\View\Components\WebSocketConfig();
echo 'Config JSON: ' . \$component->configJson();
"
```

### **2. Test in browser**
- Open browser console
- Check for WebSocket configuration object
- Verify no JavaScript errors

### **3. Check logs**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Realtime server logs
pm2 logs
```

## 🛡️ **Prevention**

### **1. Component constructor fixed (COMPLETED - 2025-07-19)**
WebSocketConfig component now properly sets configJson in constructor:
```php
// In constructor - configJson is now set as property
$this->configJson = $this->generateConfigJson();

// New generateConfigJson method with fallback
private function generateConfigJson(): string
{
    try {
        $config = [
            'server_url' => $this->serverUrl ?? 'https://realtime.mechamap.com',
            'server_host' => $this->serverHost ?? 'realtime.mechamap.com',
            'server_port' => $this->serverPort ?? 443,
            'secure' => $this->secure ?? true,
            'laravel_url' => $this->laravelUrl ?? config('app.url'),
            'environment' => app()->environment(),
            'auto_init' => $this->autoInit ?? true,
        ];
        return json_encode($config);
    } catch (\Exception $e) {
        // Fallback configuration
        return json_encode([
            'server_url' => 'https://realtime.mechamap.com',
            'server_host' => 'realtime.mechamap.com',
            'server_port' => 443,
            'secure' => true,
            'laravel_url' => config('app.url', 'https://mechamap.com'),
            'environment' => 'production',
            'auto_init' => true,
            'error' => true
        ]);
    }
}
```

### **2. View template updated**
Blade template now uses fallback values:
```php
{{-- WebSocket Configuration Component --}}
<script>
    // WebSocket Configuration from Laravel Backend
    window.websocketConfig = {!! $configJson ?? '{}' !!};

    // Auto-initialize flag
    window.autoInitWebSocket = {{ ($autoInit ?? true) ? 'true' : 'false' }};
</script>
```

### **2. Deployment checklist**
- [ ] Copy all config files to VPS
- [ ] Set WebSocket environment variables
- [ ] Clear all caches after deployment
- [ ] Test WebSocket component
- [ ] Verify realtime server is running

## 📚 **Related Documentation**

- [WebSocket Configuration Guide](../deployment/websocket-configuration.md)
- [VPS Deployment Guide](../deployment/PRODUCTION_DEPLOYMENT_README.md)
- [Troubleshooting Guide](../troubleshooting/README.md)

## 🎯 **Quick Commands Reference**

```bash
# Debug
php scripts/debug_websocket_config.php

# Fix
./scripts/fix_websocket_vps.sh

# Clear caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Test component
php artisan tinker --execute="echo (new App\View\Components\WebSocketConfig())->configJson();"

# Check logs
tail -f storage/logs/laravel.log
```
