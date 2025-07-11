# 🧪 MechaMap Testing Environment

**Setup Date:** 2025-07-12  
**Purpose:** Testing environment cho User Registration & Permission System Overhaul  
**Status:** ✅ Ready for Phase 1 Development

---

## 📋 **TESTING SETUP OVERVIEW**

### **Environment Configuration**
- **Testing Framework:** PHPUnit 10.x
- **Database:** SQLite in-memory (`:memory:`)
- **Cache Driver:** Array (no persistence)
- **Mail Driver:** Array (no actual emails sent)
- **Queue Driver:** Sync (immediate processing)
- **Session Driver:** Array (no persistence)

### **Key Features**
- ✅ **Enhanced TestCase** với helper methods
- ✅ **User Factory** với business user states
- ✅ **Test Data Seeder** cho comprehensive test data
- ✅ **Custom Assertions** cho business logic
- ✅ **Backup Integration** với existing backup system

---

## 🏗️ **TESTING ARCHITECTURE**

### **TestCase Enhancements**
Located: `tests/TestCase.php`

**Helper Methods:**
```php
// Basic user creation
$user = $this->createUser('member');
$admin = $this->createAdmin('super_admin');

// Business user creation
$businessUser = $this->createBusinessUser('manufacturer');
$verifiedUser = $this->createVerifiedBusinessUser('supplier');

// Custom assertions
$this->assertUserHasRole($user, 'member');
$this->assertBusinessUserIsVerified($verifiedUser);
$this->assertUserCanAccessMarketplace($user);
```

### **User Factory States**
Located: `database/factories/UserFactory.php`

**Available States:**
```php
// Basic states
User::factory()->create();                    // Default member
User::factory()->unverified()->create();      // Unverified email
User::factory()->admin('super_admin')->create();
User::factory()->moderator('content_moderator')->create();
User::factory()->member('student')->create();

// Business states
User::factory()->business('manufacturer')->create();           // Unverified business
User::factory()->verifiedBusiness('supplier')->create();       // Verified business
```

### **Test Data Seeder**
Located: `database/seeders/TestDataSeeder.php`

**Seeded Data:**
- **14 Test Users** covering all roles
- **2 Categories** và **2 Forums**
- **2 Threads** và **2 Showcases**
- **Business Information** cho business users
- **Verified Business Users** với complete verification data

---

## 🚀 **RUNNING TESTS**

### **All Tests**
```bash
php artisan test
```

### **Specific Test Suites**
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only  
php artisan test --testsuite=Feature

# Specific test file
php artisan test tests/Unit/TestEnvironmentSetupTest.php

# Specific test method
php artisan test --filter=test_user_factory_basic
```

### **Test Coverage**
```bash
# Generate coverage report (requires Xdebug)
php artisan test --coverage

# Coverage with HTML report
php artisan test --coverage-html=coverage-report
```

---

## 📊 **TEST CATEGORIES**

### **Unit Tests** (`tests/Unit/`)
- **TestEnvironmentSetupTest** - Verify testing setup
- **UserModelTest** - User model functionality
- **PermissionServiceTest** - Permission logic
- **BusinessLogicTest** - Business rules validation

### **Feature Tests** (`tests/Feature/`)
- **RegistrationWizardTest** - Multi-step registration
- **BusinessVerificationTest** - Verification workflow
- **MarketplacePermissionTest** - Marketplace access
- **AuthenticationTest** - Login/logout functionality

### **Browser Tests** (`tests/Browser/`)
- **RegistrationFlowTest** - End-to-end registration
- **BusinessDashboardTest** - Business user interface
- **AdminVerificationTest** - Admin verification process

---

## 🔧 **TESTING UTILITIES**

### **Database Testing**
```php
// Use RefreshDatabase trait
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_something()
    {
        // Database is fresh for each test
        $user = User::factory()->create();
        // ... test logic
    }
}
```

### **HTTP Testing**
```php
// Test API endpoints
$response = $this->postJson('/api/register', [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'account_type' => 'manufacturer'
]);

$response->assertStatus(201)
         ->assertJson(['success' => true]);
```

### **Authentication Testing**
```php
// Test as specific user
$user = $this->createUser('admin');
$response = $this->actingAs($user)
                 ->get('/admin/dashboard');

$response->assertStatus(200);
```

---

## 📝 **TESTING BEST PRACTICES**

### **Test Naming**
```php
// Good: Descriptive test names
public function test_manufacturer_can_create_business_profile()
public function test_unverified_business_cannot_sell_products()
public function test_admin_can_approve_business_verification()

// Bad: Generic test names
public function test_user_creation()
public function test_permissions()
```

### **Test Structure**
```php
public function test_something()
{
    // Arrange - Setup test data
    $user = $this->createBusinessUser('manufacturer');
    
    // Act - Perform the action
    $response = $this->actingAs($user)->post('/business/profile', $data);
    
    // Assert - Verify the result
    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['company_name' => 'Test Company']);
}
```

### **Data Providers**
```php
/**
 * @dataProvider businessRoleProvider
 */
public function test_business_roles_have_marketplace_access($role)
{
    $user = $this->createBusinessUser($role);
    $this->assertTrue($user->canAccessMarketplace());
}

public function businessRoleProvider()
{
    return [
        ['manufacturer'],
        ['supplier'],
        ['brand']
    ];
}
```

---

## 🐛 **DEBUGGING TESTS**

### **Debug Output**
```php
// Add debug output
public function test_something()
{
    $user = User::factory()->create();
    
    // Debug user data
    dump($user->toArray());
    
    // Debug database state
    $this->assertDatabaseCount('users', 1);
}
```

### **Test Isolation**
```php
// Ensure tests don't affect each other
protected function setUp(): void
{
    parent::setUp();
    
    // Clear any cached data
    Cache::flush();
    
    // Reset any global state
    Auth::logout();
}
```

### **Common Issues**
1. **Database not refreshing** - Ensure `RefreshDatabase` trait is used
2. **Authentication issues** - Use `actingAs()` method
3. **Cache interference** - Clear cache in `setUp()` method
4. **Time-sensitive tests** - Use `Carbon::setTestNow()`

---

## 📈 **TESTING ROADMAP**

### **Phase 1 Testing** (Current)
- [x] Basic environment setup
- [x] User factory enhancements
- [x] Test data seeding
- [ ] Registration wizard tests
- [ ] Business information validation tests

### **Phase 2 Testing**
- [ ] Business verification workflow tests
- [ ] Admin dashboard tests
- [ ] Notification system tests
- [ ] Document upload tests

### **Phase 3 Testing**
- [ ] Permission matrix tests
- [ ] Marketplace access tests
- [ ] Role-based feature tests
- [ ] Commission rate tests

### **Phase 4 Testing**
- [ ] Security tests
- [ ] Compliance tests
- [ ] Audit trail tests
- [ ] Performance tests

---

## 📞 **TESTING SUPPORT**

**Test Environment Issues:**
- Check `phpunit.xml` configuration
- Verify database connection
- Clear Laravel cache: `php artisan cache:clear`

**Test Data Issues:**
- Re-run seeder: `php artisan db:seed --class=TestDataSeeder`
- Check factory definitions
- Verify model relationships

**Performance Issues:**
- Use `--parallel` flag for faster tests
- Optimize database queries in tests
- Use `--stop-on-failure` for quick debugging

---

**🎯 GOAL:** Comprehensive testing coverage để đảm bảo User Registration & Permission System Overhaul được triển khai an toàn và đáng tin cậy.
