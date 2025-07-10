# 🔐 MechaMap Marketplace Secure Download System v2.0

## 📋 Tổng Quan

Hệ thống Secure Download System cho phép người dùng tải xuống các file kỹ thuật số một cách an toàn sau khi đã mua sản phẩm. Hệ thống được thiết kế với các tính năng bảo mật cao và không giới hạn thời gian tải xuống.

### **🆕 Cập nhật 2025 - Marketplace Restructure:**
- ✅ **Chỉ hỗ trợ sản phẩm `digital`** - Loại bỏ logic cũ cho `seller_type`
- ✅ **Permission-based access** - Kiểm tra quyền theo ma trận phân quyền mới
- ✅ **Enhanced security** - Token-based download với IP validation
- ✅ **Admin dashboard** - Thống kê download và quản lý files

## 🏗️ Kiến Trúc Hệ Thống

### **Components Chính**

1. **MarketplaceDownloadService** - Service xử lý logic download
2. **MarketplaceDownloadController** - Controller xử lý HTTP requests
3. **MarketplaceDownloadHistory** - Model tracking lịch sử download
4. **MarketplaceOrderObserver** - Observer tự động setup download access
5. **FileHelper** - Helper functions cho file operations

### **Database Schema**

```sql
-- marketplace_download_history
CREATE TABLE marketplace_download_history (
    id BIGINT PRIMARY KEY,
    uuid VARCHAR(36) UNIQUE,
    user_id BIGINT,
    order_id BIGINT,
    order_item_id BIGINT,
    product_id BIGINT,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    original_filename VARCHAR(255),
    file_size BIGINT,
    mime_type VARCHAR(100),
    downloaded_at TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    download_method VARCHAR(50),
    download_token VARCHAR(255),
    is_valid_download BOOLEAN,
    validation_status VARCHAR(50),
    validation_notes TEXT,
    metadata JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔒 Tính Năng Bảo Mật

### **1. Token-Based Authentication**
- Mỗi download tạo token bảo mật unique
- Token có thời hạn 24 giờ
- Token chỉ sử dụng được 1 lần cho mỗi file

### **2. Enhanced Purchase Verification (2025)**
- ✅ **Permission check** - Kiểm tra quyền mua digital theo role
- ✅ **Product type validation** - Chỉ `product_type === 'digital'`
- ✅ **Payment verification** - Xác minh đơn hàng đã thanh toán
- ✅ **Order ownership** - Validate quyền truy cập file

### **3. Advanced Download Tracking (2025)**
- ✅ **Role-based logging** - Ghi lại download với user role context
- ✅ **Permission audit** - Track permission checks
- ✅ **IP & User Agent** - Validation và logging
- ✅ **Rate limiting** - Ngăn chặn download abuse

### **4. Enhanced File Access Control (2025)**
- ✅ **Private storage** - File được lưu trong storage private
- ✅ **Secure endpoints** - Không thể truy cập trực tiếp qua URL
- ✅ **File validation** - Kiểm tra file existence trước download
- ✅ **Middleware protection** - `marketplace.permission` middleware

## 🚀 Cách Sử Dụng

### **1. Upload Digital Files (Admin/Seller)**

```php
// Trong admin product create/edit form
<input type="file" name="digital_files[]" multiple 
       accept=".dwg,.dxf,.step,.pdf,.doc,.zip">
```

### **2. Generate Download Token**

```javascript
// Frontend JavaScript
fetch('/marketplace/downloads/generate-token', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        order_item_id: orderItemId,
        file_index: fileIndex
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        window.location.href = data.data.download_url;
    }
});
```

### **3. Download File**

```php
// Service usage
$downloadService = app(MarketplaceDownloadService::class);

// Generate token
$tokenData = $downloadService->generateDownloadToken($user, $orderItem, $fileData);

// Process download
$downloadData = $downloadService->processSecureDownload($token, $ipAddress, $userAgent);
```

## 📊 API Endpoints

### **Authenticated Routes**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/marketplace/downloads` | Download history page |
| GET | `/marketplace/downloads/orders/{order}/files` | Order files page |
| GET | `/marketplace/downloads/items/{item}/files` | Item files page |
| POST | `/marketplace/downloads/generate-token` | Generate download token |
| GET | `/marketplace/downloads/stats` | Download statistics |
| GET | `/marketplace/downloads/history` | Download history API |

### **Public Routes**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/download/{token}` | Secure file download |

## 🛠️ Configuration

### **File Storage**

```php
// config/filesystems.php
'disks' => [
    'private' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'visibility' => 'private',
    ],
],
```

### **Supported File Types**

```php
// Trong validation rules
'digital_files.*' => 'file|mimes:dwg,dxf,step,stp,iges,igs,stl,pdf,doc,docx,zip,rar|max:51200'
```

## 📈 Performance Optimization

### **1. Caching Strategy**
- Download tokens cached for 24 hours
- File metadata cached
- User download stats cached

### **2. Database Indexing**
```sql
-- Indexes for performance
INDEX idx_download_history_user_date (user_id, downloaded_at)
INDEX idx_download_history_product (product_id, downloaded_at)
INDEX idx_download_history_token (download_token)
```

### **3. File Serving**
- Direct file streaming
- Proper MIME type headers
- Content-Length headers

## 🧪 Testing

### **Unit Tests**
```bash
php artisan test tests/Feature/MarketplaceDownloadTest.php
```

### **Security Test Script**
```bash
php scripts/test_download_system.php
```

### **Test Coverage**
- ✅ Token generation and validation
- ✅ Purchase verification
- ✅ File access control
- ✅ Download tracking
- ✅ User authorization
- ✅ Performance benchmarks

## 🔧 Troubleshooting

### **Common Issues**

1. **Token Expired**
   - Tokens expire after 24 hours
   - Generate new token for download

2. **File Not Found**
   - Check file exists in private storage
   - Verify file path in database

3. **Access Denied**
   - Verify user purchased product
   - Check order payment status

4. **Performance Issues**
   - Check database indexes
   - Monitor cache hit rates
   - Optimize file serving

### **Debug Commands**

```bash
# Check download history
php artisan tinker
>>> MarketplaceDownloadHistory::latest()->take(5)->get()

# Test download service
>>> $service = app(MarketplaceDownloadService::class)
>>> $service->getUserDownloadHistory($user)

# Clear download tokens cache
>>> Cache::flush()
```

## 📋 Security Checklist

- [x] Token-based authentication
- [x] Purchase verification
- [x] User authorization
- [x] Download tracking
- [x] File access control
- [x] IP address logging
- [x] User agent tracking
- [x] Secure file storage
- [x] No direct file URLs
- [x] Audit trail
- [x] No time limits (as required)

## 🚀 Deployment

### **Production Setup**

1. **Environment Variables**
```env
FILESYSTEM_DISK=private
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

2. **Storage Permissions**
```bash
chmod -R 755 storage/app/private
chown -R www-data:www-data storage/app/private
```

3. **Nginx Configuration**
```nginx
# Block direct access to private files
location /storage/app/private {
    deny all;
    return 404;
}
```

## 📞 Support

Để được hỗ trợ về hệ thống download:
1. Kiểm tra logs: `storage/logs/laravel.log`
2. Chạy test script: `php scripts/test_download_system.php`
3. Xem documentation: `/docs/marketplace/`

---

**Phiên bản:** 1.0.0  
**Cập nhật:** {{ date('d/m/Y') }}  
**Tác giả:** MechaMap Development Team
