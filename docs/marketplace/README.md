# 🛒 **MechaMap Marketplace System**

## **📋 Tổng quan**

MechaMap Marketplace là hệ thống thương mại điện tử chuyên biệt cho cộng đồng kỹ sư cơ khí, cho phép:

- **Suppliers (Nhà cung cấp)**: Bán sản phẩm vật lý (linh kiện, vật liệu, thiết bị)
- **Manufacturers (Nhà sản xuất)**: Bán thông tin kỹ thuật, thiết kế, file CAD
- **Brands (Thương hiệu)**: Trưng bày sản phẩm để quảng bá (chỉ xem)

---

## **🏗️ Kiến trúc hệ thống**

### **Models chính**
```
TechnicalProduct     # Sản phẩm kỹ thuật
├── ProductCategory  # Danh mục sản phẩm
├── ShoppingCart     # Giỏ hàng
├── Order           # Đơn hàng
├── OrderItem       # Chi tiết đơn hàng
├── PaymentTransaction # Giao dịch thanh toán
└── ProductReview   # Đánh giá sản phẩm
```

### **Controllers API**
```
Api/ProductController        # Quản lý sản phẩm
Api/ShoppingCartController   # Quản lý giỏ hàng
Api/OrderController         # Quản lý đơn hàng
Api/PaymentController       # Xử lý thanh toán
```

---

## **🔧 Cài đặt và Cấu hình**

### **1. Chạy Migrations**
```bash
php artisan migrate
```

### **2. Seed dữ liệu mẫu**
```bash
# Tạo categories
php artisan db:seed --class=ProductCategorySeeder

# Tạo sản phẩm mẫu
php artisan db:seed --class=SampleProductSeeder

# Tạo dữ liệu marketplace
php artisan db:seed --class=MarketplaceDataSeeder
```

### **3. Cấu hình Payment Gateways**
```env
# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# VNPay
VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
```

---

## **📊 Dữ liệu hiện tại**

### **Thống kê sau khi seed:**
- ✅ **TechnicalProducts**: 13 sản phẩm
- ✅ **ProductCategories**: 20 danh mục
- ✅ **ShoppingCarts**: 71 giỏ hàng
- ✅ **Orders**: 48 đơn hàng
- ✅ **OrderItems**: 73 chi tiết đơn hàng
- ✅ **PaymentTransactions**: 42 giao dịch

### **Phân bố theo vai trò:**
- **Suppliers**: 5 users → Sản phẩm vật lý
- **Manufacturers**: 4 users → File CAD, thiết kế
- **Brands**: 4 users → Sản phẩm trưng bày

---

## **🚀 API Endpoints**

### **Public Endpoints (Không cần auth)**
```http
GET /api/v1/marketplace/v2/products              # Danh sách sản phẩm
GET /api/v1/marketplace/v2/products/{id}         # Chi tiết sản phẩm
GET /api/v1/marketplace/v2/products/seller-type/{type} # Sản phẩm theo loại seller
```

### **Protected Endpoints (Cần auth)**
```http
# Product Management (Business users only)
POST   /api/v1/marketplace/v2/products           # Tạo sản phẩm mới
PUT    /api/v1/marketplace/v2/products/{id}      # Cập nhật sản phẩm
DELETE /api/v1/marketplace/v2/products/{id}      # Xóa sản phẩm

# Shopping Cart
GET    /api/v1/marketplace/v2/cart               # Xem giỏ hàng
POST   /api/v1/marketplace/v2/cart               # Thêm vào giỏ hàng
PUT    /api/v1/marketplace/v2/cart/{id}          # Cập nhật số lượng
DELETE /api/v1/marketplace/v2/cart/{id}          # Xóa khỏi giỏ hàng
DELETE /api/v1/marketplace/v2/cart               # Xóa toàn bộ giỏ hàng
GET    /api/v1/marketplace/v2/cart/count         # Số lượng items trong giỏ

# Seller Dashboard (Business users only)
GET    /api/v1/marketplace/v2/seller/dashboard   # Dashboard tổng quan
GET    /api/v1/marketplace/v2/seller/products    # Sản phẩm của seller
GET    /api/v1/marketplace/v2/seller/orders      # Đơn hàng của seller
GET    /api/v1/marketplace/v2/seller/earnings    # Doanh thu của seller
```

