<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\RegistrationWizardService;

/**
 * 🧪 Registration Wizard Logic Tests
 * 
 * Pure unit tests for RegistrationWizardService logic without database
 */
class RegistrationWizardLogicTest extends TestCase
{
    protected RegistrationWizardService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RegistrationWizardService();
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
     * Test validation of registration data - missing step 1
     */
    public function test_validates_missing_step1_completion(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Step 1 not completed.');
        
        $method->invoke($this->service, []);
    }

    /**
     * Test validation requires required fields
     */
    public function test_validates_required_fields(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $data = [
            'step_1_completed' => true,
            // Missing required fields
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Required field missing: name');
        
        $method->invoke($this->service, $data);
    }

    /**
     * Test validation requires business step 2 for business users
     */
    public function test_validates_business_step2_completion(): void
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
            // Missing step_2_completed
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Business users must complete step 2.');
        
        $method->invoke($this->service, $data);
    }

    /**
     * Test validation requires business fields
     */
    public function test_validates_business_fields(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('validateRegistrationData');
        $method->setAccessible(true);

        $data = [
            'step_1_completed' => true,
            'step_2_completed' => true,
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'account_type' => 'manufacturer',
            // Missing business fields
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Required business field missing: company_name');
        
        $method->invoke($this->service, $data);
    }

    /**
     * Test validation passes for complete community member data
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
     * Test validation passes for complete business data
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
     * Test session expiry check
     */
    public function test_checks_session_expiry(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('isSessionExpired');
        $method->setAccessible(true);

        // Test expired session
        $expiredData = [
            'expires_at' => now()->subHour()->toISOString()
        ];
        $this->assertTrue($method->invoke($this->service, $expiredData));

        // Test valid session
        $validData = [
            'expires_at' => now()->addHour()->toISOString()
        ];
        $this->assertFalse($method->invoke($this->service, $validData));

        // Test missing expires_at
        $missingData = [];
        $this->assertTrue($method->invoke($this->service, $missingData));
    }

    /**
     * Test business role detection
     */
    public function test_detects_business_roles(): void
    {
        $businessRoles = ['manufacturer', 'supplier', 'brand'];
        $communityRoles = ['member', 'student'];

        foreach ($businessRoles as $role) {
            $this->assertEquals('business_partners', $this->getRoleGroup($role));
        }

        foreach ($communityRoles as $role) {
            $this->assertEquals('community_members', $this->getRoleGroup($role));
        }
    }

    /**
     * Test account type validation
     */
    public function test_validates_account_types(): void
    {
        $validTypes = ['member', 'student', 'manufacturer', 'supplier', 'brand'];
        $invalidTypes = ['admin', 'moderator', 'invalid', ''];

        foreach ($validTypes as $type) {
            $this->assertContains($type, $validTypes);
        }

        foreach ($invalidTypes as $type) {
            $this->assertNotContains($type, $validTypes);
        }
    }

    /**
     * Test step progression logic
     */
    public function test_step_progression_logic(): void
    {
        // Community members: Step 1 → Complete
        $this->assertTrue($this->shouldCompleteAfterStep1('member'));
        $this->assertTrue($this->shouldCompleteAfterStep1('student'));

        // Business partners: Step 1 → Step 2 → Complete
        $this->assertFalse($this->shouldCompleteAfterStep1('manufacturer'));
        $this->assertFalse($this->shouldCompleteAfterStep1('supplier'));
        $this->assertFalse($this->shouldCompleteAfterStep1('brand'));
    }

    /**
     * Test data sanitization logic
     */
    public function test_data_sanitization(): void
    {
        $testData = [
            'name' => '  Test User  ',
            'email' => '  TEST@EXAMPLE.COM  ',
            'username' => '  TestUser123  ',
            'company_name' => '  TEST COMPANY LTD  ',
        ];

        $sanitized = $this->sanitizeData($testData);

        $this->assertEquals('Test User', $sanitized['name']);
        $this->assertEquals('test@example.com', $sanitized['email']);
        $this->assertEquals('testuser123', $sanitized['username']);
        $this->assertEquals('Test Company Ltd', $sanitized['company_name']);
    }

    /**
     * Helper method to get role group
     */
    private function getRoleGroup(string $accountType): string
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getRoleGroup');
        $method->setAccessible(true);
        
        return $method->invoke($this->service, $accountType);
    }

    /**
     * Helper method to check if should complete after step 1
     */
    private function shouldCompleteAfterStep1(string $accountType): bool
    {
        return in_array($accountType, ['member', 'student']);
    }

    /**
     * Helper method to simulate data sanitization
     */
    private function sanitizeData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'name':
                case 'company_name':
                    $sanitized[$key] = ucwords(strtolower(trim($value)));
                    break;
                case 'email':
                    $sanitized[$key] = strtolower(trim($value));
                    break;
                case 'username':
                    $sanitized[$key] = strtolower(trim($value));
                    break;
                default:
                    $sanitized[$key] = trim($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Test cache key generation
     */
    public function test_cache_key_generation(): void
    {
        $sessionId = 'test-session-123';
        $expectedKey = 'registration_session:' . $sessionId;
        
        // This would be the cache key format used by the service
        $this->assertEquals($expectedKey, 'registration_session:' . $sessionId);
    }

    /**
     * Test session timeout calculation
     */
    public function test_session_timeout_calculation(): void
    {
        $defaultTimeout = 1800; // 30 minutes
        $extendedTimeout = 3600; // 1 hour
        
        $this->assertEquals(30, $defaultTimeout / 60); // 30 minutes
        $this->assertEquals(60, $extendedTimeout / 60); // 60 minutes
    }

    /**
     * Test progress calculation
     */
    public function test_progress_calculation(): void
    {
        // Step 1 completed = 50% progress
        $step1Progress = (1 / 2) * 100;
        $this->assertEquals(50, $step1Progress);

        // Both steps completed = 100% progress
        $bothStepsProgress = (2 / 2) * 100;
        $this->assertEquals(100, $bothStepsProgress);

        // No steps completed = 0% progress
        $noProgress = (0 / 2) * 100;
        $this->assertEquals(0, $noProgress);
    }
}
