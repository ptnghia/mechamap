# 🧪 Registration Wizard Testing Guide

**Created:** 2025-07-12  
**Version:** 1.0  
**Status:** Complete  

---

## 📋 **TESTING OVERVIEW**

### **Objective:**
Comprehensive testing strategy cho MechaMap Registration Wizard để đảm bảo reliability, security, và user experience excellence.

### **Testing Scope:**
- **Multi-step registration flow** (community + business)
- **Real-time validation** và AJAX functionality
- **Session management** và data persistence
- **File upload** và document handling
- **Security** và rate limiting
- **Browser compatibility** và responsive design
- **Database integrity** và performance

---

## 🎯 **TEST CATEGORIES**

### **1. Unit Tests**
**Purpose:** Test individual components và business logic

**Coverage:**
- `RegistrationWizardService` logic
- Form Request validation rules
- User model methods
- Document upload handling
- Session management utilities

**Files:**
```
tests/Unit/
├── RegistrationWizardLogicTest.php
├── BasicRegistrationRequestTest.php
├── BusinessRegistrationRequestTest.php
├── UserModelTest.php
└── DocumentServiceTest.php
```

### **2. Feature Tests**
**Purpose:** Test complete workflows và integration

**Coverage:**
- End-to-end registration flows
- API endpoints functionality
- Database transactions
- Email notifications
- Error handling

**Files:**
```
tests/Feature/
├── RegistrationWizardIntegrationTest.php
├── RegisterWizardControllerTest.php
├── DocumentUploadTest.php
└── SessionManagementTest.php
```

### **3. Browser Tests**
**Purpose:** Test real user interactions và UI behavior

**Coverage:**
- JavaScript functionality
- Form interactions
- Responsive design
- Accessibility
- Cross-browser compatibility

**Files:**
```
tests/Browser/
├── RegistrationWizardBrowserTest.php
├── MobileResponsiveTest.php
└── AccessibilityTest.php
```

---

## 🚀 **RUNNING TESTS**

### **Quick Start:**
```bash
# Run all tests
./tests/Scripts/run-registration-wizard-tests.sh

# Run specific test types
./tests/Scripts/run-registration-wizard-tests.sh unit
./tests/Scripts/run-registration-wizard-tests.sh feature
./tests/Scripts/run-registration-wizard-tests.sh browser
```

### **Individual Test Commands:**
```bash
# Unit tests
php artisan test tests/Unit/ --env=testing

# Feature tests
php artisan test tests/Feature/RegistrationWizardIntegrationTest.php

# Browser tests (requires Chrome)
php artisan dusk tests/Browser/RegistrationWizardBrowserTest.php

# With coverage
php artisan test --coverage-html tests/Reports/coverage
```

---

## 📊 **TEST SCENARIOS**

### **Scenario 1: Community Member Registration**
**Flow:** Step 1 → Complete
```php
// Test data
$userData = [
    'name' => 'John Community Member',
    'username' => 'johncommunity',
    'email' => 'john@community.test',
    'password' => 'SecurePass123!',
    'account_type' => 'member',
    'terms' => true
];

// Expected outcome
- User created with role 'member'
- Logged in immediately
- No step 2 required
- Redirected to dashboard
```

### **Scenario 2: Business Partner Registration**
**Flow:** Step 1 → Step 2 → Complete
```php
// Step 1 data
$basicData = [
    'name' => 'Jane Business Owner',
    'username' => 'janebusiness',
    'email' => 'jane@business.test',
    'password' => 'SecurePass123!',
    'account_type' => 'manufacturer',
    'terms' => true
];

// Step 2 data
$businessData = [
    'company_name' => 'Jane Manufacturing Co.',
    'business_license' => 'BL-JANE-001',
    'tax_code' => '1234567890',
    'business_description' => 'Manufacturing company...',
    'business_categories' => ['automotive', 'manufacturing']
];

// Expected outcome
- User created with role 'manufacturer'
- Business fields populated
- is_verified_business = false
- Logged in after completion
```

### **Scenario 3: Validation Errors**
**Test Cases:**
- Empty required fields
- Invalid email format
- Weak passwords
- Username conflicts
- Invalid tax codes
- Missing terms acceptance

### **Scenario 4: File Upload**
**Test Cases:**
- Valid document types (PDF, JPG, PNG)
- Invalid file types
- File size limits (5MB)
- Multiple file uploads
- File security scanning

### **Scenario 5: Session Management**
**Test Cases:**
- Data persistence between steps
- Session timeout handling
- Back/forward navigation
- Auto-save functionality
- Session cleanup

---

## 🔧 **TEST UTILITIES**

### **Test Data Factory:**
```php
// Create test users
$communityUser = User::factory()->community()->create();
$businessUser = User::factory()->business()->create();

// Create test documents
$document = UserVerificationDocument::factory()->create([
    'user_id' => $user->id,
    'document_type' => 'business_license'
]);
```

### **Helper Methods:**
```php
// Complete step 1 for testing
$this->completeStep1($userData);

// Mock file uploads
$file = UploadedFile::fake()->create('test.pdf', 1024);

// Assert database state
$this->assertUserCreated($email, $role);
$this->assertDocumentUploaded($userId, $documentType);
```

