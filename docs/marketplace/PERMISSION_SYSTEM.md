# 🔐 MechaMap Marketplace Permission System v2.0

## 📋 Tổng Quan

Hệ thống phân quyền MechaMap Marketplace được thiết kế để kiểm soát chặt chẽ quyền mua/bán sản phẩm theo từng loại người dùng và loại sản phẩm.

### **🆕 Cập nhật 2025 - Marketplace Restructure:**
- ✅ **3 loại sản phẩm mới**: `digital`, `new_product`, `used_product`
- ✅ **Ma trận phân quyền chi tiết** theo role và product type
- ✅ **Middleware protection** cho tất cả marketplace actions
- ✅ **Service-based architecture** với `MarketplacePermissionService`

---

## 🎯 Ma Trận Phân Quyền

### **Bảng Quyền Hạn Chi Tiết:**

| Role | Mua Digital | Bán Digital | Mua New Product | Bán New Product | Mua Used Product | Bán Used Product |
|------|-------------|-------------|-----------------|-----------------|------------------|------------------|
| **Guest** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Member** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Senior Member** | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Supplier** | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| **Manufacturer** | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Brand** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### **Giải Thích Loại Sản Phẩm:**

#### **🔧 Digital Products (Sản phẩm kỹ thuật số):**
- File thiết kế CAD (SolidWorks, AutoCAD, Fusion 360)
- Hình ảnh kỹ thuật và bản vẽ
- Tài liệu technical và hướng dẫn
- **Đặc điểm**: Không giới hạn stock, có thể download sau khi mua

#### **📦 New Products (Sản phẩm mới):**
- Thiết bị cơ khí mới
- Linh kiện và phụ tùng mới
- Vật liệu và nguyên liệu mới
- **Đặc điểm**: Có stock management, shipping required

#### **♻️ Used Products (Sản phẩm cũ):**
- Thiết bị đã qua sử dụng
- Linh kiện tái chế
- Máy móc second-hand
- **Trạng thái**: Chưa có role nào được phép bán

---

## 🏗️ Architecture

### **Core Components:**

#### **1. MarketplacePermissionService**
```php
// app/Services/MarketplacePermissionService.php
class MarketplacePermissionService
{
    public static function canBuy(User $user, string $productType): bool
    public static function canSell(User $user, string $productType): bool
    public static function getAllowedBuyTypes(string $role): array
    public static function getAllowedSellTypes(string $role): array
    public static function validateProductCreation(User $user, array $productData): array
}
```

#### **2. MarketplacePermissionMiddleware**
```php
// app/Http/Middleware/MarketplacePermissionMiddleware.php
class MarketplacePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $action = 'view'): Response
    
    // Actions: 'view', 'buy', 'sell'
}
```

#### **3. Permission Matrix**
```php
private static function getPermissionMatrix(): array
{
    return [
        'guest' => [
            'buy' => [MarketplaceProduct::TYPE_DIGITAL],
            'sell' => [MarketplaceProduct::TYPE_DIGITAL],
        ],
        'member' => [
            'buy' => [MarketplaceProduct::TYPE_DIGITAL],
            'sell' => [MarketplaceProduct::TYPE_DIGITAL],
        ],
        'supplier' => [
            'buy' => [MarketplaceProduct::TYPE_DIGITAL],
            'sell' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
        ],
        'manufacturer' => [
            'buy' => [MarketplaceProduct::TYPE_DIGITAL, MarketplaceProduct::TYPE_NEW_PRODUCT],
            'sell' => [MarketplaceProduct::TYPE_DIGITAL],
        ],
        'brand' => [
            'buy' => [],
            'sell' => [],
        ],
    ];
}
```

---

## 🛡️ Middleware Protection

### **Route Protection:**
```php
// Shopping Cart - Require buy permission
Route::post('/marketplace/cart/add', [MarketplaceCartController::class, 'add'])
    ->middleware('marketplace.permission:buy');

// Checkout - Require buy permission
Route::prefix('checkout')->middleware(['auth', 'verified', 'marketplace.permission:buy'])
    ->group(function () {
        Route::get('/', [MarketplaceCheckoutController::class, 'index']);
        Route::post('/place-order', [MarketplaceCheckoutController::class, 'placeOrder']);
    });

// Product Creation - Require sell permission
Route::post('/admin/marketplace/products', [MarketplaceProductController::class, 'store'])
    ->middleware('marketplace.permission:sell');
```

### **Middleware Registration:**
```php
// bootstrap/app.php
$middleware->alias([
    'marketplace.permission' => \App\Http\Middleware\MarketplacePermissionMiddleware::class,
]);
```

---

## 💻 Usage Examples

### **1. Check Buy Permission:**
```php
use App\Services\MarketplacePermissionService;

$user = auth()->user();
$product = MarketplaceProduct::find(1);

if (MarketplacePermissionService::canBuy($user, $product->product_type)) {
    // User can buy this product type
    $cart->addProduct($product);
} else {
    // Permission denied
    return response()->json([
        'error' => 'Bạn không có quyền mua loại sản phẩm này'
    ], 403);
}
```

### **2. Check Sell Permission:**
```php
$user = auth()->user();
$productType = 'new_product';

if (MarketplacePermissionService::canSell($user, $productType)) {
    // User can sell this product type
    $product = MarketplaceProduct::create($productData);
} else {
    // Permission denied
    $allowedTypes = MarketplacePermissionService::getAllowedSellTypes($user->role);
    return back()->withErrors([
        'product_type' => 'Bạn chỉ có thể bán: ' . implode(', ', $allowedTypes)
    ]);
}
```

