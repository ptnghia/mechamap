# 🎛️ MechaMap Marketplace Admin Panel v2.0

## 📋 Tổng Quan

Admin Panel cho MechaMap Marketplace được thiết kế để quản lý toàn bộ hệ thống marketplace với giao diện trực quan và tính năng phân quyền chặt chẽ.

### **🆕 Cập nhật 2025 - Marketplace Restructure:**
- ✅ **Dashboard mới** với thống kê real-time cho 3 loại sản phẩm
- ✅ **Permission Matrix Visualization** - Hiển thị ma trận phân quyền trực quan
- ✅ **Enhanced Product Management** - UI cải tiến cho quản lý sản phẩm
- ✅ **Approval Workflow** - Quy trình phê duyệt sản phẩm tối ưu

---

## 🏗️ Cấu Trúc Admin Panel

### **Dashboard Chính**
- **URL**: `/admin/marketplace`
- **Controller**: `MarketplaceDashboardController`
- **View**: `admin.marketplace.dashboard`

#### **Statistics Cards:**
```php
// Thống kê theo loại sản phẩm
$stats = [
    'total_products' => MarketplaceProduct::count(),
    'digital_products' => MarketplaceProduct::where('product_type', 'digital')->count(),
    'new_products' => MarketplaceProduct::where('product_type', 'new_product')->count(),
    'used_products' => MarketplaceProduct::where('product_type', 'used_product')->count(),
];
```

#### **Charts & Visualizations:**
- 🍩 **Doughnut Chart** - Phân bố sản phẩm theo loại
- 📊 **Bar Chart** - Sản phẩm theo seller type
- 📈 **Line Chart** - Xu hướng tạo sản phẩm theo thời gian

---

## 🔐 Permission Matrix Component

### **Hiển thị Ma Trận Phân Quyền:**
```php
// Component: admin.marketplace.components.permission-matrix
@include('admin.marketplace.components.permission-matrix')
```

#### **Ma Trận Quyền Hạn:**
| Role | Mua Digital | Bán Digital | Mua New | Bán New | Mua Used | Bán Used |
|------|-------------|-------------|---------|---------|----------|----------|
| **Guest** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Member** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Supplier** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| **Manufacturer** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Brand** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 📦 Product Management

### **Enhanced Product List:**
- **URL**: `/admin/marketplace/products`
- **Features**:
  - ✅ Hiển thị loại sản phẩm bằng tiếng Việt với màu sắc
  - ✅ Cột "Thông Tin Đặc Biệt" cho từng loại sản phẩm
  - ✅ Stock management phù hợp với từng loại
  - ✅ Bulk actions (approve/reject)

#### **Product Type Display:**
```php
$typeLabels = [
    'digital' => 'Kỹ thuật số',
    'new_product' => 'Sản phẩm mới', 
    'used_product' => 'Sản phẩm cũ'
];

$typeColors = [
    'digital' => 'bg-primary',
    'new_product' => 'bg-success',
    'used_product' => 'bg-warning'
];
```

#### **Special Information Column:**
- **Digital Products**: Số lượng files, formats
- **New Products**: Vật liệu, quy trình sản xuất
- **Used Products**: Trạng thái đã sử dụng

### **Product Creation Form:**
- **URL**: `/admin/marketplace/products/create`
- **Features**:
  - ✅ Dynamic sections theo loại sản phẩm
  - ✅ JavaScript toggle cho digital files section
  - ✅ Validation rules phù hợp với từng loại
  - ✅ File upload cho digital products

---

## 📊 Statistics & Analytics

### **Real-time Statistics:**
```php
// Dashboard statistics
$stats = [
    // Product counts
    'total_products' => $totalProducts,
    'digital_products' => $digitalProducts,
    'new_products' => $newProducts,
    'used_products' => $usedProducts,
    
    // Seller type counts
    'supplier_products' => $supplierProducts,
    'manufacturer_products' => $manufacturerProducts,
    'brand_products' => $brandProducts,
    
    // Status counts
    'pending_products' => $pendingProducts,
    'approved_products' => $approvedProducts,
    'rejected_products' => $rejectedProducts,
];
```

