# 🧪 Testing & Quality Assurance

> **Cập nhật**: 24/06/2025 - Đã tối ưu và loại bỏ các báo cáo trùng lặp

Thư mục này chứa các script testing, verification và maintenance cho hệ thống MechaMap Backend.

## 📊 **TỔNG QUAN TESTING STATUS**

- ✅ **Database Testing**: 90% hoàn thành (57/61 bảng đã test)
- ✅ **API Testing**: 80% hoàn thành (core endpoints đã test)
- ✅ **Integration Testing**: 85% hoàn thành
- ✅ **Performance Testing**: 75% hoàn thành

## 📋 **CẤU TRÚC THƯ MỤC**

### 📊 **Summary Reports** (Chính)
- [`COMPREHENSIVE_TEST_PLAN.md`](./COMPREHENSIVE_TEST_PLAN.md) - Kế hoạch test tổng thể
- [`DATABASE_TESTING_STATUS_REPORT.md`](./DATABASE_TESTING_STATUS_REPORT.md) - Báo cáo test database
- [`COMPREHENSIVE_DATABASE_TEST_SUMMARY.md`](./COMPREHENSIVE_DATABASE_TEST_SUMMARY.md) - Tổng kết test database

### 🔍 **Verification Scripts**
- [`verification-tests/`](./verification-tests/) - Scripts kiểm tra hệ thống
  - `final_verification.sh` - Kiểm tra toàn diện hệ thống
  - `simple_verification.php` - Kiểm tra cơ bản
  - `validate-css-structure.sh` - Validation CSS structure

### 🧪 **Test Categories**

#### **API Tests**
- [`api-tests/`](./api-tests/) - API endpoint testing
  - `test_quality_api.php` - Test thread quality API
  - `test_rating_analytics.php` - Test rating system

#### **Integration Tests**
- [`integration-tests/`](./integration-tests/) - Full system integration tests
  - `test_complete_integration.sh` - Complete integration test
  - `test_thread_actions.sh` - Thread actions integration

#### **Performance Tests**
- [`performance-tests/`](./performance-tests/) - Performance và load testing
  - `final_performance_check.php` - Performance benchmark
  - `simple_performance_test.php` - Basic performance test

#### **Browser Tests**
- [`browser-tests/`](./browser-tests/) - Frontend browser testing
  - `manual_browser_test_guide.md` - Manual testing guide
  - `simple_browser_test.php` - Automated browser test

#### **Thread Tests**
- [`thread-tests/`](./thread-tests/) - Thread system specific tests
  - `automated_thread_creation_test.php` - Thread creation automation
  - `final_thread_creation_check.php` - Thread creation verification

#### **Simple Tests**
- [`simple-tests/`](./simple-tests/) - Quick verification tests
  - `simple_connectivity_test.php` - Database connectivity
  - `simple_server_test.php` - Server health check

#### **Utilities**
- [`utilities/`](./utilities/) - Testing utilities và helpers
  - `create_test_data.php` - Generate test data
  - `check_unused_js_files.php` - Check unused assets

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
