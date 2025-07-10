# 🧪 MechaMap Marketplace Testing Guide v2.0

## 📋 Tổng Quan

Hướng dẫn testing toàn diện cho MechaMap Marketplace v2.0 với focus vào permission system, 3 loại sản phẩm mới, và download system.

### **🆕 Cập nhật 2025 - Testing cho Marketplace Restructure:**
- ✅ **Permission Matrix Testing** - Test ma trận phân quyền chi tiết
- ✅ **Product Type Testing** - Test 3 loại sản phẩm mới
- ✅ **Middleware Testing** - Test bảo vệ routes
- ✅ **Download System Testing** - Test secure download cho digital products

---

## 🎯 Testing Strategy

### **1. Unit Tests**
- Permission Service methods
- Model relationships và validations
- Helper functions

### **2. Feature Tests**
- Cart functionality với permission checks
- Checkout process với role validation
- Product creation với type restrictions
- Download system với security checks

### **3. Integration Tests**
- End-to-end user journeys
- Admin panel workflows
- API endpoints với authentication

### **4. Browser Tests**
- UI interactions
- JavaScript functionality
- Responsive design

---

## 🔐 Permission Testing

### **Test Permission Matrix:**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Services\MarketplacePermissionService;

class PermissionMatrixTest extends TestCase
{
    /** @test */
    public function guest_can_buy_and_sell_digital_only()
    {
        $guest = User::factory()->create(['role' => 'guest']);
        
        // Can buy/sell digital
        $this->assertTrue(MarketplacePermissionService::canBuy($guest, 'digital'));
        $this->assertTrue(MarketplacePermissionService::canSell($guest, 'digital'));
        
        // Cannot buy/sell new_product
        $this->assertFalse(MarketplacePermissionService::canBuy($guest, 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell($guest, 'new_product'));
        
        // Cannot buy/sell used_product
        $this->assertFalse(MarketplacePermissionService::canBuy($guest, 'used_product'));
        $this->assertFalse(MarketplacePermissionService::canSell($guest, 'used_product'));
    }
    
    /** @test */
    public function supplier_permissions_are_correct()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        // Can buy digital only
        $this->assertTrue(MarketplacePermissionService::canBuy($supplier, 'digital'));
        $this->assertFalse(MarketplacePermissionService::canBuy($supplier, 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy($supplier, 'used_product'));
        
        // Can sell digital and new_product
        $this->assertTrue(MarketplacePermissionService::canSell($supplier, 'digital'));
        $this->assertTrue(MarketplacePermissionService::canSell($supplier, 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell($supplier, 'used_product'));
    }
    
    /** @test */
    public function manufacturer_permissions_are_correct()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        
        // Can buy digital and new_product
        $this->assertTrue(MarketplacePermissionService::canBuy($manufacturer, 'digital'));
        $this->assertTrue(MarketplacePermissionService::canBuy($manufacturer, 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canBuy($manufacturer, 'used_product'));
        
        // Can sell digital only
        $this->assertTrue(MarketplacePermissionService::canSell($manufacturer, 'digital'));
        $this->assertFalse(MarketplacePermissionService::canSell($manufacturer, 'new_product'));
        $this->assertFalse(MarketplacePermissionService::canSell($manufacturer, 'used_product'));
    }
    
    /** @test */
    public function brand_cannot_buy_or_sell_anything()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        $productTypes = ['digital', 'new_product', 'used_product'];
        
        foreach ($productTypes as $type) {
            $this->assertFalse(MarketplacePermissionService::canBuy($brand, $type));
            $this->assertFalse(MarketplacePermissionService::canSell($brand, $type));
        }
    }
}
```

---

## 🛒 Cart & Checkout Testing

### **Test Cart with Permissions:**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;

class CartPermissionTest extends TestCase
{
    /** @test */
    public function user_can_add_allowed_product_to_cart()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $digitalProduct = MarketplaceProduct::factory()->create([
            'product_type' => 'digital',
            'status' => 'approved',
            'is_active' => true
        ]);
        
        $response = $this->actingAs($supplier)
            ->postJson('/marketplace/cart/add', [
                'product_id' => $digitalProduct->id,
                'quantity' => 1
            ]);
            
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
    
    /** @test */
    public function user_cannot_add_restricted_product_to_cart()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        $digitalProduct = MarketplaceProduct::factory()->create([
            'product_type' => 'digital',
            'status' => 'approved',
            'is_active' => true
        ]);
        
        $response = $this->actingAs($brand)
            ->postJson('/marketplace/cart/add', [
                'product_id' => $digitalProduct->id,
                'quantity' => 1
            ]);
            
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Bạn không có quyền mua loại sản phẩm này'
        ]);
    }
    
    /** @test */
    public function checkout_requires_buy_permission()
    {
        $brand = User::factory()->create(['role' => 'brand']);
        
        $response = $this->actingAs($brand)
            ->get('/marketplace/checkout');
            
        $response->assertStatus(403);
    }
}
```

---

## 📦 Product Management Testing

### **Test Product Creation:**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ProductCategory;

class ProductCreationTest extends TestCase
{
    /** @test */
    public function supplier_can_create_digital_and_new_products()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        $category = ProductCategory::factory()->create();
        
