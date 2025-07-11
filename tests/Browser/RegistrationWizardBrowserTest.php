<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;

/**
 * 🌐 Registration Wizard Browser Tests
 * 
 * End-to-end browser testing for registration wizard
 * Tests real user interactions, JavaScript functionality, and UI behavior
 */
class RegistrationWizardBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test complete community member registration flow in browser
     */
    public function test_community_member_registration_flow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    ->assertSee('Đăng ký tài khoản MechaMap')
                    ->assertSee('Bước 1: Thông tin cơ bản')
                    
                    // Fill basic information
                    ->type('name', 'John Community Member')
                    ->type('username', 'johncommunity')
                    ->type('email', 'john@community.test')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    
                    // Select community member account type
                    ->radio('account_type', 'member')
                    
                    // Accept terms
                    ->check('terms')
                    
                    // Submit form
                    ->press('Tiếp tục')
                    
                    // Should be redirected to dashboard (registration complete)
                    ->waitForLocation('/dashboard', 10)
                    ->assertSee('Dashboard')
                    
                    // Verify user is logged in
                    ->assertAuthenticated();
        });

        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'email' => 'john@community.test',
            'role' => 'member',
            'role_group' => 'community_members'
        ]);
    }

    /**
     * Test complete business partner registration flow in browser
     */
    public function test_business_partner_registration_flow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Fill step 1
                    ->type('name', 'Jane Business Owner')
                    ->type('username', 'janebusiness')
                    ->type('email', 'jane@business.test')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    
                    // Select business account type
                    ->radio('account_type', 'manufacturer')
                    ->check('terms')
                    ->press('Tiếp tục')
                    
                    // Should be redirected to step 2
                    ->waitForLocation('/register/wizard/step2', 10)
                    ->assertSee('Bước 2: Thông tin doanh nghiệp')
                    ->assertSee('Nhà sản xuất')
                    
                    // Fill business information
                    ->type('company_name', 'Jane Manufacturing Co.')
                    ->type('business_license', 'BL-JANE-001')
                    ->type('tax_code', '1234567890')
                    ->type('business_description', 'A comprehensive manufacturing company specializing in mechanical components and industrial equipment for various sectors.')
                    
                    // Select business categories
                    ->check('input[name="business_categories[]"][value="automotive"]')
                    ->check('input[name="business_categories[]"][value="manufacturing"]')
                    
                    // Fill optional contact info
                    ->type('business_phone', '+1-555-0123')
                    ->type('business_email', 'info@janemanufacturing.com')
                    ->type('business_address', '123 Industrial Ave, Manufacturing City, MC 12345')
                    
                    // Submit form
                    ->press('Hoàn tất đăng ký')
                    
                    // Should be redirected to completion page or dashboard
                    ->waitForLocation('/dashboard', 15)
                    ->assertAuthenticated();
        });

        // Verify business user was created
        $this->assertDatabaseHas('users', [
            'email' => 'jane@business.test',
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'company_name' => 'Jane Manufacturing Co.',
            'tax_code' => '1234567890'
        ]);
    }

    /**
     * Test real-time username validation
     */
    public function test_real_time_username_validation(): void
    {
        // Create existing user
        User::factory()->create(['username' => 'existinguser']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Type existing username
                    ->type('username', 'existinguser')
                    ->click('body') // Trigger blur event
                    
                    // Wait for validation response
                    ->waitFor('.is-invalid', 5)
                    ->assertSee('Tên đăng nhập đã tồn tại')
                    
                    // Type available username
                    ->clear('username')
                    ->type('username', 'availableuser')
                    ->click('body')
                    
                    // Wait for success validation
                    ->waitFor('.is-valid', 5)
                    ->assertSee('Tên đăng nhập khả dụng');
        });
    }

    /**
     * Test password strength indicator
     */
    public function test_password_strength_indicator(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Type weak password
                    ->type('password', '123')
                    ->waitFor('#passwordStrength', 2)
                    ->assertSee('Yếu')
                    
                    // Type medium password
                    ->clear('password')
                    ->type('password', 'Password123')
                    ->pause(500)
                    ->assertSee('Trung bình')
                    
                    // Type strong password
                    ->clear('password')
                    ->type('password', 'SecurePass123!')
                    ->pause(500)
                    ->assertSee('Mạnh');
        });
    }

    /**
     * Test form validation errors
     */
    public function test_form_validation_errors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Submit empty form
                    ->press('Tiếp tục')
                    
                    // Should show validation errors
                    ->waitFor('.alert-danger', 5)
                    ->assertSee('Có lỗi xảy ra')
                    
                    // Fill some fields incorrectly
                    ->type('name', 'A') // Too short
                    ->type('username', 'ab') // Too short
                    ->type('email', 'invalid-email') // Invalid format
                    ->type('password', '123') // Too weak
                    ->type('password_confirmation', '456') // Doesn't match
                    
                    ->press('Tiếp tục')
                    
                    // Should still show errors
                    ->waitFor('.alert-danger', 5)
                    ->assertSee('Có lỗi xảy ra');
        });
    }

    /**
     * Test account type selection UI
     */
    public function test_account_type_selection_ui(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Test community member selection
                    ->radio('account_type', 'member')
                    ->assertSelected('account_type', 'member')
                    
                    // Test business partner selection
                    ->radio('account_type', 'manufacturer')
                    ->assertSelected('account_type', 'manufacturer')
                    
                    // Verify business notice appears
                    ->assertVisible('.business-notice')
                    ->assertSee('Tài khoản doanh nghiệp cần cung cấp thông tin công ty');
        });
    }

    /**
     * Test step 2 business categories selection
     */
    public function test_business_categories_selection(): void
    {
        $this->browse(function (Browser $browser) {
            // Complete step 1 first
            $browser->visit('/register/wizard/step1')
                    ->type('name', 'Business Test User')
                    ->type('username', 'businesstest')
                    ->type('email', 'business@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'supplier')
                    ->check('terms')
                    ->press('Tiếp tục')
                    
                    // Now on step 2
                    ->waitForLocation('/register/wizard/step2', 10)
                    
                    // Test category selection
                    ->check('input[name="business_categories[]"][value="automotive"]')
                    ->check('input[name="business_categories[]"][value="materials"]')
                    ->check('input[name="business_categories[]"][value="components"]')
                    
                    // Verify selections
                    ->assertChecked('input[name="business_categories[]"][value="automotive"]')
                    ->assertChecked('input[name="business_categories[]"][value="materials"]')
                    ->assertChecked('input[name="business_categories[]"][value="components"]');
        });
    }

    /**
     * Test responsive design on mobile
     */
    public function test_responsive_design_mobile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone SE size
                    ->visit('/register/wizard/step1')
                    
                    // Verify mobile layout
                    ->assertVisible('.registration-wizard-container')
                    ->assertVisible('.wizard-card')
                    
                    // Test form interaction on mobile
                    ->type('name', 'Mobile Test User')
                    ->type('username', 'mobiletest')
                    ->type('email', 'mobile@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'student')
                    ->check('terms')
                    
                    // Submit should work on mobile
                    ->press('Tiếp tục')
                    ->waitForLocation('/dashboard', 10);
        });
    }

    /**
     * Test wizard progress indicator
     */
    public function test_wizard_progress_indicator(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Verify step 1 progress
                    ->assertSee('Bước 1 / 2')
                    ->assertSee('50% hoàn thành')
                    
                    // Complete step 1 for business user
                    ->type('name', 'Progress Test User')
                    ->type('username', 'progresstest')
                    ->type('email', 'progress@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'brand')
                    ->check('terms')
                    ->press('Tiếp tục')
                    
                    // Verify step 2 progress
                    ->waitForLocation('/register/wizard/step2', 10)
                    ->assertSee('Bước 2 / 2')
                    ->assertSee('100% hoàn thành');
        });
    }

    /**
     * Test auto-save functionality
     */
    public function test_auto_save_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Fill some data
                    ->type('name', 'Auto Save Test')
                    ->type('username', 'autosavetest')
                    ->type('email', 'autosave@test.com')
                    
                    // Wait for auto-save (should trigger after input)
                    ->pause(2000)
                    
                    // Refresh page
                    ->refresh()
                    
                    // Data should be preserved (if auto-save is working)
                    ->assertInputValue('name', 'Auto Save Test')
                    ->assertInputValue('username', 'autosavetest')
                    ->assertInputValue('email', 'autosave@test.com');
        });
    }

    /**
     * Test terms and conditions modal
     */
    public function test_terms_and_conditions_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Click terms link
                    ->click('a[data-bs-target="#termsModal"]')
                    
                    // Wait for modal to appear
                    ->waitFor('#termsModal', 5)
                    ->assertVisible('#termsModal')
                    ->assertSee('Điều khoản sử dụng')
                    
                    // Close modal
                    ->click('#termsModal .btn-close')
                    ->waitUntilMissing('#termsModal.show', 5);
        });
    }

    /**
     * Test back button functionality
     */
    public function test_back_button_functionality(): void
    {
        $this->browse(function (Browser $browser) {
            // Complete step 1
            $browser->visit('/register/wizard/step1')
                    ->type('name', 'Back Button Test')
                    ->type('username', 'backbuttontest')
                    ->type('email', 'backbutton@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'manufacturer')
                    ->check('terms')
                    ->press('Tiếp tục')
                    
                    // Now on step 2
                    ->waitForLocation('/register/wizard/step2', 10)
                    
                    // Click back button
                    ->click('#wizardBackBtn')
                    
                    // Should go back to step 1
                    ->waitForLocation('/register/wizard/step1', 10)
                    ->assertSee('Bước 1: Thông tin cơ bản');
        });
    }

    /**
     * Test error handling and recovery
     */
    public function test_error_handling_and_recovery(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Try to submit with missing required fields
                    ->type('name', 'Error Test User')
                    ->press('Tiếp tục')
                    
                    // Should show error message
                    ->waitFor('.alert-danger', 5)
                    ->assertSee('Có lỗi xảy ra')
                    
                    // Fix errors and resubmit
                    ->type('username', 'errortest')
                    ->type('email', 'error@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'member')
                    ->check('terms')
                    ->press('Tiếp tục')
                    
                    // Should succeed
                    ->waitForLocation('/dashboard', 10)
                    ->assertAuthenticated();
        });
    }

    /**
     * Test keyboard navigation accessibility
     */
    public function test_keyboard_navigation_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register/wizard/step1')
                    
                    // Test tab navigation through form fields
                    ->keys('body', ['{tab}']) // Focus first field
                    ->assertFocused('input[name="name"]')
                    
                    ->keys('body', ['{tab}'])
                    ->assertFocused('input[name="username"]')
                    
                    ->keys('body', ['{tab}'])
                    ->assertFocused('input[name="email"]')
                    
                    // Test form submission with Enter key
                    ->type('name', 'Keyboard Test User')
                    ->type('username', 'keyboardtest')
                    ->type('email', 'keyboard@test.com')
                    ->type('password', 'SecurePass123!')
                    ->type('password_confirmation', 'SecurePass123!')
                    ->radio('account_type', 'student')
                    ->check('terms')
                    
                    // Submit with Enter key
                    ->keys('button[type="submit"]', ['{enter}'])
                    ->waitForLocation('/dashboard', 10);
        });
    }
}
