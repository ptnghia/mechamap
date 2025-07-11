<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserVerificationDocument;
use App\Services\RegistrationWizardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

/**
 * 🧪 Registration Wizard Integration Tests
 * 
 * Comprehensive testing for multi-step registration wizard
 * Tests complete flows, edge cases, and integration scenarios
 */
class RegistrationWizardIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected RegistrationWizardService $wizardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wizardService = app(RegistrationWizardService::class);
        
        // Setup storage for testing
        Storage::fake('public');
        
        // Clear cache
        Cache::flush();
    }

    /**
     * Test complete community member registration flow
     */
    public function test_complete_community_member_registration_flow(): void
    {
        // Step 1: Visit wizard
        $response = $this->get(route('register.wizard.step1'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.wizard.step1');
        
        // Verify session was created
        $sessionId = Session::get('registration_wizard_session');
        $this->assertNotNull($sessionId);
        
        // Step 2: Submit valid community member data
        $userData = [
            'name' => 'Test Community Member',
            'username' => 'testmember',
            'email' => 'member@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'member',
            'terms' => true,
        ];

        $response = $this->post(route('register.wizard.step1'), $userData);
        
        // Should complete registration immediately for community members
        $response->assertRedirect();
        
        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'member@test.com',
            'role' => 'member',
            'role_group' => 'community_members',
            'is_verified_business' => false,
        ]);
        
        // Verify user is logged in
        $this->assertAuthenticated();
        
        // Verify session was cleaned up
        $this->assertNull(Session::get('registration_wizard_session'));
    }

    /**
     * Test complete business partner registration flow
     */
    public function test_complete_business_partner_registration_flow(): void
    {
        // Step 1: Submit business user basic info
        $basicData = [
            'name' => 'Business Owner',
            'username' => 'businessowner',
            'email' => 'business@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'manufacturer',
            'terms' => true,
        ];

        $response = $this->post(route('register.wizard.step1'), $basicData);
        
        // Should redirect to step 2 for business users
        $response->assertRedirect(route('register.wizard.step2'));
        
        // Step 2: Visit step 2
        $response = $this->get(route('register.wizard.step2'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.wizard.step2');
        
        // Step 3: Submit business information
        $businessData = [
            'company_name' => 'Test Manufacturing Co.',
            'business_license' => 'BL-TEST-001',
            'tax_code' => '1234567890',
            'business_description' => 'This is a test manufacturing company for automated testing purposes. We specialize in mechanical engineering and product development.',
            'business_categories' => ['automotive', 'manufacturing'],
            'business_phone' => '+1-555-0001',
            'business_email' => 'info@testmanufacturing.com',
            'business_address' => '123 Manufacturing St, Test City, TC 12345',
        ];

        $response = $this->post(route('register.wizard.step2'), $businessData);
        
        // Should complete registration
        $response->assertRedirect();
        
        // Verify business user was created
        $this->assertDatabaseHas('users', [
            'email' => 'business@test.com',
            'role' => 'manufacturer',
            'role_group' => 'business_partners',
            'company_name' => 'Test Manufacturing Co.',
            'tax_code' => '1234567890',
            'is_verified_business' => false, // Should require admin approval
        ]);
        
        // Verify user is logged in
        $this->assertAuthenticated();
    }

    /**
     * Test business registration with document upload
     */
    public function test_business_registration_with_document_upload(): void
    {
        // Complete step 1
        $this->post(route('register.wizard.step1'), [
            'name' => 'Business Owner',
            'username' => 'businessowner2',
            'email' => 'business2@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'supplier',
            'terms' => true,
        ]);

        // Create fake files
        $document1 = UploadedFile::fake()->create('business_license.pdf', 1024, 'application/pdf');
        $document2 = UploadedFile::fake()->image('tax_certificate.jpg', 800, 600);

        // Submit step 2 with documents
        $businessData = [
            'company_name' => 'Test Supply Co.',
            'business_license' => 'BL-SUPPLY-001',
            'tax_code' => '9876543210',
            'business_description' => 'Test supply company specializing in mechanical components and materials for industrial applications.',
            'business_categories' => ['materials', 'components'],
            'verification_documents' => [$document1, $document2],
        ];

        $response = $this->post(route('register.wizard.step2'), $businessData);
        $response->assertRedirect();

        // Verify user was created
        $user = User::where('email', 'business2@test.com')->first();
        $this->assertNotNull($user);

        // Verify documents were uploaded
        $this->assertCount(2, $user->verificationDocuments);
        
        // Verify files exist in storage
        $documents = $user->verificationDocuments;
        foreach ($documents as $document) {
            Storage::disk('public')->assertExists($document->file_path);
        }
    }

    /**
     * Test validation errors in step 1
     */
    public function test_step1_validation_errors(): void
    {
        $invalidData = [
            'name' => '', // Required
            'username' => 'ab', // Too short
            'email' => 'invalid-email', // Invalid format
            'password' => '123', // Too weak
            'password_confirmation' => '456', // Doesn't match
            'account_type' => 'invalid', // Invalid type
            // terms missing
        ];

        $response = $this->post(route('register.wizard.step1'), $invalidData);
        
        $response->assertSessionHasErrors([
            'name', 'username', 'email', 'password', 'account_type', 'terms'
        ]);
        
        // Should stay on step 1
        $response->assertRedirect();
        
        // No user should be created
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email'
        ]);
    }

    /**
     * Test validation errors in step 2
     */
    public function test_step2_validation_errors(): void
    {
        // Complete step 1 first
        $this->post(route('register.wizard.step1'), [
            'name' => 'Business Owner',
            'username' => 'businessowner3',
            'email' => 'business3@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'brand',
            'terms' => true,
        ]);

        // Submit invalid step 2 data
        $invalidData = [
            'company_name' => '', // Required
            'business_license' => '', // Required
            'tax_code' => '123', // Too short
            'business_description' => 'Short', // Too short
            'business_categories' => [], // Required
            'business_email' => 'business3@test.com', // Same as personal email
        ];

        $response = $this->post(route('register.wizard.step2'), $invalidData);
        
        $response->assertSessionHasErrors([
            'company_name', 'business_license', 'tax_code', 
            'business_description', 'business_categories', 'business_email'
        ]);
        
        // No user should be created
        $this->assertDatabaseMissing('users', [
            'email' => 'business3@test.com'
        ]);
    }

    /**
     * Test session management and data persistence
     */
    public function test_session_management_and_data_persistence(): void
    {
        // Start wizard
        $response = $this->get(route('register.wizard.step1'));
        $sessionId = Session::get('registration_wizard_session');
        
        // Submit step 1 for business user
        $this->post(route('register.wizard.step1'), [
            'name' => 'Session Test User',
            'username' => 'sessiontest',
            'email' => 'session@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'manufacturer',
            'terms' => true,
        ]);
        
        // Verify session data was saved
        $sessionData = $this->wizardService->getSessionData($sessionId);
        $this->assertEquals('Session Test User', $sessionData['name']);
        $this->assertEquals('manufacturer', $sessionData['account_type']);
        $this->assertTrue($sessionData['step_1_completed']);
        
        // Visit step 2 and verify data is available
        $response = $this->get(route('register.wizard.step2'));
        $response->assertStatus(200);
        $response->assertViewHas('sessionData');
        
        $viewData = $response->viewData('sessionData');
        $this->assertEquals('Session Test User', $viewData['name']);
    }

    /**
     * Test username availability check
     */
    public function test_username_availability_check(): void
    {
        // Create existing user
        User::factory()->create(['username' => 'existinguser']);

        // Check existing username
        $response = $this->postJson(route('register.wizard.check-username'), [
            'username' => 'existinguser'
        ]);

        $response->assertJson([
            'available' => false,
            'message' => 'Tên đăng nhập đã tồn tại.'
        ]);

        // Check available username
        $response = $this->postJson(route('register.wizard.check-username'), [
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
        $response = $this->postJson(route('register.wizard.validate-field'), [
            'field' => 'email',
            'value' => 'test@example.com',
            'step' => 1
        ]);

        $response->assertJson([
            'valid' => true,
            'message' => 'Hợp lệ'
        ]);

        // Test invalid email
        $response = $this->postJson(route('register.wizard.validate-field'), [
            'field' => 'email',
            'value' => 'invalid-email',
            'step' => 1
        ]);

        $response->assertJsonStructure([
            'valid',
            'message'
        ]);
        
        $data = $response->json();
        $this->assertFalse($data['valid']);
    }

    /**
     * Test auto-save functionality
     */
    public function test_auto_save_functionality(): void
    {
        // Start wizard
        $this->get(route('register.wizard.step1'));
        $sessionId = Session::get('registration_wizard_session');

        // Test auto-save
        $saveData = [
            'data' => [
                'name' => 'Auto Saved User',
                'email' => 'autosave@test.com',
                'account_type' => 'member'
            ]
        ];

        $response = $this->postJson(route('register.wizard.save-progress'), $saveData);

        $response->assertJson([
            'success' => true,
            'message' => 'Đã lưu tự động.'
        ]);

        // Verify data was saved
        $sessionData = $this->wizardService->getSessionData($sessionId);
        $this->assertEquals('Auto Saved User', $sessionData['name']);
        $this->assertEquals('autosave@test.com', $sessionData['email']);
    }

    /**
     * Test wizard restart functionality
     */
    public function test_wizard_restart_functionality(): void
    {
        // Start wizard and add some data
        $this->get(route('register.wizard.step1'));
        $sessionId = Session::get('registration_wizard_session');
        
        $this->wizardService->updateSessionData($sessionId, [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Restart wizard
        $response = $this->post(route('register.wizard.restart'));

        $response->assertRedirect(route('register.wizard.step1'));
        $response->assertSessionHas('info');
        
        // Old session should be cleared
        $this->assertNull(Session::get('registration_wizard_session'));
        
        // Old session data should be gone
        $sessionData = $this->wizardService->getSessionData($sessionId);
        $this->assertEmpty($sessionData);
    }

    /**
     * Test step 2 access control
     */
    public function test_step2_access_control(): void
    {
        // Try to access step 2 without completing step 1
        $response = $this->get(route('register.wizard.step2'));
        
        $response->assertRedirect(route('register.wizard.step1'));
        $response->assertSessionHas('error');
    }

    /**
     * Test community member cannot access step 2
     */
    public function test_community_member_cannot_access_step2(): void
    {
        // Complete step 1 as community member
        $this->post(route('register.wizard.step1'), [
            'name' => 'Community Member',
            'username' => 'communitymember',
            'email' => 'community@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'student',
            'terms' => true,
        ]);
        
        // Should be logged in and registration complete
        $this->assertAuthenticated();
        
        // Trying to access step 2 should redirect
        $response = $this->get(route('register.wizard.step2'));
        $response->assertRedirect();
    }

    /**
     * Test database integrity after registration
     */
    public function test_database_integrity_after_registration(): void
    {
        // Register business user
        $this->post(route('register.wizard.step1'), [
            'name' => 'Integrity Test User',
            'username' => 'integritytest',
            'email' => 'integrity@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'account_type' => 'supplier',
            'terms' => true,
        ]);

        $this->post(route('register.wizard.step2'), [
            'company_name' => 'Integrity Test Co.',
            'business_license' => 'BL-INT-001',
            'tax_code' => '5555555555',
            'business_description' => 'Test company for database integrity verification.',
            'business_categories' => ['industrial'],
        ]);

        $user = User::where('email', 'integrity@test.com')->first();
        
        // Verify all required fields are set
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->role);
        $this->assertNotNull($user->role_group);
        $this->assertNotNull($user->company_name);
        $this->assertNotNull($user->business_license);
        $this->assertNotNull($user->tax_code);
        $this->assertNotNull($user->business_description);
        $this->assertNotNull($user->business_categories);
        
        // Verify business categories is valid JSON
        $categories = json_decode($user->business_categories, true);
        $this->assertIsArray($categories);
        $this->assertContains('industrial', $categories);
        
        // Verify role group is correct
        $this->assertEquals('business_partners', $user->role_group);
        
        // Verify verification status
        $this->assertFalse($user->is_verified_business);
        $this->assertNull($user->verified_at);
    }

    /**
     * Test rate limiting
     */
    public function test_rate_limiting(): void
    {
        // Make multiple requests quickly
        for ($i = 0; $i < 12; $i++) {
            $response = $this->post(route('register.wizard.step1'), [
                'name' => "Test User $i",
                'username' => "testuser$i",
                'email' => "test$i@example.com",
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'account_type' => 'member',
                'terms' => true,
            ]);
            
            if ($i >= 10) {
                // Should be rate limited after 10 attempts
                $response->assertStatus(429);
                break;
            }
        }
    }
}
