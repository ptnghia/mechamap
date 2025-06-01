# 📚 Tài Liệu MechaMap

Thư mục này chứa tất cả tài liệu kỹ thuật, hướng dẫn và báo cáo của dự án MechaMap.

## 📁 Cấu Trúc Thư Mục

```
docs/
├── 📖 guides/          # Hướng dẫn cài đặt và triển khai
├── 📊 reports/         # Báo cáo phân tích và tiến độ dự án  
├── 🧪 tests/           # File test và utility scripts
└── 🚀 scripts/         # Scripts deployment và automation
```

## 📖 Hướng Dẫn (Guides)

### 🏗️ Deployment & Configuration

| File | Mô tả | Mục đích |
|------|-------|----------|
| **[deployment-guide.md](guides/deployment-guide.md)** | Hướng dẫn triển khai chi tiết trên VPS/Server | Production deployment |
| **[deployment-guide-hosting.md](guides/deployment-guide-hosting.md)** | Triển khai qua cPanel/shared hosting | Hosting deployment |
| **[backend-config-guide.md](guides/backend-config-guide.md)** | Cấu hình CORS, Sanctum cho production | Backend configuration |
| **[server-requirements.md](guides/server-requirements.md)** | Yêu cầu server và thông số kỹ thuật | Infrastructure planning |

## 📊 Báo Cáo (Reports)

### 📈 Phân Tích Tiến Độ

| File | Mô tả | Trạng thái |
|------|-------|------------|
| **[SETTINGS-COMPLETION-REPORT.md](reports/SETTINGS-COMPLETION-REPORT.md)** | Tiến độ hoàn thành Admin Settings Interface | ✅ 11/16 modules |
| **[ADMIN-SETTINGS-ANALYSIS-REPORT.md](reports/ADMIN-SETTINGS-ANALYSIS-REPORT.md)** | Phân tích chi tiết hệ thống admin settings | Hoàn thành |
| **[SEO-SETTINGS-INTEGRATION-REPORT.md](reports/SEO-SETTINGS-INTEGRATION-REPORT.md)** | Báo cáo tích hợp SEO settings | Đang phát triển |

## 🧪 Testing & Utilities (Tests)

### 🔧 Development Tools

| File | Mô tả | Cách sử dụng |
|------|-------|--------------|
| **[check_permissions.php](tests/check_permissions.php)** | Kiểm tra quyền file/folder | `php docs/tests/check_permissions.php` |
| **[check_settings.php](tests/check_settings.php)** | Validate cấu hình hệ thống | `php docs/tests/check_settings.php` |
| **[update_image_paths.php](tests/update_image_paths.php)** | Update đường dẫn hình ảnh | `php docs/tests/update_image_paths.php` |

## 🚀 Scripts & Automation

### ⚡ Deployment Scripts

| File | Mô tả | Cách sử dụng |
|------|-------|--------------|
| **[deploy.sh](scripts/deploy.sh)** | Script triển khai tự động | `chmod +x docs/scripts/deploy.sh && ./docs/scripts/deploy.sh` |
| **[composer-hosting.json](scripts/composer-hosting.json)** | Composer config cho hosting | Copy to root khi deploy |

## 🔍 Hướng Dẫn Sử Dụng

### 🏃‍♂️ Quick Start

1. **Đọc trước**: [Server Requirements](guides/server-requirements.md)
2. **Development**: Theo README.md ở root directory
3. **Production**: 
   - VPS/Server: [Deployment Guide](guides/deployment-guide.md)
   - Shared Hosting: [Hosting Deployment](guides/deployment-guide-hosting.md)

### 🔧 Troubleshooting

1. **Lỗi permissions**: Chạy [check_permissions.php](tests/check_permissions.php)
2. **Lỗi config**: Chạy [check_settings.php](tests/check_settings.php)
3. **Deployment issues**: Xem [deployment guides](guides/)

### 📝 Contribution Guidelines

Khi thêm tài liệu mới:

1. **Guides**: Hướng dẫn step-by-step, có examples
2. **Reports**: Phân tích chi tiết với số liệu cụ thể
3. **Tests**: Include usage instructions và error handling
4. **Scripts**: Comment đầy đủ bằng tiếng Việt

### 🏷️ Naming Convention

- **Guides**: `kebab-case.md` (VD: `deployment-guide.md`)
- **Reports**: `UPPER-CASE-REPORT.md` (VD: `SETTINGS-COMPLETION-REPORT.md`)
- **Tests**: `snake_case.php` (VD: `check_permissions.php`)
- **Scripts**: `kebab-case.sh` hoặc `kebab-case.json`

---

📋 **Lưu ý**: Tất cả tài liệu được viết bằng tiếng Việt để dễ hiểu và phù hợp với cộng đồng người dùng Việt Nam.
