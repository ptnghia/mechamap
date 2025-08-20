---
type: "manual"
---

# AugmentCode Project Rules – Translation

## Quy tắc sử dụng đa ngôn ngữ

- Dự án được cấu hình **2 ngôn ngữ**:
  - **Tiếng Việt** (ngôn ngữ mặc định)  
  - **Tiếng Anh** (ngôn ngữ thứ 2)

## Translation Keys

- Tất cả các **translation keys** được lưu trong **database**.
- Sử dụng link quản lý translation:  
  👉 [https://mechamap.test/translations](https://mechamap.test/translations)  
  để **tra cứu, chỉnh sửa hoặc thêm mới** khi cần.

## Quy tắc làm việc với file `.blade`

- Khi phát hiện **text cố định** trong file `.blade`, cần:
  1. Kiểm tra translation key tương ứng.  
  2. Sử dụng key phù hợp thay cho text cố định.  
  3. Nếu chưa có key, sử dụng link [https://mechamap.test/translations](https://mechamap.test/translations)  
     để tạo mới hoặc chỉnh sửa.

## Quy tắc chỉnh sửa / thêm mới translation keys

- Khi chỉnh sửa hoặc thêm mới translation key:
  - **Bắt buộc cập nhật cả tiếng Việt và tiếng Anh**.
  - Đảm bảo nội dung dịch chính xác, thống nhất và dễ hiểu.
