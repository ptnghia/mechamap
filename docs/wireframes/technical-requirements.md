# 🔧 Technical Requirements - Multi-Step Registration Form

**Created:** 2025-07-12  
**Task:** 1.1 Phân tích và thiết kế multi-step registration form  
**Purpose:** Technical specifications và implementation requirements

---

## 📋 **IMPLEMENTATION ROADMAP**

### **Phase 1.1 Deliverables:**
- [x] **Wireframe Design** - Complete visual mockups
- [x] **User Flow Diagram** - Complete interaction flows  
- [x] **Technical Requirements** - Implementation specifications
- [x] **UI Mockup** - Interactive HTML prototype
- [ ] **Ready for Phase 1.2** - RegisterWizardController creation

---

## 🏗️ **ARCHITECTURE OVERVIEW**

### **MVC Structure:**
```
Controllers/
├── Auth/
│   ├── RegisterWizardController.php    # New multi-step controller
│   ├── RegisteredUserController.php    # Keep for backward compatibility
│   └── AjaxAuthController.php          # Update for wizard support
│
Models/
├── User.php                            # Enhanced with business fields
└── RegistrationSession.php             # New session management model
│
Views/
├── auth/
│   ├── wizard/
│   │   ├── step1.blade.php             # Basic information step
│   │   ├── step2.blade.php             # Business information step
│   │   └── success.blade.php           # Completion pages
│   └── components/
│       └── registration-wizard.blade.php # Reusable wizard component
│
Services/
├── RegistrationWizardService.php       # Business logic service
└── ValidationService.php               # Enhanced validation
│
Requests/
├── Auth/
│   ├── BasicRegistrationRequest.php    # Step 1 validation
│   └── BusinessRegistrationRequest.php # Step 2 validation
```

### **Database Schema Updates:**
```sql
-- Users table already has business fields
-- Additional fields needed:
ALTER TABLE users ADD COLUMN verification_documents JSON NULL;
ALTER TABLE users ADD COLUMN verified_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN verified_by BIGINT UNSIGNED NULL;
ALTER TABLE users ADD COLUMN verification_notes TEXT NULL;

-- New registration sessions table
CREATE TABLE registration_sessions (
    id VARCHAR(255) PRIMARY KEY,
    step TINYINT NOT NULL DEFAULT 1,
    data JSON NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🔄 **CONTROLLER SPECIFICATIONS**

### **RegisterWizardController Methods:**

```php
class RegisterWizardController extends Controller
{
    // GET /register/wizard/step1
    public function showStep1(Request $request): View
    
    // POST /register/wizard/step1  
    public function processStep1(BasicRegistrationRequest $request): RedirectResponse
    
    // GET /register/wizard/step2
    public function showStep2(Request $request): View
    
    // POST /register/wizard/step2
    public function processStep2(BusinessRegistrationRequest $request): RedirectResponse
    
    // GET /register/wizard/complete
    public function complete(Request $request): View
    
    // POST /register/wizard/restart
    public function restart(Request $request): RedirectResponse
    
