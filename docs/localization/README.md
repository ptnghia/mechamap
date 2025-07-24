# MechaMap Localization Documentation

## Mục đích
Thư mục này chứa tất cả tài liệu liên quan đến quá trình localization (đa ngôn ngữ) của dự án MechaMap.

## Cấu trúc thư mục

### Báo cáo và Phân tích
- `*_REPORT.md` - Các báo cáo chi tiết về quá trình localization
- `audit_*.json` - Kết quả kiểm tra và phân tích code
- `*_analysis.json` - Phân tích chi tiết về translation keys

### Backup Files
- `*_backup_*` - Backup các file view trước khi chuyển đổi
- Được tổ chức theo ngày và thời gian backup

### Tools và Scripts
- `*.php` - Các script hỗ trợ quá trình localization
- `*.sh` - Shell scripts cho automation

### Guides và Documentation
- `MIGRATION_GUIDE.md` - Hướng dẫn migration
- `DEVELOPER_GUIDE.md` - Hướng dẫn cho developer
- `DEPLOYMENT_CHECKLIST.md` - Checklist triển khai

## Lưu ý quan trọng

### Tại sao di chuyển từ storage/
1. **Laravel Best Practice**: Thư mục storage chỉ nên chứa runtime data
2. **Git Tracking**: Tài liệu cần được track bởi git, storage thì không
3. **Tổ chức dự án**: Tài liệu thuộc về docs/, không phải storage/
4. **Bảo mật**: Tránh expose tài liệu qua web nếu storage được symlink

### Quy tắc sử dụng
- Không di chuyển file này trở lại storage/
- Backup files chỉ để tham khảo, không sử dụng trong production
- Scripts và tools chỉ dùng trong development environment

## Liên hệ
Nếu có thắc mắc về localization process, tham khảo:
- `DEVELOPER_GUIDE.md` - Hướng dẫn chi tiết
- `PROJECT_COMPLETION_REPORT.md` - Báo cáo tổng kết
