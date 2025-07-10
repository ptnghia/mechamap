# 🛒 **MechaMap Marketplace System - Phiên bản 2.0**

## **📋 Tổng quan**

MechaMap Marketplace là hệ thống thương mại điện tử chuyên biệt cho cộng đồng kỹ sư cơ khí với **3 loại sản phẩm** và **ma trận phân quyền** rõ ràng:

### **🎯 3 Loại Sản Phẩm Chính:**

1. **🔧 Sản phẩm kỹ thuật số (digital)**
   - File thiết kế CAD (SolidWorks, AutoCAD, Fusion 360)
   - Hình ảnh kỹ thuật và bản vẽ
   - Tài liệu technical và hướng dẫn

2. **📦 Sản phẩm mới (new_product)**
   - Thiết bị cơ khí mới
   - Linh kiện và phụ tùng mới
   - Vật liệu và nguyên liệu mới

3. **♻️ Sản phẩm cũ (used_product)**
   - Thiết bị đã qua sử dụng
   - Linh kiện tái chế
   - Máy móc second-hand

### **👥 Ma Trận Phân Quyền:**

| Loại Người Dùng | Quyền Mua | Quyền Bán | Mô Tả |
|------------------|-----------|-----------|-------|
| **Cá nhân** (Guest/Member) | ✅ Digital | ✅ Digital | Chỉ được mua/bán sản phẩm kỹ thuật số |
| **Nhà cung cấp** (Supplier) | ✅ Digital | ✅ Digital + New | Có thể bán thiết bị, linh kiện mới |
| **Nhà sản xuất** (Manufacturer) | ✅ Digital + New | ✅ Digital | Mua nguyên liệu, bán file kỹ thuật |
| **Thương hiệu** (Brand) | ❌ Không | ❌ Không | Chỉ xem và liên hệ |

---

## **🏗️ Kiến trúc hệ thống mới**

### **Models chính**
```
MarketplaceProduct   # Sản phẩm marketplace (bảng chính)
├── ProductCategory  # Danh mục sản phẩm
├── ShoppingCart     # Giỏ hàng
├── Order           # Đơn hàng
├── OrderItem       # Chi tiết đơn hàng
├── PaymentTransaction # Giao dịch thanh toán
└── ProductReview   # Đánh giá sản phẩm
```

### **Services & Middleware**
```
MarketplacePermissionService      # Service quản lý phân quyền
MarketplacePermissionMiddleware   # Middleware kiểm tra quyền
```

### **Controllers**
```
Admin/MarketplaceProductController  # Quản lý sản phẩm (Admin)
MarketplaceController              # Frontend marketplace
Api/MarketplaceController          # API endpoints
```

---

## **🔧 Cài đặt và Cấu hình**

### **1. Chạy Migrations**
```bash
# Chạy tất cả migrations
php artisan migrate

# Hoặc chạy migration cụ thể cho marketplace
php artisan migrate --path=database/migrations/2025_07_09_145430_update_marketplace_products_enum_types.php
```

### **2. Seed dữ liệu mẫu**
```bash
# Tạo categories (bắt buộc chạy trước)
php artisan db:seed --class=ProductCategorySeeder

# Tạo sản phẩm mẫu với 3 loại mới
php artisan db:seed --class=NewMarketplaceProductSeeder

# Tạo dữ liệu marketplace (optional)
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

## **🔐 Hệ Thống Phân Quyền Chi Tiết**

### **Permission Service Usage:**
```php
use App\Services\MarketplacePermissionService;

// Kiểm tra quyền mua
if (MarketplacePermissionService::canBuy($user, 'digital')) {
    // User có thể mua sản phẩm kỹ thuật số
}

// Kiểm tra quyền bán
if (MarketplacePermissionService::canSell($user, 'new_product')) {
    // User có thể bán sản phẩm mới
}

// Lấy danh sách loại sản phẩm được phép bán
$allowedTypes = MarketplacePermissionService::getAllowedSellTypes($user->role);
```

### **Middleware Protection:**
```php
// Trong routes
Route::post('/marketplace/products', [ProductController::class, 'store'])
    ->middleware('marketplace.permission:sell');

Route::post('/marketplace/cart/add', [CartController::class, 'add'])
    ->middleware('marketplace.permission:buy');
