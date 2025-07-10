# 🚀 MechaMap Quick Start Guide

> **Bắt đầu sử dụng MechaMap trong 5 phút**  
> Hướng dẫn nhanh cho tất cả người dùng

---

## 🎯 **CHỌN HƯỚNG DẪN PHÍCH HỢP**

### **👥 Tôi là người dùng mới**
```
🎯 Mục tiêu: Tham gia cộng đồng, sử dụng forum và marketplace
📖 Hướng dẫn: User Quick Start (bên dưới)
⏱️ Thời gian: 5 phút
```

### **👨‍💼 Tôi là quản trị viên**
```
🎯 Mục tiêu: Quản lý hệ thống, người dùng và nội dung
📖 Hướng dẫn: Admin Quick Start
⏱️ Thời gian: 10 phút
```

### **👨‍💻 Tôi là lập trình viên**
```
🎯 Mục tiêu: Setup môi trường phát triển, hiểu architecture
📖 Hướng dẫn: Developer Quick Start
⏱️ Thời gian: 30 phút
```

---

## 👥 **USER QUICK START**

### **Bước 1: Tạo tài khoản (2 phút)**
1. **Truy cập**: https://mechamap.com
2. **Đăng ký**: Click "Đăng ký" → Điền thông tin → Xác nhận email
3. **Hoàn thiện profile**: Thêm avatar, thông tin chuyên môn

### **Bước 2: Khám phá Forum (2 phút)**
1. **Xem danh mục**: Chọn lĩnh vực quan tâm (Thiết kế, Sản xuất, Vật liệu...)
2. **Đọc bài viết**: Click vào thread để xem thảo luận
3. **Tham gia**: Comment, like, follow các thread hay

### **Bước 3: Sử dụng Marketplace (1 phút)**
1. **Duyệt sản phẩm**: Xem sản phẩm kỹ thuật số, thiết bị mới
2. **Tìm kiếm**: Sử dụng filter theo loại, giá, danh mục
3. **Mua sắm**: Thêm vào giỏ hàng → Thanh toán

### **🎉 Hoàn thành! Bạn đã sẵn sàng sử dụng MechaMap**

**📖 Tiếp theo:**
- [Hướng dẫn Forum chi tiết](./user-guides/forum-guide.md)
- [Hướng dẫn Marketplace](./user-guides/marketplace-guide.md)
- [FAQ](./user-guides/faq.md)

---

## 👨‍💼 **ADMIN QUICK START**

### **Bước 1: Truy cập Admin Panel (2 phút)**
1. **Login**: Đăng nhập với tài khoản admin
2. **Admin Dashboard**: Truy cập `/admin` → Xem overview
3. **Permissions**: Kiểm tra quyền hạn của bạn

### **Bước 2: Quản lý người dùng (3 phút)**
1. **User Management**: `/admin/users` → Xem danh sách users
2. **Role Assignment**: Gán role (Member, Supplier, Manufacturer, Brand)
3. **User Moderation**: Approve/reject registrations

### **Bước 3: Quản lý nội dung (3 phút)**
1. **Forum Moderation**: Kiểm duyệt posts, comments
2. **Marketplace Products**: Approve/reject sản phẩm mới
3. **Content Reports**: Xử lý báo cáo vi phạm

### **Bước 4: Cấu hình hệ thống (2 phút)**
1. **System Settings**: Cấu hình general settings
2. **Marketplace Settings**: Cấu hình permissions, categories
3. **Analytics**: Xem reports và statistics

### **🎉 Hoàn thành! Bạn đã nắm được cơ bản admin panel**

**📖 Tiếp theo:**
- [Admin Guide đầy đủ](./admin-guides/README.md)
- [User Management chi tiết](./admin-guides/user-management.md)
- [Marketplace Admin](./admin-guides/marketplace-admin.md)

---

## 👨‍💻 **DEVELOPER QUICK START**

### **Bước 1: Setup môi trường (10 phút)**
1. **Requirements**: PHP 8.2+, MySQL 8.0+, Node.js 18+
2. **Clone project**: 
   ```bash
   git clone https://github.com/mechamap/mechamap.git
   cd mechamap
   ```
3. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