### **Charts Implementation:**
```javascript
// Product Type Chart (Doughnut)
new Chart(productTypeCtx, {
    type: 'doughnut',
    data: {
        labels: ['Kỹ thuật số', 'Sản phẩm mới', 'Sản phẩm cũ'],
        datasets: [{
            data: [digitalCount, newCount, usedCount],
            backgroundColor: ['#556ee6', '#34c38f', '#f1b44c']
        }]
    }
});

// Seller Type Chart (Bar)
new Chart(sellerTypeCtx, {
    type: 'bar',
    data: {
        labels: ['Supplier', 'Manufacturer', 'Brand'],
        datasets: [{
            data: [supplierCount, manufacturerCount, brandCount],
            backgroundColor: ['#556ee6', '#34c38f', '#f1b44c']
        }]
    }
});
```

---

## ✅ Approval Workflow

### **Product Approval Process:**
1. **Pending Review** - Sản phẩm chờ duyệt
2. **Admin Review** - Admin kiểm tra và phê duyệt
3. **Approved/Rejected** - Kết quả phê duyệt

#### **Approval Statistics:**
```php
$approvalStats = [
    'pending_count' => MarketplaceProduct::where('status', 'pending')->count(),
    'approved_today' => MarketplaceProduct::where('status', 'approved')
        ->whereDate('approved_at', today())->count(),
    'rejected_today' => MarketplaceProduct::where('status', 'rejected')
        ->whereDate('updated_at', today())->count(),
    'avg_approval_time' => $this->getAverageApprovalTime(),
];
```

### **Bulk Actions:**
- ✅ **Bulk Approve** - Phê duyệt hàng loạt
- ✅ **Bulk Reject** - Từ chối hàng loạt
- ✅ **Toggle Featured** - Đánh dấu nổi bật

---

## 🛠️ Admin Routes

### **Main Routes:**
```php
// Dashboard
Route::get('/admin/marketplace', [MarketplaceDashboardController::class, 'index']);

// Products
Route::resource('/admin/marketplace/products', MarketplaceProductController::class);

// Approval actions
Route::post('/admin/marketplace/products/{product}/approve', 'approve');
Route::post('/admin/marketplace/products/{product}/reject', 'reject');
Route::post('/admin/marketplace/products/bulk-approve', 'bulkApprove');
Route::post('/admin/marketplace/products/bulk-reject', 'bulkReject');

// Statistics
Route::get('/admin/marketplace/approval-stats', 'approvalStats');
Route::post('/admin/marketplace/export-products', 'exportProducts');
```

---

## 🎨 UI Components

### **Permission Matrix Component:**
```blade
{{-- resources/views/admin/marketplace/components/permission-matrix.blade.php --}}
<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <i class="fas fa-shield-alt me-2"></i>
            Ma Trận Phân Quyền Marketplace
        </h4>
    </div>
    <div class="card-body">
        {{-- Permission matrix table --}}
    </div>
</div>
```

### **Statistics Cards:**
```blade
{{-- Statistics cards with real-time data --}}
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Tổng Sản Phẩm</p>
                        <h4 class="mb-0">{{ $stats['total_products'] }}</h4>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                            <span class="avatar-title">
                                <i class="bx bx-package font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- More cards... --}}
</div>
```

---

## 🔧 Configuration

### **Admin Permissions:**
```php
// Required permissions for admin access
'admin.permission:view_products'    // View products
'admin.permission:manage_products'  // Create/Edit products
'admin.permission:approve_products' // Approve/Reject products
'admin.permission:view_orders'      // View orders
'admin.permission:manage_orders'    // Manage orders
```

### **Middleware Stack:**
```php
Route::middleware(['admin.auth', 'admin.permission:view_products'])->group(function () {
    // Admin marketplace routes
});
```

---

## 📱 Responsive Design

### **Mobile Optimization:**
- ✅ **Responsive tables** với horizontal scroll
- ✅ **Collapsible cards** cho mobile
- ✅ **Touch-friendly buttons** và controls
- ✅ **Optimized charts** cho small screens

### **Browser Support:**
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+

---

## 🚀 Performance

### **Optimization Features:**
- ✅ **Lazy loading** cho large product lists
- ✅ **Pagination** với configurable page sizes
- ✅ **Caching** cho statistics data
- ✅ **Optimized queries** với eager loading

### **Monitoring:**
- ✅ **Query logging** cho performance analysis
- ✅ **Error tracking** với detailed logs
- ✅ **User activity tracking** cho audit

---

*Cập nhật lần cuối: 2025-01-09 - Admin Panel v2.0*
