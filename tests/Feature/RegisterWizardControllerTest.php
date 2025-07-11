<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\RegistrationWizardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * 🧪 Registration Wizard Controller Tests
 * 
 * Tests for multi-step registration wizard functionality
 */
class RegisterWizardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected RegistrationWizardService $wizardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wizardService = app(RegistrationWizardService::class);
    }

    /**
     * Test step 1 displays correctly
     */
    public function test_step1_displays_correctly(): void
    {
        $response = $this->get(route('register.wizard.step1'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.wizard.step1');
        $response->assertViewHas(['sessionData', 'step', 'totalSteps', 'progress']);
        
        // Check session was created
        $this->assertNotNull(Session::get('registration_wizard_session'));
    }

    /**
     * Test step 1 validation works
     */
    public function test_step1_validation_works(): void
    {
        $response = $this->post(route('register.wizard.step1'), []);

        $response->assertSessionHasErrors([
            'name', 'username', 'email', 'password', 'account_type', 'terms'
        ]);
    }

    /**
     * Test step 1 processes valid community member data
     */
    public function test_step1_processes_valid_community_member_data(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'member',
            'terms' => true,
        ];

        $response = $this->post(route('register.wizard.step1'), $userData);

        // Community members should complete registration immediately
        $response->assertRedirect();
        
        // User should be created and logged in
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'member',
            'role_group' => 'community_members'
        ]);
    }

    /**
     * Test step 1 redirects business users to step 2
     */
    public function test_step1_redirects_business_users_to_step2(): void
    {
        $userData = [
            'name' => 'Business User',
            'username' => 'businessuser',
            'email' => 'business@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'manufacturer',
            'terms' => true,
        ];

        $response = $this->post(route('register.wizard.step1'), $userData);

        // Business users should go to step 2
        $response->assertRedirect(route('register.wizard.step2'));
        
        // User should not be created yet
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'business@example.com'
        ]);
    }

    /**
     * Test step 2 requires step 1 completion
     */
    public function test_step2_requires_step1_completion(): void
    {
        $response = $this->get(route('register.wizard.step2'));

        $response->assertRedirect(route('register.wizard.step1'));
        $response->assertSessionHas('error');
    }

    /**
     * Test step 2 displays for business users
     */
    public function test_step2_displays_for_business_users(): void
    {
        // Complete step 1 first
        $sessionId = $this->wizardService->initializeSession();
        $this->wizardService->updateSessionData($sessionId, [
            'name' => 'Business User',
            'username' => 'businessuser',
            'email' => 'business@example.com',
            'password' => 'Password123!',
            'account_type' => 'manufacturer',
            'step_1_completed' => true,
        ]);
        $this->wizardService->advanceStep($sessionId);
        
        Session::put('registration_wizard_session', $sessionId);

        $response = $this->get(route('register.wizard.step2'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.wizard.step2');
        $response->assertViewHas(['sessionData', 'step', 'totalSteps', 'progress', 'accountType']);
    }

    /**
     * Test step 2 validation works
     */
    public function test_step2_validation_works(): void
    {
        // Setup session with step 1 completed
        $sessionId = $this->wizardService->initializeSession();
        $this->wizardService->updateSessionData($sessionId, [
            'account_type' => 'manufacturer',
            'step_1_completed' => true,
        ]);
        Session::put('registration_wizard_session', $sessionId);

        $response = $this->post(route('register.wizard.step2'), []);

        $response->assertSessionHasErrors([
            'company_name', 'business_license', 'tax_code', 
            'business_description', 'business_categories'
        ]);
    }

    /**
     * Test complete business registration
     */
    public function test_complete_business_registration(): void
    {
        // Setup session with step 1 completed
        $sessionId = $this->wizardService->initializeSession();
        $this->wizardService->updateSessionData($sessionId, [
            'name' => 'Business User',
            'username' => 'businessuser',
            'email' => 'business@example.com',
            'password' => 'Password123!',
            'account_type' => 'manufacturer',
            'step_1_completed' => true,
        ]);
        Session::put('registration_wizard_session', $sessionId);

        $businessData = [
            'company_name' => 'Test Manufacturing Co.',
            'business_license' => 'BL-TEST-001',
            'tax_code' => '1234567890',
            'business_description' => 'This is a test manufacturing company for automated testing purposes. We specialize in mechanical engineering and product development.',
            'business_categories' => ['automotive', 'manufacturing'],
            'business_phone' => '+1-555-0001',
            'business_email' => 'business@testmanufacturing.com',
            'business_address' => '123 Manufacturing St, Test City, TC 12345',
        ];

        $response = $this->post(route('register.wizard.step2'), $businessData);

        // Should complete registration and redirect
        $response->assertRedirect();
        
        // User should be created and logged in
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'business@example.com',
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'company_name' => 'Test Manufacturing Co.',
            'tax_code' => '1234567890',
            'is_verified_business' => false, // Should require admin approval
        ]);
    }

    /**
     * Test username availability check
     */
    public function test_username_availability_check(): void
    {
        // Create existing user
        User::factory()->create(['username' => 'existinguser']);

        // Check existing username
        $response = $this->post(route('register.wizard.check-username'), [
            'username' => 'existinguser'
        ]);

        $response->assertJson([
            'available' => false,
            'message' => 'Tên đăng nhập đã tồn tại.'
        ]);

        // Check available username
        $response = $this->post(route('register.wizard.check-username'), [
            'username' => 'newuser'
        ]);

        $response->assertJson([
            'available' => true,
            'message' => 'Tên đăng nhập khả dụng.'
        ]);
    }

    /**
     * Test field validation endpoint
     */
    public function test_field_validation_endpoint(): void
    {
        // Test valid email
        $response = $this->post(route('register.wizard.validate-field'), [
            'field' => 'email',
            'value' => 'test@example.com',
            'step' => 1
        ]);

        $response->assertJson([
            'valid' => true,
            'message' => 'Hợp lệ'
        ]);

        // Test invalid email
        $response = $this->post(route('register.wizard.validate-field'), [
            'field' => 'email',
            'value' => 'invalid-email',
            'step' => 1
        ]);

        $response->assertJson([
            'valid' => false
        ]);
    }

    /**
     * Test session restart functionality
     */
    public function test_session_restart_functionality(): void
    {
        // Create session with some data
        $sessionId = $this->wizardService->initializeSession();
        $this->wizardService->updateSessionData($sessionId, [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        Session::put('registration_wizard_session', $sessionId);

        // Restart session
        $response = $this->post(route('register.wizard.restart'));

        $response->assertRedirect(route('register.wizard.step1'));
        $response->assertSessionHas('info');
        
        // Old session should be cleared
        $this->assertNull(Session::get('registration_wizard_session'));
    }

    /**
     * Test auto-save functionality
     */
    public function test_auto_save_functionality(): void
    {
        // Create session
        $sessionId = $this->wizardService->initializeSession();
        Session::put('registration_wizard_session', $sessionId);

        $saveData = [
            'data' => [
                'name' => 'Auto Saved User',
                'email' => 'autosave@example.com'
            ]
        ];

        $response = $this->post(route('register.wizard.save-progress'), $saveData);

        $response->assertJson([
            'success' => true,
            'message' => 'Đã lưu tự động.'
        ]);

        // Verify data was saved
        $sessionData = $this->wizardService->getSessionData($sessionId);
        $this->assertEquals('Auto Saved User', $sessionData['name']);
        $this->assertEquals('autosave@example.com', $sessionData['email']);
    }
}