---

## **💡 Ví dụ sử dụng API**

### **1. Lấy danh sách sản phẩm**
```javascript
// GET /api/v1/marketplace/v2/products
const response = await fetch('/api/v1/marketplace/v2/products?seller_type=manufacturer&sort_by=price');
const data = await response.json();

console.log(data.data.data); // Array of products
```

### **2. Thêm sản phẩm vào giỏ hàng**
```javascript
// POST /api/v1/marketplace/v2/cart
const response = await fetch('/api/v1/marketplace/v2/cart', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        technical_product_id: 5,
        license_type: 'standard',
        quantity: 1
    })
});
```

### **3. Tạo sản phẩm mới (Business users)**
```javascript
// POST /api/v1/marketplace/v2/products
const response = await fetch('/api/v1/marketplace/v2/products', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({
        title: 'CAD Library - Mechanical Components',
        description: 'Thư viện CAD hoàn chỉnh...',
        price: 1500000,
        category_id: 1,
        file_formats: ['DWG', 'STEP', 'IGES'],
        software_compatibility: 'AutoCAD, SolidWorks',
        complexity_level: 'intermediate'
    })
});
```

---

## **🔒 Phân quyền**

### **Vai trò và quyền hạn:**

| Vai trò | Xem sản phẩm | Mua sản phẩm | Tạo sản phẩm | Quản lý đơn hàng |
|---------|--------------|--------------|--------------|------------------|
| **Guest** | ✅ | ❌ | ❌ | ❌ |
| **Member** | ✅ | ✅ | ❌ | ✅ |
| **Supplier** | ✅ | ✅ | ✅ (Vật lý) | ✅ |
| **Manufacturer** | ✅ | ✅ | ✅ (Kỹ thuật) | ✅ |
| **Brand** | ✅ | ❌ | ✅ (Trưng bày) | ❌ |
| **Admin** | ✅ | ✅ | ✅ | ✅ |

---

## **📈 Tính năng đã hoàn thành**

- ✅ **Product Management**: CRUD sản phẩm cho business users
- ✅ **Shopping Cart**: Thêm, sửa, xóa sản phẩm trong giỏ hàng
- ✅ **Order System**: Tạo và quản lý đơn hàng
- ✅ **Payment Integration**: Stripe và VNPay
- ✅ **User Roles**: Phân quyền theo vai trò
- ✅ **API Documentation**: Endpoints đầy đủ
- ✅ **Database Seeding**: Dữ liệu mẫu hoàn chỉnh

---

## **🚧 Tính năng đang phát triển**

- 🔄 **Product Reviews**: Hệ thống đánh giá sản phẩm
- 🔄 **Advanced Search**: Tìm kiếm nâng cao với filters
- 🔄 **Seller Analytics**: Thống kê chi tiết cho sellers
- 🔄 **Inventory Management**: Quản lý tồn kho
- 🔄 **Discount System**: Mã giảm giá và khuyến mãi
- 🔄 **Wishlist**: Danh sách yêu thích
- 🔄 **Product Comparison**: So sánh sản phẩm

---

## **🎯 Roadmap**

### **Phase 1** (Hoàn thành)
- ✅ Basic marketplace structure
- ✅ Product CRUD operations
- ✅ Shopping cart functionality
- ✅ Order management
- ✅ Payment processing

### **Phase 2** (Đang phát triển)
- 🔄 Advanced product features
- 🔄 Seller dashboard enhancements
- 🔄 Customer reviews system
- 🔄 Analytics and reporting

### **Phase 3** (Kế hoạch)
- 📋 Mobile app integration
- 📋 Advanced recommendation engine
- 📋 Multi-language support
- 📋 International payment methods

---

## **📞 Hỗ trợ**

Để được hỗ trợ về Marketplace System:
- 📧 Email: support@mechamap.com
- 📱 Discord: MechaMap Community
- 📖 Documentation: `/docs/marketplace/`
- 🐛 Bug Reports: GitHub Issues

---

**🎉 MechaMap Marketplace - Connecting Mechanical Engineers Worldwide!**
