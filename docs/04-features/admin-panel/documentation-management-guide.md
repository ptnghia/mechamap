# 📄 Hướng Dẫn Quản Lý Tài Liệu - MechaMap Admin

> **Đối tượng**: Admin, Content Manager, Moderator  
> **Cập nhật**: 02/07/2025  
> **Phiên bản**: 1.0 - Production Ready

---

## 🎯 **TỔNG QUAN**

Hệ thống Quản lý Tài liệu MechaMap cung cấp giao diện admin hoàn chỉnh để tạo, chỉnh sửa và quản lý tài liệu kỹ thuật cho cộng đồng. Hệ thống tích hợp TinyMCE editor và có error handling toàn diện.

### **✨ Tính năng chính:**
- **📝 CRUD Operations** - Tạo, xem, sửa, xóa tài liệu
- **✏️ TinyMCE Editor** - Trình soạn thảo WYSIWYG chuyên nghiệp
- **📂 Phân loại** - Danh mục, loại nội dung, độ khó
- **🎯 Đối tượng mục tiêu** - Phân quyền theo vai trò người dùng
- **🔍 SEO** - Meta title, description, keywords
- **🛡️ Error Handling** - Xử lý lỗi với thông báo tiếng Việt

---

## 🚀 **TRUY CẬP HỆ THỐNG**

### **📍 URL Admin:**
```
https://mechamap.test/admin/documentation
```

### **🔐 Quyền truy cập:**
- **Admin** - Toàn quyền
- **Content Manager** - Tạo và chỉnh sửa
- **Moderator** - Xem và duyệt

---

## 📋 **DANH SÁCH TÀI LIỆU**

### **📊 Thống kê tổng quan:**
- **Tổng tài liệu** - Số lượng tài liệu trong hệ thống
- **Đã xuất bản** - Tài liệu công khai
- **Bản nháp** - Tài liệu chưa hoàn thành
- **Lượt xem hôm nay** - Thống kê truy cập

### **🔍 Tìm kiếm và lọc:**
- **Tìm kiếm** - Theo tiêu đề và nội dung
- **Lọc theo danh mục** - Dropdown danh mục
- **Lọc theo trạng thái** - Draft, Published, Archived
- **Lọc theo loại** - Guide, API, Tutorial, Reference, FAQ
- **Lọc theo độ khó** - Beginner, Intermediate, Advanced, Expert

### **⚡ Bulk Actions:**
- **Chọn nhiều** - Checkbox để chọn nhiều tài liệu
- **Thao tác hàng loạt** - Xóa, thay đổi trạng thái, di chuyển danh mục

---

## ➕ **TẠO TÀI LIỆU MỚI**

### **📝 Thông tin cơ bản:**
1. **Tiêu đề*** - Tên tài liệu (bắt buộc)
2. **Slug** - URL thân thiện (tự động tạo từ tiêu đề)
3. **Tóm tắt** - Mô tả ngắn gọn
4. **Nội dung*** - Sử dụng TinyMCE editor (bắt buộc)
5. **Tags** - Từ khóa phân loại

### **✏️ TinyMCE Editor:**
- **Toolbar đầy đủ** - Bold, italic, alignment, lists, links
- **Word count** - Đếm từ real-time
- **Auto-save** - Tự động lưu khi thay đổi
- **Markdown support** - Hỗ trợ cú pháp Markdown

### **🔧 Cài đặt SEO:**
- **Meta Title** - Tiêu đề cho search engines
- **Meta Description** - Mô tả cho search results
- **Meta Keywords** - Từ khóa SEO

### **📂 Phân loại:**
- **Danh mục*** - Chọn danh mục phù hợp (bắt buộc)
- **Loại nội dung*** - Guide/API/Tutorial/Reference/FAQ (bắt buộc)
- **Độ khó*** - Beginner/Intermediate/Advanced/Expert (bắt buộc)
- **Đối tượng mục tiêu** - Chọn vai trò người dùng có thể xem

### **📤 Xuất bản:**
- **Trạng thái** - Draft/Review/Published/Archived
- **Công khai** - Hiển thị cho public
- **Nổi bật** - Đánh dấu tài liệu quan trọng
- **Cho phép bình luận** - Bật/tắt comment
- **Ngày xuất bản** - Lên lịch xuất bản