    // AJAX endpoints
    public function validateField(Request $request): JsonResponse
    public function checkUsername(Request $request): JsonResponse
    public function saveProgress(Request $request): JsonResponse
}
```

### **Session Management:**
```php
class RegistrationWizardService
{
    public function initializeSession(): string
    public function getSessionData(string $sessionId): array
    public function updateSessionData(string $sessionId, array $data): bool
    public function advanceStep(string $sessionId): bool
    public function completeRegistration(string $sessionId): User
    public function cleanupExpiredSessions(): int
}
```

---

## 📝 **VALIDATION SPECIFICATIONS**

### **Step 1 Validation Rules:**
```php
class BasicRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'username' => [
                'required', 
                'string', 
                'max:255', 
                'min:3',
                'unique:users,username',
                'alpha_dash',
                'not_in:admin,root,api,www'
            ],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                'unique:users,email'
            ],
            'password' => [
                'required', 
                'confirmed', 
                Rules\Password::defaults()
                    ->min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'account_type' => [
                'required', 
                'string', 
                'in:member,student,manufacturer,supplier,brand'
            ],
            'terms' => ['required', 'accepted']
        ];
    }
}
```

### **Step 2 Business Validation Rules:**
```php
class BusinessRegistrationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255', 'min:2'],
            'business_license' => ['required', 'string', 'max:100'],
            'tax_code' => [
                'required', 
                'string', 
                'max:20',
                'regex:/^[0-9]{10,13}$/',
                'unique:users,tax_code'
            ],
            'business_description' => ['required', 'string', 'min:50', 'max:1000'],
            'business_categories' => ['required', 'array', 'min:1'],
            'business_categories.*' => [
                'string',
                'in:automotive,aerospace,manufacturing,materials,components,industrial'
            ],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'business_email' => [
                'nullable', 
                'email', 
                'max:255',
                'different:email'
            ],
            'business_address' => ['nullable', 'string', 'max:500'],
            'verification_documents' => ['nullable', 'array'],
            'verification_documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ]
        ];
    }
}
```

---

## 🎨 **FRONTEND SPECIFICATIONS**

### **CSS Architecture:**
```scss
// public/css/registration-wizard.css
.registration-wizard {
    // Container styles
    max-width: 600px;
    margin: 0 auto;
    
    // Step styles
    &__step {
        display: none;
        &.active { display: block; }
    }
    
    // Progress bar
    &__progress {
        height: 8px;
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.4s ease-in-out;
    }
    
    // Form styles
    &__form {
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.2s ease-out;
            
            &:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            
            &.is-valid { border-color: #28a745; }
            &.is-invalid { border-color: #dc3545; }
        }
    }
    
    // Responsive design
    @media (max-width: 768px) {
        padding: 1rem;
        
        &__actions {
            flex-direction: column;
            gap: 1rem;
            
            .btn { width: 100%; }
        }
    }
}
```

### **JavaScript Architecture:**
```javascript
// public/js/registration-wizard.js
class RegistrationWizard {
    constructor(options = {}) {
        this.currentStep = 1;
        this.totalSteps = 2;
        this.sessionId = null;
        this.validationRules = {};
        this.init();
    }
    
    // Core methods
    init() { /* Initialize wizard */ }
    nextStep() { /* Advance to next step */ }
    prevStep() { /* Go back to previous step */ }
    validateStep(step) { /* Validate current step */ }
    saveProgress() { /* Auto-save form data */ }
    
    // Validation methods
    validateField(field) { /* Real-time field validation */ }
    checkUsername(username) { /* AJAX username check */ }
    validatePassword(password) { /* Password strength check */ }
    
    // UI methods
    updateProgress() { /* Update progress bar */ }
    showError(field, message) { /* Display validation error */ }
    showSuccess(field, message) { /* Display success message */ }
    
    // AJAX methods
    submitStep(stepData) { /* Submit step data */ }
    loadSessionData() { /* Load saved progress */ }
}

// Initialize wizard
document.addEventListener('DOMContentLoaded', function() {
    const wizard = new RegistrationWizard({
        autoSave: true,
        autoSaveInterval: 30000, // 30 seconds
        validationDelay: 500,    // 500ms debounce
        sessionTimeout: 1800000  // 30 minutes
    });
});
```

---

## 🔒 **SECURITY SPECIFICATIONS**

### **CSRF Protection:**
- All forms include `@csrf` token
- AJAX requests include `X-CSRF-TOKEN` header
- Session-based token validation

### **Input Sanitization:**
```php
// Sanitization rules
$sanitized = [
    'name' => strip_tags(trim($request->name)),
    'username' => strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $request->username)),
    'email' => strtolower(trim($request->email)),
    'company_name' => strip_tags(trim($request->company_name)),
    'business_description' => strip_tags($request->business_description, '<p><br><strong><em>'),
];
```

### **Rate Limiting:**
```php
// routes/auth.php
Route::middleware(['throttle:registration'])->group(function () {
    Route::post('register/wizard/step1', [RegisterWizardController::class, 'processStep1']);
    Route::post('register/wizard/step2', [RegisterWizardController::class, 'processStep2']);
});

