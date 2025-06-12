# 🧪 Testing Scripts và Tools

Thư mục này chứa các script testing, verification và maintenance cho hệ thống MechaMap Backend.

## 📋 Phân Loại Scripts

### 🔍 System Verification Scripts
- `final_verification.sh` - Script kiểm tra toàn diện hệ thống (PHP, Laravel, Database, etc.)
- `simple_verification.php` - Kiểm tra cơ bản cho hệ thống
- `final_verification_placeholder.php` - Kiểm tra placeholder images và thay thế

### 🎨 CSS/Frontend Validation Scripts
- `validate-css-structure.sh` - Validation cấu trúc CSS sau khi loại bỏ Vite
- `validate-css-admin-user-separation.sh` - Kiểm tra phân tách CSS admin/user
- `verify_dropdown_cleanup.sh` - Verification cleanup dropdown.js

### 🔧 Code Quality Scripts
- `check_unused_js_files.php` - Kiểm tra các file JavaScript không được sử dụng
- `simple_mock_data_check.php` - Kiểm tra mock data patterns trong codebase

### 📊 Performance Testing
- `test_performance_optimization.php` - Test tối ưu hóa performance
- `simple_performance_test.php` - Test performance đơn giản
- `final_performance_check.php` - Kiểm tra performance cuối cùng

### 🌐 API Testing
- `test_quality_api.php` - Test Thread Quality API
- `test_rating_analytics.php` - Test Rating Analytics
- `test_mechamap_domain.php` - Test domain configuration
- `test_mechamap_fixed.php` - Test fixes cho domain issues

### 🗣️ Localization Testing
- `test_localization.php` - Test localization cơ bản
- `test_localization_complete.php` - Test localization hoàn chỉnh

### 🎲 Test Data Management
- `create_test_data.php` - Tạo dữ liệu test
- `simple_test.php` - Test cơ bản
- `test_complete.php` - Test hoàn chỉnh

### 📈 Reports
- `api_health_report_*.html` - Các báo cáo health check API

## 🚀 Cách Sử Dụng

### Chạy System Verification
```bash
# Verification toàn diện
bash docs/testing/final_verification.sh

# Verification đơn giản
php docs/testing/simple_verification.php
```

### Kiểm Tra CSS Structure
```bash
bash docs/testing/validate-css-structure.sh
bash docs/testing/validate-css-admin-user-separation.sh
```

### Test Performance
```bash
php docs/testing/test_performance_optimization.php
php docs/testing/simple_performance_test.php
```

### Kiểm Tra Code Quality
```bash
php docs/testing/check_unused_js_files.php
php docs/testing/simple_mock_data_check.php
```

## 📝 Lưu Ý

- Các script này được thiết kế để chạy từ root directory của project
- Một số script yêu cầu Laravel application được bootstrap
- Scripts `.sh` yêu cầu bash/git bash trên Windows
- Tất cả output và comment đều bằng tiếng Việt để dễ đọc

## 🛠️ Maintenance

Các script này nên được chạy định kỳ để:
- Kiểm tra sức khỏe hệ thống
- Validate structure sau các thay đổi lớn
- Đảm bảo performance không bị suy giảm
- Phát hiện dead code hoặc unused assets