```

---

## **📊 Dữ liệu hiện tại**

### **Thống kê sau khi restructure (Cập nhật 2025):**
- ✅ **MarketplaceProducts**: 78+ sản phẩm
  - 22+ sản phẩm kỹ thuật số (digital)
  - 56+ sản phẩm mới (new_product)
  - 0 sản phẩm cũ (used_product) - *Chưa có role nào được phép bán*
- ✅ **ProductCategories**: 20+ danh mục
- ✅ **ShoppingCarts**: Hỗ trợ permission-based shopping
- ✅ **Orders**: Hỗ trợ digital file downloads
- ✅ **PaymentTransactions**: Tích hợp với download system

### **Phân bố theo vai trò (Ma trận phân quyền mới):**
- **Suppliers**: Bán digital + new_product, mua digital
- **Manufacturers**: Bán digital, mua digital + new_product
- **Brands**: Chỉ xem và liên hệ (không mua/bán)
- **Members/Guests**: Mua/bán digital only

### **Admin Panel Features:**
- ✅ **Dashboard mới** với thống kê real-time
- ✅ **Permission Matrix** visualization
- ✅ **Product Management** với UI cải tiến
- ✅ **Approval Workflow** cho sản phẩm mới

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

## **🔒 Phân quyền (Cập nhật 2025)**

### **Ma trận phân quyền mới:**

| Vai trò | Mua Digital | Bán Digital | Mua New Product | Bán New Product | Mua Used Product | Bán Used Product |
|---------|-------------|-------------|-----------------|-----------------|------------------|------------------|
| **Guest** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Member** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Senior Member** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Supplier** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| **Manufacturer** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Brand** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### **Giải thích loại sản phẩm:**
- **Digital**: File CAD, hình ảnh kỹ thuật, tài liệu technical
- **New Product**: Thiết bị, linh kiện, vật liệu mới
- **Used Product**: Thiết bị, linh kiện đã qua sử dụng (chưa có role nào được phép bán)

### **Lưu ý đặc biệt:**
- **Brand**: Chỉ được xem sản phẩm và liên hệ với người bán
- **Used Product**: Hiện tại chưa có role nào được phép bán loại này
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

## **🧪 Testing & Validation**

### **Permission Testing:**
```bash
# Test permission matrix
php artisan tinker --execute="
\$supplier = \App\Models\User::where('role', 'supplier')->first();
echo 'Supplier can sell new_product: ' . (\App\Services\MarketplacePermissionService::canSell(\$supplier, 'new_product') ? 'YES' : 'NO');
"

# Test middleware protection
curl -X POST http://mechamap.test/marketplace/cart/add \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1, "quantity": 1}'
```

### **Download System Testing:**
```bash
# Test digital file access
php artisan tinker --execute="
\$order = \App\Models\MarketplaceOrder::where('payment_status', 'paid')->first();
\$digitalItems = \$order->items()->whereHas('product', function(\$q) {
    \$q->where('product_type', 'digital');
})->get();
echo 'Digital items: ' . \$digitalItems->count();
"
```

### **Admin Dashboard Testing:**
```bash
# Access admin dashboard
http://mechamap.test/admin/marketplace

# Check statistics
php artisan tinker --execute="
\$stats = [
    'total' => \App\Models\MarketplaceProduct::count(),
    'digital' => \App\Models\MarketplaceProduct::where('product_type', 'digital')->count(),
    'new' => \App\Models\MarketplaceProduct::where('product_type', 'new_product')->count(),
    'used' => \App\Models\MarketplaceProduct::where('product_type', 'used_product')->count()
];
print_r(\$stats);
"
```

---

## **🔧 Troubleshooting**

### **Common Issues:**

#### **Permission Denied Errors:**
```bash
# Check user role
php artisan tinker --execute="
\$user = \App\Models\User::find(1);
echo 'User role: ' . \$user->role;
"

# Check allowed product types
php artisan tinker --execute="
\$user = \App\Models\User::find(1);
\$allowedBuy = \App\Services\MarketplacePermissionService::getAllowedBuyTypes(\$user->role);
\$allowedSell = \App\Services\MarketplacePermissionService::getAllowedSellTypes(\$user->role);
echo 'Can buy: ' . implode(', ', \$allowedBuy) . '\n';
echo 'Can sell: ' . implode(', ', \$allowedSell) . '\n';
"
```

#### **Download Issues:**
```bash
# Check if product has digital files
php artisan tinker --execute="
\$product = \App\Models\MarketplaceProduct::find(1);
echo 'Product type: ' . \$product->product_type . '\n';
echo 'Has digital files: ' . (\$product->digital_files ? 'YES' : 'NO') . '\n';
"
```

#### **Cart Issues:**
```bash
# Clear all carts
php artisan tinker --execute="
\App\Models\MarketplaceShoppingCart::truncate();
echo 'All carts cleared';
"
```

---

## **📞 Hỗ trợ**

Để được hỗ trợ về Marketplace System:
- 📧 Email: support@mechamap.com
- 📱 Discord: MechaMap Community
- 📖 Documentation: `/docs/marketplace/`
- 🐛 Bug Reports: GitHub Issues

---

**🎉 MechaMap Marketplace - Connecting Mechanical Engineers Worldwide!**