### **3. Validate Product Creation:**
```php
$user = auth()->user();
$productData = $request->validated();

$errors = MarketplacePermissionService::validateProductCreation($user, $productData);

if (!empty($errors)) {
    return back()->withErrors($errors);
}

// Proceed with product creation
```

### **4. Get Allowed Product Types:**
```php
$user = auth()->user();

$allowedBuyTypes = MarketplacePermissionService::getAllowedBuyTypes($user->role);
$allowedSellTypes = MarketplacePermissionService::getAllowedSellTypes($user->role);

return view('marketplace.dashboard', compact('allowedBuyTypes', 'allowedSellTypes'));
```

---

## 🎨 Frontend Integration

### **Conditional UI Display:**
```blade
{{-- Show buy button only if user can buy this product type --}}
@auth
    @if(App\Services\MarketplacePermissionService::canBuy(auth()->user(), $product->product_type))
        <button class="btn btn-primary" onclick="addToCart({{ $product->id }})">
            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
        </button>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Bạn không có quyền mua loại sản phẩm này
        </div>
    @endif
@else
    <a href="{{ route('login') }}" class="btn btn-outline-primary">
        Đăng nhập để mua hàng
    </a>
@endauth
```

### **Product Creation Form:**
```blade
{{-- Show product type options based on user permissions --}}
@auth
    @php
        $allowedSellTypes = App\Services\MarketplacePermissionService::getAllowedSellTypes(auth()->user()->role);
        $productTypes = App\Models\MarketplaceProduct::getProductTypes();
    @endphp
    
    <select name="product_type" class="form-select" required>
        <option value="">Chọn loại sản phẩm</option>
        @foreach($productTypes as $type => $label)
            @if(in_array($type, $allowedSellTypes))
                <option value="{{ $type }}">{{ $label }}</option>
            @endif
        @endforeach
    </select>
@endauth
```

---

## 🧪 Testing

### **Permission Testing:**
```php
// Test supplier permissions
$supplier = User::factory()->create(['role' => 'supplier']);

// Should be able to buy digital
$this->assertTrue(MarketplacePermissionService::canBuy($supplier, 'digital'));

// Should be able to sell digital and new_product
$this->assertTrue(MarketplacePermissionService::canSell($supplier, 'digital'));
$this->assertTrue(MarketplacePermissionService::canSell($supplier, 'new_product'));

// Should NOT be able to sell used_product
$this->assertFalse(MarketplacePermissionService::canSell($supplier, 'used_product'));
```

### **Middleware Testing:**
```php
// Test cart add with permission
$response = $this->actingAs($supplier)
    ->postJson('/marketplace/cart/add', [
        'product_id' => $digitalProduct->id,
        'quantity' => 1
    ]);

$response->assertStatus(200);

// Test cart add without permission
$response = $this->actingAs($brand)
    ->postJson('/marketplace/cart/add', [
        'product_id' => $digitalProduct->id,
        'quantity' => 1
    ]);

$response->assertStatus(403);
```

---

## 🔧 Configuration

### **Environment Variables:**
```env
# Marketplace permissions
MARKETPLACE_GUEST_CAN_BUY=true
MARKETPLACE_GUEST_CAN_SELL=true
MARKETPLACE_STRICT_PERMISSIONS=true
```

### **Config File:**
```php
// config/marketplace.php
return [
    'permissions' => [
        'strict_mode' => env('MARKETPLACE_STRICT_PERMISSIONS', true),
        'guest_permissions' => [
            'can_buy' => env('MARKETPLACE_GUEST_CAN_BUY', true),
            'can_sell' => env('MARKETPLACE_GUEST_CAN_SELL', true),
        ],
    ],
];
```

---

## 🚨 Error Handling

### **Permission Error Messages:**
```php
private function getPermissionMessage(string $role, string $action, ?string $productType): string
{
    $messages = [
        'guest' => [
            'buy' => 'Khách vãng lai chỉ có thể mua sản phẩm kỹ thuật số',
            'sell' => 'Khách vãng lai chỉ có thể bán sản phẩm kỹ thuật số',
        ],
        'supplier' => [
            'buy' => 'Nhà cung cấp chỉ có thể mua sản phẩm kỹ thuật số',
            'sell' => 'Nhà cung cấp có thể bán sản phẩm kỹ thuật số và sản phẩm mới',
        ],
        'brand' => [
            'buy' => 'Thương hiệu không được phép mua sản phẩm',
            'sell' => 'Thương hiệu không được phép bán sản phẩm',
        ],
    ];

    return $messages[$role][$action] ?? 'Bạn không có quyền thực hiện hành động này';
}
```

### **HTTP Status Codes:**
- **401 Unauthorized** - User chưa đăng nhập
- **403 Forbidden** - User không có quyền
- **422 Unprocessable Entity** - Validation errors

---

## 📊 Monitoring & Analytics

### **Permission Usage Tracking:**
```php
// Log permission checks for analytics
Log::info('Permission check', [
    'user_id' => $user->id,
    'role' => $user->role,
    'action' => $action,
    'product_type' => $productType,
    'result' => $hasPermission ? 'allowed' : 'denied',
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### **Dashboard Metrics:**
- Permission denials by role
- Most restricted product types
- User role distribution
- Permission usage trends

---

*Cập nhật lần cuối: 2025-01-09 - Permission System v2.0*