        // Can create digital product
        $response = $this->actingAs($supplier)
            ->post('/admin/marketplace/products', [
                'name' => 'Test Digital Product',
                'description' => 'Test description',
                'product_type' => 'digital',
                'seller_type' => 'supplier',
                'price' => 100000,
                'product_category_id' => $category->id,
                'digital_files' => ['test.dwg']
            ]);
            
        $response->assertRedirect();
        $this->assertDatabaseHas('marketplace_products', [
            'name' => 'Test Digital Product',
            'product_type' => 'digital'
        ]);
        
        // Can create new product
        $response = $this->actingAs($supplier)
            ->post('/admin/marketplace/products', [
                'name' => 'Test New Product',
                'description' => 'Test description',
                'product_type' => 'new_product',
                'seller_type' => 'supplier',
                'price' => 500000,
                'product_category_id' => $category->id,
                'stock_quantity' => 10
            ]);
            
        $response->assertRedirect();
        $this->assertDatabaseHas('marketplace_products', [
            'name' => 'Test New Product',
            'product_type' => 'new_product'
        ]);
    }
    
    /** @test */
    public function manufacturer_cannot_create_new_products()
    {
        $manufacturer = User::factory()->create(['role' => 'manufacturer']);
        $category = ProductCategory::factory()->create();
        
        $response = $this->actingAs($manufacturer)
            ->post('/admin/marketplace/products', [
                'name' => 'Test New Product',
                'description' => 'Test description',
                'product_type' => 'new_product',
                'seller_type' => 'manufacturer',
                'price' => 500000,
                'product_category_id' => $category->id,
                'stock_quantity' => 10
            ]);
            
        $response->assertStatus(403);
    }
}
```

---

## 🔒 Download System Testing

### **Test Digital File Downloads:**
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;

class DownloadSystemTest extends TestCase
{
    /** @test */
    public function user_can_download_paid_digital_products()
    {
        $user = User::factory()->create(['role' => 'member']);
        $digitalProduct = MarketplaceProduct::factory()->create([
            'product_type' => 'digital',
            'digital_files' => ['test.dwg', 'test.pdf']
        ]);
        
        $order = MarketplaceOrder::factory()->create([
            'customer_id' => $user->id,
            'payment_status' => 'paid'
        ]);
        
        $orderItem = MarketplaceOrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $digitalProduct->id
        ]);
        
        $response = $this->actingAs($user)
            ->get("/marketplace/downloads/items/{$orderItem->id}/files");
            
        $response->assertStatus(200);
        $response->assertViewIs('marketplace.downloads.item-files');
    }
    
    /** @test */
    public function user_cannot_download_unpaid_products()
    {
        $user = User::factory()->create(['role' => 'member']);
        $digitalProduct = MarketplaceProduct::factory()->create([
            'product_type' => 'digital',
            'digital_files' => ['test.dwg']
        ]);
        
        $order = MarketplaceOrder::factory()->create([
            'customer_id' => $user->id,
            'payment_status' => 'pending'
        ]);
        
        $orderItem = MarketplaceOrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $digitalProduct->id
        ]);
        
        $response = $this->actingAs($user)
            ->get("/marketplace/downloads/items/{$orderItem->id}/files");
            
        $response->assertStatus(403);
    }
    
    /** @test */
    public function only_digital_products_have_downloads()
    {
        $user = User::factory()->create(['role' => 'member']);
        $physicalProduct = MarketplaceProduct::factory()->create([
            'product_type' => 'new_product'
        ]);
        
        $order = MarketplaceOrder::factory()->create([
            'customer_id' => $user->id,
            'payment_status' => 'paid'
        ]);
        
        $response = $this->actingAs($user)
            ->get("/marketplace/downloads/orders/{$order->id}/files");
            
        $response->assertStatus(200);
        $response->assertViewHas('digitalItems');
        
        $digitalItems = $response->viewData('digitalItems');
        $this->assertCount(0, $digitalItems);
    }
}
```