// config/app.php - Rate limiting
'registration' => '10,1', // 10 attempts per minute
'username_check' => '30,1', // 30 username checks per minute
```

### **File Upload Security:**
```php
// Document upload validation
'verification_documents.*' => [
    'file',
    'mimes:pdf,jpg,jpeg,png',
    'max:5120', // 5MB
    'dimensions:max_width=4000,max_height=4000',
    function ($attribute, $value, $fail) {
        // Custom virus scan validation
        if (!$this->isFileSafe($value)) {
            $fail('File failed security scan.');
        }
    }
]
```

---

## 📊 **PERFORMANCE SPECIFICATIONS**

### **Performance Targets:**
- **Page Load Time:** <2 seconds
- **Step Transition:** <300ms
- **AJAX Response:** <500ms
- **Form Validation:** <100ms
- **File Upload:** <10 seconds (5MB)

### **Optimization Strategies:**
1. **CSS/JS Minification:** Webpack/Vite build process
2. **Image Optimization:** WebP format, lazy loading
3. **Caching:** Browser cache headers, CDN for assets
4. **Database:** Indexed queries, connection pooling
5. **Session Storage:** Redis for session management

### **Monitoring:**
```php
// Performance monitoring
Log::info('Registration Step Completed', [
    'step' => $step,
    'duration' => $duration,
    'account_type' => $accountType,
    'user_agent' => $request->userAgent(),
    'ip' => $request->ip()
]);
```

---

## 🧪 **TESTING SPECIFICATIONS**

### **Unit Tests:**
```php
// tests/Unit/RegistrationWizardServiceTest.php
class RegistrationWizardServiceTest extends TestCase
{
    public function test_can_initialize_session()
    public function test_can_save_step_data()
    public function test_can_advance_steps()
    public function test_can_complete_registration()
    public function test_handles_session_timeout()
}
```

### **Feature Tests:**
```php
// tests/Feature/RegistrationWizardTest.php
class RegistrationWizardTest extends TestCase
{
    public function test_step1_displays_correctly()
    public function test_step1_validation_works()
    public function test_step2_shows_for_business_users()
    public function test_community_registration_completes()
    public function test_business_registration_requires_approval()
}
```

### **Browser Tests:**
```php
// tests/Browser/RegistrationFlowTest.php
class RegistrationFlowTest extends DuskTestCase
{
    public function test_complete_community_registration()
    public function test_complete_business_registration()
    public function test_form_validation_feedback()
    public function test_mobile_responsive_design()
}
```

---

## 📱 **ACCESSIBILITY SPECIFICATIONS**

### **WCAG 2.1 AA Compliance:**
- **Keyboard Navigation:** Full tab order support
- **Screen Readers:** ARIA labels and descriptions
- **Color Contrast:** 4.5:1 minimum ratio
- **Focus Management:** Clear visual indicators
- **Error Announcements:** Live regions for validation

### **Implementation:**
```html
<!-- Accessible form structure -->
<div class="wizard-step" role="tabpanel" aria-labelledby="step1-tab">
    <fieldset>
        <legend>Thông tin cơ bản</legend>
        
        <div class="form-group">
            <label for="name" class="required">
                Họ và tên
                <span class="sr-only">bắt buộc</span>
            </label>
            <input type="text" id="name" name="name" 
                   aria-describedby="name-help name-error"
                   aria-invalid="false" required>
            <div id="name-help" class="form-text">
                Nhập họ và tên đầy đủ của bạn
            </div>
            <div id="name-error" class="invalid-feedback" 
                 role="alert" aria-live="polite">
            </div>
        </div>
    </fieldset>
</div>
```

---

## 🚀 **DEPLOYMENT SPECIFICATIONS**

### **Environment Configuration:**
```env
# .env additions
REGISTRATION_WIZARD_ENABLED=true
REGISTRATION_SESSION_TIMEOUT=1800
REGISTRATION_AUTO_SAVE=true
REGISTRATION_FILE_UPLOAD_MAX_SIZE=5120
REGISTRATION_ALLOWED_FILE_TYPES=pdf,jpg,jpeg,png
```

### **Cache Configuration:**
```php
// config/cache.php
'registration_sessions' => [
    'driver' => 'redis',
    'connection' => 'default',
    'prefix' => 'reg_session:',
    'ttl' => 1800, // 30 minutes
]
```

---

**🔧 TECHNICAL STATUS:** ✅ Requirements Complete - Ready for Implementation**
