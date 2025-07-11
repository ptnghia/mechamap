<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\RegistrationWizardService;
use Illuminate\Support\Facades\Cache;

/**
 * 🧪 Registration Wizard Service Tests
 * 
 * Unit tests for RegistrationWizardService functionality
 */
class RegistrationWizardServiceTest extends TestCase
{
    protected RegistrationWizardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RegistrationWizardService();
        
        // Clear cache before each test
        Cache::flush();
    }

    /**
     * Test session initialization
     */
    public function test_can_initialize_session(): void
    {
        $sessionId = $this->service->initializeSession();

        $this->assertIsString($sessionId);
        $this->assertNotEmpty($sessionId);
        
        // Verify session data was created
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertIsArray($sessionData);
        $this->assertEquals(1, $sessionData['step']);
        $this->assertFalse($sessionData['step_1_completed']);
        $this->assertFalse($sessionData['step_2_completed']);
    }

    /**
     * Test session data update
     */
    public function test_can_update_session_data(): void
    {
        $sessionId = $this->service->initializeSession();
        
        $updateData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'account_type' => 'member'
        ];

        $result = $this->service->updateSessionData($sessionId, $updateData);
        $this->assertTrue($result);

        // Verify data was updated
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertEquals('Test User', $sessionData['name']);
        $this->assertEquals('test@example.com', $sessionData['email']);
        $this->assertEquals('member', $sessionData['account_type']);
    }

    /**
     * Test step advancement
     */
    public function test_can_advance_step(): void
    {
        $sessionId = $this->service->initializeSession();
        
        // Initially at step 1
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertEquals(1, $sessionData['step']);

        // Advance to step 2
        $result = $this->service->advanceStep($sessionId);
        $this->assertTrue($result);

        // Verify step was advanced
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertEquals(2, $sessionData['step']);

        // Try to advance beyond step 2 (should stay at 2)
        $this->service->advanceStep($sessionId);
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertEquals(2, $sessionData['step']);
    }

    /**
     * Test session clearing
     */
    public function test_can_clear_session(): void
    {
        $sessionId = $this->service->initializeSession();
        
        // Add some data
        $this->service->updateSessionData($sessionId, ['name' => 'Test User']);
        
        // Verify data exists
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertNotEmpty($sessionData);

        // Clear session
        $result = $this->service->clearSession($sessionId);
        $this->assertTrue($result);

        // Verify session is cleared
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertEmpty($sessionData);
    }

    /**
     * Test session progress tracking
     */
    public function test_can_track_session_progress(): void
    {
        $sessionId = $this->service->initializeSession();
        
        // Initial progress
        $progress = $this->service->getSessionProgress($sessionId);
        $this->assertEquals(1, $progress['step']);
        $this->assertEquals(0, $progress['progress']);
        $this->assertEmpty($progress['completed_steps']);

        // Complete step 1
        $this->service->updateSessionData($sessionId, [
            'step_1_completed' => true,
            'account_type' => 'member'
        ]);

        $progress = $this->service->getSessionProgress($sessionId);
        $this->assertEquals(50, $progress['progress']);
        $this->assertContains(1, $progress['completed_steps']);
        $this->assertFalse($progress['requires_business_info']);

        // Test business account type
        $this->service->updateSessionData($sessionId, [
            'account_type' => 'manufacturer'
        ]);

        $progress = $this->service->getSessionProgress($sessionId);
        $this->assertTrue($progress['requires_business_info']);
    }

    /**
     * Test session extension
     */
    public function test_can_extend_session(): void
    {
        $sessionId = $this->service->initializeSession();
        
        $result = $this->service->extendSession($sessionId, 3600); // 1 hour
        $this->assertTrue($result);

        // Verify session was extended
        $sessionData = $this->service->getSessionData($sessionId);
        $this->assertArrayHasKey('extended_at', $sessionData);
    }

    /**
     * Test role group determination
     */
    public function test_determines_correct_role_group(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getRoleGroup');
        $method->setAccessible(true);

        // Test community members
        $this->assertEquals('community_members', $method->invoke($this->service, 'member'));
        $this->assertEquals('community_members', $method->invoke($this->service, 'student'));

        // Test business partners
        $this->assertEquals('business_partners', $method->invoke($this->service, 'manufacturer'));
        $this->assertEquals('business_partners', $method->invoke($this->service, 'supplier'));
        $this->assertEquals('business_partners', $method->invoke($this->service, 'brand'));

        // Test unknown role (should default to community_members)
        $this->assertEquals('community_members', $method->invoke($this->service, 'unknown'));
    }

    /**
     * Test validation of registration data
     */
    public function test_validates_registration_data(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        // Test missing step 1 completion
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Step 1 not completed.');
        
        $method->invoke($this->service, []);
    }

    /**
     * Test validation requires business fields for business users
     */
    public function test_validates_business_fields_for_business_users(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $data = [
            'step_1_completed' => true,
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'account_type' => 'manufacturer',
            // Missing step_2_completed and business fields
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Business users must complete step 2.');
        
        $method->invoke($this->service, $data);
    }

    /**
     * Test complete community member registration data
     */
    public function test_validates_complete_community_member_data(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $data = [
            'step_1_completed' => true,
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'account_type' => 'member',
        ];

        // Should not throw exception for community members
        $method->invoke($this->service, $data);
        $this->assertTrue(true); // If we get here, validation passed
    }

    /**
     * Test complete business registration data
     */
    public function test_validates_complete_business_data(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $data = [
            'step_1_completed' => true,
            'step_2_completed' => true,
            'name' => 'Business User',
            'username' => 'businessuser',
            'email' => 'business@example.com',
            'password' => 'password',
            'account_type' => 'manufacturer',
            'company_name' => 'Test Company',
            'business_license' => 'BL-001',
            'tax_code' => '1234567890',
            'business_description' => 'Test business description',
        ];

        // Should not throw exception for complete business data
        $method->invoke($this->service, $data);
        $this->assertTrue(true); // If we get here, validation passed
    }

    /**
     * Test session statistics
     */
    public function test_can_get_session_statistics(): void
    {
        $stats = $this->service->getSessionStatistics();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('active_sessions', $stats);
        $this->assertArrayHasKey('total_registrations_today', $stats);
        $this->assertArrayHasKey('business_registrations_pending', $stats);
    }

    /**
     * Test handling invalid session ID
     */
    public function test_handles_invalid_session_id(): void
    {
        $invalidSessionId = 'invalid-session-id';
        
        // Should return empty array for invalid session
        $sessionData = $this->service->getSessionData($invalidSessionId);
        $this->assertEmpty($sessionData);

        // Should return false for update attempts
        $result = $this->service->updateSessionData($invalidSessionId, ['test' => 'data']);
        $this->assertFalse($result);

        // Should return false for step advancement
        $result = $this->service->advanceStep($invalidSessionId);
        $this->assertFalse($result);
    }
}