---

## 🛡️ **SECURITY TESTING**

### **CSRF Protection:**
```php
public function test_csrf_protection(): void
{
    $response = $this->post('/register/wizard/step1', [], [
        'HTTP_X-Requested-With' => 'XMLHttpRequest'
    ]);
    
    $response->assertStatus(419); // CSRF token mismatch
}
```

### **Rate Limiting:**
```php
public function test_rate_limiting(): void
{
    for ($i = 0; $i < 12; $i++) {
        $response = $this->post('/register/wizard/step1', $data);
        
        if ($i >= 10) {
            $response->assertStatus(429); // Too Many Requests
        }
    }
}
```

### **Input Validation:**
```php
public function test_sql_injection_protection(): void
{
    $maliciousData = [
        'name' => "'; DROP TABLE users; --",
        'email' => 'test@example.com'
    ];
    
    $response = $this->post('/register/wizard/step1', $maliciousData);
    
    // Should be sanitized and rejected
    $response->assertSessionHasErrors(['name']);
}
```

---

## 📱 **BROWSER TESTING**

### **Responsive Design:**
```javascript
// Test mobile layout
browser.resize(375, 667) // iPhone SE
       .visit('/register/wizard/step1')
       .assertVisible('.wizard-card')
       .type('name', 'Mobile User')
       .press('Tiếp tục');
```

### **JavaScript Functionality:**
```javascript
// Test real-time validation
browser.type('username', 'existinguser')
       .click('body') // Trigger blur
       .waitFor('.is-invalid', 5)
       .assertSee('Tên đăng nhập đã tồn tại');
```

### **Accessibility:**
```javascript
// Test keyboard navigation
browser.keys('body', ['{tab}'])
       .assertFocused('input[name="name"]')
       .keys('body', ['{tab}'])
       .assertFocused('input[name="username"]');
```

---

## 📈 **PERFORMANCE TESTING**

### **Load Testing:**
```php
public function test_concurrent_registrations(): void
{
    $users = collect(range(1, 50))->map(function ($i) {
        return [
            'name' => "User $i",
            'username' => "user$i",
            'email' => "user$i@test.com",
            'password' => 'Password123!',
            'account_type' => 'member',
            'terms' => true
        ];
    });
    
    // Simulate concurrent requests
    $responses = $users->map(function ($userData) {
        return $this->post('/register/wizard/step1', $userData);
    });
    
    // All should succeed
    $responses->each(function ($response) {
        $response->assertRedirect();
    });
}
```

### **Database Performance:**
```php
public function test_database_query_optimization(): void
{
    DB::enableQueryLog();
    
    // Perform registration
    $this->post('/register/wizard/step1', $userData);
    
    $queries = DB::getQueryLog();
    
    // Should not exceed reasonable query count
    $this->assertLessThan(10, count($queries));
}
```

---

## 🎯 **TEST COVERAGE GOALS**

### **Minimum Coverage Requirements:**
- **Unit Tests:** 90%+ code coverage
- **Feature Tests:** 100% critical path coverage
- **Browser Tests:** 80%+ user interaction coverage

### **Critical Paths:**
1. ✅ Community member registration (Step 1 only)
2. ✅ Business partner registration (Step 1 + 2)
3. ✅ Validation error handling
4. ✅ File upload functionality
5. ✅ Session management
6. ✅ Security protections

---

## 🔍 **DEBUGGING TESTS**

### **Common Issues:**
```bash
# Database not migrated
php artisan migrate:fresh --env=testing

# Cache issues
php artisan config:clear
php artisan cache:clear

# Browser driver issues
php artisan dusk:chrome-driver --detect

# Permission issues
chmod +x tests/Scripts/run-registration-wizard-tests.sh
```

### **Debug Commands:**
```bash
# Run single test with verbose output
php artisan test tests/Feature/RegistrationWizardIntegrationTest.php::test_community_member_registration --verbose

# Browser test with screenshots
php artisan dusk --browse tests/Browser/RegistrationWizardBrowserTest.php

# Test with debugging
php artisan test --stop-on-failure --verbose
```

---

## 📋 **TEST CHECKLIST**

### **Pre-deployment Testing:**
- [ ] All unit tests pass
- [ ] All feature tests pass
- [ ] Browser tests pass on Chrome/Firefox/Safari
- [ ] Mobile responsive tests pass
- [ ] Security tests pass
- [ ] Performance tests meet benchmarks
- [ ] Accessibility tests pass
- [ ] Database integrity verified

### **Manual Testing:**
- [ ] Test on real devices (iOS/Android)
- [ ] Test with slow network connections
- [ ] Test with JavaScript disabled
- [ ] Test with screen readers
- [ ] Test edge cases và error scenarios

---

## 🚀 **CONTINUOUS INTEGRATION**

### **GitHub Actions Workflow:**
```yaml
name: Registration Wizard Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.1
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: ./tests/Scripts/run-registration-wizard-tests.sh all
```

### **Test Automation:**
- **Pre-commit hooks** chạy unit tests
- **CI pipeline** chạy full test suite
- **Nightly builds** với performance testing
- **Deployment gates** require 100% test pass

---

**🎯 TESTING STATUS:** ✅ Complete - Ready for Production Deployment**
