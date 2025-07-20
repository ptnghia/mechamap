# Task 1.4: Tạo Mapping Matrix - Báo Cáo

**Thời gian thực hiện:** 2025-07-20 02:20:45
**Trạng thái:** ✅ HOÀN THÀNH

## 📊 Thống Kê Mapping

- **Total used keys:** 70
- **Successfully mapped:** 37
- **Merge actions:** 0
- **Unmapped keys:** 33
- **New files to create:** 37

## 🗂️ File Organization Plan

- **ui/common.php**: 31 keys [HIGH]
  - Sample: `ui.common.language.switched_successfully`, `ui.common.language.switch_failed`, `ui.common.language.auto_detect_failed`, `ui.common.loading`, `ui.common.language.switched_successfully`

- **ui/navigation.php**: 1 keys [LOW]
  - Sample: `ui.navigation.auth.login`

- **content/pages.php**: 5 keys [MEDIUM]
  - Sample: `content.pages.processing`, `content.pages.error_occurred`, `content.pages.notify_success`, `content.pages.share_text`, `content.pages.copied`

## 🔄 Sample Mappings

- `messages.language.switched_successfully` → `ui.common.language.switched_successfully` (confidence: 50%)
- `messages.language.switch_failed` → `ui.common.language.switch_failed` (confidence: 50%)
- `messages.language.auto_detect_failed` → `ui.common.language.auto_detect_failed` (confidence: 50%)
- `common.loading` → `ui.common.loading` (confidence: 50%)
- `language.switched_successfully` → `ui.common.language.switched_successfully` (confidence: 50%)
- `language.switch_failed` → `ui.common.language.switch_failed` (confidence: 50%)
- `language.auto_detected` → `ui.common.language.auto_detected` (confidence: 50%)
- `messages.forgot_password` → `ui.common.forgot_password` (confidence: 50%)
- `nav.auth.login` → `ui.navigation.auth.login` (confidence: 70%)
- `content.processing` → `content.pages.processing` (confidence: 80%)
- `content.error_occurred` → `content.pages.error_occurred` (confidence: 80%)
- `messages.please_enter_valid_page` → `ui.common.please_enter_valid_page` (confidence: 50%)
- `messages.close` → `ui.common.close` (confidence: 50%)
- `messages.next` → `ui.common.next` (confidence: 50%)
- `messages.previous` → `ui.common.previous` (confidence: 50%)

## ✅ Task 1.4 Completion

- [x] Tạo mapping rules cho tất cả categories ✅
- [x] Map keys cũ sang cấu trúc mới ✅
- [x] Phân loại keys vào thư mục tương ứng ✅
- [x] Generate file organization plan ✅

**Next Task:** 1.5 Backup dữ liệu hiện tại