---

## 🌐 Browser Testing

### **Test UI Interactions:**
```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\MarketplaceProduct;

class MarketplaceBrowserTest extends DuskTestCase
{
    /** @test */
    public function user_can_navigate_marketplace()
    {
        $user = User::factory()->create(['role' => 'member']);
        
        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/marketplace')
                    ->assertSee('Marketplace')
                    ->clickLink('Sản phẩm')
                    ->assertPathIs('/marketplace/products')
                    ->assertSee('Danh sách sản phẩm');
        });
    }
    
    /** @test */
    public function admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/marketplace')
                    ->assertSee('Marketplace Dashboard')
                    ->assertSee('Ma Trận Phân Quyền')
                    ->assertSee('Thống kê sản phẩm');
        });
    }
    
    /** @test */
    public function product_creation_form_toggles_sections()
    {
        $supplier = User::factory()->create(['role' => 'supplier']);
        
        $this->browse(function ($browser) use ($supplier) {
            $browser->loginAs($supplier)
                    ->visit('/admin/marketplace/products/create')
                    ->select('product_type', 'digital')
                    ->assertVisible('#digitalFilesSection')
                    ->assertNotVisible('#stockSection')
                    ->select('product_type', 'new_product')
                    ->assertNotVisible('#digitalFilesSection')
                    ->assertVisible('#stockSection');
        });
    }
}
```

---

## 📊 Performance Testing

### **Load Testing:**
```bash
# Test marketplace endpoints under load
ab -n 1000 -c 10 http://mechamap.test/marketplace/products

# Test cart operations
ab -n 500 -c 5 -p cart_data.json -T application/json http://mechamap.test/marketplace/cart/add

# Test download system
ab -n 100 -c 2 http://mechamap.test/marketplace/downloads
```

### **Database Performance:**
```php
// Test query performance
$this->assertQueryCount(5, function () {
    $products = MarketplaceProduct::with(['seller', 'category'])
        ->where('product_type', 'digital')
        ->paginate(20);
});
```

---

## 🚀 Running Tests

### **All Tests:**
```bash
# Run all marketplace tests
php artisan test --testsuite=Feature --filter=Marketplace

# Run permission tests only
php artisan test tests/Feature/PermissionMatrixTest.php

# Run with coverage
php artisan test --coverage --min=80
```

### **Browser Tests:**
```bash
# Run Dusk tests
php artisan dusk --filter=MarketplaceBrowserTest

# Run with specific browser
php artisan dusk --env=testing.chrome
```

### **Continuous Integration:**
```yaml
# .github/workflows/marketplace-tests.yml
name: Marketplace Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test --filter=Marketplace
```

---

## 📋 Test Checklist

### **Permission System:**
- [ ] All role permissions work correctly
- [ ] Middleware blocks unauthorized access
- [ ] Error messages are user-friendly
- [ ] Permission changes take effect immediately

### **Product Management:**
- [ ] 3 product types create correctly
- [ ] Validation works for each type
- [ ] Admin UI shows correct information
- [ ] Bulk actions work properly

### **Cart & Checkout:**
- [ ] Permission checks work in cart
- [ ] Checkout blocks unauthorized users
- [ ] Payment integration works
- [ ] Order creation is successful

### **Download System:**
- [ ] Only digital products have downloads
- [ ] Payment verification works
- [ ] File security is maintained
- [ ] Download tracking is accurate

### **Admin Panel:**
- [ ] Dashboard loads correctly
- [ ] Statistics are accurate
- [ ] Permission matrix displays properly
- [ ] Charts render correctly

---

*Cập nhật lần cuối: 2025-01-09 - Testing Guide v2.0*