### **Bước 2: Cấu hình database (5 phút)**
1. **Create database**: `mechamap_local`
2. **Environment**: Copy `.env.example` → `.env`
3. **Database config**:
   ```env
   DB_DATABASE=mechamap_local
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

### **Bước 3: Migrate và seed (5 phút)**
1. **Generate key**: `php artisan key:generate`
2. **Run migrations**: `php artisan migrate`
3. **Seed data**: `php artisan db:seed`

### **Bước 4: Start development (5 phút)**
1. **Start server**: `php artisan serve`
2. **Compile assets**: `npm run dev`
3. **Access**: http://localhost:8000

### **Bước 5: Verify setup (5 phút)**
1. **Frontend**: Kiểm tra homepage load
2. **Admin**: Login admin@mechamap.vn / admin123456
3. **API**: Test `/api/health` endpoint

### **🎉 Hoàn thành! Development environment sẵn sàng**

**📖 Tiếp theo:**
- [Architecture Overview](./developer-guides/architecture/overview.md)
- [API Documentation](./developer-guides/api/README.md)
- [Contributing Guidelines](./developer-guides/contributing/code-standards.md)

---

## 🛒 **MARKETPLACE ADMIN QUICK START**

### **Bước 1: Hiểu Permission System (3 phút)**
1. **3 loại sản phẩm**: Digital, New Product, Used Product
2. **4 roles chính**: Guest/Member, Supplier, Manufacturer, Brand
3. **Ma trận quyền**: Ai được mua/bán gì

### **Bước 2: Marketplace Dashboard (2 phút)**
1. **Access**: `/admin/marketplace`
2. **Statistics**: Xem số liệu sản phẩm theo loại
3. **Permission Matrix**: Hiểu quyền hạn từng role

### **Bước 3: Quản lý sản phẩm (3 phút)**
1. **Product List**: `/admin/marketplace/products`
2. **Approval Workflow**: Approve/reject sản phẩm mới
3. **Product Types**: Hiểu đặc điểm từng loại

### **Bước 4: Monitor system (2 phút)**
1. **Download Analytics**: Theo dõi digital downloads
2. **Permission Logs**: Xem access attempts
3. **Performance**: Monitor marketplace performance

### **🎉 Hoàn thành! Bạn đã nắm được Marketplace v2.0**

**📖 Tiếp theo:**
- [Marketplace Overview](./marketplace/README.md)
- [Permission System](./marketplace/PERMISSION_SYSTEM.md)
- [Admin Panel Guide](./marketplace/ADMIN_PANEL.md)

---

## 🔍 **TROUBLESHOOTING**

### **❌ Vấn đề thường gặp:**

#### **User Issues:**
- **Không đăng nhập được**: Kiểm tra email verification
- **Không thấy sản phẩm**: Kiểm tra role permissions
- **Download không work**: Kiểm tra payment status

#### **Admin Issues:**
- **Không access được admin**: Kiểm tra role và permissions
- **Statistics không load**: Clear cache, check database
- **Approval không work**: Kiểm tra workflow settings

#### **Developer Issues:**
- **Migration fails**: Kiểm tra database permissions
- **Assets không load**: Run `npm run build`
- **API errors**: Kiểm tra `.env` configuration

### **📞 Cần hỗ trợ?**
- 📖 [Troubleshooting Guide](./user-guides/troubleshooting.md)
- 📧 Email: support@mechamap.vn
- 💬 Discord: [MechaMap Community](https://discord.gg/mechamap)

---

## 📚 **NEXT STEPS**

### **Sau khi hoàn thành Quick Start:**

#### **For Users:**
1. [Tham gia Forum](./user-guides/forum-guide.md)
2. [Khám phá Marketplace](./user-guides/marketplace-guide.md)
3. [Tạo Showcase](./user-guides/showcase-guide.md)

#### **For Admins:**
1. [Deep dive User Management](./admin-guides/user-management.md)
2. [Master Content Moderation](./admin-guides/content-moderation.md)
3. [Advanced System Settings](./admin-guides/system-settings.md)

#### **For Developers:**
1. [Study Architecture](./developer-guides/architecture/)
2. [Learn API](./developer-guides/api/)
3. [Write Tests](./developer-guides/testing/)

---

*Quick Start completed! Welcome to MechaMap! 🎉*