### **💾 Lưu tài liệu:**
- **Lưu tài liệu** - Lưu và quay về danh sách
- **Lưu và tiếp tục chỉnh sửa** - Lưu và ở lại trang edit
- **Hủy** - Quay về danh sách không lưu

---

## 👁️ **XEM CHI TIẾT TÀI LIỆU**

### **📄 Thông tin hiển thị:**
- **Tiêu đề và badges** - Status, Featured, Public
- **Meta information** - Danh mục, loại, độ khó, tác giả, ngày tạo
- **Tóm tắt** - Excerpt nếu có
- **Nội dung** - Markdown được render thành HTML
- **Tags** - Danh sách tags

### **📊 Sidebar thống kê:**
- **Trạng thái** - Published/Draft/Review
- **Lượt xem** - View count
- **Đánh giá** - Rating average và count
- **Tải xuống** - Download count
- **Thời gian đọc** - Estimated read time

### **🔧 Thao tác:**
- **Dropdown menu** - Chỉnh sửa và xóa
- **Confirmation modal** - Xác nhận trước khi xóa

---

## ✏️ **CHỈNH SỬA TÀI LIỆU**

### **📝 Form chỉnh sửa:**
- **Layout 2 cột** - Main content và sidebar
- **TinyMCE editor** - Rich text editing với toolbar
- **Auto-load data** - Tất cả fields được load từ database
- **Auto-slug generation** - Tự động tạo slug khi thay đổi tiêu đề

### **💾 Lưu thay đổi:**
- **Cập nhật tài liệu** - Lưu và xem chi tiết
- **Lưu và tiếp tục chỉnh sửa** - Lưu và ở lại trang edit
- **Hủy** - Quay về danh sách không lưu

### **🔄 Version control:**
- **Change summary** - Ghi chú thay đổi
- **Version tracking** - Lưu lịch sử thay đổi
- **Author tracking** - Theo dõi người chỉnh sửa

---

## 🛡️ **XỬ LÝ LỖI**

### **✅ Validation:**
- **Client-side** - Kiểm tra trước khi submit
- **Server-side** - Validation rules đầy đủ
- **Error messages** - Thông báo lỗi tiếng Việt

### **🔧 Error handling:**
- **Try-catch blocks** - Bắt lỗi toàn diện
- **Database errors** - Xử lý lỗi duplicate slug
- **User-friendly messages** - Thông báo dễ hiểu
- **Error logging** - Ghi log để debug

### **⚠️ Lỗi thường gặp:**
- **Slug trùng lặp** - Tự động suggest slug mới
- **Validation errors** - Hiển thị field bị lỗi
- **Database connection** - Thông báo lỗi kết nối
- **Permission denied** - Thông báo không đủ quyền

---

## 🎯 **BEST PRACTICES**

### **📝 Viết nội dung:**
- **Tiêu đề rõ ràng** - Mô tả chính xác nội dung
- **Cấu trúc logic** - Sử dụng headings và lists
- **Ngôn ngữ đơn giản** - Dễ hiểu cho đối tượng mục tiêu
- **Ví dụ cụ thể** - Code examples và screenshots

### **🔍 SEO optimization:**
- **Meta title** - 50-60 ký tự
- **Meta description** - 150-160 ký tự
- **Keywords** - 5-10 từ khóa chính
- **Internal links** - Liên kết đến tài liệu liên quan

### **📂 Phân loại:**
- **Danh mục phù hợp** - Chọn category chính xác
- **Tags relevant** - Sử dụng tags có ý nghĩa
- **Độ khó chính xác** - Đánh giá đúng level
- **Đối tượng mục tiêu** - Chọn roles phù hợp

---

## 📞 **HỖ TRỢ**

### **🆘 Liên hệ:**
- **Technical Support** - admin@mechamap.com
- **Content Team** - content@mechamap.com
- **Emergency** - +84-xxx-xxx-xxxx

### **📚 Tài liệu tham khảo:**
- **TinyMCE Documentation** - https://www.tiny.cloud/docs/
- **Markdown Guide** - https://www.markdownguide.org/
- **SEO Best Practices** - Internal SEO guide

---

**🎉 Hệ thống Documentation Management đã sẵn sàng phục vụ cộng đồng MechaMap!**
